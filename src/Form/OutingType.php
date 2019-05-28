<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Outing;
use App\Entity\Site;
use App\Entity\Ville;
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

        $citation = " Vous savez, moi je ne crois pas qu’il y ait de bonne ou de mauvaise situation. Moi, si je devais résumer ma vie aujourd’hui avec vous, je dirais que c’est d’abord des rencontres. Des gens qui m’ont tendu la main, peut-être à un moment où je ne pouvais pas, où j’étais seul chez moi. Et c’est assez curieux de se dire que les hasards, les rencontres forgent une destinée... Parce que quand on a le goût de la chose, quand on a le goût de la chose bien faite, le beau geste, parfois on ne trouve pas l’interlocuteur en face je dirais, le miroir qui vous aide à avancer. Alors ça n’est pas mon cas, comme je disais là, puisque moi au contraire, j’ai pu : et je dis merci à la vie, je lui dis merci, je chante la vie, je danse la vie... je ne suis qu’amour ! Et finalement, quand beaucoup de gens aujourd’hui me disent « Mais comment fais-tu pour avoir cette humanité ? », et bien je leur réponds très simplement, je leur dis que c’est ce goût de l’amour ce goût donc qui m’a poussé aujourd’hui à entreprendre une construction mécanique, mais demain qui sait ? Peut-être simplement à me mettre au service de la communauté, à faire le don, le don de soi...";

        $builder

            ->add('nom', TextType::class,
                ['label' => 'Nom de la sortie',
                    'attr' => ['placeholder' => 'Aquaponey, Curling..', 'class' => 'input-field']])
            ->add('dateHeureDebut',
                DateTimeType::class,
                ['label' => "Date et heure de l'evenement",
                    'widget' => 'single_text',
                    'attr' => ['class' => "input-field datepicker"]])
            ->add('duree',
                IntegerType::class,
                ['label' => "Durée de l'evenement",
                    'attr' => ['class' => "input-field"]])
            ->add('dateLimiteInscription',
                DateType::class,
                ['label' => "Date limite d'inscription",
                    'widget' => 'single_text',
                    'attr' => ['class' => "input-field datepicker"]])
            ->add('nbInscriptionsMax',
                IntegerType::class,
                ['label' => "Nombre de participants (maximum)",
                    'attr' => ['class' => "input-field"]])
            ->add('infosSortie',
                TextareaType::class,
                ['label' => "Infos sur l'evenement",
                    'attr' => ['class' => "input-field", "placeholder" => $citation]])
            ->add('lieu',
                EntityType::class,
                ['label' => "Lieu de l'evenement",
                    'class' => Lieu::class,
                    'choice_label' => 'nom',
                    'attr' => ['class' => 'ajaxSite']]);
            }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Outing::class,
        ]);
    }
}
