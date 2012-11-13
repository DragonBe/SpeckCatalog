<?php

use Catalog\Service\FormServiceAwareInterface;
use Catalog\Service\CatalogServiceAwareInterface;

$config = array(
    'controllers' => array(
        'invokables' => array(
            'catalog'         => 'Catalog\Controller\CatalogController',
            'product'         => 'Catalog\Controller\ProductController',
            'category'        => 'Catalog\Controller\CategoryController',
            'catalogcart'     => 'Catalog\Controller\CartController',
            'checkout'        => 'Catalog\Controller\CheckoutController',

            'catalogmanager'  => 'Catalog\Controller\CatalogManagerController',
            'manage-category' => 'Catalog\Controller\ManageCategoryController',
            'manage-product'  => 'Catalog\Controller\ManageProductController',
        ),
    ),
    'view_helpers' => array(
        'shared' => array(
            'speckCatalogForm' => false,
        ),
        'invokables' => array(
            'speckCatalogRenderChildren' => 'Catalog\View\Helper\ChildViewRenderer',
            'speckCatalogForm'           => 'Catalog\View\Helper\Form',
            'speckCatalogCart'           => 'Catalog\View\Helper\Cart',
            'speckCatalog'               => 'Catalog\View\Helper\Functions',
            'speckCatalogManagerFormRow' => 'Catalog\Form\View\Helper\CatalogManagerFormRow',
        ),
    ),
    'navigation' => array(
        'admin' => array(
            'mynavigation' => array(
                'label' => 'Catalog Manager',
                'route' => 'catalogmanager',
            ),
        ),
    ),
    'service_manager' => array(
        'shared' => array(
            'cart_item_meta' => false,
            'catalog_option_form' => false,
            'catalog_choice_form' => false,
            'catalog_product_form' => false,
            'catalog_uom_form' => false,
            'catalog_image_form' => false,
            'catalog_spec_form' => false,
            'catalog_availability_form' => false,
            'catalog_product_uom_form' => false,
        ),
        'invokables' => array(

            'cart_item_meta'                   => 'Catalog\Model\CartItemMeta',

            'catalog_product_service'          => 'Catalog\Service\Product',
            'catalog_category_service'         => 'Catalog\Service\Category',
            'catalog_company_service'          => 'Catalog\Service\Company',
            'catalog_option_service'           => 'Catalog\Service\Option',
            'catalog_choice_service'           => 'Catalog\Service\Choice',
            'catalog_product_uom_service'      => 'Catalog\Service\ProductUom',
            'catalog_uom_service'              => 'Catalog\Service\Uom',
            'catalog_document_service'         => 'Catalog\Service\Document',
            'catalog_availability_service'     => 'Catalog\Service\Availability',
            'catalog_spec_service'             => 'Catalog\Service\Spec',
            'catalog_sites_service'            => 'Catalog\Service\Sites',

            'catalog_cart_service'             => 'Catalog\Service\CatalogCartService',
            'catalog_form_service'             => 'Catalog\Service\FormService',

            'catalog_option_form'              => 'Catalog\Form\Option',
            'catalog_choice_form'              => 'Catalog\Form\Choice',
            'catalog_uom_form'                 => 'Catalog\Form\Uom',
            'catalog_company_form'             => 'Catalog\Form\Company',
            'catalog_category_form'            => 'Catalog\Form\Category',
            'catalog_spec_form'                => 'Catalog\Form\Spec',
            'catalog_image_form'               => 'Catalog\Form\Image',
            'catalog_document_form'            => 'Catalog\Form\Document',

            'catalog_spec_form_filter'         => 'Catalog\Filter\Spec',
            'catalog_document_form_filter'     => 'Catalog\Filter\Document',
            'catalog_product_form_filter'      => 'Catalog\Filter\Product',
            'catalog_product_uom_form_filter'  => 'Catalog\Filter\ProductUom',
            'catalog_option_form_filter'       => 'Catalog\Filter\Option',
            'catalog_choice_form_filter'       => 'Catalog\Filter\Choice',
            'catalog_category_form_filter'     => 'Catalog\Filter\Category',
            'catalog_availability_form_filter' => 'Catalog\Filter\Availability',
        ),
    ),
);


return $config;
