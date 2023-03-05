<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ClientRegistrationFormType;
use App\Form\RegistrationFormType;
use App\Security\AppAuthentificatorAuthenticator;
use App\Security\EmailVerifier;
use Symfony\Component\Mailer\MailerInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class ClientRegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;
    private $verifyEmailHelper;
    private $mailer;

    public function __construct(EmailVerifier $emailVerifier, VerifyEmailHelperInterface $helper, MailerInterface $mailer)
    {
        $this->emailVerifier = $emailVerifier;
        $this->verifyEmailHelper = $helper;
        $this->mailer = $mailer;
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher,SluggerInterface $slugger,/* UserAuthenticatorInterface $userAuthenticator, AppAuthentificatorAuthenticator $authenticator,*/ EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(ClientRegistrationFormType::class, $user);
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
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
                $signatureComponents = $this->verifyEmailHelper->generateSignature(
                    'app_verify_email',
                    $user->getId(),
                    $user->getEmail()
                );
                $email = new TemplatedEmail();
                $email->subject('Veuillez confirmer votre email');
                $email->from('shayma.ettaieb@esprit.tn');
                $email->to($user->getEmail());
                $email->htmlTemplate('registration/confirmation_email.html.twig');
                $email->context(['signedUrl' => $signatureComponents->getSignedUrl(),

                    'nom' => $user->getNom(),
                ]);
            $role = array("ROLE_USER");
            $user->setRoles($request->get('roles', $role));
            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('shayma.ettaieb@esprit.tn'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
                $this->mailer->send($email);
                $entityManager->persist($user);
                $entityManager->flush();
                return $this->redirectToRoute('home');
            // do anything else you need here, like send an email

        }
        }
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email', methods: ['GET', 'POST'])]
    public function verifyUserEmail(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $user->setIsVerified(true);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_register');

    }
}
