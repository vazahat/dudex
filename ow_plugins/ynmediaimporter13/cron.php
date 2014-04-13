<?php
ini_set('gd.jpeg_ignore_warning', 1);
ini_set('display_startup_errors', 0);
ini_set('display_errors', 0);
ini_set('error_reporting', E_ALL);

class YNMEDIAIMPORTER_Cron extends OW_Cron
{
	public function __construct()
	{
		parent::__construct();
		$this->addJob('processScheduler', 1);
	}

	public function run()
	{
		//ignore
	}

	public function processScheduler()
	{
		/**
		 * following step to speed up & beat performance
		 * 1. check album limit
		 * 2. check quota limit
		 * 3. get nodes of this schedulers
		 * 4. get all items of current schedulers.
		 * 5. process each node
		 * 5.1 check required quota
		 * 5.2 fetch data to pubic file
		 * 5.3 store to file model
		 * 6. check status of schedulers, if scheduler is completed == (remaining == 0)
		 * 6.1 udpate feed and message.
		*/
		
		/**
		 * Unlimited time.
		*/
		set_time_limit(0);
		
		/**
		 * default 20
		 * @var int
		*/
		$configs =  OW::getConfig()->getValues('ynmediaimporter');
		$limitUserPerCron = ($configs['number_photo']) ? (intval($configs['number_photo'])) : 20;
		
		/**
		 * default 20
		 * @var int
		*/
		$limitQueuePerCron = ($configs['number_queue']) ? (intval($configs['number_queue'])) : 20;
		/**
		 * process number queue.
		 */
		
		/**
		 * get scheduler from tables data.
		*/

		$example = new OW_Example();
		$example->andFieldLessThan('status', '3');
		$example->setOrder('last_run');
		$example->setLimitClause($first, $count)->setLimitClause(0, $limitQueuePerCron);
		$schedulers = YNMEDIAIMPORTER_BOL_SchedulerDao::getInstance()->findListByExample($example);
		
		foreach ($schedulers as $scheduler)
		{
			Ynmediaimporter::processScheduler($scheduler, 0, $limitUserPerCron, 1, 1);
		}
		
		echo "success!";
		exit(0);
	}
}
