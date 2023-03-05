<?php

namespace App\Controller;

use App\Entity\Rendezvous;
use App\Form\Rendezvous1Type;
use App\Repository\MessageRepository;
use App\Repository\RendezvousRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;

#[Route('/rendezvous')]
class RendezvousController extends AbstractController
{
    #[Route('/list', name: 'app_rendezvous_index', methods: ['GET'])]
    public function index(RendezvousRepository $rendezvousRepository, MessageRepository $messageRepository): Response
    {
       // dd($rendezvousRepository->findAll());
        return $this->render('rendezvous/index.html.twig', [
            'rendezvouses' => $rendezvousRepository->findAll(),'page'=>'rendezvous',
            'messages' => $messageRepository->findAll()            
        ]);
    }

    #[Route('/add', name: 'app_rendezvous_new', methods: ['GET', 'POST'])]
    public function new(Request $request, RendezvousRepository $rendezvousRepository, MessageRepository $messageRepository,MailerInterface $mailer): Response
    {
        $rendezvou = new Rendezvous();
        $form = $this->createForm(Rendezvous1Type::class, $rendezvou);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $rendezvousRepository->save($rendezvou, true);




$email = (new Email())
    ->from('pidev.manef3@gmail.com')
    ->to('pidev.manef3@gmail.com')
    //->cc('cc@example.com')
    //->bcc('bcc@example.com')
    //->replyTo('fabien@example.com')
    //->priority(Email::PRIORITY_HIGH)
    ->subject('Time for Symfony Mailer!')
    ->text('Sending emails is fun again!')
    ->html('<p>See Twig integration for better HTML integration!</p>');

$mailer->send($email);



            return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
        }
        

        return $this->renderForm('rendezvous/new.html.twig', [
            'rendezvou' => $rendezvou,
            'form' => $form,
            'messages' => $messageRepository->findAll(),
            'page'=>'rendezvous'
        ]);
    }

    #[Route('/', name: 'app_rendezvous_show', methods: ['GET'])]
    public function show(Rendezvous $rendezvou): Response
    {
        return $this->render('rendezvous/show.html.twig', [
            'rendezvou' => $rendezvou,
        ]);
    }

    #[Route('/edit/{id}', name: 'app_rendezvous_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Rendezvous $rendezvou, RendezvousRepository $rendezvousRepository, MessageRepository $messageRepository): Response
    {
        $form = $this->createForm(Rendezvous1Type::class, $rendezvou);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $rendezvousRepository->save($rendezvou, true);

            return $this->redirectToRoute('app_rendezvous_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('rendezvous/edit.html.twig', [
            'rendezvou' => $rendezvou,
            'form' => $form,
            'messages' => $messageRepository->findAll(),
            'page'=>'rendezvous'
        ]);
    }

    #[Route('/{id}', name: 'app_rendezvous_delete', methods: ['POST'])]
    public function delete(Request $request, Rendezvous $rendezvou, RendezvousRepository $rendezvousRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$rendezvou->getId(), $request->request->get('_token'))) {
            $rendezvousRepository->remove($rendezvou, true);
        }

        return $this->redirectToRoute('app_rendezvous_index', [], Response::HTTP_SEE_OTHER);
    }
}
