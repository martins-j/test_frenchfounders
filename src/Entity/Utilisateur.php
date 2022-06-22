<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UtilisateurRepository::class)
 * 
 * @UniqueEntity(fields={"email"}, message="L'adresse mail existe déjà")
 */
class Utilisateur implements UserInterface
{
    /**
     * @var int $id
     * 
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @var string $email
     * 
     * @ORM\Column(type="string", length=180, unique=true)
     * 
     * @Assert\NotBlank(message="L'adresse e-mail ne peut pas être vide.")
     * @Assert\Email(message="{{ value }} ne semble pas être une adresse e-mail valide.")
     * 
     * @Groups("utilisateur")
     */
    private string $email;

    /**
     * @var array $roles
     * 
     * @ORM\Column(type="json")
     * 
     * @Groups("utilisateur")
     */
    private array $roles = [];

    /** 
     * @var string $password
     * 
     * @ORM\Column(type="string", length=255)
     * 
     * @Assert\NotBlank(message="Le mot de passe ne peut pas être vide.")
     * @Assert\Length(min=6, minMessage="Le mot de passe doit comporter au moins {{ limit }} caractères.")
     */
    private string $password;

    /**
     * @var string $nom
     * 
     * @ORM\Column(type="string", length=255)
     * 
     * @Assert\NotBlank(message="Le nom ne peut pas être vide.")
     * 
     * @Groups("utilisateur")
     */
    private string $nom;

    /**
     * @var string $prenom
     * 
     * @ORM\Column(type="string", length=255)
     * 
     * @Assert\NotBlank(message="Le prénom ne peut pas être vide.")
     * 
     * @Groups("utilisateur")
     */
    private string $prenom;

    /**
     * @var string|null $societe
     * 
     * @ORM\Column(type="string", length=255, nullable=true)
     * 
     * @Groups("utilisateur")
     */
    private ?string $societe;

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
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getSociete(): ?string
    {
        return $this->societe;
    }

    public function setSociete(?string $societe): self
    {
        $this->societe = $societe;

        return $this;
    }
}
