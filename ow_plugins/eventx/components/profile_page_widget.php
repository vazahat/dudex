<?php

/**
 * This software is intended for use with Oxwall Free Community Software http://www.oxwall.org/ and is a proprietary licensed product. 
 * For more information see License.txt in the plugin folder.

 * ---
 * Copyright (c) 2012, Purusothaman Ramanujam
 * All rights reserved.

 * Redistribution and use in source and binary forms, with or without modification, are not permitted provided.

 * This plugin should be bought from the developer by paying money to PayPal account (purushoth.r@gmail.com).

 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED
 * AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */
class EVENTX_CMP_ProfilePageWidget extends BASE_CLASS_Widget {

    public function __construct(BASE_CLASS_WidgetParameter $paramsObj) {
        parent::__construct();

        $params = $paramsObj->customParamList;
        $addParams = $paramsObj->additionalParamList;

        if (empty($addParams['entityId']) || !OW::getUser()->isAuthenticated() || !OW::getUser()->isAuthorized('eventx', 'view_event')) {
            $this->setVisible(false);
            return;
        } else {
            $userId = $addParams['entityId'];
        }

        $eventParams = array(
            'action' => 'event_view_attend_events',
            'ownerId' => $userId,
            'viewerId' => OW::getUser()->getId()
        );

        try {
            OW::getEventManager()->getInstance()->call('privacy_check_permission', $eventParams);
        } catch (RedirectException $e) {
            $this->setVisible(false);
            return;
        }

        $language = OW::getLanguage();
        $eventService = EVENTX_BOL_EventService::getInstance();

        $userEvents = $eventService->findUserParticipatedPublicEvents($userId, null, $params['events_count']);


        if (empty($userEvents)) {
            $this->setVisible(false);
            return;
        }

        $this->assign('my_events', $eventService->getListingDataWithToolbar($userEvents));

        $toolbarArray = array();

        if ($eventService->findUserParticipatedPublicEventsCount($userId) > $params['events_count']) {
            $url = OW::getRequest()->buildUrlQueryString(OW::getRouter()->urlForRoute('eventx.view_event_list', array('list' => 'user-participated-events')), array('userId' => $userId));
            $toolbarArray = array(array('href' => $url, 'label' => $language->text('eventx', 'view_all_label')));
        }

        $this->assign('toolbars', $toolbarArray);
    }

    public static function getSettingList() {
        $eventConfigs = EVENTX_BOL_EventService::getInstance()->getConfigs();
        $settingList = array();
        $settingList['events_count'] = array(
            'presentation' => self::PRESENTATION_SELECT,
            'label' => OW::getLanguage()->text('eventx', 'cmp_widget_events_count'),
            'optionList' => $eventConfigs[EVENTX_BOL_EventService::CONF_WIDGET_EVENTS_COUNT_OPTION_LIST],
            'value' => $eventConfigs[EVENTX_BOL_EventService::CONF_WIDGET_EVENTS_COUNT]
        );

        return $settingList;
    }

    public static function getStandardSettingValueList() {
        return array(
            self::SETTING_SHOW_TITLE => true,
            self::SETTING_TITLE => OW::getLanguage()->text('eventx', 'profile_events_widget_block_cap_label'),
            self::SETTING_WRAP_IN_BOX => true,
            self::SETTING_ICON => self::ICON_CALENDAR
        );
    }

    public static function getAccess() {
        return self::ACCESS_ALL;
    }

}
