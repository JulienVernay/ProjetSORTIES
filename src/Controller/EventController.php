<?php

namespace App\Controller;

use App\Form\CancelEventFormType;
use App\Form\CreateEventFormType;
use App\Form\LocationFormType;
use App\Repository\CityRepository;
use App\Repository\EventRepository;
use App\Repository\StateRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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

            $env = $request->request->get('envoyer');
            $enregister = 'enregistrer';
            $publier = 'publier';

            if ($env === $publier) {
                $etat = $entityManager->getRepository('App:State')->findOneBy(['id'=>2]);
                $event->setStatus($etat);
                $this->addFlash('success', 'Votre sortie a été publiée avec succès !');


            } elseif ($env === $enregister) {
                $etat = $entityManager->getRepository('App:State')->findOneBy(['id'=>1]);
                $event->setStatus($etat);
                $this->addFlash('success', 'Votre sortie a été enregistrée avec succès !');

            }

            $entityManager->persist($event);
            $entityManager->flush();
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

    /**
     * @Route(name="event-details", methods={"GET","POST"}, path="detail/{id}", requirements={"id": "\d+"})
     *
     */
    public function eventDetails($id, EventRepository $eventRepo)
    {
        $event =$eventRepo->find($id);

        return $this->render('sortie/detailSortie.html.twig', ['event'=>$event]);
    }

    /**
     * @Route(name="inscriptionEvent",path="inscriptionEvent/{id}", requirements={"id": "\d+"} ,methods={"POST","GET"})
     *
     */
    public function inscriptionEvent($id, EventRepository $eventRepo, EntityManagerInterface $entityManager)
    {
        $event = $eventRepo->find($id);
        $participant = $this->getUser();

        if ($event ->getInscriptionDeadLine() > new \DateTime('now') &&
        $event->getNbMaxRegistration() > $event->getRegisteredMembers()->count()) {

            $event -> addRegisteredMember($participant);
            $entityManager -> persist($event);
            $entityManager ->flush();

            $this->addFlash('success', 'Vous êtes inscrit à la sortie !');
        }
        else {

            $this->addFlash('warning', 'Vous ne pouvez pas vous inscrire à la sortie !');
        }

        return $this->render('sortie/detailSortie.html.twig', ['event'=>$event]);
    }

    /**
     * @Route(name="deinscriptionEvent",path="deinscriptionEvent/{id}", requirements={"id": "\d+"} ,methods={"POST","GET"})
     *
     */
    public function deinscriptionEvent($id, EventRepository $eventRepo, EntityManagerInterface $entityManager)
    {
        $event = $eventRepo->find($id);
        $participant = $this->getUser();

        if (!is_null($event) ) {

            $event -> removeRegisteredMember($participant);
            $entityManager -> persist($event);
            $entityManager ->flush();

            $this->addFlash('success', 'Vous vous êtes désinscrit avec succès !');
        }
        else {

            $this->addFlash('warning', 'Une erreur s'.'est produite');
        }

        return $this->render('sortie/detailSortie.html.twig', ['event'=>$event]);
    }


    /**
     * @Route("/cancelEvent/{id}", name="cancelEvent")
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function cancelEvent(EntityManagerInterface $entityManager, Request $request, $id): Response
    {
        $event = $this->getDoctrine()->getRepository(Event::class)->find($id);
        if(empty($event)){
            throw $this->createNotFoundException('Cette sortie n\'existe pas');
        }

        $annulationForm = $this->createForm(CancelEventFormType::class, $event);
        $annulationForm->handleRequest($request);

        if($annulationForm->isSubmitted() && $annulationForm->isValid()){
            $annulation = $annulationForm['annulationForm']->getData();
            $status = $this->getDoctrine()->getRepository(State::class)
                ->findOneBy(['label'=>'Annulee']);
            $event->setStatus($status);
            $event->setMotifCancel($annulation);
            $entityManager->persist($event);
            $entityManager->flush();

            $this->addFlash('success', 'La sortie a bien été annulée');
            return $this->redirectToRoute('index');
        }

        return $this->render('sortie/cancelEvent.html.twig', ['annulationForm' => $annulationForm->createView()]);
    }
}