<?php

namespace AppBundle\Form;

use AppBundle\Entity\Bank;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BankType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', TextType::class, ['label' => 'bank.fields.description'])
            ->add('agency', TextType::class, ['label' => 'bank.fields.agency'])
            ->add('account', TextType::class, ['label' => 'bank.fields.account']);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Bank::class
        ]);
    }
}
