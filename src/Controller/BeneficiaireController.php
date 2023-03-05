<?php

namespace App\Controller;

use App\Entity\Beneficiaire;
use App\Form\BeneficiaireType;
use App\Repository\BeneficiaireRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\QrcodeService;
use App\Form\SearchType;



#[Route('/beneficiaire')]
class BeneficiaireController extends AbstractController
{
    #[Route('/list', name: 'app_beneficiaire_index', methods: ['GET'])]
    public function index(BeneficiaireRepository $beneficiaireRepository): Response
    {
        return $this->render('beneficiaire/index.html.twig', [
            'beneficiaires' => $beneficiaireRepository->findAll(),
        ]);
    }

    #[Route ('/search' , name : 'search', methods: ['POST'])]
    function search (BeneficiaireRepository $beneficiaireRepository, Request $request) {
        $data = $request -> get('search');
        $beneficiaire = $beneficiaireRepository ->findBy( ['rib_benef'=> $data]);
        return $this -> render('beneficiaire/index.html.twig' ,[
                'beneficiaires' => $beneficiaire
            ]
        );
    }

    #[Route('/new', name: 'app_beneficiaire_new', methods: ['GET', 'POST'])]
    public function new(Request $request, BeneficiaireRepository $beneficiaireRepository): Response
    {
        $beneficiaire = new Beneficiaire();
        $form = $this->createForm(BeneficiaireType::class, $beneficiaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $beneficiaireRepository->save($beneficiaire, true);
            return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('beneficiaire/new.html.twig', [
            'beneficiaire' => $beneficiaire,
            'form' => $form,
        ]);
    }

    #[Route("/{id_benef}/qrcode", name: 'app_qrcode',methods: ['GET'])]
    public function qrcodeGenerator(Beneficiaire $beneficiaire,QrcodeService $qrcodeService): Response
    {
       $qrCode = null;
       //$sahraoui=$beneficiaireRpeository->find($id_benef);
       
       $data="\n Rib : ";
       $data.=$beneficiaire->getRibBenef();
       $data.="\n Email : " .$beneficiaire->getEmailBenef();
       $data.="\n Nom : ".$beneficiaire->getNomBenef();
       $data.= "\n Prenom : ".$beneficiaire->getPrenomBenef();

           $qrCode = $qrcodeService->qrcode($data);
            

       return $this->render('beneficiaire/show.html.twig', [
        'beneficiaire' => $beneficiaire,
           'qrCode' => $qrCode
       ]);
    }

    #[Route('/{id_benef}', name: 'app_beneficiaire_show', methods: ['GET'])]
    public function show(Beneficiaire $beneficiaire): Response
    {
        $qrCode=false;

        return $this->render('beneficiaire/show.html.twig', [
            'beneficiaire' => $beneficiaire,
            'qrCode' => $qrCode
        ]);
    }

    #[Route('/{id_benef}/edit', name: 'app_beneficiaire_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Beneficiaire $beneficiaire, BeneficiaireRepository $beneficiaireRepository): Response
    {
        $form = $this->createForm(BeneficiaireType::class, $beneficiaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $beneficiaireRepository->save($beneficiaire, true);

            return $this->redirectToRoute('app_beneficiaire_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('beneficiaire/edit.html.twig', [
            'beneficiaire' => $beneficiaire,
            'form' => $form,
        ]);
    }

    #[Route('/{id_benef}', name: 'app_beneficiaire_delete', methods: ['POST'])]
    public function delete(Request $request, Beneficiaire $beneficiaire, BeneficiaireRepository $beneficiaireRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$beneficiaire->getIdBenef(), $request->request->get('_token'))) {
            $beneficiaireRepository->remove($beneficiaire, true);
        }

        return $this->redirectToRoute('app_beneficiaire_index', [], Response::HTTP_SEE_OTHER);
    }
    
   
    
    

}
