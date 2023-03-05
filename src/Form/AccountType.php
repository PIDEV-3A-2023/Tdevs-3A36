<?php

namespace App\Form;

use App\Entity\Account;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;


class AccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('NomComplet')
            ->add('NumTel')
            ->add('Email')
            ->add('Sexe', ChoiceType::class, array(
                'choices' => array(
                    'Male' => 'male',
                    'Female' => 'female',
                    'Other' => 'other',
                ),
                'expanded' => false,
                'multiple' => false,
            ))
            ->add('DateNaiss', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('Adresse')
            ->add('Ville')
            ->add('brochure', FileType::class, [
                'label' => 'Brochure (Carte CIN)',

                'mapped' => false,

                'required' => false,

                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'image/png',
                            'image/jpeg',
                            'image/jpg',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid ImageFile',
                    ])
                ],
            ])
            ->add('solde')
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Account::class,
        ]);
    }
}
