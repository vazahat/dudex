<?php

/**
 * Copyright (c) 2013, Oxwall CandyStore
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * Site stats administration action controller
 * 
 * @author Oxwall CandyStore <plugins@oxcandystore.com>
 * @package ow.ow_plugins.ocs_sitestats.controllers
 * @since 1.5
 */
class OCSSITESTATS_CTRL_Admin extends ADMIN_CTRL_Abstract
{    
    /**
     * Default action
     */
    public function index()
    {
        $lang = OW::getLanguage();
                
        OW::getDocument()->setHeading($lang->text('ocssitestats', 'admin_page_heading'));
        OW::getDocument()->setHeadingIconClass('ow_ic_gear_wheel');
        
        $pluginManager = OW::getPluginManager();
        $pluginsActivated = array(
        	'total_users' => true, 
        	'online_users' => true, 
        	'new_users_today' => true, 
        	'new_users_this_month' => true,
			'photos' => $pluginManager->isPluginActive('photo'), 
			'videos' => $pluginManager->isPluginActive('video'), 
			'blogs' => $pluginManager->isPluginActive('blogs'), 
			'groups' => $pluginManager->isPluginActive('groups'), 
			'events' => $pluginManager->isPluginActive('event'),
			'discussions' => $pluginManager->isPluginActive('forum'), 
			'links' => $pluginManager->isPluginActive('links')
        );
        
        $config = OW::getConfig();

        if ( OW::getRequest()->isPost() && !empty($_POST['action']) )
        {
	        switch ( $_POST['action'] )
	        {
	        	case 'update_metrics':
	        		$conf = array();
	        		foreach ( $pluginsActivated as $key => $m )
	        		{
	        			$conf[$key] = $pluginsActivated[$key] && !empty($_POST['metrics'][$key]) && $_POST['metrics'][$key];
	        		}
	        		$config->saveConfig('ocssitestats', 'metrics', json_encode($conf));
	        		OW::getFeedback()->info($lang->text('ocssitestats', 'settings_updated'));
	        		$this->redirect();
	        		break;
	        		
	        	case 'update_settings':
	        		$config->saveConfig('ocssitestats', 'zero_values', !empty($_POST['zero_values']));
	        		OW::getFeedback()->info($lang->text('ocssitestats', 'settings_updated'));
	        		$this->redirect();
	        		break;
	        }
        }
        
        $metricsConf = json_decode($config->getValue('ocssitestats', 'metrics'), true);
        $this->assign('metrics', $metricsConf);
        
        $zeroValues = $config->getValue('ocssitestats', 'zero_values');
        $this->assign('zeroValues', $zeroValues);
        
        $this->assign('pluginsActivated', $pluginsActivated);
        
        $logo = OW::getPluginManager()->getPlugin('ocssitestats')->getStaticUrl() . 'img/oxwallcandystore-logo.jpg';
        $this->assign('logo', $logo);
    }
}