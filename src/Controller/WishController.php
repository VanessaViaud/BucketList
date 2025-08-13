<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/wish', name: 'wish')]

final class WishController extends AbstractController
{
    #[Route('/list', name: '_list', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('wish/list.html.twig', [
            'controller_name' => 'WishController',
        ]);
    }

    #[Route('/{id}', name: '_detail', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function detail(int $id): Response
    {
        return $this->render('wish/detail.html.twig', [
            'controller_name' => 'WishController',
            'id' => $id,
        ]);
    }
}
