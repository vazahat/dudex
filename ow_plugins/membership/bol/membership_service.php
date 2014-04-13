<?php

/**
 * Copyright (c) 2009, Skalfa LLC
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * Membership Service Class.
 *
 * @author Egor Bulgakov <egor.bulgakov@gmail.com>
 * @package ow.ow_plugins.membership.bol
 * @since 1.0
 */
final class MEMBERSHIP_BOL_MembershipService
{
    /**
     * @var MEMBERSHIP_BOL_MembershipTypeDao
     */
    private $membershipTypeDao;
    /**
     * @var MEMBERSHIP_BOL_MembershipPlanDao
     */
    private $membershipPlanDao;
    /**
     * @var MEMBERSHIP_BOL_MembershipUserDao
     */
    private $membershipUserDao;
    /**
     * Class instance
     *
     * @var MEMBERSHIP_BOL_MembershipService
     */
    private static $classInstance;
    
    /**
     * Class constructor
     */
    private function __construct()
    {
        $this->membershipTypeDao = MEMBERSHIP_BOL_MembershipTypeDao::getInstance();
        $this->membershipPlanDao = MEMBERSHIP_BOL_MembershipPlanDao::getInstance();
        $this->membershipUserDao = MEMBERSHIP_BOL_MembershipUserDao::getInstance();
    }

    /**
     * Returns class instance
     *
     * @return MEMBERSHIP_BOL_MembershipService
     */
    public static function getInstance()
    {
        if ( null === self::$classInstance )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }
    /* ------- Memebrship type methods ------- */

    /**
     * Get list of all membership types
     * 
     * @return array of MEMBERSHIP_BOL_MembershipType
     */
    public function getTypeList()
    {
        return $this->membershipTypeDao->getAllTypeList();
    }

    /**
     * Get list of mambership types & their plans
     * 
     * @return array mixed
     */
    public function getTypeListWithPlans()
    {
        $types = $this->membershipTypeDao->getTypeList();

        $typesWithPlans = array();

        foreach ( $types as $key => $type )
        {
            $typesWithPlans[$key] = $type;
            $typesWithPlans[$key]['plans'] = $this->getPlanList($type['id']);
        }

        return $typesWithPlans;
    }

    /**
     * Finds membership type by type Id
     * 
     * @param int $typeId
     * @return MEMBERSHIP_BOL_MembershipType
     */
    public function findTypeById( $typeId )
    {
        return $this->membershipTypeDao->findById($typeId);
    }

    /**
     * Finds membership type by plan Id
     * 
     * @param int $planId
     * @return MEMBERSHIP_BOL_MembershipType
     */
    public function findTypeByPlanId( $planId )
    {
        if ( !$planId )
        {
            return false;
        }

        $plan = $this->findPlanById($planId);

        if ( $plan )
        {
            return $this->findTypeById($plan->typeId);
        }

        return false;
    }

    /**
     * Adds membership type & plan if passed
     * 
     * @param MEMBERSHIP_BOL_MembershipType $type
     * @param MEMBERSHIP_BOL_MembershipPlan $plan
     * @return boolean
     */
    public function addType( MEMBERSHIP_BOL_MembershipType $type, MEMBERSHIP_BOL_MembershipPlan $plan = null )
    {
        $this->membershipTypeDao->save($type);

        if ( $plan !== null )
        {
            $plan->typeId = $type->id;

            $this->addPlan($plan);
        }

        return true;
    }

    /**
     * Updates membership type
     * 
     * @param MEMBERSHIP_BOL_MembershipType $type
     * @return int
     */
    public function updateType( MEMBERSHIP_BOL_MembershipType $type )
    {
        $this->membershipTypeDao->save($type);

        return $type->id;
    }

    /**
     * Deletes membership type
     * 
     * @param int $typeId
     */
    public function deleteType( $typeId )
    {
        $this->membershipTypeDao->deleteById($typeId);
    }

    /**
     * Deletes membership type & its plans
     * 
     * @param int $typeId
     */
    public function deleteTypeWithPlans( $typeId )
    {
        $this->membershipTypeDao->deleteById($typeId);

        $this->membershipPlanDao->deletePlansByTypeId($typeId);
    }
    /* ------- Memebrship plan methods ------- */

    /**
     * Finds plan by Id
     * 
     * @param int $planId
     * @return MEMBERSHIP_BOL_MembershipPlan
     */
    public function findPlanById( $planId )
    {
        return $this->membershipPlanDao->findById((int) $planId);
    }

    /**
     * Get the list of membership plans
     * 
     * @return array
     */
    public function getTypePlanList()
    {
        $plans = $this->membershipPlanDao->findAll();

        $typePlans = array();
        foreach ( $plans as $plan )
        {
            $plan->price = floatval($plan->price);
            $pl = array(
                'dto' => $plan,
                'plan_format' => $this->getFormattedPlan($plan->price, $plan->period, $plan->recurring)
            );

            $typePlans[$plan->typeId][] = $pl;
        }

        return $typePlans;
    }

    /**
     * Get list of plans by membership type Id
     * 
     * @param int $typeId
     * @return array of MEMBERSHIP_BOL_MembershipPlan
     */
    public function getPlanList( $typeId )
    {
        return $this->membershipPlanDao->findPlanListByTypeId($typeId);
    }

    /**
     * Adds membership plan
     * 
     * @param MEMBERSHIP_BOL_MembershipPlan $plan
     * @return int
     */
    public function addPlan( MEMBERSHIP_BOL_MembershipPlan $plan )
    {
        $this->membershipPlanDao->save($plan);

        return $plan->id;
    }

    /**
     * Updates plan
     * 
     * @param MEMBERSHIP_BOL_MembershipPlan $plan
     * @return int
     */
    public function updatePlan( MEMBERSHIP_BOL_MembershipPlan $plan )
    {
        $this->membershipPlanDao->save($plan);

        return $plan->id;
    }

    /**
     * Deletes plan
     * 
     * @param int $planId
     */
    public function deletePlan( $planId )
    {
        $this->membershipPlanDao->deleteById($planId);
    }
    
    public function deletePlansByTypeId( $typeId )
    {
        $this->membershipPlanDao->deletePlansByTypeId($typeId);
    }

    /**
     * Get plan formatted string
     * 
     * @param float $price
     * @param int $period
     * @param boolean $recurring
     * @param string $currency
     * @return string
     */
    public function getFormattedPlan( $price, $period, $recurring = false, $currency = null )
    {
        $currency = isset($currency) ? $currency : BOL_BillingService::getInstance()->getActiveCurrency();
        $params = array('currency' => $currency, 'price' => floatval($price), 'period' => $period);
        $langKey = $recurring ? 'plan_struct_recurring' : 'plan_struct';

        $lang = OW::getLanguage();

        return $lang->text('membership', $langKey, $params);
    }
    /* ------- Misc methods ------- */

    /**
     * Get membership title by authorization role Id 
     * 
     * @param int $roleId
     * @return string
     */
    public function getMembershipTitle( $roleId )
    {
        $role = BOL_AuthorizationService::getInstance()->getRoleById($roleId);

        if ( $role )
        {
            return OW::getLanguage()->text('base', 'authorization_role_' . $role->name);
        }

        return '_ROLE_NOT_FOUND_';
    }
    
    /**
     * Set user membership
     * 
     * @param MEMBERSHIP_BOL_MembershipUser $userMembership
     */
    public function setUserMembership( MEMBERSHIP_BOL_MembershipUser $userMembership )
    {
        $userId = $userMembership->userId;
        $newType = $this->findTypeById($userMembership->typeId);

        /* @var $currentMembership MEMBERSHIP_BOL_MembershipUser */
        $currentMembership = $this->getUserMembership($userId);

        $authService = BOL_AuthorizationService::getInstance();

        if ( $currentMembership )
        {
            $currentType = $this->findTypeById($currentMembership->typeId);
            $authService->deleteUserRole($userId, $currentType->roleId);
            $this->deleleUserMembership($currentMembership);
        }

        $authService->saveUserRole($userId, $newType->roleId);
        $this->membershipUserDao->save($userMembership);
    }

    /**
     * Deletes users' expired memberships
     * 
     * @return boolean
     */
    public function expireUsersMemberships()
    {
        $msList = $this->membershipUserDao->findExpiredMemberships();

        if ( !$msList )
        {
            return;
        }
        
        $authService = BOL_AuthorizationService::getInstance();
        
        foreach ( $msList as $ms )
        {
            $type = $this->findTypeById($ms->typeId);
            $authService->deleteUserRole($ms->userId, $type->roleId);
            $authService->assignDefaultRoleToUser($ms->userId);            
            $this->membershipUserDao->deleteById($ms->id);
        }
        
        return true;
    }

    /**
     * Returns user's membership
     * 
     * @param int $userId
     * @return MEMBERSHIP_BOL_MembershipUser
     */
    public function getUserMembership( $userId )
    {
        return $this->membershipUserDao->findByUserId($userId);
    }
    
    public function getUserListByMembershipType( $typeId, $page, $onPage )
    {
        return $this->membershipUserDao->findByTypeId($typeId, $page, $onPage);
    }
    
    public function countUsersByMembershipType( $typeId )
    {
        return $this->membershipUserDao->countByTypeId($typeId);
    }
    
    public function deleteMembershipTypeByRoleId( $roleId )
    {
        $types = $this->membershipTypeDao->getTypeIdListByRoleId($roleId);
        
        if ( $types )
        {
            foreach ( $types as $typeId )
            {
                $this->membershipPlanDao->deletePlansByTypeId($typeId);
            }
        }
            
        $this->membershipTypeDao->deleteByRoleId($roleId);
        
        return true;        
    }
    
    public function deleteUserMembershipsByRoleId( $roleId )
    {        
        $types = $this->membershipTypeDao->getTypeIdListByRoleId($roleId);
        
        if ( $types )
        {
            foreach ( $types as $typeId )
            {
                $this->membershipUserDao->deleteByTypeId($typeId);
            }
        }
        
        return true;        
    }

    /**
     * Deletes user's membership
     * 
     * @param MEMBERSHIP_BOL_MembershipUser $userMembership
     * @return boolean
     */
    public function deleleUserMembership( MEMBERSHIP_BOL_MembershipUser $userMembership )
    {
        $this->membershipUserDao->delete($userMembership);

        return true;
    }
    
    public function deleleUserMembershipByUserId( $userId )
    {
        $membership = $this->getUserMembership($userId);
        
        if ( $membership )
        {   
            $this->membershipUserDao->delete($membership);
        }

        return true;
    }

    /**
     * Returns array of actions not shown on subscribe page
     * 
     * @return array
     */
    public function getSubscribeHiddenActions()
    {
        $json = OW::getConfig()->getValue('membership', 'subscribe_hidden_actions');

        return mb_strlen($json) ? json_decode($json) : array();
    }

    /**
     * Sets array of actions not shown on subscribe page
     * 
     * @param array $actions
     * @return boolean
     */
    public function setSubscribeHiddenActions( array $actions )
    {
        if ( !count($actions) )
        {
            return false;
        }

        OW::getConfig()->saveConfig('membership', 'subscribe_hidden_actions', json_encode($actions));

        return true;
    }

    /**
     * Returns the list of group actions for subscribe form 
     * 
     * @return array
     */
    public function getSubscribePageGroupActionList()
    {
        $service = BOL_AuthorizationService::getInstance();
        $actions = $service->getActionList();
        $groups = $service->getGroupList();
        $hiddenActions = $this->getSubscribeHiddenActions();

        $groupActionList = array();

        foreach ( $groups as $group )
        {
            /* @var $group BOL_AuthorizationGroup */
            $groupActionList[$group->id]['name'] = $group->name;
            $groupActionList[$group->id]['actions'] = array();
        }

        foreach ( $actions as $action )
        {
            /* @var $action BOL_AuthorizationAction */
            if ( !in_array($action->id, $hiddenActions) )
            {
                $groupActionList[$action->groupId]['actions'][] = $action;
            }
        }

        $pm = OW::getPluginManager();
        foreach ( $groupActionList as $key => $value )
        {
            if ( count($value['actions']) === 0 || !$pm->isPluginActive($value['name']) )
            {
                unset($groupActionList[$key]);
            }
        }

        return $groupActionList;
    }

    public function getSubsequentRoleIdList( $currentRoleId = null )
    {
        $authService = BOL_AuthorizationService::getInstance();
        $roleList = $authService->findNonGuestRoleList();

        $list = array();

        if ( !$roleList )
        {
            return $list;
        }

        if ( !$currentRoleId )
        {
            foreach ( $roleList as $role )
            {
                $list[] = $role->id;
            }
        }
        else
        {
            $currentRole = $authService->getRoleById($currentRoleId);

            foreach ( $roleList as $role )
            {
                if ( $role->id != $currentRoleId && $role->sortOrder > $currentRole->sortOrder )
                {
                    $list[] = $role->id;
                }
            }
        }

        return $list;
    }

    public function getPromoActionList( $userId, $limit = 3 )
    {
        if ( !$userId )
        {
            return null;
        }

        $authService = BOL_AuthorizationService::getInstance();
        $userMembership = $this->getUserMembership($userId);
        $roleId = null;
        if ( $userMembership )
        {
            $roleId = $this->findTypeById($userMembership->typeId)->roleId;
        }
        else
        {
            $userRoleList = $authService->findUserRoleList($userId);
            if ( $userRoleList )
            {
                $lastRole = array_pop($userRoleList);
                $roleId = $lastRole->id;
            }
        }

        $roleIdList = $this->getSubsequentRoleIdList($roleId);
        if ( !$roleIdList )
        {
            return null;
        }

        $permissions = BOL_AuthorizationService::getInstance()->getPermissionList();
        $currentRoleActions = array();
        foreach ( $permissions as $permission )
        {
            if ( $permission->roleId == $roleId )
            {
                $currentRoleActions[] = $permission->actionId;
            }
        }

        $hiddenActions = $this->getSubscribeHiddenActions();

        $allowedActions = array();
        $count = 0;
        foreach ( $permissions as $permission )
        {
            if ( in_array($permission->roleId, $roleIdList) && !in_array($permission->actionId, $hiddenActions)
                && !in_array($permission->actionId, $allowedActions) && !in_array($permission->actionId, $currentRoleActions) )
            {
                if ( $count > $limit )
                {
                    break;
                }
                $allowedActions[] = $permission->actionId;
                $count++;
            }
        }

        // collecting labels
        $event = new BASE_CLASS_EventCollector('admin.add_auth_labels');
        OW::getEventManager()->trigger($event);
        $data = $event->getData();
        $dataLabels = empty($data) ? array() : call_user_func_array('array_merge', $data);

        $groupActionList = $this->getSubscribePageGroupActionList();

        $labels = array();
        foreach ( $groupActionList as $groupAction )
        {
            foreach ( $groupAction['actions'] as $action )
            {
                if  ( in_array( $action->id, $allowedActions) )
                {
                    $labels[] = isset($dataLabels[$groupAction['name']]) ? $dataLabels[$groupAction['name']]['actions'][$action->name] : null;
                }
            }
        }

        return $labels;
    }

    /**
     * Returns list of roles which can be assigned to memberships 
     * 
     * @return array
     */
    public function getRolesAvailableForMembership()
    {
        $authService = BOL_AuthorizationService::getInstance();

        $roles = $authService->findNonGuestRoleList();
        $default = $authService->getDefaultRole();

        foreach ( $roles as $key => $role )
        {
            if ( $role->id == $default->id )
            {
                unset($roles[$key]);
            }
        }

        return $roles;
    }
}