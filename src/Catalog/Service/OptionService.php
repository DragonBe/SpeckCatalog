<?php

namespace Catalog\Service;

class OptionService extends ServiceAbstract
{
    protected $choiceService;
    protected $productService;
    protected $imageService;
    
    public function _populateModel($option)
    {
        $optionId = $option->getRecordId();
        $parentProducts = $this->getProductService()->getProductsByChildOptionId($optionId);
        $option->setParentProducts($parentProducts);
        $parentChoices = $this->getChoiceService()->getChoicesByChildOptionId($optionId);
        $option->setParentChoices($parentChoices);
        $option->setImages($this->getImageService()->getImagesByOptionId($optionId));
        
        $choices = $this->getChoiceService()->getChoicesByParentOptionId($optionId);
        if($choices){
            $option->setChoices($choices);
        }
        return $option;
    }
    
    public function getOptionsByProductId($productId)
    {
        $options = $this->getModelMapper()->getOptionsByProductId($productId);
        return $this->populateModels($options);
    }

    public function getOptionsByChoiceId($choiceId)
    {
        $options = $this->modelMapper->getOptionsByChoiceId($choiceId);
        return $this->populateModels($options);
    }

    public function updateSortOrder($parent, $order)
    {
        if('product' === $parent){
            $this->getModelMapper()->updateProductOptionSortOrder($order);
        }
        if('choice' === $parent){
            $this->getModelMapper()->updateChoiceOptionSortOrder($order);
        }
    }

    public function linkParentChoice($choiceId, $optionId)
    {
        return $this->getModelMapper()->linkOptionToChoice($choiceId, $optionId);
    }
    
    public function linkParentProduct($productId, $optionId)
    {
        return $this->getModelMapper()->linkOptionToProduct($productId, $optionId);
    }    

    public function getChoiceService()
    {
        if(null === $this->choiceService){
            $this->choiceService = $this->getServiceManager()->get('catalog_choice_service');
        }
        return $this->choiceService;  
    }

    public function getProductService()
    {
        if(null === $this->productService){
            $this->productService = $this->getServiceManager()->get('catalog_product_service');
        }
        return $this->productService;   
    }

    public function getImageService()
    {
        if(null === $this->imageService){
            $this->imageService = $this->getServiceManager()->get('catalog_image_service');
        }
        return $this->imageService;  
    }

}
