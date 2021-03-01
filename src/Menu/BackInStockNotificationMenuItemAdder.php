<?php

declare(strict_types=1);

namespace Webgriffe\SyliusBackInStockNotificationPlugin\Menu;

use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class BackInStockNotificationMenuItemAdder
{
    public function addMenuItems(MenuBuilderEvent $event): void
    {
        $event->getMenu()
            ->addChild('list_subscriptions', ['route' => 'webgriffe_back_in_stock_notification_account_list_subscriptions'])
            ->setLabel('webgriffe_bisn.my_account_section.menu_label')
            ->setLabelAttribute('icon', 'bell')
        ;
    }
}
