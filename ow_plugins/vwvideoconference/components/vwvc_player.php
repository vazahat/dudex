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
 * Vwvc player component
 *
 * @author Egor Bulgakov <egor.bulgakov@gmail.com>
 * @package ow.plugin.vwvc.components
 * @since 1.0
 */
class VWVC_CMP_VwvcPlayer extends OW_Component
{
    /**
     * @var VWVC_BOL_ClipService 
     */
    private $clipService;

    /**
     * Class constructor
     *
     * @param int $clipId
     */
    public function __construct( array $params )
    {
        parent::__construct();

        $clipId = $params['id'];

        $this->clipService = VWVC_BOL_ClipService::getInstance();

        $clip = $this->clipService->findClipById($clipId);
        $room_name = $clip->title;

        $params = $clip->description.'^';
        $params .= $clip->welcome.'^';
        $params .= $clip->camWidth.'^';
        $params .= $clip->camHeight.'^';
        $params .= $clip->camFPS.'^';
        $params .= $clip->micRate.'^';
        $params .= $clip->camBandwidth.'^';
        $params .= $clip->background_url.'^';
        $params .= $clip->layoutCode.'^';
        $params .= $clip->filterRegex.'^';
        $params .= $clip->filterReplace.'^';
        $params .= $clip->floodProtection.'^';
        $params .= $clip->permission.'^';
        $params .= $clip->user_list.'^';
        $params .= $clip->moderator_list.'^';

        $config = OW::getConfig();
        $settings = $config->getValue('vwvc', 'p2pGroup').'^';
        $settings .= $config->getValue('vwvc', 'camMaxBandwidth').'^';
        $settings .= $config->getValue('vwvc', 'bufferLive').'^';
        $settings .= $config->getValue('vwvc', 'bufferFull').'^';
        $settings .= $config->getValue('vwvc', 'bufferLivePlayback').'^';
        $settings .= $config->getValue('vwvc', 'bufferFullPlayback').'^';
        $settings .= $config->getValue('vwvc', 'disableBandwidthDetection').'^';
        $settings .= $config->getValue('vwvc', 'disableUploadDetection').'^';
        $settings .= $config->getValue('vwvc', 'limitByBandwidth').'^';
        $settings .= $config->getValue('vwvc', 'ws_ads').'^';
        $settings .= $config->getValue('vwvc', 'adsTimeout').'^';
        $settings .= $config->getValue('vwvc', 'adsInterval').'^';
        $settings .= $config->getValue('vwvc', 'statusInterval').'^';
        $settings .= $config->getValue('vwvc', 'videos_per_page').'^';
        $settings .= $config->getValue('vwvc', 'server').'^';
        $settings .= $config->getValue('vwvc', 'serverAMF').'^';
        $settings .= $config->getValue('vwvc', 'serverRTMFP').'^';

        // new codec setting, etc
        $cSettings = $config->getValue('vwvc', 'videoCodec').'^';
        $cSettings .= $config->getValue('vwvc', 'codecProfile').'^';
        $cSettings .= $config->getValue('vwvc', 'codecLevel').'^';
        $cSettings .= $config->getValue('vwvc', 'soundCodec').'^';
        $cSettings .= $config->getValue('vwvc', 'enableRTMP').'^';
        $cSettings .= $config->getValue('vwvc', 'enableP2P').'^';
        $cSettings .= $config->getValue('vwvc', 'supportRTMP').'^';
        $cSettings .= $config->getValue('vwvc', 'supportP2P').'^';
        $cSettings .= $config->getValue('vwvc', 'alwaysRTMP').'^';
        $cSettings .= $config->getValue('vwvc', 'alwaysP2P').'^';
        $cSettings .= $clip->soundQuality.'^';

        // room owner
        $idOwner = $clip->userId;
        $usernameOwner = BOL_UserService::getInstance()->getUsername($idOwner);

        $userId = OW::getUser()->getId();
        
        // visitor
        $userService = BOL_UserService::getInstance();
        $user = $userService->findUserById($userId);
        if (isset ($user)) {
          $username = $user->getUsername();
        } else {
    	    $username="VW".base_convert((time()-1224350000).rand(0,10),10,36);
        }

        // userLink and userPicture
        $userLink = $userService->getUserUrl($userId);
        $userPicture = BOL_AvatarService::getInstance()->getAvatarUrl($userId);

        $baseSwfUrl = $config->getValue('vwvc', 'baseSwf_url');

        $room = $room_name.'^'.$params.$settings.$username.'^'.$usernameOwner.'^'.$userId.'^'.$userLink.'^'.$userPicture.'^'.$cSettings;
        
        // create sessions/$sessionid file
        $sessionsPath = 'ow_plugins/vwvideoconference/vc/sessions';
		@chmod ($sessionsPath, 0777);
        $sessionName = "vcs".base_convert((time()-1112340000).rand(0,10),10,36);
        $filename = $sessionsPath.'/'.$sessionName.'.vwf';
        $handle = fopen($filename,"x+");
        $roomContent = $room;
        fwrite($handle,$roomContent);
        fclose($handle);
        // send file name to _login
        setcookie("sessionNameVc",$sessionName,time()+180,'/');

        $urlRoom = OW::getRouter()->urlForRoute('vwview_list', array('listType' => 'latest'));
        setcookie("urlRoomVc",$urlRoom,time()+86400,'/');

        $srcSwfUrl = $baseSwfUrl."videowhisper_conference.swf?room=".$room_name;

        $code = '<embed width="100%" height="600px" scale="noscale" salign="lt" src="'.$srcSwfUrl.'" bgcolor="#777777" base="'.$baseSwfUrl.'" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" wmode="transparent"></embed>';

        $this->assign('clipCode', $code);
    }
}
