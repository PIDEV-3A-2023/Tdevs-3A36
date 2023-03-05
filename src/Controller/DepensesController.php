<?php

namespace App\Controller;
use MercurySeries\FlashyBundle\FlashyNotifier;
use App\Entity\Virement;
use App\Entity\Depenses;
use App\Form\Depenses1Type;
use App\Repository\DepensesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Budget;
use App\Repository\BudgetRepository;
use App\Form\BudgetType;
use App\Form\SearchbytitleType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;
#[Route('/depenses')]
class DepensesController extends AbstractController
{
    #[Route('/calendrier1', name: 'depenses_calendrier')]
    public function calendrier(): Response
    {
       
        $rdvs=[];
        $depenses1 =  $this->getDoctrine()->getRepository(Depenses::class)->findAll();
        foreach($depenses1 as $event){
            $rdvs[] = [
                'title' => $event->getTitle(),
                'RIBDestinataire'=>$event->getRibDestinataire(),
                'PrenomDestinataire'=>$event->getPrenomDestinataire(),
                'Montant'=>$event->getMontant(),
                'start' => $event->getDatedebut()->format('Y-m-d H:i:s'),
                'backgroundColor' => $event->getBackgroundcolor(),
               
            ];
        }

        $data = json_encode($rdvs);
        return $this->render('depenses/calendrier.html.twig', compact('data'));
    }
    #[Route('/', name: 'app_depenses_index', methods: ['GET', 'POST'])]
    public function index(FlashyNotifier $flashyNotifier,DepensesRepository $depensesRepository,BudgetRepository $budgetRepository,Request $req,ManagerRegistry $entityManager): Response
    { 
        
        $budgets= $budgetRepository ->findlastbudget();
        $today = new \DateTime('today');
        $repository = $entityManager->getRepository(Depenses::class);
        $depenses1 = $repository->findBy(['datedebut' => $today]);
        if (!empty($depenses1)) {
            
            foreach ($depenses1 as $depense) {
                $virement = new Virement();
                $virement->setDateVirement($today);
                $virement->setMontant($depense->getMontant());
                $virement->setTitre($depense->getTitle());
                $virement->setPrenomBenef($depense->getPrenomDestinataire());
                $virement->setRibBenef($depense->getRibDestinataire());
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($virement);
                $entityManager->remove($depense);
                $entityManager->flush();
                $flashyNotifier->error('votre depense est transmetre avec succès.');
                
                
            }
           
        }
        $totalbudget=0;
       
        foreach ($budgets as $budget){
            $totalbudget=$budget->getMontant();
        }
        $sum2=0;
        $depenses = $depensesRepository->findAll();
        foreach ($depenses as $depense){
            $sum2+=$depense->getMontant();
        }
        $rest=round($totalbudget-$sum2);
        $categories=[];
        $categCount = [];
        $methodepaiment=[];
        $countmethode=[];
        
        
        foreach($depenses as $depense){
            $categorie = ($depense->getCategorieDepense());
            $methode =$depense->getTypeDepense();
            if (!in_array($categorie, $categories)) {
                $categories[] = $categorie;
            }
            if (!in_array($methode, $methodepaiment)) {
                $methodepaiment[] = $methode;
            }
           
        }
        foreach($categories as $count){
            $categCount[]= ($depensesRepository->studentSearchByUserNameDQL($count));
        }
        $d2 = [];

         foreach ($categCount as $item) {
               $d2[] = $item[0]['count'];
            }
        foreach($methodepaiment as $count){
                $countmethode[]= ($depensesRepository->statisticsbymethod($count));
            }
        $d3 = [];
    
        foreach ($countmethode as $item) {
                   $d3[] = $item[0]['count'];
        } 
           
        $form = $this->createForm(SearchbytitleType::class);
        $form->handleRequest($req);
        $parms=[];
        if($form->isSubmitted()){
            
                $parms['titre'] = $form['titre_search']->getData();   
            
                $parms['prenom'] = $form['prenom']->getData();   
            
                $parms['rib'] = $form['rib']->getData();   
           
                $parms['montant'] = $form['montant']->getData();   
           
                $parms['datedebut'] = $form['datedebut']->getData();   
            
                $parms['categorie_depense'] = $form['categorie_depense']->getData();   
        
                $parms['type_depense'] = $form['type_depense']->getData();   
          
            
            
            $depensesByAVG = $depensesRepository->depensesSearchBytitle($parms);
            return $this->renderForm('depenses/index.html.twig', [
                'formm'=>$form,
                'depenses' => $depensesByAVG,
                'Budgets' => $budgetRepository->findlastbudget(),
                'sum'=>$sum2,
                'rest'=>$rest,
                'categorie'=>json_encode($categories),
                'categCount'=>json_encode($d2),
                 'methodepaiment'=>json_encode($methodepaiment),
                'countmethode'=>json_encode($d3),
                
            ]);
        }
 
        
        return $this->render('depenses/index.html.twig', [
            'depenses' =>$depenses,
            'Budgets' => $budgetRepository->findlastbudget(),
            'sum'=>$sum2,
            'formm'=>$form->createView(),
            'rest'=>$rest,
            'categorie'=>json_encode($categories),
            'categCount'=>json_encode($d2),
            'methodepaiment'=>json_encode($methodepaiment),
            'countmethode'=>json_encode($d3),
            
             

        ]);
    }
    #[Route('/new', name: 'app_depenses_new', methods: ['GET', 'POST'])]
    public function new(FlashyNotifier $flashyNotifier,Request $request, DepensesRepository $depensesRepository,BudgetRepository $budgetRepository): Response
    {   
        $budgets= $budgetRepository ->findlastbudget();
        $totalbudget=0;
        foreach ($budgets as $budget){
            $totalbudget=$budget->getMontant();
        }
        $sum2=0;
        $depenses = $depensesRepository->findAll();
        foreach ($depenses as $depense){
            $sum2+=$depense->getMontant();
        }
        $rest=round($totalbudget-$sum2);
        $depense = new Depenses();
        $form = $this->createForm(Depenses1Type::class, $depense);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if(($form['montant']->getData())<=$rest){
                $depensesRepository->save($depense, true);
                return $this->redirectToRoute('app_depenses_index', [], Response::HTTP_SEE_OTHER);
            }
            else{
                $flashyNotifier->info('Votre montant saisi est supérieur a la reste du budget');
            }
            
            
        }
        
        return $this->renderForm('depenses/new.html.twig', [
            'depense' => $depense,
            'form' => $form,
        ]);
    }

    #[Route('/{id_depense}', name: 'app_depenses_show', methods: ['GET'])]
    public function show(Depenses $depense): Response
    {
        return $this->render('depenses/show.html.twig', [
            'depense' => $depense,
        ]);
    }

    #[Route('/{id_depense}/edit', name: 'app_depenses_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Depenses $depense, DepensesRepository $depensesRepository): Response
    {
        $form = $this->createForm(Depenses1Type::class, $depense);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $depensesRepository->save($depense, true);

            return $this->redirectToRoute('app_depenses_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('depenses/edit.html.twig', [
            'depense' => $depense,
            'form' => $form,
        ]);
    }

    #[Route('/{id_depense}', name: 'app_depenses_delete', methods: ['POST'])]
    public function delete(Request $request, Depenses $depense, DepensesRepository $depensesRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$depense->getIddepense(), $request->request->get('_token'))) {
            $depensesRepository->remove($depense, true);
        }

        return $this->redirectToRoute('app_depenses_index', [], Response::HTTP_SEE_OTHER);

    }
    #[Route('/afficher/order', name: 'depenses_order')]
    public function fetchDepensesOrderByDate(BudgetRepository $budgetRepository,DepensesRepository $depensesRepository,Request $req) :Response {
        $budgets= $budgetRepository ->findlastbudget();
        $totalbudget=0;
       
        foreach ($budgets as $budget){
            $totalbudget=$budget->getMontant();
        }
        $sum2=0;
        $depenses = $depensesRepository->findAll();
        foreach ($depenses as $depense){
            $sum2+=$depense->getMontant();
        }
        $rest=round($totalbudget-$sum2);
        $categories=[];
        $categCount = [];
        $methodepaiment=[];
        $countmethode=[];
        
        
        foreach($depenses as $depense){
            $categorie = ($depense->getCategorieDepense());
            $methode =$depense->getTypeDepense();
            if (!in_array($categorie, $categories)) {
                $categories[] = $categorie;
            }
            if (!in_array($methode, $methodepaiment)) {
                $methodepaiment[] = $methode;
            }
           
        }
        foreach($categories as $count){
            $categCount[]= ($depensesRepository->studentSearchByUserNameDQL($count));
        }
        $d2 = [];

         foreach ($categCount as $item) {
               $d2[] = $item[0]['count'];
            }
        foreach($methodepaiment as $count){
                $countmethode[]= ($depensesRepository->statisticsbymethod($count));
            }
        $d3 = [];
    
        foreach ($countmethode as $item) {
                   $d3[] = $item[0]['count'];
        }    
        $depenses = $depensesRepository->findBy([], ['datedebut' => 'ASC']);
        $form = $this->createForm(SearchbytitleType::class);
        $form->handleRequest($req);
        if($form->isSubmitted()){
            $title = $form['titre_search']->getData();
            $depensesByAVG = $depensesRepository->depensesSearchBytitle($title);
            return $this->renderForm('depenses/index.html.twig', [
                'depenses' => $depensesByAVG,
                'formm'=>$form,
                'Budgets' => $budgetRepository->findlastbudget(),
                'sum'=>$sum2,
                'categorie'=>json_encode($categories),
            'categCount'=>json_encode($d2),
            'methodepaiment'=>json_encode($methodepaiment),
            'countmethode'=>json_encode($d3),
            ]);
        }
     
        return $this->render('depenses/index.html.twig',
        [
            'depenses'=>$depenses,
            'Budgets' => $budgetRepository->findlastbudget(),
            'sum'=>$sum2,
            'formm'=>$form->createView(),
            'rest'=>$rest,
            'categorie'=>json_encode($categories),
            'categCount'=>json_encode($d2),
            'methodepaiment'=>json_encode($methodepaiment),
            'countmethode'=>json_encode($d3),
        ]);
    }
    
   

}

