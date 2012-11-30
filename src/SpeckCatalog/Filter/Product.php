<?php

namespace SpeckCatalog\Filter;

use Zend\InputFilter\InputFilter;

class Product extends InputFilter
{
    public function __construct()
    {
        $this->add(array(
            'name'        => 'product_id',
            'required'    => 'true',
            'allow_empty' => 'true',
        ));
        $this->add(array(
            'name'        => 'name',
            'allow_empty' => false,
        ));
        $this->add(array(
            'name'        => 'description',
            'allow_empty' => true,
        ));
        $this->add(array(
            'name'        => 'manufacturer_id',
        ));
        $this->add(array(
            'name'        => 'item_number',
            'allow_empty' => true,
        ));
        $this->add(array(
            'name'        => 'product_type_id',
        ));
    }
}
