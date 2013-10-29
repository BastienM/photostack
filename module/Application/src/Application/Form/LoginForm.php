<?php

namespace Application\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class LoginForm extends Form
{
    public function __construct()
    {
        parent::__construct('login_form');

        $this->setAttribute('method', 'post')
             ->setHydrator(new ClassMethodsHydrator(false))
             ->setInputFilter(new InputFilter());

        $this->add(array(
            'type' => 'Application\Form\UsersLoginFieldset',
            'options' => array(
                'use_as_base_fieldset' => true
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Csrf',
            'name' => 'csrf'
        ));

        $this->add(array( 
            'name'       => 'submit',
            'type'       => 'submit', 
            'attributes' => array( 
                'class' => 'uk-button uk-button-expand uk-button-large',
                'value' => 'LOGIN',
            ),
        ));
    }
}