<?php
class YNCONTACTIMPORTER_BOL_InvitationDao extends OW_BaseDao
{
	/**
	 * Class constructor
	 *
	 */
	protected function __construct()
	{
		parent::__construct();
	}

	/**
	 * Class instance
	 *
	 * @var YNCONTACTIMPORTER_BOL_InvitationDao
	 */
	private static $classInstance;

	/**
	 * Returns class instance
	 *
	 * @return YNCONTACTIMPORTER_BOL_InvitationDao
	 */
	public static function getInstance()
	{
		if (self::$classInstance === null)
		{
			self::$classInstance = new self();
		}

		return self::$classInstance;
	}

	/**
	 * @see OW_BaseDao::getDtoClassName()
	 *
	 */
	public function getDtoClassName()
	{
		return 'YNCONTACTIMPORTER_BOL_Invitation';
	}

	/**
	 * @see OW_BaseDao::getTableName()
	 *
	 */
	public function getTableName()
	{
		return OW_DB_PREFIX . 'yncontactimporter_invitation';
	}
	/**
	 * get All invitations with userId
	 * 
	 * @param OW_Example
	 * 
	 * @return array YNCONTACTIMPORTER_BOL_Invitation
	 */
	 public function getInvitationByUserId($params = array())
	 {
		$example = new OW_Example();
		if (isset($params['count']) && $params['count'])
		{
			$example -> setLimitClause($params['first'], $params['count']);
		}
		
		if(isset($params['userId']))
		{
			$example->andFieldEqual('userId', $params['userId']);
		}
		
		if(isset($params['provider']))
		{
			$example->andFieldEqual('provider', $params['provider']);
		}
		else 
		{
			$example->andFieldEqual('isUsed', 0);
		}
		if(isset($params['userId']) && empty($params['provider']))
		{
			// check queues
			$emails = YNCONTACTIMPORTER_BOL_PendingService::getInstance()->getAllPendingEmailsByUserId(array('userId' => $params['userId']));
			$socials = YNSOCIALBRIDGE_BOL_QueueService::getInstance()->getQueuesByUserId(array('userId' => $params['userId'], 'type' => 'sendInvite'));
			$friendIds = array();
			foreach ($emails as $email) 
			{
				$friendIds[] = $email['recipientEmail'];
			}
			foreach ($socials as $social) 
			{
				$arr_id = $arr = explode('/', $social['id']);
				$friendIds[] = $arr_id[0];
			}
			if($friendIds)
				$example -> andFieldNotInArray('friendId', $friendIds);
		}
		
		if(isset($params['search']) && $params['search'])
		{
			$example->andFieldLike('friendId', "%".$params['search']."%");
		}
		$example -> setOrder("`sentTime` DESC");
		return $this->findListByExample($example);
	 }
	 /**
	 * add invitations with userId
	 * 
	 * @param OW_Example
	 * 
	 * @return bool
	 */
	 public function addInvitations($userId = 0, $type = 'email', $provider = 'facebook', $inviteList = array(), $message)
	 {
	 	foreach ($inviteList as $key => $value) 
	 	{
			$invitation = new YNCONTACTIMPORTER_BOL_Invitation();
			$invitation->userId = $userId;
			$invitation->type = $type;
			$invitation->provider = $provider;
			$invitation->friendId = $key;
			$invitation -> email = $value;
			$invitation -> sentTime = time();
			$invitation -> message = $message;
			$invitation -> isUsed = 0;
			$this -> save($invitation);
		}
	 }
	 
	 /**
	 * find invitation by friendId
	 *
	 * @param $friendId
	 * @return YNCONTACTIMPORTER_BOL_Invitation
	 */
	 public function findByFriendId($friendId)
	 {
	 	$example = new OW_Example();
		if($friendId)
		{
			$example->andFieldEqual('friendId', $friendId);
		}
		
		return $this->findObjectByExample($example);
	 }
}
