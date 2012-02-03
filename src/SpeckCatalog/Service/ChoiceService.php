<?php

namespace SpeckCatalog\Service;

class ChoiceService extends ServiceAbstract
{
    protected $productService;
    protected $optionService;
    
    public function getChoicesByParentOptionId($id)
    {
        $choices = $this->modelMapper->getChoicesByParentOptionId($id);
        $return = array();
        foreach ($choices as $choice){
            $return[] = $this->populateModel($choice);
        }
        return $return;
    }

    public function populateModel($choice)
    {
        if($choice->getProductId()){
            $product = $this->productService->getById($choice->getProductId());
            if($product){ 
                $choice->setProduct($product); 
            }
        }
        $choice->setOptions($this->getOptionService()->getOptionsByChoiceId($choice->getChoiceId()));

        return $choice;
    }
    
    public function newOptionChoice($optionId)
    {
        $choice = $this->newModel();
        $choice->setParentOptionId($optionId);
        $this->update($choice);
        return $choice;
    }

    public function newOptionChoiceWithExistingProduct($optionId, $productId)
    {
        $choice = $this->newModel();
        $choice->setParentOptionId($optionId);
        $choice->setProductId($productId);
        $this->update($choice);
        $choice->setProduct($this->getProductService()->getById($productId));
        return $choice;
    }


    public function getProductService()
    {
        return $this->productService;
    }

    public function setProductService($productService)
    {
        $this->productService = $productService;
        return $this;
    }
 
    public function getOptionService()
    {
        return $this->optionService;
    }
 
    public function setOptionService($optionService)
    {
        $this->optionService = $optionService;
        return $this;
    }
}
