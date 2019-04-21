<?php

namespace Portal\Form;

use Core\Entity\User;
use Core\Validator\Constraints\EntityNotExists;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'required' => true,
                'attr' => ['placeholder' => 'Your email address', 'class' => 'form-control-lg'],
                'constraints' => [
                    new NotBlank(),
                    new Email(['message' => "The '{{ value }}' is not a valid email!"]),
                    new EntityNotExists([
                        'entityClass' => User::class,
                        'field' => 'email',
                        'message' => 'Email {{ value }} has already been taken!'
                    ])
                ]
            ])
            ->add('name', TextType::class, [
                'required' => true,
                'attr' => ['placeholder' => 'Your name', 'class' => 'form-control-lg'],
                'constraints' => [
                    new NotBlank(),
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
                'first_name' => 'first',
                'first_options' => [
                    'attr' => ['placeholder' => 'Password', 'class' => 'form-control-lg'],
                    'constraints' => [new NotBlank(), new Length(['min' => 8])]
                ],
                'second_name' => 'second',
                'second_options' => [
                    'attr' => ['placeholder' => 'Repeat password', 'class' => 'form-control-lg'],
                    'constraints' => [new NotBlank(), new Length(['min' => 8])]
                ]
            ])
            ;
    }
}
