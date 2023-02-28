<?php

namespace App\Controller;

use App\Entity\Wish;
use App\Form\WishType;
use App\Repository\WishRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/wish', name : 'wish_')]
class WishController extends AbstractController
{
    #[Route('/list', name: 'list')]
    public function wishList(WishRepository $wishRepository): Response
    {
        //Récupère l'ensemble des voeux en DB
        $wishes = $wishRepository->findPublishedWishes();

        dump($wishes);

        //Renvoie les voeux au twig listant les voeux : "list"
        return $this->render("/wish/list.html.twig", [
            'wishes' => $wishes
        ]);
    }

    #[Route('/{id}', name: 'show', requirements: ['id' => '\d+'])]
    public function show(int $id, WishRepository $wishRepository): Response
    {
        //Récupère le détail d'un souhait en DB
        $wish = $wishRepository->find($id);

        //Crée une erreur 404 si le souhait n'existe pas en DB (d'après son ID)
        if(!$wish){
            throw $this->createNotFoundException('Oops ! This whish does not exist ! Not found exception mother fucker !');
        }

        dump($id);

        //Renvoie le souhait au twig d'affichage d'un voeu : "show"
        return $this->render("/wish/show.html.twig", [
            'wish' => $wish
        ]);
    }

    #[Route ('/add', name : 'add')]
    public function add(WishRepository $wishRepository, EntityManagerInterface $entityManager, Request $request){

        $wish = new Wish();

        //1 - Création d'une instance de form lié à une instance de Wish
        $wishForm = $this->createForm(WishType::class, $wish);

        //2 - Méthode qui extrait les éléments du formulaire de la requête
        $wishForm->handleRequest($request);

        //3 - Traitement si le formulaire est soumis
        if($wishForm->isSubmitted() && $wishForm->isValid()){
            //Sauvegarde en DB la nouvelle série saisie par l'utilisateur
            $wishRepository->save($wish, true);

            //Message flash d'info d'ajout de la série OK
            $this->addFlash('success', 'Wish added ! You are a winner !');

            //Redirige vers la page de détail de la série
            return $this->redirectToRoute('wish_show', ['id' => $wish->getId()]);

        }

        dump($wish);

        return $this->render('/wish/add.html.twig', [
            'wishForm' => $wishForm->createView()
        ]);
    }

}
