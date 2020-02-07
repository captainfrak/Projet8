<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class Controller extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        $user = $this->getUser();
        if ($user == null) {
            return $this->redirectToRoute('app_login');
        }

        // Uncomment if you want to redirect Admin to user_list
        /*if ($user->isAdmin()) {
            return $this->redirectToRoute('user_list');
        }*/
        return $this->render('index.html.twig');
    }
}
