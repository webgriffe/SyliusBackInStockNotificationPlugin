<?php

declare(strict_types=1);

namespace Webgriffe\SyliusBackInStockNotificationPlugin\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

final class SubscriptionRepository extends EntityRepository implements SubscriptionRepositoryInterface
{
    public function createByCustomerIdAndChannelIdQueryBuilder(int $customerId, int $channelId): QueryBuilder
    {
        return $this
            ->createQueryBuilder('subscription')
            ->andWhere('subscription.customer = :customerId')
            ->andWhere('subscription.channel = :channelId')
            ->setParameter('customerId', $customerId)
            ->setParameter('channelId', $channelId)
            ;
    }
}
