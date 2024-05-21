<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;

class UserProfileController extends AbstractController
{
    #[Route('/profile', name: 'user_profile')]
    public function edit(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
        }

        $form = $this->createForm(UserProfileType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           
            $oldPassword = $form->get('old_password')->getData();
            if ($oldPassword) {
              
                if (!$passwordHasher->isPasswordValid($user, $oldPassword)) {
                    $this->addFlash('danger', 'L\'ancien mot de passe est incorrect.');
                } else {
                    $newPassword = $form->get('plainPassword')->getData();
                    if ($newPassword) {
                        $encodedPassword = $passwordHasher->hashPassword($user, $newPassword);
                        $user->setPassword($encodedPassword);
                    }
                }
            }

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Vos informations ont été mises à jour.');

            return $this->redirectToRoute('user_profile');
        } else {
           
            foreach ($form->getErrors(true, true) as $error) {
                $this->addFlash('danger', $error->getMessage());
            }
        }

        return $this->render('user_profile/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
