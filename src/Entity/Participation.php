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

    #[ORM\Column(nullable: true)]
    private ?float $prixEncheri = null;

    #[ORM\Column(nullable: true)]
    private ?float $budgetMaximum = null;

    #[ORM\ManyToOne(inversedBy: 'lesParticipations')]
    private ?Enchere $laEnchere = null;

    #[ORM\ManyToOne(inversedBy: 'lesParticipations')]
    private ?User $leUser = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrixEncheri(): ?float
    {
        return $this->prixEncheri;
    }

    public function setPrixEncheri(?float $prixEncheri): static
    {
        $this->prixEncheri = $prixEncheri;

        return $this;
    }

    public function getBudgetMaximum(): ?float
    {
        return $this->budgetMaximum;
    }

    public function setBudgetMaximum(?float $budgetMaximum): static
    {
        $this->budgetMaximum = $budgetMaximum;

        return $this;
    }

    public function getLaEnchere(): ?Enchere
    {
        return $this->laEnchere;
    }

    public function setLaEnchere(?Enchere $laEnchere): static
    {
        $this->laEnchere = $laEnchere;

        return $this;
    }

    public function getLeUser(): ?User
    {
        return $this->leUser;
    }

    public function setLeUser(?User $leUser): static
    {
        $this->leUser = $leUser;

        return $this;
    }

}
