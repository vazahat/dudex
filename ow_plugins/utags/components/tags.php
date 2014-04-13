<?php

/**
 * Copyright (c) 2013, Sergey Kambalin
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * @author Sergey Kambalin <greyexpert@gmail.com>
 * @package utags.components
 */
class UTAGS_CMP_Tags extends OW_Component
{
    protected $data = array();
    protected $groups = array();
    protected $groupDefaults = array(
        'priority' => 0,
        'alwaysVisible' => true,
        'noMatchMessage' => false,
        /*'noMatchMessage' => array(
            'prefix' => 'utags',
            'key' => 'selector_no_matches'
        )*/
    );
    
    private $uniqId;
    private $userId;
    
    public function __construct() 
    {
        parent::__construct();
        
        $this->uniqId = uniqid("ut-");
        $this->userId = OW::getUser()->getId();
        
        $this->assign("uniqId", $this->uniqId);
        
        $this->data = UTAGS_BOL_Service::getInstance()->getSuggestEntries($this->userId, null, "photo");
        
        $event = new OW_Event(UTAGS_BOL_Service::EVENT_ON_INPUT_INIT, array(
            "input" => $this,
            "userId" => $this->userId,
            "context" => "photo"
        ));
        OW::getEventManager()->trigger($event);
    }
    
    public function setupGroup( $group, $settings = array() )
    {
        $this->groups[$group] = isset($this->groups[$group])
                ? $this->groups[$group]
                : $this->groupDefaults;

        $this->groups[$group] = array_merge($this->groups[$group], $settings);
    }
    
    public function setData( $data )
    {
        $this->data = $data;
    }
    
    public function onBeforeRender() 
    {
        parent::onBeforeRender();

        $inputOptions = array(
            "width" => "off",
            "dropdownAutoWidth" => false,
            "containerCssClass" => "uts-search-select2",
            "dropdownCssClass" => 'ow_bg_color ow_border uts-dropdown ow_small',
            'multiple' => true,
            "minimumInputLength" => 1,
            "maximumSelectionSize" => 5
        );
        
        $inputSettings = array();
        $inputSettings['rspUrl'] = OW::getRouter()->urlFor('UTAGS_CTRL_Ajax', 'searchRsp');
        $inputSettings['groups'] = $this->groups;
        $inputSettings['groupDefaults'] = $this->groupDefaults;
        $inputSettings['context'] = "photo";
        $inputSettings['contextId'] = null;
        
        $input = array(
            "settings" => $inputSettings,
            "options" => $inputOptions,
            "data" => $this->data
        );
        
        $permissions = array();
        $permissions["credits"]["actions"] = UTAGS_CLASS_CreditsBridge::getInstance()->getAllPermissions();
        $permissions["credits"]["messages"] = UTAGS_CLASS_CreditsBridge::getInstance()->getAllPermissionMessages();
        $permissions["isModerator"] = OW::getUser()->isAuthorized("utags");
        
        $options = array(
            "rsp" => OW::getRouter()->urlFor("UTAGS_CTRL_Ajax", "rsp"),
            "input" => $input,
            "permissions" => $permissions
        );
        
        $js = UTIL_JsGenerator::newInstance();
        $js->addScript('UTAGS_Require("script", function() { UTAGS_init({$uniqId}, {$options}); });', array(
            "uniqId" => $this->uniqId, 
            "options" => $options
        ));
        
        OW::getDocument()->addOnloadScript($js);
        
        OW::getLanguage()->addKeyForJs('utags', 'selector_no_matches');
        OW::getLanguage()->addKeyForJs('utags', 'selector_searching');
        OW::getLanguage()->addKeyForJs('utags', 'selector_too_short');
    }
}