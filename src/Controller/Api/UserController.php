<?php

namespace App\Controller\Api;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest; 
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Form\UserType;


class UserController extends AbstractFOSRestController
{
    
    /**
     * @Rest\Post(path="/register")
     * @Rest\View(serializerGroups={"user"}, serializerEnableMaxDepthChecks=true)
     */
    public function registerUser(Request $request,EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder)
    {

        $response = new JsonResponse();
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() ){
         
                
            $user->setPassword($passwordEncoder->encodePassword(
                $user,
                $form['password']->getData()
            ));
            $entityManager->persist($user);

            // actually executes the queries (i.e. the INSERT query)
            $entityManager->flush();
            $data = [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
            ];

            $response->setData([
                'success' => true,
                'message' => 'Usuario registrado exitosamente',
                'user' => $data
                
    
            ]);

            return $response;

        }
        


        $response->setData([
            'success' => false,
            'error' => 'Request Failed',
            'user' => null

        ]);
        
        return $response;
    }


    

}
