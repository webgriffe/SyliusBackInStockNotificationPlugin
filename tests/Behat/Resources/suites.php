<?php

declare(strict_types=1);

use Behat\Config\Config;
use Behat\Config\Filter\TagFilter;
use Behat\Config\Profile;
use Behat\Config\Suite;
use Tests\Webgriffe\SyliusBackInStockNotificationPlugin\Behat\Context\Ui\Shop\AccountContext;
use Tests\Webgriffe\SyliusBackInStockNotificationPlugin\Behat\Context\Ui\Shop\ProductInventoryContext;

return (new Config())
    ->withProfile(
        (new Profile('default'))
        ->withSuite(
            (new Suite('ui_product_inventory'))
            ->withContexts(
                'sylius.behat.context.hook.doctrine_orm',
                // Setup
                'sylius.behat.context.setup.channel',
                'sylius.behat.context.setup.product',
                'sylius.behat.context.setup.customer',
                'sylius.behat.context.setup.shop_security',
                // Ui
                'sylius.behat.context.ui.shop.product',
                ProductInventoryContext::class,
                // Transform
                'sylius.behat.context.transform.product',
                'sylius.behat.context.transform.product_option',
                'sylius.behat.context.transform.channel',
                'sylius.behat.context.transform.shared_storage',
            )
            ->withFilter(new TagFilter('@product_inventory && @ui')),
        )
        ->withSuite(
            (new Suite('ui_customer_account'))
            ->withContexts(
                'sylius.behat.context.hook.doctrine_orm',
                // Setup
                'sylius.behat.context.setup.channel',
                'sylius.behat.context.setup.product',
                'sylius.behat.context.setup.customer',
                'sylius.behat.context.setup.shop_security',
                // Ui
                'sylius.behat.context.ui.shop.product',
                ProductInventoryContext::class,
                AccountContext::class,
                // Transform
                'sylius.behat.context.transform.product',
                'sylius.behat.context.transform.channel',
                'sylius.behat.context.transform.shared_storage',
            )
            ->withFilter(new TagFilter('@customer_account && @ui')),
        ),
    )
;
