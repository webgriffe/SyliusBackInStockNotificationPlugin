<?php

declare(strict_types=1);

namespace Webgriffe\SyliusBackInStockNotificationPlugin\Entity;

use DateTimeInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

interface SubscriptionInterface extends ResourceInterface
{
    public function getId(): ?int;

    public function getCustomerId(): ?int;

    public function setCustomerId(?int $customerId): self;

    public function getEmail(): ?string;

    public function setEmail(string $email): self;

    public function getProductVariantCode(): ?string;

    public function setProductVariantCode(string $productVariantCode): self;

    public function getChannelId(): ?int;

    public function setChannelId(int $channelId): self;

    public function getLocaleCode(): ?string;

    public function setLocaleCode(string $localeCode): self;

    public function getCreatedAt(): ?DateTimeInterface;

    public function setCreatedAt(DateTimeInterface $createdAt): self;

    public function getHash(): ?string;

    public function setHash(string $hash): self;
}
