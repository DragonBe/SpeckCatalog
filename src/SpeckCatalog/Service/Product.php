<?php

namespace SpeckCatalog\Service;

class Product extends AbstractService
{
    protected $entityMapper = 'speckcatalog_product_mapper';
    protected $optionService;
    protected $productUomService;
    protected $imageService;
    protected $documentService;
    protected $specService;
    protected $companyService;

    public function find(array $data, $populate=false, $recursive=false)
    {
        $product = $this->getEntityMapper()->find($data);
        if($populate) {
            $this->populate($product, $recursive);
        }
        return $product;
    }

    public function update($dataOrModel, array $originalVals = null)
    {
        if (null === $originalVals && is_array($dataOrModel)) {
            $originalVals['product_id'] = $dataOrModel['product_id'];
        }
        if (null === $originalVals && $dataOrModel instanceOf \SpeckCatalog\Model\Product) {
            $originalVals['product_id'] = $dataOrModel->getProductId();
        }
        return parent::update($dataOrModel, $originalVals);
    }

    public function getCrubs($product)
    {
        $crumbs = array($product->getName());
        return $this->getEntityMapper()->getCrumbs($product, $crumbs);
    }

    public function getFullProduct($productId)
    {
        $product = $this->find(array('product_id' => $productId), true, true);
        $this->populate($product, true);
        return $product;
    }

    public function populate($product, $recursive=false)
    {
        $productId = $product->getProductId();

        $options = $this->getOptionService()->getByProductId($productId, true, $recursive);
        $product->setOptions($options);

        $builders = $this->getOptionService()->getBuildersByProductId($productId);
        $product->setBuilders($builders);

        $images = $this->getImageService()->getImages('product', $productId);
        $product->setImages($images);

        $documents = $this->getDocumentService()->getDocuments($productId);
        $product->setDocuments($documents);

        $uoms = $this->getProductUomService()->getByProductId($productId, true, $recursive);
        $product->setUoms($uoms);

        $specs = $this->getSpecService()->getByProductId($productId);
        $product->setSpecs($specs);

        $manufacturer = $this->getCompanyService()->findById($product->getManufacturerId());
        $product->setManufacturer($manufacturer);
    }

    public function getBuilderProductsForEdit(array $productIds = array())
    {
        return $this->getEntityMapper()->getProductsById($productIds);
    }

    public function getAllChoicesByProductId($productId)
    {
        $options = $this->getOptionService()->getByProductId($productId, true, true);
        $choices = $this->getEndChoices($options);
        foreach($choices as $choiceId => $choice) {
            $return[$choiceId] = $this->prependParentString($choice);
        }
        return $return;
    }

    public function prependParentString(\SpeckCatalog\Model\AbstractModel $model, $str='')
    {
        $str = $model->__toString() . ($str ? " &gt; {$str}" : "");
        if($model->has('parent')) {
            $str = $this->prependParentString($model->getParent(), $str);
        }
        return $str;
    }

    public function getEndChoices($options, array $choiceIds = array())
    {
        foreach ($options as $option) {
            if($option->has('choices')) {
                foreach ($option->getChoices() as $choice) {
                    if ($choice->has('options')) {
                        $getEndChoices($choice->getOptions(), $choiceIds);
                    } else {
                        $choiceIds[$choice->getChoiceId()] = $choice;
                    }
                }
            }
        }
        return $choiceIds;
    }


    public function addOption($productOrId, $optionOrId)
    {
        $productId = ( is_int($productOrId) ? $productOrId : $productOrId->getProductId() );
        $optionId = ( is_int($optionOrId) ? $optionOrId : $optionOrId->getOptionId() );

        $this->getEntityMapper()->addOption($productId, $optionId);

        return $this->getOptionService()->find(array('option_id' => $optionId));
    }

    public function sortOptions($productId, $order)
    {
        return $this->getEntityMapper()->sortOptions($productId, $order);
    }

    public function removeOption(array $product, array $option)
    {
        $productId = $product['product_id'];
        $optionId = $option['option_id'];

        return $this->getEntityMapper()->removeOption($productId, $optionId);
    }

    public function insert($product)
    {
        $id = parent::insert($product);
        return $this->find(array('product_id' => $id));
    }

    public function populateForPricing($product)
    {
        //recursive options (only whats needed for pricing)
        //productUoms, Availabilities
        //
    }

    public function getByCategoryId($categoryId, $paginatorOptions=array())
    {
        return $this->usePaginator($paginatorOptions)
            ->getEntityMapper()
            ->getByCategoryId($categoryId);
    }

    /**
     * @return optionService
     */
    public function getOptionService()
    {
        if (null === $this->optionService) {
            $this->optionService = $this->getServiceLocator()->get('speckcatalog_option_service');
        }
        return $this->optionService;
    }

    /**
     * @param $optionService
     * @return self
     */
    public function setOptionService($optionService)
    {
        $this->optionService = $optionService;
        return $this;
    }

    /**
     * @return productUomService
     */
    public function getProductUomService()
    {
        if (null === $this->productUomService) {
            $this->productUomService = $this->getServiceLocator()->get('speckcatalog_product_uom_service');
        }
        return $this->productUomService;
    }

    /**
     * @param $productUomService
     * @return self
     */
    public function setProductUomService($productUomService)
    {
        $this->productUomService = $productUomService;
        return $this;
    }

    /**
     * @return imageService
     */
    public function getImageService()
    {
        if (null === $this->imageService) {
            $this->imageService = $this->getServiceLocator()->get('speckcatalog_product_image_service');
        }
        return $this->imageService;
    }

    /**
     * @param $imageService
     * @return self
     */
    public function setImageService($imageService)
    {
        $this->imageService = $imageService;
        return $this;
    }

    /**
     * @return documentService
     */
    public function getDocumentService()
    {
        if (null === $this->documentService) {
            $this->documentService = $this->getServiceLocator()->get('speckcatalog_document_service');
        }
        return $this->documentService;
    }

    /**
     * @param $documentService
     * @return self
     */
    public function setDocumentService($documentService)
    {
        $this->documentService = $documentService;
        return $this;
    }

    /**
     * @return specService
     */
    public function getSpecService()
    {
        if (null === $this->specService) {
            $this->specService = $this->getServiceLocator()->get('speckcatalog_spec_service');
        }
        return $this->specService;
    }

    /**
     * @param $specService
     * @return self
     */
    public function setSpecService($specService)
    {
        $this->specService = $specService;
        return $this;
    }

    /**
     * @return companyService
     */
    public function getCompanyService()
    {
        if (null === $this->companyService) {
            $this->companyService = $this->getServiceLocator()->get('speckcatalog_company_service');
        }
        return $this->companyService;
    }

    /**
     * @param $companyService
     * @return self
     */
    public function setCompanyService($companyService)
    {
        $this->companyService = $companyService;
        return $this;
    }
}
