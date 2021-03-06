<?php

namespace AppBundle\Form;

use AppBundle\Entity\BillInstallments;
use AppBundle\Entity\PaymentMethod;
use AppBundle\Repository\PaymentMethodRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BillInstallmentsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
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
                    'label' => 'billInstallments.fields.amount', 'required' => false,
                    'attr' => ['onkeydown' => 'Formata(this,20,event,2);']
                ]
            )
            ->add('paymentDateAt', DateType::class,
                [
                    'label' => 'billInstallments.fields.paymentDateAt',
                    'required' => false,
                    'widget' => 'single_text',
                    'html5' => false,
                    'format' => 'dd-MM-yyyy',
                    'attr' => ['class' => 'js-datepicker', 'readonly' => true]
                ]
            )
            ->add('amountPaid', TextType::class,
                [
                    'label' => 'billInstallments.fields.amountPaid',
                    'attr' => ['onkeydown' => 'Formata(this,20,event,2);']
                ]
            )
            ->add('paymentMethod', EntityType::class,
                [
                    'class' => PaymentMethod::class,
                    'query_builder' => function (PaymentMethodRepository $er) {
                        return $er->queryLatestForm();
                    },
                    'choice_label' => 'description',
                    'label' => 'paymentMethod.title.menu',
                ]
            )
            ->add('description', TextType::class, ['label' => 'billInstallments.fields.description']);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => BillInstallments::class
        ));
    }
}
