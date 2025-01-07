<?php

namespace App\Form;

use App\Entity\Categories;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('price')
            ->add('stock')
            ->add('brand')
            ->add('size')
            ->add('color')
            // ->add('image')
            // ->add('created_at', null, [
            //     'widget' => 'single_text',
            // ])
            // ->add('updated_at', null, [
            //     'widget' => 'single_text',
            // ])
            ->add('photo', FileType::class, [
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new Image(['maxSize'=>'5000k'])
                ]
            ])
            ->add('category', EntityType::class, [ 
                'class' => Categories::class, 
                'choice_label' => 'name', 
                'multiple'=>true,
                'expanded'=>false,
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
