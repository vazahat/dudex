<?php

class GROUPRSS_CMP_ManageFeedsWidget extends BASE_CLASS_Widget
{
    public function __construct(BASE_CLASS_WidgetParameter $params)
    {
        parent::__construct();
        
        $groupId     = $params->additionalParamList['entityId'];
        $userId      = OW::getUser()->getId();
        $service     = GROUPS_BOL_Service::getInstance();
        $feedService = GROUPRSS_BOL_FeedService::getInstance();
        
        $whoCanAdd = OW::getConfig()->getValue('grouprss', 'actionMember');
        
        if ($whoCanAdd == 'admin' && !OW::getUser()->isAdmin()) {
            $this->setVisible(false);
            return;
        }
        
        $mypaths = explode("/", UTIL_Url::selfUrl());
        $groupId = strtolower(end($mypaths));
        
        if ($groupId == 'customize')
            $groupId = strtolower(prev($mypaths));
        
        if ($whoCanAdd == 'creator' && $feedService->getGroupCreater($groupId) !== $userId) {
            $this->setVisible(false);
            return;
        }
        
        if ($whoCanAdd == 'both') {
            if (!OW::getUser()->isAdmin() && $feedService->getGroupCreater($groupId) !== $userId) {
                $this->setVisible(false);
                return;
            }
        }
        
        $this->assign('groupId', (int) $groupId);
    }
    
    public static function getStandardSettingValueList()
    {
        return array(
            self::SETTING_SHOW_TITLE => false,
            self::SETTING_TITLE => OW_Language::getInstance()->text('grouprss', 'widget_manage_feeds_button_title'),
            self::SETTING_ICON => self::ICON_RSS
        );
    }
    
    public static function getAccess()
    {
        return self::ACCESS_MEMBER;
    }
}