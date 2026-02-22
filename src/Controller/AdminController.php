<?php

namespace App\Controller;

use App\Entity\Dinosaurs;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class AdminController extends AbstractController
{
    // Cargar página principal de admin
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/admin.html.twig');
    }

    // Cargar datos a la base de datos desde la api
    #[Route('/admin/loadData', name: 'app_dataLoad')]
    public function loadData(HttpClientInterface $httpClient, EntityManagerInterface $entityManager): Response
    {
        $status = 'success';
        $message = '¡Dinosaurios cargados correctamente!';
        $content = [];

        try {
            $response = $httpClient->request('GET', 'https://dinoapi.brunosouzadev.com/api/dinosaurs');

            // Esto lanzará una excepción si el status code no es 2xx
            $content = $response->toArray();

            foreach ($content as $e) {
                $dinosaur = new Dinosaurs();
                $dinosaur->setName($e['name']);
                $dinosaur->setWeight($e['weight']);
                $dinosaur->setHeight($e['height']);
                $dinosaur->setLength($e['length']);
                $dinosaur->setDiet($e['diet']);
                $dinosaur->setPeriod($e['period']);
                $dinosaur->setExisted($e['existed']);
                $dinosaur->setRegion($e['region']);
                $dinosaur->setType($e['type']);
                $dinosaur->setDescription($e['description']);
                $dinosaur->setImage($e['image']);
                $dinosaur->setIsPopular($e['isPopular']);
                $dinosaur->setCode($e['_id']);

                $entityManager->persist($dinosaur);
            }

            $entityManager->flush();

        } catch (\Exception $e) {
            $status = 'danger';
            $message = 'Error al cargar los datos: ' . $e->getMessage();
        }

        return $this->render('dataLoad/dataLoad.html.twig', [
            'status' => $status,
            'message' => $message,
            'count' => count($content)
        ]);
    }
}
