<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\InscriptionFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;

class InscriptionController extends AbstractController
{

    private $entityManager;
    private $passwordCredentials;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordCredentials)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordCredentials;
    }

    #[Route('/inscription', name: 'app_inscription')]
    public function index(Request $request): Response
    {

        $user = new User();
        $form = $this->createForm(InscriptionFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $user->setRoles(['ROLE_USER']);

            $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('inscription/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
