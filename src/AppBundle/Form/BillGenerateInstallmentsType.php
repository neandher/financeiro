<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BillGenerateInstallmentsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dueDateAt', DateType::class,
                [
                    'label' => 'billInstallments.fields.dueDateAt',
                    'widget' => 'single_text',
                    'html5' => false,
                    'format' => 'dd-MM-yyyy',
                    'attr' => ['class' => 'js-datepicker', 'readonly' => true]
                ]
            )
            ->add('amount', TextType::class,
                [
                    'label' => 'billInstallments.fields.amount',
                    'attr' => ['onkeydown' => 'Formata(this,20,event,2);']
                ]
            )
            ->add('number', NumberType::class,
                [
                    'label' => 'billInstallments.generate.number',
                    'attr' => ['onkeypress' => 'return MaskNumber(this,event);']
                ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}
