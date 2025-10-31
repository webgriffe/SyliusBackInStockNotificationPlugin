<?php

declare(strict_types=1);

namespace Webgriffe\SyliusBackInStockNotificationPlugin\Processor;

use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Customer\Model\CustomerInterface;
use Webgriffe\SyliusBackInStockNotificationPlugin\Entity\SubscriptionInterface;

interface SubscriptionProcessorInterface
{
    public function process(ProductVariantInterface $productVariant, string $email, ?CustomerInterface $customer = null): SubscriptionInterface;
}
