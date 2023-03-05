<?php

namespace App\Form;

use App\Entity\Pret;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PretType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder


            ->add('duree')
            ->add('Taux')
            ->add('montant')
            ->add('raison', ChoiceType::class, array(
                'choices' => array(
                    "Lancement d'un projet" => "Lancement d'un projet",
                    'Études' => 'Études',
                    'Investissements' => 'Investissements',
                    'Travaux' => 'Travaux',
                    "Autre" => "Autre",
                ),
                'expanded' => false,
                'multiple' => false,
            ))
            ->add('poste')
            ->add('debut_travail', DateType::class, [
                'widget' => 'single_text',

            ])
            ->add('revenu');
          /*   ->add('mensualite'); */
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pret::class,
        ]);
    }
}
