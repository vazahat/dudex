<?php

class GROUPRSS_BOL_FeedService
{
    private static $classInstance;
    private $feeddao;
    
    public static function getInstance()
    {
        if (self::$classInstance === null) {
            self::$classInstance = new self();
        }
        
        return self::$classInstance;
    }
    
    private function __construct()
    {
        $this->feeddao = GROUPRSS_BOL_FeedDao::getInstance();
    }
    
    public function isDuplicate($groupId, $feedUrl)
    {
        return $this->feeddao->isDuplicate($groupId, $feedUrl);
    }
    
    public function addFeed($groupId, $userId, $feedUrl, $feedCount)
    {
        $feed            = new GROUPRSS_BOL_Feed();
        $feed->groupId   = $groupId;
        $feed->userId    = $userId;
        $feed->feedUrl   = $feedUrl;
        $feed->feedCount = $feedCount;
        $feed->timestamp = time();
        $this->feeddao->save($feed);
        return $feed->id;
    }
    
    public function deleteFeed($id)
    {
        $this->feeddao->deleteById($id);
    }
    
    public function findByGroup($groupId)
    {
        return $this->feeddao->findByGroup($groupId);
    }
    
    public function getGroupCreater($groupId)
    {
        $group = $this->feeddao->findById($groupId);
        return (int) $group->userId;
    }
    
    public function addAllGroupFeed()
    {
        if (OW::getConfig()->getValue('grouprss', 'disablePosting') == '1')
            return;
        
        $commentService  = BOL_CommentService::getInstance();
        $newsfeedService = NEWSFEED_BOL_Service::getInstance();
        $router          = OW::getRouter();
        $displayText     = OW::getLanguage()->text('grouprss', 'feed_display_text');
        
        $location = OW::getConfig()->getValue('grouprss', 'postLocation');
        
        include_once('autoloader.php');
        
        $allFeeds = $this->feeddao->findAll();
        
        foreach ($allFeeds as $feed) {
            $simplefeed = new SimplePie();
            $simplefeed->set_feed_url($feed->feedUrl);
            $simplefeed->set_stupidly_fast(true);
            $simplefeed->enable_cache(false);
            
            $simplefeed->init();
            $simplefeed->handle_content_type();
            
            $items = $simplefeed->get_items(0, $feed->feedCount);
            
            foreach ($items as $item) {
                $content = UTIL_HtmlTag::autoLink($item->get_content());
                
                if ($location == 'wall' && !$this->isDuplicateComment($feed->groupId, $content)) {
                    $commentService->addComment('groups_wal', $feed->groupId, 'groups', $feed->userId, $content);
                }
                
                if (trim($location) == 'newsfeed' && !$this->isDuplicateFeed($feed->groupId, $content)) {
                    $statusObject = $newsfeedService->saveStatus('groups', (int) $feed->groupId, $content);
                    
                    $content = UTIL_HtmlTag::autoLink($content);
                    
                    $action             = new NEWSFEED_BOL_Action();
                    $action->entityId   = $statusObject->id;
                    $action->entityType = "groups-status";
                    $action->pluginKey  = "newsfeed";
                    $data               = array(
                        "string" => $content,
                        "content" => "[ph:attachment]",
                        "view" => array(
                            "iconClass" => "ow_ic_comment"
                        ),
                        "attachment" => array(
                            "oembed" => null,
                            "url" => null,
                            "attachId" => null
                        ),
                        "data" => array(
                            "userId" => $feed->userId
                        ),
                        "actionDto" => null,
                        "context" => array(
                            "label" => $displayText,
                            "url" => $router->urlForRoute('groups-view', array(
                                'groupId' => $feed->groupId
                            ))
                        )
                    );
                    $action->data       = json_encode($data);
                    $actionObject       = $newsfeedService->saveAction($action);
                    
                    $activity = new NEWSFEED_BOL_Activity();
                    
                    $activity->activityType = NEWSFEED_BOL_Service::SYSTEM_ACTIVITY_CREATE;
                    $activity->activityId   = $feed->userId;
                    $activity->actionId     = $actionObject->id;
                    $activity->privacy      = NEWSFEED_BOL_Service::PRIVACY_EVERYBODY;
                    $activity->userId       = $feed->userId;
                    $activity->visibility   = NEWSFEED_BOL_Service::VISIBILITY_FULL;
                    $activity->timeStamp    = time();
                    $activity->data         = json_encode(array());
                    $activityObject         = $newsfeedService->saveActivity($activity);
                    
                    $newsfeedService->addActivityToFeed($activity, 'groups', $feed->groupId);
                    
                }
                
            }
            
            $simplefeed->__destruct();
            unset($feed);
        }
    }
    
    public function isDuplicateFeed($groupId, $content)
    {
        return $this->feeddao->isDuplicateFeed($groupId, $content);
    }
    
    public function isDuplicateComment($groupId, $content)
    {
        return $this->feeddao->isDuplicateComment($groupId, $content);
    }
    
}