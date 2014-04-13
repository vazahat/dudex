<?php

class IVIDEO_CLASS_Credits {

    private $actions;

    public function __construct() {
        $this->actions[] = array('pluginKey' => 'ivideo', 'action' => 'upload_video', 'amount' => 3);
        $this->actions[] = array('pluginKey' => 'ivideo', 'action' => 'view_video', 'amount' => 1);
    }

    public function bindCreditActionsCollect(BASE_CLASS_EventCollector $e) {
        foreach ($this->actions as $action) {
            $e->add($action);
        }
    }

    public function triggerCreditActionsAdd() {
        $e = new BASE_CLASS_EventCollector('usercredits.action_add');

        foreach ($this->actions as $action) {
            $e->add($action);
        }

        OW::getEventManager()->trigger($e);
    }

}