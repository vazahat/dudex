<?php

/**
 * Copyright (c) 2009, Skalfa LLC
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * Membership subscribe page controller.
 *
 * @author Egor Bulgakov <egor.bulgakov@gmail.com>
 * @package ow.ow_plugins.membership.controllers
 * @since 1.0
 */
class MEMBERSHIP_CTRL_Subscribe extends OW_ActionController
{

    public function index()
    {
        if ( !OW::getUser()->isAuthenticated() )
        {
            throw new AuthenticateException();
        }
        
        $form = new SubscribeForm();
        $this->addForm($form);

        if ( OW::getRequest()->isPost() && $form->isValid($_POST) )
        {
            $form->process();
        }

        $membershipService = MEMBERSHIP_BOL_MembershipService::getInstance();
        $authService = BOL_AuthorizationService::getInstance();

        $actions = $membershipService->getSubscribePageGroupActionList();
        $this->assign('groupActionList', $actions);

        $mTypes = $membershipService->getTypeList();

        /* @var $defaultRole BOL_AuthorizationRole */
        $defaultRole = $authService->getDefaultRole();

        /* @var $default MEMBERSHIP_BOL_MembershipType */
        $default = new MEMBERSHIP_BOL_MembershipType();
        $default->roleId = $defaultRole->id;

        $mTypes = array_merge(array($default), $mTypes);

        $userId = OW::getUser()->getId();
        $userMembership = $membershipService->getUserMembership($userId);
        $userRoleIds = array($defaultRole->id);
        
        if ( $userMembership )
        {
            $type = $membershipService->findTypeById($userMembership->typeId);
            if ( $type )
            {
                $userRoleIds[] = $type->roleId;
            }

            $this->assign('current', $userMembership);
            $this->assign('currentTitle', $membershipService->getMembershipTitle($type->roleId));
        }
        
        $permissions = $authService->getPermissionList();

        $perms = array();
        foreach ( $permissions as $permission )
        {
            /* @var $permission BOL_AuthorizationPermission */
            $perms[$permission->roleId][$permission->actionId] = true;
        }

        $mPlans = $membershipService->getTypePlanList();

        $mTypesPermissions = array();
        foreach ( $mTypes as $membership )
        {
            $mId = $membership->id;
            $data = array(
                'id' => $mId,
                'title' => $membershipService->getMembershipTitle($membership->roleId),
                'roleId' => $membership->roleId,
                'permissions' => isset($perms[$membership->roleId]) ? $perms[$membership->roleId] : null,
                'current' => in_array($membership->roleId, $userRoleIds),
                'plans' => isset($mPlans[$mId]) ? $mPlans[$mId] : null
            );

            $mTypesPermissions[$mId] = $data;
        }

        $this->assign('mTypePermissions', $mTypesPermissions);

        $this->assign('typesNumber', count($mTypes));

        // collecting labels
        $event = new BASE_CLASS_EventCollector('admin.add_auth_labels');
        OW::getEventManager()->trigger($event);
        $data = $event->getData();

        $dataLabels = empty($data) ? array() : call_user_func_array('array_merge', $data);
        $this->assign('labels', $dataLabels);
        
        $gateways = BOL_BillingService::getInstance()->getActiveGatewaysList();
        $this->assign('gatewaysActive', (bool) $gateways);
        
        $lang = OW::getLanguage();

        $this->setPageHeading($lang->text('membership', 'subscribe_page_heading'));
        $this->setPageHeadingIconClass('ow_ic_user');
    }
}

/**
 * Subscribe form class
 */
class SubscribeForm extends Form
{

    public function __construct()
    {
        parent::__construct('subscribe-form');

        $planField = new RadioGroupItemField('plan');
        $planField->setRequired();
        $this->addElement($planField);

        $gatewaysField = new BillingGatewaySelectionField('gateway');
        $gatewaysField->setRequired();
        $this->addElement($gatewaysField);

        $submit = new Submit('subscribe');
        $submit->setValue(OW::getLanguage()->text('membership', 'checkout'));
        $this->addElement($submit);
    }

    public function process()
    {
        $values = $this->getValues();
        $lang = OW::getLanguage();
        $userId = OW::getUser()->getId();
        
        $billingService = BOL_BillingService::getInstance();
        $membershipService = MEMBERSHIP_BOL_MembershipService::getInstance();
        
        if ( empty($values['gateway']['url']) || empty($values['gateway']['key']) 
                || !$gateway = $billingService->findGatewayByKey($values['gateway']['key'])
                || !$gateway->active )
        {
            OW::getFeedback()->error($lang->text('base', 'billing_gateway_not_found'));
            OW::getApplication()->redirect(OW::getRouter()->urlForRoute('membership_subscribe'));
        }
        
        if ( !$plan = $membershipService->findPlanById($values['plan']) )
        {
            OW::getFeedback()->error($lang->text('membership', 'plan_not_found'));
            OW::getApplication()->redirect(OW::getRouter()->urlForRoute('membership_subscribe'));
        }
        
        // create membership plan product adapter object
        $productAdapter = new MEMBERSHIP_CLASS_MembershipPlanProductAdapter();
        
        // sale object
        $sale = new BOL_BillingSale();
        $sale->pluginKey = 'membership';
        $sale->entityDescription = $membershipService->getFormattedPlan($plan->price, $plan->period, $plan->recurring);
        $sale->entityKey = $productAdapter->getProductKey();
        $sale->entityId = $plan->id;
        $sale->price = floatval($plan->price);
        $sale->period = $plan->period;
        $sale->userId = $userId ? $userId : 0;
        $sale->recurring = $plan->recurring;

        $saleId = $billingService->initSale($sale, $values['gateway']['key']);

        if ( $saleId )
        {
            // sale Id is temporarily stored in session
            $billingService->storeSaleInSession($saleId);
            $billingService->setSessionBackUrl($productAdapter->getProductOrderUrl());

            // redirect to gateway form page 
            OW::getApplication()->redirect($values['gateway']['url']);
        }
    }
}