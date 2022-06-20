<?php

declare(strict_types=1);

namespace Webgriffe\SyliusBackInStockNotificationPlugin\Command;

use Psr\Log\LoggerInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\RouterInterface;
use Webgriffe\SyliusBackInStockNotificationPlugin\Entity\SubscriptionInterface;

final class AlertCommand extends Command
{
    protected static $defaultName = 'webgriffe:back-in-stock-notification:alert';

    public function __construct(
        private LoggerInterface $logger,
        private SenderInterface $sender,
        private AvailabilityCheckerInterface $availabilityChecker,
        private RepositoryInterface $backInStockNotificationRepository,
        private RouterInterface $router,
        string $name = null,
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Send an email to the user if the product is returned in stock')
            ->setHelp('Check the stock status of the products in the webgriffe_back_in_stock_notification table and send and email to the user if the product is returned in stock')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        //I think that this load in the long time can be a bottle necklace
        $subscriptions = $this->backInStockNotificationRepository->findBy(['notify' => false]);
        /** @var SubscriptionInterface $subscription */
        foreach ($subscriptions as $subscription) {
            $channel = $subscription->getChannel();
            $productVariant = $subscription->getProductVariant();
            if ($productVariant === null || $channel === null) {
                $this->backInStockNotificationRepository->remove($subscription);
                $this->logger->warning(
                    'The back in stock subscription for the product does not have all the information required',
                    ['subscription' => var_export($subscription, true)],
                );

                continue;
            }

            if ($this->availabilityChecker->isStockAvailable($productVariant) && $productVariant->isEnabled() && $productVariant->getProduct()?->isEnabled()) {
                $this->router->getContext()->setHost($channel->getHostname() ?? 'localhost');
                $this->sendEmail($subscription, $productVariant, $channel);
                $subscription->setNotify(true);
                $this->backInStockNotificationRepository->add($subscription);
            }
        }

        return 0;
    }

    private function sendEmail(SubscriptionInterface $subscription, ProductVariantInterface $productVariant, ChannelInterface $channel): void
    {
        $this->sender->send(
            'webgriffe_back_in_stock_notification_alert',
            [$subscription->getEmail()],
            [
                'subscription' => $subscription,
                'product' => $productVariant->getProduct(),
                'channel' => $channel,
                'localeCode' => $subscription->getLocaleCode(),
            ],
        );
    }
}
