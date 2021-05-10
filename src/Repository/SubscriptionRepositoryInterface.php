<?php
declare(strict_types=1);

namespace Webgriffe\SyliusBackInStockNotificationPlugin\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface SubscriptionRepositoryInterface extends RepositoryInterface
{
    public function createByCustomerIdAndChannelIdQueryBuilder(int $customerId, int $channelId): QueryBuilder;
}