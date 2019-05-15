<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Seller;
use App\Entity\Checkout;
use App\Entity\User;
use App\Form\ImportTableType;
use App\Form\SearchProductType;
use App\Service\TokenGenerator;
use App\Service\ProductImportService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class IndexController extends AbstractController
{
    /**
     * @var ProductImportService
     */
    private $productImportService;

    public function __construct(ProductImportService $productImportService)
    {
        $this->productImportService = $productImportService;
    }

    /**
     * @Route("/", name="index")
     */
    public function index(Request $request):Response
    {
        $form = $this->createFormBuilder()
            ->add('seller', EntityType::class, [
                'label' => 'Магазин',
                'class' => Seller::class,
            ])
            ->add('submit', SubmitType::class, ['label' => 'Выбрать'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $session = $request->getSession();
            $session->set('sellerId', $form->getData()['seller']->getId());
            $session->set('shoppingCart', '');

            return $this->redirectToRoute('product_index', [

                'id' => $form->getData()['seller']->getId()
            ]);
        }

        return $this->render('index/index.html.twig', [
            'form' => $form->createView(),
            'userAddress' => $request->getSession()->get('userAddress')
        ]);
    }

    /**
     * @Route("/points", name="index_points")
     */
    public function getAddresses(Request $request){
        $addresses = $this->getDoctrine()->getRepository(Seller::class)->getAddresses();


        return new JsonResponse(['addresses' => $addresses], 200);
    }

    /**
     * @Route("/getseller", name="index_sellers")
     */
    public function getSellers(Request $request){
        if ($request->isXMLHttpRequest()) {
            if (($ids = $request->request->get('ids')) == true) {
                $request->getSession()->set('userAddress', $request->request->get('userAddress'));
                $sellers = [];
                foreach ($ids as $id)
                {
                    $seller = $this->getDoctrine()->getRepository(Seller::class)->find($id);
                    $sellers[$id]['id'] = $seller->getId();
                    $sellers[$id]['name'] = $seller->getName();
                    $sellers[$id]['path'] = $this->generateUrl('product_index', [ 'id' => $seller->getId()]);
                }

                return new JsonResponse(['sellers' => $sellers], 200);

            }
        }
        return new JsonResponse(['message' => 'Update failure'], 404);
        $addresses = $this->getDoctrine()->getRepository(Seller::class)->getAddresses();

        return new JsonResponse(['addresses' => $addresses], 200);
    }

}
