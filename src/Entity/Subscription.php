<?php

declare(strict_types=1);

namespace Webgriffe\SyliusBackInStockNotificationPlugin\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="webgriffe_back_in_stock_notification")
 */
final class Subscription implements SubscriptionInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true, name="customer_id")
     *
     * @var int|null
     */
    private $customerId;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $email;

    /**
     * @ORM\Column(type="string", name="product_variant_code")
     *
     * @var string
     */
    private $productVariantCode;

    /**
     * @ORM\Column(type="integer", name="channel_id")
     *
     * @var int
     */
    private $channelId;

    /**
     * @ORM\Column(type="string", length=255, name="locale_code")
     *
     * @var string
     */
    private $localeCode;

    /**
     * @ORM\Column(type="datetime", name="created_at")
     *
     * @var DateTimeInterface|null
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $hash;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomerId(): ?int
    {
        return $this->customerId;
    }

    public function setCustomerId(?int $customerId): SubscriptionInterface
    {
        $this->customerId = $customerId;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): SubscriptionInterface
    {
        $this->email = $email;

        return $this;
    }

    public function getProductVariantCode(): ?string
    {
        return $this->productVariantCode;
    }

    public function setProductVariantCode(string $productVariantCode): SubscriptionInterface
    {
        $this->productVariantCode = $productVariantCode;

        return $this;
    }

    public function getLocaleCode(): ?string
    {
        return $this->localeCode;
    }

    public function setLocaleCode(string $localeCode): SubscriptionInterface
    {
        $this->localeCode = $localeCode;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): SubscriptionInterface
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(string $hash): SubscriptionInterface
    {
        $this->hash = $hash;

        return $this;
    }

    public function getChannelId(): ?int
    {
        return $this->channelId;
    }

    public function setChannelId(int $channelId): SubscriptionInterface
    {
        $this->channelId = $channelId;

        return $this;
    }
}
