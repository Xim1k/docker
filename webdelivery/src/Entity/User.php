<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 * @UniqueEntity(fields={"login"}, message="There is already an account with this login")
 * @ORM\HasLifecycleCallbacks()
 *
 */
class User implements UserInterface
{

    const ROLE_USER = 'ROLE_USER';
    const ROLE_SELLER_MAIN = 'ROLE_SELLER_MAIN';
    const ROLE_SELLER_MANAGER = 'ROLE_SELLER_MANAGER';
    const ROLE_ADMIN = 'ROLE_ADMIN';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank(
     *     message="Пожалуйста, введите email."
     * )
     * @Assert\Email(
     *     message = "Email '{{ value }}' имеет неверный формат.",
     * )
     * @Assert\Length(
     *      max = 180,
     *      maxMessage = "Ваш email должен быть короче {{ limit }} символов."
     * )
     */
    private $email;

    /**
     * @ORM\Column(type="string")
     *
     */
    private $role;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;


    /**
     * @ORM\Column(type="string", length=120, unique=true)
     * @Assert\NotBlank(
     *     message="Пожалуйства, введите свой логин."
     * )
     * @Assert\Length(
     *      min = 6,
     *      max = 120,
     *      minMessage = "Ваш логин должен быть длиннее {{ limit }} символов.",
     *      maxMessage = "Ваш логин должен быть короче {{ limit }} символов."
     * )
     */
    private $login;

    /**
     * @ORM\Column(type="string", length=60, nullable=true)
     */
    private $token;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Seller", inversedBy="users")
     */
    private $seller;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Checkout", mappedBy="user", orphanRemoval=true)
     */
    private $checkouts;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SellerRequests", mappedBy="user", orphanRemoval=true)
     */
    private $requests;

    /**
     * @ORM\Column(type="datetime")
     */
    private $CreatedAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(
     *     message="Пожалуйста, введите email."
     * )
     *
     * @Assert\Length(
     *     max = 180,
     *     maxMessage = "Ваше имя должно быть короче {{ limit }} символов."
     * )
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(
     *     message="Пожалуйста, введите email."
     * )
     * @Assert\Length(
     *     max = 180,
     *     maxMessage = "Ваша фамилия должна быть короче {{ limit }} символов."
     * )
     */
    private $surname;


    public function __construct()
    {
        $this->orders = new ArrayCollection();
        $this->checkouts = new ArrayCollection();
        $this->requests = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->login;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles() : array
    {
        return [$this->role];
    }

    public function setRoles($role): self
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): self
    {
        $this->login = $login;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

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
            $checkout->setUser($this);
        }

        return $this;
    }

    public function removeCheckout(Checkout $checkout): self
    {
        if ($this->checkouts->contains($checkout)) {
            $this->checkouts->removeElement($checkout);
            // set the owning side to null (unless already changed)
            if ($checkout->getUser() === $this) {
                $checkout->setUser(null);
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
            $request->setUser($this);
        }

        return $this;
    }

    public function removeRequest(SellerRequests $request): self
    {
        if ($this->requests->contains($request)) {
            $this->requests->removeElement($request);
            // set the owning side to null (unless already changed)
            if ($request->getUser() === $this) {
                $request->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAt()
    {
        $this->CreatedAt = new \DateTime();
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

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

}
