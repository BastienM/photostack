<?php

namespace Application\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class SignupForm extends Form
{
    public function __construct()
    {
        parent::__construct('signup_users');

        $this->setAttribute('method', 'post')
             ->setHydrator(new ClassMethodsHydrator(false))
             ->setInputFilter(new InputFilter());

        $this->add(array(
            'type' => 'Application\Form\UsersSignupFieldset',
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
                'class' => 'btn btn-lg btn-info btn-block',
                'value' => 'SIGN UP',
                ), 
            'options'    => array( 
                ),
        ));
    }
}