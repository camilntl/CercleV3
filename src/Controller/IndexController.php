<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Event;

use App\Entity\User;
use App\Repository\CovoiturageRepository;
use App\Repository\NotificationRepository;
use App\Repository\UserRepository;

use Symfony\Component\HttpFoundation\Request;
use App\Entity\Covoiturage;
use App\Form\CreateConversationFormType;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\Conversation;
class IndexController extends AbstractController
{

    #[Route('/index', name: 'app_index')]
    public function index(): Response
    {
        return $this->render('pages/index.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }

    #[Route('/blog', name: 'app_home')]
    public function home(ArticleRepository $articleRepository): Response
    {
        return $this->render('pages/home.html.twig', [
            'articles' => $articleRepository->findAllSortedByDate(),
        ]);
    }

    #[Route('/search', name: 'app_search')]
    public function search(Request $request, ArticleRepository $articleRepository, CovoiturageRepository $covoiturageRepository, UserRepository $userRepository, EntityManagerInterface $entityManager, Security $security): Response {
        // Récupération des termes de recherche et de la page
        $query = $request->query->get('q');
        $page = $request->query->get('page');
    
        // Variables pour les résultats de la recherche
        $articles = $articleRepository->findBySearchTerm($query);
        $covoiturages = $covoiturageRepository->findBySearchTerm($query);
        $users = $userRepository->findByNames($query);
        $conversations = [];
    
        // Recherche spécifique pour la page 'conversation'
        if ($page === 'conversation') {
            $user = $security->getUser();
    
            if ($query) {
                // Recherche les conversations en fonction du terme de recherche
                $conversations = $entityManager->getRepository(Conversation::class)->findConversationsBySearchTerm($user, $query);
            } else {
                // Si aucun terme de recherche, récupérer toutes les conversations de l'utilisateur
                $conversations = $entityManager->createQueryBuilder()
                    ->select('DISTINCT c, u')
                    ->from(Conversation::class, 'c')
                    ->leftJoin('c.users', 'u')
                    ->andWhere(':userId MEMBER OF c.users')
                    ->setParameter('userId', $user)
                    ->getQuery()
                    ->getResult();
            }
        }
    
        // Sélection du template en fonction de la page
        switch ($page) {
            case 'home':
                return $this->render('pages/home.html.twig', ['articles' => $articles]);
            case 'covoiturage':
                return $this->render('covoiturage/index.html.twig', ['covoiturages' => $covoiturages]);
            case 'conversation':
                $form = $this->createForm(CreateConversationFormType::class)->createView();
                return $this->render('pages/message/messageList.html.twig', ['users' => $users, 'conversations' => $conversations, 'form' => $form]);
            default:
                // Fallback si 'page' n'est pas défini
                return $this->render('pages/home.html.twig', ['articles' => $articles, 'covoiturages' => $covoiturages, 'users' => $users]);
        }
    }
    


    
    #[Route('/messageList', name: 'app_messageList')]
    public function messageList(): Response
    {
        return $this->render('pages/message/messageList.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }

    #[Route('/messageVue', name: 'app_messageVue')]
    public function messageVue(): Response
    {
        return $this->render('pages/message/messageVue.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }

    #[Route('/notificationList', name: 'app_notificationList')]
    public function notificationList(NotificationRepository $notificationRepository): Response
    {
        return $this->render('pages/notificationList.html.twig', [
            'notifications' => $notificationRepository->findAll()
        ]);
    }

    #[Route('/studiesList', name: 'app_studiesList')]
    public function studiesList(): Response
    {
        return $this->render('pages/studies/studiesList.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }

    #[Route('/studiesVue', name: 'app_studiesVue')]
    public function studiesVue(): Response
    {
        return $this->render('pages/studies/studiesVue.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }



    #[Route('/calendar', name: 'app_calendar')]
    public function calendar(EntityManagerInterface $em): Response
    {

        $events = $em->getRepository(Event::class)->findAll();
        $eventDates = [];

        foreach ($events as $event) {
            // Assurez-vous que startDate est l'attribut correct de votre entité Event
            $eventDates[] = $event->getStartDate()->format('Y-m-d');
        }


        return $this->render('pages/calendar.html.twig', [
            'controller_name' => 'IndexController',
            'eventDates' => json_encode(array_unique($eventDates))
        ]);
    }




    #[Route('/maintenance', name: 'app_maintenance')]
    public function maintenance(): Response
    {
        return $this->render('pages/techniques/maintenance.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }

    #[Route('/error404', name: 'app_error404')]
    public function error404(): Response
    {
        return $this->render('pages/techniques/error404.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }

    #[Route('/error403', name: 'app_error403')]
    public function error403(): Response
    {
        return $this->render('pages/techniques/error403.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }

    
    

    #[Route('/detailBlog/{id}', name: 'app_detailBlog')]
    public function detailBlog(ArticleRepository $articleRepository, $id): Response
    {
        // Récupérez l'article en fonction de l'ID
        $article = $articleRepository->find($id);

        // Vérifiez si l'article existe
        if (!$article) {
            throw $this->createNotFoundException('L\'article n\'existe pas');
        }

        return $this->render('pages/blog/detailBlog.html.twig', [
            'article' => $article,  // Passez l'article à votre modèle Twig
        ]);
    }
}
