<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * BillInstallments
 *
 * @ORM\Table(name="bill_installments")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BillInstallmentsRepository")
 */
class BillInstallments
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
     * @var \DateTime
     *
     * @ORM\Column(name="dueDateAt", type="date")
     */
    private $dueDateAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="paymentDateAt", type="date", nullable=true)
     */
    private $paymentDateAt;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float")
     */
    private $amount;

    /**
     * @var float
     *
     * @ORM\Column(name="amountPaid", type="float", nullable=true)
     */
    private $amountPaid;

    /**
     * @var PaymentMethod
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\PaymentMethod")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $paymentMethod;

    /**
     * @var Bill
     * 
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Bill", inversedBy="billInstallments")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $bill;

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
     * Set dueDateAt
     *
     * @param \DateTime $dueDateAt
     *
     * @return BillInstallments
     */
    public function setDueDateAt($dueDateAt)
    {
        $this->dueDateAt = $dueDateAt;

        return $this;
    }

    /**
     * Get dueDateAt
     *
     * @return \DateTime
     */
    public function getDueDateAt()
    {
        return $this->dueDateAt;
    }

    /**
     * Set paymentDateAt
     *
     * @param \DateTime $paymentDateAt
     *
     * @return BillInstallments
     */
    public function setPaymentDateAt($paymentDateAt)
    {
        $this->paymentDateAt = $paymentDateAt;

        return $this;
    }

    /**
     * Get paymentDateAt
     *
     * @return \DateTime
     */
    public function getPaymentDateAt()
    {
        return $this->paymentDateAt;
    }

    /**
     * Set amount
     *
     * @param float $amount
     *
     * @return BillInstallments
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
     * Set amountPaid
     *
     * @param float $amountPaid
     *
     * @return BillInstallments
     */
    public function setAmountPaid($amountPaid)
    {
        $this->amountPaid = $amountPaid;

        return $this;
    }

    /**
     * Get amountPaid
     *
     * @return float
     */
    public function getAmountPaid()
    {
        return $this->amountPaid;
    }
}

