<?php

namespace App\Controller;

use App\Entity\Location;
use App\Form\LocationFormType;
use App\Repository\CityRepository;
use App\Repository\LocationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LocationController extends AbstractController
{
    /**
     * @Route("/lieu", name="lieu")
     * @IsGranted("ROLE_ADMIN", statusCode=404, message="Cet accès est réservé aux admin")
     * @param Request $request
     * @param CityRepository $cityRepo
     * @param LocationRepository $locationRepo
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function addLocation(Request $request, CityRepository $cityRepo, LocationRepository $locationRepo, EntityManagerInterface $entityManager): Response
    {
        $location = new Location();

        $formLocation = $this->createForm(LocationFormType::class, $location);
        $formLocation->handleRequest($request);
        $locationRepo = $this->getDoctrine()->getRepository(Location::class);

        if($formLocation->isSubmitted() && $formLocation->isValid()){
            $location = $formLocation->getData();

            $location->setVille($cityRepo->findOneBy([ 'name' => $cityRepo ])->getId());
            $entityManager->persist($location);
            $entityManager->flush();
        }

        return $this->render('sortie/selectLocation.html.twig', ['formLocation'=>$formLocation->createView(), 'location'=>$locationRepo]);
    }
}