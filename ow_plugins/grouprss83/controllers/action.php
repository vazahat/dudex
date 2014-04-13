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

class GROUPRSS_CTRL_Action extends OW_ActionController
{
    public function manage($params)
    {
        $groupId = (int) $params['groupId'];
        
        if (empty($groupId)) {
            throw new Redirect404Exception();
        }
        
        $service     = GROUPS_BOL_Service::getInstance();
        $feedService = GROUPRSS_BOL_FeedService::getInstance();
        $language    = OW::getLanguage();
        $config      = OW::getConfig();
        $userId      = OW::getUser()->getId();
        
        $groupDto = $service->findGroupById($groupId);
        
        if ($groupDto === null) {
            throw new Redirect404Exception();
        }
        
        $whoCanAdd = $config->getValue('grouprss', 'actionMember');
        
        if ($whoCanAdd == 'admin' && !OW::getUser()->isAdmin()) {
            throw new Redirect404Exception();
        }
        
        $mypaths = explode("/", UTIL_Url::selfUrl());
        $groupId = strtolower(end($mypaths));
        
        if ($groupId == 'customize')
            $groupId = strtolower(prev($mypaths));
        
        if ($whoCanAdd == 'creator' && $feedService->getGroupCreater($groupId) !== $userId) {
            throw new Redirect404Exception();
        }
        
        if ($whoCanAdd == 'both') {
            if (!OW::getUser()->isAdmin() && $feedService->getGroupCreater($groupId) !== $userId) {
                throw new Redirect404Exception();
            }
        }
        
        $userList    = $service->findGroupUserIdList($groupId);
        $userService = BOL_UserService::getInstance();
        $feedService = GROUPRSS_BOL_FeedService::getInstance();
        
        $newForm = new Form('newForm');
        
        $element = new Selectbox('feedUser');
        $element->setLabel($language->text('grouprss', 'newsfeed_user'));
        $element->setRequired();
        foreach ($userList as $key => $user)
            $element->addOption($user, $userService->getDisplayName($user));
        $newForm->addElement($element);
        
        $element = new TextField('feedUrl');
        $element->setRequired(true);
        $validator = new UrlValidator();
        $validator->setErrorMessage($language->text('grouprss', 'invalid_feed_url'));
        $element->addValidator($validator);
        $element->setLabel($language->text('grouprss', 'new_feed_url'));
        $newForm->addElement($element);
        
        $element = new TextField('feedCount');
        $element->setValue("2");
        $element->setRequired();
        $element->setLabel(OW::getLanguage()->text('grouprss', 'user_feed_count'));
        $validator = new IntValidator(1, 50);
        $validator->setErrorMessage(OW::getLanguage()->text('grouprss', 'invalid_feed_count_error'));
        $element->addValidator($validator);
        $newForm->addElement($element);
        
        $element = new Submit('addFeed');
        $element->setValue(OW::getLanguage()->text('grouprss', 'add_new_feed'));
        $newForm->addElement($element);
        
        if (OW::getRequest()->isPost()) {
            if ($newForm->isValid($_POST)) {
                $values    = $newForm->getValues();
                $userId    = $values['feedUser'];
                $feedUrl   = $values['feedUrl'];
                $feedCount = $values['feedCount'];
                
                if ($feedService->isDuplicate($groupId, $feedUrl)) {
                    OW::getFeedback()->error($language->text('grouprss', 'add_feed_duplicate_error'));
                } else {
                    $feedService->addFeed($groupId, $userId, $feedUrl, $feedCount);
                    
                    OW::getFeedback()->info($language->text('grouprss', 'add_feed_success'));
                    
                    GROUPRSS_BOL_FeedService::getInstance()->addAllGroupFeed();
                }
            }
        }
        
        $this->addForm($newForm);
        
        $allFeeds    = $feedService->findByGroup($groupId);
        $feedDetails = array();
        $deleteFeeds = array();
        
        foreach ($allFeeds as $feed) {
            $feedDetails[$feed->id]['feedID']    = $feed->id;
            $feedDetails[$feed->id]['groupID']   = $feed->groupId;
            $feedDetails[$feed->id]['userID']    = $feed->userId;
            $feedDetails[$feed->id]['userName']  = $userService->getDisplayName($feed->userId);
            $feedDetails[$feed->id]['userURL']   = $userService->getUserUrl($feed->userId);
            $feedDetails[$feed->id]['feedURL']   = $feed->feedUrl;
            $feedDetails[$feed->id]['feedCount'] = $feed->feedCount;
            $feedDetails[$feed->id]['timestamp'] = $feed->timestamp;
            
            $deleteFeeds[$feed->id] = OW::getRouter()->urlFor(__CLASS__, 'delete', array(
                'id' => $feed->id,
                'groupId' => $groupId
            ));
        }
        $this->assign('feedDetails', $feedDetails);
        $this->assign('deleteFeeds', $deleteFeeds);
        
        OW::getDocument()->addStyleSheet(OW::getPluginManager()->getPlugin('grouprss')->getStaticCssUrl() . 'style.css');
        OW::getDocument()->addScript(OW::getPluginManager()->getPlugin('grouprss')->getStaticJsUrl() . 'jquery.tablesorter.min.js');
        
        $this->setPageHeading(OW::getLanguage()->text('grouprss', 'manage_settings_title'));
        $this->setPageTitle(OW::getLanguage()->text('grouprss', 'manage_settings_title'));
        $this->setPageHeadingIconClass('ow_ic_gear_wheel');
    }
    
    public function delete($params)
    {
        if (isset($params['id'])) {
            GROUPRSS_BOL_FeedService::getInstance()->deleteFeed((int) $params['id']);
        }
        $this->redirect(OW::getRouter()->urlForRoute('grouprss_manage', array(
            'groupId' => $params['groupId']
        )));
    }
}