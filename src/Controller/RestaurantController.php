<?php
// src/Controller/RestaurantController.php

namespace App\Controller;

use App\Entity\Restaurant;
use App\Form\RestaurantFormType;
use App\Repository\RestaurantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class RestaurantController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route(path:"/restaurants", name:"restaurants")]
    public function index(RestaurantRepository $restaurantRepository): Response
    {
        $restaurants = $restaurantRepository->findAll();

        return $this->render('restaurant/index.html.twig', [
            'restaurants' => $restaurants,
        ]);
    }

    #[Route(path: '/create-restaurant', name: 'create_restaurant')]
    public function createRestaurant(Request $request): Response
    {
        $restaurant = new Restaurant();

        // Kreiranje forme za unos restorana
        $form = $this->createForm(RestaurantFormType::class, $restaurant);

        // Handle form submission
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Postavljanje trenutno ulogovanog korisnika kao vlasnika restorana
            $restaurant->setUser($this->getUser());

            // Spremanje slike
            $this->handleFileUpload($form, $restaurant, 'photo');

            // Spremanje menija (PDF, itd.)
            $this->handleFileUpload($form, $restaurant, 'menu');

            // Sačuvaj restoran u bazu podataka
            $this->entityManager->persist($restaurant);
            $this->entityManager->flush();

            // Dodajte poruku o uspešnom kreiranju restorana (opcionalno)
            $this->addFlash('success', 'Restoran uspešno kreiran!');

            // Preusmerite korisnika gde želite
            return $this->redirectToRoute('welcome');
        }

        // Prikazivanje forme za unos restorana
        return $this->render('restaurant/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param \Symfony\Component\Form\FormInterface $form
     * @param \App\Entity\Restaurant $restaurant
     * @param string $fieldName
     */
    private function handleFileUpload(\Symfony\Component\Form\FormInterface $form, \App\Entity\Restaurant $restaurant, string $fieldName): void
    {
        /** @var \Symfony\Component\HttpFoundation\File\UploadedFile|null $file */
        $file = $form->get($fieldName)->getData();

        if ($file instanceof UploadedFile) {
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();

            // Premještanje datoteke u public/uploads folder
            $file->move($this->getParameter('kernel.project_dir') . '/public/uploads', $fileName);

            // Postavljanje imena datoteke u entitet
            $setter = 'set' . ucfirst($fieldName);
            $restaurant->$setter($fileName);
        }
    }
    #[Route(path: '/edit-restaurant/{id}', name: 'restaurant_edit')]
    public function editRestaurant(Request $request, Restaurant $restaurant): Response
    {
        // Provjeri da li je korisnik vlasnik restorana
        if ($this->getUser() !== $restaurant->getUser()) {
            throw $this->createAccessDeniedException('You do not have access to edit this restaurant.');
        }
    
        // Kreiranje forme za uređivanje restorana s postavljenim početnim podacima
        $form = $this->createForm(RestaurantFormType::class, $restaurant);
    
        // Handle form submission
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Sačuvaj uređeni restoran u bazu podataka
            $this->entityManager->flush();
        
            // Dodajte poruku o uspješnom uređivanju restorana (opcionalno)
            $this->addFlash('success', 'Restoran uspješno uređen!');
        
            // Preusmjerite korisnika gdje želite
            return $this->redirectToRoute('welcome');
        }
    
        // Prikazivanje forme za uređivanje restorana
        return $this->render('restaurant/edit.html.twig', [
            'form' => $form->createView(),
            'restaurant' => $restaurant,
        ]);
    }

}

