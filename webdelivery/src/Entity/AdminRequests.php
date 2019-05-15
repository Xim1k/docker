<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AdminRequestsRepository")
 *
 */
class AdminRequests
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(
     *     message="Пожалуйста, введите название компании."
     * )
     * @Assert\Length(
     *      max = 50,
     *      maxMessage = "Название вашей компании должно быть короче {{ limit }} символов."
     * )
     */
    private $company_name;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank(
     *     message="Пожалуйста, опишите вашу компанию."
     * )
     */
    private $company_description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\File(mimeTypes={ "application/pdf" },
     *      mimeTypesMessage = "Файл должен иметь расширение .pdf",
     *      maxSize="2M",
     *      maxSizeMessage="Размер файла должен быть меньше {{ limit }} {{ suffix }}.",
     *     notFoundMessage="Файл не найден")
     */
    private $company_file;

    /**
     * @ORM\Column(type="string", length=255)
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
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(
     *     message="Пожалуйста, введите адрес компании."
     * )
     */
    private $company_address;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(
     *     message="Пожалуйста, введите ваше имя."
     * )
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(
     *     message="Пожалуйста, введите вашу фамилию."
     * )
     */
    private $surname;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompanyName(): ?string
    {
        return $this->company_name;
    }

    public function setCompanyName(string $company_name): self
    {
        $this->company_name = $company_name;

        return $this;
    }

    public function getCompanyDescription(): ?string
    {
        return $this->company_description;
    }

    public function setCompanyDescription(?string $company_description): self
    {
        $this->company_description = $company_description;

        return $this;
    }

    public function getCompanyFile(): ?string
    {
        return $this->company_file;
    }

    public function setCompanyFile(?string $company_file): self
    {
        $this->company_file = $company_file;

        return $this;
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

    public function getCompanyAddress(): ?string
    {
        return $this->company_address;
    }

    public function setCompanyAddress(string $company_address): self
    {
        $this->company_address = $company_address;

        return $this;
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
