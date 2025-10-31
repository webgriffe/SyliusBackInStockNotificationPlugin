<?php

declare(strict_types=1);

namespace Webgriffe\SyliusBackInStockNotificationPlugin\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class SubscriptionUnique extends Constraint
{
    public string $message = 'webgriffe_bisn.subscription.already_saved';

    public function getTargets(): string|array
    {
        return self::CLASS_CONSTRAINT;
    }
}
