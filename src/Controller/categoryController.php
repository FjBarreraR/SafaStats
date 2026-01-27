<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\DinosaursRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class categoryController extends AbstractController
{
    #[Route('/category', name: 'category_app')]
    public function main(): Response
    {
        return $this->render('category/categoriesMain.html.twig');
    }

    #[Route('/category/all', name: 'app_category_index')]
    public function seeCategories(CategoryRepository $categoryRepository): Response
    {
        return $this->render('category/seeCategories.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }
    #[Route('/category/create', name: 'app_category_new')]
    #[Route('/category/edit/{id}', name: 'app_category_edit', requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_ADMIN')]
    public function save(
        ?Category $category,
        Request $request,
        DinosaursRepository $dinosaursRepository,
        EntityManagerInterface $entityManager
    ): Response {
        // Si la ruta es 'create', $category será null, así que creamos una nueva
        if (!$category) {
            $category = new Category();
        }

        if ($request->isMethod('GET')) {
            return $this->render('category/category.html.twig', [
                'category' => $category,
                'dinosaurs' => $dinosaursRepository->findAll(),
            ]);
        }

        // Lógica POST (Guardar/Actualizar)
        $category->setName($request->request->get('name'));
        $category->setImage($request->request->get('image'));

        // Sincronizar relación N:M (Dinosaurios)
        // 1. Limpiamos los actuales para evitar duplicados o mantener los desmarcados
        foreach ($category->getDinosaurs() as $dino) {
            $category->removeDinosaur($dino);
        }

        // 2. Añadimos los seleccionados
        $dinosaur_selected = $request->request->all('items');
        foreach ($dinosaur_selected as $idDinosaur) {
            $dinosaur = $dinosaursRepository->find($idDinosaur);
            if ($dinosaur) {
                $category->addDinosaur($dinosaur);
            }
        }

        $entityManager->persist($category);
        $entityManager->flush();

        $this->addFlash('success', 'Categoría guardada correctamente.');
        return $this->redirectToRoute('app_category_index');
    }

    #[Route('/category/{id}', name: 'app_category_show', requirements: ['id' => '\d+'])]
    public function show(Category $category): Response
    {
        return $this->render('category/showCategory.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/category/delete/{id}', name: 'app_category_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Category $category, EntityManagerInterface $entityManager): Response
    {
        // Validación del token CSRF que pusimos en el template
        if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->request->get('_token'))) {
            $entityManager->remove($category);
            $entityManager->flush();
            $this->addFlash('success', 'Categoría eliminada con éxito.');
        }

        return $this->redirectToRoute('app_category_index');
    }
}
