<?php

/**
 * Copyright (c) 2014, Kairat Bakytow
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/** 
 * 
 *
 * @author Kairat Bakytow <kainisoft@gmail.com>
 * @package ow_plugins.profileprogressbar.bol
 * @since 1.0
 */
class PROFILEPROGRESSBAR_BOL_Service
{
    CONST ENTITY_TYPE_BLOG = 'blog-post';
    CONST ENTITY_TYPE_EVENT = 'event';
    CONST ENTITY_TYPE_FORUM = 'forum-topic';
    CONST ENTITY_TYPE_FRIEND = 'friend_add';
    CONST ENTITY_TYPE_GROUPS = 'group';
    CONST ENTITY_TYPE_LINK = 'link';
    CONST ENTITY_TYPE_PHOTO = 'photo_comments';
    CONST ENTITY_TYPE_VIDEO = 'video_comments';
    CONST ENTITY_TYPE_GIFT = 'user_gift';
    
    CONST KEY_PROGRESSBAR = 'progressbarData';
    CONST KEY_FEATURES = 'completedFeatures';
    CONST KEY_HINT = 'hintText';
    CONST COUNT_QUESTION = 'totalQuestionCount';
    CONST COUNT_COMPLETED_QUESTION = 'completeQuestionCount';
    
    private static $classInstance;

    public static function getInstance()
    {
        if ( self::$classInstance === null )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    private $activityLogDto;
    
    private function __construct()
    {
        $this->activityLogDto = PROFILEPROGRESSBAR_BOL_ActivityLogDao::getInstance();
    }
    
    public static function getEntityTypes()
    {
        return array(
            'blogs' => self::ENTITY_TYPE_BLOG,
            'event' => self::ENTITY_TYPE_EVENT,
            'forum' => self::ENTITY_TYPE_FORUM,
            'friends' => self::ENTITY_TYPE_FRIEND,
            'groups' => self::ENTITY_TYPE_GROUPS,
            'links' => self::ENTITY_TYPE_LINK,
            'photo' => self::ENTITY_TYPE_PHOTO,
            'video' => self::ENTITY_TYPE_VIDEO,
            'virtualgifts' => self::ENTITY_TYPE_GIFT
        );
    }
    
    public function getAvailableFeatures()
    {
        $availableFeatures = array();
        $pluginManager = OW::getPluginManager();
        $defaultFeatures = array_keys(self::getEntityTypes());
        
        foreach ( $defaultFeatures as $feature )
        {
            if ( $pluginManager->isPluginActive($feature) )
            {
                $availableFeatures[] = $feature;
            }
        }
        
        return $availableFeatures;
    }

    public function getProgressbarData( $userId, $isOwner = false )
    {
        if ( empty($userId) )
        {
            return NULL;
        }
        
        $user = BOL_UserService::getInstance()->findUserById($userId);
        
        if ( empty($user) )
        {
            return NULL;
        }
        
        $questions = BOL_QuestionService::getInstance()->findAllQuestionsForAccountType($user->getAccountType());
        
        $questionNameList = array();
        
        foreach ( $questions as $question )
        {
            $questionNameList[] = $question['name'];
        }

        $questionData = BOL_QuestionService::getInstance()->getQuestionData(array($userId), $questionNameList);

        $data = array(
            self::KEY_PROGRESSBAR => array(
                self::COUNT_QUESTION => count($questions),
                self::COUNT_COMPLETED_QUESTION => count(array_filter($questionData[$userId])) 
            )
        );
        
        $authService = BOL_AuthorizationService::getInstance();
        $defaultFeatures = self::getEntityTypes();
        $features = array_filter(get_object_vars(json_decode(OW::getConfig()->getValue('profileprogressbar', 'features'))));
        
        $_features = array();
        $actions = array(
            'blogs' => 'add',
            'event' => 'add_event',
            'forum' => 'edit',
            'friends' => 'add_friend',
            'groups' => 'create',
            'links' => 'add',
            'photo' => 'upload',
            'video' => 'add',
            'virtualgifts' => 'send_gift'
        );
        
        foreach ( $features as $feature => $count )
        {
            if ( isset($actions[$feature]) && $authService->isActionAuthorizedForUser($userId, $feature, $actions[$feature]) )
            {
                $data[self::KEY_PROGRESSBAR][self::COUNT_QUESTION] += $count;
                $_features[$defaultFeatures[$feature]] = $count;
            }
        }
        
        $data[self::KEY_PROGRESSBAR][self::COUNT_COMPLETED_QUESTION] += (int)$this->getCompletedFeaturesCount($userId, array_keys($_features));
        
        if ( $isOwner )
        {
            $langFeatures = array();
            $completedFeatures = $this->getCompletedFeatures($userId, array_keys($_features));
            
            foreach ( $_features as $feature => $count )
            {
                $need = NULL;
                
                if ( !isset($completedFeatures[$feature]) || ($need = ($count - $completedFeatures[$feature])) > 0 )
                {
                    $_feature = array_search($feature, $defaultFeatures);
                    $langFeatures[$_feature] = OW::getLanguage()->text('profileprogressbar', $_feature.'_desc');
                    $langFeatures[$_feature . 'Count'] = $need === NULL ? $count : $need;
                }
            }
            
            if ( count($langFeatures) > 0 )
            {
                $vars = array();
                
                foreach ( $langFeatures as $key => $value )
                {
                    $vars['{$' . $key .'}'] = $value;
                }
                
                $hintText = explode('#', OW::getLanguage()->text('profileprogressbar', 'hint_text'));
                
                foreach ( $hintText as $key => $hint )
                {
                    $hintText[$key] = str_replace(array_keys($vars), array_values($vars), $hint);
                }
                
                function unsetUnusedHint( $val )
                {
                    return strpos($val, '{$') === FALSE;
                }
                
                $hintText = array_filter($hintText, 'unsetUnusedHint');
                
                $data[self::KEY_HINT] = trim(implode('', $hintText));
            }
        }
        
        return $data;
    }

    public function strReplace($str, $assignVars = array())
    {
        $vars = array();
        
        foreach ($assignVars as $key => $value)
        {
            $vars['{$' . $key .'}'] = $value;
        }

        return str_replace(array_keys($vars), array_values($vars), $str);
    }
    
    public function getCompletedFeaturesCount( $userId, $features )
    {
        return $this->activityLogDto->getCompletedFeaturesCount($userId, $features);
    }
    
    
    public function getCompletedFeatures( $userId, array $features )
    {
        return $this->activityLogDto->getCompletedFeatures($userId, $features);
    }
}
