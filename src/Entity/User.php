<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`users`')]
#[UniqueConstraint(name: 'UNIQ_IDENTIFIER_NAME', fields: ['name'])]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(security: "is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_COMPANY_ADMIN')"),
        new Delete(security: "is_granted('ROLE_SUPER_ADMIN')"),
        new Patch(
            securityPostValidation: "is_granted('POST_UPDATE', previous_object)"
        ),
    ]
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const ROLES = ["ROLE_USER", "ROLE_ADMIN", "ROLE_SUPER_ADMIN"];

    public const ROLE_USER          = "ROLE_USER";
    public const ROLE_COMPANY_ADMIN = "ROLE_COMPANY_ADMIN";
    public const ROLE_SUPER_ADMIN   = "ROLE_SUPER_ADMIN";


    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\Length(
        min: 3,
        max: 100,
        minMessage: 'User name must be at least {{ limit }} characters long',
        maxMessage: 'User name cannot be longer than {{ limit }} characters',
    )]
    #[Assert\Regex('/^[A-Z][a-z]*$/', "Requires letters and space only, one uppercase letter required.")]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\Count(null, min: 1, max: 3, minMessage: "You must specify at least one OrderItem")]
    #[Assert\Choice(choices: self::ROLES, multiple: true, message: "Please choice valid role!")]
    private array|null $roles = [User::ROLE_USER];

    #[ORM\ManyToOne(inversedBy: 'users')]
    private ?Company $company = null;


    /**
     * @var ?string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    /**
     * @var Collection<int, ApiToken>
     */
    #[ORM\OneToMany(targetEntity: ApiToken::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $apiTokens;

    public function __construct()
    {
        $this->apiTokens = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->name;
    }

    /**
     * @return array
     * @see UserInterface
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param  list<string>  $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function addApiToken(ApiToken $apiToken): static
    {
        if (!$this->apiTokens->contains($apiToken)) {
            $this->apiTokens->add($apiToken);
            $apiToken->setUser($this);
        }

        return $this;
    }

    public function removeApiToken(ApiToken $apiToken): static
    {
        if ($this->apiTokens->removeElement($apiToken)) {
            // set the owning side to null (unless already changed)
            if ($apiToken->getUser() === $this) {
                $apiToken->setUser(null);
            }
        }

        return $this;
    }

    public function getCompany(): ?\App\Entity\Company
    {
        return $this->company;
    }

    public function setCompany(?\App\Entity\Company $company): static
    {
        $this->company = $company;
        return $this;
    }
}
