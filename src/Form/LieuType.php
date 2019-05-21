<?php

namespace App\Form;

use App\Entity\Lieu;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LieuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, ['label' => 'Nom du lieu', 'attr'=>['placeholder'=>'Bar branché, Bowling sur béton', 'class'=>'input-field']])
            ->add('rue', TextType::class, ['label' => 'Nom de la rue', 'attr'=>['placeholder'=>'Rue du rouge, allée bolchevik..', 'class'=>'input-field']])
            ->add('latitude', IntegerType::class, ['label' => 'Latitude', 'attr'=>['placeholder'=>'103, 666..', 'class'=>'input-field']])
            ->add('longitude', IntegerType::class, ['label' => 'Latitude', 'attr'=>['placeholder'=>'65, 607..', 'class'=>'input-field']])
            ->add('ville', TextType::class, ['label' => 'Nom de la ville', 'attr' => ['placeholder' => 'Pyongyang, Moscou..', 'class'=>'input-field']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
        ]);
    }
}
