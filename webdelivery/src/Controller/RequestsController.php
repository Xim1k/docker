<?php

namespace App\Controller;

use App\Entity\AdminRequests;
use App\Entity\Seller;
use App\Entity\SellerRequests;
use App\Entity\User;
use App\Form\AdminRequestsType;
use App\Form\SellerRequestsType;
use App\Service\RequestService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/request")
 */
class RequestsController extends AbstractController
{
    private $service;

    public function __construct(RequestService $service)
    {
        $this->service = $service;
    }

    /**
     * @Route("/seller/new", name="seller_requests_new", methods={"GET","POST"})
     */
    public function newSellerRequest(Request $request): Response
    {
        $adminRequest = new AdminRequests();
        $form = $this->createForm(AdminRequestsType::class, $adminRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getDoctrine()->getRepository(User::class)
                ->findBy(['email' => $file = $form->get('email')->getData() ]);
            if ($user)
            {
                $this->addFlash('warning', 'Пользователь с таким email уже зарегистрирован.');
                return $this->render('admin_requests/new.html.twig', [
                    'admin_request' => $adminRequest,
                    'form' => $form->createView(),
                ]);
            }
            $this->service->addNewSeller($form, $adminRequest);
            $this->addFlash('notice', 'Ваша заявка отправлена на рассмотрение');
            return $this->redirectToRoute('index');
        }

        return $this->render('admin_requests/new.html.twig', [
            'admin_request' => $adminRequest,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/manager/new/{id}", name="manager_requests_new", methods={"GET","POST"})
     */
    public function newManagerRequest(Request $request): Response
    {
        $sellerRequest = new SellerRequests();
        $seller = $this->getDoctrine()->getRepository(Seller::class)->find($request->get("id"));
        if (!$this->service->checkManager($seller, $this->getUser()))
        {
            $this->addFlash('warning', 'Вы уже отправили заявку этому продавцу');
            return $this->redirectToRoute('sellers_choice');
        }
        $sellerRequest->setSeller($seller);
        $sellerRequest->setFirstName($this->getUser()->getName());
        $sellerRequest->setLastName($this->getUser()->getSurname());
        $form = $this->createForm(SellerRequestsType::class, $sellerRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && ($form->get('file')->getData() || $form->get('resume')->getData())) {
            $this->service->addNewManager($form, $sellerRequest, $this->getUser());
            $this->addFlash('notice', 'Ваша заявка отправлена на рассмотрение');
            return $this->redirectToRoute('profile');
        }

        return $this->render('seller_requests/new.html.twig', [
            'seller_request' => $sellerRequest,
            'form' => $form->createView(),
            'name' => $seller
        ]);
    }

}
