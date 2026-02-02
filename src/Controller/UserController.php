<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;


class UserController extends AbstractController
{
    // Cargar el formulario y mandar el formulario del registro del usuario
    #[Route('/register', name: 'app_register')]
    public function register(Request                     $request,
                             UserPasswordHasherInterface $passwordHasher,
                             EntityManagerInterface      $entityManager): Response
    {

        if ($request->isMethod('POST')) {

            $new_user = new User();
            $new_user->setEmail($request->request->get('email'));
            $password_text = $request->request->get('password');
            $new_user->setRole(['ROLE_USER']);


            $hashedPassword = $passwordHasher->hashPassword(
                $new_user,
                $password_text
            );

            $new_user->setPassword($hashedPassword);

            $entityManager->persist($new_user);
            $entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('user/register.html.twig', [
        ]);
    }

    // Cargar usuarios
    #[Route('/users', name: 'users_app')]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/seeUser.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }
}
