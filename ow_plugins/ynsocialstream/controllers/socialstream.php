<?php
class YNSOCIALSTREAM_CTRL_Socialstream extends OW_ActionController
{
	private $menu;
	private $service;
	public function __construct()
	{
		
		$this -> setPageHeading(OW::getLanguage() -> text('ynsocialbridge', 'socialbridge_management_page_heading'));
		$this -> setPageHeadingIconClass('ow_yn_socialbridge');

		//Preference menu
		$contentMenu = new BASE_CMP_PreferenceContentMenu();
		$contentMenu -> getElement('socialbridge') -> setActive(true);
		$this -> addComponent('contentMenu', $contentMenu);
		
		$core = new YNSOCIALBRIDGE_CLASS_Core();
		$this->menu = $core->initMenu();			
		$this -> addComponent('menu', $this->menu);
		
		$this->service = YNSOCIALSTREAM_BOL_SocialstreamFeedService::getInstance();
		
		//load css
		$cssUrl = OW::getPluginManager() -> getPlugin('ynsocialbridge') -> getStaticCssUrl() . 'ynsocialbridge.css';
		OW::getDocument() -> addStyleSheet($cssUrl);

		//put languages to database when chage
		//OW::getLanguage()->importPluginLangs(OW::getPluginManager()->getPlugin('ynsocialbridge')->getRootDir() .
		//'langs.zip','ynsocialbridge');
	}

	// manage connections
	public function index($params)
	{				
		
		//print_r($_SERVER);die;		
		$language = OW::getLanguage();
		
		if (!OW::getUser() -> isAuthenticated())
		{
			throw new AuthenticateException();
		}
		$el = $this->menu->getElement('stream-settings');
		 if ( $el )
        {
            $el->setActive(true);
        }
        
        //check configure
		
		$configs = OW::getConfig()->getValues('ynsocialstream');
		
		
        $this->assign('configs', $configs);

        $form = new YNSOCIALSTREAM_FrontSettingsForm($configs);
         
        $this->addForm($form);
        
        if ( OW::getRequest()->isPost() && $form->isValid($_POST) )
        {
        	if ( $form->process($_POST) )
        	{     
        		OW::getFeedback()->info($language->text('ynsocialstream', 'settings_updated'));
        		//$this->redirect(OW::getRouter()->urlForRoute('ynsocialstream-global-settings'));
        	}
        }
	}
	public function getFeed()
	{
		
		$viewer = OW::getUser();			
		if (!$viewer -> isAuthenticated())
		{
			throw new AuthenticateException();
			exit;
		}
		$provider = $_GET['service'];
		
		if(!isset($provider) || $provider=='')
		{
			throw new InvalidArgumentException('Invalid parameter provider');
			$this->redirect($_GET['url']);	
			exit;
		}	
		
		if (empty($_SESSION['socialbridge_session'][$provider]))
		{
			$this->redirect($_GET['url']);	
			exit;
		}
		
		
		$core = new YNSOCIALSTREAM_CLASS_Core();
		if(!$core->checkSocialBridgePlugin($provider))
		{				
			throw new InvalidArgumentException('Not find social bridge plug-in');
			$this->redirect($_GET['url']);	
			exit;
		}	
				
		
		$core = new YNSOCIALBRIDGE_CLASS_Core();
		//get facebook or twitter or linkedin token
		$obj = $core -> getInstance($provider);	
		$uid = $obj -> getOwnerId(array('access_token' => $_SESSION['socialbridge_session'][$provider]['access_token']));
		
		
		//check permission get Feed
		/* todo  */
		
		$arr_token = $_SESSION['socialbridge_session'][$provider];
		$feedLast = $this->service->getFeedLast($provider, $uid);
		
		
		
		$arr_token['lastFeedTimestamp'] = 0;
		if ($feedLast)
			$arr_token['lastFeedTimestamp'] = $feedLast['timestamp'];
			
		//save queue
		$values = array(
			'service' => $provider,
			'userId' => $viewer -> getId()
		);
		$token = $obj -> getToken($values);
		
		
		if ($token)
		{
			if (!$obj -> getQueue(array(
				'tokenId' => $token -> id,
				'userId' => $viewer -> getId(),
				'service' => $provider,
				'type' => 'getFeed'
			)))
			{
				$values = array(
					'tokenId' => $token -> id,
					'userId' => $viewer -> getId(),
					'service' => $provider,
					'type' => 'getFeed',
					'extraParams' => '',
					'lastRun' => date('Y-m-d H:i:s'),
					'status' => 0,					
				);				
				$obj -> saveQueues($values);
			}
		}
		
		$configs = OW::getConfig()->getValues('ynsocialstream');
		
		$arr_token['limit'] = $configs['max_'.$provider.'_get_feed'];
		$arr_token['type'] = 'user';
		$arr_token['uid'] = $uid;
		$activities = $obj -> getActivity($arr_token);
		
		if ($activities != null)
		{
			$obj->insertFeeds(array(
				'activities' => $activities,
				'user_id' => $viewer->getId(),
				'timestamp' => $arr_token['lastFeedTimestamp'],
				'access_token'	=>$_SESSION['socialbridge_session'][$provider]['access_token'],
				'secret_token'	=>$_SESSION['socialbridge_session'][$provider]['secret_token'],
				'uid'           => $uid,				
			));
			sleep(5+ count($activities)/5);
		}
		
		$this->redirect($_GET['url']);	
		
	}

	// todo
	public function connect()
	{	
		try{
			// if ( !OW::getUser()->isAuthorized('ynsocialstream', 'get_feed') )
        // {
            // $this->setTemplate(OW::getPluginManager()->getPlugin('base')->getCtrlViewDir() . 'authorization_failed.html');
            // return;
        // }
			
		//get provider	
		
		$provider = $_POST['service'];		
		$oxUrl = $_POST['url'];
	
		if(!isset($provider) || $provider=='')
		{
			throw new InvalidArgumentException('Invalid parameter provider');
			exit;
		}	
		
		if (in_array($provider, array(
				'facebook',
				'twitter',
				'linkedin'
			)))
		{
			$core = new YNSOCIALSTREAM_CLASS_Core();
			if(!$core->checkSocialBridgePlugin($provider))
			{
				exit("invalid url");
				//OW::getFeedback()->warning(OW::getLanguage() -> text('ynsocialstream', 'selected_fail'));
				//$this->redirect($importUrl);
				throw new InvalidArgumentException('Not find social bridge plug-in');
				exit;
			}
			
			$viewer_id = OW::getUser()->getId();
			
			$core = new YNSOCIALBRIDGE_CLASS_Core();
			
			//get facebook or twitter or linkedin token
			$obj = $core -> getInstance($provider);	
			$values = array('service' => $provider,'userId' => $viewer_id);
			$token = $obj -> getToken($values);
			$callbackUrl = OW::getRouter() -> urlForRoute('ynsocialstream-get-feed');

			$url = NULL;
			
						
			switch ($provider) {
				case 'facebook':
					if( $token && $token->accessToken)
					{
						$_SESSION['socialbridge_session']['facebook']['access_token'] = $token->accessToken;
						$url = $callbackUrl.'?service=facebook'.'&url='.$oxUrl;
						$uid = $obj->getOwnerId(array('access_token' => $_SESSION['socialbridge_session']['facebook']['access_token']));
						
						$permissions = $obj->hasPermission(array(
							            'uid' => $uid,
							            'access_token' => $_SESSION['socialbridge_session']['facebook']['access_token']
							            ));
				        if ( empty($permissions[0]['publish_stream']) || empty($permissions[0]['status_update']) || empty($permissions[0]['read_stream'])) 
				        {
							$url = $obj -> getConnectUrl() . '?scope=publish_stream,status_update,read_stream' . '&' . http_build_query(array('callbackUrl' => $callbackUrl . '?service=facebook'.'&url='.$oxUrl));
						}
					}
					else
					{
						$url = $obj -> getConnectUrl() . '?scope=user_photos,publish_stream,status_update,read_stream' . '&' . http_build_query(array('callbackUrl' => $callbackUrl . '?service=facebook'.'&url='.$oxUrl));
					}
					
					break;
				
				case 'twitter':
					if($token &&  $token->accessToken)
					{
						$_SESSION['socialbridge_session']['twitter']['access_token'] = $token->accessToken;
				    	$_SESSION['socialbridge_session']['twitter']['secret_token'] = $token->secretToken;
						$_SESSION['socialbridge_session']['twitter']['owner_id'] = $token->uid;
						$url = $callbackUrl.'?service=twitter'.'&url='.$oxUrl;
					}
					else
					{
						$url = $obj -> getConnectUrl() . '?scope=rw_nus' . '&' . http_build_query(array('callbackUrl' => $callbackUrl . '?service=twitter'.'&url='.$oxUrl));
					}
					break;
				case 'linkedin':
					if($token && isset($_SESSION['socialbridge_session']['linkedin']['stream']))
					{
						$url = $callbackUrl.'?service=linkedin'.'&url='.$oxUrl;
					}
					else
					{
						$url = $obj -> getConnectUrl() . '?scope=rw_nus,r_basicprofile' . '&' . http_build_query(array('callbackUrl' => $callbackUrl . '?service=linkedin'.'&url='.$oxUrl));
					}	
					break;
			}
			//echo $url;die;
 			exit(json_encode($url));			
			//return $this -> redirect($url);
		}
		}catch(Exception $ex){
			throw $ex;
		}
		
	}
}
class YNSOCIALSTREAM_FrontSettingsForm extends Form
{

	/**
	 * Class constructor
	 *
	 */
	public function __construct( $configs )
	{
		parent::__construct('YNSOCIALSTREAM_FrontSettingsForm');

		$language = OW::getLanguage();
		
		$user_id = OW::getUser() -> getId();
		
        	
        $field = new RadioField('cron_job_user');
		$field->setLabel($language->text('ynsocialstream', 'cron_job_user_label'));
		$field->setValue($configs['cron_job_user_'.$user_id]);
		$field->addOptions(array('1' => $language->text('admin', 'permissions_index_yes'), '0' => $language->text('admin', 'permissions_index_no')));
		$this->addElement($field);
      
		
		

		$field = new RadioField('enable_facebook');
		$field->setLabel($language->text('ynsocialstream', 'enable_facebook_label'));
		$field->setValue($configs['enable_facebook_'.$user_id]);
		$field->addOptions(array('1' => $language->text('admin', 'permissions_index_yes'), '0' => $language->text('admin', 'permissions_index_no')));
		$this->addElement($field);
		
		$field = new RadioField('auth_fb');
		$field->setLabel($language->text('ynsocialstream', 'auth_fb_label'));
		$field->setValue($configs['auth_fb_'.$user_id]);
		$field->addOptions(array(
			'everybody' => $language->text('ynsocialstream', 'everybody'),
			'friends_only' => $language->text('ynsocialstream', 'friends_only'),
			'only_for_me' => $language->text('ynsocialstream', 'only_for_me')
			));		
		$this->addElement($field);		
		
		
		$field = new RadioField('enable_twitter');
		$field->setLabel($language->text('ynsocialstream', 'enable_twitter_label'));
		$field->setValue($configs['enable_twitter_'.$user_id]);
		$field->addOptions(array('1' => $language->text('admin', 'permissions_index_yes'), '0' => $language->text('admin', 'permissions_index_no')));
		$this->addElement($field);
		
		$field = new RadioField('auth_tw');
		$field->setLabel($language->text('ynsocialstream', 'auth_tw_label'));
		$field->setValue($configs['auth_tw_'.$user_id]);
		$field->addOptions(array(
			'everybody' => $language->text('ynsocialstream', 'everybody'),
			'friends_only' => $language->text('ynsocialstream', 'friends_only'),
			'only_for_me' => $language->text('ynsocialstream', 'only_for_me')
			));
		$this->addElement($field);
		
		$field = new RadioField('enable_linkedin');
		$field->setLabel($language->text('ynsocialstream', 'enable_linkedin_label'));
		$field->setValue($configs['enable_linkedin_'.$user_id]);
		$field->addOptions(array('1' => $language->text('admin', 'permissions_index_yes'), '0' => $language->text('admin', 'permissions_index_no')));
		$this->addElement($field);
		
		$field = new RadioField('auth_li');
		$field->setLabel($language->text('ynsocialstream', 'auth_li_label'));
		$field->setValue($configs['auth_li_'.$user_id]);
		$field->addOptions(array(
			'everybody' => $language->text('ynsocialstream', 'everybody'),
			'friends_only' => $language->text('ynsocialstream', 'friends_only'),
			'only_for_me' => $language->text('ynsocialstream', 'only_for_me')
			));
		$this->addElement($field);

		// submit
		$submit = new Submit('save');
		$submit->setValue($language->text('ynsocialstream', 'admin_save_btn'));
		$this->addElement($submit);
		
		
	}

	/**
	 * Updates photo plugin configuration
	 *
	 * @return boolean
	 */
	public function process( $data )
	{
		
		$config = OW::getConfig();
		$user_id = OW::getUser() -> getId();

		$config->saveConfig('ynsocialstream', 'enable_facebook_'.$user_id, $_POST['enable_facebook']);
		$config->saveConfig('ynsocialstream', 'enable_twitter_'.$user_id, $_POST['enable_twitter']);
		$config->saveConfig('ynsocialstream', 'enable_linkedin_'.$user_id, $_POST['enable_linkedin']);
		
		$config->saveConfig('ynsocialstream', 'auth_fb_'.$user_id, $_POST['auth_fb']);
		$config->saveConfig('ynsocialstream', 'auth_tw_'.$user_id, $_POST['auth_tw']);
		$config->saveConfig('ynsocialstream', 'auth_li_'.$user_id, $_POST['auth_li']);
		
		
		if(isset($_POST['cron_job_user']))
			$config->saveConfig('ynsocialstream', 'cron_job_user_'.$user_id, $_POST['cron_job_user']);
		
		 
		return true;
	}
}