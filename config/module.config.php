<?php
return array(
    'layout' => 'layouts/catalog-manage.phtml',
    'di' => array(
        'instance' => array(
            'alias' => array(
                'catalog'                    => 'Catalog\Controller\IndexController',
                'catalogmanage'              => 'Management\Controller\IndexController',
                'catalog_management_service' => 'Management\Service\CatalogManagementService',
            ),
            'catalogmanage' => array(
                'parameters' => array(
                    'userService'    => 'speckcatalog_user_service',
                    'catalogService' => 'catalog_management_service',
                ),
            ),
            'Zend\View\PhpRenderer' => array(
                'parameters' => array(
                    'options'  => array(
                        'script_paths' => array(
                            'SwmBase' => __DIR__ . '/../views',
                        ),
                    ),
                ),
            ), 
             'doctrine_driver_chain' => array(
                'parameters' => array(
                    'drivers' => array(
                        'speckcatalog_annotationdriver' => array(
                            'class'           => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                            'namespace'       => 'Catalog\Entity',
                            'paths'           => array(__DIR__ . '/../src/Catalog/Entity'),
                        ),
                    ),
                )
            ),         
        ),
    ),
);
