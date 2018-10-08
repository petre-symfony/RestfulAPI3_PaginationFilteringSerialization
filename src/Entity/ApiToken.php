<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="battle_api_token")
 * @ORM\Entity(repositoryClass="App\Repository\ApiTokenRepository")
 */
class ApiToken {
  /**
   * @ORM\Id()
   * @ORM\GeneratedValue()
   * @ORM\Column(type="integer")
   */
  private $id;

  /**
   * @ORM\Column(type="string", length=100)
   */
  private $token;

  /**
   * @Assert\NotBlank(message="Please add some notes about this token")
   * @ORM\Column(type="text")
   */
  private $notes;

  /**
   * @ORM\ManyToOne(targetEntity="App\Entity\User")
   * @ORM\JoinColumn(nullable=false)
   */
  private $user;

  /**
   * @ORM\Column(type="datetime")
   */
  private $createdAt;

	public function __construct(User $user) {
		$this->user = $user;
		$this->createdAt = new \DateTime();
		$this->token = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
	}

  public function getId(): ?int {
    return $this->id;
  }

	/**
  * Sets the token - used in testing, should just be set automatically
  *
  * @param string $token
  * @internal
  */
  public function getToken(): ?string {
    return $this->token;
  }

  public function setToken(string $token): self {
    $this->token = $token;

    return $this;
  }

  public function getNotes(): ?string {
    return $this->notes;
  }

  public function setNotes(string $notes): self {
    $this->notes = $notes;

    return $this;
  }

  public function getUser(): ?User {
    return $this->user;
  }


  public function getCreatedAt(): ?\DateTimeInterface {
    return $this->createdAt;
  }

}
