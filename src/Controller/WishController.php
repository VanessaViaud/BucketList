<?php

namespace App\Controller;

use App\Entity\Wish;
use App\Form\WishType;
use App\Repository\WishRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/wish', name: 'wish')]

final class WishController extends AbstractController
{
    #[Route('/list', name: '_list', methods: ['GET'])]
    public function list(WishRepository $wishRepository): Response
    {
        $wishes = $wishRepository->findPublishedWishesWithCategories();
        //si on veut avoir tous les wishes mais il vaut mieux utiliser avec
        //la jointure ci-dessus pour éviter d'avoir trop de requêtes SQL pour
        //l'affichage de la page d'accueil
       // $wishes = $wishRepository->findAll();

        // et on veut n'afficher que les wish publiés :
        //$wishesPublished = $wishRepository->findBy(['isPublished' => true]);
        
        return $this->render('wish/list.html.twig', [
            'wishes' => $wishes,
        ]);
    }

    #[Route('/{id}', name: '_detail', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function detail(int $id, WishRepository $wishRepository): Response
    {
        $wish = $wishRepository->find($id);

        if (!$wish){
            throw $this->createNotFoundException('This wish do not exists! Sorry!');
        }

        return $this->render('wish/detail.html.twig', [
            'controller_name' => 'WishController',
            'id' => $id,
            'wish' => $wish,
        ]);
    }


    #[Route('/create', name: '_create')]
    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response {

        $wish = new Wish();
        $form = $this->createForm(WishType::class, $wish);

        $form->handleRequest($request); // form va pouvoir gérer la requête

        if ($form->isSubmitted() && $form->isValid()) {
            $wish->setDateCreated(new \DateTime());
            $entityManager->persist($wish);
            $entityManager->flush();

            $this->addFlash('success', "Création réussie");

            return $this->redirectToRoute('wish_detail', ['id' => $wish->getId()]);
        }

        return $this->render('wish/edit.html.twig', [
            'wish_form'=> $form,
        ]);
    }
    #[Route('/update/{id}', name: '_update', requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_ADMIN')]
    public function update(Wish $wish, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(WishType::class, $wish);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', "Mise à jour enregistrée");

            return $this->redirectToRoute('wish_detail', ['id' => $wish->getId()]);
        }

        return $this->render('wish/edit.html.twig', [
            'wish_form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: '_delete', requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_ADMIN')]
public function delete(Wish $wish, EntityManagerInterface $entityManager, Request $request): Response
    {

        $entityManager->remove($wish);
        $entityManager->flush();

        $this->addFlash('success', "Suppression réussie");
        return $this->redirectToRoute('wish_list');
    }
}
