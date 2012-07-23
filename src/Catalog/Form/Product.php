<?php

namespace Catalog\Form;

use Zend\Form\Form as ZendForm;

class Product extends ZendForm
{
    protected $companyService;

    public function __construct()
    {
        parent::__construct();

        $this->add(array(
            'name' => 'record_id',
            'attributes' => array(
                'type' => 'hidden'
            ),
            'options' => array(
                'label' => 'Record Id',
            ),
        ));
        $this->add(array(
            'name' => 'name',
            'attributes' => array(
                'type' => 'text',
                'class' => 'span6',
            ),
            'options' => array(
                'label' => 'Name',
            ),
        ));
        $this->add(array(
            'name' => 'description',
            'attributes' => array(
                'type' => 'textarea',
                'rows' => 5,
                'class' => 'span6',
            ),
            'options' => array(
                'label' => 'Description',
            ),
        ));
        $this->add(array(
            'name' => 'item_number',
            'attributes' => array(
                'type' => 'text',
                'class' => 'span3',
            ),
            'options' => array(
                'label' => 'Item Number',
            ),
        ));
    }

    public function init()
    {
        $options = array();
        foreach($this->getCompanyService()->getAll() as $company){
            $options[$company->getRecordId()] = $company->getName();
        }
        $this->add(array(
            'name' => 'manufacturer_company_id',
            'attributes' => array(
                'type' => 'select',
                'options' => $options,
                'class' => 'span3',
            ),
            'options' => array(
                'label' => 'Manufacturer',
            ),
        ));
        return $this;
    }

    /**
     * Get companyService.
     *
     * @return companyService.
     */
    function getCompanyService()
    {
        return $this->companyService;
    }

    /**
     * Set companyService.
     *
     * @param companyService the value to set.
     */
    function setCompanyService($companyService)
    {
        $this->companyService = $companyService;
        return $this;
    }
}
