<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Form\GestionCampusFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CampusController extends AbstractController
{
    /**
     * @Route("/campus", name="gestion_campus")
     */
    public function list(Request $request, EntityManagerInterface $em)
    {
        $campusRepo = $this->getDoctrine()->getRepository(Campus::class);
        $campus = $campusRepo->findAll();


        return $this->render('gestion/gestionCampus.html.twig', ['campus'=>$campus]);
    }
}
