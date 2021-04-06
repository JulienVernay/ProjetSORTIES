<?php

namespace App\Entity;

use App\Repository\CampusRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CampusRepository::class)
 */
class Campus
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $campusName;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCampusName(): ?string
    {
        return $this->campusName;
    }

    public function setCampusName(string $campusName): self
    {
        $this->campusName = $campusName;

        return $this;
    }
}
