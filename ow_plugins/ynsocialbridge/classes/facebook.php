<?php
require_once OW::getPluginManager() -> getPlugin('ynsocialbridge') -> getRootDir() . 'libs/facebook.php';

class YNSOCIALBRIDGE_CLASS_Facebook extends YNSOCIALBRIDGE_CLASS_Abstract
{
	protected $_plugin = 'facebook';
	public $_accessToken = null;
	public $_me = null;

	public function __construct()
	{
		if (!$this -> _objPlugin)
		{
			$this -> getPlugin();
		}
	}

	/**
	 * Save Token
	 *
	 * @return
	 */
	public function saveToken($params = array())
	{
		$token = $this -> _objPlugin -> getUserAccessToken();
		
		
				
		if($token)
		{
			$this -> _objPlugin -> setAccessToken($token);
			$token_extended = $this -> _objPlugin->setExtendedAccessToken();
			
			if($token_extended)
			{
				$token =  $token_extended;
			}
			
			$this -> _objPlugin->setAccessToken($token);
			
			$this -> _me = $this -> _objPlugin -> api('/me');
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
				$tokenDto -> accessToken = $token;
				$tokenDto -> uid = $this -> getOwnerId();
				$tokenDto -> timestamp = time();
				YNSOCIALBRIDGE_BOL_TokenService::getInstance() -> save($tokenDto);
			}
		}
	}

	/**
	 * Get plugin
	 *
	 * @return
	 */
	public function getPlugin($params = array())
	{
		$provider = YNSOCIALBRIDGE_BOL_ApisettingService::getInstance() -> getConfig($this -> _plugin);
		if ($provider == null)
		{
			return false;
		}
		$api_params = unserialize($provider -> apiParams);
		$this -> _objPlugin = new YNSFacebook( array(
			'appId' => $api_params['key'],
			'secret' => $api_params['secret'],
			'cookie' => true,
		));
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
			$contact = array();
			try
			{
				$friends = $this -> _objPlugin -> api('/me/friends');
				$imgLink = "http://graph.facebook.com/%s/picture";
				foreach ($friends as $key => $value)
				{
					foreach ($value as $key2 => $aFriend)
					{
						$friends[$key][$key2]['pic'] = sprintf($imgLink, $aFriend['id']);
						$contact[$aFriend['id']] = array(
							'id' => $aFriend['id'],
							'pic' => sprintf($imgLink, $aFriend['id']),
							'name' => $aFriend['name'],
						);
					}
					break;
				}
			}
			catch(FacebookApiException $e)
			{
				throw $e;
				return null;
			}
			return $contact;
		}
		return null;
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
			try
			{
				if (isset($params['access_token']))
					$this -> _objPlugin -> setAccessToken($params['access_token']);
				$me = $this -> _objPlugin -> api('/me');
				$me['identity'] = $me['id'];
				$me['displayname'] = @$me['name'];
				$location = @$me['location']['name'];
				if ($location)
				{
					$arr_location = explode(",", $location);
					if (count($arr_location) > 0)
						$me['country'] = $arr_location[count($arr_location) - 1];
				}
				if (isset($me['username']))
					$me['facebook'] = $me['username'];
				else
				{
					$me['facebook'] = $me['id'];
					$me['username'] = $me['first_name'] . $me['last_name'];
				}
				$me['service'] = 'facebook';
				if (isset($me['birthday']) && !empty($me['birthday']))
				{
					$me['birthdate'] = date("Y-m-d", strtotime($me['birthday']));
				}
				$me['gender'] = ($me['gender'] == 'male') ? 1 : 2;
				$me['about_me'] = @$me['bio'];
				$me['about'] = @$me['bio'];
				$me['picture'] = 'https://graph.facebook.com/' . $me['id'] . '/picture?type=small';
				$me['photo_url'] = 'https://graph.facebook.com/' . $me['id'] . '/picture?type=large';
			}
			catch(FacebookApiException $e)
			{
				throw $e;
				return null;
			}
			return $me;
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
		if (isset($params['access_token']) && $params['access_token'])
			$this -> _objPlugin -> setAccessToken($params['access_token']);
		
		$me = $this -> _objPlugin -> api('/me');
		return $me['id'];
	}

	/**
	 * Get user info
	 *
	 * @param array (uid)
	 * @return array (id, name, first_name, last_name, link,...)
	 */
	public function getUserInfo($params = array())
	{
		if ($this -> _objPlugin)
		{
			$uid = $params['uid'];
			try
			{
				$user = $this -> _objPlugin -> api('/' . $uid);
			}
			catch(FacebookApiException $e)
			{
				throw $e;
				return null;
			}
			return $user;
		}
		return null;
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
		$extra = $params['extra'];
		$uid = $params['uid'];
		$limit = $params['limit'];
		$offset = $params['offset'];
		$aid = trim($params['aid'], "'");
		$aid = "'{$aid}'";

		/**
		 * fetch data for each albums
		 */
		switch($extra)
		{
			case 'like' :
				/**
				 * get albums that user liked
				 */
				$query = "SELECT aid, name, photo_count, cover_pid, owner FROM album WHERE  object_id IN (SELECT object_id FROM like WHERE user_id=$uid) LIMIT $limit OFFSET $offset";
				break;
			case 'tag' :
				/**
				 * get albums that contain photo which user is tagged.
				 */
				$query = "SELECT aid, name, photo_count, cover_pid, owner FROM album WHERE aid IN ( SELECT aid FROM photo WHERE pid IN (SELECT pid FROM photo_tag WHERE subject=$uid)) LIMIT $limit OFFSET $offset";
				break;
			case 'friend' :
				/**
				 * get albums of user's friends
				 */
				$query = "SELECT aid, name, photo_count, cover_pid, owner FROM album WHERE  owner IN (SELECT uid2 FROM friend WHERE uid1 = $uid) LIMIT $limit OFFSET $offset";
				break;
			case 'page' :
				/**
				 * get albums that belong to pages.
				 */
				break;
			case 'group' :
				/**
				 * get albums belong to groups that user is a member
				 */
				$query = "SELECT aid, name, photo_count, cover_pid, owner FROM album WHERE  owner IN (SELECT uid2 FROM friend WHERE uid1 = $uid) LIMIT $limit OFFSET $offset";
				break;
			case 'comment' :
				/**
				 * failed because fromid still not index in FBQL
				 */
				$query = "SELECT aid, name, photo_count, cover_pid, owner FROM album WHERE  bject_id IN (SELECT object_id FROM comment WHERE fromid = $uid) LIMIT $limit OFFSET $offset";
				break;
			case 'friend-like' :
				/**
				 * get photo that my friends have been liked.
				 */
				$query = "SELECT aid, name, photo_count, cover_pid, owner FROM album WHERE  owner IN (SELECT uid2 FROM friend WHERE uid1 = $uid) LIMIT $limit OFFSET $offset";
				break;
			default :
				$query = "SELECT aid, name, photo_count, cover_pid, owner FROM album WHERE owner = $uid LIMIT $limit OFFSET $offset";
		}

		$albums = $this -> _objPlugin -> api(array(
			'method' => 'fql.query',
			'query' => $query
		));
		return $albums;
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
	 * @param array(limit, )
	 * @return array
	 */
	public function getPhotos($params = array())
	{
		$extra = $params['extra'];
		$uid = $params['uid'];
		$limit = $params['limit'];
		$offset = $params['offset'];
		$aid = $params['aid'];
		$aid = trim($params['aid'], "'");
		$aid = "'{$aid}'";
		/**
		 * fetch data for each albums
		 */
		switch($extra)
		{
			case 'aid' :
				$query = "SELECT pid, aid, caption, owner, src_small, src, src_big, link FROM photo WHERE aid = $aid LIMIT $limit OFFSET $offset";
				break;
			case 'like' :
				/**
				 * get albums that user liked
				 */
				$query = "SELECT pid, aid, caption, owner, src_small, src, src_big, link FROM photo WHERE object_id IN (SELECT object_id FROM like WHERE user_id=$uid) LIMIT $limit OFFSET $offset";
				break;
			case 'tag' :
				/**
				 * get albums that contain photo which user is tagged.
				 */
				$query = "SELECT pid, created, aid, caption, owner, src_small, src, src_big, link FROM photo WHERE pid IN (SELECT pid FROM photo_tag WHERE subject=$uid) LIMIT $limit OFFSET $offset";
				break;
			case 'friend' :
				/**
				 * get albums of user's friends
				 */
				$query = "SELECT pid, created, aid, caption, owner, src_small, src, src_big, link FROM photo WHERE aid IN (SELECT aid FROM album WHERE owner IN (SELECT uid2 FROM friend WHERE uid1 = $uid) )  LIMIT $limit OFFSET $offset";
				break;
			case 'page' :
				/**
				 * get albums that belong to pages.
				 */
				break;
			case 'group' :
				/**
				 * get albums belong to groups that user is a member
				 */
				$query = "SELECT aid, name, caption, photo_count, cover_pid FROM album WHERE size > 0 AND owner IN (SELECT uid2 FROM friend WHERE uid1 = $uid) LIMIT $limit OFFSET $offset";
				break;
			case 'comment' :
				/**
				 * failed because fromid still not index in FBQL
				 */
				$query = "SELECT aid, name, caption, photo_count, cover_pid FROM album WHERE size > 0 AND bject_id IN (SELECT object_id FROM comment WHERE fromid = $uid)";
				break;
			case 'friend-like' :
				/**
				 * get photo that my friends have been liked.
				 */
				$query = "SELECT aid, name, caption, photo_count, cover_pid FROM album WHERE size > 0 AND owner IN (SELECT uid2 FROM friend WHERE uid1 = $uid) LIMIT $limit OFFSET $offset";
				break;
			case 'uid' :
			case 'my' :
			default :
				$query = "SELECT pid, aid,created, caption, owner, src_small, src, src_big, link FROM photo WHERE aid IN (select aid FROM album WHERE owner = $uid) LIMIT $limit OFFSET $offset";
		}

		$photos = $this -> _objPlugin -> api(array(
			'method' => 'fql.query',
			'query' => $query
		));
		return $photos;
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
	public function getActivity($params = array('lastFeedTimestamp' => 0, 'limit' => 5))
	{
		$this -> _objPlugin -> setAccessToken($params['access_token']);
		$result = array();

		if ($params['lastFeedTimestamp'] > 0)
		{
			$feeds = $this -> _objPlugin -> api('/' . $params['uid'] . '/feed?limit=999&since=' . $params['lastFeedTimestamp']);
			$result = @$feeds['data'];
			$temp = array();
		}
		else
		{
			$feeds = $this -> _objPlugin -> api('/' . $params['uid'] . '/feed?limit=' . $params['limit']);
			$result = @$feeds['data'];
		}
		krsort($result);
		if ($params['lastFeedTimestamp'] > 0)
		{
			$result = array_slice($result, 0, $params['limit']);
		}
		return $result;
	}

	/**
	 * Insert feeds
	 *
	 * @param array(activities, viewer, timestamp)
	 * @return bool
	 */
	public function insertFeeds($params = array())
	{
		$this -> _objPlugin -> setAccessToken($params['access_token']);			
		$user_id = $params['user_id'];
		$uid = $params['uid'];
		$activities = $params['activities'];				
		//init Social stream feeds service		
		$streamFeedService = YNSOCIALSTREAM_BOL_SocialstreamFeedService::getInstance();		
		$now = time();	
		foreach ($activities as $activity)
		{			
			if (strtotime($activity['created_time']) > $params['timestamp'])
			{
				//insert feed to social stream feed 				
				try
				{											
					$core = new YNSOCIALBRIDGE_CLASS_Core();					
					$obj = $core -> getInstance('facebook');	
					$actor = $obj->getUserInfo(array('uid' => $activity['from']['id']));
					$attachment['name'] = @$activity['name'];
				 	$attachment['src'] = @$activity['picture'];
				 	$attachment['href'] = @$activity['link'];
				 	$attachment['description'] = @$activity['description'];

				 	$type = $activity['type'];
					if(!isset($attachment['description']) || $attachment['description'] == "")
					{
						if(isset($attachment['caption']) && $attachment['caption'] != "")
							$attachment['description'] = $attachment['caption'];
						else
							$attachment['description'] = "<a href = 'http://www.facebook.com' target='_blank'>www.facebook.com</a>";
					}
					$description = "";
				 	if(isset($activity['message']) && $activity['message'] != "")
				 	{
				 		$description = $activity['message'];
					}
				 	elseif(isset($activity['story']) && $activity['story'] != "")
				 	{
						 $description = $activity['story'];
				 	}
				 	$regex = '@((https?://)?([-\w]+\.[-\w\.]+)+\w(:\d+)?(/([-\w/_\.]*(\?\S+)?)?)*)@';
				 	$description = preg_replace($regex, '<a href="$1">$1</a>', $description);

				 	if(isset($activity['place']))
				 	{
				 	$place = $activity['place'];				 	
				 	$place_link = "<a href = 'http://www.facebook.com/".$place['id']."' target='_blank'>".$place['name']."</a>";
				 	$description.= ' - at '.$place_link;
				 	}
					$values = array();
					$values = array_merge(array(
				 		'provider' 		=> 'facebook',
						'user_id'		=> $user_id,
						'uid'			=> $uid,
						'timestamp' 	=> strtotime($activity['created_time']),
						'update_key' 	=> $activity['id'],
						'update_type' 	=> $type,
						'photo_url' 	=> $attachment['src'],
						'title' 		=> $attachment['name'],
						'href'			=> $attachment['href'],
						'description'	=> $attachment['description'],		
						'friend_id'		=> $activity['from']['id'],
						'friend_name'	=> $actor['name'],
						'friend_href'	=> $actor['link'],
						'friend_description'	=> strip_tags($description),
						'privacy'	=> json_encode($activity['privacy']),
					));	
					// save feed
					$streamFeedDto = $streamFeedService->parseFeedArray($values);
					$streamFeedService->saveFeed($streamFeedDto);
					
					//attachment todo
					$oembed = null;
					$url = null;
					$attachId = null;			
					$feed = "gets <a href=\"".$values['friend_href']."\" target=\"_blank\">".$values['friend_name']."'s</a>  feed from Facebook. <span class=\"feed_item_bodytext\">".$values['friend_description']."</span>[ph:attachment]";					
					//$feed = $language->text('ynsocialstream', 'gets ')."<a href=\"".$values['friend_href']."\" target=\"_blank\">".$values['friend_name']."'s</a>".$language->text('ynsocialstream', '  feed from Facebook.')."<span class=\"feed_item_bodytext\">".$values['friend_description']."</span>[ph:attachment]";
					//get privacy
					$privacy = 'everybody';
					$configs = OW::getConfig()->getValues('ynsocialstream');  
					if(isset($configs['auth_fb_'.$user_id]))
			        {
			        	$privacy = $configs['auth_fb_'.$user_id] ; 
			        }					
					if(strlen($values['photo_url'])>0)
					{
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
      				if(!empty($oembed))
      				{
      					$event = new OW_Event('feed.action', array(
		                'pluginKey' => 'newsfeed',
		                'entityType' => 'action',
		                'entityId' => $streamFeedDto->id,
		                'userId' => $user_id,
		                'time' 		=> $now++,
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
					else {
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
					throw new InvalidArgumentException('Can not insert feed from Facebook in social stream feed');
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
	public function sendInvite($params = array())
	{
		$message = $params['message'];
		$link = $params['link'];
		$uid = $params['uid'];
		$to = $params['to'];
		$access_token = $params['access_token'];

		$api = new YNSOCIALBRIDGE_CLASS_FacebookChat();
		$options = array(
			'uid' => $uid,
			'app_id' => $this -> _objPlugin -> getAppId(),
			'server' => 'chat.facebook.com',
		);
		$connectResult = $api -> xmpp_connect($options, $access_token);

		if (!$connectResult)
		{
			echo 'connect failed';
		}

		$sMessage = $message . ' ' . $link;
		$sendMessageResult = $api -> xmpp_message($to = $to, $body = $sMessage);

		if (!$sendMessageResult)
		{
			echo 'send message failed due to some error, will be checked later';
		}
		else
		{
			echo $sendMessageResult;
		}
	}

	/**
	 * Send invites
	 *
	 * @param array (list array, message string, link string, uid int, access_token string)
	 * @return bool
	 */
	public function sendInvites($params = array())
	{
		$list = $params['list'];
		$message = $params['message'];
		$link = $params['link'];
		$uid = $params['uid'];
		$user_id = $params['user_id'];

		if (isset($list))
		{
			$api = new YNSOCIALBRIDGE_CLASS_FacebookChat();
			$options = array(
				'uid' => $uid,
				'app_id' => $this -> _objPlugin -> getAppId(),
				'server' => 'chat.facebook.com',
			);
			$access_token = $params['access_token'];
			$connectResult = $api -> xmpp_connect($options, $access_token);
			if (!$connectResult)
			{
				echo 'connect failed';
			}

			$sMessage = $message . ' ' . $link;

			//get max invite per day
			$max_invite = 20;
			$clientConfig = YNSOCIALBRIDGE_BOL_ApisettingService::getInstance() -> getConfig($this -> _plugin);
			if ($clientConfig)
			{
				$api_params = unserialize($clientConfig -> apiParams);
				if ($api_params['max_invite_day'])
				{
					$max_invite = $api_params['max_invite_day'];
				}
			}

			$count_invite = 0;

			$values = array(
				'userId' => $user_id,
				'uid' => $uid,
				'service' => $this -> _plugin,
				'date' => date('Y-m-d')
			);

			$total_invited = $this -> getTotalInviteOfDay($values);
			$count_invite_succ = $total_invited;
			$count_invite = $total_invited;

			$count_queues = 0;
			$arr_user_queues = array();

			foreach ($list as $key => $user)
			{
				try
				{
					if ($count_invite < $max_invite)
					{
						$sendMessageResult = $api -> xmpp_message($to = $key, $body = $sMessage);
						if (!$sendMessageResult)
						{
							$count_queues++;
							$arr_user_queues[$key] = $user;
						}
						else
						{
							$count_invite_succ++;
						}
					}
					else
					{
						$count_queues++;
						$arr_user_queues[$key] = $user;
					}
					$count_invite++;
				}
				catch(Exception $e)
				{
					throw $e;
				}
			}
			//save statistics
			$values = array(
				'userId' => $user_id,
				'uid' => $uid,
				'service' => $this -> _plugin,
				'inviteOfDay' => $count_invite_succ,
				'date' => date('Y-m-d')
			);
			$this -> createOrUpdateStatistic($values);
			// Save queues
			if ($count_queues > 0)
			{
				$values = array(
					'uid' => $uid,
					'service' => $this ->_plugin,
					'userId' => $user_id
				);
				$token = $this -> getToken($values);
				if ($token)
				{
					$extra_params['list'] = $arr_user_queues;
					$extra_params['link'] = $link;
					$extra_params['message'] = $message;
					$values = array(
						'tokenId' => $token -> id,
						'userId' => $user_id,
						'service' => $this -> _plugin,
						'type' => 'sendInvite',
						'extraParams' => serialize($extra_params),
						'lastRun' => time(),
						'status' => 0,
					);
					$this -> saveQueues($values);
				}
			}
			return true;
		}
		return false;
	}

		/**
	 * Post activity
	 *
	 * @param array
	 * @return bool
	 */
	public function postActivity($params = array())
	{
		if($this->_objPlugin)
		{
			try
			{
			    $this->_objPlugin->setAccessToken($params['access_token']);

				$postParam = array(
					'message' => $params['message'],
				    'description' => $params['description']
				);
				if (!empty($params['link'])) {
				    $postParam['link'] = $params['link'];
				}
				// link title
				if (isset($params['name'])) {
				    $postParam['name'] = $params['name'];
				}
				// link caption
				if (isset($params['caption'])) {
				    $postParam['caption'] = $params['caption'];
				}
                // picture url
				if (!empty($params['picture']))
				{
					$postParam['picture'] = $params['picture'];
				}

				 $this->_objPlugin -> api('/me/feed', 'POST', $postParam);
				 return true;
			}
			catch (exception $ex)
			{
				$aResponse['error'] = $ex -> getMessage();
				$aResponse['apipublisher'] = 'facebook';
				return $aResponse;
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
		if ($this -> _objPlugin == null)
		{
			return false;
		}
		$token = $this -> _objPlugin -> getAccessToken();
		if ($token)
		{
			$me = $this -> getOwnerInfo();
			if ($me)
				return true;
		}
		return false;
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
	 * @param array ()
	 * @return string
	 */
	public function getLoginUrl($params = array())
	{
		if ($this -> _objPlugin)
		{
			return $this -> _objPlugin -> getLoginUrl($params);
		}
	}

	/**
	 * Get logout
	 *
	 * @return string
	 */
	public function getLogoutUrl($params = array())
	{
		if ($this -> _objPlugin && $this -> _objPlugin -> getAccessToken())
		{
			return $this -> _objPlugin -> getLogoutUrl();
		}
	}

	/**
	 * Has permission
	 *@param array(uid, access_token)
	 * @return array
	 */
	public function hasPermission($params = array())
	{
		try
		{
			$permissions = $this -> _objPlugin -> api('/' . $params['uid'] . '/permissions?access_token=' . $params['access_token']);
			if ($permissions)
				return $permissions['data'];
			else
			{
				return null;
			}
		}
		catch(Exception $ex)
		{
			return null;
		}
	}

	/**
	 *
	 * @param $query
	 * @return array of photos
	 */
	public function facebookQuery($param = array())
	{
		$covers = $this -> _objPlugin -> api(array(
			'method' => 'fql.query',
			'query' => $param['query']
		));
		return $covers;
	}
	/**
	 * getUserAccessToken
	 * 
	 */
	 public function getUserAccessToken()
	 {
	 	if($this->_objPlugin)
	 		return $this->_objPlugin->getUserAccessToken();
	 }
	 /**
	  * setAccessToken
	  * 
	  */
	 public function setAccessToken($token)
	 {
	 	if($this->_objPlugin)
			$this->_objPlugin->setAccessToken($token);
	 }
}
