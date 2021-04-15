<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Form\AddCampusFormType;
use App\Form\ModifyCampusFormType;
use App\Form\SearchCampusFormType;
use App\Repository\CampusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CampusController extends AbstractController
{
    /**
     * @Route ("/campus", name="campus")
     * @IsGranted("ROLE_ADMIN", statusCode=404, message="Cet accès est réservé aux admin")
     */
    public function search_add(Request $request, CampusRepository $campusRepository, EntityManagerInterface $em){
        $newCampus = new Campus();

        $addCampusForm = $this->createForm(AddCampusFormType::class, $newCampus);
        $addCampusForm->handleRequest($request);

        $searchCampusForm = $this->createForm(SearchCampusFormType::class);
        $searchCampusForm->handleRequest($request);

        $campusRepo = $this->getDoctrine()->getRepository(Campus::class);
        $campus = $campusRepo->findAll();

        if ($addCampusForm->isSubmitted() && $addCampusForm->isValid()){
            $em->persist($newCampus);
            $em->flush();
            $this->addFlash('success', 'Nouveau campus ajouté ('.$newCampus->getCampusName().')');
            return $this->redirectToRoute('campus');
        }
        if ($searchCampusForm->isSubmitted() && $searchCampusForm->isValid()){
            $name = $searchCampusForm->getData();

            $campus = $campusRepository->searchCampus($name);
            return $this->render('management/manageCampus.html.twig', [
                'search_form'=>$searchCampusForm->createView(),'add_form'=>$addCampusForm->createView(), 'campus'=>$campus
            ]);

        }
        return $this->render('management/manageCampus.html.twig', [
            'search_form'=>$searchCampusForm->createView(), 'add_form'=>$addCampusForm->createView(), 'campus'=>$campus]);
    }

    /**
     * @param $id
     * @param EntityManagerInterface $em
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/campus/delete/{id}", requirements={"id":"\d+"}, name="delete_campus", methods={"GET", "POST"})
     * @IsGranted("ROLE_ADMIN", statusCode=404, message="Cet accès est réservé aux admin")
     */
    public function deleteCampus($id, EntityManagerInterface $em): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $repo = $em->getRepository(Campus::class);
        $campus = $repo->find($id);

        $em->remove($campus);
        $em->flush();

        $this->addFlash('success', 'Le campus '.$campus->getCampusName().' a été supprimé.');

        return $this->redirectToRoute('campus');
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Route("campus/modify/{id}", requirements={"id":"\d+"}, name="modify_campus")
     * @IsGranted("ROLE_ADMIN", statusCode=404, message="Cet accès est réservé aux admin")
     */
    public function modifyCampus(Request $request, EntityManagerInterface $em){
        $id = $request->get('id');
        $campus = $em->getRepository('App:Campus')->find($id);
        $form = $this->createForm(ModifyCampusFormType::class, $campus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Campus modifié avec succès');
            return $this->redirectToRoute('campus');
        }
        return $this->render('management/modifyCampus.html.twig', ['modifyCampusForm'=>$form->createView()]);
    }
}