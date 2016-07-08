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
     * @var string
     * 
     * @ORM\Column(name="description", type="string", nullable=true)
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dueDateAt", type="date")
     * @Assert\NotBlank()
     * @Assert\Date()
     */
    private $dueDateAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="paymentDateAt", type="date", nullable=true)
     * @Assert\Date()
     */
    private $paymentDateAt;

    /**
     * @var string
     *
     * @ORM\Column(name="amount", type="string")
     * @Assert\NotBlank()
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="amountPaid", type="string", nullable=true)
     */
    private $amountPaid;

    /**
     * @var PaymentMethod
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\PaymentMethod")
     * @ORM\JoinColumn(onDelete="SET NULL")
     * @Assert\NotNull()
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
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return BillInstallments
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
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
     * @param string $amount
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
     * @return string
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set amountPaid
     *
     * @param string $amountPaid
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
     * @return string
     */
    public function getAmountPaid()
    {
        return $this->amountPaid;
    }

    /**
     * @return PaymentMethod
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * @param PaymentMethod $paymentMethod
     * @return BillInstallments
     */
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
        return $this;
    }

    /**
     * @param Bill $bill
     */
    public function setBill($bill)
    {
        $this->bill = $bill;
    }
}

