<?php

namespace Application\Form;

use Application\Model\Users;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class UsersMainLoginFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('users');
        $this->setHydrator(new ClassMethodsHydrator(false))
        ->setObject(new Users());

        $this->setLabel('Users');

        $this->add(array( 
            'name'       => 'password', 
            'type'       => 'Zend\Form\Element\Password', 
            'attributes' => array( 
                'class'       => 'input-sm form-control', 
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
                'class'       => 'input-sm form-control', 
                'placeholder' => 'your@mail.com', 
                'required'    => 'required', 
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
            'password' => array(
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'not_empty',
                    ),
                    array(
                        'name' => 'string_length',
                        'options' => array(
                            'min' => 1,
                            'max' => 50,
                        ),
                    ),
                ),
            ),
            'mail' => array(
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'not_empty',
                    ),
                    array(
                        'name' => 'string_length',
                        'options' => array(
                            'min' => 1,
                            'max' => 150,
                        ),
                    ),
                ),
            )
        );
    }
}