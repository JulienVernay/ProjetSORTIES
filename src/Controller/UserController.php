<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\User;
use App\Form\UserFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class UserController extends AbstractController
{
    /**
     * @Route("/user/{id}", requirements={"id": "\d+"}, name="user_profile", methods={"GET", "POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function profile(Request $request, EntityManagerInterface $entityManager): Response
    {
        $id = $request->get('id');

        $user = $entityManager->getRepository('App:User')->find($id);

        if(is_null($user)){
            throw $this->createNotFoundException();
        }

        return $this->render('user/userProfile.html.twig', ['user' => $user]);
    }

    /**
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param EntityManagerInterface $em
     * @return Response
     * @Route("user/{id}/modify", name="user_modify", methods={"GET", "POST"})
     */
    public function modify(Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $em): Response
    {
        $id = $request->get('id');
        $user = $em->getRepository('App:User')->find($id);
        $campusRepo = $this->getDoctrine()->getRepository(Campus::class);

        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $uploadedFile = ($form['imageFile']->getData());
            if ($uploadedFile) {
                $destination = $this->getParameter('kernel.project_dir') . '/public/uploads/photo';

                $originalFileName = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFileName = Urlizer::urlize($originalFileName) . '-' . uniqid() . '.' . $uploadedFile->guessExtension();

                $uploadedFile->move(
                    $destination,
                    $newFileName
                );

                $user->setImageFileName($newFileName);
            }
            $user->setPassword(
                $passwordEncoder->encodePassword(

                    $user,
                    $form->get('password')->getData()
                )
            );
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Profil modifié');
            return $this->redirectToRoute('user_profile', ['id'=>$user->getId()]);

        }
        return $this->render('user/userModify.html.twig', ['userForm'=>$form->createView(), 'campus'=>$campusRepo]);

    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $em
     * @Route("user/{id}/delete", requirements={"id": "\d+"}, name="delete_photo", methods={"GET", "POST"})
     */
    public function deletePhoto(Request $request, UserRepository $userRepository, EntityManagerInterface $em){
        $id = $request->get('id');
        $user = $em->getRepository('App:User')->find($id)->setImageFileName('');

        $em->flush();
        $this->addFlash('success', 'Photo de profil supprimée');
        return $this->redirectToRoute('user_modify', ['id'=>$id]);
    }
}
