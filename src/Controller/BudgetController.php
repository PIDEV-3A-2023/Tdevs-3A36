<?php

namespace App\Controller;
use MercurySeries\FlashyBundle\FlashyNotifier;
use App\Entity\Budget;
use App\Form\BudgetType;
use App\Repository\BudgetRepository;
use App\Repository\AccountRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/budget')]
class BudgetController extends AbstractController
{
    #[Route('/', name: 'app_budget_index', methods: ['GET'])]
    public function index(BudgetRepository $budgetRepository): Response
    {
        return $this->render('budget/index.html.twig', [
            'budgets' => $budgetRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_budget_new', methods: ['GET', 'POST'])]
    public function new(FlashyNotifier $flashyNotifier,Request $request, BudgetRepository $budgetRepository,AccountRepository $accountRP): Response
    {
        $budgets= $accountRP ->findAll();
        $AccountSolde=0;
        foreach ($budgets as $budget){
            $AccountSolde=$budget->getSolde();
        }
        $budget = new Budget();
        $form = $this->createForm(BudgetType::class, $budget);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $existingEntity = $budgetRepository->findOneByMonth($budget->getDatedebut(),$budget->getDatefin());
            if ((!$existingEntity)) {
                if($form['montant']->getData()<=$AccountSolde){
                    $budgetRepository->save($budget, true);
                    return $this->redirectToRoute('app_depenses_index', [], Response::HTTP_SEE_OTHER);
                }
                else{
                    $flashyNotifier->error('le montant saisi est supérieur à votre solde');
                }
              
              
             
            }
            else{
                $flashyNotifier->error('Vous avez déjà un budget dans cet intervalle');
            }
            
        }

        return $this->render('budget/new.html.twig', [
            'budget' => $budget,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_budget_show', methods: ['GET'])]
    public function show(Budget $budget): Response
    {
        return $this->render('budget/show.html.twig', [
            'budget' => $budget,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_budget_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Budget $budget, BudgetRepository $budgetRepository): Response
    {
        $form = $this->createForm(BudgetType::class, $budget);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $budgetRepository->save($budget, true);

            return $this->redirectToRoute('app_depenses_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('budget/edit.html.twig', [
            'budget' => $budget,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_budget_delete', methods: ['POST'])]
    public function delete(Request $request, Budget $budget, BudgetRepository $budgetRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$budget->getId(), $request->request->get('_token'))) {
            $budgetRepository->remove($budget, true);
        }

        return $this->redirectToRoute('app_depenses_index', [], Response::HTTP_SEE_OTHER);
    }
}
