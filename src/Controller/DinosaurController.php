<?php

namespace App\Controller;

use App\Entity\apiDinosaurs;
use App\Repository\DinosaursRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class DinosaurController extends AbstractController
{
    // BASE DE DATOS LOCAL
//    #[Route('/got/dinosaur', name: 'list_dinosaur')]
//    public function index(): Response
//    {
//        return $this->render('dinosaur/list_dinosaur.html.twig', [
//            'controller_name' => 'DinosaurController',
//        ]);
//    }

    #[Route('/dinosaur', name: 'app_dinosaur')]
    public function listDinosaurs(DinosaursRepository $repository): Response {
        $dinosaurs = $repository->findAll();

        return $this->render('dinosaur/list_dinosaur.html.twig', [
            'controller_name' => 'DinosaurController',
            'dinosaurs' => $dinosaurs,
        ]);
    }

    #[Route('/got/dinosaur/{id}', name: 'show_dinosaur')]
    public function dinosaur(DinosaursRepository $repository, int $id): Response {
        $dinosaur = $repository->find($id);

        return $this->render('dinosaur/dinosaur_id.html.twig', [
            'dinosaur' => $dinosaur,
        ]);
    }

    // API
    #[Route('/api/dinosaurs', name: 'api_dinosaurios_all')]
    public function mostrarDinosauriosApi(apiDinosaurs $api): Response {
        $dinosaurios = $api->getAllDinosaurs();

        return $this->render('dinosaur/api_dinosaur.html.twig', [
            'dinosaurios' => $dinosaurios,
        ]);
    }

    #[Route('/api/dinosaurs/{name}', name: 'api_dinosaurio_name')]
    public function mostrarDinosaurioApi(string $name, apiDinosaurs $api): Response {
        $dinosaurio = $api->getDinosaurName($name);

        return $this->render('dinosaur/api_dinosaur_find_name.html.twig', [
            'dinosaurio' => $dinosaurio[0],
        ]);
    }
}
