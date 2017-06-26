<?php

namespace ExchangeBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'invalid_message' => 'Passwords must be the same.',
                'options' => array('attr' => array('class' => 'password_field')),
                'required' => true,
                'first_options' => array(
                    'label' => 'Password:'
                ),
                'second_options' => array(
                    'label' => 'Repeat password:'
                ),
                'constraints' => array(
                    new NotBlank(array(
                        'message' => 'Password should not be blank.'
                    )),
                    new Regex(array(
                        'pattern' => "/^((?=\S*?[A-Z])(?=\S*?[a-z])(?=\S*?[0-9]).{5,15})\S$/",
                        'match' => true,
                        'message' => 'Password should contain at least one digit,
                            one lowercase letter and one uppercase letter. Password length from 5 to 15 signs'
                    ))
                )
            ))
            ->add('save', SubmitType::class, array(
                'label' => 'Change password'
            ));
    }

    public function getName()
    {
        return 'user';
    }

    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ExchangeBundle\Entity\User'
        ));
    }
}