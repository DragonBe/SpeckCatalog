<?php
namespace Catalog\Service;
class ImageService extends MediaServiceAbstract
{
    public function getImagesByProductId($productId)
    {
        $images = $this->getModelMapper()->getMediaByProductId($productId);
        foreach($images as $i => $image){
            $images[$i] = $this->populateModel($image);
        }
        return $images;
    }
    
    public function updateSortOrder($parent, $order)
    {
        $this->getModelMapper()->updateProductImageSortOrder($order);
    }   

}
