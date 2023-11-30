<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusBackInStockNotificationPlugin\Behat\Element\Product\ShowPage;

use Behat\Mink\Session;
use FriendsOfBehat\PageObjectExtension\Element\Element;
use Sylius\Behat\Page\Shop\Product\ShowPageInterface;
use Sylius\Behat\Service\DriverHelper;
use Sylius\Behat\Service\JQueryHelper;

final class SubscriptionFormElement extends Element implements SubscriptionFormElementInterface
{
    /**
     * @param mixed[]|\ArrayAccess<array-key, mixed> $minkParameters
     */
    public function __construct(
        Session $session,
        $minkParameters,
        private ShowPageInterface $productPage,
    ) {
        parent::__construct($session, $minkParameters);
    }

    public function submitFormAsAGuest(string $variant, string $email): void
    {
        $this->getElement('add_email')->setValue($email);
        $this->getElement('submit_form')->submit();

        $this->waitForProductPageRefresh();
    }

    public function submitFormAsALoggedCustomer(string $variant): void
    {
        $this->getElement('submit_form')->submit();

        $this->waitForProductPageRefresh();
    }

    public function openOverlayForConfigurableProduct(): void
    {
        $this->getElement('open_overlay')->click();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'open_overlay' => '#trigger-notification-overlay',
            'add_email' => '[data-test-fill-subscription-form-whit-my-email]',
            'submit_form' => '[data-test-subscribe-to-notifications]',
        ]);
    }

    private function waitForProductPageRefresh(): void
    {
        if (DriverHelper::isJavascript($this->getDriver())) {
            JQueryHelper::waitForAsynchronousActionsToFinish($this->getSession());
            $this->getDocument()->waitFor(3, fn (): bool => $this->productPage->isOpen());
        }
    }
}
