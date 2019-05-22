<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Outing;
use App\Entity\Site;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\User\User;

class OutingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('site', EntityType::class, ['label'=>'Site concerné', 'class' => Site::class, 'choice_label' => 'nom', 'attr'=> ['class'=>'choice']])
            ->add('nom', TextType::class, ['label' => 'Nom de la sortie', 'attr' => ['placeholder' => 'Aquaponey, Curling..', 'class' => 'input-field']])
            ->add('dateHeureDebut', DateType::class, ['label' => "Date de l'evenement", 'widget' => 'single_text', 'attr' => ['class' => "input-field datepicker"]])
            ->add('duree', IntegerType::class, ['label' => "Durée de l'evenement", 'attr' => ['class' => "input-field"]])
            ->add('dateLimiteInscription', DateType::class, ['label' => "Date de l'evenement", 'widget' => 'single_text', 'attr' => ['class' => "input-field datepicker"]])
            ->add('nbInscriptionsMax', IntegerType::class, ['label' => "Nombre de participants (maximum)", 'attr' => ['class' => "input-field"]])
            ->add('infosSortie', TextareaType::class, ['label' => "Infos sur l'evenement", 'attr' => ['class' => "input-field"]])
            ->add('lieu', EntityType::class, ['label' => "Lieu de l'evenement", 'class' => Lieu::class, 'choice_label' => 'nom', 'attr' => ['class' => 'ajaxSite']]);

            }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Outing::class,
        ]);
    }
}
