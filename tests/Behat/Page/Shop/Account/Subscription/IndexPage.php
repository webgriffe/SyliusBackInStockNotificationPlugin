<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusBackInStockNotificationPlugin\Behat\Page\Shop\Account\Subscription;

use Behat\Mink\Session;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;
use Sylius\Behat\Service\Accessor\TableAccessorInterface;
use Symfony\Component\Routing\RouterInterface;

final class IndexPage extends SymfonyPage implements IndexPageInterface
{
    public function __construct(Session $session, $minkParameters, RouterInterface $router, private TableAccessorInterface $tableAccessor)
    {
        parent::__construct($session, $minkParameters, $router);
    }

    public function getRouteName(): string
    {
        return 'webgriffe_back_in_stock_notification_account_list_subscriptions';
    }

    public function deleteFirstSubscription(): void
    {
        $this->getElement('first_subscription')->click();
    }

    public function countSubscriptions(): int
    {
        return $this->tableAccessor->countTableBodyRows($this->getElement('customer_subscriptions'));
    }

    public function isPresentNoSubscriptionInfoMessage(): bool
    {
        return !$this->hasElement('table');
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'customer_subscriptions' => '[data-test-grid-table]',
            'first_subscription' => '[data-test-grid-table-body] [data-test-row]:first-child [data-test-button-delete]',
            'table' => '[data-test-grid-table-body]',
        ]);
    }
}
