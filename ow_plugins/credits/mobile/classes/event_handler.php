<?php

class CREDITS_MCLASS_EventHandler {

    private static $classInstance;

    public static function getInstance() {
        if (self::$classInstance === null) {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    private function __construct() {
        
    }

    public function onCollectProfileActions(BASE_CLASS_EventCollector $event) {
        if (!OW::getUser()->isAuthenticated()) {
            return;
        }

        $params = $event->getParams();

        $targetUserID = $params['userId'];

        if (empty($targetUserID) || $targetUserID == OW::getUser()->getId() || !OW::getAuthorization()->isUserAuthorized($targetUserID, 'credits', 'receive')) {
            return;
        }

        $linkId = 'credits' . rand(10, 1000000);

        $event->add(array(
            "label" => OW::getLanguage()->text('credits', 'profile_label_send'),
            "href" => OW::getRouter()->urlFor('CREDITS_MCTRL_Action', 'send', array('id' => $targetUserID)),
            "id" => $linkId
        ));
    }

        public function init() {
        OW::getEventManager()->bind(BASE_MCMP_ProfileActionToolbar::EVENT_NAME, array($this, "onCollectProfileActions"));
    }
}
