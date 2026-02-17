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
    // Cargar página principal de categoría
    #[Route('/admin/category', name: 'category_app')]
    public function main(): Response
    {
        return $this->render('category/categoriesMain.html.twig');
    }

    // Cargar todas las categorías
    #[Route('/admin/category/all', name: 'app_category_index')]
    public function seeCategories(CategoryRepository $categoryRepository): Response
    {
        return $this->render('category/seeCategories.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    // Guardar y editar categorías en base de datos junto a sus correspondientes dinosaurios
    #[Route('/admin/category/create', name: 'app_category_new')]
    #[Route('/admin/category/edit/{id}', name: 'app_category_edit', requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_ADMIN')]
    public function save(
        ?Category $category,
        Request $request,
        DinosaursRepository $dinosaursRepository,
        EntityManagerInterface $entityManager
    ): Response {
        if (!$category) {
            $category = new Category();
        }

        if ($request->isMethod('GET')) {
            return $this->render('category/category.html.twig', [
                'category' => $category,
                'dinosaurs' => $dinosaursRepository->findAll(),
            ]);
        }

        $category->setName($request->request->get('name'));
        $category->setImage($request->request->get('image'));

        foreach ($category->getDinosaurs() as $dino) {
            $category->removeDinosaur($dino);
        }

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

    // Cargar cada categoría con sus datos
    #[Route('/admin/category/{id}', name: 'app_category_show', requirements: ['id' => '\d+'])]
    public function show(Category $category): Response
    {
        return $this->render('category/showCategory.html.twig', [
            'category' => $category,
        ]);
    }

    // Eliminar una categoría con todos sus datos
    #[Route('/admin/category/delete/{id}', name: 'app_category_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Category $category, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->request->get('_token'))) {
            $entityManager->remove($category);
            $entityManager->flush();
            $this->addFlash('success', 'Categoría eliminada con éxito.');
        }

        return $this->redirectToRoute('app_category_index');
    }
}
