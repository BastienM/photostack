<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\SessionManager;
use Zend\Session\Container;
use Zend\Filter\File\RenameUpload;
use Application\Form\UploadForm;


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

        $request = $this->getRequest();
        if ($request->isPost()) {
            // Make certain to merge the files info!
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );

            $form->setData($post);
            if ($form->isValid()) {
                $data = $form->getData();

                error_reporting(0);

                $extension = pathinfo($data['image-file']['name'], PATHINFO_EXTENSION);
                $filename = sessionid() . uniqid() . '.' . $extension;

                $filter = new RenameUpload("./public/image/" . $filename);
                echo $filter->filter($data['image-file']);

                $manager = new SessionManager();
                $manager->start();

                $userSession = new Container('user');

                $time = new \DateTime();

                $image = array(
                    'url' => "image/" . $filename,
                    'name' => $data['image-file']['name'],
                    'date' => $time->getTimestamp(),
                    'owner' => $userSession->username,
                    'id' => null,
                    'size' => $data['image-file']['size']
                );

                $this->getImagesTable()->saveImageInfo($image);

                $this->redirect()->toUrl('/account');

            } else echo "error";
        }
    }
}