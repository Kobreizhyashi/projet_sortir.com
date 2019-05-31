<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class NewPwdType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('newPassword', RepeatedType::class, array(
                'mapped' => false,
                'constraints' => [new Length(['min' => 4],['max' => 30])],
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
