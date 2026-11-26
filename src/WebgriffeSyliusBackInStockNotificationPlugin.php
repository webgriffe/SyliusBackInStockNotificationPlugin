<?php

declare(strict_types=1);

namespace Webgriffe\SyliusBackInStockNotificationPlugin;

use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Symfony 8.1 deprecates HttpKernel's BundleInterface, still implemented by Bundle for BC with Symfony 6.4/7.x.
 *
 * @psalm-suppress DeprecatedInterface
 */
final class WebgriffeSyliusBackInStockNotificationPlugin extends Bundle
{
    use SyliusPluginTrait;

    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
