<?php

namespace App\Controller;

use App\Entity\Virement;
use App\Form\VirementType;
use App\Repository\VirementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Dompdf\Dompdf;
use Dompdf\Options;

#[Route('/virement')]
class VirementController extends AbstractController
{
    #[Route('/list', name: 'app_virement_index', methods: ['GET'])]
    public function index(VirementRepository $virementRepository): Response
    {
        return $this->render('virement/index.html.twig', [
            'virements' => $virementRepository->findAll(),
        ]);
    }
    

    #[Route ('/searchh' , name : 'searchh', methods: ['POST'])]
    function searchh (VirementRepository $virementRepository, Request $request) {
        $data = $request -> get('search');
        $virement = $virementRepository ->findBy( ['prenom_benef'=> $data]);
        return $this -> render('virement/index.html.twig' ,[
                'virements' => $virement
            ]
        );
    }


    
    #[Route('/new', name: 'app_virement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, VirementRepository $virementRepository): Response
    {
        $virement = new Virement();
        $form = $this->createForm(VirementType::class, $virement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $virementRepository->save($virement, true);

            return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('virement/new.html.twig', [
            'virement' => $virement,
            'form' => $form,
        ]);
    }

    #[Route('/trie',name:'trie')]
    public function trier(Request $request,VirementRepository $virementRepository):Response
    {
        $form = $this->createForm(VirementType::class);
        $form->handleRequest($request);
        $virement = $virementRepository->findAll();

        if($form->isSubmitted() && $form->isValid())
        {
            $data = $form->getData();
            $results = $virementRepository->getVirementByMontant();

        return $this->renderForm('/virement/index.html.twig',[
            'virements'=> $results,
            'form'=>$form
        ]);

        }

        return $this->renderForm('/virement/index.html.twig',[
            'virements'=> $virement,
            'form'=>$form
        ]);


    }
    

    #[Route('/mypdf', name: 'app_mypdf', methods: ["GET"])]
    public function mypdf(VirementRepository $virementRepository): Response
    {   
        
            // Configure Dompdf according to your needs
            $pdfOptions = new Options();
            $pdfOptions->set('defaultFont', 'Arial');
            
            // Instantiate Dompdf with our options
            $dompdf = new Dompdf($pdfOptions);
            $virement = $virementRepository->findAll();
            
            // Retrieve the HTML generated in our twig file
            $html = $this->renderView('virement/mypdf.html.twig', [
                'virements' => $virement
            ]);
            
            // Load HTML to Dompdf
            $dompdf->loadHtml($html);
            
            // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
            $dompdf->setPaper('A4', 'portrait');
    
            // Render the HTML as PDF
            $dompdf->render();
    
            // Output the generated PDF to Browser (force download)
            $dompdf->stream("mypdf.pdf", [
                "Attachment" => true
            ]);
            
            return new Response('', 200, [
                'Content-Type' => 'application/pdf'
            ]);
        
    }


    #[Route('/{id_virement}', name: 'app_virement_show', methods: ['GET'])]
    public function show(Virement $virement): Response
    {
        return $this->render('virement/show.html.twig', [
            'virement' => $virement,
        ]);
    }


    
    #[Route('/{id_virement}/edit', name: 'app_virement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Virement $virement, VirementRepository $virementRepository): Response
    {
        $form = $this->createForm(VirementType::class, $virement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $virementRepository->save($virement, true);

            return $this->redirectToRoute('app_virement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('virement/edit.html.twig', [
            'virement' => $virement,
            'form' => $form,
        ]);
    }

    

    #[Route('/{id_virement}', name: 'app_virement_delete', methods: ['POST'])]
    public function delete(Request $request, Virement $virement, VirementRepository $virementRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$virement->getIdVirement(), $request->request->get('_token'))) {
            $virementRepository->remove($virement, true);
        }

        return $this->redirectToRoute('app_virement_index', [], Response::HTTP_SEE_OTHER);
    }
    
   
    

}
