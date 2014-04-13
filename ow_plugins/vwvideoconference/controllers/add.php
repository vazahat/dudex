<?php

/**
 * This software is intended for use with Oxwall Free Community Software http://www.oxwall.org/ and is
 * licensed under The BSD license.

 * ---
 * Copyright (c) 2011, Oxwall Foundation
 * All rights reserved.

 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the
 * following conditions are met:
 *
 *  - Redistributions of source code must retain the above copyright notice, this list of conditions and
 *  the following disclaimer.
 *
 *  - Redistributions in binary form must reproduce the above copyright notice, this list of conditions and
 *  the following disclaimer in the documentation and/or other materials provided with the distribution.
 *
 *  - Neither the name of the Oxwall Foundation nor the names of its contributors may be used to endorse or promote products
 *  derived from this software without specific prior written permission.

 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED
 * AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

/**
 * Vwvc add action controller
 *
 * @author Egor Bulgakov <egor.bulgakov@gmail.com>
 * @package ow.plugin.vwvc.controllers
 * @since 1.0
 */
class VWVC_CTRL_Add extends OW_ActionController
{

    /**
     * Default action
     */
    public function index()
    {
        $language = OW::getLanguage();
        $clipService = VWVC_BOL_ClipService::getInstance();
        $userId = OW::getUser()->getId();

        if ( !OW::getUser()->isAuthorized('vwvc', 'add') )
        {
            $this->setTemplate(OW::getPluginManager()->getPlugin('base')->getCtrlViewDir() . 'authorization_failed.html');
            return;
        }

        // member list
        $config = OW::getConfig();
        $member = $config->getValue('vwvc', 'member');
        $memberList = $config->getValue('vwvc', 'member_list');


        $eventParams = array('pluginKey' => 'vwvc', 'action' => 'add_vwvc');
        $credits = OW::getEventManager()->call('usercredits.check_balance', $eventParams);
        
        if ( $credits === false )
        {
            $this->assign('auth_msg', OW::getEventManager()->call('usercredits.error_message', $eventParams));
        }
        elseif ($member == 'member' && $memberList != '') {
          $userService = BOL_UserService::getInstance();
          $user = $userService->findUserById(OW::getUser()->getId());
          $username = $user->getUsername();
      		$member = explode(",", $memberList);
      		if (trim($memberList) != '') {
      			$found = 0;
      			 foreach ($member as $key => $val) { 
      				if ($username == trim($val)) {
      					$found = 1;
                $this->assign('auth_msg', null);
    
                $vwvcAddForm = new vwvcAddForm();
                $this->addForm($vwvcAddForm);
    
                $userId = OW::getUser()->getId();
    
                if ( OW::getRequest()->isPost() && $vwvcAddForm->isValid($_POST) )
                {
                    $values = $vwvcAddForm->getValues();
                    $code = $clipService->validateClipCode($values['code']);
                    
                    if ( !BOL_TextFormatService::getInstance()->isCodeResourceValid($code) )
                    {
                        OW::getFeedback()->warning($language->text('vwvc', 'resource_not_allowed'));
                        $this->redirect();
                    }
                    
                    $res = $vwvcAddForm->process();
                    OW::getFeedback()->info($language->text('vwvc', 'clip_added'));
                    $this->redirect(OW::getRouter()->urlForRoute('vwview_clip', array('id' => $res['id'])));
                }
      				}
      			 }
      			 if ($found === 0) {
               $this->assign('auth_msg', $language->text('vwvc', 'adding_denied'));
      			 }
      		  }
        } else {
                $this->assign('auth_msg', null);
    
                $vwvcAddForm = new vwvcAddForm();
                $this->addForm($vwvcAddForm);
    
                $userId = OW::getUser()->getId();
    
                if ( OW::getRequest()->isPost() && $vwvcAddForm->isValid($_POST) )
                {
                    $values = $vwvcAddForm->getValues();
                    $code = $clipService->validateClipCode($values['code']);
                    
                    if ( !BOL_TextFormatService::getInstance()->isCodeResourceValid($code) )
                    {
                        OW::getFeedback()->warning($language->text('vwvc', 'resource_not_allowed'));
                        $this->redirect();
                    }
                    
                    $res = $vwvcAddForm->process();
                    OW::getFeedback()->info($language->text('vwvc', 'clip_added'));
                    $this->redirect(OW::getRouter()->urlForRoute('vwview_clip', array('id' => $res['id'])));
                }
        }

        if ( !OW::getRequest()->isAjax() )
        {
            OW::getNavigation()->activateMenuItem(OW_Navigation::MAIN, 'vwvc', 'vwvc');
        }

        OW::getDocument()->setHeading($language->text('vwvc', 'page_title_add_vwvc'));
        OW::getDocument()->setHeadingIconClass('ow_ic_vwvc');
        OW::getDocument()->setTitle($language->text('vwvc', 'meta_title_vwvc_add'));
    }
}

/**
 * Vwvc add form class
 */
class vwvcAddForm extends Form
{

    /**
     * Class constructor
     *
     */
    public function __construct()
    {
        parent::__construct('vwvcAddForm');

        $language = OW::getLanguage();

        // select box for permission
        $permArr0 = array(
          '1' => 'moderators',
          '3' => 'none',
          '2' => 'owner',
          '0' => 'all'
        );
        $permArr1 = array(
          '0' => 'all',
          '3' => 'none',
          '2' => 'owner',
          '1' => 'moderators'
        );

        // room_name Field
        $generated =  base_convert((time()-1224000000).rand(0,10),10,36);
        $room_nameField = new TextField('room_name');
        $sValidator = new StringValidator(1, 22);
        $room_nameField->addValidator($sValidator);
        $room_nameField->setRequired(true);
        $room_nameField->setValue($generated);

        $this->addElement($room_nameField->setLabel($language->text('vwvc', 'room_name')));

        // Description Field
        $descriptionField = new Textarea('description');
        $this->addElement($descriptionField->setLabel($language->text('vwvc', 'description')));

        // welcome Field
        $welcomeField = new Textarea('welcome');
        $welcomeField->setValue($language->text('vwvc', 'welcome_default'));
        $this->addElement($welcomeField->setLabel($language->text('vwvc', 'welcome')));

        // resolution Field
        $resolutionArr = array(
          '320x240' => '320x240',
          '160x120' => '160x120',
          '176x144' => '176x144',
          '352x288' => '352x288',
          '640x480' => '640x480'
        );
        $resolutionField = new Selectbox('resolution');
        $resolutionField->addOptions($resolutionArr);
        $resolutionField->setRequired();
        $resolutionField->setHasInvitation(false);
        $this->addElement($resolutionField->setLabel($language->text('vwvc', 'resolution')));

        // camera_fps Field
        $camera_fpsArr = array(
          '10' => '10',
          '12' => '12',
          '20' => '20',
          '25' => '25',
          '30' => '30'
        );
        $camera_fpsField = new Selectbox('camera_fps');
        $camera_fpsField->addOptions($camera_fpsArr);
        $camera_fpsField->setRequired();
        $camera_fpsField->setHasInvitation(false);
        $this->addElement($camera_fpsField->setLabel($language->text('vwvc', 'camera_fps')));

        // Microphone Rate Field
        $microphone_rateArr = array(
          '11' => '11',
          '22' => '22',
          '44' => '44',
          '48' => '48'
        );
        $microphone_rateField = new Selectbox('microphone_rate');
        $microphone_rateField->addOptions($microphone_rateArr);
        $microphone_rateField->setRequired();
        $microphone_rateField->setHasInvitation(false);
        $this->addElement($microphone_rateField->setLabel($language->text('vwvc', 'microphone_rate')));

        // soundQuality Field
        $soundQualityField = new TextField('soundQuality');
        $soundQualityField->setRequired(true);
        $soundQualityField->setValue(9);
        $this->addElement($soundQualityField->setLabel($language->text('vwvc', 'soundQuality')));

        // Bandwidth Field
        $bandwidthField = new TextField('bandwidth');
        $bandwidthField->setRequired(true);
        $bandwidthField->setValue(40960);
        $this->addElement($bandwidthField->setLabel($language->text('vwvc', 'bandwidth')));

        // verbose_level Field
        $verbose_levelArr = array(
          '2' => 'warning/recoverable failure',
          '0' => 'nothing',
          '1' => 'failure',
          '3' => 'success',
          '4' => 'action'
        );
        $verbose_levelField = new Selectbox('verbose_level');
        $verbose_levelField->addOptions($verbose_levelArr);
        $verbose_levelField->setRequired();
        $verbose_levelField->setHasInvitation(false);
        $this->addElement($verbose_levelField->setLabel($language->text('vwvc', 'verbose_level')));

        // Background url Field
        $background_urlField = new TextField('background_url');
        $this->addElement($background_urlField->setLabel($language->text('vwvc', 'background_url')));

        // Layout Code Field
        $layout_codeField = new Textarea('layout_code');
        $this->addElement($layout_codeField->setLabel($language->text('vwvc', 'layout_code')));

        // Fill window Field
        $fill_windowField = new Selectbox('fill_window');
        $fill_windowField->addOptions($permArr0);
        $fill_windowField->setRequired();
        $fill_windowField->setHasInvitation(false);
        $this->addElement($fill_windowField->setLabel($language->text('vwvc', 'fill_window')));

        // FloodProtection Field
        $flood_protectionField = new TextField('flood_protection');
        $flood_protectionField->setValue(3);
        $this->addElement($flood_protectionField->setLabel($language->text('vwvc', 'flood_protection')));

        // Filter regex Field
        $filter_regexField = new TextField('filter_regex');
        $filter_regexField->setValue('(?i)(fuck|cunt)(?-i)');
        $this->addElement($filter_regexField->setLabel($language->text('vwvc', 'filter_regex')));

        // Filter replace Field
        $filter_replaceField = new TextField('filter_replace');
        $filter_replaceField->setValue('**');
        $this->addElement($filter_replaceField->setLabel($language->text('vwvc', 'filter_replace')));

        // Show Camera Settings Field
        $show_camera_settingsField = new Selectbox('show_camera_settings');
        $show_camera_settingsField->addOptions($permArr1);
        $show_camera_settingsField->setRequired();
        $show_camera_settingsField->setHasInvitation(false);
        $this->addElement($show_camera_settingsField->setLabel($language->text('vwvc', 'show_camera_settings')));

        // Advanced Camera Settings Field
        $advanced_camera_settingsField = new Selectbox('advanced_camera_settings');
        $advanced_camera_settingsField->addOptions($permArr1);
        $advanced_camera_settingsField->setRequired();
        $advanced_camera_settingsField->setHasInvitation(false);
        $this->addElement($advanced_camera_settingsField->setLabel($language->text('vwvc', 'advanced_camera_settings')));

        // Configure Source Field
        $configure_sourceField = new Selectbox('configure_source');
        $configure_sourceField->addOptions($permArr1);
        $configure_sourceField->setRequired();
        $configure_sourceField->setHasInvitation(false);
        $this->addElement($configure_sourceField->setLabel($language->text('vwvc', 'configure_source')));

        // Disable Video Field
        $disable_videoField = new Selectbox('disable_video');
        $disable_videoField->addOptions($permArr1);
        $disable_videoField->setRequired();
        $disable_videoField->setHasInvitation(false);
        $this->addElement($disable_videoField->setLabel($language->text('vwvc', 'disable_video')));

        // disable_sound Field
        $disable_soundField = new Selectbox('disable_sound');
        $disable_soundField->addOptions($permArr1);
        $disable_soundField->setRequired();
        $disable_soundField->setHasInvitation(false);
        $this->addElement($disable_soundField->setLabel($language->text('vwvc', 'disable_sound')));

        // panel Files Field
        $panel_filesField = new Selectbox('panel_files');
        $panel_filesField->addOptions($permArr1);
        $panel_filesField->setRequired();
        $panel_filesField->setHasInvitation(false);
        $this->addElement($panel_filesField->setLabel($language->text('vwvc', 'panel_files')));

        // panel rooms Field
        $panel_roomsField = new Selectbox('panel_rooms');
        $panel_roomsField->addOptions($permArr1);
        $panel_roomsField->setRequired();
        $panel_roomsField->setHasInvitation(false);
        $this->addElement($panel_roomsField->setLabel($language->text('vwvc', 'panel_rooms')));

        // panel users Field
        $panel_usersField = new Selectbox('panel_users');
        $panel_usersField->addOptions($permArr1);
        $panel_usersField->setRequired();
        $panel_usersField->setHasInvitation(false);
        $this->addElement($panel_usersField->setLabel($language->text('vwvc', 'panel_users')));

        // File Upload Field
        $file_uploadField = new Selectbox('file_upload');
        $file_uploadField->addOptions($permArr1);
        $file_uploadField->setRequired();
        $file_uploadField->setHasInvitation(false);
        $this->addElement($file_uploadField->setLabel($language->text('vwvc', 'file_upload')));

        // file_delete Field
        $file_deleteField = new Selectbox('file_delete');
        $file_deleteField->addOptions($permArr0);
        $file_deleteField->setRequired();
        $file_deleteField->setHasInvitation(false);
        $this->addElement($file_deleteField->setLabel($language->text('vwvc', 'file_delete')));

        // Tutorial Field
        $tutorialField = new Selectbox('tutorial');
        $tutorialField->addOptions($permArr1);
        $tutorialField->setRequired();
        $tutorialField->setHasInvitation(false);
        $this->addElement($tutorialField->setLabel($language->text('vwvc', 'tutorial')));

        // Auto View Cameras Field
        $auto_view_camerasField = new Selectbox('auto_view_cameras');
        $auto_view_camerasField->addOptions($permArr1);
        $auto_view_camerasField->setRequired();
        $auto_view_camerasField->setHasInvitation(false);
        $this->addElement($auto_view_camerasField->setLabel($language->text('vwvc', 'auto_view_cameras')));

        // Show Timer Field
        $show_timerField = new Selectbox('show_timer');
        $show_timerField->addOptions($permArr1);
        $show_timerField->setRequired();
        $show_timerField->setHasInvitation(false);
        $this->addElement($show_timerField->setLabel($language->text('vwvc', 'show_timer')));

        // writeText Field
        $write_textField = new Selectbox('write_text');
        $write_textField->addOptions($permArr1);
        $write_textField->setRequired();
        $write_textField->setHasInvitation(false);
        $this->addElement($write_textField->setLabel($language->text('vwvc', 'write_text')));

        // regularWatch Field
        $regular_watchField = new Selectbox('regular_watch');
        $regular_watchField->addOptions($permArr1);
        $regular_watchField->setRequired();
        $regular_watchField->setHasInvitation(false);
        $this->addElement($regular_watchField->setLabel($language->text('vwvc', 'regular_watch')));

        // newWatch Field
        $new_watchField = new Selectbox('new_watch');
        $new_watchField->addOptions($permArr1);
        $new_watchField->setRequired();
        $new_watchField->setHasInvitation(false);
        $this->addElement($new_watchField->setLabel($language->text('vwvc', 'new_watch')));

        // privateTextchat Field
        $private_textchatField = new Selectbox('private_textchat');
        $private_textchatField->addOptions($permArr1);
        $private_textchatField->setRequired();
        $private_textchatField->setHasInvitation(false);
        $this->addElement($private_textchatField->setLabel($language->text('vwvc', 'private_textchat')));

        // user_list Field
        $user_listField = new Textarea('user_list');
        $this->addElement($user_listField->setLabel($language->text('vwvc', 'user_list')));

        // moderator_list Field
        $moderator_listField = new Textarea('moderator_list');
        $userService = BOL_UserService::getInstance();
        $user = $userService->findUserById(OW::getUser()->getId());
        $username = $user->getUsername();
        $moderator_listField->setValue($username);
        $this->addElement($moderator_listField->setLabel($language->text('vwvc', 'moderator_list')));

        // administrator Field
        $administratorField = new Selectbox('administrator');
        $administratorField->addOptions($permArr0);
        $administratorField->setRequired();
        $administratorField->setHasInvitation(false);
        $this->addElement($administratorField->setLabel($language->text('vwvc', 'administrator')));

        // clean_up Field
        $clean_upField = new TextField('clean_up');
        $clean_upField->setValue(0);
        $this->addElement($clean_upField->setLabel($language->text('vwvc', 'clean_up')));

        $tagsField = new TagsField('tags');
        $this->addElement($tagsField->setLabel($language->text('vwvc', 'tags')));

        $submit = new Submit('add');
        $submit->setValue($language->text('vwvc', 'btn_add'));
        $this->addElement($submit);
    }

    /**
     * Adds vwvc clip
     *
     * @return boolean
     */
    public function process()
    {
        $values = $this->getValues();
        $clipService = VWVC_BOL_ClipService::getInstance();

        $clip = new VWVC_BOL_Clip();
        $clip->title = htmlspecialchars($values['room_name']);
        $clip->description = htmlspecialchars($values['description']);
        $clip->welcome = htmlspecialchars($values['welcome']);
        $cam = $values['resolution'];
        $camArr = explode("x", $cam); 
        $clip->camWidth = $camArr[0];
        $clip->camHeight = $camArr[1];
        $clip->camFPS = $values['camera_fps'];
        $clip->micRate = $values['microphone_rate'];
        $clip->soundQuality = $values['soundQuality'];
        $clip->camBandwidth = $values['bandwidth'];
        $clip->background_url = $values['background_url'];
        $clip->layoutCode = htmlspecialchars($values['layout_code']);
        $permission = $values['fill_window']."|";
        $permission .= $values['show_camera_settings']."|";
        $permission .= $values['advanced_camera_settings']."|";
        $permission .= $values['configure_source']."|";
        $permission .= $values['disable_video']."|";
        $permission .= $values['disable_sound']."|";
        $permission .= $values['panel_rooms']."|";
        $permission .= $values['panel_users']."|";
        $permission .= $values['panel_files']."|";
        $permission .= $values['file_upload']."|";
        $permission .= $values['file_delete']."|";
        $permission .= $values['tutorial']."|";
        $permission .= $values['auto_view_cameras']."|";
        $permission .= $values['show_timer']."|";
        $permission .= $values['write_text']."|";
        $permission .= $values['regular_watch']."|";
        $permission .= $values['new_watch']."|";
        $permission .= $values['private_textchat']."|";
        $permission .= $values['administrator']."|";
        $permission .= $values['verbose_level']."|";
        $clip->permission = $permission;
        $clip->floodProtection = $values['flood_protection'];
        $clip->filterRegex = $values['filter_regex'];
        $clip->filterReplace = $values['filter_replace'];
        $clip->user_list = $values['user_list'];
        $clip->moderator_list = $values['moderator_list'];
        $clip->online = "no";
        $clip->onlineCount = 0;
        $clip->onlineUser = "0";
        $clip->onlineUsers = "0";

        $description = UTIL_HtmlTag::stripJs($values['description']);
        $description = UTIL_HtmlTag::stripTags($description, array('frame', 'style'), array(), true);
        $clip->description = $description;
        $clip->userId = OW::getUser()->getId();

//        $clip->code = UTIL_HtmlTag::stripJs($values['code']);

//        $prov = new VideoProviders($clip->code);

        $privacy = OW::getEventManager()->call(
            'plugin.privacy.get_privacy', 
            array('ownerId' => $clip->userId, 'action' => 'videoconference_view_video')
        );
                    
//        $clip->provider = $prov->detectProvider();
        $clip->addDatetime = time();
        $clip->modifDatetime = time();
        $config = OW::getConfig();
        $status = $config->getValue('vwvc', 'status');
        $clip->status = $status;
        $clip->privacy = mb_strlen($privacy) ? $privacy : 'everybody';

        $eventParams = array('pluginKey' => 'vwvc', 'action' => 'add_vwvc');

        if ( OW::getEventManager()->call('usercredits.check_balance', $eventParams) === true )
        {
            OW::getEventManager()->call('usercredits.track_action', $eventParams);
        }
        
        if ( $clipService->addClip($clip) )
        {
            BOL_TagService::getInstance()->updateEntityTags($clip->id, 'vwvc', TagsField::getTags($values['tags']));
            
            // Newsfeed
            $event = new OW_Event('feed.action', array(
                'pluginKey' => 'vwvc',
                'entityType' => 'vwvc_comments',
                'entityId' => $clip->id,
                'userId' => $clip->userId
            ));
            
            OW::getEventManager()->trigger($event);

            return array('result' => true, 'id' => $clip->id);
        }

        return false;
    }
}
