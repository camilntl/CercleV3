<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventController extends AbstractController
{   
    #[Route('/events', name: 'app_events')]
    public function events(EntityManagerInterface $em): Response
    {
        $lastEvent = $em->getRepository(Event::class)->findOneBy([], ['id' => 'DESC']);
        $events = $em->getRepository(Event::class)->findBy([], ['id' => 'DESC']);

        return $this->render('event/index.html.twig', [
            'lastEvent' => $lastEvent,
            'events' => $events
        ]);
    }

    #[Route('/events/list', name: 'app_events_list')]
    public function eventList(EntityManagerInterface $em): Response
    {
        $events = $em->getRepository(Event::class)->findBy([], ['id' => 'DESC']);
        
        return $this->render('event/eventList.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route('/events/{id}', name: 'app_events_view')]
    public function eventView(Event $event): Response
    {
        return $this->render('event/event-view.html.twig', [
            'event' => $event
        ]);
    }


#[Route('/events/date/{date}', name: 'app_events_by_date')]
public function eventsByDate($date, EntityManagerInterface $em): Response 
{
    try {
        $dateObj = new \DateTime($date);
    } catch (\Exception $e) {
        // Gérer l'erreur si la date n'est pas valide
    }

    $events = $em->getRepository(Event::class)->findByDate($dateObj);

    return $this->render('components/eventCalendar.html.twig', [
        'events' => $events
    ]);
}

// ... dans EventController

#[Route('/event/new', name: 'event_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Définir l'utilisateur connecté comme créateur de l'événement
            $event->setUser($this->getUser());

            // Gérer l'upload d'image
            $file = $form->get('image')->getData();
            if ($file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                // Logique pour sécuriser le nom du fichier

                try {
                    $file->move(
                        $this->getParameter('images_directory'),
                        $originalFilename
                    );
                } catch (FileException $e) {
                    // Gérer l'exception si quelque chose se passe mal pendant le téléchargement
                }

                // Mettre à jour le nom de l'image dans l'entité Event
                $event->setImage($originalFilename);
            }

            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('app_events_list'); // Assurez-vous que cette route existe
        }

        return $this->render('event/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    
}