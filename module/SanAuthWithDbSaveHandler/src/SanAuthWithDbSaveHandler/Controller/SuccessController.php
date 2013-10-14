<?php
//filename : module/SanAuthWithDbSaveHandler/src/SanAuthWithDbSaveHandler/Controller/SuccessController.php
namespace SanAuthWithDbSaveHandler\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Application\Module;

class SuccessController extends AbstractActionController
{	
    public function indexAction()
    {
		var_dump($session);
    }
}
