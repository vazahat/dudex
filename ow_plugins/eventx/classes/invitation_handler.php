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
class EVENTX_CLASS_InvitationHandler {

    const INVITATION_JOIN = 'event-join';

    private static $classInstance;

    public static function getInstance() {
        if (self::$classInstance === null) {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    private function __construct() {
        
    }

    public function onInvite(OW_Event $event) {
        $params = $event->getParams();

        $eventDto = EVENTX_BOL_EventService::getInstance()->findEvent($params['eventId']);
        $eventUrl = OW::getRouter()->urlForRoute('eventx.view', array('eventId' => $eventDto->id));

        $eventTitle = UTIL_String::truncate($eventDto->title, 100, '...');

        $userId = OW::getUser()->getId();
        $userDto = OW::getUser()->getUserObject();
        $userUrl = BOL_UserService::getInstance()->getUserUrlForUsername($userDto->username);
        $userDisplayName = BOL_UserService::getInstance()->getDisplayName($userId);
        $avatars = BOL_AvatarService::getInstance()->getDataForUserAvatars(array($userId));
        $avatar = $avatars[$userId];

        $users = array($userId);

        $stringAssigns = array(
            'event' => '<a href="' . $eventUrl . '">' . $eventTitle . '</a>'
        );

        $stringAssigns['user1'] = '<a href="' . $userUrl . '">' . $userDisplayName . '</a>';

        $contentImage = null;

        if (!empty($eventDto->image)) {
            $eventSrc = EVENTX_BOL_EventService::getInstance()->generateImageUrl($eventDto->image, true);

            $contentImage = array(
                'src' => $eventSrc,
                'url' => $eventUrl,
                'title' => $eventTitle
            );
        }

        $languageKey = 'invitation_join_string_' . 1;

        $invitationEvent = new OW_Event('invitations.add', array(
            'pluginKey' => 'eventx',
            'entityType' => self::INVITATION_JOIN,
            'entityId' => $eventDto->id,
            'userId' => $params['userId'],
            'time' => time(),
            'action' => 'event-invitation'
                ), array(
            'string' => array(
                'key' => 'eventx+' . $languageKey,
                'vars' => $stringAssigns
            ),
            'users' => $users,
            'avatar' => $avatar,
            'contentImage' => $contentImage
        ));


        OW::getEventManager()->trigger($invitationEvent);
    }

    public function onItemRender(OW_Event $event) {
        $params = $event->getParams();

        if ($params['entityType'] != self::INVITATION_JOIN) {
            return;
        }

        $eventId = (int) $params['entityId'];
        $data = $params['data'];

        $itemKey = $params['key'];
        $data['toolbar'] = array(
            array(
                'label' => 'accept',
                'id' => 'toolbar_accept_' . $itemKey
            ),
            array(
                'label' => 'ignore',
                'id' => 'toolbar_ignore_' . $itemKey
            )
        );

        $event->setData($data);

        $jsData = array(
            'eventId' => $eventId,
            'itemKey' => $itemKey
        );

        $js = UTIL_JsGenerator::newInstance();
        $js->jQueryEvent("#toolbar_ignore_$itemKey", 'click', 'OW.Invitation.send("events.ignore", e.data.eventId).removeItem(e.data.itemKey);', array('e'), $jsData);

        $js->jQueryEvent("#toolbar_accept_$itemKey", 'click', 'OW.Invitation.send("events.accept", e.data.eventId);
                 $("#toolbar_ignore_" + e.data.itemKey).hide();
                 $("#toolbar_accept_" + e.data.itemKey).hide();', array('e'), $jsData);

        OW::getDocument()->addOnloadScript($js->generateJs());
    }

    public function onCommand(OW_Event $event) {
        if (!OW::getUser()->isAuthenticated()) {
            return 'auth faild';
        }

        $params = $event->getParams();

        if (!in_array($params['command'], array('events.accept', 'events.ignore'))) {
            return 'wrong command';
        }

        $eventId = $params['data'];
        $eventDto = EVENTX_BOL_EventService::getInstance()->findEvent($eventId);

        $userId = OW::getUser()->getId();
        $jsResponse = UTIL_JsGenerator::newInstance();
        $eventService = EVENTX_BOL_EventService::getInstance();

        if (empty($eventDto)) {
            BOL_InvitationService::getInstance()->deleteInvitation(self::INVITATION_JOIN, $eventId, $userId);
            return 'empty Event Id';
        }

        if ($params['command'] == 'events.accept') {
            $feedback = array('messageType' => 'error');
            $exit = false;
            $attendedStatus = 1;

            if ($eventService->canUserView($eventId, $userId)) {
                $eventDto = $eventService->findEvent($eventId);

                if ($eventDto->getEndTimeStamp() < time()) {
                    $eventService->deleteUserEventInvites((int) $eventId, $userId);
                    $jsResponse->callFunction(array('OW', 'error'), array(OW::getLanguage()->text('eventx', 'user_status_updated')));
                    $event->setData($jsResponse);
                    return;
                }

                $eventUser = $eventService->findEventUser($eventId, $userId);

                if ($eventUser !== null && (int) $eventUser->getStatus() === (int) $attendedStatus) {
                    $jsResponse->callFunction(array('OW', 'error'), array(OW::getLanguage()->text('eventx', 'user_status_not_changed_error')));
                    $exit = true;
                }

                if ($eventDto->getUserId() == OW::getUser()->getId() && (int) $attendedStatus == EVENTX_BOL_EventService::USER_STATUS_NO) {
                    $jsResponse->callFunction(array('OW', 'error'), array(OW::getLanguage()->text('eventx', 'user_status_author_cant_leave_error')));
                    $exit = true;
                }

                if (!$exit) {
                    $eventUserDto = EVENTX_BOL_EventService::getInstance()->addEventUser($userId, $eventId, $attendedStatus);

                    if (!empty($eventUserDto)) {
                        $e = new OW_Event(EVENTX_BOL_EventService::EVENTX_ON_CHANGE_USER_STATUS, array('eventId' => $eventDto->id, 'userId' => $eventUserDto->userId));
                        OW::getEventManager()->trigger($e);

                        $jsResponse->callFunction(array('OW', 'info'), array(OW::getLanguage()->text('eventx', 'user_status_updated')));
                        BOL_InvitationService::getInstance()->deleteInvitation(self::INVITATION_JOIN, $eventId, $userId);
                    } else {
                        $jsResponse->callFunction(array('OW', 'error'), array(OW::getLanguage()->text('eventx', 'user_status_update_error')));
                    }
                }
            } else {
                $jsResponse->callFunction(array('OW', 'error'), array(OW::getLanguage()->text('eventx', 'user_status_update_error')));
            }
        } else if ($params['command'] == 'events.ignore') {
            $eventService->deleteUserEventInvites((int) $eventId, $userId);
            $jsResponse->callFunction(array('OW', 'info'), array(OW::getLanguage()->text('eventx', 'user_status_updated')));
            BOL_InvitationService::getInstance()->deleteInvitation(self::INVITATION_JOIN, $eventId, $userId);
        }

        $event->setData($jsResponse);
    }

    public function init() {
        OW::getEventManager()->bind('eventx.invite_user', array($this, 'onInvite'));
        OW::getEventManager()->bind('invitations.on_item_render', array($this, 'onItemRender'));

        OW::getEventManager()->bind('invitations.on_command', array($this, 'onCommand'));
    }

}
