<?php
/**
 * User console component class.
 *
 * @author trunglt
 * @package ow.ow_system_plugins.base.components
 * @since 1.01
 */
class YNSOCIALPUBLISHER_CMP_Popup extends OW_Component
{
    public function __construct($pluginKey, $entityType, $entityId)
    {
		// remove newfeeds cookie if has
        setcookie('ynsocialpublisher_feed_data_' . $entityId, '', -1, '/');

        parent::__construct();

        $userId = OW::getUser()->getId();
        $language = OW::getLanguage();
        $service = YNSOCIALPUBLISHER_BOL_Service::getInstance();
        $core = YNSOCIALPUBLISHER_CLASS_Core::getInstance();

        // user setting
        $userSetting = $service->getUsersetting($userId, $pluginKey);
        $this->assign('userSetting', $userSetting);

        // avatar
        $avatar = BOL_AvatarService::getInstance()->getAvatarUrl($userId);

        if ( empty($avatar) )
        {
            $avatar = BOL_AvatarService::getInstance()->getDefaultAvatarUrl();
        }
        $this->assign('avatar', $avatar);

        // default status
        $defaultStatus = $core->getDefaultStatus($pluginKey, $entityType, $entityId);
        $this->assign('defaultStatus', $defaultStatus);

        // entity url
        $url = $core->getUrl($pluginKey, $entityType, $entityId);
        $this->assign('url', $url);

        // title
        $title = $core->getTitle($pluginKey, $entityType, $entityId);
        $this->assign('title', $title);

		// -- connect each provider to get data
        // callbackUrl
        $callbackUrl = OW::getRouter() -> urlForRoute('ynsocialbridge-connects');
        $coreBridge = new YNSOCIALBRIDGE_CLASS_Core();

        $arrObjServices = array();
		
        foreach (array('facebook', 'twitter', 'linkedin') as $serviceName)
        {
            $profile = null;
            $connect_url = "";
			$access_token = "";
			$scope = '';
 			$objService = array();
			//check enable API
			$clientConfig = YNSOCIALBRIDGE_BOL_ApisettingService::getInstance() -> getConfig($serviceName);
			if ($clientConfig)
			{
				$obj = $coreBridge -> getInstance($serviceName);
				$values = array(
					'service' => $serviceName,
					'userId' => OW::getUser() -> getId()
				);
				//$tokenDto = $obj -> getToken($values);
				//print_r($_SESSION['socialbridge_session'][$serviceName]);die;
				$disconnect_url = OW::getRouter() -> urlForRoute('ynsocialbridge-disconnect') . "?service=" . $serviceName;
				if (!empty($_SESSION['socialbridge_session'][$serviceName]['access_token']))
				{
					if($serviceName == 'facebook')
					{
						$access_token = $_SESSION['socialbridge_session'][$serviceName]['access_token'];
						//check permission
                		$me = $obj->getOwnerInfo(array('access_token' => $access_token));
                		$uid = $me['id'];
                		$permissions = $obj->hasPermission(array(
        						'uid' => $uid,
            					'access_token' => $access_token
                			));
						if($permissions)
						{
							if (empty($permissions[0]['publish_stream']) || empty($permissions[0]['status_update'])) 
	                		{
	                			$scope = 'publish_stream,status_update';
								//$scope = "email,user_about_me,user_birthday,user_hometown,user_interests,user_location,user_photos,user_website,publish_stream,status_update";
	                		}
							else 
							{
								try
								{
									$profile = $obj -> getOwnerInfo($_SESSION['socialbridge_session'][$serviceName]);
								}
								catch(Exception $e)
								{
									$profile = null;
								}
							}
						}
					}
					else
					{
						$profile = $obj -> getOwnerInfo($_SESSION['socialbridge_session'][$serviceName]);	
					}
					
				}
				/*
				elseif ($tokenDto)
				{
					if($serviceName == 'facebook')
					{
						$permissions = $obj -> hasPermission(array(
								'uid' => $tokenDto -> uid,
								'access_token' => $tokenDto -> accessToken
							));
						if($permissions)
						{
							if (empty($permissions[0]['publish_stream']) || empty($permissions[0]['status_update'])) 
	                		{
	                			$scope = 'publish_stream,status_update';
								//$scope = "email,user_about_me,user_birthday,user_hometown,user_interests,user_location,user_photos,user_website,publish_stream,status_update";
	                		}
							else 
							{
								$profile = @$obj -> getOwnerInfo(array(
									'access_token' => $tokenDto -> accessToken,
									'secret_token' => $tokenDto -> secretToken,
									'user_id' => $tokenDto -> uid
								));
							}
						}
						else 
						{
							YNSOCIALBRIDGE_BOL_TokenService::getInstance() -> delete($tokenDto);
							//$scope = "email,user_about_me,user_birthday,user_hometown,user_interests,user_location,user_photos,user_website";
							$scope = 'publish_stream,status_update';
							$connect_url = $obj -> getConnectUrl() . "?scope=" . $scope . "&" . http_build_query(array('callbackUrl' => $callbackUrl));
						}
					}
					else 
					{
						$profile = @$obj -> getOwnerInfo(array(
							'access_token' => $tokenDto -> accessToken,
							'secret_token' => $tokenDto -> secretToken,
							'user_id' => $tokenDto -> uid
						));
					}
				}*/
				else
				{
					$scope = "";
					switch ($serviceName)
					{
						case 'facebook' :
							//$scope = "email,user_about_me,user_birthday,user_hometown,user_interests,user_location,user_photos,user_website";
							$scope = 'publish_stream,status_update';
							break;

						case 'twitter' :
							$scope = "";
							break;

						case 'linkedin' :
							$scope = "r_basicprofile,rw_nus,r_network,w_messages";
							break;
					}
					
				}
				$connect_url = $obj -> getConnectUrl() . "?scope=" . $scope . "&" . http_build_query(array(
                  'callbackUrl' => $callbackUrl,
                   'isFromSocialPublisher' => 1,
                   'pluginKey' => $pluginKey,
                   'entityType' => $entityType,
                   'entityId' => $entityId
                 ));
				$objService['has_config'] = 1;
			}
			else {
				$objService['has_config'] = 0;	
			}
			
			$objService['serviceName'] = $serviceName;
			$objService['connectUrl'] = $connect_url;
			$objService['disconnectUrl'] = $disconnect_url;
			$objService['profile'] = $profile;
			$objService['logo'] = OW::getPluginManager() -> getPlugin('ynsocialpublisher') -> getStaticUrl() . "img/" . $serviceName . ".png";
            $arrObjServices[$serviceName] = $objService;
        }
		//print_r($userSetting);
		//print_r($arrObjServices);
        $this -> assign('arrObjServices', $arrObjServices);
		
        // create form
        $formUrl = OW::getRouter()->urlFor('YNSOCIALPUBLISHER_CTRL_Ynsocialpublisher', 'ajaxPublish');
        $form = new Form('ynsocialpubisher_share');
        $form->setAction($formUrl);
        $form->setAjax();

        // -- hidden fields
        // for plugin key
        $pluginKeyHiddenField = new HiddenField('ynsocialpublisher_pluginKey');
        $pluginKeyHiddenField->setValue($pluginKey);
        $form->addElement($pluginKeyHiddenField);
        // for entity id
        $entityIdHiddenField = new HiddenField('ynsocialpublisher_entityId');
        $entityIdHiddenField->setValue($entityId);
        $form->addElement($entityIdHiddenField);
        // for entity type
        $entityTypeHiddenField = new HiddenField('ynsocialpublisher_entityType');
        $entityTypeHiddenField->setValue($entityType);
        $form->addElement($entityTypeHiddenField);

        // Status - textarea
        $status = new Textarea('ynsocialpublisher_status');
        $status->setValue($defaultStatus);
        $form->addElement($status);

        // Options - radio buttons
        $options = new RadioField('ynsocialpublisher_options');
        $options->setRequired();
        $options->addOptions(
            array(
                '0' => $language->text('ynsocialpublisher', 'ask'),
                '1' => $language->text('ynsocialpublisher', 'auto'),
                '2' => $language->text('ynsocialpublisher', 'not_ask')
            )
        );
        $options->setValue($userSetting['option']);
        $form->addElement($options);
        // Providers - checkboxes
        foreach (array('facebook', 'twitter', 'linkedin') as $provider)
        {
			if (in_array($provider, $userSetting['adminProviders']) && $arrObjServices[$provider]['has_config'])
        	{
				$providerField = new CheckboxField('ynsocialpublisher_' . $provider);
		        $form->addElement($providerField);
			}
        }
        // add js action to form
        $form->bindJsFunction(Form::BIND_SUCCESS, 'function(data){if( data.result ){OW.info(data.message);OWActiveFloatBox.close();}else{OW.error(data.message);}}');
		// submit button
        $submit = new Submit('submit');
        $submit->setValue(OW::getLanguage()->text('ynsocialpublisher', 'submit_label'));
        $form->addElement($submit);
        $this->addForm($form);
		// assign to view
        $this->assign('formUrl', $formUrl);
    }
}