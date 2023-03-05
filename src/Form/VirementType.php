<?php

namespace App\Form;

use App\Entity\Virement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VirementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('montant')
            ->add('date_virement',DateType::class, [
                'widget' => 'single_text'
            ])
            ->add('titre')
            ->add('prenom_benef')
            ->add('rib_benef')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Virement::class,
        ]);
    }
}
