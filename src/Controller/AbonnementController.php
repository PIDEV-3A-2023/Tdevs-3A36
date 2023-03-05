<?php

namespace App\Controller;

use App\Entity\Abonnement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\AbonnementRepository;
use App\Form\AbonnementType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;

class AbonnementController extends AbstractController
{

    #[Route('/abonnement', name: 'abonnement', methods: ['GET'])]
    public function index(AbonnementRepository $AbonnementRepository): Response
    {
        return $this->render('abonnement/index.html.twig', [
            'abonnement' => $AbonnementRepository->findAll(),
        ]);
    }
    #[Route('/FrontAbonnement', name: 'Fabonnement', methods: ['GET'])]
    public function FrontAbon(AbonnementRepository $AbonnementRepository): Response
    {
        return $this->render('abonnement/FrontAbon.html.twig', [
            'abonnements' => $AbonnementRepository->findAll(),
        ]);
    }
    #[Route('AddAbonnement', name: 'Addabonnement')]
    public function add(ManagerRegistry $doctrine, Request $req): Response
    {
        $em = $doctrine->getManager();
        $Abonnement = new Abonnement();
        $form = $this->createForm(AbonnementType::class, $Abonnement);
        $form->handleRequest($req);
        if ($form->isSubmitted()) {
            $em->persist($Abonnement);
            $em->flush();
            return $this->redirectToRoute('abonnement');
        }

        return $this->renderForm(
            'abonnement/new.html.twig',
            ['form' => $form]
        );
    }
    #[Route('deleteAbon/{id}', name: 'deleteAbon')]
    public function deleteAbon(ManagerRegistry $doctrine, $id): Response
    {
        $em= $doctrine->getManager();
        $S= $doctrine->getRepository(Abonnement::class)->find($id);
        $em->remove($S);
        $em->flush();
        return $this->redirectToRoute('abonnement');
    }
    #[Route('Acheter/{id}', name: 'AcheterAbon')]
    public function BuyAbon(ManagerRegistry $doctrine, $id): Response
    {
        //Jointure entre abonnement et User
        //Quand le utilisateur Click sur le bon acheter
        //On fait
        //user.account.solde - abonnement.prix
        ///////////////////////////////////
        
        $em= $doctrine->getManager();
        $S= $doctrine->getRepository(Abonnement::class)->find($id);
        $em->remove($S);
        $em->flush();
        return $this->redirectToRoute('abonnement');
    }
}
