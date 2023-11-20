<?php

namespace App\Repository;

use App\Entity\Conversation;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr\Join;
use App\Entity\Message;


/**
 * @extends ServiceEntityRepository<Conversation>
 *
 * @method Conversation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Conversation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Conversation[]    findAll()
 * @method Conversation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConversationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conversation::class);
    }


    public function findOneByUsersIncludingCurrentUser(array $selectedUsers, User $currentUser): ?Conversation
{
    $selectedUserIds = array_map(function(User $user) {
        return $user->getId();
    }, $selectedUsers);

    // Ajouter l'ID de l'utilisateur actuel à la liste des IDs sélectionnés
    $selectedUserIds[] = $currentUser->getId();

    // Trouver les conversations qui contiennent au moins les utilisateurs sélectionnés
    $qb = $this->createQueryBuilder('c')
        ->join('c.users', 'u')
        ->groupBy('c.id')
        ->having('COUNT(DISTINCT u.id) = :count') // Assurez-vous que le nombre d'utilisateurs est exact
        ->setParameter('count', count($selectedUserIds));

    // Ajouter une condition pour chaque utilisateur sélectionné
    foreach ($selectedUserIds as $index => $userId) {
        $qb->andWhere(":userId{$index} MEMBER OF c.users")
           ->setParameter("userId{$index}", $userId);
    }

    // Obtenir les résultats
    $conversations = $qb->getQuery()->getResult();

    // Il est possible d'avoir plus d'une conversation répondant aux critères ci-dessus,
    // notamment si les conversations peuvent avoir des utilisateurs supplémentaires.
    // Vous devez itérer sur ces conversations et vérifier celles qui ont exactement les mêmes utilisateurs.
    foreach ($conversations as $conversation) {
        $conversationUserIds = array_map(function(User $user) {
            return $user->getId();
        }, $conversation->getUsers()->toArray());

        sort($conversationUserIds);
        sort($selectedUserIds);

        if ($conversationUserIds === $selectedUserIds) {
            return $conversation;
        }
    }

    return null;
}

public function findLastMessageForConversation($conversationId)
{
    return $this->getEntityManager()
        ->getRepository(Message::class)
        ->createQueryBuilder('m')
        ->andWhere('m.Conversation = :conversationId') // Utilisez 'm.Conversation'
        ->setParameter('conversationId', $conversationId)
        ->orderBy('m.date', 'DESC')
        ->setMaxResults(1)
        ->getQuery()
        ->getOneOrNullResult();
}




public function findConversationsBySearchTerm(User $user, $searchTerm) {
    // Requête DQL pour trouver des conversations en fonction du nom d'utilisateur
    return $this->createQueryBuilder('c')
        ->leftJoin('c.users', 'u')
        ->where('u.firstname LIKE :searchTerm OR u.lastname LIKE :searchTerm')
        ->andWhere(':currentUser MEMBER OF c.users')
        ->setParameter('searchTerm', '%' . $searchTerm . '%')
        ->setParameter('currentUser', $user)
        ->getQuery()
        ->getResult();
}


//    /**
//     * @return Conversation[] Returns an array of Conversation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Conversation
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}