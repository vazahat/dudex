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
 * Vwls admin action controller
 *
 * @author Egor Bulgakov <egor.bulgakov@gmail.com>
 * @package ow.plugin.vwls.controllers
 * @since 1.0
 */
class VWLS_CTRL_Admin extends ADMIN_CTRL_Abstract
{

    /**
     * Default action
     */
    public function index()
    {
        $language = OW::getLanguage();

        $item = new BASE_MenuItem();
        $item->setLabel($language->text('vwls', 'admin_menu_general'));
        $item->setUrl(OW::getRouter()->urlForRoute('vwls_admin_config'));
        $item->setKey('general');
        $item->setIconClass('ow_ic_gear_wheel');

        $menu = new BASE_CMP_ContentMenu(array($item));
        $this->addComponent('menu', $menu);

        $configs = OW::getConfig()->getValues('vwls');

        $configSaveForm = new ConfigSaveForm();
        $this->addForm($configSaveForm);

        if ( OW::getRequest()->isPost() && $configSaveForm->isValid($_POST) )
        {
            $res = $configSaveForm->process();
            OW::getFeedback()->info($language->text('vwls', 'settings_updated'));
            $this->redirect(OW::getRouter()->urlForRoute('vwls_admin_config'));
        }

        if ( !OW::getRequest()->isAjax() )
        {
            $this->setPageHeading(OW::getLanguage()->text('vwls', 'admin_config'));
            $this->setPageHeadingIconClass('ow_ic_vwls');

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
        $configSaveForm->getElement('tokenKey')->setValue($configs['tokenKey']);
        $configSaveForm->getElement('snapshotsTime')->setValue($configs['snapshotsTime']);
        $configSaveForm->getElement('camMaxBandwidth')->setValue($configs['camMaxBandwidth']);
        $configSaveForm->getElement('bufferLive')->setValue($configs['bufferLive']);
        $configSaveForm->getElement('bufferFull')->setValue($configs['bufferFull']);
        $configSaveForm->getElement('bufferLive2')->setValue($configs['bufferLive2']);
        $configSaveForm->getElement('bufferFull2')->setValue($configs['bufferFull2']);
        $configSaveForm->getElement('disableBandwidthDetection')->setValue($configs['disableBandwidthDetection']);
        $configSaveForm->getElement('limitByBandwidth')->setValue($configs['limitByBandwidth']);
        $configSaveForm->getElement('generateSnapshots')->setValue($configs['generateSnapshots']);
        $configSaveForm->getElement('externalInterval')->setValue($configs['externalInterval']);
        $configSaveForm->getElement('externalInterval2')->setValue($configs['externalInterval2']);
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
        $this->addElement($baseSwf_urlField->setLabel($language->text('vwls', 'baseSwf_url')));
        
        // server Field
        $serverField = new TextField('server');
        $serverField->setRequired(true);
        $this->addElement($serverField->setLabel($language->text('vwls', 'server')));

        // serverAMF Field
        $serverAMFField = new TextField('serverAMF');
        $serverAMFField->setRequired(true);
        $this->addElement($serverAMFField->setLabel($language->text('vwls', 'serverAMF')));

        // serverRTMFP Field
        $serverRTMFPField = new TextField('serverRTMFP');
        $serverRTMFPField->setRequired(true);
        $this->addElement($serverRTMFPField->setLabel($language->text('vwls', 'serverRTMFP')));

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
        $this->addElement($enableRTMPField->setLabel($language->text('vwls', 'enableRTMP')));

        // enableP2P Field
        $enableP2PField = new Selectbox('enableP2P');
        $enableP2PField->addOptions($par);
        $enableP2PField->setRequired();
        $enableP2PField->setHasInvitation(false);
        $this->addElement($enableP2PField->setLabel($language->text('vwls', 'enableP2P')));

        // supportRTMP Field
        $supportRTMPField = new Selectbox('supportRTMP');
        $supportRTMPField->addOptions($par1);
        $supportRTMPField->setRequired();
        $supportRTMPField->setHasInvitation(false);
        $this->addElement($supportRTMPField->setLabel($language->text('vwls', 'supportRTMP')));

        // supportP2P Field
        $supportP2PField = new Selectbox('supportP2P');
        $supportP2PField->addOptions($par1);
        $supportP2PField->setRequired();
        $supportP2PField->setHasInvitation(false);
        $this->addElement($supportP2PField->setLabel($language->text('vwls', 'supportP2P')));

        // alwaysRTMP Field
        $alwaysRTMPField = new Selectbox('alwaysRTMP');
        $alwaysRTMPField->addOptions($par);
        $alwaysRTMPField->setRequired();
        $alwaysRTMPField->setHasInvitation(false);
        $this->addElement($alwaysRTMPField->setLabel($language->text('vwls', 'alwaysRTMP')));

        // alwaysP2P Field
        $alwaysP2PField = new Selectbox('alwaysP2P');
        $alwaysP2PField->addOptions($par);
        $alwaysP2PField->setRequired();
        $alwaysP2PField->setHasInvitation(false);
        $this->addElement($alwaysP2PField->setLabel($language->text('vwls', 'alwaysP2P')));

        // videoCodec Field
        $videoCodecField = new TextField('videoCodec');
        $videoCodecField->setRequired(true);
        $this->addElement($videoCodecField->setLabel($language->text('vwls', 'videoCodec')));

        // codecLevel Field
        $codecLevelField = new TextField('codecLevel');
        $codecLevelField->setRequired(true);
        $this->addElement($codecLevelField->setLabel($language->text('vwls', 'codecLevel')));

        // codecProfile Field
        $codecProfileArr = array(
          'main' => 'main',
          'baseline' => 'baseline'
        );
        $codecProfileField = new Selectbox('codecProfile');
        $codecProfileField->addOptions($codecProfileArr);
        $codecProfileField->setRequired();
        $codecProfileField->setHasInvitation(false);
        $this->addElement($codecProfileField->setLabel($language->text('vwls', 'codecProfile')));

        // soundCodec Field
        $soundCodecArr = array(
          'Speex' => 'Speex',
          'Nellymoser' => 'Nellymoser'
        );
        $soundCodecField = new Selectbox('soundCodec');
        $soundCodecField->addOptions($soundCodecArr);
        $soundCodecField->setRequired();
        $soundCodecField->setHasInvitation(false);
        $this->addElement($soundCodecField->setLabel($language->text('vwls', 'soundCodec')));

        // p2pGroup Field
        $p2pGroupField = new TextField('p2pGroup');
        $p2pGroupField->setRequired(true);
        $this->addElement($p2pGroupField->setLabel($language->text('vwls', 'p2pGroup')));

        // Token Key Field
        $tokenKeyField = new TextField('tokenKey');
        $tokenKeyField->setRequired(true);
        $this->addElement($tokenKeyField->setLabel($language->text('vwls', 'tokenKey')));

        // snapshotsTime Field
        $snapshotsTimeField = new TextField('snapshotsTime');
        $tokenKeyField->setRequired(true);
        $this->addElement($snapshotsTimeField->setLabel($language->text('vwls', 'snapshotsTime')));

        // camMaxBandwidth Field
        $camMaxBandwidthField = new TextField('camMaxBandwidth');
        $camMaxBandwidthField->setRequired(true);
        $this->addElement($camMaxBandwidthField->setLabel($language->text('vwls', 'camMaxBandwidth')));

        // bufferLive Field
        $bufferLiveField = new TextField('bufferLive');
        $bufferLiveField->setRequired(true);
        $this->addElement($bufferLiveField->setLabel($language->text('vwls', 'bufferLive')));

        // bufferFull Field
        $bufferFullField = new TextField('bufferFull');
        $bufferFullField->setRequired(true);
        $this->addElement($bufferFullField->setLabel($language->text('vwls', 'bufferFull')));

        // bufferLive2 Field
        $bufferLive2Field = new TextField('bufferLive2');
        $bufferLive2Field->setRequired(true);
        $this->addElement($bufferLive2Field->setLabel($language->text('vwls', 'bufferLive2')));

        // bufferFull2 Field
        $bufferFull2Field = new TextField('bufferFull2');
        $bufferFull2Field->setRequired(true);
        $this->addElement($bufferFull2Field->setLabel($language->text('vwls', 'bufferFull2')));

        // disableBandwidthDetection Field
        $disableBandwidthDetectionField = new Selectbox('disableBandwidthDetection');
        $disableBandwidthDetectionField->addOptions($par);
        $disableBandwidthDetectionField->setRequired();
        $disableBandwidthDetectionField->setHasInvitation(false);
        $this->addElement($disableBandwidthDetectionField->setLabel($language->text('vwls', 'disableBandwidthDetection')));

        // limitByBandwidth Field
        $limitByBandwidthField = new Selectbox('limitByBandwidth');
        $limitByBandwidthField->addOptions($par);
        $limitByBandwidthField->setRequired();
        $limitByBandwidthField->setHasInvitation(false);
        $this->addElement($limitByBandwidthField->setLabel($language->text('vwls', 'limitByBandwidth')));

        // generateSnapshots Field
        $generateSnapshotsField = new Selectbox('generateSnapshots');
        $generateSnapshotsField->addOptions($par);
        $generateSnapshotsField->setRequired();
        $generateSnapshotsField->setHasInvitation(false);
        $this->addElement($generateSnapshotsField->setLabel($language->text('vwls', 'generateSnapshots')));

        // externalInterval Field
        $externalIntervalField = new TextField('externalInterval');
        $externalIntervalField->setRequired(true);
        $this->addElement($externalIntervalField->setLabel($language->text('vwls', 'externalInterval')));

        // externalInterval2 Field
        $externalInterval2Field = new TextField('externalInterval2');
        $externalInterval2Field->setRequired(true);
        $this->addElement($externalInterval2Field->setLabel($language->text('vwls', 'externalInterval2')));

        // ws_ads Field
        $ws_adsField = new TextField('ws_ads');
        $ws_adsField->setRequired(true);
        $this->addElement($ws_adsField->setLabel($language->text('vwls', 'ws_ads')));

        // adsTimeout Field
        $adsTimeoutField = new TextField('adsTimeout');
        $adsTimeoutField->setRequired(true);
        $this->addElement($adsTimeoutField->setLabel($language->text('vwls', 'adsTimeout')));

        // adsInterval Field
        $adsIntervalField = new TextField('adsInterval');
        $adsIntervalField->setRequired(true);
        $this->addElement($adsIntervalField->setLabel($language->text('vwls', 'adsInterval')));

        // statusInterval Field
        $statusIntervalField = new TextField('statusInterval');
        $statusIntervalField->setRequired(true);
        $this->addElement($statusIntervalField->setLabel($language->text('vwls', 'statusInterval')));

        // availability Field
        $availabilityField = new TextField('availability');
        $availabilityField->setRequired(true);
        $this->addElement($availabilityField->setLabel($language->text('vwls', 'availability')));

        // status Field
        $statusBox = array(
          'approved' => 'approved',
          'pending' => 'pending'
        );
        $statusField = new Selectbox('status');
        $statusField->addOptions($statusBox);
        $statusField->setRequired();
        $statusField->setHasInvitation(false);
        $this->addElement($statusField->setLabel($language->text('vwls', 'status')));

        // who can create room Field
        $memberBox = array(
          'all' => 'all',
          'member' => 'member list'
        );
        $memberField = new Selectbox('member');
        $memberField->addOptions($memberBox);
        $memberField->setRequired();
        $memberField->setHasInvitation(false);
        $this->addElement($memberField->setLabel($language->text('vwls', 'member')));

        // member_list Field
        $member_listField = new Textarea('member_list');
        $userService = BOL_UserService::getInstance();
        $user = $userService->findUserById(OW::getUser()->getId());
        $username = $user->getUsername();
        $member_listField->setValue($username);
        $this->addElement($member_listField->setLabel($language->text('vwls', 'member_list')));

        // submit
        $submit = new Submit('save');
        $submit->setValue($language->text('vwls', 'btn_edit'));
        $this->addElement($submit);
    }

    /**
     * Updates vwls plugin configuration
     *
     * @return boolean
     */
    public function process()
    {
        $values = $this->getValues();

        $config = OW::getConfig();

        $config->saveConfig('vwls', 'server', $values['server']);
        $config->saveConfig('vwls', 'serverAMF', $values['serverAMF']);
        $config->saveConfig('vwls', 'serverRTMFP', $values['serverRTMFP']);

        $config->saveConfig('vwls', 'enableRTMP', $values['enableRTMP']);
        $config->saveConfig('vwls', 'enableP2P', $values['enableP2P']);
        $config->saveConfig('vwls', 'supportRTMP', $values['supportRTMP']);
        $config->saveConfig('vwls', 'supportP2P', $values['supportP2P']);
        $config->saveConfig('vwls', 'alwaysRTMP', $values['alwaysRTMP']);
        $config->saveConfig('vwls', 'alwaysP2P', $values['alwaysP2P']);
        $config->saveConfig('vwls', 'videoCodec', $values['videoCodec']);
        $config->saveConfig('vwls', 'codecProfile', $values['codecProfile']);
        $config->saveConfig('vwls', 'codecLevel', $values['codecLevel']);
        $config->saveConfig('vwls', 'soundCodec', $values['soundCodec']);

        $config->saveConfig('vwls', 'p2pGroup', $values['p2pGroup']);
        $config->saveConfig('vwls', 'tokenKey', $values['tokenKey']);
        $config->saveConfig('vwls', 'snapshotsTime', $values['snapshotsTime']);
        $config->saveConfig('vwls', 'camMaxBandwidth', $values['camMaxBandwidth']);
        $config->saveConfig('vwls', 'bufferLive', $values['bufferLive']);
        $config->saveConfig('vwls', 'bufferFull', $values['bufferFull']);
        $config->saveConfig('vwls', 'bufferLive2', $values['bufferLive2']);
        $config->saveConfig('vwls', 'bufferFull2', $values['bufferFull2']);
        $config->saveConfig('vwls', 'disableBandwidthDetection', $values['disableBandwidthDetection']);
        $config->saveConfig('vwls', 'limitByBandwidth', $values['limitByBandwidth']);
        $config->saveConfig('vwls', 'generateSnapshots', $values['generateSnapshots']);
        $config->saveConfig('vwls', 'externalInterval', $values['externalInterval']);
        $config->saveConfig('vwls', 'externalInterval2', $values['externalInterval2']);
        $config->saveConfig('vwls', 'ws_ads', $values['ws_ads']);
        $config->saveConfig('vwls', 'adsTimeout', $values['adsTimeout']);
        $config->saveConfig('vwls', 'adsInterval', $values['adsInterval']);
        $config->saveConfig('vwls', 'statusInterval', $values['statusInterval']);
        $config->saveConfig('vwls', 'availability', $values['availability']);
        $config->saveConfig('vwls', 'status', $values['status']);
        $config->saveConfig('vwls', 'member', $values['member']);
        $config->saveConfig('vwls', 'member_list', $values['member_list']);
        $config->saveConfig('vwls', 'baseSwf_url', $values['baseSwf_url']);

        return array('result' => true);
    }
}
