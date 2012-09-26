<?php

namespace Catalog\Mapper;

class Company extends AbstractMapper
{
    protected $tableName = 'contact_company';
    protected $entityPrototype = '\Catalog\Entity\Company';
    protected $hydrator = 'Catalog\Hydrator\Company';

    public function find($companyId)
    {
        $select = $this->getSelect()
            ->from($this->getTableName())
            ->where(array('company_id' => (int) $companyId));
        return $this->selectOne($select);
    }
}
