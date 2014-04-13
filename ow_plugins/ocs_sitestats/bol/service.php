<?php

/**
 * Copyright (c) 2013, Oxwall CandyStore
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * Site stats service
 * 
 * @author Oxwall CandyStore <plugins@oxcandystore.com>
 * @package ow.ow_plugins.ocs_sitestats.bol
 * @since 1.5.0
 */
final class OCSSITESTATS_BOL_Service
{
    /**
     * Constructor.
     */
    private function __construct() { }
    
    /**
     * Singleton instance.
     *
     * @var OCSSITESTATS_BOL_Service
     */
    private static $classInstance;

    /**
     * Returns an instance of class
     *
     * @return OCSSITESTATS_BOL_Service
     */
    public static function getInstance()
    {
        if ( self::$classInstance === null )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }
    
    public function getNewTodayUserListData( $first, $count )
    {
		return array(
            $this->getNewUsersToday($first, $count), // get users
            $this->countNewUsersToday() // count users
        );
    }
    
    public function getNewThisMonthUserListData( $first, $count )
    {
		return array(
            $this->getNewUsersThisMonth($first, $count), // get users
            $this->countNewUsersThisMonth() // count users
        );
    }
    
    public function getStatistics()
    {
    	$router = OW::getRouter();
    	$pm = OW::getPluginManager();
    	$lang = OW::getLanguage();
    	
    	$config = OW::getConfig();
        $metricsConf = json_decode($config->getValue('ocssitestats', 'metrics'), true);
        
        if ( $metricsConf['total_users'] )
        {
	    	$data['total_users'] = array(
	    		'label' => $lang->text('ocssitestats', 'total_users'), 
	    		'url' => $router->urlForRoute('users'),
	    		'count' => $this->countTotalUsers()
    		);
        }
    	
        if ( $metricsConf['online_users'] )
        {
	    	$data['online_users'] = array(
	    		'label' => $lang->text('ocssitestats', 'online_users'), 
	    		'url' => $router->urlForRoute('base_user_lists', array('list' => 'online')), 
	    		'count' => $this->countOnlineUsers()
	    	);
        }
        
        if ( $metricsConf['new_users_today'] )
        {
	    	$data['new_users_today'] = array(
	    		'label' => $lang->text('ocssitestats', 'new_users_today'), 
	    		'url' => $router->urlForRoute('base_user_lists', array('list' => 'new-today')), 
	    		'count' => $this->countNewUsersToday()
	    	);
        }
    	
        if ( $metricsConf['new_users_this_month'] )
        {
	    	$data['new_users_this_month'] = array(
	    		'label' => $lang->text('ocssitestats', 'new_users_this_month'), 
	    		'url' => $router->urlForRoute('base_user_lists', array('list' => 'new-this-month')),
	    		'count' => $this->countNewUsersThisMonth()
	    	);
        }
    	
    	if ( $metricsConf['photos'] && $pm->isPluginActive('photo') )
    	{
    		$data['photos'] = array(
    			'label' => $lang->text('ocssitestats', 'photos'), 
    			'url' => $router->urlForRoute('view_photo_list'), 
    			'count' => $this->countPhotos()
    		);
    	}
    	
    	if ( $metricsConf['videos'] && $pm->isPluginActive('video') )
    	{
    		$data['videos'] = array(
    			'label' => $lang->text('ocssitestats', 'videos'), 
    			'url' => $router->urlForRoute('video_list_index'), 
    			'count' => $this->countVideos()
    		);
    	}
    	
    	if ( $metricsConf['blogs'] && $pm->isPluginActive('blogs') )
    	{
    		$data['blogs'] = array(
    			'label' => $lang->text('ocssitestats', 'blogs'), 
    			'url' => $router->urlForRoute('blogs'), 
    			'count' => $this->countBlogPosts()
    		);
    	}
    	
    	if ( $metricsConf['groups'] && $pm->isPluginActive('groups') )
    	{
    		$data['groups'] = array(
    			'label' => $lang->text('ocssitestats', 'groups'), 
    			'url' => $router->urlForRoute('groups-index'), 
    			'count' => $this->countGroups()
    		);
    	}
    	
    	if ( $metricsConf['events'] && $pm->isPluginActive('event') )
    	{
    		$data['events'] = array(
    			'label' => $lang->text('ocssitestats', 'events'), 
    			'url' => $router->urlForRoute('event.main_menu_route'), 
    			'count' => $this->countUpcomingEvents()
    		);
    	}
    	
    	if ( $metricsConf['discussions'] && $pm->isPluginActive('forum') )
    	{
    		$data['discussions'] = array(
    			'label' => $lang->text('ocssitestats', 'discussions'), 
    			'url' => $router->urlForRoute('forum-default'), 
    			'count' => $this->countDiscussions()
    		);
    	}
    	
    	if ( $metricsConf['links'] && $pm->isPluginActive('links') )
    	{
    		$data['links'] = array(
    			'label' => $lang->text('ocssitestats', 'links'), 
    			'url' => $router->urlForRoute('links-latest'), 
    			'count' => $this->countLinks()
    		);
    	}
    	
    	return $data;
    }
    
    public function countTotalUsers()
    {
    	return BOL_UserService::getInstance()->count();
    }
    
    public function countOnlineUsers()
    {
    	return BOL_UserService::getInstance()->countOnline();    
    }
    
    public function countNewUsersToday()
    {
		$query = "SELECT COUNT(*) FROM `".BOL_UserDao::getInstance()->getTableName()."` as `u`
	    	LEFT JOIN `" . BOL_UserSuspendDao::getInstance()->getTableName() . "` as `s`
	    		ON( `u`.`id` = `s`.`userId` )
            LEFT JOIN `" . BOL_UserApproveDao::getInstance()->getTableName() . "` as `d`
                ON( `u`.`id` = `d`.`userId` )
	    	WHERE `s`.`id` IS NULL AND `d`.`id` IS NULL
	    	AND `u`.`joinStamp` >= :ts";

        return OW::getDbo()->queryForColumn($query, array('ts' => time() - 24 * 3600));
    }
    
    public function getNewUsersToday( $first, $count )
    {
		$query = "SELECT `u`.* FROM `".BOL_UserDao::getInstance()->getTableName()."` as `u`
	    	LEFT JOIN `" . BOL_UserSuspendDao::getInstance()->getTableName() . "` as `s`
	    		ON( `u`.`id` = `s`.`userId` )
            LEFT JOIN `" . BOL_UserApproveDao::getInstance()->getTableName() . "` as `d`
                ON( `u`.`id` = `d`.`userId` )
	    	WHERE `s`.`id` IS NULL AND `d`.`id` IS NULL
	    	AND `u`.`joinStamp` >= :ts
	    	ORDER BY `u`.`joinStamp` DESC
	    	LIMIT :first, :count";

        return OW::getDbo()->queryForObjectList(
        	$query, 
        	BOL_UserDao::getInstance()->getDtoClassName(),
        	array('ts' => time() - 24 * 3600, 'first' => $first, 'count' => $count)
        );
    }
    
    public function countNewUsersThisMonth()
    {
		$query = "SELECT COUNT(*) FROM `".BOL_UserDao::getInstance()->getTableName()."` as `u`
	    	LEFT JOIN `" . BOL_UserSuspendDao::getInstance()->getTableName() . "` as `s`
	    		ON( `u`.`id` = `s`.`userId` )
            LEFT JOIN `" . BOL_UserApproveDao::getInstance()->getTableName() . "` as `d`
                ON( `u`.`id` = `d`.`userId` )
	    	WHERE `s`.`id` IS NULL AND `d`.`id` IS NULL
	    	AND `u`.`joinStamp` >= :ts";

        return OW::getDbo()->queryForColumn($query, array('ts' => time() - 30 * 24 * 3600));
    }
    
	public function getNewUsersThisMonth( $first, $count )
    {
		$query = "SELECT `u`.* FROM `".BOL_UserDao::getInstance()->getTableName()."` as `u`
	    	LEFT JOIN `" . BOL_UserSuspendDao::getInstance()->getTableName() . "` as `s`
	    		ON( `u`.`id` = `s`.`userId` )
            LEFT JOIN `" . BOL_UserApproveDao::getInstance()->getTableName() . "` as `d`
                ON( `u`.`id` = `d`.`userId` )
	    	WHERE `s`.`id` IS NULL AND `d`.`id` IS NULL
	    	AND `u`.`joinStamp` >= :ts
	    	ORDER BY `u`.`joinStamp` DESC
	    	LIMIT :first, :count";

        return OW::getDbo()->queryForObjectList(
        	$query, 
        	BOL_UserDao::getInstance()->getDtoClassName(), 
        	array('ts' => time() - 30 * 24 * 3600, 'first' => $first, 'count' => $count)
        );
    }
    
    public function countPhotos()
    {
    	return PHOTO_BOL_PhotoDao::getInstance()->countPhotos('latest');
    }
    
    public function countVideos()
    {
    	return VIDEO_BOL_ClipDao::getInstance()->countClips('latest');
    }
    
    public function countBlogPosts()
    {
    	return PostService::getInstance()->countPosts();
    }

	public function countGroups()
	{
    	return GROUPS_BOL_Service::getInstance()->findGroupListCount(GROUPS_BOL_Service::LIST_LATEST);
	}
	
	public function countUpcomingEvents()
	{
    	return EVENT_BOL_EventService::getInstance()->findPublicEventsCount();
	}
	
	public function countDiscussions()
	{
    	return FORUM_BOL_ForumService::getInstance()->countAllTopics();
	}
	
	public function countLinks()
	{
		return LinkService::getInstance()->countLinks();
	}
}