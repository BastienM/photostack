<?php

namespace Application\Form;

use Application\Model\Users;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class UsersSignupFieldset extends Fieldset implements InputFilterProviderInterface
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
                'class'       => 'form-control',
                'placeholder' => 'Username', 
                'required'    => 'required'
                )
        ));

        $this->add(array( 
            'name'       => 'mail', 
            'type'       => 'Zend\Form\Element\Email', 
            'attributes' => array(
                'class'       => 'form-control', 
                'placeholder' => 'your@mail.com', 
                'required'    => 'required', 
                ), 
            'options'    => array( 
                ), 
        ));

        $this->add(array( 
            'name'       => 'age',
            'attributes' => array(
                'class'       => 'form-control', 
                'placeholder' => 'Your age', 
                'required'    => 'required', 
                ), 
            'options'    => array( 
                ), 
        ));

        $this->add(array(
            'name' => 'captcha',
            'type' => 'Zend\Form\Element\Captcha',
            'attributes' => array(
                'class'       => 'form-control',
                'required'    => 'required',
            ),
            'options' => array(
                'label' => '',
                'captcha' => array(
                    'class' => 'Dumb',
                ),
                'messages' => array(
                    'badCaptcha' => 'What the fuck !',
                ),
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
                'validators' => array(
                    array(
                        'name' => 'not_empty',
                    ),
                    array(
                        'name' => 'string_length',
                        'options' => array(
                            'min' => 1,
                            'max' => 25,
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
            ),
            'age' => array(
                // 'required' => true,
                'validators' => array(
                    array(
                        'name' => 'not_empty',
                    ),
                    array(
                        'name' => 'string_length',
                        'options' => array(
                            'min' => 1,
                            'max' => 2,
                        ),
                    ),
                ),
            )
        );
    }
}