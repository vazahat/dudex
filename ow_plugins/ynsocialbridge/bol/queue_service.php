<?php
class YNSOCIALBRIDGE_BOL_QueueService
{
	/*
	 * @var YNSOCIALBRIDGE_BOL_QueueService
	 */
	private static $classInstance;

	/*
	@var YNSOCIALBRIDGE_BOL_QueueDao
	*/
	private $queueDao;

	private function __construct()
	{
		$this -> queueDao = YNSOCIALBRIDGE_BOL_QueueDao::getInstance();
	}

	/**
	 * Returns class instance
	 *
	 * @return YNSOCIALBRIDGE_BOL_QueueService
	 */
	public static function getInstance()
	{
		if (!isset(self::$classInstance))
			self::$classInstance = new self();
		return self::$classInstance;
	}

	/**
	 * @var YNSOCIALBRIDGE_BOL_Queue
	 *
	 */
	public function save($queueDto)
	{
		$queueDao = $this -> queueDao;
		return $queueDao -> save($queueDto);
	}

	/**
	 * @var YNSOCIALBRIDGE_BOL_Queue
	 *
	 */
	public function delete($queueDto)
	{
		$queueDao = $this -> queueDao;
		return $queueDao -> delete($queueDto);
	}

	/*
	 * @var array $params
	 *
	 * @return YNSOCIALBRIDGE_BOL_Queue
	 */
	public function getQueue($params = array())
	{
		$queueDao = $this -> queueDao;
		if (!$params)
			return null;
		$example = new OW_Example();
		$example -> andFieldEqual('tokenId', $params['tokenId']);
		$example -> andFieldEqual('service', $params['service']);
		$example -> andFieldEqual('userId', $params['userId']);
		$example -> andFieldEqual('type', $params['type']);
		return $queueDao -> findObjectByExample($example);
	}

	/*
	 *
	 * @return List YNSOCIALBRIDGE_BOL_Queue
	 */
	public function getAllQueues()
	{
		$queueDao = $this -> queueDao;
		return $queueDao -> findAll();
	}

	/**
	 *
	 * delete all queues by token id
	 *
	 */
	public function deleteQueuesByTokenId($token_id)
	{
		$queueDao = $this -> queueDao;
		if (!$token_id)
			return null;
		$example = new OW_Example();
		$example -> andFieldEqual('tokenId', $token_id);
		return $queueDao -> deleteByExample($example);
	}

	/**
	 *
	 * get all queues by user id
	 *
	 */
	public function getQueuesByUserId($params)
	{
		$queueDao = $this -> queueDao;
		if (!$params['userId'])
			return null;
		$example = new OW_Example();
		$example -> andFieldEqual('userId', $params['userId']);
		$example -> andFieldEqual('type', $params['type']);
		$queues = $queueDao -> findListByExample($example);
		$contacts = array();
		foreach ($queues as $queue)
		{
			$extra_params = unserialize($queue -> extraParams);
			$lists = $extra_params['list'];
			foreach ($lists as $key => $value)
			{
				if (isset($params['search']) && $params['search'] != '')
				{
					if (strpos(strtoupper("." . $value), strtoupper(trim($params['search']))))
					{
						$contacts[] = array(
							'id' => $key."/".$queue -> id,
							'name' => $value,
							'time' => $queue -> lastRun,
							'provider' => ucfirst($queue -> service)
						);
					}
				}
				else
				{
					$contacts[] = array(
						'id' => $key."/".$queue -> id,
						'name' => $value,
						'time' => $queue -> lastRun,
						'provider' => ucfirst($queue -> service)
					);
				}
			}
			if(count($lists) <= 0)
			{
				$this->queueDao->delete($queue);
			}
		}
		if (count($contacts > 0))
		{
			return array_slice($contacts, $params['first'], $params['count']);
		}
		return Null;
	}

	/**
	 *
	 * count queues by user id
	 *
	 */
	public function countQueuesByUserId($params)
	{
		$queueDao = $this -> queueDao;
		if (!$params['userId'])
			return null;
		$example = new OW_Example();
		$example -> andFieldEqual('userId', $params['userId']);
		$example -> andFieldEqual('type', $params['type']);
		$queues = $queueDao -> findListByExample($example);
		$count = 0;
		foreach ($queues as $queue)
		{
			$extra_params = unserialize($queue -> extraParams);
			$lists = $extra_params['list'];
			foreach ($lists as $key => $value)
			{
				if (isset($params['search']) && $params['search'] != '')
				{
					if (strpos(strtoupper("." . $value), strtoupper(trim($params['search']))))
					{
						$count++;
					}
				}
				else
				{
					$count++;
				}
			}
		}
		return $count;
	}

	/**
	 *
	 * delete queue contact by id
	 */
	public function deleteContactById($id)
	{
		if (!$id)
			return;
		$arr = explode('/', $id);
		if($arr)
		{
			$contactId = @$arr[0];
			$queId = @$arr[1];
			if($queId)
			{
				$queue = $this->queueDao->findById($queId);
				$extra_params = unserialize($queue -> extraParams);
				$lists = $extra_params['list'];
				$arr_user_queues = array();
				foreach ($lists as $key => $value)
				{
					if($key != $contactId)
					{
						$arr_user_queues[$key] = $value;
					}
				}
				$extra_params['list'] = $arr_user_queues;
				$queue->extraParams = serialize($extra_params);
				$this->queueDao->save($queue);
			}
		}
	}
}
