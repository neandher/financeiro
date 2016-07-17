<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @Assert\NotBlank()
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="amount", type="string", nullable=true)
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="text", nullable=true)
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
     * @Assert\NotNull()
     */
    private $billPlan;

    /**
     * @var BillCategory
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BillCategory")
     * @ORM\JoinColumn(onDelete="SET NULL")
     * @Assert\NotNull()
     */
    private $billCategory;

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
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\BillInstallments", mappedBy="bill", cascade={"persist"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Assert\Count(min="1", minMessage="bill.validator.installments_count")
     * @Assert\Valid()
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
     * @param string $amount
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
     * @return string
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
    public function setBillStatus($billStatus)
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
    public function setBillPlan($billPlan)
    {
        $this->billPlan = $billPlan;
        return $this;
    }

    /**
     * @return BillCategory
     */
    public function getBillCategory()
    {
        return $this->billCategory;
    }

    /**
     * @param BillCategory $billCategory
     * @return Bill
     */
    public function setBillCategory($billCategory)
    {
        $this->billCategory = $billCategory;
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
    public function setBank($bank)
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

    public function addBillInstallment(BillInstallments $billInstallment)
    {
        $billInstallment->setBill($this);
        $this->billInstallments->add($billInstallment);
    }

    public function removeBillInstallment(BillInstallments $billInstallment)
    {
        $this->billInstallments->removeElement($billInstallment);
    }

    /**
     * @return bool
     */
    public function isDateOverDue()
    {
        $isDateOverDue = false;

        foreach ($this->getBillInstallments() as $billInstallment) {
            if ($billInstallment->getPaymentDateAt() === null && $billInstallment->getDueDateAt() < (new \DateTime(date('y-m-d')))) {
                $isDateOverDue = true;
                break;
            }
        }
        return $isDateOverDue;
    }

    public function getInstallmentNextPayment()
    {
        $installmentNextPayment = null;

        $criteria = Criteria::create()->orderBy(['dueDateAt' => Criteria::ASC]);

        foreach ($this->getBillInstallments()->matching($criteria) as $billInstallment) {
            if ($billInstallment->getPaymentDateAt() === null) {
                $installmentNextPayment = $billInstallment;
                break;
            }
        }

        return $installmentNextPayment;
    }

    public function getTotalInstallmentsPaid()
    {
        $i = 0;
        foreach ($this->getBillInstallments() as $billInstallment) {
            if ($billInstallment->getPaymentDateAt() !== null) {
                $i++;
            }
        }
        return $i;
    }
}

