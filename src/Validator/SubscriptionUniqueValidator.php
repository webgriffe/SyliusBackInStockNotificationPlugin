<?php

declare(strict_types=1);

namespace Webgriffe\SyliusBackInStockNotificationPlugin\Validator;

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Customer\Context\CustomerContextInterface;
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
        private CustomerContextInterface $customerContext,
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
        if (array_key_exists('email', $value)) {
            $email = $value['email'];
        } else {
            $customer = $this->customerContext->getCustomer();
            if (!$customer instanceof CustomerInterface) {
                return;
            }
            $email = $customer->getEmail();
        }
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
