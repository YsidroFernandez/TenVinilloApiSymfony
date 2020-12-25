<?php

namespace App\Entity;

use App\Repository\PurchaseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PurchaseRepository::class)
 */
class Purchase
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fecha_compra;

    /**
     * @ORM\Column(type="float")
     */
    private $gastos_envio;

    /**
     * @ORM\Column(type="float")
     */
    private $monto;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="purchases")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=PurchaseDetail::class, mappedBy="purchase")
     */
    private $details;

    public function __construct()
    {
        $this->fecha_compra = new  \DateTime();
        $this->details = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFechaCompra(): ?\DateTimeInterface
    {
        return $this->fecha_compra;
    }

    public function setFechaCompra(\DateTimeInterface $fecha_compra): self
    {
        $this->fecha_compra = $fecha_compra;

        return $this;
    }

    public function getGastosEnvio(): ?float
    {
        return $this->gastos_envio;
    }

    public function setGastosEnvio(float $gastos_envio): self
    {
        $this->gastos_envio = $gastos_envio;

        return $this;
    }

    public function getMonto(): ?float
    {
        return $this->monto;
    }

    public function setMonto(float $monto): self
    {
        $this->monto = $monto;

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

    /**
     * @return Collection|PurchaseDetail[]
     */
    public function getDetails(): Collection
    {
        return $this->details;
    }

    public function addDetail(PurchaseDetail $detail): self
    {
        if (!$this->details->contains($detail)) {
            $this->details[] = $detail;
            $detail->setPurchase($this);
        }

        return $this;
    }

    public function removeDetail(PurchaseDetail $detail): self
    {
        if ($this->details->removeElement($detail)) {
            // set the owning side to null (unless already changed)
            if ($detail->getPurchase() === $this) {
                $detail->setPurchase(null);
            }
        }

        return $this;
    }
}
