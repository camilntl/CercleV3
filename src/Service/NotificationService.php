<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Notification;

class NotificationService
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function sendNotification(string $message)
    {
        $notification = new Notification();
        $notification->setMessage($message);

        $this->entityManager->persist($notification);
        $this->entityManager->flush();

        return $notification->getId();
    }
}