<?php

class CREDITS_CLASS_Credits
{
    private $actions;
    
    public function __construct()
    {
        $this->actions[] = array('pluginKey' => 'credits', 'action' => 'send', 'amount' => 0);
        $this->actions[] = array('pluginKey' => 'credits', 'action' => 'receive', 'amount' => 0);
        //$this->actions[] = array('pluginKey' => 'credits', 'action' => 'revert', 'amount' => 0);        
    }
    
    public function bindCreditActionsCollect( BASE_CLASS_EventCollector $e )
    {
        foreach ( $this->actions as $action )
        {
            $e->add($action);
        }        
    }
    
    public function triggerCreditActionsAdd()
    {
        $e = new BASE_CLASS_EventCollector('usercredits.action_add');
        
        foreach ( $this->actions as $action )
        {
            $e->add($action);
        }

        OW::getEventManager()->trigger($e);
    }
}
