<?php

namespace App\Controller;

use App\Repository\NotificationRepository;
use App\Entity\Notification;
use App\Service\NotificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/ajax', name: 'ajax_')]
class AjaxNotificationController extends AbstractController
{
    #[Route('/send-notification', name: 'send_notification', methods: ['POST'])]
    public function sendNotification(Request $request, NotificationService $notificationService): JsonResponse
    {
        $message = $request->request->get('message');
            
        if (!isset($message)) {
            return new JsonResponse(['status' => 'error', 'message' => 'Données invalides'], 400);
        }

        $id = $notificationService->sendNotification($message);

        return new JsonResponse(['status' => 'success', 'id' => $id]);
    }

    #[Route('/get-notification-template/{id}', name: 'get_notification', methods: ['GET'])]
    public function getNotification(Notification $notification): JsonResponse
    {
        $html = $this->renderView('components/notificationCard.html.twig',[
            "title" => $notification->getMessage(),
            "user" => $notification->getUser(),
        ]);
        
        return new JsonResponse($html);
    }
}