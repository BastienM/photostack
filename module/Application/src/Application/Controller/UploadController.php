<?php

namespace Application\Controller;

use Application\Form\UploadForm;
use Zend\Filter\File\RenameUpload;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\Session\SessionManager;

//ini_set('memory_limit', '6M');
//error_reporting(0);

class UploadController extends AbstractActionController
{
    protected $imagesTable;

    /**
     * getImagesTable is method which allow us to
     * use ImagesTable (TableGateway object)
     * dynamicly through the Service Manager
     *
     * @return object TableGateway instance of ImagesTable
     */
    public function getImagesTable()
    {
        if (!$this->imagesTable) {
            $sm = $this->getServiceLocator();
            $this->imagesTable = $sm->get('ImagesTable');
        }
        return $this->imagesTable;
    }

    public function indexAction()
    {
        $form = new UploadForm('upload-form');

        $manager = new SessionManager();
        $manager->start();

        $userSession = new Container('user');

        $request = $this->getRequest();
        if ($request->isPost()) {

            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );

            $form->setData($post);
            if ($form->isValid()) {

                $data = $form->getData();

                $extension = pathinfo($data['image-file']['name'], PATHINFO_EXTENSION);
                $filename = session_id() . uniqid() . '.' . $extension;

                $filter = new RenameUpload("./public/image/".$filename);
                echo $filter->filter($data['image-file']['tmp_name']);

                $time = new \DateTime();

                $image = array(
                    'url' => "http://photostack.dev/image/$filename",
                    'name' => $data['image-file']['name'],
                    'date' => $time->getTimestamp(),
                    'owner' => $userSession->username,
                    'id' => null,
                    'size' => $data['image-file']['size']
                );

                $this->getImagesTable()->saveImageInfo($image);

                $userSession->uploadInfo = "<div class='alert alert-success alert-dismissable'>
                                                <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                                                <strong>Success !</strong> Your file has been uploaded.
                                            </div>";

                $this->redirect()->toRoute('account');

            } else {
                $userSession->uploadInfo = "<div class='alert alert-danger alert-dismissable'>
                                                <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                                                <strong>Error !</strong> Your file doesn't matchs the requirements.
                                            </div>";
                $this->redirect()->toRoute('account');


            }
        } else {
            $userSession->uploadInfo = "<div class='alert alert-warning alert-dismissable'>
                                                <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                                                <strong>Warning !</strong> Something came wrong, try again.
                                            </div>";
            $this->redirect()->toRoute('account');
        }
    }
}