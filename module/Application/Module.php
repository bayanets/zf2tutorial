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


class Module
{	
	public $userRole; 
	
	public function __construct(){
		if(empty($this->userRole)){
			$this->userRole = 'guest';
		}
	}
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

    	echo $this->userRole;
    
//     	if (!$e -> getViewModel() -> acl -> isAllowed($userRole, $route)) {
    	if ($e -> getViewModel() -> acl ->hasResource($route) && !$e -> getViewModel() -> acl -> isAllowed($this->userRole, $route)) {
    		$response = $e -> getResponse();
    		//location to page or what ever
    		$response -> getHeaders() -> addHeaderLine('Location', $e -> getRequest() -> getBaseUrl() . '/');
    		$response -> setStatusCode(303);
    	}
    }
    
}
