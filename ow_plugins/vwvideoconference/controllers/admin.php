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
 * Vwvc admin action controller
 *
 * @author Egor Bulgakov <egor.bulgakov@gmail.com>
 * @package ow.plugin.vwvc.controllers
 * @since 1.0
 */
class VWVC_CTRL_Admin extends ADMIN_CTRL_Abstract
{

    /**
     * Default action
     */
    public function index()
    {
        $language = OW::getLanguage();

        $item = new BASE_MenuItem();
        $item->setLabel($language->text('vwvc', 'admin_menu_general'));
        $item->setUrl(OW::getRouter()->urlForRoute('vwvc_admin_config'));
        $item->setKey('general');
        $item->setIconClass('ow_ic_gear_wheel');

        $menu = new BASE_CMP_ContentMenu(array($item));
        $this->addComponent('menu', $menu);

        $configs = OW::getConfig()->getValues('vwvc');

        $configSaveForm = new ConfigSaveForm();
        $this->addForm($configSaveForm);

        if ( OW::getRequest()->isPost() && $configSaveForm->isValid($_POST) )
        {
            $res = $configSaveForm->process();
            OW::getFeedback()->info($language->text('vwvc', 'settings_updated'));
            $this->redirect(OW::getRouter()->urlForRoute('vwvc_admin_config'));
        }

        if ( !OW::getRequest()->isAjax() )
        {
            $this->setPageHeading(OW::getLanguage()->text('vwvc', 'admin_config'));
            $this->setPageHeadingIconClass('ow_ic_vwvc');

            $menu->getElement('general')->setActive(true);
        }

        $configSaveForm->getElement('server')->setValue($configs['server']);
        $configSaveForm->getElement('serverAMF')->setValue($configs['serverAMF']);
        $configSaveForm->getElement('serverRTMFP')->setValue($configs['serverRTMFP']);

        $configSaveForm->getElement('enableRTMP')->setValue($configs['enableRTMP']);
        $configSaveForm->getElement('enableP2P')->setValue($configs['enableP2P']);
        $configSaveForm->getElement('supportRTMP')->setValue($configs['supportRTMP']);
        $configSaveForm->getElement('supportP2P')->setValue($configs['supportP2P']);
        $configSaveForm->getElement('alwaysRTMP')->setValue($configs['alwaysRTMP']);
        $configSaveForm->getElement('alwaysP2P')->setValue($configs['alwaysP2P']);
        $configSaveForm->getElement('videoCodec')->setValue($configs['videoCodec']);
        $configSaveForm->getElement('codecProfile')->setValue($configs['codecProfile']);
        $configSaveForm->getElement('codecLevel')->setValue($configs['codecLevel']);
        $configSaveForm->getElement('soundCodec')->setValue($configs['soundCodec']);

        $configSaveForm->getElement('p2pGroup')->setValue($configs['p2pGroup']);
        $configSaveForm->getElement('camMaxBandwidth')->setValue($configs['camMaxBandwidth']);
        $configSaveForm->getElement('bufferLive')->setValue($configs['bufferLive']);
        $configSaveForm->getElement('bufferFull')->setValue($configs['bufferFull']);
        $configSaveForm->getElement('bufferLivePlayback')->setValue($configs['bufferLivePlayback']);
        $configSaveForm->getElement('bufferFullPlayback')->setValue($configs['bufferFullPlayback']);
        $configSaveForm->getElement('disableBandwidthDetection')->setValue($configs['disableBandwidthDetection']);
        $configSaveForm->getElement('disableUploadDetection')->setValue($configs['disableUploadDetection']);
        $configSaveForm->getElement('limitByBandwidth')->setValue($configs['limitByBandwidth']);
        $configSaveForm->getElement('ws_ads')->setValue($configs['ws_ads']);
        $configSaveForm->getElement('adsTimeout')->setValue($configs['adsTimeout']);
        $configSaveForm->getElement('adsInterval')->setValue($configs['adsInterval']);
        $configSaveForm->getElement('statusInterval')->setValue($configs['statusInterval']);
        $configSaveForm->getElement('availability')->setValue($configs['availability']);
        $configSaveForm->getElement('status')->setValue($configs['status']);
        $configSaveForm->getElement('member')->setValue($configs['member']);
        $configSaveForm->getElement('member_list')->setValue($configs['member_list']);
        $configSaveForm->getElement('baseSwf_url')->setValue($configs['baseSwf_url']);
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

        // baseSwf_url Field
        $baseSwf_urlField = new TextField('baseSwf_url');
        $baseSwf_urlField->addAttribute('readonly', 'readonly');
        $this->addElement($baseSwf_urlField->setLabel($language->text('vwvc', 'baseSwf_url')));
        
        // server Field
        $serverField = new TextField('server');
        $serverField->setRequired(true);
        $this->addElement($serverField->setLabel($language->text('vwvc', 'server')));

        // serverAMF Field
        $serverAMFField = new TextField('serverAMF');
        $serverAMFField->setRequired(true);
        $this->addElement($serverAMFField->setLabel($language->text('vwvc', 'serverAMF')));

        // serverRTMFP Field
        $serverRTMFPField = new TextField('serverRTMFP');
        $serverRTMFPField->setRequired(true);
        $this->addElement($serverRTMFPField->setLabel($language->text('vwvc', 'serverRTMFP')));

        // select box for certain parameters
        $par = array(
          '0' => 'no',
          '1' => 'yes'
        );
        $par1 = array(
          '1' => 'yes',
          '0' => 'no'
        );

        // enableRTMP Field
        $enableRTMPField = new Selectbox('enableRTMP');
        $enableRTMPField->addOptions($par1);
        $enableRTMPField->setRequired();
        $enableRTMPField->setHasInvitation(false);
        $this->addElement($enableRTMPField->setLabel($language->text('vwvc', 'enableRTMP')));

        // enableP2P Field
        $enableP2PField = new Selectbox('enableP2P');
        $enableP2PField->addOptions($par);
        $enableP2PField->setRequired();
        $enableP2PField->setHasInvitation(false);
        $this->addElement($enableP2PField->setLabel($language->text('vwvc', 'enableP2P')));

        // supportRTMP Field
        $supportRTMPField = new Selectbox('supportRTMP');
        $supportRTMPField->addOptions($par1);
        $supportRTMPField->setRequired();
        $supportRTMPField->setHasInvitation(false);
        $this->addElement($supportRTMPField->setLabel($language->text('vwvc', 'supportRTMP')));

        // supportP2P Field
        $supportP2PField = new Selectbox('supportP2P');
        $supportP2PField->addOptions($par1);
        $supportP2PField->setRequired();
        $supportP2PField->setHasInvitation(false);
        $this->addElement($supportP2PField->setLabel($language->text('vwvc', 'supportP2P')));

        // alwaysRTMP Field
        $alwaysRTMPField = new Selectbox('alwaysRTMP');
        $alwaysRTMPField->addOptions($par);
        $alwaysRTMPField->setRequired();
        $alwaysRTMPField->setHasInvitation(false);
        $this->addElement($alwaysRTMPField->setLabel($language->text('vwvc', 'alwaysRTMP')));

        // alwaysP2P Field
        $alwaysP2PField = new Selectbox('alwaysP2P');
        $alwaysP2PField->addOptions($par);
        $alwaysP2PField->setRequired();
        $alwaysP2PField->setHasInvitation(false);
        $this->addElement($alwaysP2PField->setLabel($language->text('vwvc', 'alwaysP2P')));

        // videoCodec Field
        $videoCodecField = new TextField('videoCodec');
        $videoCodecField->setRequired(true);
        $this->addElement($videoCodecField->setLabel($language->text('vwvc', 'videoCodec')));

        // codecLevel Field
        $codecLevelField = new TextField('codecLevel');
        $codecLevelField->setRequired(true);
        $this->addElement($codecLevelField->setLabel($language->text('vwvc', 'codecLevel')));

        // codecProfile Field
        $codecProfileArr = array(
          'main' => 'main',
          'baseline' => 'baseline'
        );
        $codecProfileField = new Selectbox('codecProfile');
        $codecProfileField->addOptions($codecProfileArr);
        $codecProfileField->setRequired();
        $codecProfileField->setHasInvitation(false);
        $this->addElement($codecProfileField->setLabel($language->text('vwvc', 'codecProfile')));

        // soundCodec Field
        $soundCodecArr = array(
          'Speex' => 'Speex',
          'Nellymoser' => 'Nellymoser'
        );
        $soundCodecField = new Selectbox('soundCodec');
        $soundCodecField->addOptions($soundCodecArr);
        $soundCodecField->setRequired();
        $soundCodecField->setHasInvitation(false);
        $this->addElement($soundCodecField->setLabel($language->text('vwvc', 'soundCodec')));

        // p2pGroup Field
        $p2pGroupField = new TextField('p2pGroup');
        $p2pGroupField->setRequired(true);
        $this->addElement($p2pGroupField->setLabel($language->text('vwvc', 'p2pGroup')));

        // camMaxBandwidth Field
        $camMaxBandwidthField = new TextField('camMaxBandwidth');
        $camMaxBandwidthField->setRequired(true);
        $this->addElement($camMaxBandwidthField->setLabel($language->text('vwvc', 'camMaxBandwidth')));

        // bufferLive Field
        $bufferLiveField = new TextField('bufferLive');
        $bufferLiveField->setRequired(true);
        $this->addElement($bufferLiveField->setLabel($language->text('vwvc', 'bufferLive')));

        // bufferFull Field
        $bufferFullField = new TextField('bufferFull');
        $bufferFullField->setRequired(true);
        $this->addElement($bufferFullField->setLabel($language->text('vwvc', 'bufferFull')));

        // bufferLivePlayback Field
        $bufferLivePlaybackField = new TextField('bufferLivePlayback');
        $bufferLivePlaybackField->setRequired(true);
        $this->addElement($bufferLivePlaybackField->setLabel($language->text('vwvc', 'bufferLivePlayback')));

        // bufferFullPlayback Field
        $bufferFullPlaybackField = new TextField('bufferFullPlayback');
        $bufferFullPlaybackField->setRequired(true);
        $this->addElement($bufferFullPlaybackField->setLabel($language->text('vwvc', 'bufferFullPlayback')));

        // disableBandwidthDetection Field
        $disableBandwidthDetectionField = new Selectbox('disableBandwidthDetection');
        $disableBandwidthDetectionField->addOptions($par);
        $disableBandwidthDetectionField->setRequired();
        $disableBandwidthDetectionField->setHasInvitation(false);
        $this->addElement($disableBandwidthDetectionField->setLabel($language->text('vwvc', 'disableBandwidthDetection')));

        // disableUploadDetection Field
        $disableUploadDetectionField = new Selectbox('disableUploadDetection');
        $disableUploadDetectionField->addOptions($par);
        $disableUploadDetectionField->setRequired();
        $disableUploadDetectionField->setHasInvitation(false);
        $this->addElement($disableUploadDetectionField->setLabel($language->text('vwvc', 'disableUploadDetection')));

        // limitByBandwidth Field
        $limitByBandwidthField = new Selectbox('limitByBandwidth');
        $limitByBandwidthField->addOptions($par);
        $limitByBandwidthField->setRequired();
        $limitByBandwidthField->setHasInvitation(false);
        $this->addElement($limitByBandwidthField->setLabel($language->text('vwvc', 'limitByBandwidth')));

        // ws_ads Field
        $ws_adsField = new TextField('ws_ads');
        $ws_adsField->setRequired(true);
        $this->addElement($ws_adsField->setLabel($language->text('vwvc', 'ws_ads')));

        // adsTimeout Field
        $adsTimeoutField = new TextField('adsTimeout');
        $adsTimeoutField->setRequired(true);
        $this->addElement($adsTimeoutField->setLabel($language->text('vwvc', 'adsTimeout')));

        // adsInterval Field
        $adsIntervalField = new TextField('adsInterval');
        $adsIntervalField->setRequired(true);
        $this->addElement($adsIntervalField->setLabel($language->text('vwvc', 'adsInterval')));

        // statusInterval Field
        $statusIntervalField = new TextField('statusInterval');
        $statusIntervalField->setRequired(true);
        $this->addElement($statusIntervalField->setLabel($language->text('vwvc', 'statusInterval')));

        // availability Field
        $availabilityField = new TextField('availability');
        $availabilityField->setRequired(true);
        $this->addElement($availabilityField->setLabel($language->text('vwvc', 'availability')));

        // status Field
        $statusBox = array(
          'approved' => 'approved',
          'pending' => 'pending'
        );
        $statusField = new Selectbox('status');
        $statusField->addOptions($statusBox);
        $statusField->setRequired();
        $statusField->setHasInvitation(false);
        $this->addElement($statusField->setLabel($language->text('vwvc', 'status')));

        // who can create room Field
        $memberBox = array(
          'all' => 'all',
          'member' => 'member list'
        );
        $memberField = new Selectbox('member');
        $memberField->addOptions($memberBox);
        $memberField->setRequired();
        $memberField->setHasInvitation(false);
        $this->addElement($memberField->setLabel($language->text('vwvc', 'member')));

        // member_list Field
        $member_listField = new Textarea('member_list');
        $userService = BOL_UserService::getInstance();
        $user = $userService->findUserById(OW::getUser()->getId());
        $username = $user->getUsername();
        $member_listField->setValue($username);
        $this->addElement($member_listField->setLabel($language->text('vwvc', 'member_list')));

        // submit
        $submit = new Submit('save');
        $submit->setValue($language->text('vwvc', 'btn_edit'));
        $this->addElement($submit);
    }

    /**
     * Updates vwvc plugin configuration
     *
     * @return boolean
     */
    public function process()
    {
        $values = $this->getValues();

        $config = OW::getConfig();

        $config->saveConfig('vwvc', 'server', $values['server']);
        $config->saveConfig('vwvc', 'serverAMF', $values['serverAMF']);
        $config->saveConfig('vwvc', 'serverRTMFP', $values['serverRTMFP']);

        $config->saveConfig('vwvc', 'enableRTMP', $values['enableRTMP']);
        $config->saveConfig('vwvc', 'enableP2P', $values['enableP2P']);
        $config->saveConfig('vwvc', 'supportRTMP', $values['supportRTMP']);
        $config->saveConfig('vwvc', 'supportP2P', $values['supportP2P']);
        $config->saveConfig('vwvc', 'alwaysRTMP', $values['alwaysRTMP']);
        $config->saveConfig('vwvc', 'alwaysP2P', $values['alwaysP2P']);
        $config->saveConfig('vwvc', 'videoCodec', $values['videoCodec']);
        $config->saveConfig('vwvc', 'codecProfile', $values['codecProfile']);
        $config->saveConfig('vwvc', 'codecLevel', $values['codecLevel']);
        $config->saveConfig('vwvc', 'soundCodec', $values['soundCodec']);

        $config->saveConfig('vwvc', 'p2pGroup', $values['p2pGroup']);
        $config->saveConfig('vwvc', 'camMaxBandwidth', $values['camMaxBandwidth']);
        $config->saveConfig('vwvc', 'bufferLive', $values['bufferLive']);
        $config->saveConfig('vwvc', 'bufferFull', $values['bufferFull']);
        $config->saveConfig('vwvc', 'bufferLivePlayback', $values['bufferLivePlayback']);
        $config->saveConfig('vwvc', 'bufferFullPlayback', $values['bufferFullPlayback']);
        $config->saveConfig('vwvc', 'disableBandwidthDetection', $values['disableBandwidthDetection']);
        $config->saveConfig('vwvc', 'disableUploadDetection', $values['disableUploadDetection']);
        $config->saveConfig('vwvc', 'limitByBandwidth', $values['limitByBandwidth']);
        $config->saveConfig('vwvc', 'ws_ads', $values['ws_ads']);
        $config->saveConfig('vwvc', 'adsTimeout', $values['adsTimeout']);
        $config->saveConfig('vwvc', 'adsInterval', $values['adsInterval']);
        $config->saveConfig('vwvc', 'statusInterval', $values['statusInterval']);
        $config->saveConfig('vwvc', 'availability', $values['availability']);
        $config->saveConfig('vwvc', 'status', $values['status']);
        $config->saveConfig('vwvc', 'member', $values['member']);
        $config->saveConfig('vwvc', 'member_list', $values['member_list']);
        $config->saveConfig('vwvc', 'baseSwf_url', $values['baseSwf_url']);

        return array('result' => true);
    }
}
