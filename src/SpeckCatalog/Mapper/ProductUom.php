<?php

namespace SpeckCatalog\Mapper;

class ProductUom extends AbstractMapper
{
    protected $tableName = 'catalog_product_uom';
    protected $dbModel = '\SpeckCatalog\Model\ProductUom';
    protected $relationalModel = '\SpeckCatalog\Model\ProductUom\Relational';
    protected $key = array('product_id', 'uom_code', 'quantity');

    public function find(array $data)
    {
        $where = array(
            'product_id' => $data['product_id'],
            'uom_code'   => $data['uom_code'],
            'quantity'   => $data['quantity'],
        );
        return parent::find($where);
    }

    public function getByProductId($productId)
    {
        $select = $this->getSelect()
            ->where(array('product_id' => $productId))
            ->order('quantity');
        return $this->selectMany($select);
    }
}
