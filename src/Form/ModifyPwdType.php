<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModifyPwdType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('currentPassword', PasswordType::class, array(
                'mapped' => false,'label'=>'Mot de passe actuel',
                'attr'=>['placeholder'=>'Veuillez renseigner le mot de passe actuel']
            ))
            ->add('newPassword', RepeatedType::class, array(
                'mapped' => false,
                'type' => PasswordType::class,
                'invalid_message' => 'Les 2 mots de passe doivent être identiques !',
                'options' => array('attr' => array('class' => 'password-field')),
                'required' => true,
                'first_options' => array('label' => 'Nouveau mot de passe',
                    'attr'=>['placeholder'=>'Veuillez renseigner le nouveau mot de passe souhaité']),
                'second_options' => array('label' => 'Confirmation mot de passe',
                    'attr'=>['placeholder'=>'Veuillez confirmer le nouveau mot de passe'])
            ));
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
