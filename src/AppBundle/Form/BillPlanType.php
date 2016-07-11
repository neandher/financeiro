<?php

namespace AppBundle\Form;

use AppBundle\Entity\BillPlan;
use AppBundle\Entity\BillPlanCategory;
use AppBundle\Repository\BillPlanCategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BillPlanType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', TextType::class, ['label' => 'billPlan.fields.description'])
            ->add('billPlanCategory', EntityType::class,
                [
                    'class' => BillPlanCategory::class,
                    'query_builder' => function (BillPlanCategoryRepository $er) {
                        return $er->queryLatestForm();
                    },
                    'choice_label' => 'description',
                    'label' => 'billPlanCategory.title.menu'
                ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => BillPlan::class
        ]);
    }
}
