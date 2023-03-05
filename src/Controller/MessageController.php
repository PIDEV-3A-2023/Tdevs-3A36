<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use App\Repository\RendezvousRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\QueryBuilder;
use TCPDF;

#[Route('/message')]
class MessageController extends AbstractController
{

    
    #[Route('/listepage', name: 'app_message_index', methods: ['GET'])]
    public function inpage(Request $request, MessageRepository $messageRepository,RendezvousRepository $rendezvousRepository): Response
    {
        $page = $request->query->getInt('page', 1);
        $perPage = 3;
        $users = $messageRepository->findByPage($page, $perPage);

        return $this->render('message/index.html.twig',[
            'users' => $users,
            'paged' => $page,
            'perPage' => $perPage,
            'messages' => $messageRepository->findAll(),'page'=>'messages',
            'rendezvouses' => $rendezvousRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_message_new', methods: ['GET', 'POST'])]
    public function new(Request $request, MessageRepository $messageRepository, RendezvousRepository $rendezvousRepository): Response
    {
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message->setDate(new DateTime());
            $messageRepository->save($message, true);

            return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('message/new.html.twig', [
            'message' => $message,
            'form' => $form,
            'messages' => $messageRepository->findAll(),
            'page'=>'messages',
            'rendezvouses' => $rendezvousRepository->findAll(),'page'=>'rendezvous',
            
        ]);
    }

    #[Route('/{id}', name: 'app_message_show', methods: ['GET'])]
    public function show(Message $message, MessageRepository $messageRepository): Response
    {
        return $this->render('message/show.html.twig', [
            'message' => $message,
            'messages' => $messageRepository->findAll(),
            'page'=>'messages'
        ]);
    }

    #[Route('/edit/{id}', name: 'app_message_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Message $message, MessageRepository $messageRepository ,RendezvousRepository $rendezvousRepository): Response
    {
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message->setDate(new DateTime());
            $messageRepository->save($message, true);

            return $this->redirectToRoute('app_message_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('message/edit.html.twig', [
            'message' => $message,
            'form' => $form,
            'messages' => $messageRepository->findAll(),
            'page'=>'messages',
            'rendezvouses' => $rendezvousRepository->findAll(),'page'=>'rendezvous'
        ]);
    }

    #[Route('/del/{id}', name: 'app_message_delete', methods: ['POST'])]
    public function delete(Request $request, Message $message, MessageRepository $messageRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$message->getId(), $request->request->get('_token'))) {
            $messageRepository->remove($message, true);
        }

        return $this->redirectToRoute('app_message_index', [], Response::HTTP_SEE_OTHER);
    }



    #[Route('/list/pdf', name: 'generate_pdf', methods: ['GET'])]
    public function generatePdfAction(MessageRepository $messageRepository)
    {
        $messages = $messageRepository->findAll();
    // créer une instance de TCPDF
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // définir les informations du document
    $pdf->SetCreator('Mon application Symfony');
    $pdf->SetAuthor('Moi');
    $pdf->SetTitle('Liste des clubs');
    $pdf->SetSubject('Liste des clubs');

    // ajouter une page
    $pdf->AddPage();

    // créer le contenu du PDF
    $html = $this->renderView('message/list.html.twig', [
        'messages' => $messages,
    ]);

    // écrire le contenu dans le PDF
    $pdf->writeHTML($html, true, false, true, false, '');

    // sauvegarder le PDF sur le bureau
         $projectDir = $this->getParameter('kernel.project_dir');

     // Use the project directory path to define the path to the PDF file
     $pdfPath = $projectDir . '/public/pdfs/liste_messages.pdf';
    $pdf->Output($pdfPath, 'F');

    // renvoyer une réponse HTTP
    $response = new Response();
    $disposition = $response->headers->makeDisposition(
        ResponseHeaderBag::DISPOSITION_ATTACHMENT,
        'liste_messages.pdf'
    );
    $response->headers->set('Content-Type', 'application/pdf');
    $response->headers->set('Content-Disposition', $disposition);
    $response->setContent(file_get_contents($pdfPath));

    return $response;
}
    
    
}
