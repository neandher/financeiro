<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('login', TextType::class, ['label' => 'Usuário', 'required' => true])
            ->add('password', PasswordType::class, ['label' => 'Senha', 'required' => true])
            ->add('_remember_me', CheckboxType::class, [
                'label' => 'Lembrar-me',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'csrf_protection' => true,
                'csrf_field_name' => '_token',
                'csrf_token_id' => 'authentication'
            ]
        );
    }
}
