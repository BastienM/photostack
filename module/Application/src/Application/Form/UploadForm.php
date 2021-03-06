<?php

namespace Application\Form;

use Zend\InputFilter;
use Zend\Form\Element;
use Zend\Form\Form;

class UploadForm extends Form
{
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);
        $this->addElements();
        $this->addInputFilter();
    }

    public function addElements()
    {
        // File Input
        $file = new Element\File('image-file');
        $file->setLabel('Select your image')
            ->setAttribute('id', 'image-file');
        $this->add($file);
    }

    public function addInputFilter()
    {
        $inputFilter = new InputFilter\InputFilter();

        $fileInput = new InputFilter\FileInput('image-file');
        $fileInput->setRequired(true);

        $fileInput->getValidatorChain()
            ->attachByName('filesize',      array('max' => 5242880))
            ->attachByName('filemimetype',  array('mimeType' => 'image/png,image/jpeg'));

        $inputFilter->add($fileInput);

        $this->setInputFilter($inputFilter);
    }
}
