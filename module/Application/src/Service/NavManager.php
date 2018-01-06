<?php
namespace Application\Service;

/**
 * This service is responsible for determining which items should be in the main menu.
 * The items may be different depending on whether the user is authenticated or not.
 */
class NavManager
{
    /**
     * Auth service.
     * @var Zend\Authentication\Authentication
     */
    private $authService;
    
    /**
     * Url view helper.
     * @var Zend\View\Helper\Url
     */
    private $urlHelper;
    
    /**
     * RBAC manager.
     * @var User\Service\RbacManager
     */
    private $rbacManager;
    
    /**
     * Constructs the service.
     */
    public function __construct($authService, $urlHelper, $rbacManager) 
    {
        $this->authService = $authService;
        $this->urlHelper = $urlHelper;
        $this->rbacManager = $rbacManager;
    }
    
    /**
     * This method returns menu items depending on whether user has logged in or not.
     */
    public function getMenuItems() 
    {
        $url = $this->urlHelper;
        $items = [];
        
        
//        $items[] = [
//            'id' => 'about',
//            'label' => 'About',
//            'link'  => $url('about')
//        ];
        
        // Display "Login" menu item for not authorized user only. On the other hand,
        // display "Admin" and "Logout" menu items only for authorized users.
        if (!$this->authService->hasIdentity()) {
            $items[] = [
                'id' => 'login',
                'label' => 'Sign in',
                'link'  => $url('login'),
                'float' => 'right'
            ];
        } else {
            
            $items[] = [
                'id' => 'home',
                'label' => 'Главная',
                'link'  => $url('home')
            ];
            
            $items[] = [
                'id' => 'shop',
                'label' => 'Каталог',
                'link'  => $url('shop')
            ];
            
            $items[] = [
                'id' => 'cart',
                'label' => 'Корзина',
                'link'  => $url('shop', ['action' => 'cart'])
            ];
            
            $items[] = [
                'id' => 'goods',
                'label' => 'Товары',
                'link'  => $url('goods')
            ];
            
            $items[] = [
                'id' => 'client',
                'label' => 'Покупатели',
                'link'  => $url('client')
            ];
            
            $items[] = [
                'id' => 'supplier',
                'label' => 'Поставщики',
                'link'  => $url('supplier')
            ];

            $items[] = [
                'id' => 'raw',
                'label' => 'Прайсы',
                'link'  => $url('raw')
            ];
            
            //Справочники
            $rbDropdownItems = [];
            if ($this->rbacManager->isGranted(null, 'rb.manage')) {
                $rbDropdownItems[] = [
                            'id' => 'producer',
                            'label' => 'Производители',
                            'link' => $url('producer', [])
                        ];
                $rbDropdownItems[] = [
                            'id' => 'unknown_producer',
                            'label' => 'Неизвестные производители',
                            'link' => $url('producer', ['action'=>'unknown'])
                        ];
                
                $rbDropdownItems[] = [
                            'id' => 'currency',
                            'label' => 'Валюты',
                            'link' => $url('currency')
                        ];
                
                $rbDropdownItems[] = [
                            'id' => 'country',
                            'label' => 'Страны',
                            'link' => $url('rb', ['action'=>'country'])
                        ];
                
                $rbDropdownItems[] = [
                            'id' => 'tax',
                            'label' => 'Налоги',
                            'link' => $url('rb', ['action'=>'tax'])
                        ];
            }
            
            if (count($rbDropdownItems)!=0) {
                $items[] = [
                    'id' => 'rb',
                    'label' => 'Справочники',
                    'dropdown' => $rbDropdownItems
                ];
            }
            
            // Determine which items must be displayed in Admin dropdown.
            $adminDropdownItems = [];
            
            if ($this->rbacManager->isGranted(null, 'user.manage')) {
                $adminDropdownItems[] = [
                            'id' => 'users',
                            'label' => 'Пользователи',
                            'link' => $url('users')
                        ];
            }
            
            if ($this->rbacManager->isGranted(null, 'permission.manage')) {
                $adminDropdownItems[] = [
                            'id' => 'permissions',
                            'label' => 'Права',
                            'link' => $url('permissions')
                        ];
            }
            
            if ($this->rbacManager->isGranted(null, 'role.manage')) {
                $adminDropdownItems[] = [
                            'id' => 'roles',
                            'label' => 'Роли',
                            'link' => $url('roles')
                        ];
            }
            
            if (count($adminDropdownItems)!=0) {
                $items[] = [
                    'id' => 'admin',
                    'label' => 'Пользователи',
                    'dropdown' => $adminDropdownItems
                ];
            }
            
            $items[] = [
                'id' => 'logout',
                'label' => $this->authService->getIdentity(),
                'float' => 'right',
                'dropdown' => [
                    [
                        'id' => 'settings',
                        'label' => 'Настройки',
                        'link' => $url('application', ['action'=>'settings'])
                    ],
                    [
                        'id' => 'logout',
                        'label' => 'Выход',
                        'link' => $url('logout')
                    ],
                ]
            ];
        }
        
        return $items;
    }
}


