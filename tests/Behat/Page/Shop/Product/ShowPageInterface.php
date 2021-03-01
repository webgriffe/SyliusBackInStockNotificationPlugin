<?php
declare(strict_types=1);

namespace Tests\Webgriffe\SyliusBackInStockNotificationPlugin\Behat\Page\Shop\Product;

use Sylius\Behat\Page\Shop\Product\ShowPageInterface as BaseShowPageInterfaceAlias;

interface ShowPageInterface extends BaseShowPageInterfaceAlias
{
    public function addToBackInStockListAsAGuest(string $variant, string $email);

    public function addToBackInStockListAsALoggedCustomer(string $variant);
}
