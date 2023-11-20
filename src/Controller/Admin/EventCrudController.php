<?php

namespace App\Controller\Admin;

use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class EventCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Event::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->hideOnForm(),
            TextField::new('name'),
            TextEditorField::new('description'),
            DateTimeField::new('startDate')
                ->setFormat('dd/MM/yyyy HH:mm'),
            DateTimeField::new('endDate')
                ->setFormat('dd/MM/yyyy HH:mm'), 
            TextField::new('location'),
            AssociationField::new('user', 'Créé par')
                ->hideOnForm(),
            ImageField::new('image')
                ->setLabel('Image')
                ->setUploadDir('public/images/')
                ->setRequired(false)
            ];
    }

    public function createEntity(string $entityFqcn)
    {
        $event = new Event();
        
        // Assignez l'utilisateur actuel à l'événement
        $event->setUser($this->getUser());

        return $event;
    }
}
