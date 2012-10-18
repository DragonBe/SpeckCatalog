<?php

namespace SpeckCatalog;

use Zend\ModuleManager\ModuleManager;
use Zend\Navigation;
use Application\Extra\Page;
use Service\Installer;
use Catalog\Service\FormServiceAwareInterface;
use Catalog\Service\CatalogServiceAwareInterface;
use Zend\Console\Request as ConsoleRequest;

class Module
{
    protected $view;
    protected $viewListener;
    protected $serviceManager;

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
        if($e->getRequest() instanceof ConsoleRequest){
            return;
        }

        $app = $e->getParam('application');
        $em  = $app->getEventManager()->getSharedManager();
        $em->attach('ImageUploader\Service\Uploader', 'fileupload.pre', array($this, 'preFileUpload'));
        $em->attach('ImageUploader\Service\Uploader', 'fileupload.post', array($this, 'postFileUpload'));

        $app          = $e->getParam('application');
        $locator      = $app->getServiceManager();
        $this->setServiceManager($locator);
        $renderer     = $locator->get('Zend\View\Renderer\PhpRenderer');
        $renderer->plugin('url')->setRouter($locator->get('Router'));
        $renderer->plugin('headScript')->appendFile('/assets/speck-catalog/js/speck-catalog-manager.js');
        $renderer->plugin('headLink')->appendStylesheet('/assets/speck-catalog/css/speck-catalog.css');
    }

    public function preFileUpload($e)
    {
        $formData = $e->getParam('params');
        $getter = 'get' . ucfirst($formData['file_type']) . 'Upload';

        $catalogOptions = $this->getServiceManager()->get('catalog_module_options');

        if($formData['file_type'] === 'productDocument'){
            $e->getParam('options')->setAllowedFileTypes(array('pdf' => 'pdf'));
            $e->getParam('options')->setUseMin(false);
            $e->getParam('options')->setUseMax(false);
        }

        $appRoot = __DIR__ . '/../..';
        $path = $appRoot . $catalogOptions->$getter();
        $e->getParam('options')->setDestination($path);
    }

    public function postFileUpload($e)
    {
        $params = $e->getParams();
        switch ($params['params']['file_type']) {
            case 'productImage' :
                $imageService = $this->getServiceManager()->get('catalog_product_image_service');
                $image = $imageService->getEntity();
                $image->setProductId($params['params']['product_id'])
                    ->setFileName($params['fileName']);
                $imageService->persist($image);
                break;
            case 'productDocument' :
                $documentService = $this->getServiceManager()->get('catalog_document_service');
                $document = $documentService->getEntity();
                $document->setProductId($params['params']['product_id'])
                    ->setFileName($params['fileName']);
                $documentService->persist($document);
                break;
            case 'optionImage' :
                $imageService = $this->getServiceManager()->get('catalog_option_image_service');
                $image = $imageService->getEntity();
                $image->setOptionId($params['params']['option_id'])
                    ->setFileName($params['fileName']);
                $imageService->persist($image);
                break;
            default :
                throw new \Exception('no handler for file type - ' . $params['params']['file_type']);
        }
    }

    public function getViewHelperConfig()
    {
        return array(
            'invokables' => array(
                'speckCatalogRenderChildren' => 'Catalog\View\Helper\ChildViewRenderer',
                'speckCatalogRenderForm'     => 'Catalog\View\Helper\RenderForm',
                'speckCatalogCart'           => 'Catalog\View\Helper\Cart',
                'speckCatalogAdderHelper'    => 'Catalog\View\Helper\AdderHelper',
            ),
            'factories' => array(
                'speckCatalogOptionImageUploader'  => function ($sm) {
                    $imageUploader = $sm->get('imageUploader');
                    $element = array('name' => 'file_type', 'attributes' => array('value' => 'optionImage', 'type' => 'hidden'));
                    $imageUploader->getForm()->add($element);
                    return $imageUploader;
                },
                'speckCatalogProductImageUploader'  => function ($sm) {
                    $imageUploader = $sm->get('imageUploader');
                    $element = array('name' => 'file_type', 'attributes' => array('value' => 'productImage', 'type' => 'hidden'));
                    $imageUploader->getForm()->add($element);
                    return $imageUploader;
                },
                'speckCatalogProductDocumentUploader' => function ($sm) {
                    $uploader = $sm->get('imageUploader');
                    $element = array('name' => 'file_type', 'attributes' => array('value' => 'productDocument', 'type' => 'hidden'));
                    $uploader->getForm()->add($element);
                    return $uploader;
                },
                'speckCatalogCategoryNav'    => function ($sm) {
                    $sm = $sm->getServiceLocator();
                    $helper = new \Catalog\View\Helper\CategoryNav;
                    return $helper->setCategoryService($sm->get('catalog_category_service'));
                },
                'speckCatalogImage' => function ($sm) {
                    $sm = $sm->getServiceLocator();
                    $settings = $sm->get('catalog_module_options');
                    return new \Catalog\View\Helper\MediaUrl($settings, 'image');
                },
            ),
            'initializers' => array(
                function($instance, $sm){
                    if($instance instanceof \Catalog\Service\FormServiceAwareInterface){
                        $sm = $sm->getServiceLocator();
                        $formService = $sm->get('catalog_form_service');
                        $instance->setFormService($formService);
                    }
                },
            ),
        );
    }

    public function getControllerConfig()
    {
        return array(
            'initializers' => array(
                function($instance, $sm){
                    if($instance instanceof FormServiceAwareInterface){
                        $sm = $sm->getServiceLocator();
                        $formService = $sm->get('catalog_form_service');
                        $instance->setFormService($formService);
                    }
                },
                function($instance, $sm){
                    if($instance instanceof CatalogServiceAwareInterface){
                        $sm = $sm->getServiceLocator();
                        $catalogService = $sm->get('catalog_generic_service');
                        $instance->setCatalogService($catalogService);
                    }
                },
            ),
        );
    }

    public function getServiceConfig()
    {
        return array(
            'shared' => array(
                //'cart_item_meta' => false,
            ),
            'factories' => array(
                'catalog_product_image_service' => function ($sm) {
                    $service = new \Catalog\Service\Image;
                    $mapper = $sm->get('catalog_image_mapper')->setParentType('product');
                    return $service->setEntityMapper($mapper);
                },
                'catalog_option_image_service' => function ($sm) {
                    $service = new \Catalog\Service\Image;
                    $mapper = $sm->get('catalog_image_mapper')->setParentType('option');
                    return $service->setEntityMapper($mapper);
                },
                'catalog_module_options' => function ($sm) {
                    $config = $sm->get('Config');
                    return new \Catalog\Options\ModuleOptions(isset($config['speckcatalog']) ? $config['speckcatalog'] : array());
                },
                'catalog_availability_form' => function ($sm) {
                    $form = new \Catalog\Form\Availability;
                    $form->setCompanyService($sm->get('catalog_company_service'));
                    return $form->init();
                },
                'catalog_product_form' => function ($sm) {
                    $form = new \Catalog\Form\Product;
                    $companyService = $sm->get('catalog_company_service');
                    $form->setCompanyService($sm->get('catalog_company_service'));
                    return $form->init();
                },
                'catalog_product_uom_form' => function ($sm) {
                    $form = new \Catalog\Form\ProductUom;
                    $form->setUomService($sm->get('catalog_uom_service'));
                    return $form->init();
                },

                'catalog_product_mapper'           => function ($sm) {
                    $mapper = new \Catalog\Mapper\Product;
                    return $mapper->setDbAdapter($sm->get('catalog_db'));
                },
                'catalog_category_mapper'          => function ($sm) {
                    $mapper = new \Catalog\Mapper\Category;
                    return $mapper->setDbAdapter($sm->get('catalog_db'));
                },
                'catalog_company_mapper'           => function ($sm) {
                    $mapper = new \Catalog\Mapper\Company;
                    return $mapper->setDbAdapter($sm->get('catalog_db'));
                },
                'catalog_option_mapper'            => function ($sm) {
                    $mapper = new \Catalog\Mapper\Option;
                    return $mapper->setDbAdapter($sm->get('catalog_db'));
                },
                'catalog_choice_mapper'            => function ($sm) {
                    $mapper = new \Catalog\Mapper\Choice;
                    return $mapper->setDbAdapter($sm->get('catalog_db'));
                },
                'catalog_product_uom_mapper'       => function ($sm) {
                    $mapper = new \Catalog\Mapper\ProductUom;
                    return $mapper->setDbAdapter($sm->get('catalog_db'));
                },
                'catalog_image_mapper'             => function ($sm) {
                    $mapper = new \Catalog\Mapper\Image;
                    return $mapper->setDbAdapter($sm->get('catalog_db'));
                },
                'catalog_document_mapper'          => function ($sm) {
                    $mapper = new \Catalog\Mapper\Document;
                    return $mapper->setDbAdapter($sm->get('catalog_db'));
                },
                'catalog_uom_mapper'               => function ($sm) {
                    $mapper = new \Catalog\Mapper\Uom;
                    return $mapper->setDbAdapter($sm->get('catalog_db'));
                },
                'catalog_availability_mapper'      => function ($sm) {
                    $mapper = new \Catalog\Mapper\Availability;
                    return $mapper->setDbAdapter($sm->get('catalog_db'));
                },
                'catalog_spec_mapper'              => function ($sm) {
                    $mapper = new \Catalog\Mapper\Spec;
                    return $mapper->setDbAdapter($sm->get('catalog_db'));
                },

                'catalog_db' => function ($sm) {
                    return $sm->get('Zend\Db\Adapter\Adapter');
                },
            ),
        );
    }

    /**
     * @return serviceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * @param $serviceManager
     * @return self
     */
    public function setServiceManager($serviceManager)
    {
        $this->serviceManager = $serviceManager;
        return $this;
    }
}
