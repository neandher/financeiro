<?php

namespace AppBundle\Form;

use AppBundle\Entity\BillCategory;
use AppBundle\Entity\BillPlanCategory as BillPlanCategoryEntity;
use AppBundle\Repository\BillCategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BillPlanCategoryType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', TextType::class, ['label' => 'billPlanCategory.fields.description'])
            ->add('billCategory', EntityType::class,
                [
                    'class' => BillCategory::class,
                    'query_builder' => function (BillCategoryRepository $er) {
                        return $er->queryLatestForm();
                    },
                    'choice_label' => 'description',
                    'label' => 'billCategory.title.menu'
                ]);;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => BillPlanCategoryEntity::class
        ]);
    }
}
