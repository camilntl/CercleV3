<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('slug', null, [
                'attr' => ['class' => 'hidden']
            ])
            ->add('content', TextareaType::class)
            //->add('featuredText')
            //->add('createdAt')
            //->add('updatedAt')
            ->add('image', FileType::class, [
                'label' => 'Image (fichier image)',
                'mapped' => false, // si l'image n'est pas directement liée à la propriété de l'entité
                'required' => false,
                'constraints' => [
                    new \Symfony\Component\Validator\Constraints\File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger un fichier image valide',
                    ])
                ],
                'attr' => ['class' => 'tailwind-class-here'], // Ajoutez vos classes Tailwind ici
            ])
            //->add('categories')
            //->add('featuredImage')
            //->add('user')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
