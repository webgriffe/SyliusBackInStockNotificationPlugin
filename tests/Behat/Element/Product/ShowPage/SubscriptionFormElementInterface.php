<?php
declare(strict_types=1);

namespace Tests\Webgriffe\SyliusBackInStockNotificationPlugin\Behat\Element\Product\ShowPage;

interface SubscriptionFormElementInterface
{
    public function openOverlayForConfigurableProduct();

    public function submitFormAsAGuest(string $variant, string $email);

    public function submitFormAsALoggedCustomer(string $variant);
}
