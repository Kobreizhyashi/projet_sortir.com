<?php

namespace App\Form;

use App\Entity\ModifyPassword;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ModifyPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('currentPassword', PasswordType::class, array(
                'mapped' => false,'label'=>'Mot de passe actuel',
                'attr'=>['placeholder'=>'Veuillez renseigner le mot de passe actuel']
            ))
            ->add('plainPassword', RepeatedType::class, array(
                'mapped' => false,
                'type' => PasswordType::class,
                'invalid_message' => 'Les deux mots de passe doivent être identiques',
                'options' => array(
                    'attr' => array(
                        'class' => 'password-field'
                    )
                ),
                'required' => true,
            ))

        ;    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }




}
