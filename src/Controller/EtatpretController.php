<?php

namespace App\Controller;

use App\Entity\Etatpret;
use App\Entity\Pret;
use App\Form\EtatpretType;
use App\Repository\EtatpretRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/etatpret')]
class EtatpretController extends AbstractController
{
    #[Route('/list', name: 'app_etatpret_list', methods: ['GET', 'POST'])]
    public function index(EtatpretRepository $etatpretRepository): Response
    {
        return $this->render('etatpret/index.html.twig', [
            'etatprets' => $etatpretRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_etatpret_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EtatpretRepository $etatpretRepository): Response
    {
        $etatpret = new Etatpret();
        $form = $this->createForm(EtatpretType::class, $etatpret);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $etatpretRepository->save($etatpret, true);

            return $this->redirectToRoute('app_etatpret_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('etatpret/new.html.twig', [
            'etatpret' => $etatpret,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_etatpret_show', methods: ['GET'])]
    public function show(Etatpret $etatpret): Response
    {
        return $this->render('etatpret/show.html.twig', [
            'etatpret' => $etatpret,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_etatpret_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Etatpret $etatpret, EtatpretRepository $etatpretRepository): Response
    {
        $form = $this->createForm(EtatpretType::class, $etatpret);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $etatpretRepository->save($etatpret, true);

            return $this->redirectToRoute('app_pret_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('etatpret/edit.html.twig', [
            'etatpret' => $etatpret,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_etatpret_delete', methods: ['POST'])]
    public function delete(Request $request, Etatpret $etatpret, EtatpretRepository $etatpretRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $etatpret->getId(), $request->request->get('_token'))) {
            $etatpretRepository->remove($etatpret, true);
        }

        return $this->redirectToRoute('app_etatpret_index', [], Response::HTTP_SEE_OTHER);
    }
}
