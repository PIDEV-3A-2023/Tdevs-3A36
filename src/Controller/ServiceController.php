<?php

namespace App\Controller;

use App\Entity\Service;
use App\Entity\Speciality;
use App\Form\ServiceType;
use App\Form\AbonnementType;

use App\Form\SpecialityType;
use App\Repository\AccountRepository;
use App\Repository\ServiceRepository;
//use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Routing\Annotation\Route;
class ServiceController extends AbstractController
{


    #[Route('service/', name: 'service', methods: ['GET'])]
    public function index(ServiceRepository $serviceRepository): Response
    {
        return $this->render('service/index.html.twig', [
            'service' => $serviceRepository->findAll(),
        ]);
    }

    #[Route('addService', name: 'addService')]
    public function add(ManagerRegistry $doctrine, Request $req): Response {
        $em = $doctrine->getManager();
        $service = new Service();
        $form = $this->createForm(ServiceType::class,$service);
        $form->handleRequest($req);
        if($form->isSubmitted()){
            $em->persist($service);
            $em->flush();
            return $this->redirectToRoute('service');
        }

        return $this->renderForm('/service/new.html.twig',['form'=>$form]);
    }

    #[Route('deleteSercvie/{id}', name: 'deleteSercvie')]
    public function deleteSpeciality(ManagerRegistry $doctrine, $id): Response
    {
        $em= $doctrine->getManager();
        $S= $doctrine->getRepository(Service::class)->find($id);
        $em->remove($S);
        $em->flush();
        return $this->redirectToRoute('service');
    }
    #[Route('updateService/{id}', name: 'updateService')]
    public function updateService(ManagerRegistry $doctrine, $id, Request $req): Response {
        $em = $doctrine->getManager();
        $service = $doctrine->getRepository(Service::class)->find($id);
        $form = $this->createForm(ServiceType::class,$service);
        $form->handleRequest($req);
        if($form->isSubmitted()){
            $em->persist($service);
            $em->flush();
            return $this->redirectToRoute('service');
        }
        return $this->renderForm('service/new.html.twig',['form'=>$form]);

    }
}
