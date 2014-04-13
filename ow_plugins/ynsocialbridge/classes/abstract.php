<?php
	ini_set('gd.jpeg_ignore_warning', 1);

	ini_set('display_startup_errors', 1);

	ini_set('display_errors', 1);
abstract class YNSOCIALBRIDGE_CLASS_Abstract
{

	protected $_objPlugin = null;
	protected $_plugin = '';

	/**
	 * Get plugin
	 *
	 * @return
	 */
	abstract public function getPlugin($params = array());
	
	/**
	 * Save Token
	 *
	 * @return
	 */
	abstract public function saveToken($params = array());

	/**
	 * Get contact list
	 *
	 * @return array (id, pic, name)
	 */
	abstract public function getContacts($params = array());

	/**
	 * Get owner info
	 *
	 * @return array (id, name, first_name, last_name, link,...)
	 */
	abstract public function getOwnerInfo($params = array());

	/**
	 * Get owner id
	 *
	 * @return string
	 */
	abstract public function getOwnerId($params = array());

	/**
	 * Get user info
	 *
	 * @return array
	 */
	abstract public function getUserInfo($params = array());

	/**
	 * Get users info
	 *
	 * @return array
	 */
	abstract public function getUsersInfo($params = array());

	/**
	 * Get albums
	 *
	 * @return array
	 */
	abstract public function getAlbums($params = array());

	/**
	 * Get album info
	 *
	 * @return array
	 */
	abstract public function getAlbumInfo($params = array());

	/**
	 * Get photos
	 *
	 * @return array
	 */
	abstract public function getPhotos($params = array());

	/**
	 * Get photo info
	 *
	 * @return array
	 */
	abstract public function getPhotoInfo($params = array());

	/**
	 * Get activity
	 *
	 * @param array(lastFeedTimestamp, limit)
	 * @return array (post_id,actor_id,target_id,message,description,created_time,attachment,permalink,description_tags,type)
	 */
	abstract public function getActivity($params = array());

	/**
	 * Insert feeds
	 *
	 * @param array(activities, viewer, timestamp)
	 * @return bool
	 */
	abstract public function insertFeeds($params = array());

	/**
	 * Send invite
	 *
	 * @param array (to string, message string, link string, uid int, access_token string)
	 * @return bool
	 */
	abstract public function sendInvite($params = array());

	/**
	 * Send invites
	 *
	 * @param array (list array, message string, link string, uid int, access_token string)
	 * @return bool
	 */
	abstract public function sendInvites($params = array());

	/**
	 * Post activity
	 *
	 * @param array
	 * @return bool
	 */
	abstract public function postActivity($params = array());

	/**
	 * Get login status
	 *
	 * @param array
	 * @return string
	 */
	abstract public function getLoginStatus($params = array());

	/**
	 * Get throttle limit
	 *
	 * @param array
	 * @return integer
	 */
	abstract public function getThrottleLimit($params = array());

	/**
	 * Get login URL
	 *
	 * @param array
	 * @return string
	 */
	abstract public function getLoginUrl($params = array());

	/**
	 * Get logout
	 *
	 * @return string
	 */
	abstract public function getLogoutUrl($params = array());
	
	/**
	 * Get login URL
	 *
	 * @param array ()
	 * @return string
	 */
	public function getConnectUrl($params = array())
	{
		if ($this -> _objPlugin)
		{
			return OW::getRouter() -> urlForRoute('ynsocialbridge-connect-'.$this->_plugin);
		}
	}

	/**
	 * Has permission
	 *
	 * @return bool
	 */
	abstract public function hasPermission($params = array());
	/*
	 * get Token
	 *
	 */
	public function getToken($params = array())
	{
		return YNSOCIALBRIDGE_BOL_TokenService::getInstance()->findUserToken($params);
	}

	/*
	 * get Total invite of day
	 *
	 */
	public function getTotalInviteOfDay($params = array())
	{
		$statistic = YNSOCIALBRIDGE_BOL_StatisticService::getInstance()->getTotalInviteOfDay($params);
		if ($statistic)
			return $statistic -> inviteOfDay;
		else
			return 0;
	}

	/*
	 * create or update statistics
	 *
	 */
	public function createOrUpdateStatistic($params = array())
	{
		$statistic = YNSOCIALBRIDGE_BOL_StatisticService::getInstance()->getTotalInviteOfDay($params);
		if ($statistic)
		{
			$statistic -> inviteOfDay = $params['inviteOfDay'];
		}
		else
		{
			$statistic = new YNSOCIALBRIDGE_BOL_Statistic();
			$statistic->service = $params['service'];
			$statistic->userId = $params['userId'];
			$statistic->uid = $params['uid'];
			$statistic->inviteOfDay = $params['inviteOfDay'];
			$statistic->date = $params['date'];
		}
		YNSOCIALBRIDGE_BOL_StatisticService::getInstance()->save($statistic);
	}

	/*
	 * save queues
	 *
	 */
	public function saveQueues($params = array())
	{
		$queue = new YNSOCIALBRIDGE_BOL_Queue();
		$queue->service = $params['service'];
		$queue->tokenId = $params['tokenId'];
		$queue->userId = $params['userId'];
		$queue->type = $params['type'];
		$queue->extraParams = $params['extraParams'];
		$queue->lastRun = $params['lastRun'];
		$queue->status = $params['status'];
		YNSOCIALBRIDGE_BOL_QueueService::getInstance()->save($queue);
	}

	/*
	 * get Queue
	 *
	 */
	public function getQueue($params)
	{
		return YNSOCIALBRIDGE_BOL_QueueService::getInstance()->getQueue($params);
	}

	//Add activitys
	public function addActivity($feed = array(), $viewer)
	{
		$service = $this -> _plugin;
	}

	public function _parseImage($uri, HttpResponse $response)
	{
		return $arr_result['uri'] = $uri;
	}

	public function _parseText($uri, HttpResponse $response)
	{
		$body = $response -> getBody();
		if (preg_match('/charset=([a-zA-Z0-9-_]+)/i', $response -> getHeader('content-type'), $matches) || preg_match('/charset=([a-zA-Z0-9-_]+)/i', $response -> getBody(), $matches))
		{
			$charset = trim($matches[1]);
		}
		else
		{
			$charset = 'UTF-8';
		}
		// Reduce whitespace
		$body = preg_replace('/[\n\r\t\v ]+/', ' ', $body);

		$arr_result = array();

		$arr_result['title'] = substr($body, 0, 63);
		$arr_result['description'] = substr($body, 0, 255);
		return $arr_result;
	}

	public function _parseHtml($uri, HttpResponse $response)
	{
		$body = $response -> getBody();
		$body = trim($body);
		// Get DOM
		if (class_exists('DOMDocument'))
		{
			$dom = new DOMXPath($body);
		}
		else
		{
			$dom = null;
			// Maybe add b/c later
		}

		$title = null;
		if ($dom)
		{
			$titleList = $dom -> query('title');
			if (count($titleList) > 0)
			{
				$title = trim($titleList -> current() -> textContent);
				$title = substr($title, 0, 255);
			}
		}
		$arr_result['title'] = $title;

		$description = null;
		if ($dom)
		{
			$descriptionList = $dom -> queryXpath("//meta[@name='description']");
			// Why are they using caps? -_-
			if (count($descriptionList) == 0)
			{
				$descriptionList = $dom -> queryXpath("//meta[@name='Description']");
			}
			if (count($descriptionList) > 0)
			{
				$description = trim($descriptionList -> current() -> getAttribute('content'));
				$description = substr($description, 0, 255);
			}
		}
		$arr_result['description'] = $description;

		$thumb = null;
		if ($dom)
		{
			$thumbList = $dom -> queryXpath("//link[@rel='image_src']");
			if (count($thumbList) > 0)
			{
				$thumb = $thumbList -> current() -> getAttribute('href');
			}
		}

		$medium = null;
		if ($dom)
		{
			$mediumList = $dom -> queryXpath("//meta[@name='medium']");
			if (count($mediumList) > 0)
			{
				$medium = $mediumList -> current() -> getAttribute('content');
			}
		}

		// Get baseUrl and baseHref to parse . paths
		$baseUrlInfo = parse_url($uri);
		$baseUrl = null;
		$baseHostUrl = null;
		if ($dom)
		{
			$baseUrlList = $dom -> query('base');
			if ($baseUrlList && count($baseUrlList) > 0 && $baseUrlList -> current() -> getAttribute('href'))
			{
				$baseUrl = $baseUrlList -> current() -> getAttribute('href');
				$baseUrlInfo = parse_url($baseUrl);
				$baseHostUrl = $baseUrlInfo['scheme'] . '://' . $baseUrlInfo['host'] . '/';
			}
		}
		if (!$baseUrl)
		{
			$baseHostUrl = $baseUrlInfo['scheme'] . '://' . $baseUrlInfo['host'] . '/';
			if (empty($baseUrlInfo['path']))
			{
				$baseUrl = $baseHostUrl;
			}
			else
			{
				$baseUrl = explode('/', $baseUrlInfo['path']);
				array_pop($baseUrl);
				$baseUrl = join('/', $baseUrl);
				$baseUrl = trim($baseUrl, '/');
				$baseUrl = $baseUrlInfo['scheme'] . '://' . $baseUrlInfo['host'] . '/' . $baseUrl . '/';
			}
		}

		$images = array();
		if ($thumb)
		{
			$images[] = $thumb;
		}
		if ($dom)
		{
			$imageQuery = $dom -> query('img');
			foreach ($imageQuery as $image)
			{
				$src = $image -> getAttribute('src');
				// Ignore images that don't have a src
				if (!$src || false === ($srcInfo = @parse_url($src)))
				{
					continue;
				}
				$ext = ltrim(strrchr($src, '.'), '.');
				// Detect absolute url
				if (strpos($src, '/') === 0)
				{
					// If relative to root, add host
					$src = $baseHostUrl . ltrim($src, '/');
				}
				else
				if (strpos($src, './') === 0)
				{
					// If relative to current path, add baseUrl
					$src = $baseUrl . substr($src, 2);
				}
				else
				if (!empty($srcInfo['scheme']) && !empty($srcInfo['host']))
				{
					// Contians host and scheme, do nothing
				}
				else
				if (empty($srcInfo['scheme']) && empty($srcInfo['host']))
				{
					// if not contains scheme or host, add base
					$src = $baseUrl . ltrim($src, '/');
				}
				else
				if (empty($srcInfo['scheme']) && !empty($srcInfo['host']))
				{
					// if contains host, but not scheme, add scheme?
					$src = $baseUrlInfo['scheme'] . ltrim($src, '/');
				}
				else
				{
					// Just add base
					$src = $baseUrl . ltrim($src, '/');
				}
				// Ignore images that don't come from the same domain
				//if( strpos($src, $srcInfo['host']) === false ) {
				// @todo should we do this? disabled for now
				//continue;
				//}
				// Ignore images that don't end in an image extension
				if (!in_array($ext, array(
					'jpg',
					'jpeg',
					'gif',
					'png'
				)))
				{
					// @todo should we do this? disabled for now
					//continue;
				}
				if (!in_array($src, $images))
				{
					$images[] = $src;
				}
			}
		}

		// Unique
		$images = array_values(array_unique($images));

		// Truncate if greater than 20
		if (count($images) > 30)
		{
			array_splice($images, 30, count($images));
		}

		$imageCount = count($images);
		if ($imageCount > 0)
			$arr_result['uri'] = $images[0];
		else
			$arr_result['uri'] = '';
		return $arr_result;
	}

}
