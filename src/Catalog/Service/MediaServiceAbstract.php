<?php
namespace Catalog\Service;
abstract class MediaServiceAbstract extends ServiceAbstract
{
    public function populateModel($model)
    {
        return $model;
    }
    
    public function linkParentProduct($productId, $mediaId)
    {
        $this->getModelMapper()->linkParentProduct($productId, $mediaId);
    }
}
