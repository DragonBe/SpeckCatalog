<?php

namespace SpeckCatalog\Service;

class ChoiceService extends ServiceAbstract
{
    protected $productService;
    
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
        $product = $this->productService->getById($choice->getProductId());
        if($product){ $choice->setProduct($product); }
        return $choice;
    }
    
    public function newOptionChoice($optionId)
    {
        $choice = $this->modelMapper->newModel();
        $choice->setParentOptionId($optionId);
        //$product = $this->productService->newModel('shell');
        //$choice->setProductId($product->getProductId());
        $this->modelMapper->update($choice);
        //$choice->setProduct($product);
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
}
