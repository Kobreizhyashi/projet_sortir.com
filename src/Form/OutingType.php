<?php

namespace App\Form;

use App\Entity\Outing;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OutingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, ['label' => 'Nom de la sortie', 'attr'=>['placeholder'=>'Aquaponey, Curling..', 'class'=>'input-field']])
            ->add('dateHeureDebut', DateType::class, ['label'=>"Date de l'evenement", 'widget' => 'single_text', 'attr'=>['class'=>"input-field datepicker"]])
            ->add('duree', IntegerType::class, ['label'=>"Durée de l'evenement", 'attr'=>['class'=>"input-field"]])
            ->add('dateLimiteInscription', DateType::class, ['label'=>"Date de l'evenement", 'widget' => 'single_text', 'attr'=>['class'=>"input-field datepicker"]])
            ->add('nbInscriptionsMax', IntegerType::class, ['label'=>"Nombre de participants (maximum)", 'attr'=>['class'=>"input-field"]])
            ->add('infosSortie', TextareaType::class, ['label'=>"Infos sur l'evenement", 'attr'=>['class'=>"input-field"]])
            // ->add('etat') ???????
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Outing::class,
        ]);
    }
}
