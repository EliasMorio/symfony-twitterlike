<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="twt_user")
 * @method string getUserIdentifier()
 * @uniqueEntity(fields={"username"}, message="Le nom d'utilisateur est déjà utilisé")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     * @Assert\NotBlank(message="Le nom d'utilisateur est obligatoire.")
     * @Assert\Length(min=3, minMessage="Le nom d'utilisateur doit faire au moins 3 caractères.")
     * @Assert\Length(max=30, maxMessage="Le nom d'utilisateur doit faire au plus 30 caractères.")
     * @Assert\Regex(pattern="/^[a-zA-Z0-9]+$/", message="Le nom d'utilisateur ne peut contenir que des lettres et des chiffres.")
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le mot de passe doit être renseigné.")
     * @Assert\Length(min=8, minMessage="Le mot de passe doit faire au moins 8 caractères.")
     * @Assert\Length(max=50, maxMessage="Le mot de passe doit faire au plus 50 caractères.")
     */
    private $password;

    /**
     * @Assert\EqualTo(propertyPath="confirmPassword", message="Les mots de passe ne correspondent pas.")
     */
    private $confirmPassword;

    /**
     * @ORM\OneToMany(targetEntity=Tweet::class, mappedBy="author", orphanRemoval=true)
     */
    private $tweets;

    public function __construct()
    {
        $this->tweets = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getConfirmPassword()
    {
        return $this->confirmPassword;
    }

    /**
     * @param mixed $confirmPassword
     */
    public function setConfirmPassword($confirmPassword): void
    {
        $this->confirmPassword = $confirmPassword;
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * @return Collection<int, Tweet>
     */
    public function getTweets(): Collection
    {
        return $this->tweets;
    }

    public function addTweet(Tweet $tweet): self
    {
        if (!$this->tweets->contains($tweet)) {
            $this->tweets[] = $tweet;
            $tweet->setAuthor($this);
        }

        return $this;
    }

    public function removeTweet(Tweet $tweet): self
    {
        if ($this->tweets->removeElement($tweet)) {
            // set the owning side to null (unless already changed)
            if ($tweet->getAuthor() === $this) {
                $tweet->setAuthor(null);
            }
        }

        return $this;
    }
}
