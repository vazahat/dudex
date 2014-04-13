<?php
require_once OW::getPluginManager()->getPlugin('ynsocialbridge')->getRootDir().'libs/linkedin.php';

class YNSOCIALBRIDGE_CLASS_Linkedin extends YNSOCIALBRIDGE_CLASS_Abstract
{
	protected $_plugin = 'linkedin';
	public  $_api_params = null;
	
	public function __construct()
	{
		if (!$this -> _objPlugin)
		{
			$this->getPlugin();
		}
		
	}
	/**
	 * Save Token
	 *
	 * @return
	 */
	public function saveToken($params = array())
	{
		$user_id = OW::getUser() -> getId();
		if ($user_id > 0)
		{
			//get token old if exists
			$params = array(
				'service' => $this -> _plugin,
				'userId' => $user_id
			);
			// YNSOCIALBRIDGE_BOL_Token
			$tokenDto = $this -> getToken($params);
			if (!$tokenDto)
			{
				//save new Token
				$tokenDto = new YNSOCIALBRIDGE_BOL_Token();
				$tokenDto -> service = $this -> _plugin;
				$tokenDto -> userId = $user_id;
			}
			$tokenDto -> accessToken = $_SESSION['socialbridge_session'][$this -> _plugin]['access_token'];
			$tokenDto -> secretToken = $_SESSION['socialbridge_session'][$this -> _plugin]['secret_token'];
			$tokenDto -> uid = $this -> getOwnerId();
			$tokenDto -> timestamp = time();
			YNSOCIALBRIDGE_BOL_TokenService::getInstance() -> save($tokenDto);
		}	
	}
	/**
	 * Get plugin
	 *
	 * @return
	 */
	public function getPlugin($params = array())
	{
		$clientConfig = YNSOCIALBRIDGE_BOL_ApisettingService::getInstance() -> getConfig($this->_plugin);
		if ($clientConfig == null)
		{
			return false;
		}
		$api_params = unserialize($clientConfig -> apiParams);
		if(isset($_GET['oauth_callback']) && $_GET['oauth_callback']!= null)
		{
			
			$this ->_objPlugin = new linkedin_API(array(
				'appKey' => $api_params['key'],
				'appSecret' =>$api_params['secret'],
				'callbackUrl' => $_GET['oauth_callback']
			));
		}
		else 
		{
			$this ->_objPlugin = new linkedin_API(array(
				'appKey' => $api_params['key'],
				'appSecret' =>$api_params['secret']	,
				'callbackUrl' => ''						
			));
		}
	}

	/**
	 * Get contact list
	 *
	 * @return array (id, pic, name)
	 */
	public function getContacts($params = array())
	{
		if ($this -> _objPlugin)
		{
			$this->initConnect($params);	       
			$response = $this->_objPlugin->connections();			
	        $connections = new SimpleXMLElement($response['linkedin']);				
			$datatopost = null;
			$index = 0;
			foreach($connections as $con)
			{
				$datatopost .= 'id_'.$index.','.(string)$con->id.',name_'.$index.','.(string)$con->{'first-name'}.',pic_'.$index.','.(string)$con->{'picture-url'}.',';
				$index++;
						
			}
			if($index==0)
				return array();
			else
			{
				$array_data = explode(',', $datatopost);
				$count = count($array_data) - 1;
				$contacts = array();
				for ($i = 0; $i < $count - 1; $i += 6)
				{
					$contacts[$array_data[$i + 1]] = array(
						'id' => $array_data[$i + 1],
						'name' => $array_data[$i + 3],
						'pic' => $array_data[$i + 5]
					);
				}
			}
			return $contacts;
		}
		return null;
	}
	
	
	private function initConnect($params = array())
	{
		$access = array(
	            'oauth_callback_confirmed' => true,
	            'xoauth_request_auth_url' => 'https://api.linkedin.com/uas/oauth/authorize',
	            'oauth_expires_in' => 599);
		if(!isset($params['secret_token']))
		{
			$access['oauth_token'] = @$_SESSION['socialbridge_session'][$this->_plugin]['access_token'];
	        $access['oauth_token_secret'] = @$_SESSION['socialbridge_session'][$this->_plugin]['secret_token'];
		}
		else
		{
			$access['oauth_token'] = $params['access_token'];
	        $access['oauth_token_secret'] = $params['secret_token'];
		}
	    $this->_objPlugin->setTokenAccess($access);
	}
	

	/**
	 * Get owner info
	 *
	 * @return array (id, name, first_name, last_name, link,...)
	 */
	public function getOwnerInfo($params = array())
	{
		if ($this -> _objPlugin)
		{
			$this->initConnect($params);	
			$fields = '';
			if(!isset($params['fields']) || $params['fields'] == '')			
				$fields = '~:(id,first-name,last-name,maiden-name,formatted-name,phonetic-first-name,phonetic-last-name,formatted-phonetic-name,headline,location,industry,distance,relation-to-viewer,current-status,current-status-timestamp,current-share,num-connections,num-connections-capped,summary,specialties,positions,picture-url,site-standard-profile-request,api-standard-profile-request,public-profile-url,email-address,last-modified-timestamp,proposal-comments,associations,honors,interests,publications,patents,languages,skills,certifications,educations,courses,volunteer,three-current-positions,three-past-positions,num-recommenders,recommendations-received,mfeed-rss-url,following,job-bookmarks,suggestions,date-of-birth,member-url-resources,related-profile-views)';			
			$response = $this->_objPlugin->profile($fields);
	        $connections = new SimpleXMLElement($response['linkedin']);	
			$arr_data = (array)$connections;
			
			if(count($arr_data)> 0)
			{
				$me = $arr_data;
				$me['identity'] = @$arr_data['id'];
				$me['first_name'] = @$arr_data['first-name'];
				$me['last_name'] = @$arr_data['last-name'];
				$me['username'] = @$me['first_name'] .@$me['last_name'];
				$me['displayname'] = @$me['formatted-name'];
				$me['picture'] = @$me['picture-url'];
				$me['photo_url'] = @$me['picture-url'];
				$me['email'] = @$me['email-address'];					
				$me['service'] = 'linkedin';				
				$year = @$me['date-of-birth']->year;
				$month = @$me['date-of-birth']->month;
				$day= @$me['date-of-birth']->day;
				
				if (!empty($year) && !empty($month) && !empty($day))
				{
					$me['birthdate'] = date("Y-m-d", strtotime($year.'-'.$month.'-'.$day));
				}		
				return $me;
			}			
			return null;
		}
		return null;
	}

	/**
	 * Get owner id
	 *
	 * @return string
	 */
	public function getOwnerId($params = array())
	{
		if ($this -> _objPlugin)
		{
			$this->initConnect($params);			
			$fields = '~:(id)';			
			$response = $this->_objPlugin->profile($fields);			
	        $connections = new SimpleXMLElement($response['linkedin']);	
			if(!empty($connections->id))
				return (string)$connections->id;	
			return null;
		}
		return null;
	}

	/**
	 * Get user info
	 *
	 * @param array (uid)
	 * @return array (id, name, first_name, last_name, link,...)
	 */
	public function getUserInfo($params = array())
	{
		
	}

	/**
	 * Get users info
	 *
	 * @return array
	 */
	public function getUsersInfo($params = array())
	{

	}

	/**
	 * Get albums
	 *
	 * @return array
	 */
	public function getAlbums($params = array())
	{

	}

	/**
	 * Get album info
	 *
	 * @return array
	 */
	public function getAlbumInfo($params = array())
	{

	}

	/**
	 * Get photos
	 *
	 * @return array
	 */
	public function getPhotos($params = array())
	{

	}

	/**
	 * Get photo info
	 *
	 * @return array
	 */
	public function getPhotoInfo($params = array())
	{

	}

	/**
	 * Get activity
	 *
	 * @param array(lastFeedTimestamp, limit)
	 * @return array (post_id,actor_id,target_id,message,description,created_time,attachment,permalink,description_tags,type)
	 */
	public function getActivity($params = array('lastFeedTimestamp' => 0,'limit' => 5))
	{
		$arr_data = array();
		if ($this -> _objPlugin)
		{
			$_SESSION['socialbridge_session']['linkedin']['stream'] = 1;
			$access['oauth_token'] = $params['access_token'];
	        $access['oauth_token_secret'] = $params['secret_token'];
	    	$this->_objPlugin->setTokenAccess($access);
			$this->initConnect($params);	
			
			if (!isset($params['type']) || (isset($params['type']) && $params['type'] != 'user')) 
        	{
			
				//Connection Updates
				$fields = '?type=CONN';
				if($params['lastFeedTimestamp'])
				{
					$fields .= '&after='.($params['lastFeedTimestamp'] + 1);
				}
				$response = $this->_objPlugin->updates($fields);
				if($response['success'])
		        {
		        	$activities = new SimpleXMLElement($response['linkedin']);	
					$activities = (array)$activities;
					if($activities['@attributes']['total'] == 1)
						$updates = array('0' => (array)$activities['update']);
					else
						$updates = (array)@$activities['update'];
					$arr_data = array_merge($arr_data,$updates);			
				}
				
				//Joined a Group
				$fields = '?type=JGRP';
				if($params['lastFeedTimestamp'])
				{
					$fields .= '&after='.($params['lastFeedTimestamp'] + 1);
				}
				$response = $this->_objPlugin->updates($fields);
				if($response['success'])
		        {
		        	$activities = new SimpleXMLElement($response['linkedin']);	
					$activities = (array)$activities;
					if($activities['@attributes']['total'] == 1)
						$updates = array('0' => (array)$activities['update']);
					else
						$updates = (array)@$activities['update'];
					$arr_data = array_merge($arr_data,$updates);			
				}
				
				//Shared item
				$fields = '?type=SHAR';
				if($params['lastFeedTimestamp'])
				{
					$fields .= '&after='.($params['lastFeedTimestamp'] + 1);
				}
				$response = $this->_objPlugin->updates($fields);
				if($response['success'])
		        {
		        	$activities = new SimpleXMLElement($response['linkedin']);	
					$activities = (array)$activities;
					if($activities['@attributes']['total'] == 1)
						$updates = array('0' => (array)$activities['update']);
					else
						$updates = (array)@$activities['update'];
					$arr_data = array_merge($arr_data,$updates);			
				}
				
				//All updates
				$fields = '';
				if($params['lastFeedTimestamp'])
				{
					$fields .= '?after='.$params['lastFeedTimestamp'];
				}
				$response = $this->_objPlugin->updates($fields);
				if($response['success'])
		        {
		        	$activities = new SimpleXMLElement($response['linkedin']);	
					$activities = (array)$activities;
					$updates = (array)@$activities['update'];
					$arr_data = array_merge($arr_data,$updates);			
				}
			}
			//get self
			$fields = '?scope=self';
			if($params['lastFeedTimestamp'])
			{
				$fields .= '&after='.($params['lastFeedTimestamp'] + 1);
			}
			$response = $this->_objPlugin->updates($fields);
			if($response['success'])
	        {
	        	$activities = new SimpleXMLElement($response['linkedin']);	
				$activities = (array)$activities;
				if($activities['@attributes']['total'] == 1)
					$updates = array('0' => (array)$activities['update']);
				else
					$updates = (array)@$activities['update'];
				$arr_data = array_merge($arr_data,$updates);			
			}	
			if($params['lastFeedTimestamp'])
			{
				krsort($arr_data);
			}
			if($params['limit'])
			{
				$arr_data = array_slice($arr_data, 0, $params['limit']);
			}
			if(!$params['lastFeedTimestamp'])
			{
				krsort($arr_data);
			}
		}
		return $arr_data;
	}
	
	/**
	 * Insert feeds
	 *
	 * @param array(activities, viewer, timestamp)
	 * @return bool
	 */
	public function insertFeeds($params = array())
	{
		$access['oauth_token'] = $params['access_token'];
        $access['oauth_token_secret'] = $params['secret_token'];
    	$this->_objPlugin->setTokenAccess($access);
		//$viewer = $params['viewer'];
		$user_id = $params['user_id'];
		$uid = $params['uid'];		
		$activities = $params['activities'];
		//init language
		$language = OW::getLanguage();
		
		//init Social stream feeds service				
		$streamFeedService = YNSOCIALSTREAM_BOL_SocialstreamFeedService::getInstance();
		$nophoto = OW::getPluginManager()->getPlugin('ynsocialstream')->getStaticUrl().'img/li_nopho.png';
		//init time now
		$now = time();
		
	  	foreach($activities as $activity)
		{
			$activity = (array)$activity;
			if((@$activity['timestamp'] > $params['timestamp']) 
			&& $activity['update-content']->{'person'}->{'first-name'} != 'private')
			{
				
				//insert feed to social stream feed 				
				try
				{					
					$type = $activity['update-type'];
					$person = (array)$activity['update-content']->{'person'};					
					//friend info
					$friend_description = @$person['current-status'];
					$friend_name = (string)@$person['first-name'].' '.(string)@$person['last-name'];
					$friend_href = (string)@$person['site-standard-profile-request']->{'url'};
					$href_friend = "<a href = '".$friend_href."' target = '_blank'>".$friend_name."</a>";
					$arr_info = array();
		    		//check type and get data.
		    		switch ($type) 
					{
						case 'CONN':
							//object info
							$arr_info['photo_url'] = (string)$person['connections']->{'person'}->{'picture-url'};
							$arr_info['title'] = (string)$person['connections']->{'person'}->{'first-name'}.' '. (string)$person['connections']->{'person'}->{'last-name'};
							$arr_info['href'] = (string)$person['connections']->{'person'}->{'site-standard-profile-request'}->{'url'};
							$arr_info['description'] = (string)$person['connections']->{'person'}->{'headline'};
							
							//$friend_description = $href_friend.$language->text('ynsocialstream', 'has_a_new_connection.');							 
							$friend_description = $href_friend.' has a new connection.';
							break;
							
						case 'PROF';
						if($activity['updated-fields']->{'update-field'}->{'name'} == 'person/skills')
						{
							$skills = (array)$person['skills'];
							if($skills)
							{
								//$description = "</br>".$language->text('ynsocialstream', 'Skills')." - ";
								$description = "</br>Skills - ";
								$count = 0;
								foreach($skills['skill'] as $skill)
								{
									$count ++;
									$description.= $skill->{'skill'}->{'name'};
									if($count < $skills['@attributes']['count'])
										$description .= ', ';
								}
							}
							//$friend_description = $href_friend.$language->text('ynsocialstream', 'has_an_updated_profile');
							$friend_description = $href_friend.' has an updated profile';
							$friend_description .= $description;
						}

						case 'JGRP':
							$group = @$person['member-groups']->{'member-group'};
							$group_href = (string)@$group->{'site-group-request'}->{'url'};
							$result = array();
							if($group_href)
							{
								try
								{
									$client = new Zend_Http_Client($group_href, array(
								        'maxredirects' => 2,
								        'timeout'      => 10,
								      ));
								      // Try to mimic the requesting user's UA
								      $client->setHeaders(array(
								        'User-Agent' => @$_SERVER['HTTP_USER_AGENT'],
								        'X-Powered-By' => 'Zend Framework'
								      ));
								
								      $response = $client->request();	
									  list($contentType) = explode(';', $response->getHeader('content-type'));
									  // Handling based on content-type
								      switch( strtolower($contentType) ) 
								      {
								        // Images
								        case 'image/gif':
								        case 'image/jpeg':
								        case 'image/jpg':
								        case 'image/tif': // Might not work
								        case 'image/xbm':
								        case 'image/xpm':
								        case 'image/png':
								        case 'image/bmp': // Might not work
								          $result = $this->_parseImage($group_href, $response);
								          break;
								
								        // HTML
								        case '':
								        case 'text/html':
								          $result = $this->_parseHtml($group_href, $response);
								          break;
								
								        // Plain text
								        case 'text/plain':
								          $result = $this->_parseText($group_href, $response);
								          break;
								
								        // Unknown
								        default:
								          break;
								      }
								}
								catch( Exception $e )
							    {
							      throw $e;
							    }
							}
							$arr_info['photo_url'] = @$result['uri'];
							$arr_info['title'] = @$result['title'];
							$arr_info['href'] = $group_href;
							$arr_info['description'] = @$result['description'];
							//$friend_description = $href_friend.$language->text('ynsocialstream', 'joined_a_group');
							$friend_description = $href_friend.' joined a group';
							
							break;
						
						case 'SHAR':
							$share = (array)@$person['current-share'];
							$arr_info['photo_url'] = (string)@$share['content']->{'submitted-image-url'};
							$arr_info['title'] = (string)@$share['content']->{'title'};
							$arr_info['href'] = (string)@$share['content']->{'submitted-url'};
							$arr_info['description'] = (string)@$share['content']->{'description'};
							$friend_description = (string)@$share['comment'];
							$regex = '@((https?://)?([-\w]+\.[-\w\.]+)+\w(:\d+)?(/([-\w/_\.]*(\?\S+)?)?)*)@';
							$friend_description = preg_replace($regex, '<a href="$1">$1</a>', $friend_description);

							break;
							
						case 'STAT':
							$regex = '@((https?://)?([-\w]+\.[-\w\.]+)+\w(:\d+)?(/([-\w/_\.]*(\?\S+)?)?)*)@';
							$friend_description = preg_replace($regex, '<a href="$1">$1</a>', $friend_description);
					
							break;
						case 'PICU':
							if($person['id'] != 'private')
								//$friend_description = $href_friend.$language->text('ynsocialstream', 'has_a_new_profile_picture');
								$friend_description = $href_friend.' has a new profile picture';
							break;
							
						case 'PRFX':
							//$friend_description = $href_friend.$language->text('ynsocialstream', 'has_updated_their_extended_profile_data');
							$friend_description = $href_friend.' has updated their extended profile data';
							break;
							
						default:
							
							break;
					}
					$values = array();
					$values = array_merge(array(	    			
					  	'provider' 		=> 'linkedin',
						'user_id'		=> $user_id,
						'uid'			=> $uid,
					  	'timestamp' 	=> (string)$activity['timestamp'],
					  	'update_key' 	=> (string)$activity['update-key'],
					  	'update_type' 	=> (string)$activity['update-type'],
					  	
					  	'photo_url' 	=> $arr_info['photo_url'],
					  	'title' 		=> $arr_info['title'],
					  	'href'			=> $arr_info['href'],
					 	'description'	=> $arr_info['description'],
					 	
					 	'friend_id'		=> (string)$person['id'],
					 	'friend_name'	=> $friend_name,
					 	'friend_href'	=> $friend_href,
					 	'friend_description'	=> strip_tags($friend_description),
					 		  	
					));	
					
					// save feed
					$streamFeedDto = $streamFeedService->parseFeedArray($values);
					$streamFeedService->saveFeed($streamFeedDto);					
					
					//attachment todo
					$oembed = null;
					$url = null;
					$attachId = null;			
					//$feed = $language->text('ynsocialstream', 'gets ')."<a href=\"".$values['friend_href']."\" target=\"_blank\">".$values['friend_name']."'s</a>".$language->text('ynsocialstream', '  feed from Linkedin.')."<span class=\"feed_item_bodytext\">".$values['friend_description']."</span>[ph:attachment]";					
					$feed = "gets <a href=\"".$values['friend_href']."\" target=\"_blank\">".$values['friend_name']."'s</a>  feed from Linkedin. <span class=\"feed_item_bodytext\">".$values['friend_description']."</span>[ph:attachment]";
					
					//get privacy
					$privacy = 'everybody';
					$configs = OW::getConfig()->getValues('ynsocialstream');  
					if(isset($configs['auth_li_'.$user_id]))
			        {
			        	$privacy = $configs['auth_li_'.$user_id] ; 
			        }
					
					if(strlen($values['href'])>0)
					{
						//$values['photo_url'] = 'http://s.c.lnkd.licdn.com/scds/common/u/images/themes/katy/ghosts/person/ghost_person_65x65_v1.png';
						if(strlen($values['photo_url']) == 0 )
						{
							$values['photo_url']  = $nophoto;
						}
						
						$oembed = array(
							'uid'=>'nfa-feed1',
							'type' => 'photo',
							'genId' => null,
							'thumbnail_url' => $values['photo_url'],
							'href'=> $values['href'],
							'title' => $values['title'],
							'description' => $values['description']
						);
						
						
					}
					if($oembed)
					{
						$event = new OW_Event('feed.action', array(
			                'pluginKey' => 'newsfeed',
			                'entityType' => 'action',
			                'time' 		=> $now++,
			                'entityId' => $streamFeedDto->id,
			                'userId' => $user_id,
			                'privacy' => $privacy,
					            ), array(
									'string' => $feed,
						            'attachment' => array(
						                'oembed' => $oembed,
						                'url' => $url,
						                'attachId' => $attachId
						            ),
								)
						);		
					}
					else 
					{
						$event = new OW_Event('feed.action', array(
			                'pluginKey' => 'newsfeed',
			                'entityType' => 'action',
			                'time' 		=> $now++,
			                'entityId' => $streamFeedDto->id,
			                'userId' => $user_id,
			                'privacy' => $privacy,
					            ), array(
									'string' => $feed,						            
								)
						);		
					}
					
					
			        OW::getEventManager()->trigger($event);		
					
					
				}
				catch( Exception $e)
				{
					throw new InvalidArgumentException('Can not insert feed from Linkedin in social stream feed');
					exit;
				}
		    }				
		}
	}
	
	/**
	 * Send invite
	 *
	 * @param array (to string, message string, link string, uid int, access_token string)
	 * @return bool
	 */
	public function sendInvite($arr = array())
	{
		if($this->_objPlugin)
		{
			try 
			{
				$mailtemp = $arr['message'].$arr['link'];
				$user_id = $arr['user_id'];
				$this->initConnect();	
				$members = array();
				$members[] = key($arr['list']);
		        $response = $this->_objPlugin->message($members, BOL_UserService::getInstance()->getDisplayName($user_id), htmlspecialchars($mailtemp), true);
		        if($response['success'] === TRUE) {
		          //successful
		        } 
			} catch (Exception $e) 
			{
				echo "Error sending message:\n\nRESPONSE:\n\n" . print_r($e->getMessage(), TRUE) . "\n\nLINKEDIN OBJ:\n\n" ;		
			}
		}	
	}

	/**
	 * Send invites
	 *
	 * @param array (list array, message string, link string, uid int, access_token string)
	 * @return bool
	 */
	public function sendInvites($arr = array())
	{
		if($this->_objPlugin)
		{
			try 
			{
				$mailtemp = $arr['message'].$arr['link'];
				$user_id = $arr['user_id'];
				$uid = $arr['uid'];
				$this->initConnect($arr);
				
				//get max invite per day
				$max_invite = 10;
				$clientConfig = YNSOCIALBRIDGE_BOL_ApisettingService::getInstance() -> getConfig($this -> _plugin);
				if($clientConfig)
				{
					$api_params = unserialize($clientConfig -> apiParams);
					if($api_params['max_invite_day'])
					{
						$max_invite = $api_params['max_invite_day'];
					}
				}
				$count_invite = 0;
				$values = array('userId' => $user_id, 'uid' => $uid, 'service' => $this -> _plugin, 'date' => date('Y-m-d'));
				$total_invited = $this->getTotalInviteOfDay($values);
				$count_invite_succ = $total_invited;
				$count_invite = $total_invited;
				
				$count_queues = 0;
				$arr_user_queues = array();
				
				$members = array();
				foreach($arr['list'] as $key => $name)
				{
					if($count_invite < $max_invite)
					{
						$members[] = $key;
					}
					else
					{
						$count_queues ++;
						$arr_user_queues[$key] = $name;
					}
					$count_invite ++;
				}			
				if(count($members) > 0)
				{
		        	$response = $this->_objPlugin->message($members, BOL_UserService::getInstance()->getDisplayName($user_id) , htmlspecialchars($mailtemp), true);
			        if($response['success'] === TRUE) 
			        {
			        	$count_invite_succ = $count_invite_succ + count($members);
			        }
				} 

				//save statistics
				$values = array('userId' => $user_id, 'uid' => $uid, 'service' => $this -> _plugin, 'inviteOfDay'=> $count_invite_succ,'date' => date('Y-m-d'));
				$this->createOrUpdateStatistic($values);

				// Save queues
				if($count_queues > 0)
				{
					$values = array(
							'uid' => $uid,
							'service' => $this -> _plugin,
							'userId' => $user_id
						);
					$token = $this->getToken($values);
					if($token)
					{
						$extra_params['list'] = $arr_user_queues;
						$extra_params['link'] = $arr['link'];
						$extra_params['message'] = $arr['message'];
						$values = array(
								'tokenId' => $token->id,
								'userId' => $user_id,
								'service' => $this -> _plugin,
								'type' => 'sendInvite',
								'extraParams' => serialize($extra_params),
								'lastRun' => time(),
								'status' => 0,
								);
						$this->saveQueues($values);
					}
				}
				return true;
			} 
			catch (Exception $e) 
			{
				echo "Error sending message:\n\nRESPONSE:\n\n" . print_r($e->getMessage(), TRUE) . "\n\nLINKEDIN OBJ:\n\n" ;
				return false;	
			}
		}
	}

	/**
	 * Post activity
	 *
	 * @param array
	 * @return bool
	 */
	public function postActivity($params = array())
	{
		if ($this -> _objPlugin)
	    {
	        try {
    	        $content = array(
    	                'comment' => $params['comment'],
    	                'title' => $params['title'],
    	                'submitted-url' => $params['submitted-url'],
    	                'description' => $params['description']
    	        );
    	        if (!empty($params['submitted-image-url'])) {
    	            $content['submitted-image-url'] = $params['submitted-image-url'];
    	        }

    	        $this->initConnect($params);
    	        if (!empty($params['message']) && !empty($params['link'])) {
    	            $status = "<a href='{$params['link']}' target='_blank'>{$params['message']}</a>";
    	        }

	            $response = $this -> _objPlugin->share('new', $content, FALSE);
	            return true;
	        } catch (Exception $e) {
	            return $e->getMessage();
	        }
	    }
	    return false;

	}


	/**
	 * Get login status
	 *
	 * @param array
	 * @return bool
	 */
	public function getLoginStatus($params = array())
	{
		
	}

	/**
	 * Get throttle limit
	 *
	 * @param array
	 * @return integer
	 */
	public function getThrottleLimit($params = array())
	{

	}

	/**
	 * Get login URL
	 *
	 * @param array (name, module)
	 * @return string
	 */
	public function getLoginUrl($params = array())
	{
		
	}

	/**
	 * Get logout
	 *
	 * @return string
	 */
	public function getLogoutUrl($params = array())
	{
		
	}

	/**
	 * Has permission
	 *
	 * @return bool
	 */
	public function hasPermission($params = array())
	{

	}
	// MinhNC add
	
	public function getGetType()
	{
		return $this->_objPlugin->getGetType();
	}
	public function getGetResponse()
	{
		return $this->_objPlugin->getGetResponse(); 
	}
	public function retrieveTokenRequest($params = array())
	{	
		return $this->_objPlugin->retrieveTokenRequest($params);
	}
	public function getUrlAuth()
	{
		return $this->_objPlugin->getUrlAuth();
	}
	public function retrieveTokenAccess($token, $secret, $verifier)
	{
		return $this->_objPlugin->retrieveTokenAccess($token, $secret, $verifier);
	}
	public function getProfile()
	{
		return $this->_objPlugin->profile();
	}
}
