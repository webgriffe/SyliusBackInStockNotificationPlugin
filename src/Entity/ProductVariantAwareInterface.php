<?php

declare(strict_types=1);

namespace Webgriffe\SyliusBackInStockNotificationPlugin\Entity;

use Sylius\Component\Core\Model\ProductVariantInterface;

interface ProductVariantAwareInterface
{
    public function getProductVariant(): ?ProductVariantInterface;

    public function setProductVariant(?ProductVariantInterface $productVariant): void;
}
