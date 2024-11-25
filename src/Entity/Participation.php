<?php

namespace App\Entity;

use App\Repository\ParticipationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParticipationRepository::class)]
class Participation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $prixEncheri = null;

    #[ORM\Column]
    private ?float $budgetMaximum = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrixEncheri(): ?float
    {
        return $this->prixEncheri;
    }

    public function setPrixEncheri(float $prixEncheri): static
    {
        $this->prixEncheri = $prixEncheri;

        return $this;
    }

    public function getBudgetMaximum(): ?float
    {
        return $this->budgetMaximum;
    }

    public function setBudgetMaximum(float $budgetMaximum): static
    {
        $this->budgetMaximum = $budgetMaximum;

        return $this;
    }
}
