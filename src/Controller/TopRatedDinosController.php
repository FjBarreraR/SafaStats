<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Dinosaurs;
use App\Entity\Ranking;
use App\Entity\RankingDinosaur;
use App\Repository\CategoryRepository;
use App\Repository\RankingDinosaurRepository;
use App\Repository\RankingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

class TopRatedDinosController extends AbstractController
{
    // Cargar página principal
    #[Route('/top_rated_dinos', name: 'top_rated_dinos_app')]
    public function menu(): Response
    {
        return $this->render('topRatedDinos/topRatedDinosMain.html.twig');
    }

//    #[Route('/top_rated_dinos/all', name: 'top_rated_dinos_all_app')]
//    public function showRanking(RankingDinosaurRepository $rankingRepo): Response
//    {
//        // Obtenemos los registros de la tabla intermedia ordenados por posición
//        $rankingEntries = $rankingRepo->findBy([], ['position' => 'ASC']);
//
//        return $this->render('topRatedDinos/topRatedDinos.html.twig', [
//            'rankingEntries' => $rankingEntries,
//        ]);
//    }

    // Cargar categorías
    #[Route('/top_rated_dinos/categories', name: 'show_categories_top_rated_dinos_app')]
    public function showCategories(CategoryRepository $categoryRepository): Response
    {
        return $this->render('topRatedDinos/topRatedDinosSee.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    // Cargar el ranking de las categorías
    #[Route('/top_rated_dinos/categories/top', name: 'show_categories_top_rated_dinos_top_app')]
    public function showCategoriesTOP(CategoryRepository $categoryRepository): Response
    {
        return $this->render('topRatedDinos/topRatedDinosCategoriesTOP.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    // Cargar el formulario para valorar una categoría
    #[Route('/top_rated_dinos/valorar/{id}', name: 'top_rated_dinos_rate_app')]
    public function rate(Category $category, RankingRepository $rankingRepo, RankingDinosaurRepository $rankingDinosaurRepo): Response
    {
        $user = $this->getUser();
        $userRanking = [];

        if ($user) {
            $existingRanking = $rankingRepo->findOneBy([
                'user' => $user,
                'category' => $category
            ]);

            if ($existingRanking) {
                $rankingDinosaurs = $rankingDinosaurRepo->findBy(['ranking' => $existingRanking]);
                foreach ($rankingDinosaurs as $rd) {
                    $userRanking[$rd->getDinosaur()->getId()] = $rd->getPosition();
                }
            }
        }

        return $this->render('topRatedDinos/topRatedDinosPost.html.twig', [
            'category' => $category,
            'userRanking' => $userRanking,
        ]);
    }

    // Guardar toda la información del ranking de la categoría
    #[Route('/top_rated_dinos/valorar/submit/{id}', name: 'category_submit_rating', methods: ['POST'])]
    public function submitRating(
        Category $category,
        Request $request,
        EntityManagerInterface $em,
        RankingRepository $rankingRepo,
        RankingDinosaurRepository $rankingDinosaurRepo
    ): Response {

        $user = $this->getUser();

        if (!$user) {
            $this->addFlash('error', 'Debes iniciar sesión para votar.');
            return $this->redirectToRoute('app_login'); // O tu ruta de login
        }

        // Buscar si ya existe un ranking
        $ranking = $rankingRepo->findOneBy([
            'user' => $user,
            'category' => $category
        ]);

        if ($ranking) {
            // Si existe, borramos las posiciones anteriores para guardar las nuevas
            $oldEntries = $rankingDinosaurRepo->findBy(['ranking' => $ranking]);
            foreach ($oldEntries as $oldEntry) {
                $em->remove($oldEntry);
            }
            // Importante: hacer flush aquí o asegurar que los nuevos inserts no colisionen si hubiera checks únicos (aunque aquí borramos)
            // En este caso, persistiremos los nuevos y al final haremos flush general.
        } else {
            // Si no existe, creamos uno nuevo
            $ranking = new Ranking();
            $ranking->setCategory($category);
            $ranking->setUser($user);
            $em->persist($ranking);
        }

        $rankingData = $request->request->all('ranking');

        foreach ($rankingData as $dinoId => $positionValue) {
            if (!$positionValue) continue;

            $dinosaur = $em->getRepository(Dinosaurs::class)->find($dinoId);

            if ($dinosaur) {
                $rankingDino = new RankingDinosaur();
                $rankingDino->setDinosaur($dinosaur);
                $rankingDino->setRanking($ranking);
                $rankingDino->setPosition((int)$positionValue);

                $em->persist($rankingDino);
            }
        }

        $em->flush();

        $this->addFlash('success', '¡Ranking guardado con éxito!');

        // Redirigimos al ranking global para ver cómo ha quedado
        return $this->redirectToRoute('top_rated_dinos_app');
    }

    // Cargar el ranking de la categoría seleccionada por puntos
    #[Route('/top_rated_dinos/ranking/categoria/{id}', name: 'top_rated_dinos_top_app')]
    public function showRankingPoints(
        Category $category,
        RankingDinosaurRepository $rankingDinoRepo,
        RankingRepository $rankingRepo
    ): Response
    {
        $queryData = $rankingDinoRepo->createQueryBuilder('rd')
            ->select('d.id, d.name, d.image, d.diet, d.period, d.type, AVG(rd.position) as avgPosition')
            ->innerJoin('rd.ranking', 'r')
            ->innerJoin('rd.dinosaur', 'd')
            ->where('r.category = :cat')
            ->setParameter('cat', $category)
            ->groupBy('d.id')
            ->orderBy('avgPosition', 'ASC')
            ->getQuery()
            ->getResult();

        $totalDinos = count($queryData);
        $totalVotes = $rankingRepo->count(['category' => $category]);

        $rankingEntries = [];
        foreach ($queryData as $key => $data) {
            $position = $key + 1;
            $positionPoints = 100 / $totalDinos;

            if ($totalDinos > 1) {
                $points = 100 + $positionPoints - ($data['avgPosition'] * $positionPoints);
            } else {
                $points = 100;
            }

            $rankingEntries[] = [
                'position' => $position,
                'points' => (int)round($points),
                'avgPosition' => $data['avgPosition'],
                'dinosaur' => [
                    'name' => $data['name'],
                    'image' => $data['image'],
                    'diet' => $data['diet'],
                    'period' => $data['period'],
                    'type' => $data['type']
                ]
            ];
        }

        return $this->render('topRatedDinos/topRatedDinos.html.twig', [
            'category' => $category,
            'rankingEntries' => $rankingEntries,
            'totalVotes' => $totalVotes,
        ]);
    }
}