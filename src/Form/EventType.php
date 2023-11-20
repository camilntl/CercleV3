<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('startDate', DateTimeType::class, [
                'widget' => 'single_text',
                // Ajoutez d'autres options ici si nécessaire
            ])

            ->add('endDate', DateTimeType::class, [
                'widget' => 'single_text',
                // Ajoutez d'autres options ici si nécessaire
            ])
            ->add('location')
            ->add('image', FileType::class, [
                'label' => 'Image de l\'événement',
                'mapped' => false, // Important si le champ n'est pas mappé directement à la propriété de l'entité
                'required' => false
            ]);
    }
    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
