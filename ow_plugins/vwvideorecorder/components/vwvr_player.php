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
 * @author Egor Bulgakov <egor.bulgakov@gmail.com>
 * @package ow.plugin.vwvr.components
 * @since 1.0
 */
class VWVR_CMP_VwvrPlayer extends OW_Component
{
    /**
     * @var VWVR_BOL_ClipService 
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

        $this->clipService = VWVR_BOL_ClipService::getInstance();

        $clip = $this->clipService->findClipById($clipId);
        $config = OW::getConfig();

        $room_name = $clip->title;

        $params = $clip->recordingId.'^';
        $params .= $clip->description.'^';
        $settings .= $config->getValue('vwvr', 'recordLimit').'^';
        $settings .= $config->getValue('vwvr', 'camWidth').'^';
        $settings .= $config->getValue('vwvr', 'camHeight').'^';
        $settings .= $config->getValue('vwvr', 'camFPS').'^';
        $settings .= $config->getValue('vwvr', 'micRate').'^';
        $settings .= $config->getValue('vwvr', 'camBandwidth').'^';
        $settings .= $config->getValue('vwvr', 'layoutCode').'^';
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

        // room owner
        $idOwner = $clip->userId;
        $usernameOwner = BOL_UserService::getInstance()->getUsername($idOwner);

        $urlRoom = OW::getRouter()->urlForRoute('vwview_list_vr', array('listType' => 'latest'));

        $userId = OW::getUser()->getId();
        
        // visitor
        $userService = BOL_UserService::getInstance();
        $user = $userService->findUserById($userId);
        if (isset ($user)) {
          $username = $user->getUsername();
        } else {
    	    $username="VW".base_convert((time()-1224350000).rand(0,10),10,36);          
        }

        $baseSwfUrl = $config->getValue('vwvr', 'baseSwf_url');

        $srcSwfUrl= $baseSwfUrl."streamplayer.swf?streamName=".urlencode($room_name.$clip->recordingId)."&serverRTMP=".urlencode($config->getValue('vwvr', 'server'))."&templateURL=";

        $code = '<embed width="100%" height="450px" scale="noscale" salign="lt" src="'.$srcSwfUrl.'" bgcolor="#777777" base="'.$baseSwfUrl.'" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" wmode="transparent"></embed>';

        $this->assign('clipCode', $code);
    }
}
