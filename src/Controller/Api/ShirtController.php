<?php

namespace App\Controller\Api;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest; 
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Shirt;
use App\Repository\ShirtRepository;
use App\Form\ShiryType;

class ShirtController extends AbstractFOSRestController
{
    /**
     * @Rest\Get(path="/shirt")
     * @Rest\View(serializerGroups={"shirt"}, serializerEnableMaxDepthChecks=true)
     */
    public function getShirts(ShirtRepository $shirtRepository)
    {
       
        $shirts = $shirtRepository->findAll();
        return $shirts;
    }

    /**
     * @Rest\Post(path="/shirt")
     * @Rest\View(serializerGroups={"shirt"}, serializerEnableMaxDepthChecks=true)
     */
    public function createShirt(Request $request,EntityManagerInterface $entityManager)
    {

        $shirt = new Shirt();
        $response = new JsonResponse();
        
        $form = $this->createForm(ShiryType::class, $shirt);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($shirt);

            // actually executes the queries (i.e. the INSERT query)
            $entityManager->flush();
            $data = [
                'id' => $shirt->getId(),
                'talla' => $shirt->getTalla(),
                'color' => $shirt->getColor(),
                'precio' => $shirt->getPrecio(),
            ];
            $response->setData([
                'success' => true,
                'shirt' => $data
                
    
            ]);
            return $response;
        }

        $response->setData([
            'success' => false,
            'error' => 'Request Failed',
            'shirt' => null

        ]);
        
        return $response;
    }
}
