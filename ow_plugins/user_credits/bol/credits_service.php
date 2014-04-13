<?php

/**
 * Copyright (c) 2009, Skalfa LLC
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * User credits Service Class.  
 * 
 * @author Egor Bulgakov <egor.bulgakov@gmail.com>
 * @package ow.plugin.user_credits.bol
 * @since 1.0
 */
final class USERCREDITS_BOL_CreditsService
{
    /**
     * @var USERCREDITS_BOL_BalanceDao
     */
    private $balanceDao;
    /**
     * @var USERCREDITS_BOL_ActionDao
     */
    private $actionDao;
    /**
     * @var USERCREDITS_BOL_PackDao
     */
    private $packDao;
    /**
     * @var USERCREDITS_BOL_LogDao
     */
    private $logDao;
    /**
     * Class instance
     *
     * @var USERCREDITS_BOL_CreditsService
     */
    private static $classInstance;
    
    const ACTION_INTERVAL = 30;

    /**
     * Class constructor
     *
     */
    private function __construct()
    {
        $this->balanceDao = USERCREDITS_BOL_BalanceDao::getInstance();
        $this->actionDao = USERCREDITS_BOL_ActionDao::getInstance();
        $this->packDao = USERCREDITS_BOL_PackDao::getInstance();
        $this->logDao = USERCREDITS_BOL_LogDao::getInstance();
    }

    /**
     * Returns class instance
     *
     * @return USERCREDITS_BOL_CreditsService
     */
    public static function getInstance()
    {
        if ( null === self::$classInstance )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }
    
    /**
     * Returns user credits balance
     * 
     * @param int $userId
     */
    public function getCreditsBalance( $userId )
    {
    	if ( !$userId )
    	{
    		return 0;
    	}
    	
    	$balance = $this->balanceDao->findByUserId($userId);

    	return $balance ? $balance->balance : 0;
    }
    
    /**
     * Increases user balance
     *  
     * @param int $userId
     * @param float $amount
     */
    public function increaseBalance( $userId, $amount )
    {
        if ( !$userId || !$amount )
        {
            return false;
        }
        
        $balance = $this->balanceDao->findByUserId($userId);
        
        if ( $balance )
        {
            $balance->balance += (int) $amount;
        }
        else 
        {
            $balance = new USERCREDITS_BOL_Balance();
            $balance->userId = $userId;
            $balance->balance = (int) $amount;
        }
        
        $this->balanceDao->save($balance);
        
        return true;
    }
    
    /**
     * Decreases user balance
     * 
     * @param int $userId
     * @param float $amount
     */
    public function decreaseBalance( $userId, $amount )
    {
        if ( !$userId || !$amount )
        {
            return false;
        }
        
        $amount = (int) $amount;
        $balance = $this->balanceDao->findByUserId($userId);
        
        if ( $balance && $balance->balance >= $amount )
        {
            $balance->balance -= (int) $amount;
            $this->balanceDao->save($balance);
            
            return true;
        }
        else 
        {
            return false;
        }
    }
    
    public function setBalance( $userId, $amount )
    {
        if ( !$userId || !$amount )
        {
            return false;
        }
        
        $balance = $this->balanceDao->findByUserId($userId);
        
        if ( !$balance )
        {
            $balance = new USERCREDITS_BOL_Balance();
        }
        
        $balance->userId = $userId;
        $balance->balance = (int) $amount;
        
        $this->balanceDao->save($balance);
        
        return true;
    }

    public function grantCredits( $grantorId, $userId, $amount )
    {
        if ( !$grantorId || !$userId || !$amount )
        {
            return false;
        }

        $grantorBalance = $this->balanceDao->findByUserId($grantorId);

        if ( !$grantorBalance || $grantorBalance->balance < $amount )
        {
            return false;
        }

        $balance = $this->balanceDao->findByUserId($userId);

        if ( !$balance )
        {
            $balance = new USERCREDITS_BOL_Balance();
            $balance->userId = $userId;
            $balance->balance = 0;
        }

        // increase balance
        $balance->balance = $balance->balance + $amount;
        $this->balanceDao->save($balance);

        //decrease grantor balance
        $grantorBalance->balance = $grantorBalance->balance - $amount;
        $this->balanceDao->save($grantorBalance);

        return true;
    }

    public function getGrantableAmountForUser( $userId )
    {
        if ( !$userId )
        {
            return 0;
        }

        $amounts = array(10, 50, 100);
        $balance = $this->getCreditsBalance($userId);
        $portion = $balance * 0.1;

        if ( $portion < $amounts[0] )
        {
            return $amounts[0];
        }

        $closest = null;
        foreach ( $amounts as $item )
        {
            if ( $closest == null || abs($portion - $closest) > abs($item - $portion) )
            {
                $closest = $item;
            }
        }

        return $closest;
    }

    /**
     * Checks user balance for sufficient credits to perform action
     *
     * @param string $pluginKey
     * @param string $action
     * @param int $userId
     * @param null $extra
     * @return bool
     */
    public function checkBalance( $pluginKey, $action, $userId, $extra = null )
    {
        if ( !mb_strlen($pluginKey) || !mb_strlen($action) || !$userId )
        {
            return false;
        }
        
        if ( !$action = $this->findAction($pluginKey, $action) )
        {
            return true;
        }
        
        if ( $action->amount >= 0 )
        {
            return true;
        }

        // layer check
        $params = array('userId' => $userId, 'pluginKey' => $pluginKey, 'action' => $action, 'extra' => $extra);
        $event = new OW_Event('usercredits.layer_check', $params);
        OW::getEventManager()->trigger($event);
        $layerCheck = $event->getData();

        if ( $layerCheck )
        {
            return true;
        }
        
        $balance = $this->balanceDao->findByUserId($userId);

        if ( $balance && $balance->balance >= abs($action->amount) )
        {
            return true;
        }
        
        return false;
    }
    
    /**
     * Checks balance of a list of users for sufficient credits to perform action
     * 
     * @param string $pluginKey
     * @param string $action
     * @param int $userIdList
     */
    public function checkBalanceForUserList( $pluginKey, $action, array $userIdList )
    {
        if ( !mb_strlen($pluginKey) || !mb_strlen($action) || !$userIdList )
        {
            return array();
        }
        
        $def = array_fill_keys($userIdList, true);
        
        if ( !$action = $this->findAction($pluginKey, $action) )
        {
            return $def;
        }
        
        if ( $action->amount >= 0 )
        {
            return $def;
        }
        
        $balance = $this->balanceDao->getBalanceForUserList($userIdList);
        
        $balanceList = array();
        if ( $balance )
        {
            foreach ( $balance as $userBalance )
            {
                $balanceList[$userBalance->userId] = $userBalance->balance;
            }
        }
        
        $result = array();
        
        foreach ( $userIdList as $userId )
        {
             $result[$userId] = !empty($balanceList[$userId]) && $balanceList[$userId] >= abs($action->amount);
        }
        
        return $result;
    }
    
    public function checkBalanceForActionList( array $keyList, $userId )
    {
        if ( !$keyList || !$userId )
        {
            return array();
        }
        
        $actions = $this->findActionList($keyList);
        
        $actionList = array();
        if ( $actions )
        {
            foreach ( $actions as $action )
            {
                $actionList[$action->pluginKey][$action->actionKey] = $action->amount;
            }
        }
        
        $balance = $this->balanceDao->findByUserId($userId);
        
        $result = array();
        foreach ( $keyList as $pluginKey => $actionKeys )
        {
            foreach ( $actionKeys as $actionKey )
            {
                $result[$pluginKey][$actionKey] = !empty($actionList[$pluginKey][$actionKey]) && $balance >= $actionList[$pluginKey][$actionKey];
            }
        }
        
        return $result;
    }
    
    /**
     * Tracks action use by a user
     * 
     * @param string $pluginKey
     * @param string $action
     * @param int $userId
     * @param bool $checkInterval
     */
    public function trackAction( $pluginKey, $action, $userId, $checkInterval = true, $extra = null )
    {
        if ( !mb_strlen($pluginKey) || !mb_strlen($action) || !$userId )
        {
            return false;
        }
        
        if ( !$action = $this->findAction($pluginKey, $action) )
        {
            return false;
        }

        // layer check
        $params = array('userId' => $userId, 'pluginKey' => $pluginKey, 'action' => $action, 'extra' => $extra);
        $event = new OW_Event('usercredits.layer_check', $params);
        OW::getEventManager()->trigger($event);
        $layerCheck = $event->getData();

        if ( $layerCheck )
        {
            return false;
        }
        
        $balanceUpdated = false;
        
        if ( $action->amount > 0 )
        {
            $lastAction = $this->findLog($userId, $action->id);
            
            if ( $checkInterval && $lastAction && (time() - $lastAction->logTimestamp < self::ACTION_INTERVAL) )
            {
                return false;
            }
            
            $balanceUpdated = $this->increaseBalance($userId, abs($action->amount));
        }
        elseif ( $action->amount < 0 )
        {
            $balanceUpdated = $this->decreaseBalance($userId, abs($action->amount));
        }
        
        if ( $balanceUpdated )
        {
            $this->logAction($action->id, $userId, $action->amount);
        }
        
        return $balanceUpdated;
    }

    /**
     * Adds new credits action
     * 
     * @param USERCREDITS_BOL_Action $action
     */
    public function addCreditsAction( USERCREDITS_BOL_Action $action )
    {
        // check if action already exists
    	if ( $this->findAction($action->pluginKey, $action->actionKey) )
    	{
    		return true;
    	}
    	
    	$this->actionDao->save($action);
    	
    	return $action->id;
    }

    /**
     * Updates credits action
     * 
     * @param USERCREDITS_BOL_Action $action
     */
    public function updateCreditsAction( USERCREDITS_BOL_Action $action )
    {
        $this->actionDao->save($action);
        
        return $action->id;
    }
    
    /**
     * Collects and stores actions generated by plugins
     * 
     * @param array $actions
     */
    public function collectActions( array $actions )
    {        
        foreach ( $actions as $a )
        {
            if ( $action = $this->findAction($a['pluginKey'], $a['action']) )
            {
                if ( $action->active == 0 )
                {
                    $action->active = 1;
                    $this->updateCreditsAction($action);
                    continue;
                }
            }
            $action = new USERCREDITS_BOL_Action();
            
            $action->pluginKey = $a['pluginKey'];
            $action->actionKey = $a['action'];
            $action->amount = (int) $a['amount'];
            $action->isHidden = isset($a['hidden']) ? (int) $a['hidden'] : 0;
            $action->settingsRoute = isset($a['settingsRoute']) ? $a['settingsRoute'] : null;
            $action->active = isset($a['active']) ? (int) $a['active'] : 1;
            
            $actionId = $this->addCreditsAction($action);
        }
                
        return true;
    }
    
    public function updateActions( array $actions )
    {
        foreach ( $actions as $a )
        {
            if ( !$action = $this->findAction($a['pluginKey'], $a['action']) )
            {
                $action = new USERCREDITS_BOL_Action();
            }
            
            $action->pluginKey = $a['pluginKey'];
            $action->actionKey = $a['action'];
            $action->amount = (int) $a['amount'];
            $action->isHidden = isset($a['hidden']) ? (int) $a['hidden'] : $action->isHidden;
            $action->settingsRoute = isset($a['settingsRoute']) ? $a['settingsRoute'] : $action->settingsRoute;
            
            $this->actionDao->save($action);
        }
        
        return true;
    }
    
    /**
     * Deletes array of actions
     * 
     * @param array $actions
     */
    public function deleteActions( array $actions )
    {
        foreach ( $actions as $a )
        {
            $action = $this->findAction($a['pluginKey'], $a['action']);
            
            if ( $action )
            {                
                $this->actionDao->deleteById($action->id);
            }
        }
        
        return true;
    }
    
    /** 
     * Deletes plugin all actions
     * 
     * @param string $pluginKey
     */
    public function deleteActionsByPluginKey( $pluginKey = null )
    {
        if ( $pluginKey == null )
        {
            $actions = $this->actionDao->findAll();
        }
        else 
        {
            $actions = $this->actionDao->findActionsByPluginKey($pluginKey);
        }
        
        foreach ( $actions as $a )
        {
            $this->actionDao->deleteById($a->id);
        }
        
        return true;
    }
    
    public function activateActionsByPluginKey( $pluginKey )
    {
        $actions = $this->actionDao->findActionsByPluginKey($pluginKey);

        foreach ( $actions as $a )
        {
            $a->active = 1;
            $this->actionDao->save($a);
        }
                
        return true;
    }
    
    public function deactivateActionsByPluginKey( $pluginKey )
    {
        $actions = $this->actionDao->findActionsByPluginKey($pluginKey);

        foreach ( $actions as $a )
        {
            $a->active = 0;
            $this->actionDao->save($a);
        }
                
        return true;
    }

    /**
     * Finds credits actions by type
     * 
     * @param string $type
     */
    public function findCreditsActions( $type )
    {
    	$list = $this->actionDao->findList($type);
    	
    	$actions = array();
    	foreach ( $list as $action )
    	{
    	   $actions[] = array('dto' => $action, 'title' => $this->getActionTitle($action->pluginKey, $action->actionKey));
    	}
    	
    	return $actions;
    }
    
    /**
     * Returns action title for multi-language support
     * 
     * @param string $pluginKey
     * @param string $actionKey
     */
    public function getActionTitle( $pluginKey, $actionKey )
    {
        return OW::getLanguage()->text($pluginKey, 'usercredits_action_' . $actionKey);
    }
    
    /**
     * Finds action by plugin key & action name
     * 
     * @param string $pluginKey
     * @param string $actionKey
     * @return USERCREDITS_BOL_Action 
     */
    public function findAction( $pluginKey, $actionKey )
    {
        return $this->actionDao->findAction($pluginKey, $actionKey);
    }
    
    public function findActionList( array $keyList )
    {
        return $this->actionDao->findActionList($keyList);
    }
    
    /**
     * Finds action by Id
     * 
     * @param int $actionId
     */
    public function findActionById( $actionId )
    {
        return $this->actionDao->findById($actionId);
    }
    
    /**
     * Adds user credits pack
     * 
     * @param USERCREDITS_BOL_Pack $pack
     */
    public function addPack( USERCREDITS_BOL_Pack $pack )
    {
        $this->packDao->save($pack);
        
        return $pack->id;
    }
    
    /**
     * Get list of packs
     * 
     * @return array 
     */
    public function getPackList()
    {
        $packs = $this->packDao->getAllPacks();
        
        $packList = array();
        
        foreach ( $packs as $packDto )
        {
            $price = floatval($packDto->price);
            $packList[] = array(
                'id' => $packDto->id,
                'credits' => $packDto->credits, 
                'price' => $price,
                'title' => $this->getPackTitle($price, $packDto->credits)
            );
        }
        
        return $packList;
    }
    
    /**
     * Returns pack title for multi-language support
     * 
     * @param $price
     * @param $credits
     */
    public function getPackTitle( $price, $credits )
    {
        $currency = BOL_BillingService::getInstance()->getActiveCurrency();
        $params = array('price' => floatval($price), 'curr' => $currency, 'credits' => $credits);
        
        return  OW::getLanguage()->text('usercredits', 'pack_title', $params);
    }
    
    /**
     * Deletes pack by Id
     * 
     * @param int $id
     */
    public function deletePackById( $id )
    {
        $this->packDao->deleteById($id);
        
        return true;
    }
    
    /**
     * Finds pack by Id
     * 
     * @param int $id
     * @return USERCREDITS_BOL_Pack
     */
    public function findPackById( $id )
    {
        return $this->packDao->findById($id);
    }
    
    /**
     * Checks if packs added
     * 
     * @return bool
     */
    public function packSetup()
    {
        return (bool) $this->packDao->countAll();
    }
    
    /**
     * Logs action use
     * 
     * @param int $actionId
     * @param int $userId
     * @param float $amount
     */
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
        
        $this->logDao->save($log);
    }
    
    /**
     * Finds action log record
     * 
     * @param int $userId
     * @param int $actionId
     * @return USERCREDITS_BOL_Log
     */
    public function findLog ( $userId, $actionId )
    {
        return $this->logDao->findLast($userId, $actionId);
    }
}