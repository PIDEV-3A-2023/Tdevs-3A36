<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Transport\Serialization\Serializer;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class UserApiController extends AbstractController
{
    #[Route('/user/api', name: 'app_user_api')]
    public function register(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $email = $request->query->get("email");
        $nom = $request->query->get("nom");
        $prenom = $request->query->get("prenom");
        $password = $request->query->get("password");

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return new Response("Please enter a valid email");
        }
        $user = new User();
        $user->setNom($nom);
        $user->setPrenom($prenom);
        $user->setEmail($email);
        $user->setPassword(
            $encoder->encodePassword(
                $user,
                $password
            )
        );
        $user->setIsVerified(true);
        $role = array("ROLE_USER");
        $user->setRoles($request->get('roles', $role));

        try {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return new JsonResponse("account is created", 200);
        } catch (\Exception $exception){
            return new Response("exeption".$exception->getMessage());
        }
    }

    #[Route('/agent/api', name: 'app_agent_api')]
    public function registerAgent(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $email = $request->query->get("email");
        $nom = $request->query->get("nom");
        $prenom = $request->query->get("prenom");
        $password = $request->query->get("password");

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return new Response("Please enter a valid email");
        }
        $user = new User();
        $user->setNom($nom);
        $user->setPrenom($prenom);
        $user->setEmail($email);
        $user->setPassword(
            $encoder->encodePassword(
                $user,
                $password
            )
        );
        $user->setIsVerified(true);
        $role = array("ROLE_AGENT");
        $user->setRoles($request->get('roles', $role));

        try {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return new JsonResponse("account is created", 200);
        } catch (\Exception $exception){
            return new Response("exeption".$exception->getMessage());
        }
    }

    #[Route('/login/api', name: 'app_login_api')]
    public function login(Request $request): Response
    {
        $email = $request->query->get("email");
        $password = $request->query->get("password");

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);

        //$validPassword = $userPasswordHasher->isPasswordValid($user, $password);
        if ($user) {
            if (password_verify($password,$user->getPassword())) {
                $normalizer = new ObjectNormalizer();
                $serializer = new \Symfony\Component\Serializer\Serializer(array(new DateTimeNormalizer(), $normalizer));
                $formatted = $serializer->normalize($user);
                return new JsonResponse($formatted);
            }
            else{
                return new Response("password not found");
            }
        }
        else{
            return new Response("user not found");
        }
    }
}

