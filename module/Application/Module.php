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

use Zend\Session\SessionManager;
use Zend\Session\Container;


class Module
{	
	public $userRole; 
	
//** ORIGINAL function onBootstrap
//*
//     public function onBootstrap(MvcEvent $e)
//     {
//         $eventManager        = $e->getApplication()->getEventManager();
//         $moduleRouteListener = new ModuleRouteListener();
//         $moduleRouteListener->attach($eventManager);
//     }

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
    
    
    public function onBootstrap(MvcEvent $e) {
    	$this -> initAcl($e);
    	$e -> getApplication() -> getEventManager() -> attach('route', array($this, 'checkAcl'));
    	
    	$eventManager        = $e->getApplication()->getEventManager();
    	$serviceManager      = $e->getApplication()->getServiceManager();
    	$moduleRouteListener = new ModuleRouteListener();
    	$moduleRouteListener->attach($eventManager);
    	$this->bootstrapSession($e);
    }
    
    public function bootstrapSession($e)
    {
    	$session = $e->getApplication()
    	->getServiceManager()
    	->get('Zend\Session\SessionManager');
    	$session->start();
    
    	$container = new Container('initialized');
    	if (!isset($container->init)) {
    		$session->regenerateId(true);
    		$container->init = 1;
    	}
    }
    
    public function getServiceConfig(){
    	return array(
    			'factories' => array(
    					'Zend\Session\SessionManager' => function ($sm) {
    						$config = $sm->get('config');
    						if (isset($config['session'])) {
    							$session = $config['session'];
    
    							$sessionConfig = null;
    							if (isset($session['config'])) {
    								$class = isset($session['config']['class'])  ? $session['config']['class'] : 'Zend\Session\Config\SessionConfig';
    								$options = isset($session['config']['options']) ? $session['config']['options'] : array();
    								$sessionConfig = new $class();
    								$sessionConfig->setOptions($options);
    							}
    
    							$sessionStorage = null;
    							if (isset($session['storage'])) {
    								$class = $session['storage'];
    								$sessionStorage = new $class();
    							}
    
    							$sessionSaveHandler = null;
    							if (isset($session['save_handler'])) {
    								// class should be fetched from service manager since it will require constructor arguments
    								$sessionSaveHandler = $sm->get($session['save_handler']);
    							}
    
    							$sessionManager = new SessionManager($sessionConfig, $sessionStorage, $sessionSaveHandler);
    
    							if (isset($session['validator'])) {
    								$chain = $sessionManager->getValidatorChain();
    								foreach ($session['validator'] as $validator) {
    									$validator = new $validator();
    									$chain->attach('session.validate', array($validator, 'isValid'));
    
    								}
    							}
    						} else {
    							$sessionManager = new SessionManager();
    						}
    						Container::setDefaultManager($sessionManager);
    						return $sessionManager;
    						}
    				)
    		);
    }    
    
    public function initAcl(MvcEvent $e) {
    
    	$acl = new \Zend\Permissions\Acl\Acl();
    	$roles = include __DIR__ . '/config/module.acl.roles.php';
    	$allResources = array();
    	foreach ($roles as $role => $resources) {
    
    		$role = new \Zend\Permissions\Acl\Role\GenericRole($role);
    		$acl -> addRole($role);
    
    		$allResources = array_merge($resources, $allResources);
    
    		//adding resources
    		foreach ($resources as $resource) {
    			// Edit 4
    			if(!$acl ->hasResource($resource))
    				$acl -> addResource(new \Zend\Permissions\Acl\Resource\GenericResource($resource));
    		}
    		//adding restrictions
    		foreach ($allResources as $resource) {
    			$acl -> allow($role, $resource);
    		}
    	}
    	//testing
    	//var_dump($acl->isAllowed('admin','home'));
    	//true
    
    	//setting to view
    	$e -> getViewModel() -> acl = $acl;
    
    }
    
    public function checkAcl(MvcEvent $e) {
    	$route = $e -> getRouteMatch() -> getMatchedRouteName();
    	//you set your role
//     	$userRole = 'guest';
//     	$userRole = 'admin';
  	
    	$this->userRole = $_SESSION['my_storage_namespace']->storage['role'];
    	if ($this->userRole == NULL){ $this->userRole = 'guest'; }
    	
    
    	if ($e -> getViewModel() -> acl ->hasResource($route) && !$e -> getViewModel() -> acl -> isAllowed($this->userRole, $route)) {
    		$response = $e -> getResponse();
    		//location to page or what ever
    		$response -> getHeaders() -> addHeaderLine('Location', $e -> getRequest() -> getBaseUrl() . '/');
    		$response -> setStatusCode(303);
    	}
    }
    
}
