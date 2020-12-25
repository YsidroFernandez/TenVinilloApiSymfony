<?php

namespace App\Form;

use App\Entity\Cart;
use App\Entity\CartDetail;
use App\Entity\Shirt;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CartDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cantidad',IntegerType::class)
            ->add('precio',NumberType::class)
            // ->add('cart', EntityType::class, [
            //     // looks for choices from this entity
            //     'class' => Cart::class,
            
            //     // uses the User.username property as the visible option string
            //     'choice_label' => 'id',
            // ])
            ->add('product', EntityType::class, [
                // looks for choices from this entity
                'class' => Shirt::class,
            
                // uses the User.username property as the visible option string
                'choice_label' => 'id',
            ]);
            // ->add('user',IntegerType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CartDetail::class,
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
