<?php

namespace App\Controller;

use App\Entity\Checkout;
use App\Entity\Seller;
use App\Entity\SellerRequests;
use App\Entity\User;
use App\Entity\Product;
use App\Form\ChangePasswordType;
use App\Form\ProductType;
use App\Form\EditProfileType;
use App\Form\ImportTableType;
use App\Form\RegistrationFormType;
use App\Service\AccountService;
use App\Service\ProductImportService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class AccountController extends AbstractController
{
    private $service;
    /**
     * @var ProductImportService
     */
    private $productImportService;

    public function __construct(AccountService $service, ProductImportService $importService)
    {
        $this->service = $service;
        $this->productImportService = $importService;
    }

    /**
     * @Route("/account/profile", name="profile")
     */
    public function profileAction()
    {
        $user = $this->getUser();

        return $this->render('account/profile.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/account/edit/profile", name="edit_profile")
     */
    public function profileEditAction(Request $request)
    {
        $user = $this->getUser();
        $form = $this->createForm(EditProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->service->persistToTable($user);
            $this->addFlash('notice', 'Вы успешно отредактивовали профиль');
            return $this->redirectToRoute('profile');
        }

        return $this->render('account/edit_profile.html.twig', [
            'form' => $form->createView(),
            'error' => null
        ]);

    }


    /**
     * @Route("/account/edit/password", name="password_edit")
     */
    public function passwordEditAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->service->changePassword($form, $user);
            $this->addFlash('notice', 'Вы успешно изменили пароль');
            return $this->redirectToRoute('profile');
        }

        return $this->render('account/edit_password.html.twig', [
            'form' => $form->createView(),
            'error' => null
        ]);
    }

    /**
     * @Route("/account/myhistory", name="myhistory")
     */
    public function myHistoryAction(Request $request)
    {
        $orders = $this->getDoctrine()->getRepository(Checkout::class)
            ->findByUser($this->getUser()->getId(), $request->get('page'));

        $thisPage = $request->get('page') ?: 1;

        $maxPages = ceil($orders->count() / 4);

        return $this->render('account/history.html.twig', [
            'thisPage' => $thisPage,
            'maxPages' => $maxPages,
            'orders' => $orders
        ]);
    }

    /**
     * @Route("/account/seller/orders", name="seller_orders")
     */
    public function sellerOrdersListAction(Request $request)
    {

        $user = $this->getUser();
        $sellerId = $user->getSeller()->getId();
        $orders = $this->getDoctrine()->getRepository(Checkout::class)
            ->findBySeller($sellerId, $request->get('page'));

        $thisPage = $request->get('page') ?: 1;

        $maxPages = ceil($orders->count() / 4);

        return $this->render('account/seller_orders.html.twig', [
            'thisPage' => $thisPage,
            'maxPages' => $maxPages,
            'orders' => $orders
        ]);
    }

    /**
     * @Route("/account/seller/orders/submit", name="seller_orders_submit")
     */
    public function sellerOrdersSubmitAction(Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            if (($id = $request->request->get('id')) == true) {

                $order = $this->getDoctrine()->getRepository(Checkout::class)->find($id);
                $order->setStatus(Checkout::STATUS_ACCEPT);
                $this->service->persistToTable($order);

                return new JsonResponse(['message' => 'Done'], 200);

            }
        }
        return new JsonResponse(['message' => 'Update failure'], 404);
    }

    /**
     * @Route("/account/seller/orders/cancel", name="seller_orders_cancel")
     */
    public function sellerOrdersCancelAction(Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            if (($id = $request->request->get('id')) == true) {

                $order = $this->getDoctrine()->getRepository(Checkout::class)->find($id);
                $order->setStatus(Checkout::STATUS_CANCEL);
                $this->service->persistToTable($order);

                return new JsonResponse(['message' => 'Done'], 200);

            }
        }
        return new JsonResponse(['message' => 'Update failure'], 404);
    }

    /**
     * @Route("/account/seller/request", name="requests")
     */
    public function sellerRequestsListAction(Request $request)
    {

        $sellerId = $this->getUser()->getSeller()->getId();
        $requests = $this->getDoctrine()->getRepository(SellerRequests::class)
            ->findBySeller($sellerId, $request->get('page'));

        $thisPage = $request->get('page') ?: 1;

        $maxPages = ceil($requests->count() / 4);

        return $this->render('account/requests.html.twig', [
            'thisPage' => $thisPage,
            'maxPages' => $maxPages,
            'requests' => $requests
        ]);
    }

    /**
     * @Route("/account/seller/managers", name="managers")
     */
    public function sellerManagersListAction(Request $request)
    {

        $managersClear = $this->service->getManagers($this->getUser(), $request);

        $thisPage = $request->get('page') ?: 1;

        $maxPages = ceil(count($managersClear) / 4);

        return $this->render('account/managers.html.twig', [
            'thisPage' => $thisPage,
            'maxPages' => $maxPages,
            'managers' => $managersClear,
            'directory' => $this->getParameter('request_doc_directory')
        ]);
    }

    /**
     * @Route("/account/seller/request/submit", name="request_submit")
     */
    public function sellerRequestSubmitAction(Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            if (($id = $request->request->get('id')) == true) {

                $this->service->submit($id);

                return new JsonResponse(['message' => 'Done'], 200);

            }
        }
        return new JsonResponse(['message' => 'Update failure'], 404);
    }

    /**
     * @Route("/account/seller/request/cancel", name="request_cancel")
     */
    public function sellerRequestCancelAction(Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            if (($id = $request->request->get('id')) == true) {

                $this->service->cancel($id);

                return new JsonResponse(['message' => 'Done'], 200);

            }
        }
        return new JsonResponse(['message' => 'Update failure'], 404);
    }

    /**
     * @Route("/account/sellers/list", name="sellers_choice")
     */
    public function choiceSellerAction(Request $request)
    {
        $sellers = $this->getDoctrine()->getRepository(Seller::class)
            ->findByNamePaginate($request->get('page'), $request->get('search'));

        $thisPage = $request->get('page') ?: 1;

        $maxPages = ceil($sellers->count() / 4);

        return $this->render('account/sellers.html.twig', [
            'thisPage' => $thisPage,
            'maxPages' => $maxPages,
            'sellers' => $sellers
        ]);
    }

    /**
     * @Route("/account/seller/import", name="importcsv")
     */
    public function importCsv(Request $request): Response
    {
        $form = $this->createForm(ImportTableType::class);
        $form->handleRequest($request);

        $user = $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->getData()['importFile'];
            $this->productImportService->importCsv($file, $user);
            return $this->redirectToRoute('seller_product_list');
        }

        return $this->render('index/importcsv.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/account/seller/products", name="seller_product_list", methods={"GET"})
     */
    public function sellerProductList(Request $request): Response
    {
        $saller = $this->getUser()->getSeller()->getId();
        $seller = $this->getDoctrine()->getRepository(Seller::class)->find($saller);

        if ($seller)
        {
            $products = $this->getDoctrine()->getRepository(Product::class)
                ->searchProducts($request->get('query'), $seller->getId(), $request->get('page'));

            $thisPage = $request->get('page') ?: 1;

            $maxPages = ceil($products->count() / 4);
            return $this->render('account/product/list.html.twig', [
                'thisPage' => $thisPage,
                'maxPages' => $maxPages,
                'seller' => $seller,
                'products' => $products,
            ]);
        }

        return $this->redirectToRoute('index');
    }

    /**
     * @Route("/account/seller/products/new", name="seller_product_new", methods={"GET","POST"})
     */
    public function sellerCreateProduct(Request $request): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product->setSeller($this->getUser()->getSeller());
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('seller_product_list');
        }

        return $this->render('account/product/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/account/seller/products/{id}", name="seller_product_delete", methods={"DELETE"})
     */
    public function sellerDeleteProduct(Request $request, Product $product): Response
    {

        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($product);
            if ($checkouts = $this->service->checkProduct($product))
            {
                foreach ($checkouts as $item)
                {
                    $entityManager->remove($item);
                }

                $entityManager->remove($product);
                $entityManager->flush();
                return $this->redirectToRoute('seller_product_list');
            }
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('seller_product_list');
    }

    /**
     * @Route("/account/seller/products/{id}/edit", name="seller_product_edit", methods={"GET","POST"})
     */
    public function sellerEditProduct(Request $request, Product $product): Response
    {
        if ($product->getImage() !== null) {
            $product->setImage(
                new File($this->getParameter('product_images_directory').'/'.$product->getImage())
            );
        }
        $form = $this->createForm(ProductType::class, $product);
        $file = $product->getImage();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($product->getImage() == null){
                $product->setImage($file);
            }
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('seller_product_list', [
                'id' => $product->getId(),
            ]);
        }

        return $this->render('account/product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }
}
