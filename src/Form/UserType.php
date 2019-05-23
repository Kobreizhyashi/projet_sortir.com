<?php

namespace App\Form;

use App\Entity\Site;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('prenom')
            ->add('telephone')
//            ->add('administrateur', CheckboxType::class, [
//                'label'    => 'Administrateur',
//                'required' => false,
//            ])
            //->add('actif')
            ->add('username', TextType::class, ['label'=>'Pseudo'])
            ->add('email')
//            ->add('password',RepeatedType::class,[
//                'type'=>PasswordType::class,
//                'invalid_message'=>'Les champs mot de passe doivent être identiques',
//                'required'=>true,
//                'first_options'=>array('label'=>'Mot de passe'),
//                'second_options'=>array('label'=>'Répéter mot de passe'),
//            ])
            ->add('site', EntityType::class, [
                'label'=>'Site de rattachement', 'class' => Site::class, 'choice_label' => 'nom',
                'attr'=> ['class'=>'choice']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
