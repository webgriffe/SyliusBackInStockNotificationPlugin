<?php

declare(strict_types=1);

namespace Webgriffe\SyliusBackInStockNotificationPlugin\Validator;

use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Webgriffe\SyliusBackInStockNotificationPlugin\Repository\SubscriptionRepositoryInterface;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class SubscriptionUniqueValidator extends ConstraintValidator
{
    public function __construct(
        private SubscriptionRepositoryInterface $subscriptionRepository,
        private ProductVariantRepositoryInterface $productVariantRepository,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof SubscriptionUnique) {
            throw new UnexpectedTypeException($constraint, SubscriptionUnique::class);
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) to take care of that
        if (null === $value || '' === $value) {
            return;
        }

        if (!is_array($value)) {
            // throw this exception if your validator cannot handle the passed type so that it can be marked as invalid
            throw new UnexpectedValueException($value, 'array');
        }

        $productVariantCode = $value['product_variant_code'];
        $email = $value['email'];
        if (!is_string($productVariantCode) || !is_string($email)) {
            return;
        }
        $productVariant = $this->productVariantRepository->findOneBy(['code' => $productVariantCode]);
        if (!$productVariant instanceof ProductVariantInterface) {
            return;
        }

        if ([] !== $this->subscriptionRepository->findBy([
            'email' => $email,
            'productVariant' => $productVariant,
            'notify' => false,
        ])) {
            $this->context
                ->buildViolation($constraint->message)
                ->addViolation()
            ;
        }
    }
}
