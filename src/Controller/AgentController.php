<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('admin/viewagent')]
class AgentController extends AbstractController
{
    #[Route('/', name: 'app_agent_view', methods: ['GET'])]
    public function index(Request $request, UserRepository $userRepository): Response
    {
        $page = $request->query->getInt('page', 1);
        $perPage = 4;
        $users = $userRepository->findByPage($page, $perPage);

        return $this->render('agent/index.html.twig',[
            'users' => $users,
            'page' => $page,
            'perPage' => $perPage,
        ]);
    }

    #[Route('/new', name: 'app_agent_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserRepository $userRepository): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_agent_view', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('agent/new.html.twig', [
            'user' => $user,
            'registrationForm' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_agent_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('agent/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_agent_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user,SluggerInterface $slugger, UserRepository $userRepository): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $eventImage */
            $eventImage = $form->get('image')->getData();
            if ($eventImage) {
                $originalFilename = pathinfo($eventImage->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $eventImage->guessExtension();

                // Move the file to the directory where images are stored
                try {
                    $eventImage->move(
                        $this->getParameter('image_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'eventImage' property to store the image file name
                // instead of its contents
                $user->setImage($newFilename);
                $userRepository->save($user, true);
            }


            return $this->redirectToRoute('app_agent_view', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('agent/edit.html.twig', [
            'user' => $user,
            'registrationForm' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_agent_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('app_agent_view', [], Response::HTTP_SEE_OTHER);
    }
}
