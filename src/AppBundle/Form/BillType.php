<?php

namespace AppBundle\Form;

use AppBundle\Entity\Bill;
use AppBundle\Entity\BillCategory;
use AppBundle\Entity\BillPlan;
use AppBundle\Repository\BillCategoryRepository;
use AppBundle\Repository\BillPlanRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
            ->add('note', TextareaType::class, ['label' => 'bill.fields.note'])
            ->add('billCategory', EntityType::class,
                [
                    'class' => BillCategory::class,
                    'query_builder' => function (BillCategoryRepository $er) {
                        return $er->queryLatestForm();
                    },
                    'choice_label' => 'description',
                    'label' => 'billCategory.title.menu',
                    'placeholder' => '',
                ]
            )
            ->add('choices', BankTypeChoices::class)
            ->add('billInstallments', CollectionType::class,
                [
                    'entry_type' => BillInstallmentsType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'label' => 'billInstallments.title.menu',
                    'label_attr' => ['class' => 'hide'],
                    'attr' => ['class' => 'hide'],
                ]
            )
            ->add('billFiles', CollectionType::class,
                [
                    'entry_type' => BillFilesType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'label' => 'billFiles.title.menu',
                    'label_attr' => ['class' => 'hide'],
                    'attr' => ['class' => 'hide'],
                ]
            );

        $formModifier = function (FormInterface $form, BillCategory $billCategory = null) {

            if ($billCategory === null) {
                $billPlanOptions = [
                    'choices' => []
                ];
            } else {
                $billPlanOptions = [
                    'query_builder' => function (BillPlanRepository $er) use ($billCategory) {
                        return $er->queryLatestForm($billCategory->getId());
                    }
                ];
            }

            $form->add('billPlan', EntityType::class,
                array_merge(
                    [
                        'class' => BillPlan::class,
                        'choice_label' => 'getDescriptionWithPlanCategory',
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
                $formModifier($event->getForm(), $data->getBillCategory());
            }
        );

        $builder->get('billCategory')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                $billCategory = $event->getForm()->getData();
                $formModifier($event->getForm()->getParent(), $billCategory);
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
