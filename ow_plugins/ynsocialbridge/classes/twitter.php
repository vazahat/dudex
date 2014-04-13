<?php
require_once OW::getPluginManager() -> getPlugin('ynsocialbridge') -> getRootDir() . 'libs/twitter.php';

class YNSOCIALBRIDGE_CLASS_Twitter extends YNSOCIALBRIDGE_CLASS_Abstract
{
	/**
	 * Twitter instance
	 * @var	Twitter
	 */
	protected $_plugin = 'twitter';
	const SHORT_LENGTH_URL = 22;

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
		$clientConfig = YNSOCIALBRIDGE_BOL_ApisettingService::getInstance() -> getConfig($this -> _plugin);
		if ($clientConfig == null)
		{
			return false;
		}
		$api_params = unserialize($clientConfig -> apiParams);
		$this -> _objPlugin = new Twitter($api_params['key'], $api_params['secret']);
	}

	/*
	 * Returns an array contains every user following the specified user.
	 * @param  string $params['oauth_token'] The token to use.
	 * @param  string $params['oauth_token_secret'] The token secret to use.
	 * @param  string $param['user_id'] User id
	 * @return array(id, name, pic)
	 */
	public function getContacts($params = array())
	{
		if (empty($params['user_id']))
		{
			throw new Exception('Specify user id.');
		}

		try
		{
			$this -> authorize($params['access_token'], $params['secret_token']);

			$friends = $this -> _objPlugin -> followersIds($params['user_id']);

			$return_data = array();
			// get friends info
			if (count($friends['ids']) > 0)
			{
				foreach ($friends['ids'] as $key => $value)
				{
					$users_info = $this -> _objPlugin -> usersLookup($value);
					$return_data[$users_info[0]['id']] = array(
						'id' => $users_info[0]['id'],
						'name' => $users_info[0]['name'],
						'pic' => $users_info[0]['profile_image_url']
					);
				}
			}
		}
		catch (Exception $e)
		{
			echo $e -> getMessage();
		}
		return $return_data;
	}

	/*
	 * Returns user object as values passed to the identity (user_id and/or user_name) parameter.
	 * @param  string $params['oauth_token'] The token to use.
	 * @param  string $params['oauth_token_secret'] The token secret to use.
	 * @param  string[optional] $param['user_id'] User id
	 * @param  string[optional] $param['user_name'] User name
	 * @return array
	 */
	public function getUserInfo($params = array())
	{
		$user_name = @(string)$params['user_name'];
		$user_id = @(string)$params['user_id'];
		$response = array();
		if (empty($user_id) && empty($user_name))
		{
			throw new Exception('Specify an user id or an user name.');
		}

		try
		{
			$this -> authorize(@$params['access_token'], @$params['secret_token']);
			if (!empty($user_id))
			{
				$response = $this -> _objPlugin -> usersShow($user_id);
			}
			else
			{
				$response = $this -> _objPlugin -> usersShow(null, $user_name);
			}
		}
		catch (Exception $e)
		{
			echo $e -> getMessage();
		}
		return $response;
	}

	/*
	 * Returns user objects for up to 100 users per request, as specified by comma-separated values passed to the user_id
	 * and/or user_name parameters.
	 * @param  string $params['oauth_token'] The token to use.
	 * @param  string $params['oauth_token_secret'] The token secret to use.
	 * @param  mixed[optional] $param['user_ids'] An array of user IDs, up to 100 are allowed in a single request.
	 * @param  mixed[optional] $param['user_names'] An array of user names, up to 100 are allowed in a single request.
	 * @return array
	 */
	public function getUsersInfo($params = array())
	{
		$user_ids = $params['user_ids'];
		$user_names = $params['user_names'];

		if (empty($user_ids) && empty($user_names))
		{
			throw new Exception('Specify user ids or an user names.');
		}

		try
		{
			$this -> authorize($params['access_token'], $params['secret_token']);
			$response = $this -> _objPlugin -> usersLookup($user_ids, $user_names);
		}
		catch (Exception $e)
		{
			echo $e -> getMessage();
		}
		return $response;
	}

	/**
	 * Sends a new direct message to the specified user from the authenticating user.
	 * @param  string $params['oauth_token'] The token to use.
	 * @param  string $params['oauth_token_secret'] The token secret to use.
	 * @param string[optional] $params['user_id']  The ID of the user who should receive the direct message.
	 * @param string[optional] $params['user_name'] The user name of the user who should receive the direct message.
	 * @param string $text The text of your direct message. Be sure to URL encode as necessary, and keep the message under
	 * 140 characters.
	 */
	public function sendInvite($params = array())
	{

		if (empty($params['message']))
		{
			throw new Exception('Specify message.');
		}

		if (empty($params['link']))
		{
			throw new Exception('Specify link.');
		}

		if (empty($params['user_id']) && empty($params['user_name']))
		{
			throw new Exception('Specify an user id or an user name.');
		}

		try
		{
			$this -> authorize($params['access_token'], $params['secret_token']);
			$message = $params['message'];
			$link = $params['link'];
			$message = strip_tags($message);
			if (!empty($link))
			{
				$message = substr($message, 0, 140 - strlen($link)) . "\n\r" . $link;
			}
			if (!isset($params['user_name']))
			{
				$params['user_name'] = null;
			}

			$this -> _objPlugin -> directMessagesNew(trim($params['user_id']), $params['user_name'], $message);
		}
		catch (Exception $e)
		{
			echo $e -> getMessage();
		}
	}

	/**
	 * @param array (list array, message string, link string, uid int, access_token string)
	 * Sends a new direct message to many users from the authenticating user.
	 * @param  string $params['oauth_token'] The token to use.
	 * @param  string $params['oauth_token_secret'] The token secret to use.
	 * @param  array $param['list'] An array of user IDs, up to 100 are allowed in a single request.
	 * @param  string message The text of your direct message.
	 * @param  string $param['link'] The link to add to your messge
	 */
	public function sendInvites($params = array())
	{
		if (empty($params['message']))
		{
			throw new Exception('Specify message.');
		}
		if (empty($params['list']))
		{
			throw new Exception('Specify user ids.');
		}
		if (empty($params['link']))
		{
			throw new Exception('Specify link.');
		}

		try
		{
			$user_id = $params['user_id'];
			$uid = $params['uid'];
			//get max invite per day
			$max_invite = 250;
			$service = 'twitter';
			$clientConfig = YNSOCIALBRIDGE_BOL_ApisettingService::getInstance() -> getConfig($this ->_plugin);
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
				'service' => $service,
				'date' => date('Y-m-d')
			);
			$total_invited = $this -> getTotalInviteOfDay($values);
			$count_invite_succ = $total_invited;
			$count_invite = $total_invited;

			$count_queues = 0;
			$arr_user_queues = array();
			// send message to user ids
			foreach ($params['list'] as $key => $name)
			{
				if ($count_invite < $max_invite)
				{
					$params['user_id'] = $key;
					$this -> sendInvite($params);
					$count_invite_succ++;
				}
				else
				{
					$count_queues++;
					$arr_user_queues[$key] = $name;
				}
				$count_invite++;
			}

			//save statistics
			$values = array(
				'userId' => $user_id,
				'uid' => $uid,
				'service' => $service,
				'inviteOfDay' => $count_invite_succ,
				'date' => date('Y-m-d')
			);
			$this -> createOrUpdateStatistic($values);

			// Save queues
			if ($count_queues > 0)
			{
				$values = array(
					'uid' => $uid,
					'service' => $service,
					'userId' => $user_id
				);
				$token = $this -> getToken($values);
				if ($token)
				{
					$extra_params['list'] = $arr_user_queues;
					$extra_params['link'] = $params['link'];
					$extra_params['message'] = $params['message'];
					$values = array(
						'tokenId' => $token -> id,
						'userId' => $user_id,
						'service' => $service,
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
		catch (Exception $e)
		{
			echo $e -> getMessage();
			return false;
		}
	}

	/*
	 * Returns authenticating user object
	 * @see YNSOCIALBRIDGE_CLASS_Abstract::getOwnerInfo()
	 */
	public function getOwnerInfo($params = array())
	{
		if (isset($params['access_token']))
			$this -> authorize($params['access_token'], $params['secret_token']);
		if (empty($_SESSION['socialbridge_session']['twitter']['owner_id']) && !isset($params['user_id']))
		{
			throw new Exception('Can not get owner information.');
		}
		if (isset($_SESSION['socialbridge_session']['twitter']['owner_id']))
			$params['user_id'] = $_SESSION['socialbridge_session']['twitter']['owner_id'];
		$me = $this -> getUserInfo($params);
		$me['identity'] = @$me['id'];
		$me['displayname'] = @$me['name'];
		if (isset($me['name']) && !isset($me['first_name']))
		{
			$arr_name = explode(" ", $me['name']);
			$me['first_name'] = @$arr_name[0];
			$me['last_name'] = @$arr_name[1];
		}
		$me['username'] = @$me['screen_name'];
		$me['website'] = @$me['entities']['url']['urls'][0]['display_url'];
		$me['twitter'] = @$me['screen_name'];
		$me['service'] = 'twitter';

		$me['about_me'] = @$me['description'];
		$me['about'] = @$me['description'];
		$me['picture'] = @$me['profile_image_url'];
		$me['photo_url'] = str_replace('normal', 'bigger', @$me['profile_image_url']);
		return $me;
	}

	/*
	 * Returns authenticating user id
	 * @see YNSOCIALBRIDGE_CLASS_Abstract::getOwnerId()
	 */
	public function getOwnerId($params = array())
	{
		if (!empty($_SESSION['socialbridge_session']['twitter']['owner_id']))
		{
			return $_SESSION['socialbridge_session']['twitter']['owner_id'];
		}
		return null;
	}

	/*
	 * authorize Twitter using token and secret token
	 * @param  string $token[optional] The token to use.
	 * @param  string $secret_token[optional] The token secret to use.
	 */
	public function authorize($token = null, $secret_token = null)
	{
		if (!$token && isset($_SESSION['twitter']['oauth_token']))
		{
			$token = @$_SESSION['twitter']['oauth_token'];
		}
		if (!$secret_token && isset($_SESSION['twitter']['oauth_token_secret']))
		{
			$secret_token = @$_SESSION['twitter']['oauth_token_secret'];
		}
		if (empty($token) || empty($secret_token))
		{
			throw new Exception('Specify token and secret token parameters');
		}

		$this -> _objPlugin -> setOAuthToken($token);
		$this -> _objPlugin -> setOAuthTokenSecret($secret_token);
	}

	/*
	 * (non-PHPdoc) @see YNSOCIALBRIDGE_CLASS_Abstract::getActivity()
	 * Returns a collection of the Tweets posted by the authenticating user.
	 * @param  string $params['oauth_token'] The token to use.
	 * @param  string $params['oauth_token_secret'] The token secret to use.
	 * @param int[optional] $params['count'] Specifies the number of records to retrieve. Must be less than or equal to 200.
	 * Defaults to 20.
	 * @param type[optional] $params['type'] Specifies Tweets from user profile or homepage. Defaults to user profile.
	 * @return array
	 */
	public function getActivity($params = array())
	{
		$result = array();
		try
		{
			$this -> authorize($params['access_token'], $params['secret_token']);
			if (!isset($params['limit']))
			{
				$params['limit'] = 5;
			}

			if (isset($params['type']) && $params['type'] == 'user')
			{
				$result = $this -> _objPlugin -> statusesUserTimeline($params['uid'], null, null, null);
			}
			else
			{
				$result = $this -> _objPlugin -> statusesHomeTimeline();
			}

			if ($params['lastFeedTimestamp'])
			{
				$temp = array();
				foreach ($result as $feed)
				{
					if (strtotime($feed['created_at']) > $params['lastFeedTimestamp'])
					{
						$temp[] = $feed;
					}
				}
				$result = $temp;
				krsort($result);
			}
			if ($params['limit'])
			{
				$result = array_slice($result, 0, $params['limit']);
			}
			if (!$params['lastFeedTimestamp'])
			{
				krsort($result);
			}
		}
		catch (Exception $e)
		{
			echo $e -> getMessage();
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
		$this -> authorize($params['access_token'], $params['secret_token']);		
		$user_id = $params['user_id'];
		$uid = $params['uid'];
		$activities = $params['activities'];
		
		//init Social stream feeds service		
		$streamFeedService = YNSOCIALSTREAM_BOL_SocialstreamFeedService::getInstance();
		$now =  time();
		foreach ($activities as $activity)
		{
			if (strtotime($activity['created_at']) > $params['timestamp'])
			{				
				try
				{
					//parse friend_description
					 $friend_description = (string)$activity['text'];
					 $regex = '@((https?://)?([-\w]+\.[-\w\.]+)+\w(:\d+)?(/([-\w/_\.]*(\?\S+)?)?)*)@';
					 $friend_description = preg_replace($regex, '<a href="$1">$1</a>', $friend_description);
					 $values = array();
					 $values = array_merge(array(
					 'provider' 		=> 'twitter',
					 'user_id'		=> $user_id,
					 'uid'			=> $uid,
					 'timestamp' 	=> strtotime($activity['created_at']),
					 'update_key' 	=> $activity['id'],
					 'update_type' 	=> 'STATUS',
					 'photo_url' 	=> '',
					 'title' 		=> '',
					 'href'			=> '',
					 'description'	=> '',
					 'friend_id'		=> $activity['user']['id'],
					 'friend_name'	=> $activity['user']['name'],
					 'friend_href'	=> 'https://twitter.com/'.$activity['user']['screen_name'],
					 'friend_description'	=> strip_tags($friend_description),
					 ));
					 
					// save feed
					$streamFeedDto = $streamFeedService->parseFeedArray($values);
					$streamFeedService->saveFeed($streamFeedDto);	
					
					//attachment todo
					$oembed = null;
					$url = null;
					$attachId = null;			
					$feed = "gets <a href=\"".$values['friend_href']."\" target=\"_blank\">".$values['friend_name']."'s</a>  feed from Twitter. <span class=\"feed_item_bodytext\">".$values['friend_description']."</span>";					
					//$feed = $language->text('ynsocialstream', 'gets ')."<a href=\"".$values['friend_href']."\" target=\"_blank\">".$values['friend_name']."'s</a>".$language->text('ynsocialstream', '  feed from Facebook.')."<span class=\"feed_item_bodytext\">".$values['friend_description']."</span>[ph:attachment]";
					//get privacy
					$privacy = 'everybody';
					$configs = OW::getConfig()->getValues('ynsocialstream');  
					if(isset($configs['auth_tw_'.$user_id]))
			        {
			        	$privacy = $configs['auth_tw_'.$user_id] ; 
			        }	
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
			        OW::getEventManager()->trigger($event);			
				}
				catch( Exception $e)
				{
					throw new InvalidArgumentException('Can not insert feed from Twitter in social stream feed');
					exit;
				}				
			}
		}
	}

	/*
	 * @see YNSOCIALBRIDGE_CLASS_Abstract::postActivity()
	 * Updates the authenticating user's status.
	 * @param string $params['access_token'] The token to use.
	 * @param string $params['secret_token'] The token secret to use.
	 * @param string $params['status'] The text of your status update, typically up to 140 characters.
	 * @param string[optional] $params['tweet_id'] The ID of an existing status that the update is in reply to. Note: This
	 * parameter will be ignored unless the author of the tweet this parameter references is mentioned within the status
	 * text. Therefore, you must include @username, where username is the author of the referenced tweet, within the update.
	 * @return array
	 */
	public function postActivity($params = array())
	{
		 if($this->_objPlugin)
	    {
    	    try {
    	        $this->authorize($params['access_token'], $params['secret_token']);

    	        if (empty($params['message'])) {
                    //throw new Exception('Specify message.');
    	        }
    	        $message = $params['message'];
    	        $message = strip_tags($message);
    	        if (!empty($params['link'])) {
    	            $link = $params['link'];
    	            $message = substr($message, 0, 140 - YNSOCIALBRIDGE_CLASS_Twitter::SHORT_LENGTH_URL) . " " . $link;
    	        }

    	        $reply_to_status_id = isset($params['tweet_id'])?$params['tweet_id']:null;
    	        if ($reply_to_status_id) {
                    $author_name = $this->getStatusAuthorName($params);
                    $message = "@$author_name " . $message;
    	        }
                $this ->_objPlugin->statusesUpdate($message, $reply_to_status_id);
                return true;
    	    } catch (Exception $e) {
    	        return $e->getMessage();
    	    }
	    }
	    return false;
	}

	/**
	 * Returns a single Tweet, specified by the id parameter. The Tweet's author will also be embedded within the tweet.
	 * @param string $params['oauth_token_secret'] The token secret to use.
	 * @param string $params['status'] The text of your status update, typically up to 140 characters.
	 * @param  string $params['tweet_id'] The numerical ID of the desired Tweet.
	 * @return array
	 */
	public function getStatusAuthorName($params = array())
	{
		try
		{
			$this -> authorize($params['access_token'], $params['secret_token']);
			if (empty($params['tweet_id']))
			{
				throw new Exception('Specify tweet id');
			}
			$tweet_id = $params['tweet_id'];
			$respone = $this -> _objPlugin -> statusesShow($tweet_id);
			return $respone['user']['screen_name'];
		}
		catch (Exception $e)
		{
			echo $e -> getMessage();
		}
	}

	/*
	 * (non-PHPdoc) @see YNSOCIALBRIDGE_CLASS_Abstract::getAlbums()
	 */
	public function getAlbums($params = array())
	{
		// TODO Auto-generated method stub
	}

	/*
	 * (non-PHPdoc) @see YNSOCIALBRIDGE_CLASS_Abstract::getAlbumInfo()
	 */
	public function getAlbumInfo($params = array())
	{
		// TODO Auto-generated method stub
	}

	/*
	 * (non-PHPdoc) @see YNSOCIALBRIDGE_CLASS_Abstract::getPhotos()
	 */
	public function getPhotos($params = array())
	{
		// TODO Auto-generated method stub
	}

	/*
	 * (non-PHPdoc) @see YNSOCIALBRIDGE_CLASS_Abstract::getPhotoInfo()
	 */
	public function getPhotoInfo($params = array())
	{
		// TODO Auto-generated method stub
	}

	/*
	 * (non-PHPdoc) @see YNSOCIALBRIDGE_CLASS_Abstract::getLoginStatus()
	 */
	public function getLoginStatus($params = array())
	{
		// TODO Auto-generated method stub
	}

	/*
	 * @see YNSOCIALBRIDGE_CLASS_Abstract::getThrottleLimit()
	 * Returns the current configuration used by Twitter
	 */
	public function getThrottleLimit($params = array())
	{
		try
		{
			$this -> authorize($params['access_token'], $params['secret_token']);
			return $this -> _objPlugin -> helpConfiguration();
		}
		catch (Exception $e)
		{
			echo $e -> getMessage();
		}
	}

	/*
	 * get oauth_token value and redirect to the page to authorize the applicatione
	 */
	public function authorizeRequest($params = array())
	{
		// TODO Auto-generated method stub
		$response = $this -> _objPlugin -> oAuthRequestToken($params['url']);
		$this -> _objPlugin -> oAuthAuthorize($response['oauth_token']);
	}

	/*
	 * (non-PHPdoc) @see YNSOCIALBRIDGE_CLASS_Abstract::getLogoutUrl()
	 */
	public function getLogoutUrl($params = array())
	{
		// TODO Auto-generated method stub
	}

	/*
	 * (non-PHPdoc) @see YNSOCIALBRIDGE_CLASS_Abstract::hasPermission()
	 */
	public function hasPermission($params = array())
	{
		// TODO Auto-generated method stub
	}

	/*
	 *
	 *
	 */
	public function getAuthAccessToken($params = array())
	{
		return $this -> _objPlugin -> oAuthAccessToken($params['oauth_token'], $params['oauth_verifier']);
	}

	/* (non-PHPdoc)
	 * @see YNSOCIALBRIDGE_CLASS_Abstract::getLoginUrl()
	 */
	public function getLoginUrl($params = array())
	{
		// TODO Auto-generated method stub
	}
}
