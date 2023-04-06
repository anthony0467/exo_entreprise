<?php

namespace App\Controller;

use App\Entity\Employe;
use App\Form\EmployeType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EmployeController extends AbstractController
{
    #[Route('/employe', name: 'app_employe')]
    public function index(ManagerRegistry $doctrine): Response
    {
       // récupérer toutes les entreprises de la BDD
       $employes = $doctrine->getRepository(Employe::class)->findBy([], ["nom"=> "ASC"]);
       return $this->render('employe/index.html.twig', [
           'employes' => $employes,
       ]);
    }

    #[Route('/employe/add', name: 'add_employe'),
    Route('/employe/edit/{id}', name: 'edit_employe')]
    public function add(ManagerRegistry $doctrine, Employe $employe = null, Request $request): Response{

        if(!$employe){
            $employe = new Employe();
        }

        $form = $this->createForm(EmployeType::class, $employe); // crée mon formulaire à partir du builder EntrepriseType
        $form-> handleRequest($request); // quand une action est effectué sur le formulaire, récupère les données

        if($form->isSubmitted() && $form->isValid()){ // is valid = sécurité des champs

            $employe = $form->getData(); // récupère les données du formulaire et les envoie dans entreprise
            //vers la bdd

            $entityManager = $doctrine->getManager();
            $entityManager->persist($employe); // constituer l'objet / prepare
            $entityManager->flush(); // ajout en bdd / insert into 

            return $this->redirectToRoute('app_employe');

        }

        // vue pour formulaire
        return $this->render('employe/add.html.twig', [
            'formAddEmploye' => $form->createView(),
            'edit' => $employe->getId(), //important pour verifier si employe existe et mettre une condition sur la view
        ]);
    }

    #[Route('/employe/{id}/delete', name: 'delete_employe')]
    public function delete (ManagerRegistry $doctrine, Employe $employe): Response{
        $entityManager = $doctrine->getManager();
        $entityManager->remove($employe);
        $entityManager->flush(); //delete l'objet 

        return $this->redirectToRoute('app_employe');

    }



    #[Route('/employe/{id}', name: 'show_employe')]
    public function show (Employe $employe) : Response{

        return $this->render('employe/show.html.twig', [
            'employe' => $employe,
        ]);

    }
}
