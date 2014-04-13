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
 * Vwvr add action controller
 *
 * @author Egor Bulgakov <egor.bulgakov@gmail.com>
 * @package ow.plugin.vwvr.controllers
 * @since 1.0
 */
class VWVR_CTRL_Add extends OW_ActionController
{

    /**
     * Default action
     */
    public function index()
    {

        // whether the video was recorded?
        $is_video_recorded = false;
        if ($_COOKIE["video_recorded"]) {
          $video_recorded= $_COOKIE["video_recorded"];
          $is_video_recorded = true;
        }

        $language = OW::getLanguage();
        $clipService = VWVR_BOL_ClipService::getInstance();
        $userId = OW::getUser()->getId();

        if ( !OW::getUser()->isAuthorized('vwvr', 'add') )
        {
            $this->setTemplate(OW::getPluginManager()->getPlugin('base')->getCtrlViewDir() . 'authorization_failed.html');
            return;
        }

        // member list
        $config = OW::getConfig();
        $member = $config->getValue('vwvr', 'member');
        $memberList = $config->getValue('vwvr', 'member_list');


        $eventParams = array('pluginKey' => 'vwvr', 'action' => 'add_vwvr');
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
      
                $vwvrAddForm = new vwvrAddForm();
                $this->addForm($vwvrAddForm);

                $userId = OW::getUser()->getId();
                
                if ($is_video_recorded) {
      
                  if ( OW::getRequest()->isPost() && $vwvrAddForm->isValid($_POST) )
                  {
                      $values = $vwvrAddForm->getValues();
                      $code = $clipService->validateClipCode($values['code']);
                      
                      if ( !BOL_TextFormatService::getInstance()->isCodeResourceValid($code) )
                      {
                          OW::getFeedback()->warning($language->text('vwvr', 'resource_not_allowed'));
                          $this->redirect();
                      }
                      
                      $res = $vwvrAddForm->process();
                      OW::getFeedback()->info($language->text('vwvr', 'clip_added'));

                      // delete cookie 
                      setcookie("video_recorded", "" ,time()-3600,'/');

                      $this->redirect(OW::getRouter()->urlForRoute('vwview_list_vr', array('listType' => 'latest')));
                  } // end post
                } else $this->redirect(OW::getRouter()->urlForRoute('vwrecord_clip_vr'));
      				}
      			 }
      			 if ($found === 0) {
               $this->assign('auth_msg', $language->text('vwvr', 'adding_denied'));
      			 }
      		  }
        } else {
                $this->assign('auth_msg', null);
    
                $vwvrAddForm = new vwvrAddForm();
                $this->addForm($vwvrAddForm);
    
                $userId = OW::getUser()->getId();
                
                if ($is_video_recorded) {
      
                  if ( OW::getRequest()->isPost() && $vwvrAddForm->isValid($_POST) )
                  {
                      $values = $vwvrAddForm->getValues();
                      $code = $clipService->validateClipCode($values['code']);
                      
                      if ( !BOL_TextFormatService::getInstance()->isCodeResourceValid($code) )
                      {
                          OW::getFeedback()->warning($language->text('vwvr', 'resource_not_allowed'));
                          $this->redirect();
                      }
                      
                      $res = $vwvrAddForm->process();
                      OW::getFeedback()->info($language->text('vwvr', 'clip_added'));

                      // delete cookie 
                      setcookie("video_recorded", "" ,time()-3600,'/');

                      $this->redirect(OW::getRouter()->urlForRoute('vwview_list_vr', array('listType' => 'latest')));
                  } // end post
                } else $this->redirect(OW::getRouter()->urlForRoute('vwrecord_clip_vr'));
        }

        if ( !OW::getRequest()->isAjax() )
        {
            OW::getNavigation()->activateMenuItem(OW_Navigation::MAIN, 'vwvr', 'vwvr');
        }

        OW::getDocument()->setHeading($language->text('vwvr', 'page_title_add_vwvr'));
        OW::getDocument()->setHeadingIconClass('ow_ic_vwvr');
        OW::getDocument()->setTitle($language->text('vwvr', 'meta_title_vwvr_add'));
    }
}

/**
 * Vwvr add form class
 */
class vwvrAddForm extends Form
{

    /**
     * Class constructor
     *
     */
    public function __construct()
    {
        parent::__construct('vwvrAddForm');

        $language = OW::getLanguage();

        // room_name Field
        $room_nameArr = explode ("-", $_COOKIE["video_recorded"]);
        $room_namex = $room_nameArr[0];

        $room_nameField = new TextField('room_name');
        $sValidator = new StringValidator(1, 22);
        $room_nameField->addValidator($sValidator);
        $room_nameField->setRequired(true);
        $room_nameField->setValue($room_namex);

        $this->addElement($room_nameField->setLabel($language->text('vwvr', 'room_name')));

        // Description Field
        $descriptionField = new Textarea('description');
        $this->addElement($descriptionField->setLabel($language->text('vwvr', 'description')));

        $tagsField = new TagsField('tags');
        $this->addElement($tagsField->setLabel($language->text('vwvr', 'tags')));

        $submit = new Submit('add');
        $submit->setValue($language->text('vwvr', 'btn_add'));
        $this->addElement($submit);
    }

    /**
     * Adds vwvr clip
     *
     * @return boolean
     */
    public function process()
    {
        $values = $this->getValues();
        $clipService = VWVR_BOL_ClipService::getInstance();

        $clip = new VWVR_BOL_Clip();
        $clip->room_name = htmlspecialchars($values['room_name']);

        $description = UTIL_HtmlTag::stripJs($values['description']);
        $description = UTIL_HtmlTag::stripTags($description, array('frame', 'style'), array(), true);
        $clip->description = $description;
        $clip->userId = OW::getUser()->getId();

        $room_nameArr = explode ("-", $_COOKIE["video_recorded"]);
        $room_namex = $room_nameArr[0]; // title
        $recordingIdx = $room_nameArr[1];
        $clip->recordingId="-".$recordingIdx;
        $clip->title = $room_namex;

//        $clip->code = UTIL_HtmlTag::stripJs($values['code']);

//        $prov = new VideoProviders($clip->code);

        $privacy = OW::getEventManager()->call(
            'plugin.privacy.get_privacy', 
            array('ownerId' => $clip->userId, 'action' => 'videorecorder_view_video')
        );
                    
//        $clip->provider = $prov->detectProvider();
        $clip->addDatetime = time();
        $config = OW::getConfig();
        $status = $config->getValue('vwvr', 'status');
        $clip->status = $status;
        $clip->privacy = mb_strlen($privacy) ? $privacy : 'everybody';

        $eventParams = array('pluginKey' => 'vwvr', 'action' => 'add_vwvr');

        if ( OW::getEventManager()->call('usercredits.check_balance', $eventParams) === true )
        {
            OW::getEventManager()->call('usercredits.track_action', $eventParams);
        }

        // add clip to video plugin
        $isVideoActive = OW::getPluginManager()->isPluginActive('video');
        if ($isVideoActive) {
          $clipServiceVideo = VIDEO_BOL_ClipService::getInstance();
          $clipVideo = new VIDEO_BOL_Clip();
          $clipVideo->title = htmlspecialchars($values['room_name']);
          $clipVideo->description = $description;
          $clipVideo->userId = OW::getUser()->getId();
  
          $clipVideo->code = '<iframe width="420" height="315" src="'.$config->getValue('vwvr', 'baseSwf_url').'streamplayer.swf?streamName='.$room_namex.$clip->recordingId.'&serverRTMP='.$config->getValue('vwvr', 'server').'&templateURL=" frameborder="0" allowfullscreen="allowfullscreen"></iframe>';
          $prov = new VideoProviders($clipVideo->code);
          $clipVideo->provider = $prov->detectProvider();
  
          $privacy = OW::getEventManager()->call(
              'plugin.privacy.get_privacy', 
              array('ownerId' => $clipVideo->userId, 'action' => 'video_view_video')
          );
          $clipVideo->addDatetime = time();
          $clipVideo->status = 'approved';
          $clipVideo->privacy = mb_strlen($privacy) ? $privacy : 'everybody';
  
          $clipServiceVideo->addClip($clipVideo);
        }

        // videowhisper
        // get record path
        $recordPath = $config->getValue('vwvr', 'recordPath');
        $recordPathArr = explode ('/', $recordPath);
        $recordPathArrCount = count ($recordPathArr);
      	for ($i=1; $i<$recordPathArrCount; $i++){
        $recorded .= "/".$recordPathArr[$i];
          if (!is_dir($recorded))
            mkdir ($recorded);
        }
      
        // get streams directory
        $dirname = 'streams';
        if (file_exists('../../'.$dirname)) {
          $dir = '../../'.$dirname;
        } elseif (file_exists('../../../'.$dirname)) {
          $dir = '../../../'.$dirname;
        } elseif (file_exists('../../../../'.$dirname)) {
          $dir = '../../../../'.$dirname;
        } elseif (file_exists('../../../../../'.$dirname)) {
          $dir = '../../../../../'.$dirname;
        } elseif (file_exists('../../../../../../'.$dirname)) {
          $dir = '../../../../../../'.$dirname;
        }
        // $streamsPath = realpath($dir);
        $streamsPath = realpath("../".$dir);
      
        // convert and copy, or just copy
        $filename = $_COOKIE["video_recorded"];
        $old = $streamsPath."/streams/".$filename;
        $new = $recordPath."/".$filename;
        $ffmpeg = trim(shell_exec('type -P ffmpeg'));
        if (empty($ffmpeg)) {
        	copy($old.'.flv', $new.'.flv');
        } else 
        {
          shell_exec($ffmpeg . ' -i '.$old.'.flv -sameq -ar 22050 '.$new.'.mp4');
        }

        if ( $clipService->addClip($clip) )
        {
            BOL_TagService::getInstance()->updateEntityTags($clip->id, 'vwvr', TagsField::getTags($values['tags']));
            
            // Newsfeed
            $event = new OW_Event('feed.action', array(
                'pluginKey' => 'vwvr',
                'entityType' => 'vwvr_comments',
                'entityId' => $clip->id,
                'userId' => $clip->userId
            ));
            
            OW::getEventManager()->trigger($event);

            return array('result' => true, 'id' => $clip->id);
        }

        return false;
    }
}
