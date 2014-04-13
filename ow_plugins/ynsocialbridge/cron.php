<?php
class YNSOCIALBRIDGE_Cron extends OW_Cron
{
    public function __construct()
    {
        parent::__construct();
        $this->addJob('queueProcess', 5);
    }

    public function run()
    {
		//ignore
    }

    public function queueProcess()
    {
    	$queues = YNSOCIALBRIDGE_BOL_QueueService::getInstance()->getAllQueues();
		$core = new YNSOCIALBRIDGE_CLASS_Core();
		foreach ($queues as $queue)
		{
			switch ($queue->type)
			{
				case 'sendInvite' :
					$extra_params = unserialize($queue->extraParams);
					$token = YNSOCIALBRIDGE_BOL_TokenService::getInstance()->findById($queue->tokenId);
					$obj = $core -> getInstance($queue -> service);
					$params['list'] = $extra_params['list'];
					$params['link'] = $extra_params['link'];
					$params['message'] = $extra_params['message'];
					$params['uid'] = $token->uid;
					$params['user_id'] = $queue -> userId;
					$params['access_token'] = $token -> accessToken;
					$params['secret_token'] = $token -> secretToken;
					echo ucfirst($queue -> service).": ".$token->uid.": Send invite successfully! ";
					if($obj -> sendInvites($params))
					{
						echo " <br/>  ";
						YNSOCIALBRIDGE_BOL_QueueService::getInstance()->delete($queue);
					}
					break;
				case 'getFeed':
$configs = OW::getConfig()->getValues('ynsocialstream');
						if(isset($configs['get_feed_cron']) && $configs['get_feed_cron'])
						{
								
							$service = $queue -> service;
							$token = YNSOCIALBRIDGE_BOL_TokenService::getInstance()->findById($queue->tokenId);
							//get user & check authorized get feed
							OW_User::getInstance()->login($token->userId);		
							if ( !OW::getUser()->isAuthorized('ynsocialstream', 'get_feed') )
					        {					        	
					            break;					           
					        }
							
											
							//check preferences
							if(!$configs['enable_'.$service.'_'.$token->userId])
								break;
							
							if($token && isset($configs['cron_job_user_'.$token->userId]) && $configs['cron_job_user_'.$token->userId])
							{								
								$uid = $token->uid;
								//get Feeds
								$obj = $core->getInstance($service);
								
								$feedService = YNSOCIALSTREAM_BOL_SocialstreamFeedService::getInstance();
								
								$feedLast =  $feedService->getFeedLast($service, $uid);
								
								$arr_token = array('access_token'=>$token -> accessToken,'secret_token'=>$token -> secretToken);
								$arr_token['lastFeedTimestamp'] = 0;
								
								if($feedLast)
										$arr_token['lastFeedTimestamp'] = $feedLast['timestamp'];
								
								$arr_token['limit'] = $configs['max_'.$service.'_get_feed'];									
								$arr_token['uid'] = $uid;
								$arr_token['type'] = 'user';
								$activities = $obj->getActivity($arr_token);	
								
								
								
								if($activities != null)
								{
									$obj->insertFeeds(array(
										'activities'	=> $activities,
							 			'user_id'		=> $token->userId,
										'timestamp'     => $arr_token['lastFeedTimestamp'],
										'access_token'	=>$token -> accessToken,
										'secret_token'	=>$token -> secretToken,
										'uid'           => $uid,
									));
								}	
								
								$queue->lastRun = date ('Y-m-d H:i:s');
								YNSOCIALBRIDGE_BOL_QueueService::getInstance()->save($queue);
								echo ucfirst($service).": ".$token->uid.": Get feed successfully!";
								echo " <br/>  ";
							}							
							OW_User::getInstance()->logout();
							
						}
					break;
					
				default :
					break;
			}
		}
	}
}