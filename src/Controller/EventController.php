<?php

namespace App\Controller;

use App\Form\CreateEventFormType;
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
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @return Response
     */
    public function createEvent(EntityManagerInterface $entityManager, Request $request): Response
    {
        $event = new Event();

        $event->setStartingDateTime(new \DateTime('now'));

        $inscriptionDeadLine = new \DateTime('now');
        $inscriptionDeadLine->modify("+1 day");

        $locationRepo = $this->getDoctrine()->getRepository(Location::class);
        $event->setInscriptionDeadLine($inscriptionDeadLine);
        $sortieForm = $this->createForm(CreateEventFormType::class, $event);
        $sortieForm->handleRequest($request);

        $user = $this->getUser();
        $event->setOrganizer($user);
        $site = $user->getSite();
        $event->setSite($site);

        $entityManager->persist($event);
        $entityManager->flush();
        $this->addFlash("messageSuccess", "Votre sortie a bien été enregistrée");

        return $this->render('sortie/createEvent.html.twig', ['sortieForm'=>$sortieForm->createView(), 'location'=>$locationRepo]);
    }
}