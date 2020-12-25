<?php

namespace App\Form;

use App\Entity\PurchaseDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PurchaseDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // ->add('precio',NumberType::class)
            // ->add('cantidad',NumberType::class)
            // ->add('fecha')
            // ->add('purchase')
            // ->add('product')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PurchaseDetail::class,
            'csrf_protection' => false,
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }

    public function getName(){
        return '';
    }

}
