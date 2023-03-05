<?php

namespace App\Controller;

use App\Entity\Depenses;
use App\Form\DepensesType;
use App\Repository\DepensesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/back/depenses')]
class BackDepensesController extends AbstractController
{
    #[Route('/', name: 'app_back_depenses_index', methods: ['GET'])]
    public function index(DepensesRepository $depensesRepository): Response
    {
        return $this->render('back_depenses/index.html.twig', [
            'depenses' => $depensesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_back_depenses_new', methods: ['GET', 'POST'])]
    public function new(Request $request, DepensesRepository $depensesRepository): Response
    {
        $depense = new Depenses();
        $form = $this->createForm(DepensesType::class, $depense);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $depensesRepository->save($depense, true);

            return $this->redirectToRoute('app_back_depenses_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back_depenses/new.html.twig', [
            'depense' => $depense,
            'form' => $form,
        ]);
    }

    #[Route('/{id_depense}', name: 'app_back_depenses_show', methods: ['GET'])]
    public function show(Depenses $depense): Response
    {
        return $this->render('back_depenses/show.html.twig', [
            'depense' => $depense,
        ]);
    }

    #[Route('/{id_depense}/edit', name: 'app_back_depenses_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Depenses $depense, DepensesRepository $depensesRepository): Response
    {
        $form = $this->createForm(DepensesType::class, $depense);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $depensesRepository->save($depense, true);

            return $this->redirectToRoute('app_back_depenses_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back_depenses/edit.html.twig', [
            'depense' => $depense,
            'form' => $form,
        ]);
    }

    #[Route('/{id_depense}', name: 'app_back_depenses_delete', methods: ['POST'])]
    public function delete(Request $request, Depenses $depense, DepensesRepository $depensesRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$depense->getId_depense(), $request->request->get('_token'))) {
            $depensesRepository->remove($depense, true);
        }

        return $this->redirectToRoute('app_back_depenses_index', [], Response::HTTP_SEE_OTHER);
    }
}
