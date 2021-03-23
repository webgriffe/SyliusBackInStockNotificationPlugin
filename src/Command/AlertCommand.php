<?php
declare(strict_types=1);

namespace Webgriffe\SyliusBackInStockNotificationPlugin\Command;

use Psr\Log\LoggerInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Webgriffe\SyliusBackInStockNotificationPlugin\Entity\SubscriptionInterface;
use Webmozart\Assert\Assert;

final class AlertCommand extends Command
{
    protected static $defaultName = 'webgriffe:back-in-stock-notification:alert';

    /** @var RepositoryInterface */
    private $backInStockNotificationRepository;

    /** @var ProductVariantRepositoryInterface */
    private $productVariantRepository;

    /** @var AvailabilityCheckerInterface */
    private $availabilityChecker;

    /** @var SenderInterface */
    private $sender;

    /** @var LoggerInterface */
    private $logger;

    /** @var ChannelRepositoryInterface */
    private $channelRepository;

    public function __construct(
        LoggerInterface $logger,
        SenderInterface $sender,
        AvailabilityCheckerInterface $availabilityChecker,
        ProductVariantRepositoryInterface $productVariantRepository,
        RepositoryInterface $backInStockNotificationRepository,
        ChannelRepositoryInterface $channelRepository,
        string $name = null
    ) {
        $this->backInStockNotificationRepository = $backInStockNotificationRepository;
        $this->productVariantRepository = $productVariantRepository;
        $this->availabilityChecker = $availabilityChecker;
        $this->sender = $sender;
        $this->logger = $logger;
        $this->channelRepository = $channelRepository;
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Send an email to the user if the product is returned in stock')
            ->setHelp('Check the stock status of the products in the webgriffe_back_in_stock_notification table and send and email to the user if the product is returned in stock');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        foreach ($this->backInStockNotificationRepository->findAll() as $subscription) {
            //I think that this load can be a bottle necklace
            Assert::implementsInterface($subscription, SubscriptionInterface::class);
            /** @var SubscriptionInterface $subscription */
            $channel = $this->channelRepository->find($subscription->getChannelId());
            $productVariantCode = $subscription->getProductVariantCode();
            if ($productVariantCode === null) {
                $this->backInStockNotificationRepository->remove($subscription);
                $this->logger->warning(
                    'The back in stock subscription for the product does not have all the information required,' .
                    ' in particular the product_variant_id',
                    ['subscription' => var_export($subscription, true)]
                );

                continue;
            }
            if ($channel === null) {
                $this->backInStockNotificationRepository->remove($subscription);
                $this->logger->warning(
                    'None channel founded with the current id',
                    ['channel_id' => var_export($subscription->getChannelId(), true)]
                );

                continue;
            }

            $criteria = ['code' => $productVariantCode];
            $variant = $this->productVariantRepository->findOneBy($criteria);
            /** @var ProductVariantInterface|null $variant */
            if ($variant !== null && $this->availabilityChecker->isStockAvailable($variant)) {
                $this->sender->send(
                    'webgriffe_back_in_stock_notification_alert',
                    [$subscription->getEmail()],
                    [
                        'subscription' => $subscription,
                        'product' => $variant->getProduct(),
                        'channel' => $channel,
                        'localeCode' => $subscription->getLocaleCode(),
                    ]
                );
                $this->backInStockNotificationRepository->remove($subscription);
            }
        }

        return 0;
    }
}