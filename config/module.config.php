<?php
$config = array(
    'controller' => array(
        //'classes' => array(
        //    'catalogmanager' => 'CatalogManager\Controller\CatalogManagerController'
        //),
        'factories' => array(
            'catalogmanager' => function ($sm) {
                $userAuth = $sm->get('zfcUserAuthentication');
                $controller = new \CatalogManager\Controller\CatalogManagerController($userAuth);
                return $controller;
            },
        ),
    ), 
);

$configFiles = array(
    __DIR__ . '/module.config.routes.php',
);

foreach($configFiles as $configFile) {
    $config = Zend\Stdlib\ArrayUtils::merge($config, include $configFile);
}

return $config;
