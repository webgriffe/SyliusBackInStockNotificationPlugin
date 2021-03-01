<?php
declare(strict_types=1);

namespace Tests\Webgriffe\SyliusBackInStockNotificationPlugin\Behat\Page\Shop\Product;

use Sylius\Behat\Page\Shop\Product\ShowPage as BaseShowPage;

final class ShowPage extends BaseShowPage implements ShowPageInterface
{
    public function addToBackInStockListAsAGuest(string $variant, string $email)
    {
        $this->getElement('open_overlay')->click();
        $this->getElement('add_email')->setValue($email);
        $this->getElement('add_to_notification_list')->click();
    }

    public function addToBackInStockListAsALoggedCustomer(string $variant)
    {
        $this->getElement('open_overlay')->click();
        $this->getElement('add_to_notification_list')->click();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'open_overlay' => '[data-test-open-overlay-to-back-in-stock-notification-list]',
            'add_email' => '[data-test-add-email-to-back-in-stock-notification-list]',
            'add_to_notification_list' => '[data-test-add-to-back-in-stock-notification-list]'
        ]);
    }
}
