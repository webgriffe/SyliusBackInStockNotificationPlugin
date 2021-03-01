<?php
declare(strict_types=1);

namespace Tests\Webgriffe\SyliusBackInStockNotificationPlugin\Behat\Context\Ui\Shop;

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Webgriffe\SyliusBackInStockNotificationPlugin\Entity\SubscriptionInterface;
use Tests\Webgriffe\SyliusBackInStockNotificationPlugin\Behat\Page\Shop\Product\ShowPageInterface;
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

    /** @var ShowPageInterface */
    private $showPage;

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
        ShowPageInterface $showPage
    ) {
        $this->notificationChecker = $notificationChecker;
        $this->emailChecker = $emailChecker;
        $this->translator = $translator;
        $this->backInStockNotificationRepository = $backInStockNotificationRepository;
        $this->backInStockNotificationFactory = $backInStockNotificationFactory;
        $this->localeContext = $localeContext;
        $this->channelContext = $channelContext;
        $this->showPage = $showPage;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @When I subscribe to the alert list for the product :product
     * @When I subscribe to the alert list for the product :product with the email :email
     */
    public function iSubscribeToTheAlertListForThisProduct(ProductInterface $product, string $email = null)
    {
        if ($email) {
            $this->showPage->addToBackInStockListAsAGuest($product->getCode(), $email);
        } else {
            $this->showPage->addToBackInStockListAsALoggedCustomer($product->getCode());
        }
    }

    /**
     * @Then I should be notified that the email is subscribed correctly
     */
    public function iShouldBeNotifiedThatTheEmailIsSubscribedCorrectly()
    {
        $this->notificationChecker->checkNotification(
            $this->translator->trans('app.back_in_stock_notification.subscription_successfully'),
            NotificationType::success()
        );
    }

    /**
     * @Then an email with a success message should be sent to :email
     */
    public function anEmailWithASuccessMessageShouldBeSentTo($email)
    {
        Assert::true($this->emailChecker->hasMessageTo(
            $this->translator->trans('theme.email.stock.subscription_title'),
            $email
        ));
    }

    /**
     * @Then I should be notified that the email :email is already subscribed
     */
    public function iShouldBeNotifiedThatTheEmailIsAlreadySubscribed(string $email)
    {
        $this->notificationChecker->checkNotification(
            $this->translator->trans('app.back_in_stock_notification.already_saved', ['email' => $email]),
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
