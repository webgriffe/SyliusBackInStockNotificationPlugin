<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusBackInStockNotificationPlugin\Behat\Element\Product\ShowPage;

use FriendsOfBehat\PageObjectExtension\Element\Element;

final class SubscriptionFormElement extends Element implements SubscriptionFormElementInterface
{
    public function submitFormAsAGuest(string $variant, string $email): void
    {
        $this->getElement('add_email')->setValue($email);
        $this->getElement('submit_form')->click();

        $this->waitForProductPageRefresh();
    }

    public function submitFormAsALoggedCustomer(string $variant): void
    {
        $this->getElement('submit_form')->click();

        $this->waitForProductPageRefresh();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'open_overlay' => '#trigger-notification-overlay',
            'add_email' => '[data-live-name-value="webgriffe:sylius_shop:product:add_notification"] [data-test-email]',
            'form' => '[data-live-name-value="webgriffe:sylius_shop:product:add_notification"]',
            'submit_form' => '[data-test-button="add-notification"]',
        ]);
    }

    private function waitForProductPageRefresh(): void
    {
        $form = $this->getElement('form');

        usleep(500000);
        $form->waitFor(1500, fn () => !$form->hasAttribute('busy'));
    }
}
