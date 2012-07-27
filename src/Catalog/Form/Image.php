<?php

namespace Catalog\Form;

use Zend\Form\Form as ZendForm;

class Image extends ZendForm
{
    public function __construct()
    {
        parent::__construct();

        $this->add(array(
            'name' => 'media_id',
            'attributes' => array(
                'type' => 'hidden'
            ),
        ));
    }
}
