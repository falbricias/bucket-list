<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{

    #[Route('/', name: 'main_home')]
    public function home(): Response
    {
        $tpname = 'Bucket List';
        $myList = ['Visit Rome', 'Travel to Japan', 'Go surfin Mexico', 'Try skyjump'];
        return $this->render("main/home.html.twig", [
            'name' => $tpname,
            'list' => $myList
        ]);
    }

    #[Route('/about-us', name: 'main_about_us')]
    public function aboutUs(): Response
    {
        return $this->render("main/about_us.html.twig");
    }



}
