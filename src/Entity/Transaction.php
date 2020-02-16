<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TransactionRepository")
 */
class Transaction
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
    private $nomdep;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $teldep;

    /**
     * @ORM\Column(type="integer")
     */
    private $montant;

    /**
     * @ORM\Column(type="datetime")
     */
    private $datetransaction;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="transactions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userTransaction;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Tarif", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $tarifs;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomdep(): ?string
    {
        return $this->nomdep;
    }

    public function setNomdep(string $nomdep): self
    {
        $this->nomdep = $nomdep;

        return $this;
    }

    public function getTeldep(): ?string
    {
        return $this->teldep;
    }

    public function setTeldep(string $teldep): self
    {
        $this->teldep = $teldep;

        return $this;
    }

    public function getMontant(): ?int
    {
        return $this->montant;
    }

    public function setMontant(int $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getDatetransaction(): ?\DateTimeInterface
    {
        return $this->datetransaction;
    }

    public function setDatetransaction(\DateTimeInterface $datetransaction): self
    {
        $this->datetransaction = $datetransaction;

        return $this;
    }

    public function getUserTransaction(): ?User
    {
        return $this->userTransaction;
    }

    public function setUserTransaction(?User $userTransaction): self
    {
        $this->userTransaction = $userTransaction;

        return $this;
    }

    public function getTarifs(): ?Tarif
    {
        return $this->tarifs;
    }

    public function setTarifs(Tarif $tarifs): self
    {
        $this->tarifs = $tarifs;

        return $this;
    }
}
