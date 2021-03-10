<?php
declare(strict_types=1);

namespace Tests\Webgriffe\SyliusBackInStockNotificationPlugin\Behat\Element\Product\ShowPage;

use FriendsOfBehat\PageObjectExtension\Element\Element;

final class SubscriptionFormElement extends Element implements SubscriptionFormElementInterface
{
    public function submitFormAsAGuest(string $variant, string $email)
    {
        $this->getElement('add_email')->setValue($email);
        $this->getElement('submit_form')->click();
    }

    public function submitFormAsALoggedCustomer(string $variant)
    {
        $this->getElement('submit_form')->click();
    }

    public function openOverlayForConfigurableProduct()
    {
        $this->getElement('open_overlay')->click();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'open_overlay' => '#trigger-notification-overlay',
            'add_email' => '[data-test-fill-subscription-form-whit-my-email]',
            'submit_form' => '[data-test-subscribe-to-notifications]'
        ]);
    }
}
