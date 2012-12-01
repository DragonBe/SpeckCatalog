<?php

namespace SpeckCatalog\Service;

class Availability extends AbstractService
{
    protected $entityMapper = 'speckcatalog_availability_mapper';

    public function getByProductUom($productId, $uomCode, $quantity)
    {
        return $this->getEntityMapper()->getByProductUom($productId, $uomCode, $quantity);
    }

    public function insert($availability)
    {
        parent::insert($availability);
        return $availability;
    }
}
