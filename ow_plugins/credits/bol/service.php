<?php

/**
 * This software is intended for use with Oxwall Free Community Software http://www.oxwall.org/ and is a proprietary licensed product. 
 * For more information see License.txt in the plugin folder.

 * ---
 * Copyright (c) 2012, Purusothaman Ramanujam
 * All rights reserved.

 * Redistribution and use in source and binary forms, with or without modification, are not permitted provided.

 * This plugin should be bought from the developer by paying money to PayPal account (purushoth.r@gmail.com).

 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED
 * AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */
class CREDITS_BOL_Service {

    private static $classInstance;

    public static function getInstance() {
        if (self::$classInstance === null) {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    private function __construct() {
        
    }

    public function transferCredits($userId, $receiveUser, $creditValue) {

        $debitValue = $creditValue * -1;

        $userCreditsService = USERCREDITS_BOL_CreditsService::getInstance();
        $creditsService = CREDITS_BOL_Service::getInstance();

        $sendItem = $this->logAction($creditsService->getSentActionId(), $userId, $debitValue);
        $receiveItem = $this->logAction($creditsService->getReceiveActionId(), $receiveUser, $creditValue);

        $userCreditsService->increaseBalance($receiveUser, $creditValue);
        $userCreditsService->decreaseBalance($userId, $creditValue);

        $sqlInsert = "INSERT INTO " .OW_DB_PREFIX . "credits_sent_log(senderItem, receiverItem, sender, receiver) 
                             VALUES(:sendItem, :receiveItem, :userId, :receiveUser)";

        $qParams = array('sendItem' => $sendItem , 'receiveItem' => $receiveItem , 'userId' => $userId, 'receiveUser' => $receiveUser);

        OW::getDbo()->query($sqlInsert,$qParams);

                    $service = BOL_UserService::getInstance();
                    $avatars = BOL_AvatarService::getInstance()->getDataForUserAvatars(array(
                        $userId,$receiveUser
                    ));
                    $names   = $service->getDisplayNamesForList(array(
                        $userId,$receiveUser
                    ));
                    $uUrls   = $service->getUserUrlsForList(array(
                        $userId,$receiveUser
                    ));
                    
            if(OW::getConfig()->getValue('credits', 'enableNotification') == '1')
            {
                   //Send notification to receiver

                    $avatar  = $avatars[$userId];
                    
                    $notificationParams = array(
                        'pluginKey' => 'credits',
                        'action' => 'credits-received',
                        'entityType' => 'received',
                        'entityId' => $receiveItem,
                        'userId' => $receiveUser,
                        'time' => time()
                    );
                    
                    $sender = '<a href="' . $uUrls[$userId] . '" target="_blank" >' . $names[$userId] . '</a>';
                    
                    $notificationData = array(
                        'string' => array(
                            'key' => 'credits+notify_credits_received',
                            'vars' => array(
                                'sender' => $sender,
                                'credits' => $creditValue
                            )
                        ),
                        'avatar' => $avatar,
                        'url' => $uUrls[$userId]
                    );
                    
                    $event = new OW_Event('notifications.add', $notificationParams, $notificationData);
                    OW::getEventManager()->trigger($event);
        }

            $subject = OW::getLanguage()->text('credits', 'credits_email_subject',array('requester_name' => $names[$userId],
                                                                                    'credits' => $creditValue));
                                                                                      
            $content = OW::getLanguage()->text('credits', 'credits_email_content',array('requester_name' => $names[$userId],
                                                                                    'requester_url' => $uUrls[$userId],              
                                                                                    'credits' => $creditValue,
                                                                                    'user_url' => $uUrls[$receiveUser],
                                                                                    'name' => $names[$receiveUser]));
                                                                                                  
            if(OW::getConfig()->getValue('credits', 'enableEmail') == '1')
            {
              $tmpUser = $service->findUserById($receiveUser);
              $sitemail = OW::getConfig()->getValue('base', 'site_email');
              $sitename = OW::getConfig()->getValue('base', 'site_name');
                            
              $mail = OW::getMailer()->createMail();              
              $mail->addRecipientEmail($tmpUser->getEmail());
              $mail->setSender($sitemail,$sitename);
              $mail->setSenderSuffix(true);             
              $mail->setSubject($subject);
              $mail->setHtmlContent($content);
              $mail->setTextContent(UTIL_HtmlTag::stripTags($content));
              
              OW::getMailer()->addToQueue($mail);
            }

            if(OW::getConfig()->getValue('credits', 'enablePM') == '1')
            {
               $conversation = MAILBOX_BOL_ConversationService::getInstance()->createConversation($userId,$receiveUser,$subject,$content);            
            }
                                
        return true;
    }

    public function logAction( $actionId, $userId, $amount )
    {
        if ( !$userId )
        {
            return false;
        }
        
        $log = new USERCREDITS_BOL_Log();
        $log->actionId = $actionId;
        $log->userId = $userId;
        $log->amount = (int) $amount;
        $log->logTimestamp = time();

        USERCREDITS_BOL_LogDao::getInstance()->save($log);

        return $log->id;
    }

    public function getCreditHistory($userId, $type, $page, $limit) {
        $logDao = USERCREDITS_BOL_LogDao::getInstance();
        $actionDao = USERCREDITS_BOL_ActionDao::getInstance();
        $sentLogTable = OW_DB_PREFIX . "credits_sent_log";
        $first = ($page - 1 ) * $limit;

        if ($type == 'all') {
            $query = "SELECT l.id, a.pluginKey , a.actionKey, l.amount, l.logTimestamp
                        FROM " . $logDao->getTableName() . " l,"
                    . $actionDao->getTableName() . " a                 
                       WHERE l.userId = :userId
                         AND l.actionId = a.id
                         AND a.active = 1
                         AND a.isHidden = 0
                       ORDER BY l.logTimestamp DESC
                       LIMIT :first, :limit";
        } else if ($type == 'sent') {
            $query = "SELECT l.id, a.pluginKey , a.actionKey, l.amount, l.logTimestamp,c.receiver
                        FROM " . $logDao->getTableName() . " l,"
                    . $actionDao->getTableName() . " a,"
                    . $sentLogTable . " c
                       WHERE l.userId = :userId
                         AND l.actionId = a.id
                         AND l.id = c.senderItem
                         AND l.actionId = " . $this->getSentActionId() . "
                         AND a.active = 1
                         AND a.isHidden = 0
                       ORDER BY l.logTimestamp DESC
                       LIMIT :first, :limit";
        } else if ($type == 'received') {
            $query = "SELECT l.id, a.pluginKey , a.actionKey, l.amount, l.logTimestamp,c.sender
                        FROM " . $logDao->getTableName() . " l,"
                    . $actionDao->getTableName() . " a,"
                    . $sentLogTable . " c
                       WHERE l.userId = :userId
                         AND l.actionId = a.id
                         AND l.id = c.receiverItem
                         AND l.actionId = " . $this->getReceiveActionId() . "
                         AND a.active = 1
                         AND a.isHidden = 0
                       ORDER BY l.logTimestamp DESC
                       LIMIT :first, :limit";
        }

        $qParams = array('userId' => $userId, 'first' => $first, 'limit' => $limit);

        $actions = OW::getDbo()->queryForList($query, $qParams);
        
        $actionLogs = array();

        foreach($actions as $action){
           $id = $action['id'];
           $actionLogs[$id]['id'] = $id;
           $actionLogs[$id]['pluginKey'] = $action['pluginKey'];
           $actionLogs[$id]['actionKey'] = $action['actionKey'];
           $actionLogs[$id]['amount'] = $action['amount'];
           $actionLogs[$id]['logTimestamp'] = $action['logTimestamp'];  
           $actionLogs[$id]['actionTitle'] = USERCREDITS_BOL_CreditsService::getInstance()->getActionTitle($action['pluginKey'],$action['actionKey']);
           
           if ($type == 'sent'){  
              $actionLogs[$id]['userUrl'] = BOL_UserService::getInstance()->getUserUrl($action['receiver']);
              $actionLogs[$id]['userName'] = BOL_UserService::getInstance()->getDisplayName($action['receiver']);              
           }
           else if ($type == 'received'){  
              $actionLogs[$id]['userUrl'] = BOL_UserService::getInstance()->getUserUrl($action['sender']);
              $actionLogs[$id]['userName'] = BOL_UserService::getInstance()->getDisplayName($action['sender']);            
           }                                                      
        }
        
        return $actionLogs;
    }

    public function getCreditHistoryCount($userId, $type) {
        $logDao = USERCREDITS_BOL_LogDao::getInstance();
        $actionDao = USERCREDITS_BOL_ActionDao::getInstance();

        if ($type == 'all') {
            $query = "SELECT COUNT(*)
                        FROM " . $logDao->getTableName() . " l,"
                    . $actionDao->getTableName() . " a
                       WHERE l.userId = :userId
                         AND l.actionId = a.id
                         AND a.isHidden = 0
                         AND a.active = 1";
        } else if ($type == 'sent') {
            $query = "SELECT COUNT(*)
                        FROM " . $logDao->getTableName() . " l,"
                    . $actionDao->getTableName() . " a
                       WHERE l.userId = :userId
                         AND l.actionId = a.id
                         AND l.actionId = " . $this->getSentActionId() . "
                         AND a.isHidden = 0
                         AND a.active = 1";
        } else if ($type == 'received') {
            $query = "SELECT COUNT(*)
                        FROM " . $logDao->getTableName() . " l,"
                    . $actionDao->getTableName() . " a
                       WHERE l.userId = :userId
                         AND l.actionId = a.id
                         AND l.actionId = " . $this->getReceiveActionId() . "
                         AND a.isHidden = 0
                         AND a.active = 1";
        }

        $qParams = array('userId' => $userId);

        return (int) OW::getDbo()->queryForColumn($query, $qParams);
    }

    public function getUserFriends($userId) {
        $friendsCount = FRIENDS_BOL_Service::getInstance()->countFriends($userId);
        $friends = FRIENDS_BOL_Service::getInstance()->findFriendIdList($userId, 0, $friendsCount);
        $list = array();

        foreach ($friends as $friend)
        {
            if ( OW::getAuthorization()->isUserAuthorized($friend, 'credits', 'receive') )
            {
               $list[$friend] = BOL_UserService::getInstance()->getDisplayName($friend);
            }
        }

        return $list;
    }

    public function getSentActionId() {
        $actions = USERCREDITS_BOL_ActionDao::getInstance()->findActionsByPluginKey('credits');

        foreach ($actions as $action) {
            if ($action->actionKey == 'send')
                return $action->id;
        }

        return 0;
    }

    public function getReceiveActionId() {
        $actions = USERCREDITS_BOL_ActionDao::getInstance()->findActionsByPluginKey('credits');

        foreach ($actions as $action) {
            if ($action->actionKey == 'receive')
                return $action->id;
        }

        return 0;
    }

    public function getCreditHistoryForAllUsers($page, $limit) {
        $logDao = USERCREDITS_BOL_LogDao::getInstance();
        $actionDao = USERCREDITS_BOL_ActionDao::getInstance();
        $sentLogTable = OW_DB_PREFIX . "credits_sent_log";
        $first = ($page - 1 ) * $limit;

            $query = "SELECT l.id, a.pluginKey , a.actionKey, l.amount, l.logTimestamp,c.receiver,c.sender,c.id delKey
                        FROM " . $logDao->getTableName() . " l,"
                    . $actionDao->getTableName() . " a,"
                    . $sentLogTable . " c
                       WHERE l.actionId = a.id
                         AND l.actionId = " . $this->getSentActionId() . "                       
                         AND l.id = c.senderItem
                         AND a.active = 1
                         AND a.isHidden = 0
                       ORDER BY l.logTimestamp DESC
                       LIMIT :first, :limit";
 
        $qParams = array('first' => $first, 'limit' => $limit);

        $actions = OW::getDbo()->queryForList($query, $qParams);
        
        $actionLogs = array();

        $userService = BOL_UserService::getInstance();
        
        foreach($actions as $action){
           $id = $action['id'];

           $actionLogs[$id]['id'] = $id;
           $actionLogs[$id]['pluginKey'] = $action['pluginKey'];
           $actionLogs[$id]['actionKey'] = $action['actionKey'];
           $actionLogs[$id]['amount'] = $action['amount'];
           $actionLogs[$id]['logTimestamp'] = $action['logTimestamp'];  
           $actionLogs[$id]['actionTitle'] = USERCREDITS_BOL_CreditsService::getInstance()->getActionTitle($action['pluginKey'],$action['actionKey']);
           $actionLogs[$id]['receiverUserUrl'] = $userService->getUserUrl($action['receiver']);
           $actionLogs[$id]['receiverUserName'] = $userService->getDisplayName($action['receiver']);              
           $actionLogs[$id]['senderUserUrl'] = $userService->getUserUrl($action['sender']);
           $actionLogs[$id]['senderUserName'] = $userService->getDisplayName($action['sender']);
           $actionLogs[$id]['revertUrl'] = OW::getRouter()->urlFor('CREDITS_CTRL_Action', 'revert', array('id' => $action['delKey'],'amount'=>$action['amount']));           
        }
        
        return $actionLogs;
    }

    public function getCreditHistoryCountForAllUsers() {
        $logDao = USERCREDITS_BOL_LogDao::getInstance();
        $actionDao = USERCREDITS_BOL_ActionDao::getInstance();

            $query = "SELECT COUNT(*)
                        FROM " . $logDao->getTableName() . " l,"
                    . $actionDao->getTableName() . " a
                       WHERE l.actionId = a.id
                         AND l.actionId = " . $this->getSentActionId() . "
                         AND a.isHidden = 0
                         AND a.active = 1";

        return (int) OW::getDbo()->queryForColumn($query);
    }
    
    public function deleteTransferRecord($id,$amount) {
        $sentLogTable = OW_DB_PREFIX . "credits_sent_log";
            
        $query = "SELECT receiver,sender FROM ". $sentLogTable . " WHERE id = :id";
 
        $actions = OW::getDbo()->queryForList($query, array('id' => $id));
        
        $receiveUser =   $actions[0]['receiver'];
        $sentUser =   $actions[0]['sender']; 
                
        USERCREDITS_BOL_CreditsService::getInstance()->increaseBalance($sentUser, $amount);
        USERCREDITS_BOL_CreditsService::getInstance()->decreaseBalance($receiveUser, $amount);
        
        $sql = "DELETE FROM `" . $sentLogTable . "` WHERE `id` = :id";

        OW::getDbo()->query($sql, array('id' => $id));      
                
    }    
}