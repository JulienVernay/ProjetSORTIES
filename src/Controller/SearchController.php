<?php


namespace App\Controller;


use App\Form\SearchCampusFormType;
use App\Repository\CampusRepository;
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

        if ($searchCampusForm->isSubmitted() && $searchCampusForm->isValid()){
            $name = $searchCampusForm->getData();

            $campus = $campusRepository->searchCampus($name);
            dd($campus);
    }

        return $this->render('search/searchCampus.html.twig', [
            'search_form'=>$searchCampusForm->createView()
        ]);
    }
}