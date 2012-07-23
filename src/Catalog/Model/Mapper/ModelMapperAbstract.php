<?php

namespace Catalog\Model\Mapper;

use ZfcBase\Mapper\AbstractDbMapper;
use Catalog\Model\Mapper\Hydrator;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\Db\Sql\Select;

class ModelMapperAbstract extends AbstractDbMapper
{
    protected $tableName;
    protected $hydrator;

    public function __construct($adapter, $unsetFields=null)
    {
        if(null === $unsetFields){
            $unsetFields = array();
        }
        $abstractFields = array('sort_weight', 'linker_id');
        $unsetFields = array_merge($unsetFields, $abstractFields);
        $this->setDbAdapter($adapter);
        $this->setEntityPrototype($this->getModel());
        $this->setHydrator(new Hydrator($unsetFields));
    }

    public function getAll()
    {
        $select = $this->select()
                       ->from($this->getTablename());

        $result = $this->selectMany($select);
        return $result;
    }

    public function findById($id)
    {
        $select = $this->select()
                       ->from($this->getTablename())
                       ->where(array('record_id' => $id));
        return $this->selectwith($select);
    }

    public function update($entity, $where = null, $tableName = null, HydratorInterface $hydrator = null)
    {
        if (!$where) {
            if(is_array($entity)){
                $id = $entity['record_id'];
            }else{
                $id = $entity->getRecordId();
            }
            $where = 'record_id = ' . $entity['record_id'];
        }

        parent::update($entity, $where, $tableName, $hydrator);

        return $id;
    }

    public function add($model, $tableName = null)
    {
        if(null === $tableName){
            $tableName = $this->getTableName();
        }
        $result = parent::insert($model, $tableName);
        if(is_array($model)){
            return;
        }
        $model->setRecordId($result->getGeneratedValue());
        return $model;
    }

    public function getTableName()
    {
        return $this->tableName;
    }

    public function selectWith(Select $select, $entityPrototype = null, HydratorInterface $hydrator = null)
    {
        $result = parent::selectWith($select, $entityPrototype, $hydrator);
        if(count($result) === 1){
            return $result->current();
        }elseif(count($result) > 1){
            throw new \Exception('returned more than one result, use selectMany');
        }
    }

    public function selectMany(Select $select, $entityPrototype = null, HydratorInterface $hydrator = null)
    {
        $result = parent::selectWith($select, $entityPrototype, $hydrator);

        $return = array();
        if(count($result) > 0){
            foreach($result as $res){
                $return[] = $res;
            }
        }
        return $return;
    }
}
