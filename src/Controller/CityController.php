<?php

namespace App\Controller;

use App\Entity\City;
use App\Form\AddCityFormType;
use App\Form\ModifyCityFormType;
use App\Form\SearchCityFormType;
use App\Repository\CityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CityController extends AbstractController
{
    /**
     * @Route ("/city", name="city")
     */
    public function search_add(Request $request, CityRepository $cityRepository, EntityManagerInterface $em){
        $newCity = new City();

        $addCityForm = $this->createForm(AddCityFormType::class, $newCity);
        $addCityForm->handleRequest($request);

        $searchCityForm = $this->createForm(SearchCityFormType::class);
        $searchCityForm->handleRequest($request);

        $cityRepo = $this->getDoctrine()->getRepository(City::class);
        $city = $cityRepo->findAll();

        if ($addCityForm->isSubmitted() && $addCityForm->isValid()){
            $em->persist($newCity);
            $em->flush();
            $this->addFlash('success', 'Nouvelle ville ajoutée ('.$newCity->getName().')');
            return $this->redirectToRoute('city');
        }
        if ($searchCityForm->isSubmitted() && $searchCityForm->isValid()){
            $nameCity = $searchCityForm->getData();
            $zipCodeCity = $searchCityForm->getData();



            $city = $cityRepository->searchCity($nameCity, $zipCodeCity);


            return $this->render('management/manageCity.html.twig', [
                'search_city_form'=>$searchCityForm->createView(),'add_city_form'=>$addCityForm->createView(), 'city'=>$city
            ]);

        }
        return $this->render('management/manageCity.html.twig', [
            'search_city_form'=>$searchCityForm->createView(), 'add_city_form'=>$addCityForm->createView(), 'city'=>$city]);
    }

    /**
     * @param $id
     * @param EntityManagerInterface $em
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/city/delete/{id}", requirements={"id":"\d+"}, name="delete_city", methods={"GET", "POST"})
     */
    public function deleteCity($id, EntityManagerInterface $em): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $repo = $em->getRepository(City::class);
        $city = $repo->find($id);

        $em->remove($city);
        $em->flush();

        $this->addFlash('success', 'La ville de '.$city->getName().' a été supprimée.');

        return $this->redirectToRoute('city');
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     * @Route("city/modify/{id}", requirements={"id":"\d+"}, name="modify_city")
     */
    public function modifyCity(Request $request, EntityManagerInterface $em): Response
    {
        $id = $request->get('id');
        $city = $em->getRepository('App:City')->find($id);
        $form = $this->createForm(ModifyCityFormType::class, $city);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Ville modifiée avec succès');
            return $this->redirectToRoute('city');
        }
        return $this->render('management/modifyCity.html.twig', ['modifyCityForm'=>$form->createView()]);
    }
}

