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
class EVENTX_CMP_EventUsers extends OW_Component {

    private $eventService;
    private $userLists;
    private $userListMenu;

    public function __construct($eventId) {
        parent::__construct();

        $this->eventService = EVENTX_BOL_EventService::getInstance();

        $event = $this->eventService->findEvent($eventId);

        if ($event === null) {
            $this->setVisible(false);
        }

        $this->addUserList($event, EVENTX_BOL_EventService::USER_STATUS_YES);
        $this->addUserList($event, EVENTX_BOL_EventService::USER_STATUS_MAYBE);
        $this->addUserList($event, EVENTX_BOL_EventService::USER_STATUS_NO);
        $this->assign('userLists', $this->userLists);
        $this->addComponent('userListMenu', new BASE_CMP_WidgetMenu($this->userListMenu));
    }

    private function addUserList(EVENTX_BOL_Event $event, $status) {
        $configs = $this->eventService->getConfigs();

        $language = OW::getLanguage();
        $listTypes = $this->eventService->getUserListsArray();
        $serviceConfigs = $this->eventService->getConfigs();
        $userList = $this->eventService->findEventUsers($event->getId(), $status, null, $configs[EVENTX_BOL_EventService::CONF_EVENTX_USERS_COUNT]);
        $usersCount = $this->eventService->findEventUsersCount($event->getId(), $status);

        $idList = array();

        foreach ($userList as $eventUser) {
            $idList[] = $eventUser->getUserId();
        }

        $usersCmp = new BASE_CMP_AvatarUserList($idList);

        $linkId = UTIL_HtmlTag::generateAutoId('link');
        $contId = UTIL_HtmlTag::generateAutoId('cont');

        $this->userLists[] = array(
            'contId' => $contId,
            'cmp' => $usersCmp->render(),
            'bottomLinkEnable' => ($usersCount > $serviceConfigs[EVENTX_BOL_EventService::CONF_EVENTX_USERS_COUNT]),
            'toolbarArray' => array(
                array(
                    'label' => $language->text('eventx', 'avatar_user_list_bottom_link_label', array('count' => $usersCount)),
                    'href' => OW::getRouter()->urlForRoute('eventx.user_list', array('eventId' => $event->getId(), 'list' => $listTypes[(int) $status]))
                )
            )
        );

        $this->userListMenu[] = array(
            'label' => $language->text('eventx', 'avatar_user_list_link_label_' . $status),
            'id' => $linkId,
            'contId' => $contId,
            'active' => ( sizeof($this->userListMenu) < 1 ? true : false )
        );
    }

}
