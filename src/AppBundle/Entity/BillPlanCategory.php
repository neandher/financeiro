<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * BillPlanCategory
 *
 * @ORM\Table(name="bill_plan_category")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BillPlanCategoryRepository")
 */
class BillPlanCategory
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
     * @var BillCategory
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BillCategory")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $billCategory;

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
     * @return BillPlanCategory
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
     * @return BillCategory
     */
    public function getBillCategory()
    {
        return $this->billCategory;
    }

    /**
     * @param BillCategory $billCategory
     * @return BillPlanCategory
     */
    public function setBillCategory(BillCategory $billCategory)
    {
        $this->billCategory = $billCategory;
        return $this;
    }
}

