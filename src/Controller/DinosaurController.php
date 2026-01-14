<?php

namespace App\Controller;

use App\Repository\DinosaursRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DinosaurController extends AbstractController
{
    #[Route('/dinosaur/list', name: 'list_dinosaur')]
    public function ListDinosaurs(DinosaursRepository $repository): Response {
        $dinosaurs = $repository->findAll();

        return $this->render('dinosaur/list_dinosaur.html.twig', [
            'dinosaurs' => $dinosaurs,
        ]);
    }
}
