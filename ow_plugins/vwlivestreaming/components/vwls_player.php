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
 * Vwls player component
 *
 * @author Egor Bulgakov <egor.bulgakov@gmail.com>
 * @package ow.plugin.vwls.components
 * @since 1.0
 */
class VWLS_CMP_VwlsPlayer extends OW_Component
{
    /**
     * @var VWLS_BOL_ClipService 
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

        $this->clipService = VWLS_BOL_ClipService::getInstance();

        $clip = $this->clipService->findClipById($clipId);
        $room_name = $clip->title;

        $params = $clip->description.'^';
        $params .= $clip->roomLimit.'^';
        $params .= $clip->welcome.'^';
        $params .= $clip->welcome2.'^';
        $params .= $clip->offlineMessage.'^';
        $params .= $clip->camWidth.'^';
        $params .= $clip->camHeight.'^';
        $params .= $clip->camFPS.'^';
        $params .= $clip->micRate.'^';
        $params .= $clip->camBandwidth.'^';
        $params .= $clip->labelColor.'^';
        $params .= $clip->layoutCode.'^';
        $params .= $clip->filterRegex.'^';
        $params .= $clip->filterReplace.'^';
        $params .= $clip->floodProtection.'^';
        $params .= $clip->floodProtection2.'^';
        $params .= $clip->permission.'^';
        $params .= $clip->user_list.'^';
        $params .= $clip->moderator_list.'^';
        $params .= $clip->layoutCode2.'^';

        $config = OW::getConfig();
        $settings = $config->getValue('vwls', 'server').'^';
        $settings .= $config->getValue('vwls', 'serverAMF').'^';
        $settings .= $config->getValue('vwls', 'serverRTMFP').'^';
        $settings .= $config->getValue('vwls', 'p2pGroup').'^';
        $settings .= $config->getValue('vwls', 'tokenKey').'^';
        $settings .= $config->getValue('vwls', 'snapshotsTime').'^';
        $settings .= $config->getValue('vwls', 'camMaxBandwidth').'^';
        $settings .= $config->getValue('vwls', 'bufferLive').'^';
        $settings .= $config->getValue('vwls', 'bufferFull').'^';
        $settings .= $config->getValue('vwls', 'bufferLive2').'^';
        $settings .= $config->getValue('vwls', 'bufferFull2').'^';
        $settings .= $config->getValue('vwls', 'disableBandwidthDetection').'^';
        $settings .= $config->getValue('vwls', 'limitByBandwidth').'^';
        $settings .= $config->getValue('vwls', 'generateSnapshots').'^';
        $settings .= $config->getValue('vwls', 'externalInterval').'^';
        $settings .= $config->getValue('vwls', 'externalInterval2').'^';
        $settings .= $config->getValue('vwls', 'ws_ads').'^';
        $settings .= $config->getValue('vwls', 'adsTimeout').'^';
        $settings .= $config->getValue('vwls', 'adsInterval').'^';
        $settings .= $config->getValue('vwls', 'statusInterval').'^';

        // new codec setting, etc
        $cSettings = $config->getValue('vwls', 'videoCodec').'^';
        $cSettings .= $config->getValue('vwls', 'codecProfile').'^';
        $cSettings .= $config->getValue('vwls', 'codecLevel').'^';
        $cSettings .= $config->getValue('vwls', 'soundCodec').'^';
        $cSettings .= $config->getValue('vwls', 'enableRTMP').'^';
        $cSettings .= $config->getValue('vwls', 'enableP2P').'^';
        $cSettings .= $config->getValue('vwls', 'supportRTMP').'^';
        $cSettings .= $config->getValue('vwls', 'supportP2P').'^';
        $cSettings .= $config->getValue('vwls', 'alwaysRTMP').'^';
        $cSettings .= $config->getValue('vwls', 'alwaysP2P').'^';
        $cSettings .= $clip->soundQuality.'^';

        // room owner
        $idOwner = $clip->userId;
        $usernameOwner = BOL_UserService::getInstance()->getUsername($idOwner);

        $userId = OW::getUser()->getId();
        // linkcode, embedcode, embedvcode
        $urlRoom = OW::getRouter()->urlForRoute('vwview_clip_ls_w', array('id' => $clipId));
        
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

        $baseSwfUrl = $config->getValue('vwls', 'baseSwf_url');

        $room = $room_name.'^'.$params.$settings.$username.'^'.$usernameOwner.'^'.$userId.'^'.$urlRoom.'^'.$baseSwfUrl.'^'.$userLink.'^'.$userPicture.'^'.$cSettings;

        // create sessions/$sessionid file
        $sessionsPath = 'ow_plugins/vwlivestreaming/ls/sessions';
		@chmod ($sessionsPath, 0777);
        $sessionName = "vls".base_convert((time()-1112340000).rand(0,10),10,36);
        $filename = $sessionsPath.'/'.$sessionName.'.vwf';
        $handle = fopen($filename,"x+");
        $roomContent = $room;
        fwrite($handle,$roomContent);
        fclose($handle);
        // send file name to _login via cookie
        setcookie("sessionNameLs",$sessionName,time()+180,'/');
        
        // create sessions/$parameters file
        $sessionName = "params".$room_name.".vwf";
        $filenamex = $sessionsPath.'/'.$sessionName;
        $handle = fopen($filenamex,"w");
        $roomContent = $room_name.'^'.$params.$settings.$cSettings;
        fwrite($handle,$roomContent);
        fclose($handle);

        if ($idOwner == $userId) $srcSwfUrl = $baseSwfUrl."live_broadcast.swf?room=".$room_name;
        else $srcSwfUrl = $baseSwfUrl."live_watch.swf?n=".$room_name;

        // logout
        $urlLogout = OW::getRouter()->urlForRoute('vwview_list_ls', array('listType' => 'latest'));
        setcookie("urlLogoutLs",$urlLogout,time()+86400,'/');

        $code = '<embed width="100%" height="450px" scale="noscale" salign="lt" src="'.$srcSwfUrl.'" bgcolor="#777777" base="'.$baseSwfUrl.'" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" wmode="transparent"></embed>';

        $this->assign('clipCode', $code);
    }
}
