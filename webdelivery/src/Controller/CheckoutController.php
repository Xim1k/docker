<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Seller;
use App\Repository\ProductRepository;
use App\Entity\CheckoutProduct;
use App\Entity\Checkout;
use App\Form\CheckoutType;
use App\Repository\CheckoutRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @Route("/checkout")
 */
class CheckoutController extends AbstractController
{
    /**
     * @Route("/cart", name="checkout_cart", methods={"POST"})
     */
    public function shoppingCart(Request $request)
    {
        $session = $request->getSession();
        $shoppingCart = json_decode($request->request->get('products'), true);
        $session->set('shoppingCart', $shoppingCart);
        return new Response();
    }

    /**
     * @Route("/new", name="checkout_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        
        $checkout = new Checkout();

        $session = $request->getSession();

        $sellerId = $session->get('sellerId');
        $seller = $this->getDoctrine()
            ->getRepository(Seller::class)
            ->findOneById($sellerId)
        ;

        if ($session->get('userAddress'))
        {
            $checkout->setAddress($session->get('userAddress'));
        }

        $checkout->setSeller($seller);
        $checkout->setUser($this->getUser());

        $shoppingCart = $session->get('shoppingCart');
        $entityManager = $this->getDoctrine()->getManager();
        foreach($shoppingCart as $id => $count) {
            $product = $this->getDoctrine()
                ->getRepository(Product::class)
                ->findOneById($id)
            ;
            $checkoutProduct = new CheckoutProduct();
            $checkoutProduct->setProduct($product)
                ->setCount($count)
            ;
            $entityManager->persist($checkoutProduct);
            $checkout->addCheckoutProduct($checkoutProduct);
        }

        $form = $this->createForm(CheckoutType::class, $checkout);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($checkout);
            $entityManager->flush();

            return $this->redirectToRoute('myhistory');
        }

        return $this->render('checkout/new.html.twig', [
            'checkout' => $checkout,
            'form' => $form->createView(),
        ]);
    }
}
