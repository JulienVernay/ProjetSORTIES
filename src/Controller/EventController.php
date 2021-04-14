<?php

namespace App\Controller;

use App\Form\CancelEventFormType;
use App\Form\CreateEventFormType;
use App\Form\ModifyEventType;
use App\Repository\CityRepository;
use App\Repository\EventRepository;
use App\Repository\StateRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Event;
use App\Entity\Location;
use App\Entity\State;

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
        $locationRepo = $this->getDoctrine()->getRepository(Location::class);

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

    /**
     * @Route(name="event-details", methods={"GET","POST"}, path="detail/{id}", requirements={"id": "\d+"})
     *
     */
    public function eventDetails($id, EventRepository $eventRepo): Response
    {
        $event =$eventRepo->find($id);

        return $this->render('sortie/detailSortie.html.twig', ['event'=>$event]);
    }

    /**
     * @Route(name="inscriptionEvent",path="inscriptionEvent/{id}", requirements={"id": "\d+"} ,methods={"POST","GET"})
     *
     */
    public function inscriptionEvent($id, EventRepository $eventRepo, EntityManagerInterface $entityManager): Response
    {
        $event = $eventRepo->find($id);
        $participant = $this->getUser();

        if ($event ->getInscriptionDeadLine() > new \DateTime('now') &&
        $event->getNbMaxRegistration() > $event->getRegisteredMembers()->count()) {

            $event -> addRegisteredMember($participant);
            
            if ($event->getNbMaxRegistration() == $event->getRegisteredMembers()->count()){
                $etat = $entityManager->getRepository('App:State')->findOneBy(['id'=>3]);
                $event ->setStatus($etat);
            }
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
     * @Route(name="desinscriptionEvent",path="desinscriptionEvent/{id}", requirements={"id": "\d+"} ,methods={"POST","GET"})
     *
     */
    public function desinscriptionEvent($id, EventRepository $eventRepo, EntityManagerInterface $entityManager): Response
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
            $annulation = $annulationForm['motifCancel']->getData();
            $status = $this->getDoctrine()->getRepository(State::class)
                ->findOneBy(['label'=>'Annulee']);
            $event->setStatus($status);
            $event->setMotifCancel($annulation);
            $entityManager->persist($event);
            $entityManager->flush();

            $this->addFlash('success', 'La sortie a bien été annulée');
            return $this->redirectToRoute('index');
        }

        return $this->render('sortie/cancelEvent.html.twig', ['annulationForm' => $annulationForm->createView(), 'event' => $event]);
    }

    /**
     * @Route("/modifyEvent/{id}", name="modify_event", methods={"GET","POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function modifyEvent(Request $request,EntityManagerInterface $entityManager): Response
    {
        $id = $request->get('id');
        $event = $this->getDoctrine()->getRepository(Event::class)->find($id);
        $locationRepo = $this->getDoctrine()->getRepository(Location::class)->find($event->getLocation()->getId());

        $eventForm = $this->createForm(CreateEventFormType::class, $event);
        $eventForm->handleRequest($request);

        if ($eventForm->isSubmitted() && $eventForm->isValid()) {

            $entityManager->persist($event);
            $entityManager->flush();
            $this->addFlash('success', "L'évenement a été modifié avec succès");
            return $this->render('sortie/detailSortie.html.twig', ['event'=>$event]);
        }

        return $this->render('sortie/modifyEvent.html.twig', ['sortieForm' => $eventForm->createView(), 'location' => $locationRepo, 'event' => $event]);

    }

}