<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Bill
 *
 * @ORM\Table(name="bill")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BillRepository")
 */
class Bill
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
     * @var float
     *
     * @ORM\Column(name="amount", type="float")
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="string", length=255)
     */
    private $note;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="date")
     */
    private $createdAt;

    /**
     * @var BillStatus
     * 
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BillStatus")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $billStatus;

    /**
     * @var BillPlan
     * 
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BillPlan")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $billPlan;

    /**
     * @var BillType
     * 
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BillType")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $billType;

    /**
     * @var Bank
     * 
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Bank")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $bank;

    /**
     * @var BillInstallments
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\BillInstallments", mappedBy="bill")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $billInstallments;

    /**
     * Bill constructor.
     */
    public function __construct()
    {
        $this->billInstallments = new ArrayCollection();
    }

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
     * @return Bill
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
     * Set amount
     *
     * @param float $amount
     *
     * @return Bill
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set note
     *
     * @param string $note
     *
     * @return Bill
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note
     *
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Bill
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return BillStatus
     */
    public function getBillStatus()
    {
        return $this->billStatus;
    }

    /**
     * @param BillStatus $billStatus
     * @return Bill
     */
    public function setBillStatus(BillStatus $billStatus)
    {
        $this->billStatus = $billStatus;
        return $this;
    }

    /**
     * @return BillPlan
     */
    public function getBillPlan()
    {
        return $this->billPlan;
    }

    /**
     * @param BillPlan $billPlan
     * @return Bill
     */
    public function setBillPlan(BillPlan $billPlan)
    {
        $this->billPlan = $billPlan;
        return $this;
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
     * @return Bill
     */
    public function setBillType(BillType $billType)
    {
        $this->billType = $billType;
        return $this;
    }

    /**
     * @return Bank
     */
    public function getBank()
    {
        return $this->bank;
    }

    /**
     * @param Bank $bank
     * @return Bill
     */
    public function setBank(Bank $bank)
    {
        $this->bank = $bank;
        return $this;
    }

    /**
     * @return ArrayCollection|BillInstallments[]
     */
    public function getBillInstallments()
    {
        return $this->billInstallments;
    }
}

