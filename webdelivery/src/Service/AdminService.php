<?php


namespace App\Service;


use App\Entity\AdminRequests;
use App\Entity\Checkout;
use App\Entity\DeliveryOrder;
use App\Entity\Seller;
use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Twig\Environment;

class AdminService
{
    private $manager;
    private $authService;
    private $templating;
    private $mailer;

    public function __construct(ObjectManager $manager, AuthService $service, \Swift_Mailer $mailer, Environment $templating)
    {
        $this->manager = $manager;
        $this->authService = $service;
        $this->templating = $templating;
        $this->mailer = $mailer;
    }

    public function persistToTable($entity)
    {
        $this->manager->persist($entity);
        $this->manager->flush();
    }

    public function removeFromTable($entity)
    {
        $this->manager->remove($entity);
        $this->manager->flush();
    }

    public function chartUserRegistry(int $count)
    {
        $repository = $this->manager->getRepository(User::class);
        $counts = [];
        $date = new \DateTime();
        $date->modify('+1 day');
        for ($i = 0; $i < $count; $i++)
        {
            $date->modify('-1 day');
            $counts['data'][] = $repository->getCountByDay(
                $date->format('Y'),
                $date->format('m'),
                $date->format('d')
            );
            $counts['labels'][] = $date->format('d M');
            $counts['max'] = max($counts['data']);

        }
        $counts['labels'] = array_reverse($counts['labels']);
        $counts['data'] = array_reverse($counts['data']);
        return $counts;
    }

    public function newUserRegistry()
    {
        $repository = $this->manager->getRepository(User::class);
        $date = new \DateTime();
        return $repository->getCountByDay(
            $date->format('Y'),
            $date->format('m'),
            $date->format('d')
        );
    }

    public function getDoneOrdersToday()
    {
        return $this->manager->getRepository(Checkout::class)->countDoneOrders(new \DateTime());
    }

    public function getCancelOrdersToday()
    {
        return $this->manager->getRepository(Checkout::class)->countCancelOrders(new \DateTime());
    }

    public function requestSubmit(AdminRequests $adminRequest, $domen)
    {
        $user = new User();
        $seller = new Seller();
        $login = $this->authService->generateStr();

        $seller->setName($adminRequest->getCompanyName());
        $seller->setAddress($adminRequest->getCompanyAddress());
        $seller->setDescription($adminRequest->getCompanyDescription());
        $this->manager->persist($seller);

        $user->setEmail($adminRequest->getEmail());
        $user->setRoles(User::ROLE_SELLER_MAIN);
        $user->setName($adminRequest->getName());
        $user->setSurname($adminRequest->getSurname());
        $user->setLogin($login);
        $user->setSeller($seller);

        $this->authService->register($user, $domen, $this->authService->generateStr(), 'email/admin_request_submit.html.twig', 'Заявка одобрена');
        $this->removeFromTable($adminRequest);
    }

    public function requestCancel(AdminRequests $adminRequest)
    {
        $this->removeFromTable($adminRequest);

        $message = (new \Swift_Message('Заявка отлонена'))
            ->setFrom('delivery.dev@gmail.com')
            ->setTo($adminRequest->getEmail())
            ->setBody(
                $this->templating->render(
                    'email/admin_request_cancel.html.twig'
                ),
                'text/html'
            );
        $this->mailer->send($message);
    }
}