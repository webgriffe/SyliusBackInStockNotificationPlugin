document.addEventListener("DOMContentLoaded", function(event) {
    function getSelectedVariantCode() {
        const syliusVariantsStock = document.querySelector('#sylius-variants-stock')
        const formData = new FormData(document.querySelector('#sylius-product-adding-to-cart'))
        let productVariantCode= formData.get('sylius_add_to_cart[cartItem][variant]')
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

    document.querySelector('#trigger-notification-overlay').onclick = function() {
        jQuery('#notification-overlay').modal('show')
        document.querySelector('#back_in_stock_not input[type="hidden"]').value = getSelectedVariantCode()
    }

    document.querySelector('#sylius-product-adding-to-cart').onchange = renderNotifyMeBtn
    renderNotifyMeBtn()
})