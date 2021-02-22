<?php

declare(strict_types=1);

namespace Webgriffe\SyliusBackInStockNotificationPlugin\Controller;

use DateTime;
use Exception;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Customer\Context\CustomerContextInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Webgriffe\SyliusBackInStockNotificationPlugin\Entity\SubscriptionInterface;
use Webmozart\Assert\Assert;

final class SubscriptionController extends AbstractController
{
    /** @var RepositoryInterface */
    private $backInStockNotificationRepository;

    /** @var FactoryInterface */
    private $backInStockNotificationFactory;

    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    /** @var LocaleContextInterface */
    private $localeContext;

    /** @var SenderInterface */
    private $sender;

    /** @var ProductVariantRepositoryInterface */
    private $productVariantRepository;

    /** @var AvailabilityCheckerInterface */
    private $availabilityChecker;

    /** @var CustomerContextInterface */
    private $customerContext;

    /** @var ValidatorInterface */
    private $validator;

    /** @var TranslatorInterface */
    private $translator;

    /** @var ChannelContextInterface */
    private $channelContext;

    public function __construct(
        ChannelContextInterface $channelContext,
        TranslatorInterface $translator,
        ValidatorInterface $validator,
        CustomerContextInterface $customerContext,
        AvailabilityCheckerInterface $availabilityChecker,
        ProductVariantRepositoryInterface $productVariantRepository,
        SenderInterface $sender,
        LocaleContextInterface $localeContext,
        CustomerRepositoryInterface $customerRepository,
        RepositoryInterface $backInStockNotificationRepository,
        FactoryInterface $backInStockNotificationFactory
    ) {
        $this->backInStockNotificationRepository = $backInStockNotificationRepository;
        $this->backInStockNotificationFactory = $backInStockNotificationFactory;
        $this->customerRepository = $customerRepository;
        $this->localeContext = $localeContext;
        $this->sender = $sender;
        $this->productVariantRepository = $productVariantRepository;
        $this->availabilityChecker = $availabilityChecker;
        $this->customerContext = $customerContext;
        $this->validator = $validator;
        $this->translator = $translator;
        $this->channelContext = $channelContext;
    }

    public function addAction(Request $request): Response
    {
        $subscription = $this->backInStockNotificationFactory->createNew();
        Assert::implementsInterface($subscription, SubscriptionInterface::class);
        /** @var SubscriptionInterface $subscription */
        $customer = $this->customerContext->getCustomer();
        if ($customer && $customerEmail = $customer->getEmail()) {
            $subscription->setEmail($customerEmail);
        }
        $productVariantCode = (string) $request->query->get('product_variant_code');
        if ($productVariantCode) {
            $subscription->setProductVariantCode($productVariantCode);
        }

        $form = $this->createFormBuilder($subscription)
            ->add('email', EmailType::class)
            ->add('product_variant_code', HiddenType::class)
            ->add('submit', SubmitType::class)
            ->getForm();

        if ($customer && $customer->getEmail()) {
            $form->remove('email');
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('error', $this->translator->trans('webgriffe_bisn.back_in_stock_notification.invalid_form'));

            return $this->redirect($this->getRefererUrl($request));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $subscription = $form->getData();
            $email = $subscription->getEmail();
            $errors = $this->validator->validate($email, new Email());
            if (!$email || count($errors)) {
                $this->addFlash('error', $errors[0]->getMessage());

                return $this->redirect($this->getRefererUrl($request));
            }
            $customer = $this->customerRepository->findOneBy(['email' => $email]);
            if ($customer) {
                $subscription->setCustomerId($customer->getId());
            }

            /** @var ProductVariantInterface|null $variant */
            $variant = $this->productVariantRepository->findOneBy(
                ['code' => $subscription->getProductVariantCode()]
            );
            if (!$variant) {
                $this->addFlash('error', $this->translator->trans('webgriffe_bisn.back_in_stock_notification.variant_not_found'));

                return $this->redirect($this->getRefererUrl($request));
            }
            if ($this->availabilityChecker->isStockAvailable($variant)) {
                $this->addFlash('error', $this->translator->trans('webgriffe_bisn.back_in_stock_notification.variant_not_oos'));

                return $this->redirect($this->getRefererUrl($request));
            }

            $subscriptionSaved = $this->backInStockNotificationRepository->findOneBy(
                ['email' => $email, 'productVariantCode' => $subscription->getProductVariantCode()]
            );
            if ($subscriptionSaved) {
                $this->addFlash('error', $this->translator->trans(
                    'webgriffe_bisn.back_in_stock_notification.already_saved',
                    ['email' => $email])
                );

                return $this->redirect($this->getRefererUrl($request));
            }

            $currentChannel = $this->channelContext->getChannel();
            $subscription
                ->setLocaleCode($this->localeContext->getLocaleCode())
                ->setCreatedAt(new DateTime())
                ->setChannelId($currentChannel->getId());

            try {
                //I generate a random string to handle the delete action of the subscription using a GET
                //This way is easier and does not send sensible information
                //see: https://paragonie.com/blog/2015/09/comprehensive-guide-url-parameter-encryption-in-php
                $hash = strtr(base64_encode(random_bytes(9)), '+/', '-_');
            } catch (Exception $e) {
                $this->addFlash('error', $this->translator->trans('webgriffe_bisn.back_in_stock_notification.subscription_failed'));

                return $this->redirect($this->getRefererUrl($request));
            }
            $subscription->setHash($hash);

            $this->backInStockNotificationRepository->add($subscription);
            $this->sender->send(
                'webgriffe_back_in_stock_notification_success_subscription',
                [$email],
                [
                    'subscription' => $subscription,
                    'product' => $variant->getProduct(),
                    'channel' => $currentChannel,
                    'localeCode' => $subscription->getLocaleCode(),
                ]
            );

            $this->addFlash('success', $this->translator->trans('webgriffe_bisn.back_in_stock_notification.subscription_successfully'));

            return $this->redirect($this->getRefererUrl($request));
        }

        return $this->render(
            '@WebgriffeSyliusBackInStockNotificationPlugin/productSubscriptionForm.html.twig',
            ['form' => $form->createView(),]
        );
    }

    public function deleteAction(Request $request, string $hash): Response
    {
        $subscription = $this->backInStockNotificationRepository->findOneBy(['hash' => $hash]);
        if ($subscription) {
            /** @var SubscriptionInterface $subscription */
            Assert::implementsInterface($subscription, SubscriptionInterface::class);
            $this->backInStockNotificationRepository->remove($subscription);
            $this->addFlash('info', $this->translator->trans('webgriffe_bisn.back_in_stock_notification.deletion-successful'));

            return $this->redirect($this->getRefererUrl($request));
        }
        $this->addFlash('info', $this->translator->trans('webgriffe_bisn.back_in_stock_notification.deletion-not-successful'));

        return $this->redirect($this->getRefererUrl($request));
    }

    public function accountListAction(): Response
    {
        $customer = $this->customerContext->getCustomer();
        if (!$customer) {
            return $this->redirect($this->generateUrl('sylius_shop_login'));
        }

        $subscriptions = $this->backInStockNotificationRepository->findBy(['customerId' => $customer->getId()]);
        Assert::allImplementsInterface($subscriptions, SubscriptionInterface::class);
        /** @var SubscriptionInterface[] $subscriptions */
        $data = array_map(function (SubscriptionInterface $subscription) {
            /** @var ProductVariantInterface|null $variant */
            $variant = $this->productVariantRepository->findOneBy(['code' => $subscription->getProductVariantCode()]);

            return [
                'hash' => $subscription->getHash(),
                'variant' => $variant,
            ];
        }, $subscriptions);

        $data = array_filter($data, function ($element) {
            return $element['variant'];
        });

        return $this->render('@WebgriffeSyliusBackInStockNotificationPlugin/accountSubscriptionList.html.twig', [
            'lines' => $data,
        ]);
    }

    private function getRefererUrl(Request $request): string
    {
        $referer = $request->headers->get('referer');
        if (!is_string($referer)) {
            $referer = $this->generateUrl('sylius_shop_homepage');
        }

        return $referer;
    }
}
