<?php

namespace App\Entity\IFG_SDPD;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * InfoToSaisie
 */
class InfoToSaisie
{
    /**
     * @var int
     * 
     */
    private $id;

    /**
     * @var int
     * @Assert\NotBlank()
     */
    private $siren;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $numdepot;

    /**
     * @var datetime
     * @Assert\NotBlank()
     */
    private $datedepot;

    public function getId()
    {
        return $this->id;
    }

    public function getSiren()
    {
        return $this->siren;
    }

    public function setSiren($siren): self
    {
        $this->siren = $siren;

        return $this;
    }

    public function getNumdepot(): ?string
    {
        return $this->numdepot;
    }

    public function setNumdepot(string $numdepot): self
    {
        $this->numdepot = $numdepot;

        return $this;
    }

    public function getDatedepot(): ?\DateTimeInterface
    {
        return $this->datedepot;
    }

    public function setDatedepot(\DateTimeInterface $datedepot): self
    {
        $this->datedepot = $datedepot;

        return $this;
    }
}
