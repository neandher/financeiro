<?php

namespace AppBundle\Form;

use AppBundle\Entity\BillPlan;
use Doctrine\ORM\EntityRepository;
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
            ->add('billPlanType', EntityType::class,
                [
                    'class' => \AppBundle\Entity\BillPlanType::class,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('billPlanType')
                            ->orderBy('billPlanType.description', 'ASC');
                    },
                    'choice_label' => 'description',
                    'label' => 'billPlanType.title.menu'
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
