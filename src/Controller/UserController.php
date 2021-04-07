<?php

namespace App\Controller;

use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\User;

/**
 * Class UserController
 * @package App\Controller
 */
class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user_profile")
     */
    public function index(): Response
    {
        return $this->render('user/modifyUser.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    /**
     * @Route ("/user/modify", name="user_modify")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function modifyUser(Request $request, EntityManagerInterface $em): Response
    {
        $id = $request->get('id');
        $user = $em->getRepository('App:User')->find($id);
        $form = $this->createForm(User::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {

            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Profil modifié');
            return $this->redirectToRoute("user_profile");
        }
        return $this->render('user/modifyUser.html.twig', ['userForm'=>$form->createView()]);
    }
}
