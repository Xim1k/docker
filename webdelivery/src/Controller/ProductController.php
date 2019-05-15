<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Seller;
use App\Form\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProductController extends AbstractController
{
    /**
     * @Route("/seller/{id}/products", name="product_index", methods={"GET"})
     */
    public function index(int $id, Request $request): Response
    {

        $seller = $this->getDoctrine()->getRepository(Seller::class)->find($id);
        $request->getSession()->set('sellerId', $id);

        if ($seller)
        {
            $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
            $products = $this->getDoctrine()->getRepository(Product::class)
                ->searchProducts($request->get('query'), $seller->getId(), $request->get('page'), $request->get('category'));

            $thisPage = $request->get('page') ?: 1;

            $maxPages = ceil($products->count() / 9);
            return $this->render('product/index.html.twig', [
                'thisPage' => $thisPage,
                'maxPages' => $maxPages,
                'seller' => $seller,
                'products' => $products,
                'categories' => $categories,
                'userAddress' => $request->getSession()->get('userAddress')
            ]);
        }

        return $this->redirectToRoute('index');
    }

    /**
     * @Route("/product/{id}", name="product_show", methods={"POST", "GET"})
     */
    public function show(Product $product): Response
    {
        return new JsonResponse(['product' => [
            'name' => $product->getName(),
            'description' => $product->getDescription(),
            'price' => $product->getPrice(),
            'category' => $product->getCategory()->getName(),
            'image' => $product->getImage(),
        ]], 200);
    }
}
