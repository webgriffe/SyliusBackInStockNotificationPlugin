<?php

declare(strict_types=1);

namespace Webgriffe\SyliusBackInStockNotificationPlugin\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Webgriffe\SyliusBackInStockNotificationPlugin\Validator\SubscriptionUnique;

/**
 * @TODO: Transform this in an entity type?
 */
final class SubscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank([], null, null, null, ['webgriffe_sylius_back_in_stock_notification_plugin']),
                    new Email(['mode' => Email::VALIDATION_MODE_STRICT], null, null, null, ['webgriffe_sylius_back_in_stock_notification_plugin']),
                ],
            ])
            ->add('product_variant_code', HiddenType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'validation_groups' => ['webgriffe_sylius_back_in_stock_notification_plugin'],
            'constraints' => [new SubscriptionUnique(
                null,
                ['webgriffe_sylius_back_in_stock_notification_plugin'],
            )],
        ]);
    }
}
