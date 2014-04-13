<?php

class CREDITS_CLASS_RequestEventHandler
{

    private static $classInstance;

    public static function getInstance()
    {
        if ( !isset(self::$classInstance) )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    private function __construct()
    {
    }

    public function collectItems( BASE_CLASS_ConsoleItemCollector $event )
    {
        $language = OW::getLanguage();
        $router = OW::getRouter();

        if ( OW::getUser()->isAuthenticated() )
        {
        
            $userId = OW::getUser()->getId();
            $credits = USERCREDITS_BOL_CreditsService::getInstance()->getCreditsBalance($userId);
                
            $item = new BASE_CMP_ConsoleDropdownMenu($language->text('credits', 'action_label',array('credits' => $credits)));
            $item->setUrl($router->urlForRoute('base_user_profile', array('username' => OW::getUser()->getUserObject()->getUsername())));
            
            if(OW::getUser()->isAdmin() || OW::getUser()->isAuthorized('credits')) {
               $item->addItem('main', array('label' => $language->text('credits', 'view_admin_logs'), 'url' => $router->urlForRoute('credits_admin_logs')));
            }
                        
            $item->addItem('main', array('label' => $language->text('credits', 'my_credit_log'), 'url' => $router->urlForRoute('credits_logs', array('type' => 'all'))));
            $item->addItem('main', array('label' => $language->text('credits', 'send_credits'), 'url' => $router->urlForRoute('credits_transfer')));
            $item->addItem('main', array('label' => $language->text('credits', 'buy_credits'), 'url' => $router->urlForRoute('usercredits.buy_credits')));

            $addItemsEvent = new BASE_CLASS_EventCollector('base.add_main_console_item');
            OW::getEventManager()->trigger($addItemsEvent);
            
            $event->addItem($item, 7);
        }
    }

    public function init()
    {
        OW::getEventManager()->bind('console.collect_items', array($this, 'collectItems'));
    }
}