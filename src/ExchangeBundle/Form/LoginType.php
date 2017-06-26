<?php
namespace ExchangeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->
        add('login', TextType::class, array(
            'label' => 'Login:',
            'required' => 'true'
        ))
            ->add('password', PasswordType::class, array(
                'label' => 'Password:',
                'required' => 'true'
            ))
            ->add('save', SubmitType::class, array(
                'label' => 'Login'
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