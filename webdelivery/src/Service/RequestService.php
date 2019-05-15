<?php


namespace App\Service;


use App\Entity\AdminRequests;
use App\Entity\Seller;
use App\Entity\SellerRequests;
use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormInterface;

class RequestService
{
    private $manager;
    private $directory;

    public function __construct(ObjectManager $manager, $directory)
    {
        $this->manager = $manager;
        $this->directory = $directory;
    }

    public function addNewSeller(FormInterface $form, AdminRequests $adminRequest)
    {
        if ($file = $form->get('company_file')->getData())
        {
            $file = $form->get('company_file')->getData();
            $fileName = time() . uniqid() . '.' . $file->guessExtension();
            $file->move(
                $this->directory,
                $fileName
            );
            $adminRequest->setCompanyFile($fileName);
        }
        $this->manager->persist($adminRequest);
        $this->manager->flush();
    }

    public function addNewManager(FormInterface $form, SellerRequests $sellerRequest, User $thisUser)
    {
        if ($file = $form->get('file')->getData())
        {
            $file = $form->get('file')->getData();
            $fileName = time() . uniqid() . '.' . $file->guessExtension();
            $file->move(
                $this->directory,
                $fileName
            );
            $sellerRequest->setFile($fileName);
        }
        $sellerRequest->setUser($thisUser);
        $this->manager->persist($sellerRequest);
        $this->manager->flush();
    }

    public function checkManager(Seller $seller, User $user)
    {
        $checkManager = $this->manager->getRepository(SellerRequests::class)
            ->findBySellerAndUser($seller->getId(), $user->getId());

        if ($checkManager)
        {
            return false;
        }

        return true;
    }
}