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

OW::getRouter()->addRoute(new OW_Route('credits_admin', 'admin/credits/settings', "CREDITS_CTRL_Admin", 'index'));
OW::getRouter()->addRoute(new OW_Route('credits_admin_logs', 'admin/credits/all-logs', "CREDITS_CTRL_Action", 'adminlogs'));
OW::getRouter()->addRoute(new OW_Route('credits_logs', 'your-credits/history/:type', "CREDITS_CTRL_Action", 'logs'));
OW::getRouter()->addRoute(new OW_Route('credits_transfer', 'your-credits/transfer', "CREDITS_CTRL_Action", 'transfer'));

function credits_add_console_dashboard_item( BASE_EventCollector $e )
{
    if ( !OW::getUser()->isAuthenticated() )
    {
        return;
    }

    $userId = OW::getUser()->getId();
    $credits = USERCREDITS_BOL_CreditsService::getInstance()->getCreditsBalance($userId);

        $e->add(
            array(
                BASE_CMP_Console::DATA_KEY_URL => OW::getRouter()->urlForRoute('credits_logs', array('type' => 'all')),
                BASE_CMP_Console::DATA_KEY_ICON_CLASS => 'ow_ic_lens',
                BASE_CMP_Console::DATA_KEY_TITLE => OW::getLanguage()->text('credits', 'action_label',array('credits' => $credits)),
                BASE_CMP_Console::DATA_KEY_ITEMS_LABEL => OW::getLanguage()->text('credits', 'action_label',array('credits' => $credits)),
                BASE_CMP_Console::DATA_KEY_BLOCK => true
            )
        );
}
OW::getEventManager()->bind(BASE_CMP_Console::EVENT_NAME, 'credits_add_console_dashboard_item');

function credits_add_auth_labels(BASE_CLASS_EventCollector $event) {
    $language = OW::getLanguage();
    $event->add(
            array(
                'credits' => array(
                    'label' => $language->text('credits', 'auth_group_label'),
                    'actions' => array(
                        'send' => $language->text('credits', 'auth_action_label_send'),
                        'receive' => $language->text('credits', 'auth_action_label_receive')
                    )
                )
            )
    );
}

OW::getEventManager()->bind('admin.add_auth_labels', 'credits_add_auth_labels');

function credits_members_action_tool( BASE_CLASS_EventCollector $event )
{
    if ( !OW::getUser()->isAuthenticated() )
    {
        return;
    }

    $params = $event->getParams();

    $targetUserID = $params['userId'];

    if ( empty($targetUserID) || $targetUserID == OW::getUser()->getId() || !OW::getAuthorization()->isUserAuthorized($targetUserID, 'credits', 'receive') )
    {
        return;
    }

    $user = BOL_UserService::getInstance()->getUserName((int) $targetUserID);

    $linkId = 'credits' . rand(10, 1000000);

    $resultArray = array(
        BASE_CMP_ProfileActionToolbar::DATA_KEY_LABEL => OW::getLanguage()->text('credits', 'profile_label_send'),
        BASE_CMP_ProfileActionToolbar::DATA_KEY_LINK_HREF => OW::getRouter()->urlFor('CREDITS_CTRL_Action', 'send', array('id' => $targetUserID)),
        BASE_CMP_ProfileActionToolbar::DATA_KEY_LINK_ID => $linkId);

    $event->add($resultArray);
}

OW::getEventManager()->bind(BASE_CMP_ProfileActionToolbar::EVENT_NAME, 'credits_members_action_tool');

$credits = new CREDITS_CLASS_Credits();
OW::getEventManager()->bind('usercredits.on_action_collect', array($credits, 'bindCreditActionsCollect'));

CREDITS_CLASS_RequestEventHandler::getInstance()->init();