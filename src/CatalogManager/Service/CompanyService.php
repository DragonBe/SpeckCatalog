<?php

namespace CatalogManager\Service;

use Catalog\Service\CompanyService as CatalogCompanyService;
use Zend\Stdlib\Hydrator\ClassMethods as Hydrator;

class CompanyService extends CatalogCompanyService
{
    public function getCatalogManagerForm($model = null)
    {
        $key = 'catalogmanager_company_form';

        $form = $this->getServiceManager()->get($key);
        $hydrator = new Hydrator();
        $data = $hydrator->extract($model);
        $form->setHydrator($hydrator);
        $form->bind($model);
        $form->setData($data);

        return $form;
    }
}
