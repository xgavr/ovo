<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonModule for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Blank\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class IndexController extends AbstractActionController
{
    /**
     * Менеджер blank.
     * @var Blank\Service\BlankManager 
     */
    private $blankManager;    
    
    // Метод конструктора, используемый для внедрения зависимостей в контроллер.
    public function __construct($blankManager) 
    {
        $this->blankManager = $blankManager;

    }    

    public function indexAction()
    {
        $this->blankManager->test();
        return [];
    }
}
