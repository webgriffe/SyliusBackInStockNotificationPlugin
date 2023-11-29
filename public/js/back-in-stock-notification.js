document.addEventListener("DOMContentLoaded", function (event) {
  function getSelectedVariantCode() {
    const syliusVariantsStock = document.querySelector('#sylius-variants-stock')
    const formData = new FormData(document.querySelector('#sylius-product-adding-to-cart'))
    let productVariantCode = formData.get('sylius_add_to_cart[cartItem][variant]')
    if (!productVariantCode) {
      // handle the case variant selection -> option matching
      let variants = Array.from(syliusVariantsStock.children)
      for (let optionCode of JSON.parse(syliusVariantsStock.dataset.optionCode)) {
        let optionValueSelected = formData.get(`sylius_add_to_cart[cartItem][variant][${optionCode}]`)
        variants = variants.filter((variant) => JSON.parse(variant.dataset.optionsValue).includes(optionValueSelected))
      }
      if (variants.length === 1) {
        productVariantCode = variants[0].dataset.variantCode
      }
    }
    return productVariantCode;
  }

  function renderNotifyMeBtn() {
    const selectedVariant = document.querySelector(`#sylius-variants-stock [data-variant-code="${getSelectedVariantCode()}"]`)
    if (selectedVariant) {
      const notifyMeBtn = document.querySelector('#trigger-notification-overlay');
      const addToCartBtn = document.querySelector('#sylius-product-adding-to-cart button[type="submit"]');
      if (selectedVariant.dataset.available) {
        notifyMeBtn.style.display = 'none';
        addToCartBtn.style.display = 'block';
      } else {
        notifyMeBtn.style.display = 'block';
        addToCartBtn.style.display = 'none';
      }
    }
  }

  const notificationTrigger = document.querySelector('#trigger-notification-overlay')
  if (notificationTrigger) {
    notificationTrigger.onclick = function () {
      jQuery('#notification-overlay').modal('show')
      document.querySelector('#back_in_stock_not input[type="hidden"]').value = getSelectedVariantCode()
    }
  }

  const addToCartBtn = document.querySelector('#sylius-product-adding-to-cart')
  if (addToCartBtn) {
    addToCartBtn.onchange = renderNotifyMeBtn
    renderNotifyMeBtn()
  }

  function subscribeToBackInStockNotification(container) {
    const form = container.querySelector('form')

    const submitFormThroughAjax = e => {
      e.preventDefault()

      const formAction = form.getAttribute('action')
      fetch(formAction, {
        method: form.getAttribute('method'),
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          Accept: 'text/html',
        },
        body: new FormData(form),
        cache: 'no-cache',
        redirect: 'follow',
      })
        .then(response => {
          // Response has been redirected to a new URL,
          // follow the redirect by changing the current browser page
          if (response.redirected) {
            window.location.href = response.url
          }

          return response.text()
        })
        .then(htmlResponse => {
          container.innerHTML = htmlResponse
        })
        .catch((e) => {
          console.error(e, e.options)
        })
    }

    form.addEventListener('submit', submitFormThroughAjax)
  }

  const container = document.querySelector('[data-back-in-stock-notification-form-container]')

  const observer = new MutationObserver((mutationList, observer) => {
    for (const mutation of mutationList) {
      if (mutation.type === "childList") {
        for (const node of mutation.addedNodes) {
          if (node.nodeType === 1 && node.parentNode.getAttribute('data-back-in-stock-notification-form-container')) {
            subscribeToBackInStockNotification(container)
          }
        }
      }
    }
  });

  if (container) {
    observer.observe(container, { attributes: false, childList: true, subtree: true });
    subscribeToBackInStockNotification(container)
  }
})
