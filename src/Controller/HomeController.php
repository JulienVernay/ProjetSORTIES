<?php


namespace App\Controller;


use App\Entity\Event;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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

        return $this->render('home/home.html.twig',[
            'events' => $events
        ]);

    }
}