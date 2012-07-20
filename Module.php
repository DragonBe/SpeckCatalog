<?php

namespace SpeckCatalog;

use Zend\ModuleManager\ModuleManager,
    Zend\Navigation,
    Application\Extra\Page,
    Service\Installer,
    Catalog\Model\Mapper,
    Catalog\Service\FormServiceAwareInterface;

class Module
{
    protected $view;
    protected $viewListener;

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                    'Catalog' => __DIR__ . '/src/Catalog',
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function onBootstrap($e)
    {
        $app          = $e->getParam('application');
        $locator      = $app->getServiceManager();
        $renderer     = $locator->get('Zend\View\Renderer\PhpRenderer');
        $renderer->plugin('url')->setRouter($locator->get('Router'));
        $renderer->plugin('headScript')->appendFile('/assets/speck-catalog/js/speck-catalog-manager.js');
        $renderer->plugin('headLink')->appendStylesheet('/assets/speck-catalog/css/speck-catalog.css');
    }

    public function getViewHelperConfig()
    {
        return array(
            'invokables' => array(
                'speckCatalogRenderChildren' => 'Catalog\View\Helper\ChildViewRenderer',
                'speckCatalogRenderForm' => 'Catalog\View\Helper\RenderForm',
                'speckCatalogAdderHelper' => 'Catalog\View\Helper\AdderHelper',
            ),
            'initializers' => array(
                function($instance, $sm){
                    if($instance instanceof FormServiceAwareInterface){
                        $sm = $sm->getServiceLocator();
                        $formService = $sm->get('catalog_form_service');
                        $instance->setFormService($formService);
                    }
                },
            ),
        );
    }

   //      ,,,,,,,,,,,,,,
   //    <{ blahblahblah }
   //      ''''''''''''''

    public function getServiceConfig()
    {
        return array(
            'invokables' => array(
                'catalog_generic_service'      => 'Catalog\Service\CatalogService',
                'catalog_model_linker_service' => 'Catalog\Service\ModelLinkerService',
                'catalog_product_service'      => 'Catalog\Service\ProductService',
                'catalog_option_service'       => 'Catalog\Service\OptionService',
                //'catalog_image_service'        => 'Catalog\Service\ImageService',
                //'catalog_document_service'     => 'Catalog\Service\DocumentService',
                'catalog_category_service'     => 'Catalog\Service\CategoryService',
                'catalog_choice_service'       => 'Catalog\Service\ChoiceService',
                'catalog_product_uom_service'  => 'Catalog\Service\ProductUomService',
                'catalog_uom_service'          => 'Catalog\Service\UomService',
                'catalog_availability_service' => 'Catalog\Service\AvailabilityService',
                'catalog_company_service'      => 'Catalog\Service\CompanyService',
                'catalog_spec_service'         => 'Catalog\Service\SpecService',

                'catalog_form_service' => 'Catalog\Service\FormService',
                'catalog_option_form' => 'Catalog\Form\Option',
                'catalog_choice_form' => 'Catalog\Form\Choice',
                'catalog_availability_form' => 'Catalog\Form\Availability',
                'catalog_uom_form' => 'Catalog\Form\Uom',
                'catalog_company_form' => 'Catalog\Form\Company',
                'catalog_spec_form' => 'Catalog\Form\Spec',
                'catalog_image_form' => 'Catalog\Form\Image',
                'catalog_document_form' => 'Catalog\Form\Document',

                'catalog_product_uom_form_filter' => 'Catalog\Form\FilterProductUom',
                'catalog_product_form_filter' => 'Catalog\Form\FilterProduct',
                'catalog_option_form_filter' => 'Catalog\Form\FilterOption',
                'catalog_spec_form_filter' => 'Catalog\Form\FilterSpec',
            ),
            'factories' => array(
                'catalog_product_form' => function ($sm) {
                    $form = new \Catalog\Form\Product;
                    $form->setCompanyService($sm->get('catalog_company_service'));
                    return $form->init();
                },
                'catalog_product_uom_form' => function ($sm) {
                    $form = new \Catalog\Form\ProductUom;
                    $form->setUomService($sm->get('catalog_uom_service'));
                    return $form->init();
                },
                'catalog_db' => function ($sm) {
                    return $sm->get('Zend\Db\Adapter\Adapter');
                },
                //model mappers
                'catalog_product_mapper' => function ($sm) {
                    $adapter = $sm->get('catalog_db');
                    return new Mapper\ProductMapper($adapter);
                },
                'catalog_option_mapper' => function ($sm) {
                    $adapter = $sm->get('catalog_db');
                    return new Mapper\OptionMapper($adapter);
                },
                'catalog_category_mapper' => function ($sm) {
                    $adapter = $sm->get('catalog_db');
                    return new Mapper\CategoryMapper($adapter);
                },
                'catalog_choice_mapper' => function ($sm) {
                    $adapter = $sm->get('catalog_db');
                    return new Mapper\ChoiceMapper($adapter);
                },
                'catalog_availability_mapper' => function ($sm) {
                    $adapter = $sm->get('catalog_db');
                    return new Mapper\AvailabilityMapper($adapter);
                },
                'catalog_product_uom_mapper' => function ($sm) {
                    $adapter = $sm->get('catalog_db');
                    return new Mapper\ProductUomMapper($adapter);
                },
                'catalog_image_mapper' => function ($sm) {
                    $adapter = $sm->get('catalog_db');
                    return new Mapper\ImageMapper($adapter);
                },
                'catalog_document_mapper' => function ($sm) {
                    $adapter = $sm->get('catalog_db');
                    return new Mapper\DocumentMapper($adapter);
                },
                'catalog_company_mapper' => function ($sm) {
                    $adapter = $sm->get('catalog_db');
                    return new Mapper\CompanyMapper($adapter);
                },
                'catalog_spec_mapper' => function ($sm) {
                    $adapter = $sm->get('catalog_db');
                    return new Mapper\SpecMapper($adapter);
                },
                'catalog_uom_mapper' => function ($sm) {
                    $adapter = $sm->get('catalog_db');
                    return new Mapper\UomMapper($adapter);
                },
            ),
        );
    }

}
