<?php
namespace SpeckCatalog\Form\Definition;
use SpiffyAnnotation\Form,
    SpiffyForm\Form\Manager,
    SpiffyForm\Form\Definition;

 
class ItemUom implements Definition
{
    public function build(Manager $m)
    {
        $m
            ->add('retail')
            ->add('price');

    }

    public function isValid($params, $form)
    {
        return true;
    }

    public function getName()
    {
        return 'update';
    }

    public function getOptions()
    {
        return array('data_class' => 'SpeckCatalogManager\Entity\ItemUom');
    }
}
