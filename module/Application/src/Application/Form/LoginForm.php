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

        /**
         * We imports our inputs from the Fieldset
         */
        $this->add(array(
            'type' => 'Application\Form\UsersLoginFieldset',
            'options' => array(
                'use_as_base_fieldset' => true
            )
        ));

        /**
         * And we add our submit button and a Csrf generator
         */
        $this->add(array(
            'type' => 'Zend\Form\Element\Csrf',
            'name' => 'csrf'
        ));

        $this->add(array( 
            'name'       => 'submit',
            'type'       => 'submit', 
            'attributes' => array( 
                'class' => 'btn btn-lg btn-success btn-block',
                'value' => 'Let me in',
            ),
        ));
    }
}