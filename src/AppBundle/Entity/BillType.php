<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * BillType
 *
 * @ORM\Table(name="bill_type")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BillTypeRepository")
 */
class BillType
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="referency", type="string", length=255)
     */
    private $referency;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return BillType
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set referency
     *
     * @param string $referency
     *
     * @return BillType
     */
    public function setReferency($referency)
    {
        $this->referency = $referency;

        return $this;
    }

    /**
     * Get referency
     *
     * @return string
     */
    public function getReferency()
    {
        return $this->referency;
    }
}

