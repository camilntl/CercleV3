<?php

// src/Controller/ChatController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use App\Entity\Message;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\UserRepository;
use App\Entity\Conversation; 
use App\Entity\User;
use App\Form\CreateConversationFormType;
use Doctrine\Common\Collections\ArrayCollection;

class ChatController extends AbstractController
{


#[Route('/chat/{id}', name:'chat')]
    public function chat(Conversation $conversation, UserRepository $userRepository)
    {
        $message = new Message();
        $form = $this->createFormBuilder($message)
        ->add('content', TextType::class, [
            'label' => ':)',
            'attr' => [
                'class' => 'w-full px-4 py-3 text-sm rounded-full placeholder-lavande-500 text-lavande-500 bg-purple-200 border border-purple-200 focus:ring-purple-500 focus-visible:outline-purple-500',
                'placeholder' => 'Entrez votre message'
                
                ]
        ])
        ->getForm();
        
        $users = $conversation->getUsers();
        // Logique pour sélectionner l'utilisateur ou les utilisateurs appropriés
        // Par exemple, sélectionner l'autre utilisateur dans une conversation 1-1
    
        // Rendre le formulaire dans votre template
        return $this->render('pages/message/messageVue.html.twig', [
            'form' => $form->createView(),
            'conversation' => $conversation,
            'message' => $message,
            'users' => $users // Ou 'user' si vous passez un seul utilisateur
        ]);

    }


    #[Route('/chat/create/{userId}', name: 'create_or_join_chat')]
    public function createOrJoinChat(EntityManagerInterface $entityManager, $userId): Response
    {
        $currentUser = $this->getUser();
        $targetUser = $entityManager->getRepository(User::class)->find($userId);
    
        if (!$targetUser) {
            // Gérer l'erreur si l'utilisateur n'est pas trouvé
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }
    
        // Modifier cette ligne pour inclure les deux utilisateurs
        $conversation = $entityManager->getRepository(Conversation::class)
            ->findOneByUsersIncludingCurrentUser([$currentUser, $targetUser], $currentUser);
    
        if (!$conversation) {
            // Créer une nouvelle conversation si elle n'existe pas
            $conversation = new Conversation();
            $conversation->addUser($currentUser);
            $conversation->addUser($targetUser);
            $entityManager->persist($conversation);
            $entityManager->flush();
        }
    
        // Rediriger vers la conversation
        return $this->redirectToRoute('chat', ['id' => $conversation->getId()]);
    }
    

    #[Route('/help', name: 'app_help')]
    public function help(): Response
    {

        $message = new Message();
    $form = $this->createFormBuilder($message,)
        ->add('content', TextType::class, [
            'label' => ':)',
            'attr' => [
                'class' => 'block w-full px-4 py-3 text-sm rounded-full placeholder-lavande-500 text-lavande-500 bg-purple-200 border border-purple-200 focus:ring-purple-500 focus-visible:outline-purple-500',
                'placeholder' => 'Entrez votre message'
                
                ]
        ])
        ->getForm();


        return $this->render('pages/help.html.twig', [
            'controller_name' => 'IndexController',
            'form' => $form->createView(),
            'message' => $message
        ]);
    }


    public function __construct(private UserRepository $userRepository)
    {
    }
    public function getUserFromInterface()
    {
        return $this->userRepository->findOneBy(["email" => $this->getUser()->getUserIdentifier()]);
    }

    // Dans votre contrôleur Symfony


    #[Route('/conversation', name: 'conversation')]

    public function conversation(EntityManagerInterface $entityManager, Security $security, Request $request): Response
        
        {
        $user = $security->getUser();
        
        // Récupérer les conversations existantes
        $conversations = $entityManager->createQueryBuilder()
            ->select('DISTINCT c, u')
            ->from(Conversation::class, 'c')
            ->leftJoin('c.users', 'u')
            ->andWhere(':userId MEMBER OF c.users')
            ->setParameter('userId', $user)
            ->getQuery()
            ->getResult();

        $users = $entityManager->createQueryBuilder()
        ->select('DISTINCT u')
        ->from(User::class, 'u')
        ->getQuery()
        ->getResult();

            // Récupérer les IDs des conversations
        $conversationIds = array_map(function($conversation) {
            return $conversation->getId();
        }, $conversations);


        $lastMessages = [];
        foreach ($conversations as $conversation) {
            $lastMessages[$conversation->getId()] = $entityManager->getRepository(Conversation::class)
                ->findLastMessageForConversation($conversation->getId());
        }
        
        

        $form = $this->createForm(CreateConversationFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $selectedUsers = $form->get('users')->getData();
        
            // Convertir ArrayCollection en Array si nécessaire
            if ($selectedUsers instanceof ArrayCollection) {
                $selectedUsers = $selectedUsers->toArray();
            }
        
            // Appeler findOneByUsersIncludingCurrentUser avec un tableau
            $existingConversation = $entityManager->getRepository(Conversation::class)
                ->findOneByUsersIncludingCurrentUser($selectedUsers, $user);

            if ($existingConversation) {
                // Rediriger vers la conversation existante
                return $this->redirectToRoute('chat', ['id' => $existingConversation->getId()]);
            }

            // Créer une nouvelle conversation si elle n'existe pas
            $conversation = new Conversation();
            $conversation->addUser($user);
            foreach ($selectedUsers as $selectedUser) {
                $conversation->addUser($selectedUser);
            }

            $entityManager->persist($conversation);
            $entityManager->flush();

            return $this->redirectToRoute('chat', ['id' => $conversation->getId()]);
        }

        return $this->render('pages/message/messageList.html.twig', [
            'conversations' => $conversations,
            'lastMessages' => $lastMessages,
            'form' => $form->createView(),
            'users' => $users
        ]);
    }


    #[Route('/get-messages/{conversation}', name: 'get_messages')]
    public function getMessages(Conversation $conversation, EntityManagerInterface $entityManager)
    {
        // Utilisez l'objet Conversation provenant de la route
        $messageRepository = $entityManager->getRepository(Message::class);
        
        // Récupère les messages de la conversation spécifique
        $messages = $messageRepository->findBy(['Conversation' => $conversation]);
        
        
        $messagesArray = [];
        foreach ($messages as $message) {
            $messagesArray[] = [
                'id' => $message->getId(),
                'content' => $message->getContent(),
                'createdAt' => $message->getDate()->format('Y-m-d H:i:s'),
                'author_id' => $message->getUser()->getId(),
                'authorName' => $message->getUser()->getFirstname() . ' ' . $message->getUser()->getLastname(), 
            ];
        }
        
        return new JsonResponse($messagesArray);
    }


#[Route("/send-message/{conversation}", name:"send_message", methods:"POST")]
    public function sendMessage(Conversation $conversation, Request $request, SessionInterface $session, Security $security, EntityManagerInterface $entityManager)
    {

            $data = json_decode($request->getContent(), true);
            
            if (!isset($data['message'])) {
                return new JsonResponse(['status' => 'error', 'message' => 'Données invalides'], 400);
            }

            
        
            $user = $security->getUser();
            if (!$user) {
                return new JsonResponse(['status' => 'error', 'message' => 'Utilisateur non authentifié'], 403);
            }
            $message = new Message();
            $message->setContent($data['message']);
            $message->setDate(new \DateTimeImmutable()) ;
            $message->setUser($user); 
            $message->setConversation($conversation);

        
            $entityManager->persist($message);
            $entityManager->flush();
        
            return new JsonResponse(['status' => 'success', 'message' => 'Message envoyé']);
        } 
    
    }