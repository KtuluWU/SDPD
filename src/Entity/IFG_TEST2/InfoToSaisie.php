<?php

namespace App\Entity\IFG_TEST2;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 */
class InfoToSaisie
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private $siren;

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
}
