<?php

namespace SpeckCatalog\Service;

class ProductService
{
    protected $productMapper;
    protected $optionMapper;
    
    
    public function getProductById($id)
    {
        $product = $this->productMapper->getProductById($id);
        $options = $this->optionMapper->getOptionsByProductId($id);
        $product->setOptions($options);
        return $product;
    }

    public function getModelsBySearchData($string)
    {
        return $this->productMapper->getModelsBySearchData($string);
    }    

    public function add($product)
    {
        $this->productMapper->add($product);
    }

    public function update($product)
    {
        $this->productMapper->update($product);
    
        if($product->hasOptions()){
            foreach($product->getOptions() as $option){
                if($option->getOptionId()){
                    $this->optionMapper->update($option);
                    $this->optionMapper->linkOptionToProduct($product->getProductId(), $option);
                }else{
                    $option = $this->optionMapper->add($option);
                    $this->optionMapper->linkOptionToProduct($product->getProductId(), $option);
                }
            }
        }
    }

    public function getProductMapper()
    {
        return $this->productMapper;
    }
 
    public function setProductMapper($productMapper)
    {
        $this->productMapper = $productMapper;
        return $this;
    }

    public function getOptionMapper()
    {
        return $this->optionMapper;
    }

    public function setOptionMapper($optionMapper)
    {
        $this->optionMapper = $optionMapper;
        return $this;
    }
}
