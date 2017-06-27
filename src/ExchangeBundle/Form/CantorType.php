<?php
namespace ExchangeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CantorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('britishPound', NumberType::class, array(
                'label' => 'GBP'
            ))
            ->add('euro', NumberType::class, array(
                'label' => 'EUR'
            ))
            ->add('uSDollar', NumberType::class, array(
                'label' => 'USD'
            ))
            ->add('czechKoruna', NumberType::class, array(
                'label' => 'CZK'
            ))
            ->add('russianRuble', NumberType::class, array(
                'label' => 'RUB'
            ))
            ->add('swissFranc', NumberType::class, array(
                'label' => 'CHF'
            ))
            ->add('save', SubmitType::class, array(
                'label' => 'Add funds'
            ));
    }

    public function getName()
    {
        return 'cantor';
    }

    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ExchangeBundle\Entity\Cantor'
        ));
    }
}