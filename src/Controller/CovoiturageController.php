<?php

namespace App\Controller;

use App\Entity\Covoiturage;
use App\Form\CovoiturageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CovoiturageController extends AbstractController
{
    #[Route('/covoiturage', name: 'app_covoiturage')]
    public function index(EntityManagerInterface $em, Security $security): Response
    {
        // Récupérez l'utilisateur connecté
        $user = $security->getUser();

        // Si l'utilisateur est connecté, filtrez les covoiturages en fonction de son ID
        if ($user) {
            $covoituragesUser = $em->getRepository(Covoiturage::class)->findBy(['user' => $user], ['id' => 'DESC']);
        } else {
            $covoituragesUser = [];
        }

        // Récupérez tous les covoiturages (pour l'élément 1)
        $covoituragesAll = $em->getRepository(Covoiturage::class)->findBy([], ['id' => 'DESC']);

        return $this->render('covoiturage/index.html.twig', [
            'covoituragesUser' => $covoituragesUser,
            'covoituragesAll' => $covoituragesAll,
            'currentUserId' => $user ? $user->getId() : null,
        ]);
    }



    #[Route('/covoiturage/new', name: 'covoiturage_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $covoiturage = new Covoiturage();
        $covoiturage->setUser($this->getUser());
        $form = $this->createForm(CovoiturageType::class, $covoiturage);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($covoiturage);
            $em->flush();

            // Redirigez vers la liste des covoiturages ou une autre page appropriée
            return $this->redirectToRoute('app_covoiturage');
        }

        return $this->render('covoiturage/covoiturage_new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    
    #[Route('/covoiturage/delete/{id}', name: 'covoiturage_delete')]
public function delete(Request $request, EntityManagerInterface $em, int $id): JsonResponse
{
    $covoiturage = $em->getRepository(Covoiturage::class)->find($id);

    if (!$covoiturage) {
        return new JsonResponse(['message' => 'Trajet non trouvé.'], 404);
    }

    // Vous pouvez ajouter des vérifications supplémentaires ici, par exemple, pour vérifier si l'utilisateur a le droit de supprimer ce trajet.

    $em->remove($covoiturage);
    $em->flush();

    return new JsonResponse(['message' => 'Trajet supprimé avec succès.']);
}
}
