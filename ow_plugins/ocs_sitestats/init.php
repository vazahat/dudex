<?php

/**
 * Copyright (c) 2013, Oxwall CandyStore
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * /init.php
 * 
 * @author Oxwall CandyStore <plugins@oxcandystore.com>
 * @package ow.ow_plugins.ocs_sitestats
 * @since 1.5
 */

OW::getRouter()->addRoute(
    new OW_Route('ocssitestats.admin_config', 'admin/plugin/ocs-sitestats', 'OCSSITESTATS_CTRL_Admin', 'index')
);

function ocssitestats_add_userlist_data( BASE_CLASS_EventCollector $event )
{
	$config = OW::getConfig();
    $metricsConf = json_decode($config->getValue('ocssitestats', 'metrics'), true);
    $zeroValues = $config->getValue('ocssitestats', 'zero_values');
    $service = OCSSITESTATS_BOL_Service::getInstance();
	
    $count = $service->countNewUsersToday();
    if ( !empty($metricsConf['new_users_today']) && ($count || $zeroValues) )
    {
	    $event->add(
	        array(
	            'label' => OW::getLanguage()->text('ocssitestats', 'new_users_today'),
	            'url' => OW::getRouter()->urlForRoute('base_user_lists', array('list' => 'new-today')),
	            'iconClass' => 'ow_ic_user',
	            'key' => 'new-today',
	            'order' => 6,
	            'dataProvider' => array(OCSSITESTATS_BOL_Service::getInstance(), 'getNewTodayUserListData')
	        )
	    );
    }
    
	$count = $service->countNewUsersThisMonth();
	
    if ( !empty($metricsConf['new_users_this_month']) && ($count || $zeroValues) )
    {
	    $event->add(
	        array(
	            'label' => OW::getLanguage()->text('ocssitestats', 'new_users_this_month'),
	            'url' => OW::getRouter()->urlForRoute('base_user_lists', array('list' => 'new-this-month')),
	            'iconClass' => 'ow_ic_user',
	            'key' => 'new-this-month',
	            'order' => 7,
	            'dataProvider' => array($service, 'getNewThisMonthUserListData')
	        )
	    );
    }
}
OW::getEventManager()->bind('base.add_user_list', 'ocssitestats_add_userlist_data');