<?php

namespace Application\Form;

use Application\Model\Users;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class UsersFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('users');
        $this->setHydrator(new ClassMethodsHydrator(false))
             ->setObject(new Users());

        $this->setLabel('Users');

        $this->add(array(
            'name' => 'username',
            'options' => array(
            ),
            'attributes' => array(
                'class'       => 'uk-form-large',
                'placeholder' => 'Username', 
                'required'    => 'required'
            )
        ));

        $this->add(array( 
            'name'       => 'password', 
            'type'       => 'Zend\Form\Element\Password', 
            'attributes' => array( 
                'class'       => 'uk-form-large', 
                'placeholder' => 'Password', 
                'required'    => 'required', 
                ), 
            'options'    => array( 
                ), 
        ));

        $this->add(array( 
            'name'       => 'mail', 
            'type'       => 'Zend\Form\Element\Email', 
            'attributes' => array(
                'class'       => 'uk-form-large', 
                'placeholder' => 'Mail address', 
                'required'    => 'required', 
            ), 
            'options'    => array( 
            ), 
        ));

        $this->add(array( 
            'name'       => 'age',
            'attributes' => array(
                'class'       => 'uk-form-large', 
                'placeholder' => 'Your age', 
                //'required'    => 'required', 
            ), 
            'options'    => array( 
            ), 
        ));

    }

    /**
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return array(
            'username' => array(
                'required' => true,
            ),
            'password' => array(
                'required' => true,
            ),
            'mail' => array(
                'required' => true,
            ),
            'age' => array(
                'required' => true,
            )
        );
    }
}