<?php
class YNCONTACTIMPORTER_BOL_JoinedService
{
	/*
	 * @var YNCONTACTIMPORTER_BOL_JoinedService
	 */
	private static $classInstance;
	
	/*
	@var YNCONTACTIMPORTER_BOL_JoinedDao
	*/
	private $joinedDao;

	private function __construct()
	{
		$this -> joinedDao = YNCONTACTIMPORTER_BOL_JoinedDao::getInstance();
	}
	
	
	/**
	 * Returns class instance
	 *
	 * @return YNCONTACTIMPORTER_BOL_JoinedService
	 */
	public static function getInstance()
	{
		if (!isset(self::$classInstance))
			self::$classInstance = new self();
		return self::$classInstance;
	}

	/**
	 * find provider by id
	 *
	 * @param $userId
	 * @return 
	 */
	public function onUserRegister($userId = 0)
	{
		if(!$userId)
			return null;
		
		if(isset($_COOKIE['yncontactimporter_userId']))
		{
			$refId = $_COOKIE['yncontactimporter_userId'];
			unset($_COOKIE['yncontactimporter_userId']);
			
			// check email and update isued
			$user = BOL_UserService::getInstance() -> findUserById($userId);
			$email = $user -> getEmail();
			if($invitation = YNCONTACTIMPORTER_BOL_InvitationService::getInstance() -> checkInvitedUser($email))
			{
				$invitation -> isUsed = 1;
				YNCONTACTIMPORTER_BOL_InvitationService::getInstance() -> save($invitation);
			}
			
			// save joined
			$joined = new YNCONTACTIMPORTER_BOL_Joined();
			$joined->userId = $userId;
			$joined->inviterId = $refId;
			$this -> joinedDao -> save($joined);
			
			//invite friend
			$event = new OW_Event('friends.add_friend', array(
	                'requesterId' => $refId,
	                'userId' => $userId
	            ));
	
	        OW::getEventManager()->trigger($event);
		}
	}

}
