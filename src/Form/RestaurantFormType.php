<?php

namespace App\Form;

use App\Entity\Restaurant;
use App\Entity\User;
use PhpParser\Node\Scalar\MagicConst\File;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;


class RestaurantFormType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('slug')
            ->add('contact')
            ->add('photo')
            ->add('disclaimer')
            ->add('termsAndConditions')
            ->add('Email')
            ->add('User', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('photo', FileType::class, [
                'label' => 'Photo (JPEG or PNG file)',
                'required' => false,
                'data_class' => null,
                'mapped' => false, 
            ])
            ->add('menu', FileType::class, [
                'label' => 'Menu (PDF file)',
                'required' => false,
            ])<
            $builder->get('name')->setData($options['data']->getName());
        $builder->get('slug')->setData($options['data']->getSlug());
        $builder->get('contact')->setData($options['data']->getContact());
         
        
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Restaurant::class,
        ]);
    }
}


