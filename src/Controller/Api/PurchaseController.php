<?php

namespace App\Controller\Api;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Purchase;
use App\Entity\PurchaseDetail;
use App\Repository\CartRepository;
use App\Repository\CartDetailRepository;
use App\Repository\PurchaseDetailRepository;
use App\Repository\PurchaseRepository;

class PurchaseController extends AbstractFOSRestController
{
    /**
     * @Rest\Get(path="/purchase")
     * @Rest\View(serializerGroups={"user","shirt","purchase_detail"}, serializerEnableMaxDepthChecks=true)
     */

    public function getPurchase(PurchaseRepository $purchaseRepository,PurchaseDetailRepository $purchaseDetailRepository)
    {
    
        $user = $this->getUser();
        $data = $purchaseRepository->findOneBy(['user' => $user]);
       
        $purchase_id = $data->getId();

        $info = $purchaseDetailRepository->findBy(['purchase' => $purchase_id]);


        return $info;
    }

    /**
     * @Rest\Post(path="/save-purchase")
     * @Rest\View(serializerGroups={"cart","user","cart_detail""purchase","purchase_detail","shirt"}, serializerEnableMaxDepthChecks=true)
     */
    public function savePurchase(Request $request, EntityManagerInterface $entityManager, CartRepository $cartRepository, CartDetailRepository $cartDetailRepository)
    {

        $user = $this->getUser();
        $data = $cartRepository->findOneBy(['user' => $user]);

        $response = new JsonResponse();
        $purchase = new Purchase();
        

        $total = 0;
        $costs = 0;

        if (!empty($data)) {

            $cart_id = $data->getId();

            $info = $cartDetailRepository->findBy(['cart' => $cart_id]);

            //Calculo del total de la compra
            foreach ($info as $valor) {

                $costs += ($valor->getPrecio() * $valor->getCantidad());
                $total += $costs;
            }



            if ($total > 50) {

                //Creamos el formulario para el detalle del carrito
                // $form = $this->createForm(PurchaseType::class, $purchase);
                // $form->handleRequest($request);



                //Lo asociamos al usuario
                $purchase->setUser($user);
                $purchase->setGastosEnvio(0);
                $purchase->setMonto($total);


                $entityManager->persist($purchase);

                // actually executes the queries (i.e. the INSERT query)
                $entityManager->flush();

                //Creamos el formulario para el detalle de la compra
                // $form_detail = $this->createForm(PurchaseDetailType::class, $detail);
                // $form_detail->handleRequest($request);


                $serie=0;

                $purchase_id = $purchase->getId();

                foreach ($info as $item) {
                    $detail = new PurchaseDetail();
                    $detail->setPurchase($purchase);
                    $detail->setPrecio($item->getPrecio());
                    $detail->setCantidad($item->getCantidad());
                    $detail->setProduct($item->getProduct());

                    $entityManager->persist($detail);

                    // actually executes the queries (i.e. the INSERT query)
                    $entityManager->flush();

                    $entityManager->refresh($detail);
                    $serie++;
                }




                
                $cartDetailRepository->removeCartDetail($cart_id);

                $cartRepository->removeCart($cart_id);

                $response->setData([
                    'success' => true,
                    'error' => false,
                    'message' => 'Se ha agregado el producto correctamente '.$serie,

                ]);
                return $response;

            } else {

                //Creamos el formulario para el detalle del carrito
                // $form = $this->createForm(PurchaseType::class, $purchase);
                // $form->handleRequest($request);


                //Lo asociamos al usuario
                $purchase->setUser($user);
                $purchase->setGastosEnvio(0);
                $purchase->setMonto($total);

                $entityManager->persist($purchase);

                // actually executes the queries (i.e. the INSERT query)
                $entityManager->flush();

                //Creamos el formulario para el detalle de la compra
                // $form_detail = $this->createForm(PurchaseDetailType::class, $detail);
                // $form_detail->handleRequest($request);

                $serie=0;

                $purchase_id = $purchase->getId();

                foreach ($info as $item) {
                    $detail = new PurchaseDetail();
                    $detail->setPurchase($purchase);
                    $detail->setPrecio($item->getPrecio());
                    $detail->setCantidad($item->getCantidad());
                    $detail->setProduct($item->getProduct());

                    $entityManager->persist($detail);

                    // actually executes the queries (i.e. the INSERT query)
                    $entityManager->flush();
                    $entityManager->refresh($detail);

                    $serie++;
                }




            

                $cartDetailRepository->removeCartDetail($cart_id);

                $cartRepository->removeCart($cart_id);
                $response->setData([
                    'success' => true,
                    'error' => false,
                    'message' => 'Se ha agregado el producto correctamente '.$serie,

                ]);
                return $response;
            }

            $response->setData([
                'success' => true,
                'error' => false,
                'message' => 'Compra registrada correctamente',
                'total' => $total,

            ]);
            return $response;
        }



        $response->setData([
            'success' => false,
            'error' => true,
            'message' => 'Ha ocurrido un error al registrar la compra',

        ]);
        return $response;
    }
}
