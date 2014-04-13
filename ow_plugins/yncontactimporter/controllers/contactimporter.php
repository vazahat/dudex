<?php
class YNCONTACTIMPORTER_CTRL_Contactimporter extends OW_ActionController
{
	private $menu;
	public function __construct()
	{
		$this -> setPageHeading(OW::getLanguage() -> text('yncontactimporter', 'page_heading'));
		$this -> setPageHeadingIconClass('');

		$this->menu = $this -> _initMenu();
		
		OW::getNavigation()->activateMenuItem(OW_Navigation::MAIN, 'yncontactimporter', 'contact_importer');

		//load css
		$cssUrl = OW::getPluginManager() -> getPlugin('yncontactimporter') -> getStaticCssUrl() . 'yncontactimporter.css';
		OW::getDocument() -> addStyleSheet($cssUrl);

		//put languages to database when development
		//OW::getLanguage() -> importPluginLangs(OW::getPluginManager() -> getPlugin('yncontactimporter') -> getRootDir() . 'langs.zip', 'yncontactimporter');
	}
	/**
	 * init top menu for social bridge
	 *
	 * @return void
	 */
	protected function _initMenu()
	{
		$item = array();
		$item[0] = new BASE_MenuItem( array());
		$item[0] -> setLabel(OW::getLanguage() -> text('yncontactimporter', 'invite_friends'));
		$item[0] -> setIconClass('ow_ic_reply');
		$item[0] -> setKey('1');
		$item[0] -> setUrl(OW::getRouter() -> urlForRoute('yncontactimporter-import'));
		$item[0] -> setOrder(1);

		$item[1] = new BASE_MenuItem( array());
		$item[1] -> setLabel(OW::getLanguage() -> text('yncontactimporter', 'email_queue_invites'));
		$item[1] -> setIconClass('ow_ic_mail');
		$item[1] -> setKey('2');
		$item[1] -> setUrl(OW::getRouter() -> urlForRoute('yncontactimporter-email-queue'));
		$item[1] -> setOrder(2);
		
		if(OW::getPluginManager()->isPluginActive('ynsocialbridge'))
		{
			$item[2] = new BASE_MenuItem( array());
			$item[2] -> setLabel(OW::getLanguage() -> text('yncontactimporter', 'message_queue_invites'));
			$item[2] -> setIconClass('ow_ic_chat');
			$item[2] -> setKey('3');
			$item[2] -> setUrl(OW::getRouter() -> urlForRoute('yncontactimporter-social-queue'));
			$item[2] -> setOrder(3);
		}
		$item[3] = new BASE_MenuItem( array());
		$item[3] -> setLabel(OW::getLanguage() -> text('yncontactimporter', 'pending_invitations'));
		$item[3] -> setIconClass('ow_ic_friends');
		$item[3] -> setKey('4');
		$item[3] -> setUrl(OW::getRouter() -> urlForRoute('yncontactimporter-pending'));
		$item[3] -> setOrder(4);
		
		$contentMenu = new BASE_CMP_ContentMenu($item);
		$this -> addComponent('menu', $contentMenu);
		return $contentMenu;
	}

	// home page
	public function import()
	{
		if (!OW::getUser() -> isAuthenticated())
		{
			throw new AuthenticateException();
		}
		if ( !OW::getUser()->isAuthorized('yncontactimporter', 'invite') )
        {
            $this->setTemplate(OW::getPluginManager()->getPlugin('base')->getCtrlViewDir() . 'authorization_failed.html');
            return;
        }
		$el = $this->menu->getElement('1');
        if ( $el )
        {
            $el->setActive(true);
        }
		OW::getDocument()->setTitle(OW::getLanguage()->text('yncontactimporter', 'meta_title_invite_import'));
        OW::getDocument()->setDescription(OW::getLanguage()->text('yncontactimporter', 'meta_description_invite_import'));
		$userId = OW::getUser()->getId();
		
		if (isset($_REQUEST['service']) || OW::getRequest() -> isPost())
		{
			//add friends if the email was been used in system
			if(isset($_REQUEST['task']) && $_REQUEST['task'] == 'do_add')
			{
				$service = FRIENDS_BOL_Service::getInstance();
				$count_addFriends = 0;
				$aFriendIdSelected = explode(',', $_POST['friendIds']);
				foreach ($aFriendIdSelected as $key => $val)
				{
					if ($val)
					{
						$email = $val;
						$user = BOL_UserService::getInstance()->findByEmail($email);
						if($user && $service->findFriendship($userId, $user->id) === null)
						{
							$service->request($userId, $user->id);
           					$this->onRequest($user->id);
							$count_addFriends ++;
						}
					}
				}
				if($count_addFriends > 0)
					OW::getFeedback()->info(OW::getLanguage()->text('friends', 'feedback_request_was_sent'));
			}
			//end add friends
			
			$provider = '';
			if (isset($_REQUEST['service']))
			{
				$provider = $_REQUEST['service'];
			}
			if (isset($_POST['contact']) && $_POST['contact'] != "" )
			{
				$provider = "yahoo_google_hotmail_csv";
			}
			$importUrl = OW::getRouter() -> urlForRoute('yncontactimporter-import');
			$useSocialBridge = 0;
			$contacts = null;
			$totalFriends = 0;
			$totalFriendSearch = 0;
			$contacts_add = NULL;
			$maxInvite = 10;
			$totalIvited = 0;
			$gmailContacts = "";
			$obj = null;
			if (in_array($provider, array(
				'facebook',
				'twitter',
				'linkedin'
			)))
			{
				$core = new YNCONTACTIMPORTER_CLASS_Core();
				if(!$core->checkSocialBridgePlugin($provider))
				{
					OW::getFeedback()->warning(OW::getLanguage() -> text('yncontactimporter', 'selected_fail'));
					$this->redirect($importUrl);
				}
				
				$core = new YNSOCIALBRIDGE_CLASS_Core();
				$obj = $core -> getInstance($provider);
				$tokenDto = null;
				if (empty($_SESSION['socialbridge_session'][$provider]))
				{
					$values = array(
						'service' => $provider,
						'userId' => OW::getUser() -> getId()
					);
					$tokenDto = $obj -> getToken($values);
				}
				$useSocialBridge = 1;
				$clientConfig = YNSOCIALBRIDGE_BOL_ApisettingService::getInstance() -> getConfig($provider);
				if($clientConfig)
				{
					$api_params = unserialize($clientConfig -> apiParams);
					if(isset($api_params['max_invite_day']))
					{
						$maxInvite = $api_params['max_invite_day'];
					}
				}
			}
			switch ($provider)
			{
				case 'facebook' :
					if (!empty($_SESSION['socialbridge_session'][$provider]['access_token']) || $tokenDto)
					{
						if ($tokenDto)
						{
							$_SESSION['socialbridge_session'][$provider]['access_token'] = $tokenDto -> accessToken;
						}
						$uid = $obj -> getOwnerId(array('access_token' => $_SESSION['socialbridge_session']['facebook']['access_token']));
						$permissions = $obj -> hasPermission(array(
							'uid' => $uid,
							'access_token' => $_SESSION['socialbridge_session'][$provider]['access_token']
						));
						if (empty($permissions[0]['publish_stream']) || empty($permissions[0]['xmpp_login']))
						{
							$this -> redirect($importUrl);
						}
						else
						{
							$friendsInvited = YNCONTACTIMPORTER_BOL_InvitationService::getInstance() -> getInvitationsByUserId(array('userId' => $userId, 'provider' =>'facebook'));
							
							$arr_invited = array();
							foreach ($friendsInvited as $invitation) 
							{
								$arr_invited[] = $invitation -> friendId;
							}
							$params = $_SESSION['socialbridge_session'][$provider];
							$params['invited'] = $arr_invited;
							$totalFriends = $obj -> getTotalFriends($params);
							$totalFriendSearch = $totalFriends;
							$contactPerPage = (int) OW::getConfig() -> getValue('yncontactimporter', 'contact_per_page');
							$params['limit'] = $contactPerPage;
							$params['offset'] = 0;
							if(isset($_REQUEST['search_page_id']))
							{
								$params['offset'] = $contactPerPage * ($_REQUEST['search_page_id'] - 1);
							} 
							if(isset($_REQUEST['search']))
							{
								$params['search'] = $_REQUEST['search'];
								$totalFriendSearch = $obj -> getTotalFriends($params);
							} 
							$contacts = $obj -> getContacts($params);
							//get total invited
							$values = array('uid' => $uid, 'service' => $provider, 'date' => date('Y-m-d'));
							$totalIvited = $obj->getTotalInviteOfDay($values);
						}
					}
					else
					{
						$this -> redirect($obj -> getConnectUrl() . '?scope=publish_stream,xmpp_login' . '&' . http_build_query(array('callbackUrl' => $importUrl)));
					}
					break;

				case'twitter' :
					if (!empty($_SESSION['socialbridge_session'][$provider]['access_token']) || $tokenDto)
					{
						if ($tokenDto)
						{
							$_SESSION['socialbridge_session'][$provider]['access_token'] = $tokenDto -> accessToken;
							$_SESSION['socialbridge_session'][$provider]['secret_token'] = $tokenDto -> secretToken;
							$_SESSION['socialbridge_session'][$provider]['owner_id'] = $tokenDto -> uid;
						}
						$params = $_SESSION['socialbridge_session'][$provider];
						$params['user_id'] = $params['owner_id'];
						$tmp_contacts = $obj -> getContacts($params);
						foreach ($tmp_contacts as $key => $value)
						{
							if(!YNCONTACTIMPORTER_BOL_InvitationService::getInstance() -> checkInvitedUser($key))
							{
								$contacts[$key] = $value;
							}
						}
						//get total invited
						$values = array('uid' => $params['user_id'], 'service' => $provider, 'date' => date('Y-m-d'));
						$totalIvited = $obj->getTotalInviteOfDay($values);
					}
					else
					{
						$this -> redirect($obj -> getConnectUrl() . '?' . http_build_query(array('callbackUrl' => $importUrl)));
					}
					break;

				case 'linkedin' :
					if (!empty($_SESSION['socialbridge_session'][$provider]['access_token']) || $tokenDto)
					{
						if ($tokenDto)
						{
							$_SESSION['socialbridge_session'][$provider]['access_token'] = $tokenDto -> accessToken;
							$_SESSION['socialbridge_session'][$provider]['secret_token'] = $tokenDto -> secretToken;
						}
						$params = $_SESSION['socialbridge_session'][$provider];
						$tmp_contacts = $obj -> getContacts($params);
						foreach ($tmp_contacts as $key => $value)
						{
							if(!YNCONTACTIMPORTER_BOL_InvitationService::getInstance() -> checkInvitedUser($key))
							{
								$contacts[$key] = $value;
							}
						}
						//get total invited
						$values = array('uid' => $obj->getOwnerId($params), 'service' => $provider, 'date' => date('Y-m-d'));
						$totalIvited = $obj->getTotalInviteOfDay($values);
					}
					else
					{
						$this -> redirect($obj -> getConnectUrl() . '?scope=r_network,w_messages&' . http_build_query(array('callbackUrl' => $importUrl)));
					}
					break;

				case 'yahoo_google_hotmail_csv' :
					$aContacts = $_POST['contact'];
					$gmailContacts = $aContacts;
					$aContacts = urldecode($aContacts);
					$aContacts = json_decode($aContacts);	
					foreach ($aContacts as $aContact)
					{
						//check email
						if($user = BOL_UserService::getInstance()->findByEmail($aContact -> email))
						{
							$service = FRIENDS_BOL_Service::getInstance();
							if($service->findFriendship($userId, $user->id) === null && $userId != $user->id)
							{
								$contacts_add[$aContact -> email] = $aContact -> name;
							}
						}
						else if(!YNCONTACTIMPORTER_BOL_InvitationService::getInstance() -> checkInvitedUser($aContact -> email))
						{
							$contacts[$aContact -> email] = $aContact -> name;
						}
					}
					break;
			}
			if(count($contacts) == 0)
			{
				OW::getFeedback()->warning(OW::getLanguage() -> text('yncontactimporter', 'no_contact'));
			}
			$this -> assign('showContacts', true);
			//check invite or add friends
			$component = new YNCONTACTIMPORTER_CMP_InviteFriends(array(
																'contacts'=>$contacts,
																'totalFriends' => $totalFriends,
																'totalFriendSearch' => $totalFriendSearch,
																'contacts_add'=>$contacts_add,
																'provider' => $provider, 
																'service'  => $_REQUEST['service'],
																'useSocialBridge'=> $useSocialBridge,
																'maxInvite' => $maxInvite,
																'totalInvited' => $totalIvited,
																'gmailContacts' => $gmailContacts));
			$this->addComponent('contactImports', $component);
		}
		else
		{
			$this -> assign('showContacts', false);
			$providers = YNCONTACTIMPORTER_BOL_ProviderService::getInstance() -> getAllProviders(array('enable' => 1));
			$arr_providers = array();
			foreach ($providers as $provider)
			{
				if (in_array($provider -> name, array(
					'facebook',
					'twitter',
					'linkedin'
				)))
				{
					$core = new YNCONTACTIMPORTER_CLASS_Core();
					if(!$core->checkSocialBridgePlugin($provider->name))
					{
						continue;
					}
				}
				$item = array();
				$item['title'] = $provider -> title;
				$item['name'] = $provider -> name;
				$item['id'] = $provider -> id;
				$item['logo'] = OW::getPluginManager() -> getPlugin('yncontactimporter') -> getStaticUrl() . "img/" . $provider -> name . ".png";
				$arr_providers[] = $item;
			}
			$this -> assign('providers', $arr_providers);
			$this -> assign("uploadCSVTitle", OW::getLanguage() -> text('yncontactimporter', 'upload_csv_file'));
			
			$this -> assign("customInviteTitle", OW::getLanguage() -> text('yncontactimporter', 'custom_invite'));
			$this -> assign("customInviteDescription", OW::getLanguage() -> text('yncontactimporter', 'custom_invite_description'));
    		$link = OW::getRequest()->buildUrlQueryString(OW::getRouter()->urlForRoute('yncontactimporter-user-join'), array('refId' => $userId));
			$this -> assign('urlInvite', $link);
			$this -> assign('authorization', OW::getLanguage() -> text('yncontactimporter', 'authorization'));
			$this -> assign('import_your_contacts', OW::getLanguage() -> text('yncontactimporter', 'import_your_contacts'));
			
			// get top inviter
			$this -> assign('topInviters', YNCONTACTIMPORTER_BOL_InvitationService::getInstance() -> getTopInviters());		
			
			// get statistic
			$this -> assign('statistics', YNCONTACTIMPORTER_BOL_InvitationService::getInstance() -> getStatistics());
			
			unset($_SESSION['ynfriends_checked']);
		}
	}

	// ajax delete contact
	public function ajaxResend()
	{
		if (!OW::getRequest() -> isAjax())
		{
			throw new Redirect404Exception();
		}

		if (OW::getRequest() -> isPost())
		{
			if(isset($_POST['id']) && $_POST['id'])
			{
				$id = $_POST['id'];
				$userId = OW::getUser() -> getId();
				$invitation = YNCONTACTIMPORTER_BOL_InvitationService::getInstance() -> findInvitationById($id);
				if($invitation)
				{
					$displayName = BOL_UserService::getInstance()->getDisplayName($userId);
					$vars = array(
			            'inviter' => $displayName,
			            'siteName' => OW::getConfig()->getValue('base', 'site_name'),
			            'customMessage' => $invitation -> message
			        );
					$link = OW::getRequest()->buildUrlQueryString(OW::getRouter()->urlForRoute('yncontactimporter-user-join'), array('refId' => $userId));
	                $vars['siteInviteURL'] = $link;     
                    $mail = OW::getMailer()->createMail();
		            $mail->setSubject(OW::getLanguage()->text('yncontactimporter', 'mail_email_invite_subject', $vars));
		            $mail->setHtmlContent(OW::getLanguage()->text('yncontactimporter', 'mail_email_invite_msg_html', $vars));
		            $mail->setTextContent(OW::getLanguage()->text('yncontactimporter', 'mail_email_invite_msg_txt' , $vars));
		            $mail->addRecipientEmail($invitation -> friendId);
					YNCONTACTIMPORTER_BOL_PendingService::getInstance()->savePending($mail);
					exit(json_encode($result['success'] = TRUE));
				}
			}
		}
	}
	// ajax delete contact
	public function ajaxDelete()
	{
		if (!OW::getRequest() -> isAjax())
		{
			throw new Redirect404Exception();
		}

		if (OW::getRequest() -> isPost())
		{
			if(isset($_POST['id']) && $_POST['id'])
			{
				$id = $_POST['id'];
				YNCONTACTIMPORTER_BOL_InvitationService::getInstance() -> deleteInvitationById($id);
			}
			exit(json_encode($result['success'] = TRUE));
		}
	}
	//send invite
	public function invite()
	{
		if (!OW::getUser() -> isAuthenticated())
		{
			throw new AuthenticateException();
		}
		if ( !OW::getUser()->isAuthorized('yncontactimporter', 'invite') )
        {
            $this->setTemplate(OW::getPluginManager()->getPlugin('base')->getCtrlViewDir() . 'authorization_failed.html');
            return;
        }
		if (OW::getRequest() -> isPost())
		{
			try
			{
				//start invite
				$values = $_POST;
				$provider = $values['provider'];
				$userId = OW::getUser() -> getId();
				$selected_contacts = array();
				$max_invitation = OW::getConfig() -> getValue('yncontactimporter', 'max_invite_per_times');
				if(!$max_invitation)
				{
					$max_invitation = 10;
				}
				$aFriendIdSelected = explode(',', $values['friendIds']);
				$aFriendNameSelected = explode(',', $values['friendNames']);
				foreach ($aFriendIdSelected as $key => $val)
				{
					if ($val)
					{
						if($provider == "myspace")
						{
							$user_id =  $val;
							$strpos = strpos($user_id, 'friendid=');
							if($strpos)
								$user_id = substr($user_id, $strpos + 9);
							$selected_contacts[$user_id] = $aFriendNameSelected[$key];
						}
						else 
						{
							$selected_contacts[$val] = $aFriendNameSelected[$key];
						}
						if (--$max_invitation < 1)
							break;
					}
				}
				if (count($selected_contacts) == 0)
				{
					OW::getFeedback()->warning(OW::getLanguage() -> text('yncontactimporter', 'no_contact_selected'));
					$this->redirect(OW::getRouter() -> urlForRoute('yncontactimporter-import'));
				}
				// ADD INVITE HERE
				$message = $values['message'];
				$message = trim($message);

				if (is_array($selected_contacts) && !empty($selected_contacts))
				{				
            		$link = OW::getRequest()->buildUrlQueryString(OW::getRouter()->urlForRoute('yncontactimporter-user-join'), array('refId' => $userId));
					$obj = null;
					if (in_array($provider, array(
						'facebook',
						'twitter',
						'linkedin'
					)))
					{
						$core = new YNSOCIALBRIDGE_CLASS_Core();
						$obj = $core -> getInstance($provider);
						$tokenDto = null;
						if (empty($_SESSION['socialbridge_session'][$provider]))
						{
							$values = array(
								'service' => $provider,
								'userId' => OW::getUser() -> getId()
							);
							$tokenDto = $obj -> getToken($values);
						}
					}
					
					switch($values['provider'])
					{
						case 'twitter':
							if (!empty($_SESSION['socialbridge_session'][$provider]) || $tokenDto)
							{
								if ($tokenDto)
								{
									$_SESSION['socialbridge_session'][$provider]['access_token'] = $tokenDto -> accessToken;
									$_SESSION['socialbridge_session'][$provider]['secret_token'] = $tokenDto -> secretToken;
									$_SESSION['socialbridge_session'][$provider]['owner_id'] = $tokenDto -> uid;
								}
								$params = $_SESSION['socialbridge_session'][$provider];
								$params['list'] = $selected_contacts;
								$params['link'] = $link;
								$params['message'] = $message;
								$params['user_id'] =  OW::getUser() -> getId();
								$params['uid'] =  $_SESSION['socialbridge_session'][$provider]['owner_id'];
								$obj -> sendInvites($params);
								YNCONTACTIMPORTER_BOL_InvitationService::getInstance() -> addInvitations($userId, 'social', $values['provider'], $selected_contacts, $message);
							}
							break;
						case 'linkedin' :
							if (!empty($_SESSION['socialbridge_session'][$provider]) || $tokenDto)
							{
								if ($tokenDto)
								{
									$_SESSION['socialbridge_session'][$provider]['access_token'] = $tokenDto -> accessToken;
									$_SESSION['socialbridge_session'][$provider]['secret_token'] = $tokenDto -> secretToken;
								}
								$params = $_SESSION['socialbridge_session'][$provider];
								$params['list'] = $selected_contacts;
								$params['link'] = $link;
								$params['message'] = $message;
								$params['user_id'] = OW::getUser() -> getId();
								$params['uid'] =  $obj->getOwnerId($_SESSION['socialbridge_session'][$provider]);
								$obj ->sendInvites($params);
								YNCONTACTIMPORTER_BOL_InvitationService::getInstance() -> addInvitations($userId, 'social', $values['provider'], $selected_contacts, $message);
							}
							break;
						case 'facebook' :
							if (!empty($_SESSION['socialbridge_session'][$provider]) || $tokenDto)
							{
								if ($tokenDto)
								{
									$_SESSION['socialbridge_session'][$provider]['access_token'] = $tokenDto -> accessToken;
								}
								
								$params['list'] = $selected_contacts;
								$params['link'] = $link;
								$params['message'] = $message;
								$params['uid'] = $obj->getOwnerId(array('access_token' => $_SESSION['socialbridge_session']['facebook']['access_token']));
								$params['user_id'] =  OW::getUser() -> getId();
								$params['access_token'] =  $_SESSION['socialbridge_session']['facebook']['access_token'];
								$obj ->sendInvites($params);
								YNCONTACTIMPORTER_BOL_InvitationService::getInstance() -> addInvitations($userId, 'social', $values['provider'], $selected_contacts, $message);
							}
							break;
						// Send mail with oxwall mail
						case 'gmail':
						case 'yahoo':
						case 'hotmail':
						case 'mail2world':
						case 'File CSV':
        					$displayName = BOL_UserService::getInstance()->getDisplayName($userId);
							$vars = array(
					            'inviter' => $displayName,
					            'siteName' => OW::getConfig()->getValue('base', 'site_name'),
					            'customMessage' => $message
					        );
							
							foreach ($selected_contacts as $email => $name)
			                {
			                	$vars['siteInviteURL'] = $link;                    
			                    $mail = OW::getMailer()->createMail();
					            $mail->setSubject(OW::getLanguage()->text('yncontactimporter', 'mail_email_invite_subject', $vars));
					            $mail->setHtmlContent(OW::getLanguage()->text('yncontactimporter', 'mail_email_invite_msg_html', $vars));
					            $mail->setTextContent(OW::getLanguage()->text('yncontactimporter', 'mail_email_invite_msg_txt' , $vars));
					            $mail->addRecipientEmail($email);
								YNCONTACTIMPORTER_BOL_PendingService::getInstance()->savePending($mail);
			                }
							YNCONTACTIMPORTER_BOL_InvitationService::getInstance() -> addInvitations($userId, 'email', $values['provider'], $selected_contacts, $message);
							break;
					}
				}
				unset($_SESSION['ynfriends_checked']);
				// END INVITE
				OW::getFeedback()->info(OW::getLanguage() -> text('yncontactimporter', 'invite_successfully'));
				$this->redirect(OW::getRouter() -> urlForRoute('yncontactimporter-import'));
				exit;
			}
			catch(Exception $e)
			{
				echo $e -> getMessage(). $e -> getFile(). $e -> getLine(); die;
				OW::getFeedback()->warning(OW::getLanguage() -> text('yncontactimporter', 'invite_failed'));
				$this->redirect(OW::getRouter() -> urlForRoute('yncontactimporter-import'));
				exit;
			}
		}
		else 
		{
			$this->redirect(OW::getRouter() -> urlForRoute('yncontactimporter-import'));
			exit;
		}
	}
	// manage pending emails
	public function pending()
	{
		$this->menu->getElement('4')->setActive(true);
		OW::getDocument()->setTitle(OW::getLanguage()->text('yncontactimporter', 'meta_title_invite_pending_invitation'));
        OW::getDocument()->setDescription(OW::getLanguage()->text('yncontactimporter', 'meta_description_invite_import'));
		if (!OW::getUser() -> isAuthenticated())
		{
			throw new AuthenticateException();
		}
		if ( !OW::getUser()->isAuthorized('yncontactimporter', 'invite') )
        {
            $this->setTemplate(OW::getPluginManager()->getPlugin('base')->getCtrlViewDir() . 'authorization_failed.html');
            return;
        }
		$userId = OW::getUser()->getId();
		if (OW::getRequest() -> isPost())
		{
			if(isset($_POST['resend']) || isset($_POST['delete']))
			{
				try
				{
					foreach ($_POST as $key => $val)
					{
						if (strpos($key, 'check_') !== false)
						{
							if(isset($_POST['delete']) && $_POST['delete'])
							{
								YNCONTACTIMPORTER_BOL_InvitationService::getInstance()->deleteInvitationById($val);
							}
							else 
							{
								$invitation = YNCONTACTIMPORTER_BOL_InvitationService::getInstance() -> findInvitationById($val);
								if($invitation && $invitation -> type == 'email')
								{
									$displayName = BOL_UserService::getInstance()->getDisplayName($userId);
									$vars = array(
							            'inviter' => $displayName,
							            'siteName' => OW::getConfig()->getValue('base', 'site_name'),
							            'customMessage' => $invitation -> message
							        );
									$link = OW::getRequest()->buildUrlQueryString(OW::getRouter()->urlForRoute('yncontactimporter-user-join'), array('refId' => $userId));
					                $vars['siteInviteURL'] = $link;     
				                    $mail = OW::getMailer()->createMail();
						            $mail->setSubject(OW::getLanguage()->text('yncontactimporter', 'mail_email_invite_subject', $vars));
						            $mail->setHtmlContent(OW::getLanguage()->text('yncontactimporter', 'mail_email_invite_msg_html', $vars));
						            $mail->setTextContent(OW::getLanguage()->text('yncontactimporter', 'mail_email_invite_msg_txt' , $vars));
						            $mail->addRecipientEmail($invitation -> friendId);
									YNCONTACTIMPORTER_BOL_PendingService::getInstance()->savePending($mail);
								}
							}
						}
					}
					if(isset($_POST['delete']) && $_POST['delete'])
					{
						OW::getFeedback()->info(OW::getLanguage() -> text('yncontactimporter', 'message_delete_completed'));
					}
					else {
						OW::getFeedback()->info(OW::getLanguage() -> text('yncontactimporter', 'message_resend_completed'));
					}
				}
				catch(Exception $e)
				{
				}
			}
		}
		$rpp = (int) OW::getConfig() -> getValue('yncontactimporter', 'contact_per_page');
		$page = (!empty($_GET['page']) && intval($_GET['page']) > 0 ) ? $_GET['page'] : 1;
		$first = ($page - 1) * $rpp;
        $count = $rpp;
		$search = '';
		if(isset($_REQUEST['search']))
		{
			$search = $_REQUEST['search'];
		}
		$params = array('userId' => $userId, 'first' => $first, 'count' => $count, 'search' => $search);
		$list = YNCONTACTIMPORTER_BOL_InvitationService::getInstance()->getInvitationsByUserId($params);
		$itemsCount = YNCONTACTIMPORTER_BOL_InvitationService::getInstance()->countInvitationsByUserId($params);
		$this->assign('invitations', $list);

		$paging = new BASE_CMP_Paging($page, ceil($itemsCount / $rpp), 5);
        $this->addComponent('paging', $paging);
		
		$this -> assign('currentSearch', !empty($_REQUEST['search']) ? htmlspecialchars($_REQUEST['search']) : '');
		$this->assign('totalSearch', $itemsCount);
		$this -> assign('warningNoContactSelected', OW::getLanguage() -> text('yncontactimporter', 'no_contacts_selected'));
		$this -> assign('confirmDeleteSelected', OW::getLanguage() -> text('yncontactimporter', 'confirm_delete_selected'));
		$this -> assign('confirmDeleteContact', OW::getLanguage() -> text('yncontactimporter', 'confirm_delete_contact'));
		$this -> assign('messageResendCompleted', OW::getLanguage() -> text('yncontactimporter', 'message_resend_completed'));
		$this->assign('deleteURL', OW::getRouter() -> urlForRoute('yncontactimporter-ajax-delete'));
		$this->assign('resendURL', OW::getRouter() -> urlForRoute('yncontactimporter-ajax-resend'));
	}
	// manage queue emails
	public function emailQueue()
	{
		$this->menu->getElement('2')->setActive(true);
		OW::getDocument()->setTitle(OW::getLanguage()->text('yncontactimporter', 'meta_title_invite_queue_email'));
        OW::getDocument()->setDescription(OW::getLanguage()->text('yncontactimporter', 'meta_description_invite_import'));
		if (!OW::getUser() -> isAuthenticated())
		{
			throw new AuthenticateException();
		}
		if ( !OW::getUser()->isAuthorized('yncontactimporter', 'invite') )
        {
            $this->setTemplate(OW::getPluginManager()->getPlugin('base')->getCtrlViewDir() . 'authorization_failed.html');
            return;
        }
		if (OW::getRequest() -> isPost())
		{
			try
			{
				foreach ($_POST as $key => $val)
				{
					if (strpos($key, 'check_') !== false)
					{
						YNCONTACTIMPORTER_BOL_PendingService::getInstance()->deleteEmailById($val);
					}
				}
			}
			catch(Exception $e)
			{
			}
		}
		$userId = OW::getUser()->getId();
		$rpp = (int) OW::getConfig() -> getValue('yncontactimporter', 'contact_per_page');
		$page = (!empty($_GET['page']) && intval($_GET['page']) > 0 ) ? $_GET['page'] : 1;
		$first = ($page - 1) * $rpp;
        $count = $rpp;
		$search = '';
		if(isset($_REQUEST['search']))
		{
			$search = $_REQUEST['search'];
		}
		$params = array('userId' => $userId, 'first' => $first, 'count' => $count, 'search' => $search);
		$list = YNCONTACTIMPORTER_BOL_PendingService::getInstance()->getAllPendingEmailsByUserId($params);
		$itemsCount = YNCONTACTIMPORTER_BOL_PendingService::getInstance()->countPendingEmailsByUserId($params);
		$this->assign('emails', $list);

		$paging = new BASE_CMP_Paging($page, ceil($itemsCount / $rpp), 5);
        $this->addComponent('paging', $paging);
		
		$this -> assign('currentSearch', !empty($_REQUEST['search']) ? htmlspecialchars($_REQUEST['search']) : '');
		$this->assign('totalSearch', $itemsCount);
		$this -> assign('warningNoContactSelected', OW::getLanguage() -> text('yncontactimporter', 'no_contacts_selected'));
		$this -> assign('confirmDeleteSelected', OW::getLanguage() -> text('yncontactimporter', 'confirm_delete_selected'));
		$this -> assign('confirmDeleteContact', OW::getLanguage() -> text('yncontactimporter', 'confirm_delete_contact'));
	}
	// manage queue messages
	public function socialQueue()
	{
		$importUrl = OW::getRouter() -> urlForRoute('yncontactimporter-import');
		if(!OW::getPluginManager()->isPluginActive('ynsocialbridge'))
		{
			$this->redirect($importUrl);
		}
		$this->menu->getElement('3')->setActive(true);
		OW::getDocument()->setTitle(OW::getLanguage()->text('yncontactimporter', 'meta_title_invite_queue_message'));
        OW::getDocument()->setDescription(OW::getLanguage()->text('yncontactimporter', 'meta_description_invite_import'));
		if (!OW::getUser() -> isAuthenticated())
		{
			throw new AuthenticateException();
		}
		if ( !OW::getUser()->isAuthorized('yncontactimporter', 'invite') )
        {
            $this->setTemplate(OW::getPluginManager()->getPlugin('base')->getCtrlViewDir() . 'authorization_failed.html');
            return;
        }
		if (OW::getRequest() -> isPost())
		{
			try
			{
				foreach ($_POST as $key => $val)
				{
					if (strpos($key, 'check_') !== false)
					{
						YNSOCIALBRIDGE_BOL_QueueService::getInstance()->deleteContactById($val);
					}
				}
			}
			catch(Exception $e)
			{
			}
		}
		$userId = OW::getUser()->getId();
		$rpp = (int) OW::getConfig() -> getValue('yncontactimporter', 'contact_per_page');
		$page = (!empty($_GET['page']) && intval($_GET['page']) > 0 ) ? $_GET['page'] : 1;
		$first = ($page - 1) * $rpp;
        $count = $rpp;
		$search = '';
		if(isset($_REQUEST['search']))
		{
			$search = $_REQUEST['search'];
		}
		$params = array('userId' => $userId, 'type' => 'sendInvite', 'first' => $first, 'count' => $count, 'search' => $search);
		$list = YNSOCIALBRIDGE_BOL_QueueService::getInstance()->getQueuesByUserId($params);
		$itemsCount = YNSOCIALBRIDGE_BOL_QueueService::getInstance()->countQueuesByUserId($params);
		
		$tmp_list = array();
		foreach ($list as $value) 
		{
			$arr_id = $arr = explode('/', $value['id']);
			$value['newId'] = $arr_id[0];
			$tmp_list[] = $value;
		}
		
		$this->assign('contacts', $tmp_list);

		$paging = new BASE_CMP_Paging($page, ceil($itemsCount / $rpp), 5);
        $this->addComponent('paging', $paging);
		
		$this -> assign('currentSearch', !empty($_REQUEST['search']) ? htmlspecialchars($_REQUEST['search']) : '');
		$this->assign('totalSearch', $itemsCount);
		$this -> assign('warningNoContactSelected', OW::getLanguage() -> text('yncontactimporter', 'no_contacts_selected'));
		$this -> assign('confirmDeleteSelected', OW::getLanguage() -> text('yncontactimporter', 'confirm_delete_selected'));
		$this -> assign('confirmDeleteContact', OW::getLanguage() -> text('yncontactimporter', 'confirm_delete_contact'));
	}
	private function onRequest( $userId )
    {
        $requesterId = OW::getUser()->getId();

        $reqr = BOL_UserService::getInstance()->findUserById($requesterId);
        $displayName = BOL_UserService::getInstance()->getDisplayName($reqr->getId());
        $userUrl = BOL_UserService::getInstance()->getUserUrl($reqr->getId());
        $event = new OW_Event('friends.request-sent', array(
                'senderId' => $requesterId,
                'recipientId' => $userId,
                'time' => time()
        ));

        OW::getEventManager()->trigger($event);
    }
	public function upload()
	{
		if (!OW::getUser() -> isAuthenticated())
		{
			throw new AuthenticateException();
		}
		if ( !OW::getUser()->isAuthorized('yncontactimporter', 'invite') )
        {
            $this->setTemplate(OW::getPluginManager()->getPlugin('base')->getCtrlViewDir() . 'authorization_failed.html');
            return;
        }
		if (OW::getRequest() -> isPost())
		{
			try
			{
				$core = new YNCONTACTIMPORTER_CLASS_Core();
				$import_result = $core->uploadContactFile();
				if(!$import_result['is_error'])
				{
					$contacts = $import_result['contacts'];
					foreach($contacts as $email => $name)
					{
						$contacts_new[] = array('name'=> $name, 'email' => $email);
					}
					$this->assign('contacts', urlencode(json_encode($contacts_new)));
					$this->assign('actionURL', OW::getRouter() -> urlForRoute('yncontactimporter-import'));
				}
				else 
				{
					OW::getFeedback()->error($import_result['error_message']);
					$this->redirect(OW::getRouter() -> urlForRoute('yncontactimporter-import'));
				}
			}
			catch(Exception $e)
			{
			}
		}
	}
	// check when click invitation link
	public function click()
	{
		$user_id = $_REQUEST['refId'];
		if ($user_id)
		{
			$expired = 7 * 86400 + time();
			setcookie('yncontactimporter_userId', $user_id, $expired, '/');
		}
		$this->redirect(OW::getRouter() -> urlForRoute('base_join'));
	}
}
