<?php

declare(strict_types=1);

namespace Webgriffe\SyliusBackInStockNotificationPlugin\Repository;

if (!interface_exists(\Sylius\Resource\Doctrine\Persistence\RepositoryInterface::class)) {
    class_alias(\Sylius\Component\Resource\Repository\RepositoryInterface::class, \Sylius\Resource\Doctrine\Persistence\RepositoryInterface::class);
}
use Doctrine\ORM\QueryBuilder;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Webgriffe\SyliusBackInStockNotificationPlugin\Entity\SubscriptionInterface;

/**
 * @extends RepositoryInterface<SubscriptionInterface>
 */
interface SubscriptionRepositoryInterface extends RepositoryInterface
{
    public function createByCustomerIdAndChannelIdQueryBuilder(int $customerId, int $channelId): QueryBuilder;
}
