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
 * Vwvr player component
 *
 */
class VWVR_CMP_VwvrRecorder extends OW_Component
{
 
    /**
     * Class constructor
     *
     */
    public function __construct()
    {
        parent::__construct();

        $config = OW::getConfig();

        $generated =  base_convert((time()-1224000000).rand(0,10),10,36);
        $room_name = $generated;

        $params = "-".base_convert(time(),10,36).'^'; // recording_id
        $params .= "".'^'; // description
        $settings = $config->getValue('vwvr', 'recordLimit').'^';
        $settings .= $config->getValue('vwvr', 'camWidth').'^';
        $settings .= $config->getValue('vwvr', 'camHeight').'^';
        $settings .= $config->getValue('vwvr', 'camFPS').'^';
        $settings .= $config->getValue('vwvr', 'micRate').'^';
        $settings .= $config->getValue('vwvr', 'camBandwidth').'^';
        // $settings .= $config->getValue('vwvr', 'layoutCode').'^';
        $settings .= '^';
        $settings .= $config->getValue('vwvr', 'showCamSettings').'^';
        $settings .= $config->getValue('vwvr', 'advancedCamSettings').'^';
        $settings .= $config->getValue('vwvr', 'fillWindow').'^';
        $settings .= $config->getValue('vwvr', 'server').'^';
        $settings .= $config->getValue('vwvr', 'serverAMF').'^';
        $settings .= $config->getValue('vwvr', 'camMaxBandwidth').'^';
        $settings .= $config->getValue('vwvr', 'bufferLive').'^';
        $settings .= $config->getValue('vwvr', 'bufferFull').'^';
        $settings .= $config->getValue('vwvr', 'bufferLivePlayback').'^';
        $settings .= $config->getValue('vwvr', 'bufferFullPlayback').'^';

        // new codec setting, etc
        $cSettings = $config->getValue('vwvr', 'videoCodec').'^';
        $cSettings .= $config->getValue('vwvr', 'codecProfile').'^';
        $cSettings .= $config->getValue('vwvr', 'codecLevel').'^';
        $cSettings .= $config->getValue('vwvr', 'soundCodec').'^';
        $cSettings .= $config->getValue('vwvr', 'soundQuality').'^';
        
        // layout code
        $layoutCode = $config->getValue('vwvr', 'layoutCode');

        // get record path
        $recordPath = $config->getValue('vwvr', 'recordPath');

        // room owner
        $idOwner = $clip->userId;
        $usernameOwner = BOL_UserService::getInstance()->getUsername($idOwner);

        $urlRoom = OW::getRouter()->urlFor('VWVR_CTRL_Add', 'index');
        $urlRoomx = OW::getRouter()->urlForRoute('vwview_list_vr', array('listType' => 'latest'));
        setcookie("urlRoomVr",$urlRoom."^".$urlRoomx,time()+86400,'/');

        $userId = OW::getUser()->getId();
        
        // visitor
        $userService = BOL_UserService::getInstance();
        $user = $userService->findUserById($userId);
        if (isset ($user)) {
          $username = $user->getUsername();
        } else {
    	    $username="VW".$generated;
        }

        $baseSwfUrl = $config->getValue('vwvr', 'baseSwf_url');

        $room = $room_name.'^'.$params.$settings;
        $room2 = $username.'^'.$usernameOwner.'^'.$userId.'^'.$urlRoom.'^'.$recordPath.'^'.$baseSwfUrl.'^'.$urlRoomx.'^'.$cSettings;

        // create sessions/$sessionid file
        $sessionsPath = 'ow_plugins/vwvideorecorder/vr/sessions';
		@chmod ($sessionsPath, 0777);
        $sessionName = "vrs".base_convert((time()-1112340000).rand(0,10),10,36);
        $filename = $sessionsPath.'/'.$sessionName.'.vwf';
        $handle = fopen($filename,"x+");
        $roomContent = $room.$room2;
        fwrite($handle,$roomContent);
        // send file name to _login
        setcookie("sessionNameVr",$sessionName,time()+180,'/');
        fclose($handle);

        $srcSwfUrl = $baseSwfUrl."videorecorder.swf";

        // replace not allowed characters
        $layoutCodex = str_replace (";", "^", $layoutCode);
        $layoutCodexx = str_replace ("=", "|", $layoutCodex);
        setcookie("layoutCode",$layoutCodexx,time()+180,'/');

        $code = '<embed width="100%" height="500px" align="middle" scale="noscale" salign="lt" src="'.$srcSwfUrl.'" bgcolor="#777777" base="'.$baseSwfUrl.'" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" wmode="transparent"></embed>';
        $this->assign('clipCode', $code);
    }
}
