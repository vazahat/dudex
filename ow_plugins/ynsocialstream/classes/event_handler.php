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
 *
 * @author Sergey Kambalin <greyexpert@gmail.com>
 * @package ow_plugins.newsfeed.classes
 * @since 1.0
 */
class YNSOCIALSTREAM_CLASS_EventHandler
{
    /**
     * Singleton instance.
     *
     * @var NEWSFEED_CLASS_EventHandler
     */
    private static $classInstance;

    /**
     * Returns an instance of class (singleton pattern implementation).
     *
     * @return NEWSFEED_CLASS_EventHandler
     */
    public static function getInstance()
    {
        if ( self::$classInstance === null )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    /**
     *
     * @var NEWSFEED_BOL_Service
     */
    private $service;

    private function __construct()
    {
        $this->service = NEWSFEED_BOL_Service::getInstance();
    }

    public function onApplicationInit( OW_Event $e )
    {
    	$cssUrl = OW::getPluginManager()->getPlugin('ynsocialstream')->getStaticCssUrl() . 'sociastream.css';
        OW::getDocument()->addStyleSheet($cssUrl);
		
    	$providers = array('Facebook' => "facebook", 'Twitter' => "twitter", 'LinkedIn' => "linkedin");
		
		$user = OW::getUser()->getUserObject();
				
		$user_id = $user -> getId();		
		$profile = $user -> username;
		
		//can't get feed in another profile
		$pos = strpos(OW_Router::getInstance()->getUri(), "user/");
		
		if($pos ===0){			
			if(OW_Router::getInstance()->getUri() == "user/".$profile)
				$this->init_icon($providers,$user_id);
		}  
		else{			
			$this->init_icon($providers,$user_id);
		}  
	}

	private function init_icon($providers, $user_id)
	{
		//$callback = OW_Router::getInstance()->getBaseUrl().OW_Router::getInstance()->getUri();
		$callback = OW::getRequest()->getRequestUri();
		 
		$config = OW::getConfig(); 
		foreach($providers as $key => $provider )
		{	
			$core = new YNSOCIALSTREAM_CLASS_Core();
			
			if(!OW::getConfig()->configExists('ynsocialstream', 'enable_facebook_'.$user_id))
	        {	        	
	        	
	        	OW::getConfig()->addConfig('ynsocialstream', 'enable_facebook_'.$user_id, 1);
	        	OW::getConfig()->addConfig('ynsocialstream', 'enable_twitter_'.$user_id, 1);
	        	OW::getConfig()->addConfig('ynsocialstream', 'enable_linkedin_'.$user_id, 1);
				OW::getConfig()->addConfig('ynsocialstream', 'cron_job_user_'.$user_id, 1); 
				OW::getConfig()->addConfig('ynsocialstream', 'auth_fb_'.$user_id, 'only_for_me');
				OW::getConfig()->addConfig('ynsocialstream', 'auth_tw_'.$user_id, 'only_for_me');
				OW::getConfig()->addConfig('ynsocialstream', 'auth_li_'.$user_id, 'only_for_me');
	        }	
			$configs = OW::getConfig()->getValues('ynsocialstream');
				
			
			if($core->checkSocialBridgePlugin($provider) && $configs['enable_'.$provider.'_'.$user_id])
			{			
			
				$src = OW::getPluginManager()->getPlugin('ynsocialstream')->getStaticUrl().'img/' . 'socialStream_icon.png';
							//echo $src;die;
				$html = "<a class=\"ynsocialstream_get_feed_".$provider."\" title=".$key." id=\"get_feed_".$provider."\" rel=\"$provider\" href=\"javascript://\" style=\"margin:0 0 0 -1px;\"></a>";
				$url = OW::getRouter()->urlFor('YNSOCIALSTREAM_CTRL_Socialstream', 'connect');
				
				$sript = "$(document).ready(function() {if($('.ow_attachment_icons #nfa-feed1 span.buttons').length){
					$('.ow_attachment_icons #nfa-feed1 span.buttons').append('".$html."');
					$('#get_feed_".$provider."').click(function(){			
						$('.ow_submit_auto_click').hide();
			    		$('#attachment_preview_nfa-feed1').show();
			    		$('#attachment_preview_nfa-feed1').empty().addClass('attachment_preloader').animate({height:45});
			    	
			    	$.ajax({
						 type: 'POST',
						 url: '$url',
						 data: 'service=$provider&url=$callback',
						 dataType: 'json',
						 success : function(data){
							document.location.href=data;
		                },
		                error : function( XMLHttpRequest, textStatus, errorThrown ){
		                	OW.error(textStatus);
					   }
					});
			    	
					});
				}});";
				OW::getDocument()->addScriptDeclaration($sript);
			}
		}
	}		
}