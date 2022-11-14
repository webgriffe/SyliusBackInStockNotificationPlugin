<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusBackInStockNotificationPlugin\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Tests\Webgriffe\SyliusBackInStockNotificationPlugin\Behat\Page\Shop\Account\Subscription\IndexPageInterface;
use Webmozart\Assert\Assert;

final class AccountContext implements Context
{
    /** @var IndexPageInterface */
    private $subscriptionIndexPage;

    /** @var NotificationCheckerInterface */
    private $notificationChecker;

    public function __construct(IndexPageInterface $subscriptionIndexPage, NotificationCheckerInterface $notificationChecker)
    {
        $this->subscriptionIndexPage = $subscriptionIndexPage;
        $this->notificationChecker = $notificationChecker;
    }

    /**
     * @When /^I browse to my product subscriptions$/
     */
    public function iBrowseToMyProductSubscriptions(): void
    {
        $this->subscriptionIndexPage->open();
    }

    /**
     * @When /^I delete the first subscription$/
     */
    public function iDeleteTheFirstSubscription(): void
    {
        $this->subscriptionIndexPage->deleteFirstSubscription();
    }

    /**
     * @Then /^I should be notified that the subscription has been successfully deleted$/
     */
    public function iShouldBeNotifiedThatTheSubscriptionHasBeenSuccessfullyDeleted(): void
    {
        $this->notificationChecker->checkNotification('Mail is deleted from the notification alert list', NotificationType::info());
    }

    /**
     * @Then /^there should be no subscriptions$/
     */
    public function thereShouldBeNoSubscriptions(): void
    {
        Assert::true($this->subscriptionIndexPage->isPresentNoSubscriptionInfoMessage());
    }

    /**
     * @Then /^I should see only one subscription$/
     */
    public function iShouldSeeOnlyOneSubscription(): void
    {
        Assert::same($this->subscriptionIndexPage->countSubscriptions(), 1);
    }
}
