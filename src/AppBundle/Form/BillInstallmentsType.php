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
            ->add('dueDateAt', DateType::class, ['label' => 'billInstallments.fields.dueDateAt'])
            ->add('paymentDateAt', DateType::class, ['label' => 'billInstallments.fields.paymentDateAt', 'required' => false])
            ->add('amount', TextType::class, ['label' => 'billInstallments.fields.amount', 'required' => false])
            ->add('amountPaid', TextType::class, ['label' => 'billInstallments.fields.amountPaid'])
            ->add('paymentMethod', EntityType::class,
                [
                    'class' => PaymentMethod::class,
                    'query_builder' => function (PaymentMethodRepository $er) {
                        return $er->queryLatestForm();
                    },
                    'choice_label' => 'description',
                    'label' => 'paymentMethod.title.menu',
                ]
            );
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
