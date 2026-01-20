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
    #[Route('/admin/dinosaur/load', name: 'app_admin')]
    public function index(HttpClientInterface $httpClient, EntityManagerInterface $entityManager): Response
    {
        $response = $httpClient->request('GET', 'https://dinoapi.brunosouzadev.com/api/dinosaurs');

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

        return $this->render('admin/admin.html.twig', [
            'controller_name' => 'AdminController',
            'content' => $content
        ]);
    }
}
