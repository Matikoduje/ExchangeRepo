<?php
namespace ExchangeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="ExchangeBundle\Repository\UserRepository")
 * @ORM\Table(name="users")
 * @UniqueEntity(fields={"login"}, message="This login is already used.")
 * @UniqueEntity(fields={"email"}, message="This email is alredy used.")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="login", type="string", length=15, unique=true)
     * @Assert\NotBlank(message="Login should not be blank.")
     * @Assert\Length(
     *     min=5,
     *     max=15,
     *     minMessage="Login is too short. It should have 5 characters or more.",
     *     maxMessage="Login is too long. It should have 15 characters or less."
     * )
     */
    private $login;

    /**
     * @ORM\Column(name="email", type="string", length=254, unique=true)
     * @Assert\NotBlank(message="Email should not be blank.")
     * @Assert\Email(message="This value is not a valid email address.")
     * @Assert\Length(
     *     min=3,
     *     max=254,
     *     minMessage="Email is too short. It should have 3 characters or more.",
     *     maxMessage="Email is too long. It should have 254 characters or less."
     * )
     */
    private $email;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     *
     */
    private $plainPassword;

    /**
     * @ORM\Column(name="roles", type="json_array")
     */
    private $roles;

    /**
     * @ORM\OneToOne(targetEntity="ExchangeBundle\Entity\Wallet")
     * @ORM\JoinColumn(nullable=false)
     */
    private $wallet;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->roles[] = 'ROLE_USER';
    }

    public function getId()
    {
        return $this->id;
    }

    public function getLogin()
    {
        return $this->login;
    }

    public function setLogin($login)
    {
        $this->login = $login;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setWallet(Wallet $wallet)
    {
        $this->wallet = $wallet;
    }

    public function getWallet()
    {
        return $this->wallet;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getSalt()
    {

    }

    public function getUsername()
    {
        return $this->login;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
        $this->password = null;
    }

    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }
}
