<?php

declare(strict_types=1);

namespace Webgriffe\SyliusBackInStockNotificationPlugin\Controller;

use DateTime;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Customer\Context\CustomerContextInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Webgriffe\SyliusBackInStockNotificationPlugin\Entity\SubscriptionInterface;
use Webgriffe\SyliusBackInStockNotificationPlugin\Form\SubscriptionType;
use Webgriffe\SyliusBackInStockNotificationPlugin\Repository\SubscriptionRepositoryInterface;
use Webmozart\Assert\Assert;

final class SubscriptionController extends AbstractController
{
    /**
     * @param FactoryInterface<SubscriptionInterface> $backInStockNotificationFactory
     */
    public function __construct(
        private ChannelContextInterface $channelContext,
        private TranslatorInterface $translator,
        private CustomerContextInterface $customerContext,
        private AvailabilityCheckerInterface $availabilityChecker,
        private ProductVariantRepositoryInterface $productVariantRepository,
        private SenderInterface $sender,
        private LocaleContextInterface $localeContext,
        private SubscriptionRepositoryInterface $backInStockNotificationRepository,
        private FactoryInterface $backInStockNotificationFactory,
    ) {
    }

    public function addAction(Request $request): Response
    {
        $form = $this->createForm(SubscriptionType::class);
        /** @var string|null $productVariantCode */
        $productVariantCode = $request->query->get('product_variant_code');
        if (is_string($productVariantCode)) {
            $form->setData(['product_variant_code' => $productVariantCode]);
        }

        $customer = $this->customerContext->getCustomer();
        if ($customer !== null && $customer->getEmail() !== null) {
            $form->remove('email');
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var array{email?: string, product_variant_code: string} $data */
            $data = $form->getData();
            $subscription = $this->createSubscriptionFromData($data);

            $this->backInStockNotificationRepository->add($subscription);
            $this->sender->send(
                'webgriffe_back_in_stock_notification_success_subscription',
                [$subscription->getEmail()],
                [
                    'subscription' => $subscription,
                    'channel' => $subscription->getChannel(),
                    'localeCode' => $subscription->getLocaleCode(),
                ],
                [],
                [],
            );

            $this->addFlash(
                'success',
                $this->translator->trans('webgriffe_bisn.form_submission.subscription_successfully'),
            );

            return $this->redirect($this->getRefererUrl($request));
        }

        return $this->render(
            '@WebgriffeSyliusBackInStockNotificationPlugin/productSubscriptionForm.html.twig',
            ['form' => $form->createView()],
        );
    }

    public function deleteAction(Request $request, string $hash): Response
    {
        $subscription = $this->backInStockNotificationRepository->findOneBy(['hash' => $hash]);
        if ($subscription === null) {
            $this->addFlash('info', $this->translator->trans('webgriffe_bisn.deletion_submission.not-successful'));

            return $this->redirect($this->getRefererUrl($request));
        }
        $this->backInStockNotificationRepository->remove($subscription);
        $this->addFlash('info', $this->translator->trans('webgriffe_bisn.deletion_submission.successful'));

        return $this->redirect($this->getRefererUrl($request));
    }

    private function getRefererUrl(Request $request): string
    {
        $referer = $request->headers->get('referer');
        if (!is_string($referer)) {
            $referer = $this->generateUrl('sylius_shop_homepage');
        }

        return $referer;
    }

    /**
     * @param array{email?: string, product_variant_code: string} $data
     */
    private function createSubscriptionFromData(array $data): SubscriptionInterface
    {
        $customer = $this->customerContext->getCustomer();
        $email = null;
        $subscription = $this->backInStockNotificationFactory->createNew();
        if (array_key_exists('email', $data)) {
            $email = $data['email'];
        }
        if ($customer !== null) {
            $email = $customer->getEmail();
        }
        Assert::stringNotEmpty($email);
        $subscription->setEmail($email);
        $subscription->setCustomer($customer);

        $productVariantCode = $data['product_variant_code'];
        $productVariant = $this->productVariantRepository->findOneBy(['code' => $productVariantCode]);
        Assert::isInstanceOf($productVariant, ProductVariantInterface::class);
        Assert::false($this->availabilityChecker->isStockAvailable($productVariant), 'Product variant is in stock');
        $subscription->setProductVariant($productVariant);

        $subscription->setChannel($this->channelContext->getChannel());
        $subscription->setLocaleCode($this->localeContext->getLocaleCode());
        $now = new DateTime();
        $subscription->setCreatedAt($now);
        $subscription->setUpdatedAt($now);

        //I generate a random string to handle the delete action of the subscription using a GET
        //This way is easier and does not send sensible information
        //see: https://paragonie.com/blog/2015/09/comprehensive-guide-url-parameter-encryption-in-php
        $hash = strtr(base64_encode(random_bytes(9)), '+/', '-_');
        $subscription->setHash($hash);

        return $subscription;
    }
}
