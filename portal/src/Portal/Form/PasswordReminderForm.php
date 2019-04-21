<?php

namespace Portal\Form;

use Core\Entity\User;
use Core\Validator\Constraints\EntityExists;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class PasswordReminderForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'required' => true,
                'attr' => ['placeholder' => 'Adres email', 'class' => 'form-control-lg'],
                'constraints' => [
                    new NotBlank(),
                    new Email(['message' => "The '{{ value }}' is not a valid email!"]),
                    new EntityExists([
                        'entityClass' => User::class,
                        'field' => 'email',
                        'message' => 'Email {{ value }} has not been found!'
                    ])
                ]
            ])
            ;
    }
}
