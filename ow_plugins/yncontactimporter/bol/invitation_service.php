<?php
class YNCONTACTIMPORTER_BOL_InvitationService
{
	/*
	 * @var YNCONTACTIMPORTER_BOL_ProviderService
	 */
	private static $classInstance;

	/*
	@var YNCONTACTIMPORTER_BOL_InvitationDao
	*/
	private $invitationDao;

	/*
	@var YNCONTACTIMPORTER_BOL_StatisticDao
	*/
	private $statisticDao;
	
	/*
	@var YNCONTACTIMPORTER_BOL_JoinedDao
	*/
	private $joinedDao;
	
	private function __construct()
	{
		$this -> invitationDao = YNCONTACTIMPORTER_BOL_InvitationDao::getInstance();
		$this -> statisticDao = YNCONTACTIMPORTER_BOL_StatisticDao::getInstance();
		$this -> joinedDao = YNCONTACTIMPORTER_BOL_JoinedDao::getInstance();
	}
	
	/**
	 * Returns class instance
	 *
	 * @return YNCONTACTIMPORTER_BOL_InvitationService
	 */
	public static function getInstance()
	{
		if (!isset(self::$classInstance))
			self::$classInstance = new self();
		return self::$classInstance;
	}


	/**
	 * find All Invitations
	 *
	 * @param array $params
	 * @return array YNCONTACTIMPORTER_BOL_Invitation
	 */
	public function getInvitationsByUserId($params = array())
	{
		return $this -> invitationDao -> getInvitationByUserId($params);
	}
	
	/**
	 * count All Invitations
	 *
	 * @param array $params
	 * @return int
	 */
	public function countInvitationsByUserId($params = array())
	{
		$params['count'] = "";
		return count($this -> invitationDao -> getInvitationByUserId($params));
	}
	/**
	 * delete Invitation
	 *
	 * @param YNCONTACTIMPORTER_BOL_Invitation
	 * @return void
	 */
	public function delete(YNCONTACTIMPORTER_BOL_Invitation $invitationDto)
	{
		return $this -> invitationDao -> delete($invitationDto);
	}
	/**
	 * @var YNCONTACTIMPORTER_BOL_Invitation
	 *
	 */
	public function save(YNCONTACTIMPORTER_BOL_Invitation $invitationDto)
	{
		return  $this -> invitationDao -> save($invitationDto);
	}
	
	/**
	 * find invitation by id
	 *
	 * @param $invitationId
	 * @return YNCONTACTIMPORTER_BOL_Invitation
	 */
	public function findInvitationById($invitationId = 0)
	{
		if(!$invitationId)
			return null;
		return $this->invitationDao->findById($invitationId);
	}
	
	
	/**
	 * find invitation by id
	 *
	 * @param $invitationId
	 * @return YNCONTACTIMPORTER_BOL_Invitation
	 */
	public function checkInvitedUser($friendId = "")
	{
		if(!$friendId)
			return null;
		return $this->invitationDao->findByFriendId($friendId);
	}
	
	/**
	 * add invitations
	 * 
	 * @param OW_Example
	 * 
	 * @return bool
	 */
	 public function addInvitations($userId = 0, $type = 'email', $provider = 'facebook', $inviteList = array(), $message)
	 {
	 	$this -> invitationDao -> addInvitations($userId, $type, $provider, $inviteList, $message);
		$this -> statisticDao -> updateStatistic($userId, count($inviteList));
	 }
	 /**
	 * delete by id
	 *
	 * @param 
	 * @return void
	 */
	public function deleteInvitationById($id)
	{
		$invitation = $this->invitationDao->findById($id);
		if($invitation)
		{
			$this -> delete($invitation);
		}
	}
	
	/**
	 * get top inviters
	 * @param array $params
	 * @return array YNCONTACTIMPORTER_BOL_Statistic
	 */
	public function getTopInviters($params = array())
	{
		$inviters = $this -> statisticDao -> getTopInviters($params);
		$arr_inviters = array();
		foreach ($inviters as $inviter) 
		{
			$href = BOL_UserService::getInstance() -> getUserUrl($inviter -> userId);
			$name = BOL_UserService::getInstance() -> getUserName($inviter -> userId);
			$total_queue_mail = YNCONTACTIMPORTER_BOL_PendingService::getInstance() -> countPendingEmailsByUserId(array('userId' => $inviter -> userId));
			$total_queue_message = YNSOCIALBRIDGE_BOL_QueueService::getInstance()->countQueuesByUserId(array('userId' => $inviter -> userId, 'type' => 'sendInvite'));
			$total_remain = $total_queue_mail + $total_queue_message;
			$arr_inviters[] = array('href' => $href, 'total' => $inviter -> totalSent - $total_remain, 'name' => $name);
		}
		return $arr_inviters;
	}
	
	/**
	 * get statistics
	 * @param array $params
	 * @return array YNCONTACTIMPORTER_BOL_Statistic
	 */
	public function getStatistics($params = array())
	{
		$userId = OW::getUser() -> getId();
		
		$total_queue_mail = YNCONTACTIMPORTER_BOL_PendingService::getInstance() -> countPendingEmailsByUserId(array('userId' => $userId));
		$total_queue_message = YNSOCIALBRIDGE_BOL_QueueService::getInstance()->countQueuesByUserId(array('userId' => $userId, 'type' => 'sendInvite'));
		$total_remain = $total_queue_mail + $total_queue_message;
		
		$total_sent = 0;
		$statistic = $this -> statisticDao -> findByUserId($userId);
		if($statistic)
		{
			$total_sent = $statistic -> totalSent - $total_remain;
		}
		
		$total_joined = $this -> joinedDao -> getTotalJoined($userId);
		return array('sent' => $total_sent, 'remain' => $total_remain, 'joined' => $total_joined);
	}
}
