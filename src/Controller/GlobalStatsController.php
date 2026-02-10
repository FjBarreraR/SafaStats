<?php

namespace App\Controller;

use App\Repository\DinosaursRepository;
use App\Repository\ReviewRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GlobalStatsController extends AbstractController
{
    #[Route('/global/stats', name: 'app_global_stats')]
    public function index(DinosaursRepository $dinosaursRepository, ReviewRepository $reviewRepository): Response
    {
        $dinosaurs = $dinosaursRepository->findAll();
        $dinosaurStats = [];

        foreach ($dinosaurs as $dinosaur) {
            $reviews = $reviewRepository->findBy(['dinosaur' => $dinosaur]);
            $totalRating = 0;
            $count = count($reviews);

            if ($count > 0) {
                foreach ($reviews as $review) {
                    $totalRating += $review->getRating();
                }
                $averageRating = $totalRating / $count;
            } else {
                $averageRating = 0; // Or null, depending on how we want to handle it
            }

            $dinosaurStats[] = [
                'dinosaur' => $dinosaur,
                'averageRating' => $averageRating,
                'reviewCount' => $count,
            ];
        }

        return $this->render('globalStats/globalStats.html.twig', [
            'dinosaurStats' => $dinosaurStats,
        ]);
    }
}