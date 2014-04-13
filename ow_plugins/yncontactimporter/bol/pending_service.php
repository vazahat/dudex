<?php
class YNCONTACTIMPORTER_BOL_PendingService
{
	/*
	 * @var YNCONTACTIMPORTER_BOL_PendingService
	 */
	private static $classInstance;

	/*
	@var YNCONTACTIMPORTER_BOL_PendingDao
	*/
	private $pendingDao;

	private function __construct()
	{
		$this -> pendingDao = YNCONTACTIMPORTER_BOL_PendingDao::getInstance();
	}

	/**
	 * Returns class instance
	 *
	 * @return YNCONTACTIMPORTER_BOL_PendingService
	 */
	public static function getInstance()
	{
		if (!isset(self::$classInstance))
			self::$classInstance = new self();
		return self::$classInstance;
	}

	/**
	 * @var YNCONTACTIMPORTER_BOL_Pending
	 *
	 */
	public function savePending($mail)
	{
		$mailState = $mail->saveToArray();
		$mailDto = NULL;
		foreach ( $mailState['recipientEmailList'] as $email )
        {
			$mailDto = new BOL_Mail();
	        $mailDto->senderEmail = $mailState['sender'][0];
	        $mailDto->senderName = $mailState['sender'][1];
	        $mailDto->subject = $mailState['subject'];
	        $mailDto->textContent = $mailState['textContent'];
	        $mailDto->htmlContent = $mailState['htmlContent'];
	        $mailDto->sentTime = empty($mailState['sentTime']) ? time() : $mailState['sentTime'];
	        $mailDto->priority = $mailState['priority'];
	        $mailDto->recipientEmail = $email;
	        $mailDto->senderSuffix = intval($mailState['senderSuffix']);
		}
        BOL_MailDao::getInstance()->save($mailDto);
		$pendingDto = new YNCONTACTIMPORTER_BOL_Pending();
		$pendingDto->emailId = $mailDto->id;
		$pendingDto->userId = OW::getUser()->getId();
		$pendingDao = $this -> pendingDao;
		return $pendingDao->save($pendingDto);
	}

	/**
	 * find All Pending
	 *
	 * @param array $params
	 * @return array YNCONTACTIMPORTER_BOL_Pending
	 */
	public function getAllPendingEmailsByUserId($params = array())
	{
		return $this -> pendingDao -> getAllPendingEmailsByUserId($params);
	}
	
	/**
	 * count All Pending
	 *
	 * @param array $params
	 * @return int
	 */
	public function countPendingEmailsByUserId($params = array())
	{
		return $this -> pendingDao -> countPendingEmailsByUserId($params);
	}

	/**
	 * delete Pending
	 *
	 * @param YNCONTACTIMPORTER_BOL_Pending
	 * @return void
	 */
	public function delete(YNCONTACTIMPORTER_BOL_Pending $pendingDto)
	{
		$pendingDao = $this -> pendingDao;
		return $pendingDao -> delete($pendingDto);
	}
	/**
	 * delete Pending by id
	 *
	 * @param 
	 * @return void
	 */
	public function deleteEmailById($id)
	{
		$pendingDao = $this -> pendingDao;
		$pending = $pendingDao->findById($id);
		if($pending)
		{
			$email = BOL_MailDao::getInstance() -> findById($pending->emailId) -> recipientEmail;
			BOL_MailDao::getInstance()->deleteById($pending->emailId);
			$pendingDao -> delete($pending);
			// delete invatation 
			$invitation = YNCONTACTIMPORTER_BOL_InvitationService::getInstance() -> checkInvitedUser($email);
			if($invitation)
				YNCONTACTIMPORTER_BOL_InvitationService::getInstance() -> delete($invitation);
		}
	}
}
