<?php
declare(strict_types=1);

namespace Tests\Webgriffe\SyliusBackInStockNotificationPlugin\Behat\Context\Ui\Shop;

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Tests\Webgriffe\SyliusBackInStockNotificationPlugin\Behat\Element\Product\ShowPage\SubscriptionFormElementInterface;
use Webgriffe\SyliusBackInStockNotificationPlugin\Entity\SubscriptionInterface;
use Behat\Behat\Context\Context;
use DateTime;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Core\Test\Services\EmailCheckerInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Webmozart\Assert\Assert;

final class ProductInventoryContext implements Context
{
    /** @var NotificationCheckerInterface */
    private $notificationChecker;

    /** @var EmailCheckerInterface */
    private $emailChecker;

    /** @var TranslatorInterface */
    private $translator;

    /** @var RepositoryInterface */
    private $backInStockNotificationRepository;

    /** @var FactoryInterface */
    private $backInStockNotificationFactory;

    /** @var LocaleContextInterface */
    private $localeContext;

    /** @var ChannelContextInterface */
    private $channelContext;

    /** @var SubscriptionFormElementInterface */
    private $subscriptionFormElement;

    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        NotificationCheckerInterface $notificationChecker,
        EmailCheckerInterface $emailChecker,
        TranslatorInterface $translator,
        RepositoryInterface $backInStockNotificationRepository,
        FactoryInterface $backInStockNotificationFactory,
        LocaleContextInterface $localeContext,
        ChannelContextInterface $channelContext,
        SubscriptionFormElementInterface $subscriptionFormElement
    ) {
        $this->notificationChecker = $notificationChecker;
        $this->emailChecker = $emailChecker;
        $this->translator = $translator;
        $this->backInStockNotificationRepository = $backInStockNotificationRepository;
        $this->backInStockNotificationFactory = $backInStockNotificationFactory;
        $this->localeContext = $localeContext;
        $this->channelContext = $channelContext;
        $this->subscriptionFormElement = $subscriptionFormElement;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @When I subscribe to the alert list for the product :product
     * @When I subscribe to the alert list for the product :product with the email :email
     */
    public function iSubscribeToTheAlertListForThisProduct(ProductInterface $product, string $email = null)
    {
        if ($product->isConfigurable()) {
            $this->subscriptionFormElement->openOverlayForConfigurableProduct();
        }
        if ($email) {
            $this->subscriptionFormElement->submitFormAsAGuest($product->getCode(), $email);
        } else {
            $this->subscriptionFormElement->submitFormAsALoggedCustomer($product->getCode());
        }
    }

    /**
     * @Then I should be notified that the email is subscribed correctly
     */
    public function iShouldBeNotifiedThatTheEmailIsSubscribedCorrectly()
    {
        $this->notificationChecker->checkNotification(
            $this->translator->trans('webgriffe_bisn.form_submission.subscription_successfully'),
            NotificationType::success()
        );
    }

    /**
     * @Then an email with a success message should be sent to :email
     */
    public function anEmailWithASuccessMessageShouldBeSentTo($email)
    {
        Assert::true($this->emailChecker->hasMessageTo(
            $this->translator->trans('webgriffe_bisn.subscription_mail.subscription_title'),
            $email
        ));
    }

    /**
     * @Then I should be notified that the email :email is already subscribed
     */
    public function iShouldBeNotifiedThatTheEmailIsAlreadySubscribed(string $email)
    {
        $this->notificationChecker->checkNotification(
            $this->translator->trans('webgriffe_bisn.form_submission.already_saved', ['email' => $email]),
            NotificationType::failure()
        );
    }

    /**
     * @Given in the back in stock list there is an entry identified by the customer with the email :email an by the product :product
     */
    public function inTheBackInStockListThereIsAnEntryIdentifiedByAnEmailAnByTheProduct(string $email, ProductInterface $product)
    {
        /** @var SubscriptionInterface $subscription */
        $subscription = $this->backInStockNotificationFactory->createNew();

        /** @var CustomerInterface $customer */
        $customer = $this->customerRepository->findOneBy(['email' => $email]);

        $subscription
            ->setEmail($customer->getEmail())
            ->setCustomerId($customer->getId())
            ->setProductVariantCode($product->getCode())
            ->setCreatedAt(new DateTime())
            ->setHash('qe5ZH3biBD1W')
            ->setChannelId($this->channelContext->getChannel()->getId())
            ->setLocaleCode($this->localeContext->getLocaleCode())
        ;

        $this->backInStockNotificationRepository->add($subscription);
    }
}
