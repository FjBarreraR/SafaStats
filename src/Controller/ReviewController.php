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
    // Cargar página principal
    #[Route('/reviews/latest', name: 'app_reviews_latest', methods: ['GET'])]
    public function index(ReviewRepository $reviewRepo, DinosaursRepository $dinoRepo): Response
    {
        $user = $this->getUser();
        $reviewedDinoIds = [];

        if ($user) {
            $userReviews = $reviewRepo->findBy(['user' => $user]);
            foreach ($userReviews as $review) {
                // Guardamos solo los IDs de los dinos
                $reviewedDinoIds[] = $review->getDinosaur()->getId();
            }
        }

        return $this->render('review/review.html.twig', [
            'reviews' => $reviewRepo->findBy([], ['id' => 'DESC']),
            'dinosaurs' => $dinoRepo->findAll(),
            // 2. Pasamos la lista a la vista
            'reviewed_ids' => $reviewedDinoIds,
        ]);
    }

    // Guardar una review
    #[Route('/reviews/submit', name: 'app_review_submit_global', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function submit(
        Request $request,
        EntityManagerInterface $entityManager,
        DinosaursRepository $dinoRepo,
        ReviewRepository $reviewRepo // Inyectamos el repo de reviews
    ): Response {
        $dinoId = $request->request->get('dinosaur_id');
        $rating = $request->request->get('rating');
        $comment = $request->request->get('comment');
        $user = $this->getUser();

        $dinosaur = $dinoRepo->find($dinoId);

        if (!$dinosaur || !$comment || !$rating) {
            $this->addFlash('danger', 'Error: Asegúrate de elegir un dinosaurio y escribir un comentario.');
            return $this->redirectToRoute('app_reviews_latest');
        }

        $existingReview = $reviewRepo->findOneBy([
            'user' => $user,
            'dinosaur' => $dinosaur
        ]);

        if ($existingReview) {
            $this->addFlash('warning', '¡Ya has valorado al ' . $dinosaur->getName() . '! Puedes editar tu reseña existente.');
            return $this->redirectToRoute('app_reviews_latest');
        }

        $review = new Review();
        $review->setComment($comment);
        $review->setRating((int)$rating);
        $review->setDinosaur($dinosaur);
        $review->setUser($user);

        $entityManager->persist($review);
        $entityManager->flush();

        $this->addFlash('success', '¡Tu review sobre el ' . $dinosaur->getName() . ' ha sido publicada!');

        return $this->redirectToRoute('app_reviews_latest');
    }

    // Editar una review
    #[Route('/review/edit/{id}', name: 'app_review_edit')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function edit(Review $review, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($review->getUser() !== $this->getUser()) {
            // ...
            return $this->redirectToRoute('app_reviews_latest');
        }

        return $this->render('review/editReview.html.twig', [ 'review' => $review ]);
    }
}
