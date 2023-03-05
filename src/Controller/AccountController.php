<?php

namespace App\Controller;

use App\Entity\Account;
use App\Form\AccountType;
use App\Form\SearchAccountType;
use App\Repository\AccountRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Doctrine\ORM\Tools\Pagination\Paginator;



#[Route('/account')]
class AccountController extends AbstractController
{
    
    
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        
        $this->mailer = $mailer;
    }
    #[Route('/', name: 'app_account_index', methods: ['GET', 'POST'])]
    public function index(AccountRepository $accountRepository,Request $request, Request $request2): Response
    {
        $form = $this->createForm(SearchAccountType::class);
        $search = $form->handleRequest($request2);

        ////////////////////////////////////////
        ////////////////////////////////////////
        ////////////////////////////////////////
        $em = $this->getDoctrine()->getManager();
    $query = $em->getRepository(Account::class)->createQueryBuilder('u')->getQuery();

    $accounts  = new Paginator($query);
    $currentPage = $request->query->getInt('page', 1);
    $itemsPerPage = 5;
    $accounts
    ->getQuery()
    ->setFirstResult($itemsPerPage * ($currentPage - 1))
    ->setMaxResults($itemsPerPage);
    $totalItems = count($accounts);
    $pagesCount = ceil($totalItems / $itemsPerPage);
    if ($form->isSubmitted() && $form->isValid()) {
        $accounts = $accountRepository->search($search->get('mots')->getData());
            $currentPage = $request->query->getInt('page', 1);
            $totalItems = count($accounts);
            $pagesCount = ceil($totalItems / $itemsPerPage);
            return $this->render('account/index.html.twig', [
                'form' => $form->createView(),
                'accounts' => $accounts,
                'currentPage' => $currentPage,
                'pagesCount' => $pagesCount,    
            ]);
    }

        return $this->render('account/index.html.twig', [
            'form' => $form->createView(),
            'accounts' => $accounts,
            'currentPage' => $currentPage,
            'pagesCount' => $pagesCount,    
        ]);
    }

    #[Route('/new', name: 'app_account_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AccountRepository $accountRepository, SluggerInterface $slugger): Response
    {
        $account = new Account();
        $form = $this->createForm(AccountType::class, $account);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $brochureFile */
            $brochureFile = $form->get('brochure')->getData();

            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();

                try {
                    $brochureFile->move(
                        $this->getParameter('image_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }

                $account->setBrochureFilename($newFilename);
            }
            $account->setEtat(null);
            $accountRepository->save($account, true);
            return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('account/new.html.twig', [
            'account' => $account,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_account_show', methods: ['GET'])]
    public function show(Account $account): Response
    {
        return $this->render('account/etat.html.twig', [
            'account' => $account,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_account_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Account $account, AccountRepository $accountRepository): Response
    {
        $form = $this->createForm(AccountType::class, $account);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $accountRepository->save($account, true);

            return $this->redirectToRoute('app_account_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('account/edit.html.twig', [
            'account' => $account,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_account_delete', methods: ['POST'])]
    public function delete(Request $request, Account $account, AccountRepository $accountRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$account->getId(), $request->request->get('_token'))) {
            $accountRepository->remove($account, true);
        }

        return $this->redirectToRoute('app_account_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/refuse/{id}', name: 'account_refuse', methods: ['GET', 'POST'])]
    public function refuse(Account $account, AccountRepository $accountRepository, Request $request, Request $request2): Response
    {
        $form = $this->createForm(SearchAccountType::class);
        $search = $form->handleRequest($request2);
        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository(Account::class)->createQueryBuilder('u')->getQuery();
    
        $accounts  = new Paginator($query);
        $currentPage = $request->query->getInt('page', 1);
        $itemsPerPage = 5;
        $accounts
        ->getQuery()
        ->setFirstResult($itemsPerPage * ($currentPage - 1))
        ->setMaxResults($itemsPerPage);
        $totalItems = count($accounts);
        $pagesCount = ceil($totalItems / $itemsPerPage);
        if ($account->isEtat() == null) {
            $account->setEtat(0);
            $accountRepository->save($account, true);
            $accounts = $accountRepository->search($search->get('mots')->getData());
            $currentPage = $request->query->getInt('page', 1);
            $totalItems = count($accounts);
            $pagesCount = ceil($totalItems / $itemsPerPage);
            return $this->render('account/index.html.twig', [
                'form' => $form->createView(),
                'accounts' => $accounts,
                'currentPage' => $currentPage,
                'pagesCount' => $pagesCount,    
            ]);
        } 
        return $this->render('account/index.html.twig', [
            'form' => $form->createView(),
            'accounts' => $accounts,
            'currentPage' => $currentPage,
            'pagesCount' => $pagesCount,    
        ]);
    }
    #[Route('/validate/{id}', name: 'account_validate', methods: ['GET', 'POST'])]
    public function validate(Account $account, AccountRepository $accountRepository, Request $request, Request $request2): Response
    {
        $form = $this->createForm(SearchAccountType::class);
        $search = $form->handleRequest($request2);
        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository(Account::class)->createQueryBuilder('u')->getQuery();
    
        $accounts  = new Paginator($query);
        $currentPage = $request->query->getInt('page', 1);
        $itemsPerPage = 5;
        $accounts
        ->getQuery()
        ->setFirstResult($itemsPerPage * ($currentPage - 1))
        ->setMaxResults($itemsPerPage);
        $totalItems = count($accounts);
        $pagesCount = ceil($totalItems / $itemsPerPage);
        
        if  ($account->isEtat() == 0){
            $account->setEtat(1);
            $email = new TemplatedEmail();
            $email->subject('Votre compte bancaire est bien vérifie');
            $email->from('bankin.notifier@gmail.com');
            $email->to($account->getEmail());
            $email->htmlTemplate('mailing/email.html.twig');
            $email->context(['solde' => $account->getSolde()]);
            $this->mailer->send($email);

            $accountRepository->save($account, true);
            $accounts = $accountRepository->search($search->get('mots')->getData());
            $currentPage = $request->query->getInt('page', 1);
            $totalItems = count($accounts);
            $pagesCount = ceil($totalItems / $itemsPerPage);
            return $this->render('account/index.html.twig', [
                'form' => $form->createView(),
                'accounts' => $accounts,
                'currentPage' => $currentPage,
                'pagesCount' => $pagesCount,    
            ]);
        }
        return $this->render('account/index.html.twig', [
            'form' => $form->createView(),
            'accounts' => $accounts,
            'currentPage' => $currentPage,
            'pagesCount' => $pagesCount,    
        ]);
    }
    #[Route('/solde/{solde}', name: 'solde', methods: ['GET', 'POST'])]
    public function solde($solde): Response
    {
       
            //$solde = $request->query->get('solde');

            //$accountRepository->save($account, true);
            
            return $this->render('account/solde.html.twig', [
                'solde' => $solde,
            ]);
        
    }

}
