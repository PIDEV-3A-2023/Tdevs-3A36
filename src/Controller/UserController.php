<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AgentRegistrationFormType;
use App\Form\EditPasswordType;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Service\BanningService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('admin/', name: 'app_user_index', methods: ['GET'])]
    public function index(Request $request, UserRepository $userRepository): Response
    {
        $page = $request->query->getInt('page', 1);
        $perPage = 4;
        $users = $userRepository->findByPage($page, $perPage);

        return $this->render('user/show_users.html.twig', [
            'users' => $users,
            'nbUsers' => $userRepository->countUsersCreatedLast7Days(),
            'nbAgent' => $userRepository->findAllAgents(),
            'nbuserbanned' => $userRepository->countBannedUsers(),
            'page' => $page,
            'perPage' => $perPage,
        ]);
    }

    #[Route('admin/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserRepository $userRepository): Response
    {
        $user = new User();
        $form = $this->createForm(AgentRegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'registrationForm' => $form,
        ]);
    }

    #[Route('admin/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('home/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user,SluggerInterface $slugger,  UserRepository $userRepository): Response
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


            return $this->redirectToRoute('profile', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'registrationForm' => $form,
        ]);
    }

    #[Route('admin/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/user/{id}/ban', name: 'ban_user')]
    public function banUser(BanningService $userService, $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        $userService->banUser($user);

        return $this->redirectToRoute('app_user_index');
    }

    #[Route('home/{id}/editpass', name: 'app_user_editpass', methods: ['GET', 'POST'])]
    public function editpass(Request $request, User $user, UserPasswordHasherInterface $userPasswordHasher ,UserRepository $userRepository): Response
    {
        $form = $this->createForm(EditPasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $userRepository->save($user, true);
            return $this->redirectToRoute('profile', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/editPassword.html.twig', [
            'user' => $user,
            'registrationForm' => $form,
        ]);
    }

}
