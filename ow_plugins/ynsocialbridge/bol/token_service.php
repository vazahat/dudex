<?php
class YNSOCIALBRIDGE_BOL_TokenService
{
	/*
	 * @var YNSOCIALBRIDGE_BOL_TokenService
	 */
	private static $classInstance;

	/*
	@var YNSOCIALBRIDGE_BOL_TokenDao
	*/
	private $tokenDao;

	private function __construct()
	{
		$this -> tokenDao = YNSOCIALBRIDGE_BOL_TokenDao::getInstance();
	}

	/**
	 * Returns class instance
	 *
	 * @return YNSOCIALBRIDGE_BOL_TokenService
	 */
	public static function getInstance()
	{
		if (!isset(self::$classInstance))
			self::$classInstance = new self();
		return self::$classInstance;
	}
	/**
	 * @var YNSOCIALBRIDGE_BOL_Token
	 * 
	 */
	public function save($tokenDto)
	{
		$tokenDao = $this -> tokenDao;
		return $tokenDao -> save($tokenDto);
	}
	/**
     * find User Token
     *
     * @param array $params
     * @return YNSOCIALBRIDGE_BOL_Token
     */
    public function findUserToken( $params = array() )
    {
    	return $this->tokenDao->findUserToken($params);
	}
	
	/**
     * delete Token
     *
     * @param YNSOCIALBRIDGE_BOL_Token
     * @return void
     */
     public function delete(YNSOCIALBRIDGE_BOL_Token $tokenDto)
	 {
	 	//get all Queues with this token
	 	YNSOCIALBRIDGE_BOL_QueueService::getInstance()->deleteQueuesByTokenId($tokenDto->id);
		$tokenDao = $this -> tokenDao;
		return $tokenDao -> delete($tokenDto);
	 }
	 /**
     * @return YNSOCIALBRIDGE_BOL_Token
     */
    public function findById( $id )
    {
        $tokenDao = $this->tokenDao;

        return $tokenDao->findById($id);
    }
}
