<?php

declare(strict_types=1);

namespace Webgriffe\SyliusBackInStockNotificationPlugin\Entity;

use Sylius\Component\Channel\Model\ChannelAwareInterface;
use Sylius\Component\Customer\Model\CustomerAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

interface SubscriptionInterface extends ResourceInterface, CustomerAwareInterface, ChannelAwareInterface, TimestampableInterface, ProductVariantAwareInterface
{
    public function getHash(): ?string;

    public function setHash(string $hash): void;

    public function getEmail(): ?string;

    public function setEmail(string $email): void;

    public function getLocaleCode(): ?string;

    public function setLocaleCode(string $localeCode): void;
}
