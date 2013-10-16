<?php

namespace Application\Form; 

use Zend\Form\Element; 
use Zend\Form\Form; 

class LoginForm extends Form 
{ 
    public function __construct($name = null) 
    { 
        parent::__construct('application/form'); 
        
        $this->setAttributes(array(
            'action' => '#',
            'method' => 'post',
            'class'  => 'uk-form'
        ));
        
        $this->add(array( 
            'name'       => 'email', 
            'type'       => 'Zend\Form\Element\Email', 
            'attributes' => array(
                'class'       => 'uk-form-large', 
                'placeholder' => 'Adresse Mail', 
                'required'    => 'required', 
            ), 
            'options'    => array( 
            ), 
        ));

        $this->add(array( 
            'name'       => 'password', 
            'type'       => 'Zend\Form\Element\Password', 
            'attributes' => array( 
                'class'       => 'uk-form-large', 
                'placeholder' => 'Mot de passe', 
                'required'    => 'required', 
                ), 
            'options'    => array( 
                ), 
        ));

        $this->add(array( 
            'name'       => 'submit',
            'type'       => 'submit', 
            'attributes' => array( 
                'class' => 'uk-button uk-button-expand uk-button-large',
                'value' => 'LOGIN',
                ), 
            'options'    => array( 
                ), 
        ));    
    } 
} 