<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Enum\RoleUser;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }
    
    public function configureFields(string $pageName): iterable
    {
        $roles = [
            RoleUser::User->value,
            RoleUser::Admin->value,
            RoleUser::SuperAdmin->value,
            RoleUser::COP->value,
            RoleUser::CAPA->value,
            RoleUser::BDE->value
        ];
            
        return [
            IdField::new('id')
                ->hideOnForm(),
            TextField::new('firstname'),
            TextField::new('lastname'),
            TextField::new('email'),
            ChoiceField::new('roles')
                ->setLabel('Rôle')
                ->setChoices(array_combine($roles, $roles))
                ->allowMultipleChoices()
        ];
    }
    
}
