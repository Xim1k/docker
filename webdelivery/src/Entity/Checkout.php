<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CheckoutRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Checkout
{
    const STATUS_WAIT = 'wait';
    const STATUS_ACCEPT = 'accepted';
    const STATUS_CANCEL = 'cancel';
    const STATUS_DONE = 'done';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $address;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Seller", inversedBy="checkouts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $seller;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="checkouts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="integer")
     */
    private $cost;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CheckoutProduct", mappedBy="checkout", orphanRemoval=true)
     */
    private $checkoutProducts;

    public function __construct()
    {
        $this->checkoutProducts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }


    public function getSeller(): ?Seller
    {
        return $this->seller;
    }

    public function setSeller(?Seller $seller): self
    {
        $this->seller = $seller;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $CreatedAt): self
    {
        $this->createdAt = $CreatedAt;

        return $this;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        $this->createdAt = new \DateTime();
    }

    public function getCost(): ?int
    {
        return $this->cost;
    }

    public function setCost(int $cost): self
    {
        $this->cost = $cost;

        return $this;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCostAtValue()
    {
        $checkoutProducts = $this->getCheckoutProducts();
        $cost = 0;

        foreach ($checkoutProducts as $item)
        {
            $cost += $item->getCount() * $item->getProduct()->getPrice();
        }

        $this->cost = $cost;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @ORM\PrePersist
     */
    public function setStatusWait()
    {
        $this->status = self::STATUS_WAIT;
    }

    /**
     * @return Collection|CheckoutProduct[]
     */
    public function getCheckoutProducts(): Collection
    {
        return $this->checkoutProducts;
    }

    public function addCheckoutProduct(CheckoutProduct $checkoutProduct): self
    {
        if (!$this->checkoutProducts->contains($checkoutProduct)) {
            $this->checkoutProducts[] = $checkoutProduct;
            $checkoutProduct->setCheckout($this);
        }

        return $this;
    }

    public function removeCheckoutProduct(CheckoutProduct $checkoutProduct): self
    {
        if ($this->checkoutProducts->contains($checkoutProduct)) {
            $this->checkoutProducts->removeElement($checkoutProduct);
            // set the owning side to null (unless already changed)
            if ($checkoutProduct->getCheckout() === $this) {
                $checkoutProduct->setCheckout(null);
            }
        }

        return $this;
    }

}
