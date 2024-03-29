<?php

namespace App\Controller;

use App\Entity\Budget;
use App\Form\Budget1Type;
use App\Repository\BudgetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/back/budget')]
class BackBudgetController extends AbstractController
{
    #[Route('/', name: 'app_back_budget_index', methods: ['GET'])]
    public function index(BudgetRepository $budgetRepository): Response
    {
        return $this->render('back_budget/index.html.twig', [
            'budgets' => $budgetRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_back_budget_new', methods: ['GET', 'POST'])]
    public function new(Request $request, BudgetRepository $budgetRepository): Response
    {
        $budget = new Budget();
        $form = $this->createForm(Budget1Type::class, $budget);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $budgetRepository->save($budget, true);

            return $this->redirectToRoute('app_back_budget_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back_budget/new.html.twig', [
            'budget' => $budget,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_back_budget_show', methods: ['GET'])]
    public function show(Budget $budget): Response
    {
        return $this->render('back_budget/show.html.twig', [
            'budget' => $budget,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_back_budget_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Budget $budget, BudgetRepository $budgetRepository): Response
    {
        $form = $this->createForm(Budget1Type::class, $budget);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $budgetRepository->save($budget, true);

            return $this->redirectToRoute('app_back_budget_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back_budget/edit.html.twig', [
            'budget' => $budget,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_back_budget_delete', methods: ['POST'])]
    public function delete(Request $request, Budget $budget, BudgetRepository $budgetRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$budget->getId(), $request->request->get('_token'))) {
            $budgetRepository->remove($budget, true);
        }

        return $this->redirectToRoute('app_back_budget_index', [], Response::HTTP_SEE_OTHER);
    }
}
