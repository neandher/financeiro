<?php

namespace AppBundle\Form;

use AppBundle\Entity\Bank;
use AppBundle\Entity\Bill;
use AppBundle\Entity\BillInstallments;
use AppBundle\Entity\BillPlan;
use AppBundle\Repository\BankRepository;
use AppBundle\Repository\BillPlanRepository;
use AppBundle\Repository\BillTypeRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
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
                    'label' => 'billType.title.menu',
                    'placeholder' => '',
                ]
            )
            ->add('bank', EntityType::class, [
                    'class' => Bank::class,
                    'query_builder' => function (BankRepository $er) {
                        return $er->queryLatestForm();
                    },
                    'choice_label' => 'description',
                    'label' => 'bank.title.menu'
                ]
            )
            ->add('billInstallments', CollectionType::class,
                [
                    'entry_type' => BillInstallmentsType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'label_format' => ' '
                ]
            );

        $formModifier = function (FormInterface $form, \AppBundle\Entity\BillType $billType = null) {

            if ($billType === null) {
                $billPlanOptions = [
                    'choices' => []
                ];
            } else {
                $billPlanOptions = [
                    'query_builder' => function (BillPlanRepository $er) use ($billType) {
                        return $er->queryLatestForm($billType->getId());
                    }
                ];
            }

            $form->add('billPlan', EntityType::class,
                array_merge(
                    [
                        'class' => BillPlan::class,
                        'choice_label' => 'getStringSelectForm',
                        'label' => 'billPlan.title.menu',
                        'placeholder' => '',
                    ],
                    $billPlanOptions
                )
            );
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                /** @var Bill $data */
                $data = $event->getData();
                $formModifier($event->getForm(), $data->getBillType());
            }
        );

        $builder->get('billType')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                $billType = $event->getForm()->getData();
                $formModifier($event->getForm()->getParent(), $billType);
            }
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Bill::class,
        ]);
    }
}
