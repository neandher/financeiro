<?php

namespace AppBundle\Form;

use AppBundle\Entity\Bank;
use AppBundle\Repository\BankRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BankTypeChoices extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('bank', EntityType::class, [
                    'class' => Bank::class,
                    'query_builder' => function (BankRepository $er) {
                        return $er->queryLatestForm();
                    },
                    'choice_label' => 'description',
                    'label' => 'bank.title.menu'
                ]
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            ['inherit_data' => true]
        );
    }
}
