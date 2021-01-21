<?php

namespace App\Controller;

use App\Repository\PropertyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PropertyController extends AbstractController
{
    /**
     *
     * @var PropertyRepository
     */
    private $repository;

    public function __construct(PropertyRepository $repository)
    {
        $this->repository = $repository;
    }


    /**
     * @Route("/biens",  name="property.index")
     * @return Response
     */
    public function index(): Response
    {
        $property = $this->repository->findOneBy(['floor'=>4]);
        dump($property);
        // $repository = $this->getDoctrine()->getRepository(Property::class);
        // dump($repository);

        // $property = new Property();
        // $property->setTitle('Mon premier bien')
        // ->setPrice(200000)
        // ->setRooms(4)
        // ->setBedrooms(3)
        // ->setDescription('Une petite description')
        // ->setSurface(60)
        // ->setFloor(4)
        // ->setHeat(1)
        // ->setCity('Montpellier')
        // ->setAddress('64 rue Gambetta')
        // ->setPostalCode('34000');
        // $entityManager = $this->getDoctrine()->getManager();
        // $entityManager->persist($property);
        // $entityManager->flush();
        return $this->render('property/index.html.twig', [
            'current_menu' => 'properties'
        ]);
    }
}
