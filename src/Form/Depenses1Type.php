<?php

namespace App\Form;

use App\Entity\Depenses;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Budget;
class Depenses1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            
            ->add('title')
            ->add('prenom_destinataire')
            ->add('rib_destinataire')
            ->add('montant')
            ->add('backgroundcolor',ColorType::class)
            ->add('datedebut',DateType::class, [
                'widget' => 'single_text'
            ])
            ->add('categorie_depense',ChoiceType::class, [
                'choices'  => [
                    'credit' => 'credit',
                    'famille' => 'famille',
                    'santé' => 'santé',
                    'loisirs et culture' => 'loisirs et culture',
                    
                ],
            ])
            ->add('type_depense',ChoiceType::class, [
                'choices'  => [
                    'virement' => 'virement',
                    'cash' => 'cash',
                    
                ],
            ])
            ->add('idbudget', EntityType::class, [
                'class' => Budget::class,
                'choice_label' => 'id',
                'attr' => [
                    'class' => 'hidden',
                    'style' => 'display:none;'
                ]
            ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Depenses::class,
        ]);
    }
}
