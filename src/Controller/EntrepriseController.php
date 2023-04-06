<?php

namespace App\Controller;

use App\Entity\Entreprise;
use App\Form\EntrepriseType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EntrepriseController extends AbstractController
{
    #[Route('/entreprise', name: 'app_entreprise')]
    public function index(ManagerRegistry $doctrine): Response
    {
        // récupérer toutes les entreprises de la BDD
        $entreprises = $doctrine->getRepository(Entreprise::class)->findBy([], ["raisonSociale"=> "ASC"]);
        return $this->render('entreprise/index.html.twig', [
            'entreprises' => $entreprises,
        ]);
    }

    #[Route('/entreprise/add', name: 'add_entreprise'),
        Route('/entreprise/edit/{id}', name: 'edit_entreprise')]
    public function add(ManagerRegistry $doctrine, Entreprise $entreprise = null, Request $request): Response{

        if(!$entreprise){
            $entreprise = new Entreprise();
        }

        $form = $this->createForm(EntrepriseType::class, $entreprise); // crée mon formulaire à partir du builder EntrepriseType
        $form-> handleRequest($request); // quand une action est effectué sur le formulaire, récupère les données

        if($form->isSubmitted() && $form->isValid()){ // is valid = sécurité des champs

            $entreprise = $form->getData(); // récupère les données du formulaire et les envoie dans entreprise
            //vers la bdd

            $entityManager = $doctrine->getManager();
            $entityManager->persist($entreprise); // constituer l'objet / prepare
            $entityManager->flush(); // ajout en bdd / insert into 

            return $this->redirectToRoute('app_entreprise');

        }

        // vue pour formulaire
        return $this->render('entreprise/add.html.twig', [
            'formAddEntreprise' => $form->createView(),
            'edit' => $entreprise->getId(),
        ]);
    }

    #[Route('/entreprise/{id}/delete', name: 'delete_entreprise')]
    public function delete (ManagerRegistry $doctrine, Entreprise $entreprise): Response{
        $entityManager = $doctrine->getManager();
        $entityManager->remove($entreprise);
        $entityManager->flush(); //delete l'objet 

        return $this->redirectToRoute('app_entreprise');

    }



    #[Route('/entreprise/{id}', name: 'show_entreprise')]
    public function show (Entreprise $entreprise) : Response{

        return $this->render('entreprise/show.html.twig', [
            'entreprise' => $entreprise,
        ]);

    }

   
}
