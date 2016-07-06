<?php

namespace AppBundle\Form;

use AppBundle\Entity\Bank;
use AppBundle\Entity\Bill;
use AppBundle\Entity\BillPlan;
use AppBundle\Repository\BankRepository;
use AppBundle\Repository\BillPlanRepository;
use AppBundle\Repository\BillTypeRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BillType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', TextType::class, ['label' => 'bill.fields.description'])
            ->add('amount', TextType::class, ['label' => 'bill.fields.amount'])
            ->add('billType', EntityType::class,
                [
                    'class' => \AppBundle\Entity\BillType::class,
                    'query_builder' => function (BillTypeRepository $er) {
                        return $er->queryLatestForm();
                    },
                    'choice_label' => 'description',
                    'label' => 'billType.title.menu'
                ]
            )
            ->add('billPlan', EntityType::class, $this->billPlanOptions($options))
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
        $resolver->setDefaults([
            'data_class' => Bill::class,
            'bill_type_id' => null
        ]);
    }

    private function billPlanOptions($options)
    {
        $billPlanOptionsDefault = [
            'class' => BillPlan::class,
            'choice_label' => 'getStringSelectForm',
            'label' => 'billPlan.title.menu',
        ];

        if (is_null($options['bill_type_id'])) {
            $billPlanOptions = array_merge($billPlanOptionsDefault, [
                'choices' => [],
            ]);
        } else {
            $billPlanOptions = array_merge($billPlanOptionsDefault, [
                'query_builder' => function (BillPlanRepository $er) use ($options) {
                    return $er->queryLatestForm($options['bill_type_id']);
                },
            ]);
        }

        return $billPlanOptions;
    }
}
