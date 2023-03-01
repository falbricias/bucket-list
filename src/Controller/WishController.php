<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Wish;
use App\Form\WishType;
use App\Repository\WishRepository;
use App\Utils\Censurator;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

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
    //#[IsGranted("ROLE_USER")] => pour info : car déjà paramétré dans le security.yaml => ligne 35 : - { path: ^/wish/add, roles: ROLE_USER }
    public function add(WishRepository $wishRepository,
                        EntityManagerInterface $entityManager,
                        Request $request,
                        Censurator $censurator){

        $wish = new Wish();
        /*0 - Dans notre formulaire d'ajout d'un voeu :
        Sette le nom de l'auteur d'un nouveau voeu avec le nom de l'user connecté */
        $wish->setAuthor($this->getUser()->getUserIdentifier());

        //1 - Création d'une instance de form lié à une instance de Wish
        $wishForm = $this->createForm(WishType::class, $wish);

        //2 - Méthode qui extrait les éléments du formulaire de la requête
        $wishForm->handleRequest($request);

        //3 - Traitement si le formulaire est soumis
        if($wishForm->isSubmitted() && $wishForm->isValid()){

            //Avant la sauvegarde en DB => checke les mots et censure ceux concernés
            $titleCensored = $censurator->purify($wish->getTitle());
            $descriptionCensored = $censurator->purify($wish->getDescription());

            //Re-sette le voeu avec titre et description censurés
            $wish->setTitle($titleCensored);
            $wish->setDescription($descriptionCensored);

            /* Option plus rapide :
             * $wish->setTitle($censurator->purify($wish->getTitle()));
             * $wish->setDescription($censurator->purify($wish->getDescription()));
             * */

            //Sauvegarde en DB le nouveau voeu saisi par l'utilisateur
            $wishRepository->save($wish, true);

            //Message flash d'info d'ajout du voeu OK
            $this->addFlash('success', 'Wish added ! You are a winner !');

            //Redirige vers la page de détail du voeu
            return $this->redirectToRoute('wish_show', ['id' => $wish->getId()]);

        }

        dump($wish);

        return $this->render('/wish/add.html.twig', [
            'wishForm' => $wishForm->createView()
        ]);
    }

    #[Route('update/{id}', name: 'update', requirements: ['id' => '\d+'])]
    public function update(int $id, WishRepository $wishRepository): Response
    {
        //Récupère le détail d'un souhait en DB
        $wish = $wishRepository->find($id);

        //Crée une erreur 404 si le souhait n'existe pas en DB (d'après son ID)
        if(!$wish){
            throw $this->createNotFoundException('Oops ! This whish does not exist ! Not found exception mother fucker !');
        }

        $wishForm = $this->createForm(WishType::class, $wish);


        //Renvoie le souhait au twig d'affichage d'un voeu : "show"
        return $this->render("/wish/update.html.twig", [
            'wish' => $wish,
            'wishForm' => $wishForm->createView()
        ]);
    }

}
