<?php

namespace Catalog\Model\Mapper;

use Catalog\Model\Product, 
    ArrayObject;

class ProductMapper extends ModelMapperAbstract
{
    protected $childOptionLinkerTable;
    protected $parentCategoryLinkerTable;
    
    public function getModel($constructor = null)
    {
        return new Product($constructor);
    }
    
    public function getProductsByCategoryId($categoryId)
    {
        $db = $this->getReadAdapter();
        $sql = $db->select()
                  ->from('catalog_category_product_linker')
                  ->join($this->getTableName(), 'catalog_category_product_linker.product_id = '.$this->getTableName().'.product_id') 
                  ->where('category_id = ?', $categoryId);
        return $this->selectMany($select);
    }
    
    public function getProductsByChildOptionId($optionId)
    {
        $linkerName = $this->getChildOptionLinkerTable()->getTableName();
        $select = $this->newSelect();
        $select->from($this->getTableName())
            ->join($linkerName, $this->getTableName() . '.record_id = '. $linkerName .'.product_id')
            ->where(array('option_id' => $optionId));
            //->order('sort_weight DESC');
        return $this->selectMany($select);
    }

    public function linkParentCategory($categoryId, $productId)
    {
        $db = $this->getReadAdapter();
        $sql = $db->select()
            ->from('catalog_category_product_linker')
            ->where('category_id = ?', $categoryId)
            ->where('product_id = ?', $productId);
        $this->events()->trigger(__FUNCTION__, $this, array('query' => $sql));
        $row = $db->fetchRow($sql);
        if(false === $row){
            $data = new ArrayObject(array(
                'category_id'  => $categoryId,
                'product_id' => $productId,
            ));
            $result = $db->insert('catalog_category_product_linker', (array) $data);
            if($result !== 1){
                var_dump($result);
                die('something didnt work!');
            }
        }
    }

    public function getChildOptionLinkerTable()
    {
        if(null === $this->childOptionLinkerTable){
            $this->childOptionLinkerTable = $this->getServiceManager()->get('catalog_product_option_linker_tg');
        }
        return $this->childOptionLinkerTable;
    }

    public function setChildOptionLinkerTable($childOptionLinkerTable)
    {
        $this->childOptionLinkerTable = $childOptionLinkerTable;
        return $this;
    }

    public function getParentCategoryLinkerTable()
    {
        if(null === $this->parentCategoryLinkerTable){
            $this->parentCategoryLinkerTable = $this->getServiceManager()->get('catalog_category_product_linker_tg');
        }
        return $this->parentCategoryLinkerTable;
    }

    public function setParentCategoryLinkerTable($parentCategoryLinkerTable)
    {
        $this->parentCategoryLinkerTable = $parentCategoryLinkerTable;
        return $this;
    }
}
