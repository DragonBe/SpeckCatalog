<?php

namespace Catalog\Service;

class ProductUomService extends ServiceAbstract
{
    protected $availabilityService;
    protected $uomService;

    public function populateModel($productUom)
    {
        $productUom->setAvailabilities(
            $this->getAvailabilityService()->getAvailabilitiesByParentProductUomId($productUom->getProductUomId())
        );
        $productUom->setUoms($this->getUomService()->getAll());
        $productUom->setUom($this->getUomService()->getById($productUom->getUomCode()));

        return $productUom;
    }

    public function newProductProductUom($parentId)
    {
        $productUom = $this->getModelMapper()->newModel();
        $productUom->setParentProductId($parentId);
        $this->modelMapper->update($productUom);
        return $productUom;    
    }

    public function getProductUomsByParentProductId($id)
    {
        $productUoms = $this->modelMapper->getProductUomsByParentProductId($id);
        $return = array();
        foreach ($productUoms as $productUom){
            $return[] = $this->populateModel($productUom);
        }
        return $return;
    }
      
    public function getAvailabilityService()
    {
        return $this->availabilityService;
    }
 
    public function setAvailabilityService($availabilityService)
    {
        $this->availabilityService = $availabilityService;
        return $this;
    }

    public function getUomService()
    {
        return $this->uomService;
    }
 
    public function setUomService($uomService)
    {
        $this->uomService = $uomService;
        return $this;
    }
}
