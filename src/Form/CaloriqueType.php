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

    const REGIME_CHOICES = [
        'vegan' => 'Vegan',
        'vegetarian' => 'Vegetarian',
        'pescatarian' => 'Pescatarian',
        'omnivore' => 'Omnivore',
    ];

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('objectif', ChoiceType::class, [
                'choices' => array_flip(self::OBJECTIF_CHOICES),
                'expanded' => true, // Utiliser des boutons radio
                'multiple' => false, // Ne pas autoriser la sélection multiple
            ])
            ->add('besoinsCaloriques')
            ->add('activite', ChoiceType::class, [
                'choices' => array_flip(self::ACTIVITE_CHOICES),
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('regimeAlimentaire', ChoiceType::class, [
                'choices' => array_flip(self::REGIME_CHOICES),
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('niveauStress', ChoiceType::class, [
                'choices' => array_flip(self::NIVEAU_STRESS_CHOICES),
                'expanded' => true,
                'multiple' => false,
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
