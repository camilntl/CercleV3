<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Message>
 *
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }
// Exemple de fonction pour récupérer les messages avec les noms complets d'utilisateur
public function getMessagesWithUserNames()
{
    $messages = $this->messageRepository->findAll(); // Remplacer par votre logique de récupération de messages
    $messagesWithUserNames = [];

    foreach ($messages as $message) {
        $messagesWithUserNames[] = [
            'content' => $message->getContent(),
            'userName' => $message->getUser()->getFirstname() . " " . $message->getUser()->getLastname(),
            'userId' => $message->getUser()->getId()
            // Ajoutez d'autres champs au besoin
        ];
    }

    return $messagesWithUserNames;
}



//    /**
//     * @return Message[] Returns an array of Message objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Message
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
