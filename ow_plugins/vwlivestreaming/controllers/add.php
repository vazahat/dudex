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
 * Vwls add action controller
 *
 * @author Egor Bulgakov <egor.bulgakov@gmail.com>
 * @package ow.plugin.vwls.controllers
 * @since 1.0
 */
class VWLS_CTRL_Add extends OW_ActionController
{

    /**
     * Default action
     */
    public function index()
    {
        $language = OW::getLanguage();
        $clipService = VWLS_BOL_ClipService::getInstance();
        $userId = OW::getUser()->getId();

        if ( !OW::getUser()->isAuthorized('vwls', 'add') )
        {
            $this->setTemplate(OW::getPluginManager()->getPlugin('base')->getCtrlViewDir() . 'authorization_failed.html');
            return;
        }

        // member list
        $config = OW::getConfig();
        $member = $config->getValue('vwls', 'member');
        $memberList = $config->getValue('vwls', 'member_list');


        $eventParams = array('pluginKey' => 'vwls', 'action' => 'add_vwls');
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
    
                $vwlsAddForm = new vwlsAddForm();
                $this->addForm($vwlsAddForm);
    
                $userId = OW::getUser()->getId();
    
                if ( OW::getRequest()->isPost() && $vwlsAddForm->isValid($_POST) )
                {
                    $values = $vwlsAddForm->getValues();
                    $code = $clipService->validateClipCode($values['code']);
                    
                    if ( !BOL_TextFormatService::getInstance()->isCodeResourceValid($code) )
                    {
                        OW::getFeedback()->warning($language->text('vwls', 'resource_not_allowed'));
                        $this->redirect();
                    }
                    
                    $res = $vwlsAddForm->process();
                    OW::getFeedback()->info($language->text('vwls', 'clip_added'));
                    $this->redirect(OW::getRouter()->urlForRoute('vwview_clip_ls', array('id' => $res['id'])));
                }
      				}
      			 }
      			 if ($found === 0) {
               $this->assign('auth_msg', $language->text('vwls', 'adding_denied'));
      			 }
      		  }
        } else {
                $this->assign('auth_msg', null);
    
                $vwlsAddForm = new vwlsAddForm();
                $this->addForm($vwlsAddForm);
    
                $userId = OW::getUser()->getId();
    
                if ( OW::getRequest()->isPost() && $vwlsAddForm->isValid($_POST) )
                {
                    $values = $vwlsAddForm->getValues();
                    $code = $clipService->validateClipCode($values['code']);
                    
                    if ( !BOL_TextFormatService::getInstance()->isCodeResourceValid($code) )
                    {
                        OW::getFeedback()->warning($language->text('vwls', 'resource_not_allowed'));
                        $this->redirect();
                    }
                    
                    $res = $vwlsAddForm->process();
                    OW::getFeedback()->info($language->text('vwls', 'clip_added'));
                    $this->redirect(OW::getRouter()->urlForRoute('vwview_clip_ls', array('id' => $res['id'])));
                }
        }

        if ( !OW::getRequest()->isAjax() )
        {
            OW::getNavigation()->activateMenuItem(OW_Navigation::MAIN, 'vwls', 'vwls');
        }

        OW::getDocument()->setHeading($language->text('vwls', 'page_title_add_vwls'));
        OW::getDocument()->setHeadingIconClass('ow_ic_vwls');
        OW::getDocument()->setTitle($language->text('vwls', 'meta_title_vwls_add'));
    }
}

/**
 * Vwls add form class
 */
class vwlsAddForm extends Form
{

    /**
     * Class constructor
     *
     */
    public function __construct()
    {
        parent::__construct('vwlsAddForm');

        $language = OW::getLanguage();

        // select box for broadcasting
        $arr1 = array(
          '1' => 'yes',
          '0' => 'no'
        );
        $arr0 = array(
          '0' => 'no',
          '1' => 'yes'
        );

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
        $permArr2 = array(
          '3' => 'none',
          '2' => 'owner',
          '1' => 'moderators',
          '0' => 'all'
        );

        // room_name Field
        $generated =  base_convert((time()-1224000000).rand(0,10),10,36);
        $room_nameField = new TextField('room_name');
        $sValidator = new StringValidator(1, 22);
        $room_nameField->addValidator($sValidator);
        $room_nameField->setRequired(true);
        $room_nameField->setValue($generated);

        $this->addElement($room_nameField->setLabel($language->text('vwls', 'room_name')));

        // Description Field
        $descriptionField = new Textarea('description');
        $this->addElement($descriptionField->setLabel($language->text('vwls', 'description')));

        // Room limit Field
        $room_limitField = new TextField('room_limit');
        $room_limitField->setRequired(false);
        $room_limitField->setValue(0);
        $this->addElement($room_limitField->setLabel($language->text('vwls', 'room_limit')));

        // Show Camera Settings Field
        $show_camera_settingsField = new Selectbox('show_camera_settings');
        $show_camera_settingsField->addOptions($arr1);
        $show_camera_settingsField->setRequired();
        $show_camera_settingsField->setHasInvitation(false);
        $this->addElement($show_camera_settingsField->setLabel($language->text('vwls', 'show_camera_settings')));

        // Advanced Camera Settings Field
        $advanced_camera_settingsField = new Selectbox('advanced_camera_settings');
        $advanced_camera_settingsField->addOptions($arr1);
        $advanced_camera_settingsField->setRequired();
        $advanced_camera_settingsField->setHasInvitation(false);
        $this->addElement($advanced_camera_settingsField->setLabel($language->text('vwls', 'advanced_camera_settings')));

        // Configure Source Field
        $configure_sourceField = new Selectbox('configure_source');
        $configure_sourceField->addOptions($arr1);
        $configure_sourceField->setRequired();
        $configure_sourceField->setHasInvitation(false);
        $this->addElement($configure_sourceField->setLabel($language->text('vwls', 'configure_source')));

        // user_list Field
        $user_listField = new Textarea('user_list');
        $this->addElement($user_listField->setLabel($language->text('vwls', 'user_list')));

        // moderator_list Field
        $moderator_listField = new Textarea('moderator_list');
        $userService = BOL_UserService::getInstance();
        $user = $userService->findUserById(OW::getUser()->getId());
        $username = $user->getUsername();
        $moderator_listField->setValue($username);
        $this->addElement($moderator_listField->setLabel($language->text('vwls', 'moderator_list')));

        // administrator Field
/**        $administratorField = new Selectbox('administrator');
        $administratorField->addOptions($permArr0);
        $administratorField->setRequired();
        $administratorField->setHasInvitation(false);
        $this->addElement($administratorField->setLabel($language->text('vwls', 'administrator')));
*/
        // clean_up Field
        $clean_upField = new TextField('clean_up');
        $clean_upField->setValue(0);
        $this->addElement($clean_upField->setLabel($language->text('vwls', 'clean_up')));


        // Broadcasting
        // welcome Field
        $welcomeField = new Textarea('welcome');
        $welcomeField->setValue($language->text('vwls', 'welcome_default'));
        $this->addElement($welcomeField->setLabel($language->text('vwls', 'welcome')));

        // Only video Field
        $only_videoField = new Selectbox('only_video');
        $only_videoField->addOptions($arr0);
        $only_videoField->setRequired();
        $only_videoField->setHasInvitation(false);
        $this->addElement($only_videoField->setLabel($language->text('vwls', 'only_video')));

        // No Video Field
        $no_videoField = new Selectbox('no_video');
        $no_videoField->addOptions($arr0);
        $no_videoField->setRequired();
        $no_videoField->setHasInvitation(false);
        $this->addElement($no_videoField->setLabel($language->text('vwls', 'no_video')));

        // No Embeds Field
        $no_embedsField = new Selectbox('no_embeds');
        $no_embedsField->addOptions($arr0);
        $no_embedsField->setRequired();
        $no_embedsField->setHasInvitation(false);
        $this->addElement($no_embedsField->setLabel($language->text('vwls', 'no_embeds')));

        // Show Timer Field
        $show_timerField = new Selectbox('show_timer');
        $show_timerField->addOptions($arr1);
        $show_timerField->setRequired();
        $show_timerField->setHasInvitation(false);
        $this->addElement($show_timerField->setLabel($language->text('vwls', 'show_timer')));

        // writeText Field
        $write_textField = new Selectbox('write_text');
        $write_textField->addOptions($arr1);
        $write_textField->setRequired();
        $write_textField->setHasInvitation(false);
        $this->addElement($write_textField->setLabel($language->text('vwls', 'write_text')));

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
        $this->addElement($resolutionField->setLabel($language->text('vwls', 'resolution')));

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
        $this->addElement($camera_fpsField->setLabel($language->text('vwls', 'camera_fps')));

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
        $this->addElement($microphone_rateField->setLabel($language->text('vwls', 'microphone_rate')));

        // soundQuality Field
        $soundQualityField = new TextField('soundQuality');
        $soundQualityField->setRequired(true);
        $soundQualityField->setValue(9);
        $this->addElement($soundQualityField->setLabel($language->text('vwls', 'soundQuality')));

        // Bandwidth Field
        $bandwidthField = new TextField('bandwidth');
        $bandwidthField->setRequired(true);
        $bandwidthField->setValue(40960);
        $this->addElement($bandwidthField->setLabel($language->text('vwls', 'bandwidth')));

        // FloodProtection Field
        $flood_protectionField = new TextField('flood_protection');
        $flood_protectionField->setValue(3);
        $this->addElement($flood_protectionField->setLabel($language->text('vwls', 'flood_protection')));

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
        $this->addElement($verbose_levelField->setLabel($language->text('vwls', 'verbose_level')));

        // Label Color Field
        $label_colorField = new TextField('label_color');
        $label_colorField->setValue('FFFFFF');
        $this->addElement($label_colorField->setLabel($language->text('vwls', 'label_color')));

        // privateTextchat Field
        $private_textchatField = new Selectbox('private_textchat');
        $private_textchatField->addOptions($arr1);
        $private_textchatField->setRequired();
        $private_textchatField->setHasInvitation(false);
        $this->addElement($private_textchatField->setLabel($language->text('vwls', 'private_textchat')));

        // Layout Code Field
        $layout_codeField = new Textarea('layout_code');
        $this->addElement($layout_codeField->setLabel($language->text('vwls', 'layout_code')));

        // Fill window Field
        $fill_windowField = new Selectbox('fill_window');
        $fill_windowField->addOptions($arr1);
        $fill_windowField->setRequired();
        $fill_windowField->setHasInvitation(false);
        $this->addElement($fill_windowField->setLabel($language->text('vwls', 'fill_window')));


        // Video / Watch
        // welcome Field
        $welcome2Field = new Textarea('welcome2');
        $welcome2Field->setValue($language->text('vwls', 'welcome_default2'));
        $this->addElement($welcome2Field->setLabel($language->text('vwls', 'welcome2')));

        // Offline message Field
        $offline_messageField = new Textarea('offline_message');
        $this->addElement($offline_messageField->setLabel($language->text('vwls', 'offline_message')));

        // FloodProtection2 Field
        $flood_protection2Field = new TextField('flood_protection2');
        $flood_protection2Field->setValue(3);
        $this->addElement($flood_protection2Field->setLabel($language->text('vwls', 'flood_protection2')));

        // Filter regex Field
        $filter_regexField = new TextField('filter_regex');
        $filter_regexField->setValue('(?i)(fuck|cunt)(?-i)');
        $this->addElement($filter_regexField->setLabel($language->text('vwls', 'filter_regex')));

        // Filter replace Field
        $filter_replaceField = new TextField('filter_replace');
        $filter_replaceField->setValue('**');
        $this->addElement($filter_replaceField->setLabel($language->text('vwls', 'filter_replace')));

        // Layout Code2 Field
        $layout_code2Field = new Textarea('layout_code2');
        $this->addElement($layout_code2Field->setLabel($language->text('vwls', 'layout_code2')));

        // Fill window2 Field
        $fill_window2Field = new Selectbox('fill_window2');
        $fill_window2Field->addOptions($permArr0);
        $fill_window2Field->setRequired();
        $fill_window2Field->setHasInvitation(false);
        $this->addElement($fill_window2Field->setLabel($language->text('vwls', 'fill_window2')));

        // writeText2 Field
        $write_text2Field = new Selectbox('write_text2');
        $write_text2Field->addOptions($permArr1);
        $write_text2Field->setRequired();
        $write_text2Field->setHasInvitation(false);
        $this->addElement($write_text2Field->setLabel($language->text('vwls', 'write_text2')));

        // Enable Video Field
        $enable_videoField = new Selectbox('enable_video');
        $enable_videoField->addOptions($permArr1);
        $enable_videoField->setRequired();
        $enable_videoField->setHasInvitation(false);
        $this->addElement($enable_videoField->setLabel($language->text('vwls', 'enable_video')));

        // Enable chat Field
        $enable_chatField = new Selectbox('enable_chat');
        $enable_chatField->addOptions($permArr1);
        $enable_chatField->setRequired();
        $enable_chatField->setHasInvitation(false);
        $this->addElement($enable_chatField->setLabel($language->text('vwls', 'enable_chat')));

        // Enable users Field
        $enable_usersField = new Selectbox('enable_users');
        $enable_usersField->addOptions($permArr1);
        $enable_usersField->setRequired();
        $enable_usersField->setHasInvitation(false);
        $this->addElement($enable_usersField->setLabel($language->text('vwls', 'enable_users')));


        $tagsField = new TagsField('tags');
        $this->addElement($tagsField->setLabel($language->text('vwls', 'tags')));

        $submit = new Submit('add');
        $submit->setValue($language->text('vwls', 'btn_add'));
        $this->addElement($submit);
    }

    /**
     * Adds vwls clip
     *
     * @return boolean
     */
    public function process()
    {
        $values = $this->getValues();
        $clipService = VWLS_BOL_ClipService::getInstance();

        $clip = new VWLS_BOL_Clip();
        $clip->title = htmlspecialchars($values['room_name']);
        $clip->roomLimit = $values['room_limit'];
        $clip->user_list = $values['user_list'];
        $clip->moderator_list = $values['moderator_list'];

        $clip->welcome = htmlspecialchars($values['welcome']);
        $cam = $values['resolution'];
        $camArr = explode("x", $cam); 
        $clip->camWidth = $camArr[0];
        $clip->camHeight = $camArr[1];
        $clip->camFPS = $values['camera_fps'];
        $clip->micRate = $values['microphone_rate'];
        $clip->soundQuality = $values['soundQuality'];
        $clip->camBandwidth = $values['bandwidth'];
        $clip->floodProtection = $values['flood_protection'];
        $clip->labelColor = $values['label_color'];
        $clip->layoutCode = $values['layout_code'];

        $clip->welcome2 = htmlspecialchars($values['welcome2']);
        $clip->offlineMessage = htmlspecialchars($values['offline_message']);
        $clip->floodProtection2 = $values['flood_protection2'];
        $clip->layoutCode2 = htmlspecialchars($values['layout_code2']);
        $clip->filterRegex = $values['filter_regex'];
        $clip->filterReplace = $values['filter_replace'];


        $permission = $values['show_camera_settings']."|";
        $permission .= $values['advanced_camera_settings']."|";
        $permission .= $values['configure_source']."|";
        $permission .= $values['only_video']."|";
        $permission .= $values['no_video']."|";
        $permission .= $values['no_embeds']."|";
        $permission .= $values['show_timer']."|";
        $permission .= $values['write_text']."|";
        $permission .= $values['private_textchat']."|";
        $permission .= $values['fill_window']."|";
        $permission .= $values['write_text2']."|";
        $permission .= $values['enable_video']."|";
        $permission .= $values['enable_chat']."|";
        $permission .= $values['enable_users']."|";
        $permission .= $values['fill_window2']."|";
        $permission .= $values['verbose_level']."|";
        $clip->permission = $permission;
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
            array('ownerId' => $clip->userId, 'action' => 'livestreaming_view_video')
        );
                    
//        $clip->provider = $prov->detectProvider();
        $clip->addDatetime = time();
        $clip->modifDatetime = time();
        $config = OW::getConfig();
        $status = $config->getValue('vwls', 'status');
        $clip->status = $status;
        $clip->privacy = mb_strlen($privacy) ? $privacy : 'everybody';

        $eventParams = array('pluginKey' => 'vwls', 'action' => 'add_vwls');

        if ( OW::getEventManager()->call('usercredits.check_balance', $eventParams) === true )
        {
            OW::getEventManager()->call('usercredits.track_action', $eventParams);
        }
        
        if ( $clipService->addClip($clip) )
        {
            BOL_TagService::getInstance()->updateEntityTags($clip->id, 'vwls', TagsField::getTags($values['tags']));
            
            // Newsfeed
            $event = new OW_Event('feed.action', array(
                'pluginKey' => 'vwls',
                'entityType' => 'vwls_comments',
                'entityId' => $clip->id,
                'userId' => $clip->userId
            ));
            
            OW::getEventManager()->trigger($event);

            return array('result' => true, 'id' => $clip->id);
        }

        return false;
    }
}
