<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Covoiturage;
use App\Entity\Event;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    #[IsGranted('ROLE_SUPER_ADMIN')]
    public function index(): Response
    {
        $routeBuilder = $this->container->get(AdminUrlGenerator::class);
        $url = $routeBuilder->setController(UserCrudController::class)->generateUrl();

        return $this->redirect($url);

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    #[Route('/admin/articles', name: 'admin_articles')]
    #[IsGranted('ROLE_USER')]
    public function admin_articles(): Response
    {
        $routeBuilder = $this->container->get(AdminUrlGenerator::class);
        $url = $routeBuilder->setController(ArticleCrudController::class)->setAction('new')->generateUrl();

        return $this->redirect($url);
    }

    #[Route('/admin/events', name: 'admin_events')]
    #[IsGranted('ROLE_BDE')]
    public function admin_event(): Response
    {
        $routeBuilder = $this->container->get(AdminUrlGenerator::class);
        $url = $routeBuilder->setController(EventCrudController::class)->setAction('new')->generateUrl();

        return $this->redirect($url);
    }

    #[Route('/admin/covoiturage', name: 'admin_covoit')]
    #[IsGranted('ROLE_USER')]
    public function admin_covoit(): Response
    {
        $routeBuilder = $this->container->get(AdminUrlGenerator::class);
        $url = $routeBuilder->setController(CovoiturageCrudController::class)->setAction('new')->generateUrl();

        return $this->redirect($url);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Cercle De Projet 2023');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToRoute('Retour au site', 'fas fa-rotate-left', 'app_home');

        if ($this->isGranted('ROLE_SUPER_ADMIN') ) {
            yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-user', User::class);
        }
        
        if ($this->isGranted('ROLE_USER') ) {
            yield MenuItem::linkToCrud('Articles', 'fas fa-newspaper', Article::class);
        }

        if ($this->isGranted("ROLE_BDE") ) {
            yield MenuItem::linkToCrud('Evènements', 'fas fa-champagne-glasses', Event::class);
        }

        if ($this->isGranted("ROLE_USER") ) {
            yield MenuItem::linkToCrud('Covoiturages', 'fas fa-car', Covoiturage::class);
        }
    }
}
