<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * BillPlanType
 *
 * @ORM\Table(name="bill_plan_type")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BillPlanTypeRepository")
 */
class BillPlanType
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
     * @var BillType
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BillType")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $billType;

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
     * @return BillPlanType
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
     * @return BillType
     */
    public function getBillType()
    {
        return $this->billType;
    }

    /**
     * @param BillType $billType
     * @return BillPlanType
     */
    public function setBillType(BillType $billType)
    {
        $this->billType = $billType;
        return $this;
    }
}

