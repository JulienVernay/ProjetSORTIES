<?php


namespace App\Controller;


use App\Entity\Campus;
use App\Form\SearchCampusFormType;
use App\Repository\CampusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    /**
     * @Route ("/campus/search", name="search_campus")
     */
    public function search(Request $request, CampusRepository $campusRepository){
        $searchCampusForm = $this->createForm(SearchCampusFormType::class);
        $searchCampusForm->handleRequest($request);

        $campusRepo = $this->getDoctrine()->getRepository(Campus::class);
        $campus = $campusRepo->findAll();

        if ($searchCampusForm->isSubmitted() && $searchCampusForm->isValid()){
            $name = $searchCampusForm->getData();

            $campus = $campusRepository->searchCampus($name);
            return $this->render('search/searchCampus.html.twig', [
                'search_form'=>$searchCampusForm->createView(), 'campus'=>$campus
            ]);

        }
        return $this->render('search/searchCampus.html.twig', [
            'search_form'=>$searchCampusForm->createView(), 'campus'=>$campus]);
    }

    /**
     * @param $id
     * @param EntityManagerInterface $em
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/campus/delete/{id}", requirements={"id":"\d+"}, name="delete_campus", methods={"POST"})
     */
    public function deleteCampus($id, EntityManagerInterface $em){
        $repo = $em->getRepository(Campus::class);
        $campus = $repo->find($id);

        $em->remove($campus);
        $em->flush();

        $this->addFlash('success', 'Le campus '.$campus->getCampusName().' a été supprimé.');

        return $this->redirectToRoute('search_campus');
    }
}