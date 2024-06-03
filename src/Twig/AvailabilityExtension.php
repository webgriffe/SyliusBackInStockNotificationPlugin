<?php

declare(strict_types=1);

namespace Webgriffe\SyliusBackInStockNotificationPlugin\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class AvailabilityExtension extends AbstractExtension
{
    /**
     * @psalm-suppress InvalidArgument
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('is_product_variant_available', [AvailabilityRuntime::class, 'isProductVariantAvailable']),
        ];
    }
}
