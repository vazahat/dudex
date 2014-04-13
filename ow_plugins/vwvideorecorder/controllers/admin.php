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
 * Vwvr admin action controller
 *
 * @author Egor Bulgakov <egor.bulgakov@gmail.com>
 * @package ow.plugin.vwvr.controllers
 * @since 1.0
 */
class VWVR_CTRL_Admin extends ADMIN_CTRL_Abstract
{

    /**
     * Default action
     */
    public function index()
    {
        $language = OW::getLanguage();

        $item = new BASE_MenuItem();
        $item->setLabel($language->text('vwvr', 'admin_menu_general'));
        $item->setUrl(OW::getRouter()->urlForRoute('vwvr_admin_config'));
        $item->setKey('general');
        $item->setIconClass('ow_ic_gear_wheel');

        $menu = new BASE_CMP_ContentMenu(array($item));
        $this->addComponent('menu', $menu);

        $configs = OW::getConfig()->getValues('vwvr');

        $configSaveForm = new ConfigSaveForm();
        $this->addForm($configSaveForm);

        if ( OW::getRequest()->isPost() && $configSaveForm->isValid($_POST) )
        {
            $res = $configSaveForm->process();
            OW::getFeedback()->info($language->text('vwvr', 'settings_updated'));
            $this->redirect(OW::getRouter()->urlForRoute('vwvr_admin_config'));
        }

        if ( !OW::getRequest()->isAjax() )
        {
            $this->setPageHeading(OW::getLanguage()->text('vwvr', 'admin_config'));
            $this->setPageHeadingIconClass('ow_ic_vwvr');

            $menu->getElement('general')->setActive(true);
        }

        $configSaveForm->getElement('server')->setValue($configs['server']);
        $configSaveForm->getElement('serverAMF')->setValue($configs['serverAMF']);

        $configSaveForm->getElement('videoCodec')->setValue($configs['videoCodec']);
        $configSaveForm->getElement('codecProfile')->setValue($configs['codecProfile']);
        $configSaveForm->getElement('codecLevel')->setValue($configs['codecLevel']);
        $configSaveForm->getElement('soundCodec')->setValue($configs['soundCodec']);
        $configSaveForm->getElement('soundQuality')->setValue($configs['soundQuality']);
        // $configSaveForm->getElement('microphone_rate')->setValue($configs['micRate']);

        $configSaveForm->getElement('bufferLive')->setValue($configs['bufferLive']);
        $configSaveForm->getElement('camMaxBandwidth')->setValue($configs['camMaxBandwidth']);
        $configSaveForm->getElement('bufferLive')->setValue($configs['bufferLive']);
        $configSaveForm->getElement('bufferFull')->setValue($configs['bufferFull']);
        $configSaveForm->getElement('bufferLivePlayback')->setValue($configs['bufferLivePlayback']);
        $configSaveForm->getElement('bufferFullPlayback')->setValue($configs['bufferFullPlayback']);
        $configSaveForm->getElement('availability')->setValue($configs['availability']);
        $configSaveForm->getElement('status')->setValue($configs['status']);
        $configSaveForm->getElement('member')->setValue($configs['member']);
        $configSaveForm->getElement('member_list')->setValue($configs['member_list']);
        $configSaveForm->getElement('baseSwf_url')->setValue($configs['baseSwf_url']);
        $configSaveForm->getElement('record_path')->setValue($configs['recordPath']);

        $configSaveForm->getElement('record_limit')->setValue($configs['recordLimit']);
        $configSaveForm->getElement('resolution')->setValue($configs['camWidth']."x".$configs['camHeight']);
        $configSaveForm->getElement('camera_fps')->setValue($configs['camFPS']);
        $configSaveForm->getElement('microphone_rate')->setValue($configs['micRate']);
        $configSaveForm->getElement('bandwidth')->setValue($configs['camBandwidth']);
        $configSaveForm->getElement('layout_code')->setValue($configs['layoutCode']);
        $configSaveForm->getElement('show_camera_settings')->setValue($configs['showCamSettings']);
        $configSaveForm->getElement('advanced_camera_settings')->setValue($configs['advancedCamSettings']);
        $configSaveForm->getElement('fill_window')->setValue($configs['fillWindow']);
    }
}

/**
 * Save Configurations form class
 */
class ConfigSaveForm extends Form
{

    /**
     * Class constructor
     *
     */
    public function __construct()
    {
        parent::__construct('configSaveForm');

        $language = OW::getLanguage();

        // select box for permission
        $arr1 = array(
          '1' => 'yes',
          '0' => 'no'
        );

        // Record path Field
        $record_limitPath = new TextField('record_path');
        $record_limitPath->setRequired(false);
        $this->addElement($record_limitPath->setLabel($language->text('vwvr', 'record_path')));

        // Record limit Field
        $record_limitField = new TextField('record_limit');
        $record_limitField->setRequired(false);
        $this->addElement($record_limitField->setLabel($language->text('vwvr', 'record_limit')));

        // Show Camera Settings Field
        $show_camera_settingsField = new Selectbox('show_camera_settings');
        $show_camera_settingsField->addOptions($arr1);
        $show_camera_settingsField->setRequired();
        $show_camera_settingsField->setHasInvitation(false);
        $this->addElement($show_camera_settingsField->setLabel($language->text('vwvr', 'show_camera_settings')));

        // Advanced Camera Settings Field
        $advanced_camera_settingsField = new Selectbox('advanced_camera_settings');
        $advanced_camera_settingsField->addOptions($arr1);
        $advanced_camera_settingsField->setRequired();
        $advanced_camera_settingsField->setHasInvitation(false);
        $this->addElement($advanced_camera_settingsField->setLabel($language->text('vwvr', 'advanced_camera_settings')));

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
        $this->addElement($resolutionField->setLabel($language->text('vwvr', 'resolution')));

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
        $this->addElement($camera_fpsField->setLabel($language->text('vwvr', 'camera_fps')));

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
        $this->addElement($microphone_rateField->setLabel($language->text('vwvr', 'microphone_rate')));

        // Bandwidth Field
        $bandwidthField = new TextField('bandwidth');
        $bandwidthField->setRequired(true);
        $this->addElement($bandwidthField->setLabel($language->text('vwvr', 'bandwidth')));

        // Layout Code Field
        $layout_codeField = new Textarea('layout_code');
        $this->addElement($layout_codeField->setLabel($language->text('vwvr', 'layout_code')));

        // Fill window Field
        $fill_windowField = new Selectbox('fill_window');
        $fill_windowField->addOptions($arr1);
        $fill_windowField->setRequired();
        $fill_windowField->setHasInvitation(false);
        $this->addElement($fill_windowField->setLabel($language->text('vwvr', 'fill_window')));

        // baseSwf_url Field
        $baseSwf_urlField = new TextField('baseSwf_url');
        $baseSwf_urlField->addAttribute('readonly', 'readonly');
        $this->addElement($baseSwf_urlField->setLabel($language->text('vwvr', 'baseSwf_url')));
        
        // server Field
        $serverField = new TextField('server');
        $serverField->setRequired(true);
        $this->addElement($serverField->setLabel($language->text('vwvr', 'server')));

        // serverAMF Field
        $serverAMFField = new TextField('serverAMF');
        $serverAMFField->setRequired(true);
        $this->addElement($serverAMFField->setLabel($language->text('vwvr', 'serverAMF')));

        // videoCodec Field
        $videoCodecField = new TextField('videoCodec');
        $videoCodecField->setRequired(true);
        $this->addElement($videoCodecField->setLabel($language->text('vwvr', 'videoCodec')));

        // codecLevel Field
        $codecLevelField = new TextField('codecLevel');
        $codecLevelField->setRequired(true);
        $this->addElement($codecLevelField->setLabel($language->text('vwvr', 'codecLevel')));

        // soundQuality Field
        $soundQualityField = new TextField('soundQuality');
        $soundQualityField->setRequired(true);
        $this->addElement($soundQualityField->setLabel($language->text('vwvr', 'soundQuality')));

        // codecProfile Field
        $codecProfileArr = array(
          'main' => 'main',
          'baseline' => 'baseline'
        );
        $codecProfileField = new Selectbox('codecProfile');
        $codecProfileField->addOptions($codecProfileArr);
        $codecProfileField->setRequired();
        $codecProfileField->setHasInvitation(false);
        $this->addElement($codecProfileField->setLabel($language->text('vwvr', 'codecProfile')));

        // soundCodec Field
        $soundCodecArr = array(
          'Speex' => 'Speex',
          'Nellymoser' => 'Nellymoser'
        );
        $soundCodecField = new Selectbox('soundCodec');
        $soundCodecField->addOptions($soundCodecArr);
        $soundCodecField->setRequired();
        $soundCodecField->setHasInvitation(false);
        $this->addElement($soundCodecField->setLabel($language->text('vwvr', 'soundCodec')));

        // camMaxBandwidth Field
        $camMaxBandwidthField = new TextField('camMaxBandwidth');
        $camMaxBandwidthField->setRequired(true);
        $this->addElement($camMaxBandwidthField->setLabel($language->text('vwvr', 'camMaxBandwidth')));

        // bufferLive Field
        $bufferLiveField = new TextField('bufferLive');
        $bufferLiveField->setRequired(true);
        $this->addElement($bufferLiveField->setLabel($language->text('vwvr', 'bufferLive')));

        // bufferFull Field
        $bufferFullField = new TextField('bufferFull');
        $bufferFullField->setRequired(true);
        $this->addElement($bufferFullField->setLabel($language->text('vwvr', 'bufferFull')));

        // bufferLivePlayback Field
        $bufferLivePlaybackField = new TextField('bufferLivePlayback');
        $bufferLivePlaybackField->setRequired(true);
        $this->addElement($bufferLivePlaybackField->setLabel($language->text('vwvr', 'bufferLivePlayback')));

        // bufferFullPlayback Field
        $bufferFullPlaybackField = new TextField('bufferFullPlayback');
        $bufferFullPlaybackField->setRequired(true);
        $this->addElement($bufferFullPlaybackField->setLabel($language->text('vwvr', 'bufferFullPlayback')));

        // availability Field
        $availabilityField = new TextField('availability');
        $availabilityField->setRequired(true);
        $this->addElement($availabilityField->setLabel($language->text('vwvr', 'availability')));

        // status Field
        $statusBox = array(
          'approved' => 'approved',
          'pending' => 'pending'
        );
        $statusField = new Selectbox('status');
        $statusField->addOptions($statusBox);
        $statusField->setRequired();
        $statusField->setHasInvitation(false);
        $this->addElement($statusField->setLabel($language->text('vwvr', 'status')));

        // who can create room Field
        $memberBox = array(
          'all' => 'all',
          'member' => 'member list'
        );
        $memberField = new Selectbox('member');
        $memberField->addOptions($memberBox);
        $memberField->setRequired();
        $memberField->setHasInvitation(false);
        $this->addElement($memberField->setLabel($language->text('vwvr', 'member')));

        // member_list Field
        $member_listField = new Textarea('member_list');
        $userService = BOL_UserService::getInstance();
        $user = $userService->findUserById(OW::getUser()->getId());
        $username = $user->getUsername();
        $member_listField->setValue($username);
        $this->addElement($member_listField->setLabel($language->text('vwvr', 'member_list')));

        // submit
        $submit = new Submit('save');
        $submit->setValue($language->text('vwvr', 'btn_edit'));
        $this->addElement($submit);
    }

    /**
     * Updates vwvr plugin configuration
     *
     * @return boolean
     */
    public function process()
    {
        $values = $this->getValues();

        $config = OW::getConfig();

        $config->saveConfig('vwvr', 'server', $values['server']);
        $config->saveConfig('vwvr', 'serverAMF', $values['serverAMF']);

        $config->saveConfig('vwvr', 'videoCodec', $values['videoCodec']);
        $config->saveConfig('vwvr', 'codecProfile', $values['codecProfile']);
        $config->saveConfig('vwvr', 'codecLevel', $values['codecLevel']);
        $config->saveConfig('vwvr', 'soundCodec', $values['soundCodec']);
        $config->saveConfig('vwvr', 'soundQuality', $values['soundQuality']);

        $config->saveConfig('vwvr', 'camMaxBandwidth', $values['camMaxBandwidth']);
        $config->saveConfig('vwvr', 'bufferLive', $values['bufferLive']);
        $config->saveConfig('vwvr', 'bufferFull', $values['bufferFull']);
        $config->saveConfig('vwvr', 'bufferLivePlayback', $values['bufferLivePlayback']);
        $config->saveConfig('vwvr', 'bufferFullPlayback', $values['bufferFullPlayback']);
        $config->saveConfig('vwvr', 'availability', $values['availability']);
        $config->saveConfig('vwvr', 'status', $values['status']);
        $config->saveConfig('vwvr', 'member', $values['member']);
        $config->saveConfig('vwvr', 'member_list', $values['member_list']);
        $config->saveConfig('vwvr', 'baseSwf_url', $values['baseSwf_url']);
        $config->saveConfig('vwvr', 'recordPath', $values['record_path']);

        $cam = $values['resolution'];
        $camArr = explode("x", $cam); 
        $config->saveConfig('vwvr', 'camWidth', $camArr[0]);
        $config->saveConfig('vwvr', 'camHeight', $camArr[1]);

        $config->saveConfig('vwvr', 'showCamSettings', $values['show_camera_settings']);
        $config->saveConfig('vwvr', 'advancedCamSettings', $values['advanced_camera_settings']);
        $config->saveConfig('vwvr', 'recordLimit', $values['record_limit']);
        $config->saveConfig('vwvr', 'camFPS', $values['camera_fps']);
        $config->saveConfig('vwvr', 'micRate', $values['microphone_rate']);
        $config->saveConfig('vwvr', 'camBandwidth', $values['bandwidth']);
        $config->saveConfig('vwvr', 'layoutCode', $values['layout_code']);

        return array('result' => true);
    }
}
