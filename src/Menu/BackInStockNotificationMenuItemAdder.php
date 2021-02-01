<?php

declare(strict_types=1);

namespace Webgriffe\SyliusBackInStockNotificationPlugin\Menu;

use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class BackInStockNotificationMenuItemAdder
{
    public function addMenuItems(MenuBuilderEvent $event): void
    {
        //TODO: make the position configurable by yml
        $menu = $event->getMenu();
        //It is not possible to specify the order of an added child so I have to delete the last,
        //add one and then add the last, to make my item the penultimate
        $logoutItem = $menu->getLastChild();
        $menu->removeChild($logoutItem);

        $menu
            ->addChild('notification_subscription', ['route' => 'app_back_in_stock_notification_account_subscription_book_index'])
            ->setLabel('app.back_in_stock_notification.account_menu_label')
            ->setLabelAttribute('icon', 'star')
        ;

        $menu->addChild($logoutItem);
    }
}
