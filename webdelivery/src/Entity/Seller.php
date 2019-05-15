<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SellerRepository")
 */
class Seller
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     */
    private $address;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Product", mappedBy="seller", orphanRemoval=true)
     */
    private $products;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="seller", orphanRemoval=true)
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Checkout", mappedBy="seller", orphanRemoval=true)
     */
    private $checkouts;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SellerRequests", mappedBy="seller", orphanRemoval=true)
     */
    private $requests;



    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->orders = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->checkouts = new ArrayCollection();
        $this->requests = new ArrayCollection();
    }

    public function __toString(): ?string
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setSeller($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
            // set the owning side to null (unless already changed)
            if ($product->getSeller() === $this) {
                $product->setSeller(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setSeller($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            // set the owning side to null (unless already changed)
            if ($user->getSeller() === $this) {
                $user->setSeller(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Checkout[]
     */
    public function getCheckouts(): Collection
    {
        return $this->checkouts;
    }

    public function addCheckout(Checkout $checkout): self
    {
        if (!$this->checkouts->contains($checkout)) {
            $this->checkouts[] = $checkout;
            $checkout->setSeller($this);
        }

        return $this;
    }

    public function removeCheckout(Checkout $checkout): self
    {
        if ($this->checkouts->contains($checkout)) {
            $this->checkouts->removeElement($checkout);
            // set the owning side to null (unless already changed)
            if ($checkout->getSeller() === $this) {
                $checkout->setSeller(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|SellerRequests[]
     */
    public function getRequests(): Collection
    {
        return $this->requests;
    }

    public function addRequest(SellerRequests $request): self
    {
        if (!$this->requests->contains($request)) {
            $this->requests[] = $request;
            $request->setSeller($this);
        }

        return $this;
    }

    public function removeRequest(SellerRequests $request): self
    {
        if ($this->requests->contains($request)) {
            $this->requests->removeElement($request);
            // set the owning side to null (unless already changed)
            if ($request->getSeller() === $this) {
                $request->setSeller(null);
            }
        }

        return $this;
    }

}
