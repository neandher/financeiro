<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BillPlan
 *
 * @ORM\Table(name="bill_plan")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BillPlanRepository")
 */
class BillPlan
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
     */
    private $description;

    /**
     * @var BillPlanType
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BillPlanType")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $billPlanType;


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
     * @return BillPlan
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
     * @return BillPlanType
     */
    public function getBillPlanType()
    {
        return $this->billPlanType;
    }

    /**
     * @param BillPlanType $billPlanType
     */
    public function setBillPlanType(BillPlanType $billPlanType)
    {
        $this->billPlanType = $billPlanType;
    }
}

