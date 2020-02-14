<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/admin/register", name="app_register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = $this->getUser();
        if ($user) {
            if ($user->isAdmin()) {
                $user = new User();
                $form = $this->createForm(RegistrationFormType::class, $user);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    // encode the plain password
                    $user
                        ->setRoles(['ROLE_USER'])
                        ->setPassword(
                            $passwordEncoder->encodePassword(
                                $user,
                                $form->get('plainPassword')->getData()
                            )
                        );
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($user);
                    $entityManager->flush();

                    $this->addFlash('success', 'Nouvel utilisateur enregistré.');
                    return $this->redirectToRoute('user_list');
                }
                return $this->render('registration/register.html.twig', [
                    'registrationForm' => $form->createView(),
                ]);
            }
        }
        return $this->redirectToRoute('homepage');
    }
}
