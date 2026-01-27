<?php

namespace App\Controller;

use App\Entity\Dinosaurs;
use App\Entity\Review;
use App\Repository\DinosaursRepository;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ReviewController extends AbstractController
{
    #[Route('/reviews/latest', name: 'app_reviews_latest', methods: ['GET'])]
    public function index(ReviewRepository $reviewRepo, DinosaursRepository $dinoRepo): Response
    {
        return $this->render('review/review.html.twig', [
            // Traemos las reviews más recientes primero
            'reviews' => $reviewRepo->findBy([], ['id' => 'DESC']),
            // Pasamos todos los dinosaurios para el <select> del formulario
            'dinosaurs' => $dinoRepo->findAll(),
        ]);
    }

    /**
     * Esta ruta procesa el envío del formulario manual.
     */
    #[Route('/reviews/submit', name: 'app_review_submit_global', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function submit(
        Request $request,
        EntityManagerInterface $entityManager,
        DinosaursRepository $dinoRepo
    ): Response {
        // 1. Obtener datos del formulario manual (atributo 'name' de los inputs)
        $dinoId = $request->request->get('dinosaur_id');
        $rating = $request->request->get('rating');
        $comment = $request->request->get('comment');

        // 2. Buscar la entidad Dinosaurio por el ID seleccionado
        $dinosaur = $dinoRepo->find($dinoId);

        // 3. Validación rápida
        if (!$dinosaur || !$comment || !$rating) {
            $this->addFlash('danger', 'Error: Asegúrate de elegir un dinosaurio y escribir un comentario.');
            return $this->redirectToRoute('app_reviews_latest');
        }

        // 4. Crear la entidad Review y mapear los campos
        $review = new Review();
        $review->setComment($comment);
        $review->setRating((int)$rating);
        $review->setDinosaur($dinosaur);

        // Asignar el usuario actual (tu entidad requiere un User)
        $review->setUser($this->getUser());

        // 5. Guardar en la base de datos
        $entityManager->persist($review);
        $entityManager->flush();

        $this->addFlash('success', '¡Tu review sobre el ' . $dinosaur->getName() . ' ha sido publicada!');

        // 6. Redirigir de vuelta al listado
        return $this->redirectToRoute('app_reviews_latest');
    }
}
