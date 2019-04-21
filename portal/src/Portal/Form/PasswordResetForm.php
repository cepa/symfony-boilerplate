<?php

namespace Portal\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class PasswordResetForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
                'first_name' => 'first',
                'first_options' => [
                    'attr' => ['placeholder' => 'Hasło do konta', 'class' => 'form-control-lg'],
                    'constraints' => [new NotBlank(), new Length(['min' => 8])]
                ],
                'second_name' => 'second',
                'second_options' => [
                    'attr' => ['placeholder' => 'Powtórz hasło', 'class' => 'form-control-lg'],
                    'constraints' => [new NotBlank(), new Length(['min' => 8])]
                ]
            ])
        ;
    }
}
