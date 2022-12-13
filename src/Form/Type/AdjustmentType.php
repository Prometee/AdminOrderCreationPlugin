<?php

declare(strict_types=1);

namespace Sylius\AdminOrderCreationPlugin\Form\Type;

use Sylius\Bundle\MoneyBundle\Form\Type\MoneyType;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Core\Model\Adjustment;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Range;
use Webmozart\Assert\Assert;

final class AdjustmentType extends AbstractResourceType
{
    public const ORDER_DISCOUNT_ADJUSTMENT = 'order_discount';

    public const ORDER_ITEM_DISCOUNT_ADJUSTMENT = 'order_item_discount';

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('amount', MoneyType::class, [
            'label' => $options['label'],
            'currency' => $options['currency'],
            'constraints' => [
                new Range(['min' => 0, 'minMessage' => 'sylius_admin_order_creation.order_discount', 'groups' => ['sylius']]),
            ],
        ]);

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) use ($options): void {
            /** @var Adjustment|null $adjustment */
            $adjustment = $event->getData();

            if ($adjustment === null) {
                return;
            }

            $adjustment->setLabel('sylius_admin_order_creation.ui.order_discount');
            $adjustment->setAmount(-1 * $adjustment->getAmount());

            $type = $options['type'];
            Assert::string($type);

            $adjustment->setType($type);

            $event->setData($adjustment);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setRequired('label');
        $resolver->setRequired('currency');
        $resolver->setRequired('type');
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_admin_order_creation_new_order_adjustment';
    }
}
