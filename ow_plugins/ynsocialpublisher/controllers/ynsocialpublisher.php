<?php

/**
 * Social Publisher
 *
 * @author trunglt
 * @package ow_plugins.ynsocialpublisher.controllers
 * @since 1.01
 */
class YNSOCIALPUBLISHER_CTRL_Ynsocialpublisher extends OW_ActionController
{
    private $_menu;

    public function __construct()
    {
        $this -> setPageHeading(OW::getLanguage() -> text('ynsocialbridge', 'socialbridge_management_page_heading'));
        $this -> setPageHeadingIconClass('ow_yn_socialbridge');

        if (!OW::getUser()->isAuthenticated())
        {
            throw new AuthenticateException();
        }
        //Preference menu
        $contentMenu = new BASE_CMP_PreferenceContentMenu();
        $contentMenu -> getElement('socialbridge') -> setActive(true);
        $this -> addComponent('contentMenu', $contentMenu);

        $core = new YNSOCIALBRIDGE_CLASS_Core();
        $this->_menu = $core->initMenu();
        $this -> addComponent('menu', $this->_menu);

        //load css
        $cssUrl = OW::getPluginManager() -> getPlugin('ynsocialbridge') -> getStaticCssUrl() . 'ynsocialbridge.css';
        OW::getDocument() -> addStyleSheet($cssUrl);
    }

    public function index()
    {
        $service = YNSOCIALPUBLISHER_BOL_Service::getInstance();
        $core = YNSOCIALPUBLISHER_CLASS_Core::getInstance();
        $userId = OW::getUser()->getId();

        $pluginKeys = $core->getSupportedPluginKeys();
        // plugin settings of user
        $settings = array();
        foreach ($pluginKeys as $key)
        {
            $userSetting = $service->getUsersetting($userId, $key);
            if (!empty($userSetting) && count($userSetting['adminProviders']) > 0)
            {
                $settings[$key] = $userSetting;
            }
        }

        $form_url = OW::getRouter()->urlForRoute('ynsocialbridge-publisher-settings');
        // add CSS file
        OW::getDocument()->addStyleSheet(OW::getPluginManager() -> getPlugin('ynsocialpublisher')->getStaticCssUrl() . 'ynsocialpublisher.css');

        $this->assign('settings', $settings);
        $this->assign('form_url', $form_url);

        if (OW::getRequest()->isPost()) {
            $params = $_POST['params'];
            foreach ($params as $key => $values)
            {
                $usersettingDto = $service->findByUserIdOrKey($userId, $key);
                if (!$usersettingDto)
                {
                    $usersettingDto = new YNSOCIALPUBLISHER_BOL_Usersetting();
                    $usersettingDto->userId = $userId;
                    $usersettingDto->key = $key;
                }
                $usersettingDto->option = $values['option'];
                if (!isset($values['providers']))
                {
                    $values['providers'] = array();
                }
                $usersettingDto->providers = json_encode($values['providers']);
                $service->saveOrUpdateUsersetting($usersettingDto);
            }
            OW::getFeedback()->info(OW::getLanguage()->text('ynsocialpublisher', 'settings_updated'));
            $this->redirect($form_url);
        }
    }

    public function processAjaxPublish($pluginKey, $entityId, $entityType, $providers, $status)
    {
        $userId = OW::getUser()->getId();
        $core = YNSOCIALPUBLISHER_CLASS_Core::getInstance();

        $postData = $core->getPostData($pluginKey, $entityId, $entityType, $providers, $status);
        $coreBridge = new YNSOCIALBRIDGE_CLASS_Core();

        $language = OW::getLanguage();
        $responseMessage = array();
        foreach($providers as $provider)
        {
        	if (isset($postData[$provider]))
        	{
	            $obj = $coreBridge -> getInstance($provider);
	            try
	            {
	                $postStatus = $obj->postActivity($postData[$provider]);
	                if ($postStatus != true)
	                {
	                    $responseMessage[] = sprintf($language->text('ynsocialpublisher', 'Can not publish to %s'), ucfirst($provider));
	                }
	            } catch(Exception $e) {
	                //echo $e->getMessage();
	            }
        	}
        }
        return $responseMessage;
    }

    public function ajaxPublish()
    {
       if ( !OW::getRequest()->isAjax() )
        {
            throw new Redirect404Exception();
        }

        if ( OW::getRequest()->isPost() )
        {
            $language = OW::getLanguage();
            try
            {
                $preText = 'ynsocialpublisher_';
                $pluginKey = $_POST["{$preText}pluginKey"];
                $entityId = $_POST["{$preText}entityId"];
                $entityType = $_POST["{$preText}entityType"];
                $status = (!empty($_POST["{$preText}status"]))?$_POST["{$preText}status"]:'';
                $options = $_POST["{$preText}options"];
                $facebook = isset($_POST["{$preText}facebook"])?$_POST["{$preText}facebook"]:'';
                $twitter = isset($_POST["{$preText}twitter"])?$_POST["{$preText}twitter"]:'';
                $linkedin = isset($_POST["{$preText}linkedin"])?$_POST["{$preText}linkedin"]:'';

                $providers = array();
                if ($facebook == 'on')
                {
                    $providers[] = 'facebook';
                }
                if ($twitter == 'on')
                {
                    $providers[] = 'twitter';
                }
                if ($linkedin == 'on')
                {
                    $providers[] = 'linkedin';
                }
                $result = array();
                if (count($providers) > 0)
                {
                    $result = $this->processAjaxPublish($pluginKey, $entityId, $entityType, $providers, $status);
                }
                else
                {
                    //exit(json_encode(array('result' => false, 'message' => $language->text('ynsocialpublisher','ajax_error_providers'))));
                }
                // save user settings
                $userId = OW::getUser()->getId();
                $service = YNSOCIALPUBLISHER_BOL_Service::getInstance();
                $usersettingDto = $service->findByUserIdOrKey($userId, $pluginKey);
                if (!$usersettingDto)
                {
                    $usersettingDto = new YNSOCIALPUBLISHER_BOL_Usersetting();
                    $usersettingDto->userId = $userId;
                    $usersettingDto->key = $pluginKey;
                }
                $usersettingDto->option = $options;
                $usersettingDto->providers = json_encode($providers);
                $service->saveOrUpdateUsersetting($usersettingDto);
            }
            catch ( LogicException $e )
            {
                exit(json_encode(array('result' => false, 'message' => $language->text('ynsocialpublisher','ajax_error'))));
            }

           $message = implode(';', $result);

            if (empty($message))
            {
                exit(json_encode(array('result' => true, 'message' =>  $language->text('ynsocialpublisher', 'ajax_successful'))));
            }
            else
            {
                exit(json_encode(array('result' => true, 'message' => $message)));
            }

            exit(json_encode(array()));
        }

        exit(json_encode(array()));
    }

    public function connect()
    {
    }
}