<?php
class YNSOCIALSTREAM_BOL_SocialstreamFeedService
{
	/*
	 * @var YNSOCIALSTREAM_BOL_SocialstreamFeedService
	 */
	private static $classInstance;

	/*
	@var YNSOCIALSTREAM_BOL_SocialstreamFeedService
	*/
	private $providerDao;

	private function __construct()
	{
		$this -> feedsDao = YNSOCIALSTREAM_BOL_SocialstreamFeedDao::getInstance();
	}

	/**
	 * Returns class instance
	 *
	 * @return YNCONTACTIMPORTER_BOL_ProviderService
	 */
	public static function getInstance()
	{
		if (!isset(self::$classInstance))
			self::$classInstance = new self();
		return self::$classInstance;
	}

	/**
	 * @var YNSOCIALSTREAM_BOL_SocialstreamFeedService
	 *
	 */
	public function saveFeed(YNSOCIALSTREAM_BOL_SocialstreamFeed $feedDto)
	{		
		$this -> feedsDao -> save($feedDto);
	}
	public function getFeedLast($provider, $uid)
	{		
		return $this->feedsDao->getStreamFeedLast($provider, $uid);
	}
	public function parseFeedArray($values = array())
	{
		
		$feedDto = new YNSOCIALSTREAM_BOL_SocialstreamFeed();
		$feedDto->uid = $values['uid'];
		$feedDto->userId = $values['user_id'];
		$feedDto->provider = $values['provider'];
		$feedDto->timestamp = $values['timestamp'];
		$feedDto->updateKey = $values['update_key'];
		$feedDto->updateType = $values['update_type'];
		$feedDto->creationDate = date('Y-m-d H:i:s');
		$feedDto->modifiedDate = date('Y-m-d H:i:s');
		$feedDto->photoUrl = $values['photo_url'];
		$feedDto->title = $values['title'];
		$feedDto->href = $values['href'];
		$feedDto->description = $values['description'];
		$feedDto->friendId = $values['friend_id'];
		$feedDto->friendName = $values['friend_name'];
		$feedDto->friendHref = $values['friend_href'];
		$feedDto->friendDescription = $values['friend_description'];
		$feedDto->privacy = $values['privacy'];			
		return $feedDto;
	}
}
