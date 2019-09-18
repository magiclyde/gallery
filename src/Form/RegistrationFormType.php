<?php

namespace App\Form;

use App\Validation\AvailableEmailConstraint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Email;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label'       => 'Email',
                'attr'        => ['placeholder' => 'Email'],
                'constraints' => [
                    new NotBlank(),
                    new Email(),
                    new AvailableEmailConstraint(),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type'            => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options'         => ['attr' => ['class' => 'password-field']],
                'required'        => true,
                'first_options'   => [
                    'label'       => 'Password',
                    'attr'        => [
                        'placeholder' => 'Password',
                    ],
                    'constraints' => [
                        new NotBlank(),
                        new Length([
                            'min' => 6, 
                            'minMessage' => 'Your password should be at least {{ limit }} characters',
                            'max' => 35]
                        ),
                    ],
                ],
                'second_options'  => [
                    'label' => 'Repeat Password',
                    'attr'  => [
                        'placeholder' => 'Repeat password',
                    ],
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],

            ])
        ;
    }

}
