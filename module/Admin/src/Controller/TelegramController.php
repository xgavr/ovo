<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonModule for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class TelegramController extends AbstractActionController
{
    
    /**
     * TelegrammManager manager.
     * @var Admin\Service\TelegrammManager
     */
    private $telegramManager;    
    
    // Метод конструктора, используемый для внедрения зависимостей в контроллер.
    public function __construct($telegramManager) 
    {
        $this->telegramManager = $telegramManager;        
    }   
    
    public function indexAction()
    {
        return [];
    }
    
    /*
     * Telegramm hook
     */
    public function hookAction()
    {
        $this->telegramManager->hook();
        exit;        
    }
    
    public function setAction()
    {
        $this->telegramManager->setHook();
        exit;
    }
    
    public function unsetAction()
    {
        $this->telegramManager->unsetHook();
        exit;
    }   
    
    public function testAction()
    {
        $this->telegramManager->sendTestMessage();
        exit;
    }
}
