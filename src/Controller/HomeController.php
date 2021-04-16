<?php


namespace App\Controller;


use App\Entity\State;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route(path="", name="index", methods={"GET"})
     */
    public function index(EntityManagerInterface $em): Response
    {
        if (is_null($this->getUser()))
        {
            return $this->redirectToRoute('app_login');
        }
        $events = $this->getDoctrine()->getRepository('App:Event')->findAll();
        $cloturee = $this->getDoctrine()->getRepository(State::class)->findOneBy(['label'=>'Cloturee']);
        foreach ($events as $event){
            if ($event->getNbMaxRegistration() == $event->getRegisteredMembers()->count() &&
            $event->getStatus()->getId() != 6) {
                $event->setStatus($cloturee);
            } else if($event->getInscriptionDeadLine() < new \DateTime('now')){
                $event->setStatus($cloturee);
            }
        }

        return $this->render('home/home.html.twig',[
            'events' => $events
        ]);

    }
}