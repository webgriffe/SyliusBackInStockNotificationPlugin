<?php

declare(strict_types=1);

namespace Webgriffe\SyliusBackInStockNotificationPlugin\Twig;

use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Twig\Extension\RuntimeExtensionInterface;

final class AvailabilityRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private AvailabilityCheckerInterface $availabilityChecker,
    ) {
    }

    public function isProductVariantAvailable(ProductVariantInterface $productVariant): bool
    {
        return $this->availabilityChecker->isStockAvailable($productVariant);
    }
}
