<?php

namespace App\Controller;

use App\Entity\AdminRequests;
use App\Entity\Category;
use App\Entity\Checkout;
use App\Entity\Product;
use App\Entity\Seller;
use App\Entity\User;
use App\Form\CategoryType;
use App\Service\AdminService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin", name="admin_")
 */
class AdminController extends AbstractController
{
    const COUNT_USER = 10;

    private $service;

    public function __construct(AdminService $service)
    {
        $this->service = $service;
    }

    /**
     * @Route("", name="dashboard")
     */
    public function dashboardAction()
    {
        $newUsers = $this->service->newUserRegistry();
        $doneOrdersToday = $this->service->getDoneOrdersToday();
        $cancelOrderToday = $this->service->getCancelOrdersToday();
        return $this->render('admin/dashboard.html.twig', [
            'newUser' => $newUsers,
            'doneOrdersToday' => $doneOrdersToday,
            'cancelOrdersToday' => $cancelOrderToday
        ]);
    }

    /**
     * @Route("/users", name="users")
     */
    public function usersListAction(Request $request)
    {
        $users = $this->getDoctrine()->getRepository(User::class)
            ->findByLoginAndRole($request->get('page'), $request->get('search'), $request->get('role'));
        $thisPage = $request->get('page') ?: 1;

        $maxPages = ceil($users->count() / 4);

        return $this->render('admin/users.html.twig', [
            'thisPage' => $thisPage,
            'maxPages' => $maxPages,
            'users' => $users
        ]);
    }

    /**
     * @Route("/users/delete", name="user_delete")
     */
    public function deleteUser(Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            if (($id = $request->request->get('id')) == true) {
                $user = $this->getDoctrine()->getRepository(User::class)->find($id);
                $this->service->removeFromTable($user);

                return new JsonResponse(['message' => 'Done'], 200);

            }
        }
        return new JsonResponse(['message' => 'Update failure'], 404);
    }

    /**
     * @Route("/data/registry", name="data_count_users")
     */
    public function getRegistryUser(Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
            $userRegistryCount = $this->service->chartUserRegistry(self::COUNT_USER);
            return new JsonResponse($userRegistryCount, 200);
        }

        return new JsonResponse(['message' => 'Update failure'], 404);
    }

    /**
     * @Route("/requests", name="requests")
     */
    public function adminRequestsListAction(Request $request)
    {
        $adminRequests = $this->getDoctrine()->getRepository(AdminRequests::class)->findAllPaginate($request->get('page'));

        $thisPage = $request->get('page') ?: 1;

        $maxPages = ceil($adminRequests->count() / 4);

        return $this->render('admin/requests.html.twig', [
            'thisPage' => $thisPage,
            'maxPages' => $maxPages,
            'requests' => $adminRequests,
            'directory' => $this->getParameter('request_doc_directory')
        ]);
    }

    /**
     * @Route("/requests/submit", name="requests_submit")
     */
    public function adminRequestsSubmitAction(Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            if (($id = $request->request->get('id')) == true) {

                $adminRequest = $this->getDoctrine()->getRepository(AdminRequests::class)->find($id);
                $this->service->requestSubmit($adminRequest, $request->getSchemeAndHttpHost());
                return new JsonResponse(['message' => 'Done'], 200);

            }
        }
        return new JsonResponse(['message' => 'Update failure'], 404);
    }

    /**
     * @Route("/requests/cancel", name="requests_cancel")
     */
    public function adminRequestsCancelAction(Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            if (($id = $request->request->get('id')) == true) {

                $adminRequest = $this->getDoctrine()->getRepository(AdminRequests::class)->find($id);
                $this->service->requestCancel($adminRequest);
                return new JsonResponse(['message' => 'Done'], 200);
            }
        }
        return new JsonResponse(['message' => 'Update failure'], 404);
    }

    /**
     * @Route("/sellers", name="sellers")
     */
    public function sellerListAction(Request $request)
    {
        $sellers = $this->getDoctrine()->getRepository(Seller::class)
            ->findByNamePaginate($request->get('page'), $request->get('search'));

        $thisPage = $request->get('page') ?: 1;

        $maxPages = ceil($sellers->count() / 4);

        return $this->render('admin/sellers.html.twig', [
            'thisPage' => $thisPage,
            'maxPages' => $maxPages,
            'sellers' => $sellers
        ]);
    }

    /**
     * @Route("/sellers/delete", name="seller_delete")
     */
    public function sellerDeleteAction(Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            if (($id = $request->request->get('id')) == true) {
                $seller = $this->getDoctrine()->getRepository(Seller::class)->find($id);
                $this->service->removeFromTable($seller);
                return new JsonResponse(['message' => 'Done'], 200);
            }
        }
        return new JsonResponse(['message' => 'Update failure'], 404);
    }

    /**
     * @Route("/sellers/{id}", name="seller_view")
     */
    public function sellerViewAction(Request $request)
    {
        $seller = $this->getDoctrine()->getRepository(Seller::class)->find($request->get('id'));

        $products = $this->getDoctrine()->getRepository(Product::class)
            ->findBySeller($seller, $request->get('page'), $request->get('search'));

        $thisPage = $request->get('page') ?: 1;

        $maxPages = ceil($products->count() / 4);

        return $this->render('admin/view_seller.html.twig', [
            'thisPage' => $thisPage,
            'maxPages' => $maxPages,
            'products' => $products,
            'seller' => $seller,
        ]);
    }

    /**
     * @Route("/categories", name="categories")
     */
    public function categoriesListAction(Request $request)
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->search($request->get('page'), $request->get('search'));

        $thisPage = $request->get('page') ?: 1;

        $maxPages = ceil($categories->count() / 4);

        return $this->render('admin/categories.html.twig', [
            'thisPage' => $thisPage,
            'maxPages' => $maxPages,
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/categories/new", name="category_new", methods={"GET","POST"})
     */
    public function newCategoryAction(Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->service->persistToTable($category);
            return $this->redirectToRoute('admin_categories');
        }

        return $this->render('category/new_edit.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
            'title' => 'Создание категории',
            'btn' => 'Создать'
        ]);
    }

    /**
     * @Route("/categories/edit/{id}", name="category_edit", methods={"GET","POST"})
     */
    public function editCategoryAction(Request $request): Response
    {
        $category = $this->getDoctrine()->getRepository(Category::class)->find($request->get('id'));
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->service->persistToTable($category);
            return $this->redirectToRoute('admin_categories');
        }

        return $this->render('category/new_edit.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
            'title' => 'Редактирование категории',
            'btn' => 'Изменить'
        ]);
    }

    /**
     * @Route("/categories/delete", name="category_delete")
     */
    public function categoryDeleteAction(Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            if (($id = $request->request->get('id')) == true) {
                $seller = $this->getDoctrine()->getRepository(Category::class)->find($id);
                $this->service->removeFromTable($seller);
                return new JsonResponse(['message' => 'Done'], 200);
            }
        }
        return new JsonResponse(['message' => 'Update failure'], 404);
    }

    /**
     * @Route("/orders", name="admin_orders")
     */
    public function adminOrdersListAction(Request $request)
    {
        $orders = $this->getDoctrine()->getRepository(Checkout::class)
            ->findAllPaginate($request->get('page'));

        $thisPage = $request->get('page') ?: 1;

        $maxPages = ceil($orders->count() / 4);

        return $this->render('admin/orders.html.twig', [
            'thisPage' => $thisPage,
            'maxPages' => $maxPages,
            'orders' => $orders
        ]);
    }

    /**
     * @Route("orders/submit", name="admin_orders_done")
     */
    public function adminOrdersDoneAction(Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            if (($id = $request->request->get('id')) == true) {

                $order = $this->getDoctrine()->getRepository(Checkout::class)->find($id);
                $order->setStatus(Checkout::STATUS_DONE);
                $this->service->persistToTable($order);

                return new JsonResponse(['message' => 'Done'], 200);

            }
        }
        return new JsonResponse(['message' => 'Update failure'], 404);
    }

    /**
     * @Route("orders/cancel", name="admin_orders_cancel")
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
}
