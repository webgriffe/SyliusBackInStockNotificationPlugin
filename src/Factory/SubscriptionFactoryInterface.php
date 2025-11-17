<?php

declare(strict_types=1);

namespace Webgriffe\SyliusBackInStockNotificationPlugin\Factory;

use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Customer\Model\CustomerInterface;
use Sylius\Resource\Factory\FactoryInterface;
use Webgriffe\SyliusBackInStockNotificationPlugin\Entity\SubscriptionInterface;

/**
 * @extends FactoryInterface<SubscriptionInterface>
 */
interface SubscriptionFactoryInterface extends FactoryInterface
{
    public function createWithData(
        ChannelInterface $channel,
        ProductVariantInterface $productVariant,
        string $email,
        string $localeCode,
        ?CustomerInterface $customer,
    ): SubscriptionInterface;
}
