<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('marque')
            ->add('ref')
            ->add('prix_unit')
            ->add('quantity')
            ->add('description')
            ->add('image', FileType::class,
            [
                'data_class' => null,
                'required' => false,
                'empty_data' => ""
            ])
            ->add('category', EntityType::class ,[
                'class' => Category::class,
                'choice_label' =>'name',
                'attr' =>[
                    'class' => 'cnbv w-75 text-light'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
