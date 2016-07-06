<?php

namespace AppBundle\Form;

use AppBundle\Entity\BillPlanType as BillPlanTypeEntity;
use AppBundle\Entity\BillType;
use AppBundle\Repository\BillTypeRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BillPlanTypeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', TextType::class, ['label' => 'billPlanType.fields.description'])
            ->add('billType', EntityType::class,
                [
                    'class' => BillType::class,
                    'query_builder' => function (BillTypeRepository $er) {
                        return $er->queryLatestForm();
                    },
                    'choice_label' => 'description',
                    'label' => 'billType.title.menu'
                ]);;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => BillPlanTypeEntity::class
        ]);
    }
}
