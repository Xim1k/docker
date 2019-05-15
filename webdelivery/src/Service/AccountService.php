<?php


namespace App\Service;


use App\Entity\CheckoutProduct;
use App\Entity\Product;
use App\Entity\SellerRequests;
use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Twig\Environment;

class AccountService
{

    private $manager;
    private $mailer;
    private $encoder;
    private $templating;

    public function __construct(\Swift_Mailer $mailer, Environment $templating, ObjectManager $manager, UserPasswordEncoderInterface $encoder)
    {
        $this->manager = $manager;
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->encoder = $encoder;
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

    public function changePassword(FormInterface $form, User $user)
    {
        $oldPwd = $form->get('old_pass')->getData();
        $newPwd = $form->get('new_password')->getData();

        $checkPass = $this->encoder->isPasswordValid($user, $oldPwd);
        if ($checkPass) {
            $newPwd = $this->encoder->encodePassword($user, $newPwd);
            $user->setPassword($newPwd);
            $this->persistToTable($user);
        } else {
            return $this->templating->render('account/edit_password.html.twig', [
                'form' => $form->createView(),
                'error' => 'Неправильный пароль.'
            ]);
        }
    }

    public function submit(int $id)
    {
        $sellerRequest = $this->manager->getRepository(SellerRequests::class)->find($id);
        if ($sellerRequest->getFile())
        {
            $filesystem = new Filesystem();
            $filesystem->remove(str_replace('/src/Service', '', __DIR__ . '/public/assets/request_doc/' . $sellerRequest->getFile()));
        }

        $user = $sellerRequest->getUser();
        $user->setRoles(User::ROLE_SELLER_MANAGER);
        $user->setSeller($sellerRequest->getSeller());
        $this->manager->persist($user);
        $this->manager->remove($sellerRequest);
        $this->manager->flush();

        $message = (new \Swift_Message('Заявка на роль менеджера.'))
            ->setFrom('delivery.dev@gmail.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->templating->render(
                    'email/request_submit.html.twig',
                    [
                        'seller' => $user->getSeller(),
                        'name' => $user->getLogin(),
                    ]
                ),
                'text/html'
            );
        $this->mailer->send($message);
    }

    public function cancel(int $id)
    {
        $sellerRequest = $this->manager->getRepository(SellerRequests::class)->find($id);
        if ($sellerRequest->getFile())
        {
            $filesystem = new Filesystem();
            $filesystem->remove(str_replace('/src/Service', '', __DIR__ . '/public/assets/request_doc/' . $sellerRequest->getFile()));
        }

        $user = $sellerRequest->getUser();
        $this->removeFromTable($sellerRequest);

        $message = (new \Swift_Message('Заявка на роль менеджера.'))
            ->setFrom('delivery.dev@gmail.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->templating->render(
                    'email/request_cancel.html.twig',
                    [
                        'seller' => $user->getSeller(),
                        'name' => $user->getLogin(),
                    ]
                ),
                'text/html'
            );
        $this->mailer->send($message);
    }

    public function getManagers(User $user, Request $request)
    {
        $managers = $this->manager->getRepository(User::class)
            ->findBySeller($user->getSeller()->getId(), $request->get('page'));
        $managersClear = [];
        foreach ($managers as $manager) {
            if ($manager !== $user) {
                $managersClear[] = $manager;
            }
        }

        return $managersClear;
    }

    public function checkProduct(Product $product)
    {
        $checkouts = $this->manager->getRepository(CheckoutProduct::class)->findBy(['product' => $product]);
        return $checkouts;
    }
}