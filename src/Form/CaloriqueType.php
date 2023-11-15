<?php

namespace App\Form;

use App\Entity\Calorique;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CaloriqueType extends AbstractType
{
    // Define constants for choices
    const OBJECTIF_CHOICES = [
        'loseWeight' => 'Lose Weight',
        'maintain' => 'Maintain',
        'buildMuscle' => 'Build Muscle',
    ];

    const ACTIVITE_CHOICES = [
        'sedentary' => 'Sedentary',
        'lightlyActive' => 'Lightly Active',
        'moderatelyActive' => 'Moderately Active',
        'veryActive' => 'Very Active',
    ];

    const NIVEAU_STRESS_CHOICES = [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
    ];

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('objectif', ChoiceType::class, [
                'choices' => array_flip(self::OBJECTIF_CHOICES),
            ])
            ->add('besoinsCaloriques')
            ->add('activite', ChoiceType::class, [
                'choices' => array_flip(self::ACTIVITE_CHOICES),
            ])
            ->add('regimeAlimentaire')
            ->add('niveauStress', ChoiceType::class, [
                'choices' => array_flip(self::NIVEAU_STRESS_CHOICES),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Calorique::class,
        ]);
    }
}
