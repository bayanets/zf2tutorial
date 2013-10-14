<?php
return array (
		'view_manager' => array (
				'template_path_stack' => array (
						__DIR__ . '/../view' 
				)
		),
		'router' => array (
				'routes' => array (
						'acl' => array (
								'type' => 'Zend\Mvc\Router\Http\Literal',
								'options' => array (
										'route' => '/acl',
										'defaults' => array (
												'controller' => 'Yacl\Controller\Index',
												'action' => 'index' 
										) 
								) 
						) 
				) 
		),
		'controllers' => array (
				'invokables' => array (
						'Yacl\Controller\Index' 
							=> 'Yacl\Controller\IndexController' 
				)
		) 
);