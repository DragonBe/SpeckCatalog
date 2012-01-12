<?php

namespace SpeckCatalog\Entity;

use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="catalog_availability")
 */
class Availability extends RevisionAbstract
{
    /**
     * @ORM\Id
     * @ORM\Column(name="availability_id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $availabilityId;

    /**
     * @ORM\ManyToOne(targetEntity="ItemUom")
     * @ORM\JoinColumn(name="item_uom_id", referencedColumnName="item_uom_id")
     */
    protected $parentItemUom;


    /**
     * @ORM\OneToOne(targetEntity="Company")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="company_id")
     */
    protected $distributor;

    /**
     * @ORM\Column(type="decimal")
     */
    protected $cost = 0;

    protected $parentProductUomId;

    protected $distributorId;

    public function getQuantity()
    {
        return $this->quantity;
    }
 
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
        return $this;
    }
 
    public function getCost()
    {
        return $this->cost;
    }
 
    public function setCost($cost)
    {
        $this->cost = (float)$cost;
        return $this;
    }
 
    public function getDistributor()
    {
        return $this->distributor;
    }
 
    public function setDistributor(Company $distributor=null)
    {
        $this->distributor = $distributor;
        return $this;
    }
 
    public function setAvailabilityId($availabilityId)
    {
        $this->availabilityId = $availabilityId;
        return $this;
    }
 
    public function getAvailabilityId()
    {
        return $this->availabilityId;
    }

    public function getName()
    {
        return $this->getCost();
    }
}
