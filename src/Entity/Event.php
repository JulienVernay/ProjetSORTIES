<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EventRepository::class)
 */
class Event
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
    private $name;

    /**
     * @ORM\Column(type="datetime")
     */
    private $startingDateTime;

    /**
     * @ORM\Column(type="datetime")
     */
    private $duration;

    /**
     * @ORM\Column(type="datetime")
     */
    private $inscriptionDeadLine;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbMaxRegistration;

    /**
     * @ORM\Column(type="text")
     */
    private $eventDetails;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="organizedEvents")
     * @ORM\JoinColumn(nullable=false)
     */
    private $organizer;

    /**
     * @ORM\ManyToOne(targetEntity=Location::class, inversedBy="events")
     * @ORM\JoinColumn(nullable=false)
     */
    private $location;

    /**
     * @ORM\ManyToOne(targetEntity=State::class, inversedBy="events")
     * @ORM\JoinColumn(nullable=false)
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity=Campus::class, inversedBy="events")
     * @ORM\JoinColumn(nullable=false)
     */
    private $site;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="registeredEvents")
     */
    private $registeredMembers;

    public function __construct()
    {
        $this->registeredMembers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getStartingDateTime(): ?\DateTimeInterface
    {
        return $this->startingDateTime;
    }

    public function setStartingDateTime(\DateTimeInterface $startingDateTime): self
    {
        $this->startingDateTime = $startingDateTime;

        return $this;
    }

    public function getDuration(): ?\DateTimeInterface
    {
        return $this->duration;
    }

    public function setDuration(\DateTimeInterface $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getInscriptionDeadLine(): ?\DateTimeInterface
    {
        return $this->inscriptionDeadLine;
    }

    public function setInscriptionDeadLine(\DateTimeInterface $inscriptionDeadLine): self
    {
        $this->inscriptionDeadLine = $inscriptionDeadLine;

        return $this;
    }

    public function getNbMaxRegistration(): ?int
    {
        return $this->nbMaxRegistration;
    }

    public function setNbMaxRegistration(int $nbMaxRegistration): self
    {
        $this->nbMaxRegistration = $nbMaxRegistration;

        return $this;
    }

    public function getEventDetails(): ?string
    {
        return $this->eventDetails;
    }

    public function setEventDetails(string $eventDetails): self
    {
        $this->eventDetails = $eventDetails;

        return $this;
    }

    public function addUser(User $user): self
    {
        if (!$this->registeredMembers->contains($user)) {
            $this->registeredMembers[] = $user;
            $user->addRegisteredEvent($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->registeredMembers->removeElement($user)) {
            $user->removeRegisteredEvent($this);
        }

        return $this;
    }

    public function getOrganizer(): ?User
    {
        return $this->organizer;
    }

    public function setOrganizer(?User $organizer): self
    {
        $this->organizer = $organizer;

        return $this;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getStatus(): ?State
    {
        return $this->status;
    }

    public function setStatus(?State $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getSite(): ?Campus
    {
        return $this->site;
    }

    public function setSite(?Campus $site): self
    {
        $this->site = $site;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getRegisteredMembers(): Collection
    {
        return $this->registeredMembers;
    }

    public function addRegisteredMember(User $registeredMember): self
    {
        if (!$this->registeredMembers->contains($registeredMember)) {
            $this->registeredMembers[] = $registeredMember;
            $registeredMember->addRegisteredEvent($this);
        }

        return $this;
    }

    public function removeRegisteredMember(User $registeredMember): self
    {
        if ($this->registeredMembers->removeElement($registeredMember)) {
            $registeredMember->removeRegisteredEvent($this);
        }

        return $this;
    }

    public function __toString()
    {
        // TODO: Implement __toString() method.
    }


}
