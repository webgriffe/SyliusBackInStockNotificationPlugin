<?php
declare(strict_types=1);

namespace Tests\Webgriffe\SyliusBackInStockNotificationPlugin\Behat\Element\Product\ShowPage;

interface SubscriptionFormElementInterface
{
    public function openOverlayForConfigurableProduct(): void;

    public function submitFormAsAGuest(string $variant, string $email): void;

    public function submitFormAsALoggedCustomer(string $variant): void;
}
