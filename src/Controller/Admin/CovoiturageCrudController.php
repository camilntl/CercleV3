<?php

namespace App\Controller\Admin;

use App\Entity\Covoiturage;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CovoiturageCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Covoiturage::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->hideOnForm(),
            DateTimeField::new('date')
                ->setFormat('dd/MM/yyyy HH:mm'),
            TextField::new('departureLocation'),
            TextField::new('arrivalLocation'),
            AssociationField::new('user', 'Créé par')
                ->hideOnForm(),
            NumberField::new('price'),
            ];
    }

    
    public function createEntity(string $entityFqcn)
    {
        $covoit = new Covoiturage();
        
        // Assignez l'utilisateur actuel à l'événement
        $covoit->setUser($this->getUser());

        return $covoit;
    }
}
