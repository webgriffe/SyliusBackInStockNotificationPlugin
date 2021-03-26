<?php
declare(strict_types=1);

namespace Webgriffe\SyliusBackInStockNotificationPlugin\Menu;

use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class AdminMenuListener
{
    public function addAdminMenuItems(MenuBuilderEvent $event): void
    {
        $customersMenu = $event->getMenu()->getChild('customers');

        if ($customersMenu) {
            $customersMenu
                ->addChild('list_subscriptions', ['route' => 'webgriffe_admin_back_in_stock_notification_subscription_index'])
                ->setLabel('webgriffe_bisn.admin.menu_label')
                ->setLabelAttribute('icon', 'bell')
            ;
        }

    }
}
