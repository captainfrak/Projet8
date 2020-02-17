<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    // *** ALL USER CONTROLLER IS FOR ROLE_ADMIN ONLY *** //

    /**
     * @Route("/admin/users", name="user_list")
     */
    public function listUsers()
    {
        $user = $this->getUser();
        if ($user) {
            if ($user->isAdmin()) {
                return $this->render('user/list.html.twig', ['users' => $this->getDoctrine()->getRepository(User::class)->findAll()]);
            }
        }

        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route("/admin/users/{id}/edit", name="user_edit")
     *
     * @return RedirectResponse|Response
     */
    public function editAction(User $user, Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $currentUser = $this->getUser();
        if ($currentUser) {
            if ($currentUser->isAdmin()) {
                $form = $this->createForm(UserType::class, $user);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    if (null != $form->get('password')->getData()) {
                        $user->setPassword(
                            $passwordEncoder->encodePassword(
                                $user,
                                $form->get('password')->getData()
                            )
                        );
                    }
                    $entityManager->persist($user);
                    $entityManager->flush();

                    $this->addFlash('success', "L'utilisateur a bien été modifié");

                    return $this->redirectToRoute('user_list');
                }

                return $this->render('user/edit.html.twig', ['form' => $form->createView(), 'user' => $user]);
            }
        }

        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route("/admin/users/{id}/delete", name="user_delete")
     *
     * @return RedirectResponse
     */
    public function deleteAction(User $user, EntityManagerInterface $entityManager)
    {
        $currentUser = $this->getUser();
        if ($currentUser) {
            if ($currentUser->isAdmin()) {
                $entityManager->remove($user);
                $entityManager->flush();

                $this->addFlash('success', 'L\'utilisateur a bien été supprimé');

                return $this->redirectToRoute('user_list');
            }
        }

        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route("/admin/changerole/{id}", name="user_changerole")
     *
     * @return RedirectResponse
     */
    public function changeRole(User $user, EntityManagerInterface $entityManager)
    {
        $currentUser = $this->getUser();
        if ($currentUser) {
            if ($currentUser->isAdmin()) {
                if ($user->isAdmin()) {
                    $user->setRoles(['ROLE_USER']);
                    $entityManager->persist($user);
                    $entityManager->flush();
                    $this->addFlash('success', 'Le ROLE a bien été changé en USER');

                    return $this->redirectToRoute('user_list');
                } elseif (!$user->isAdmin()) {
                    $user->setRoles(['ROLE_ADMIN']);
                    $entityManager->persist($user);
                    $entityManager->flush();
                    $this->addFlash('success', 'Le ROLE a bien été changé en ADMIN');

                    return $this->redirectToRoute('user_list');
                }
            }
        }

        return $this->redirectToRoute('homepage');
    }
}
