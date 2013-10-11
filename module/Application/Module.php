<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Applciation\Model\UsersTable;
use Applciation\Model\ImagesTable;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                    ),
                ),
            );
    }

    public function getServiceConfig()
    {
        return array(
            'initializers' => array(
                function ($instance, $sm) {
                   if ($instance instanceof \Zend\Db\Adapter\AdapterAwareInterface) {
                       $instance->setDbAdapter($sm->get('Zend\Db\Adapter\Adapter'));
                   }
               }
               ),
            'invokables' => array(
                'UsersTable'  =>  'Application\Model\UsersTable',
                'ImagesTable' =>  'Application\Model\ImagesTable'
                ),
            );
    }
}
