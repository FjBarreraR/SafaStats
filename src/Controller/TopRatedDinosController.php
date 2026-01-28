<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Dinosaurs;
use App\Entity\Ranking;
use App\Entity\RankingDinosaur;
use App\Repository\CategoryRepository;
use App\Repository\RankingDinosaurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

class TopRatedDinosController extends AbstractController
{
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

    #[Route('/top_rated_dinos/categories', name: 'show_categories_top_rated_dinos_app')]
    public function showCategories(CategoryRepository $categoryRepository): Response
    {
        return $this->render('topRatedDinos/topRatedDinosSee.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    #[Route('/top_rated_dinos/categories/top', name: 'show_categories_top_rated_dinos_top_app')]
    public function showCategoriesTOP(CategoryRepository $categoryRepository): Response
    {
        return $this->render('topRatedDinos/topRatedDinosCategoriesTOP.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    #[Route('/top_rated_dinos/valorar/{id}', name: 'top_rated_dinos_rate_app')]
    public function rate(Category $category): Response
    {
        return $this->render('topRatedDinos/topRatedDinosPost.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/top_rated_dinos/valorar/submit/{id}', name: 'category_submit_rating', methods: ['POST'])]
    public function submitRating(
        Category $category,
        Request $request,
        EntityManagerInterface $em
    ): Response {

        // Obtenemos el usuario. Para pruebas buscamos el ID 1.
        // En producción usa: $user = $this->getUser();
        $user = $this->getUser();

        if (!$user) {
            $this->addFlash('error', 'Debes iniciar sesión para votar.');
            return $this->redirectToRoute('app_login'); // O tu ruta de login
        }
        // 1. Creamos el objeto Ranking (Cabecera)
        $ranking = new Ranking();
        $ranking->setCategory($category);
        $ranking->setUser($user);

        $em->persist($ranking);

        // 2. Obtenemos las posiciones del formulario (name="ranking[ID_DINO]")
        $rankingData = $request->request->all('ranking');

        foreach ($rankingData as $dinoId => $positionValue) {
            if (!$positionValue) continue;

            $dinosaur = $em->getRepository(Dinosaurs::class)->find($dinoId);

            if ($dinosaur) {
                // 3. Creamos el detalle (Fila en ranking_dinosaur)
                $rankingDino = new RankingDinosaur();
                $rankingDino->setDinosaur($dinosaur);
                $rankingDino->setRanking($ranking); // Vinculamos al objeto Ranking creado arriba
                $rankingDino->setPosition((int)$positionValue);

                $em->persist($rankingDino);
            }
        }

        // 4. Guardamos todo en una sola transacción
        $em->flush();

        $this->addFlash('success', '¡Ranking guardado con éxito!');

        // Redirigimos al ranking global para ver cómo ha quedado
        return $this->redirectToRoute('top_rated_dinos_app');
    }

    #[Route('/top_rated_dinos/ranking/categoria/{id}', name: 'top_rated_dinos_top_app')]
    public function showRankingPoints(
        Category $category,
        RankingDinosaurRepository $rankingDinoRepo
    ): Response
    {
        // 1. Usamos el QueryBuilder para agrupar por dinosaurio y calcular la posición media
        // Esto evita que se repitan las especies si varios usuarios las han votado
        $queryData = $rankingDinoRepo->createQueryBuilder('rd')
            ->select('d.id, d.name, d.image, d.diet, d.period, d.type, AVG(rd.position) as avgPosition')
            ->innerJoin('rd.ranking', 'r')
            ->innerJoin('rd.dinosaur', 'd')
            ->where('r.category = :cat')
            ->setParameter('cat', $category)
            ->groupBy('d.id')
            ->orderBy('avgPosition', 'ASC') // El que tiene media de 1.0 va primero
            ->getQuery()
            ->getResult();

        $totalDinos = count($queryData);

        // 2. Transformamos los datos para que el template los entienda
        $rankingEntries = [];
        foreach ($queryData as $key => $data) {
            $position = $key + 1; // La posición real en el ranking agrupado

            // Cálculo de puntos proporcionales (0 a 100)
            if ($totalDinos > 1) {
                $points = (($totalDinos - $position) / ($totalDinos - 1)) * 100;
            } else {
                $points = 100;
            }

            // Creamos un objeto genérico para que el template no falle
            $rankingEntries[] = [
                'position' => $position,
                'points' => (int)round($points),
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
        ]);
    }
}
