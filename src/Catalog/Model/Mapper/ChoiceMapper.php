<?php

namespace Catalog\Model\Mapper;
use Catalog\Model\Choice, 
    ArrayObject;

class ChoiceMapper extends ModelMapperAbstract
{
    protected $tableName = 'catalog_choice';

    public function getModel($constructor = null)
    {
        return new Choice($constructor);
    }

    public function getChoicesByParentOptionId($optionId)
    {
        $db = $this->getReadAdapter();
        $sql = $db->select()
                  ->from($this->getTableName())
                  ->join('catalog_option_choice_linker', 'catalog_option_choice_linker'.'.choice_id = '.$this->getTableName().'.choice_id') 
                  ->where('option_id = ?', $optionId)
                  ->order('sort_weight DESC');
        $this->events()->trigger(__FUNCTION__, $this, array('query' => $sql));
        $rows = $db->fetchAll($sql);

        return $this->rowsToModels($rows);
    }

    public function getChoicesByChildProductId($productId)
    {
        $db = $this->getReadAdapter();
        $sql = $db->select()
                  ->from($this->getTableName())
                  ->where('product_id = ?', $productId);
        $this->events()->trigger(__FUNCTION__, $this, array('query' => $sql));
        $rows = $db->fetchAll($sql);

        return $this->rowsToModels($rows);
    }

    public function linkParentOption($optionId, $choiceId)
    {
        $db = $this->getReadAdapter();
        $sql = $db->select()
            ->from('catalog_option_choice_linker')
            ->where('option_id = ?', $optionId)
            ->where('choice_id = ?', $choiceId);
        $this->events()->trigger(__FUNCTION__, $this, array('query' => $sql));
        $row = $db->fetchRow($sql);
        if(false === $row){
            $data = new ArrayObject(array(
                'option_id'  => $optionId,
                'choice_id' => $choiceId,
            ));
            $result = $db->insert('catalog_option_choice_linker', (array) $data);
            if($result !== 1){
                var_dump($result);
                die('something didnt work!');
            }
        }
        return $db->lastInsertId();
    }


    public function getChoicesByChildOptionId($choiceId)
    {
        $db = $this->getReadAdapter();
        $sql = $db->select()
            ->from('catalog_choice_option_linker')
            ->join($this->getTableName(), 'catalog_choice_option_linker.choice_id = ' . $this->getTableName() . '.choice_id')
            ->where('choice_id = ?', $choiceId);
        $this->events()->trigger(__FUNCTION__, $this, array('query' => $sql));
        $rows = $db->fetchAll($sql);

        return $this->rowsToModels($rows);   
    } 

    public function updateOptionChoiceSortOrder($order)
    {
        return $this->updateSort('catalog_option_choice_linker', $order);
    }   

    public function removeLinker($linkerId)
    {
        return $this->deleteLinker('catalog_option_choice_linker', $linkerId);
    }     
}
