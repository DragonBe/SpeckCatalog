<?php

namespace SpeckCatalog\Mapper;

use SpeckCatalog\Model\AbstractModel;
use Zend\Stdlib\Hydrator\HydratorInterface;

class Option extends AbstractMapper
{
    protected $tableName = 'catalog_option';
    protected $model = '\SpeckCatalog\Model\Option\Relational';
    protected $tableKeyFields = array('option_id');
    protected $tableFields = array('option_id', 'name', 'instruction', 'required', 'builder', 'option_type_id');

    public function find(array $data)
    {
        $where = array('option_id' => $data['option_id']);
        return parent::find($where);
    }

    public function getBuildersByProductId($productId)
    {
        $link = 'catalog_product_option';
        $opt = 'catalog_option';
        $build = 'catalog_builder_product';
        $joinOpt = $link . '.option_id = ' . $opt . '.option_id';
        $joinBuild = $opt . '.option_id = ' . $build . '.option_id';
        $where = array(
            $opt . '.builder' => 1,
            $link . '.product_id' => $productId
        );
        $order = array(
            $link . '.product_id',
            $build . '.choice_id'
        );

        $select = $this->getSelect($link)
            ->join($opt, $joinOpt)
            ->join($build, $joinBuild)
            ->where($where)
            ->order($order);
        $result = $this->selectMany($select);
        return $result;
    }

    public function getByProductId($productId)
    {
        $linker = 'catalog_product_option';
        $table = $this->getTableName();
        $joinString = $linker . '.option_id = ' . $table . '.option_id';

        $select = $this->getSelect()
            ->join($linker, $joinString)
            ->where(array('product_id' => (int) $productId))
            ->order('sort_weight', 'ASC');
        return $this->selectManyModels($select);
    }

    public function getByParentChoiceId($choiceId)
    {
        $linker = 'catalog_choice_option';
        $table = $this->getTableName();
        $joinString = $linker . '.option_id = ' . $table . '.option_id';

        $select = $this->getSelect()
            ->join($linker, $joinString)
            ->where(array($linker . '.choice_id' => (int) $choiceId))
            ->order('sort_weight', 'ASC');
        return $this->selectManyModels($select);
    }

    public function sortChoices($optionId, $order)
    {
        $table = 'catalog_choice';
        foreach ($order as $i => $choiceId) {
            $where = array('option_id' => $optionId, 'choice_id' => $choiceId);
            $select = $this->getSelect($table)->where($where);
            $row = $this->queryOne($select);
            $row['sort_weight'] = $i;
            $this->update($row, $where, $table);
        }
    }

    public function insert($option, $tableName=null, HydratorInterface $hydrator=null)
    {
        $optionId = parent::insert($option, $tableName, $hydrator);
        $option = $this->find(array('option_id' => $optionId));

        return $option;
    }
}
