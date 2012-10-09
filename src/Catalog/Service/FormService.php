<?php

namespace Catalog\Service;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Catalog\Model\AbstractModel;

class FormService implements ServiceLocatorAwareInterface
{
    protected $serviceLocator;

    public function getForm($name = null, $model=null, $data=null)
    {
        $serviceLocator = $this->getServiceLocator();

        $formName = 'catalog_' . $name . '_form';

        $form = $serviceLocator->get($formName);
        $filter = $serviceLocator->get($formName . '_filter');
        $form->setInputFilter($filter);

        if ($model instanceOf AbstractModel) {
            $form->bind($model);
        }
        if (is_array($data)) {
            $form->setData($data);
        }

        return $form;
    }

    public function getKeyFields($name, $model=null)
    {
        $form = $this->getServiceLocator()->get('catalog_' . $name . '_form');

        $fields = array();
        foreach ($form->getOriginalFields() as $field) {
            $getter = 'get' . $this->camel($field);
            $fields[$field] = $model->$getter();
        }

        return $fields;
    }

    private function camel($name)
    {
        $camel = new \Zend\Filter\Word\UnderscoreToCamelCase;
        return $camel->__invoke($name);
    }

    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }
}
