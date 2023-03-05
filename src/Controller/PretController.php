<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\Etatpret;
use App\Entity\Pret;
use App\Form\AccountType;
use App\Form\PretType;
use App\Form\SearchPretType;
use App\Repository\AccountRepository;
use App\Repository\EtatpretRepository;
use App\Repository\PretRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/pret')]
class PretController extends AbstractController
{
    #[Route('/account', name: 'app_pret_account', methods: ['GET', 'POST'])]
    public function selectAccount(Request $request): Response
    {
        $prets = $this->getDoctrine()
            ->getRepository(Account::class)
            ->findAll();

        $form = $this->createFormBuilder()
            ->add('account', ChoiceType::class, [
                'choices' => $prets,
                'choice_label' => 'NomComplet',
                'placeholder' => 'Choose an account',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Select',
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $accountId = $form->get('account')->getData()->getId();
            return $this->redirectToRoute('app_pret_desc', ['id' => $accountId]);
        }

        return $this->render('pret/account.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/', name: 'app_pret_desc', methods: ['GET'])]
    public function desc(Request $request, AccountRepository $accountRepository, PretRepository $pretRepository): Response
    {
        $pret = new Pret();
        $accountId = $request->get('id');

        $account = $accountRepository->find($accountId);
       
        

        


        return $this->render('pret/firstpage.html.twig', ['id' => $accountId, 'account' => $account,]);
    }

    #[Route('/new/{id}', name: 'app_pret_new', methods: ['GET', 'POST'])]
    public function new(EntityManagerInterface $entityManager, Request $request, PretRepository $pretRepository, AccountRepository $accountRepository, EtatpretRepository $etatrepo): Response
    {
        $pret = new Pret();
        $form = $this->createForm(PretType::class, $pret);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $accountId = $request->get('id');
            $account = $accountRepository->find($accountId);
            $pret->setAccount($account);
            $pretRepository->save($pret, true);
            $pret->setDatePret(new \DateTimeImmutable());
            $etat = new Etatpret();
            $etat->setEtat("En attente");
            $etat->setPret($pret);
            $etatrepo->save($etat, true);
            $this->addFlash('success', 'The entity has been saved.');



            return $this->redirectToRoute('app_pret_desc', ['id' => $accountId], Response::HTTP_SEE_OTHER);
        }


        return $this->renderForm('pret/new.html.twig', [
            'pret' => $pret,
            'form' => $form,

        ]);
    }


    #[Route('/list', name: 'app_pret_list', methods: ['GET', 'POST'])]
    public function index(Request $request2, EntityManagerInterface $entityManager, PretRepository $pretRepository, Request $request): Response
    {
        $form = $this->createForm(SearchPretType::class);
        $search = $form->handleRequest($request2);

        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository(Pret::class)
            ->createQueryBuilder('u');


        $prets  = new Paginator($query);
        $currentPage = $request->query->getInt('page', 1);
        //itemsPerPage kadeh min element f page
        //$itemsPerPage = 1;
        $itemsPerPage = 10;
        $prets


            ->getQuery()
            ->setFirstResult($itemsPerPage * ($currentPage - 1))
            ->setMaxResults($itemsPerPage);
        $totalItems = count($prets);
        $pagesCount = ceil($totalItems / $itemsPerPage);
        if ($form->isSubmitted() && $form->isValid()) {
            $prets = $pretRepository->search($search->get('mots')->getData());
            $currentPage = $request->query->getInt('page', 1);
            $totalItems = count($prets);
            $pagesCount = ceil($totalItems / $itemsPerPage);
            return $this->render('pret/index.html.twig', [
                'form' => $form->createView(),
                'prets' => $prets,
                'currentPage' => $currentPage,
                'pagesCount' => $pagesCount,
            ]);
        }
        return $this->render('pret/index.html.twig', [
            'form' => $form->createView(),
            'prets' => $prets,
            'currentPage' => $currentPage,
            'pagesCount' => $pagesCount,

        ]);
    }
    #[Route('/{id}', name: 'app_pret_show', methods: ['GET', 'POST'])]
    public function show( Account $account, PretRepository $pretRepository): Response
    {
    
        $prets = $pretRepository->findBy(['account' => $account]);

        return $this->render('pret/show.html.twig', [
            'account' => $account,
            'prets' => $prets,
        ]);
    }

    #[Route('/cons/{id}', name: 'app_pret_consulte', methods: ['GET'])]
    public function consulte(Pret $pret): Response
    {
        return $this->render('pret/cons.html.twig', [
            'pret' => $pret,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_pret_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Pret $pret, PretRepository $pretRepository): Response
    {
        $form = $this->createForm(PretType::class, $pret);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pretRepository->save($pret, true);

            return $this->redirectToRoute('app_pret_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('pret/edit.html.twig', [
            'pret' => $pret,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_pret_delete', methods: ['POST'])]
    public function delete(Request $request, Pret $pret, PretRepository $pretRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $pret->getId(), $request->request->get('_token'))) {
            $pretRepository->remove($pret, true);
        }

        return $this->redirectToRoute('app_pret_list', [], Response::HTTP_SEE_OTHER);
    }
}
