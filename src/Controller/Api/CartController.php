<?php

namespace App\Controller\Api;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest; 
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Cart;
use App\Entity\CartDetail;
use App\Repository\CartRepository;
use App\Form\CartType;
use App\Form\CartDetailType;
use App\Repository\CartDetailRepository;

class CartController extends AbstractFOSRestController
{
    /**
     * @Rest\Get(path="/cart")
     * @Rest\View(serializerGroups={"user","cart_detail","shirt"}, serializerEnableMaxDepthChecks=true)
     */

    public function getCartByUser(CartRepository $cartRepository,CartDetailRepository $cartDetailRepository )
    {
        $user = $this->getUser();
        $data = $cartRepository->findOneBy(['user' => $user]);
       
        $cart_id = $data->getId();

        $info = $cartDetailRepository->findBy(['cart' => $cart_id]);


        return $info;
    }

    /**
     * @Rest\Post(path="/save-cart")
     * @Rest\View(serializerGroups={"cart","user","cart_detail"}, serializerEnableMaxDepthChecks=true)
     */
    public function createCart(Request $request,EntityManagerInterface $entityManager,CartRepository $cartRepository)
    {

        $cart = new Cart();
        $cartDetail = new CartDetail();
        $response = new JsonResponse();
        $user = $this->getUser();

        $cartActive = $cartRepository->findOneBy(['user' => $user]);
       
        
        //Tiene un carrito activo
        if(!empty($cartActive)){

            $cart_id = $cartActive;
            
            
            //Creamos el formulario para el detalle del carrito
            $form = $this->createForm(CartDetailType::class, $cartDetail);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                //Añadimos el detalle del poroducto
                $cartDetail->setCart($cart_id);

                $entityManager->persist($cartDetail);
            
                // actually executes the queries (i.e. the INSERT query)
                $entityManager->flush();
                
                $response->setData([
                    'success' => true,
                    'error' => false,
                    'message' => 'Se ha agregado el producto correctamente',   
        
                ]);
                return $response;
            }

            $response->setData([
                'success' => false,
                'error' => true,
                'message' => 'Error al agregar el producto',   
                'Cart' => $cart_id,   
    
            ]);
            return $response;

        }

        //Creamos el carrito de compra
        
        $formCart = $this->createForm(CartType::class, $cart);
        $formCart->handleRequest($request);

        $cart->setUser($user);
        $entityManager->persist($cart);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        $card_id = $cart->getId();

        //Creamos el detalle del carrito de compra

        $formDetail = $this->createForm(CartDetailType::class, $cartDetail);
        $formDetail->handleRequest($request);

          
            if($formDetail->isSubmitted() && $formDetail->isValid()){

                $cartDetail->setCart($cart);

                $entityManager->persist($cartDetail);

                // actually executes the queries (i.e. the INSERT query)
                $entityManager->flush();

                $response->setData([
                    'success' => true,
                    'error' => false,
                    'message' => 'Se ha agregado el producto correctamente',   
        
                ]);
                return $response;

            }

        $response->setData([
            'success' => false,
            'error' => true,
            'message' => 'Error al crear el detalle de la compra',   
            'id' => $card_id,   

        ]);
        return $response;
        
    




       
    }
}
