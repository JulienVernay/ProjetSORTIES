<?php

namespace App\Controller;

use App\Form\CreateEventFormType;
use App\Form\LocationFormType;
use App\Repository\CityRepository;
use App\Repository\EventRepository;
use App\Repository\StateRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Campus;
use App\Entity\City;
use App\Entity\Event;
use App\Entity\Location;
use App\Entity\State;
use App\Entity\User;
use Symfony\Component\Validator\Constraints\DateTime;

class EventController extends AbstractController
{
    /**
     * @Route("/createEvent", name="event", methods={"GET","POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param UserRepository $userRepository
     * @param StateRepository $stateRepository
     * @param CityRepository $cityRepository
     * @return Response
     */
    public function createEvent(Request $request,EntityManagerInterface $entityManager, UserRepository $userRepository,
                             StateRepository $stateRepository, CityRepository $cityRepository): Response
    {
        $event = new Event();

        $event->setStartingDateTime(new \DateTime('now'));

        $inscriptionDeadLine = new \DateTime('now');
        $inscriptionDeadLine->modify("+1 day");

        $locationRepo = $this->getDoctrine()->getRepository(Location::class);
        $event->setInscriptionDeadLine($inscriptionDeadLine);
        $sortieForm = $this->createForm(CreateEventFormType::class, $event);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {

            $user = $this->getUser();
            $event->setOrganizer($user);


            $entityManager->persist($event);
            $entityManager->flush();
            $this->addFlash("messageSuccess", "Votre sortie a bien été enregistrée");
            return $this->redirectToRoute('index');
        }

        return $this->render('sortie/createEvent.html.twig', ['sortieForm' => $sortieForm->createView(), 'location' => $locationRepo]);

    }

    // Ajouter un lieu
    public function eventAddLocation(EntityManagerInterface $entityManager, StateRepository $stateRepository,EventRepository $eventRepository, Request $request): Response
    {
        $location = new Location();

        $formLocation = $this->createForm(LieuType::class, $location);
        $formLocation->handleRequest($request);

        if($formLocation->isSubmitted() && $formLocation->isValid()){
            $location = $formLocation->getData();
            $entityManager->persist($location);
            $entityManager->flush();
        }

        return $this->render('sorties/createEvent.html.twig', [
            "LocationFormType" => $formLocation->createView(),
        ]);
    }
}