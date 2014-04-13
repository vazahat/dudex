<?php

/**
 * Copyright (c) 2009, Skalfa LLC
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * Membership admin controller.
 *
 * @author Egor Bulgakov <egor.bulgakov@gmail.com>
 * @package ow.ow_plugins.membership.controllers
 * @since 1.0
 */
class MEMBERSHIP_CTRL_Admin extends ADMIN_CTRL_Abstract
{
    private $membershipService;

    public function __construct()
    {
        parent::__construct();

        $this->membershipService = MEMBERSHIP_BOL_MembershipService::getInstance();
    }

    private function getMenu()
    {
        $language = OW::getLanguage();
        $menuItems = array();

        $item = new BASE_MenuItem();
        $item->setLabel($language->text('membership', 'admin_menu_memberships'));
        $item->setUrl(OW::getRouter()->urlForRoute('membership_admin'));
        $item->setKey('memberships');
        $item->setIconClass('ow_ic_update');
        $item->setOrder(0);

        $menuItems[] = $item;

        $item = new BASE_MenuItem();
        $item->setLabel($language->text('membership', 'admin_menu_subscribe'));
        $item->setUrl(OW::getRouter()->urlForRoute('membership_admin_subscribe'));
        $item->setKey('subscribe');
        $item->setIconClass('ow_ic_script');
        $item->setOrder(1);

        $menuItems[] = $item;

        $item = new BASE_MenuItem();
        $item->setLabel($language->text('membership', 'admin_menu_browse_users'));
        $item->setUrl(OW::getRouter()->urlForRoute('membership_admin_browse_users_st'));
        $item->setKey('users');
        $item->setIconClass('ow_ic_user');
        $item->setOrder(2);

        $menuItems[] = $item;
        
        return new BASE_CMP_ContentMenu($menuItems);
    }

    public function index()
    {
        $lang = OW::getLanguage();
        $this->addComponent('menu', $this->getMenu());

        $memberships = $this->membershipService->getTypeListWithPlans();
        $this->assign('memberships', $memberships);

        $this->setPageHeading($lang->text('membership', 'admin_page_heading_memberships'));
        $this->setPageHeadingIconClass('ow_ic_user');

        $form = new AddMembershipForm();
        $this->addForm($form);

        if ( OW::getRequest()->isPost() )
        {
            if ( isset($_POST['delete_types']) )
            {
                foreach ( $_POST['types'] as $typeId )
                {
                    $this->membershipService->deleteTypeWithPlans($typeId);
                }

                OW::getFeedback()->info($lang->text('membership', 'types_deleted'));
                $this->redirect();
            }
            else if ( $form->isValid($_POST) && $form->process() )
            {
                OW::getFeedback()->info($lang->text('membership', 'membership_added'));
                $this->redirect();
            }
        }

        $this->assign('currency', BOL_BillingService::getInstance()->getActiveCurrency());

        $lang->addKeyForJs('membership', 'no_types_selected');
        $lang->addKeyForJs('membership', 'type_delete_confirm');
    }

    public function edit( array $params )
    {
        if ( !$params['id'] )
        {
            $this->redirectToAction('index');
        }

        $typeId = $params['id'];

        $lang = OW::getLanguage();

        $form = new AddPlanForm($typeId);
        $this->addForm($form);

        $formEdit = new EditMembershipForm($typeId);
        $this->addForm($formEdit);

        if ( OW::getRequest()->isPost() )
        {
            if ( isset($_POST['update_plans']) )
            {
                foreach ( $_POST['periods'] as $planId => $period )
                {
                    $plan = $this->membershipService->findPlanById($planId);
                    if ( $plan )
                    {
                        $plan->period = isset($_POST['periods'][$planId]) && intval($_POST['periods'][$planId]) ? $_POST['periods'][$planId] : $plan->period;
                        $plan->price = isset($_POST['prices'][$planId]) && floatval($_POST['prices'][$planId]) ? $_POST['prices'][$planId] : $plan->price;
                        $plan->recurring = isset($_POST['recurring'][$planId]) ? (bool) $_POST['recurring'][$planId] : false;

                        $this->membershipService->updatePlan($plan);
                    }
                }
                OW::getFeedback()->info($lang->text('membership', 'plans_updated'));
                $this->redirect();
            }
            else if ( isset($_POST['delete_plans']) )
            {
                foreach ( $_POST['plans'] as $plan => $val )
                {
                    $this->membershipService->deletePlan($plan);
                }
                OW::getFeedback()->info($lang->text('membership', 'plans_deleted'));
                $this->redirect();
            }
            else if ( $_POST['form_name'] == 'edit-membership-form' )
            {
                if ( $formEdit->isValid($_POST) && $formEdit->process() )
                {
                    OW::getFeedback()->info($lang->text('membership', 'type_updated'));
                    $this->redirect();
                }
            }
            else if ( $_POST['form_name'] == 'add-plan-form' )
            {
                if ( $form->isValid($_POST) && $form->process() )
                {
                    OW::getFeedback()->info($lang->text('membership', 'plan_added'));
                    $this->redirect();
                }
            }
        }

        $menu = $this->getMenu();
        $menu->getElement('memberships')->setActive(true);

        $this->addComponent('menu', $menu);

        $this->setPageHeading($lang->text('membership', 'admin_page_heading_memberships'));
        $this->setPageHeadingIconClass('ow_ic_update');

        $type = $this->membershipService->findTypeById($typeId);
        $this->assign('type', $type);

        $plans = $this->membershipService->getPlanList($typeId);
        $this->assign('plans', $plans);

        $this->assign('currency', BOL_BillingService::getInstance()->getActiveCurrency());

        $lang->addKeyForJs('membership', 'no_plans_selected');
        $lang->addKeyForJs('membership', 'plan_delete_confirm');
    }

    public function subscribe()
    {
        if ( isset($_POST['actions']) )
        {
            $hidden = array();

            foreach ( $_POST['actions'] as $id => $isDisplayed )
            {
                if ( $isDisplayed == 0 )
                {
                    array_push($hidden, $id);
                }
            }

            $this->membershipService->setSubscribeHiddenActions($hidden);
            $this->redirect();
        }

        $lang = OW::getLanguage();
        $this->addComponent('menu', $this->getMenu());

        $service = BOL_AuthorizationService::getInstance();

        $actions = $service->getActionList();
        $groups = $service->getGroupList();

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
            $groupActionList[$action->groupId]['actions'][] = $action;
        }

        $pm = OW::getPluginManager();
        foreach ( $groupActionList as $key => $value )
        {
            if ( count($value['actions']) === 0 || !$pm->isPluginActive($value['name']) )
            {
                unset($groupActionList[$key]);
            }
        }

        $this->assign('groupActionList', $groupActionList);
        
        // collecting labels
        $event = new BASE_CLASS_EventCollector('admin.add_auth_labels');
        OW::getEventManager()->trigger($event);
        $data = $event->getData();

        $dataLabels = empty($data) ? array() : call_user_func_array('array_merge', $data);
        $this->assign('labels', $dataLabels);

        $this->setPageHeading($lang->text('membership', 'admin_page_heading_memberships'));
        $this->setPageHeadingIconClass('ow_ic_user');

        $this->assign('hidden', $this->membershipService->getSubscribeHiddenActions());
    }
    
    public function users( array $params )
    {
        $typeId = !empty($params['typeId']) ? $params['typeId'] : null;
        
        $lang = OW::getLanguage();
        $userService = BOL_UserService::getInstance();
        
        $menu = $this->getMenu();
        $menu->getElement('users')->setActive(true);
        
        $this->addComponent('menu', $menu);
        
        $this->setPageHeading($lang->text('membership', 'admin_page_heading_users_by_membership'));
        $this->setPageHeadingIconClass('ow_ic_user');
        
        $this->assign('route', OW::getRouter()->urlForRoute('membership_admin_browse_users_st'));
        
        $memberships = $this->membershipService->getTypeList();
        
        $types = array();
        $firstTypeId = null;
        foreach ( $memberships as $id => $type )
        {
            if ( $id == 0 )
            {
                $firstTypeId = $type->id;
            }
            $types[$id]['dto'] = $type;
            $types[$id]['title'] = $this->membershipService->getMembershipTitle($type->roleId);
        }
        
        $this->assign('types', $types);
        
        $page = !empty($_GET['page']) && (int) $_GET['page'] ? abs((int) $_GET['page']) : 1;
        $onPage = 20;
        
        $typeId = $typeId ? $typeId : ($firstTypeId ? $firstTypeId : null);
        
        if ( !$typeId )
        {
            return;
        }
        
        $this->assign('typeId', $typeId);
        
        $list = $this->membershipService->getUserListByMembershipType($typeId, $page, $onPage);
        
        if ( !$list )
        {
            return;
        }
        
        $this->assign('list', $list);
        
        $total = $this->membershipService->countUsersByMembershipType($typeId);
        
        // Paging
        $pages = (int) ceil($total / $onPage);
        $paging = new BASE_CMP_Paging($page, $pages, $onPage);
        $this->addComponent('paging', $paging);
        
        $userIdList = array();

        foreach ( $list as $user )
        {
            if ( !in_array($user['userId'], $userIdList) )
            {
                array_push($userIdList, $user['userId']);
            }
        }

        $this->assign('avatars', BOL_AvatarService::getInstance()->getDataForUserAvatars($userIdList, true, true, false, false));
        
        $userNameList = $userService->getUserNamesForList($userIdList);
        $this->assign('userNameList', $userNameList);

        $displayNameList = $userService->getDisplayNamesForList($userIdList);
        $this->assign('displayNames', $displayNameList);

        $questionList = BOL_QuestionService::getInstance()->getQuestionData($userIdList, array('sex', 'birthdate', 'email'));
        $this->assign('questionList', $questionList);

        $onlineStatus = $userService->findOnlineStatusForUserList($userIdList);
        $this->assign('onlineStatus', $onlineStatus);
    }
}

class AddMembershipForm extends Form
{

    public function __construct()
    {
        parent::__construct('add-membership-form');

        $lang = OW::getLanguage();

        $rolesField = new Selectbox('role');
        $roles = MEMBERSHIP_BOL_MembershipService::getInstance()->getRolesAvailableForMembership();

        $options = array();
        
        foreach ( $roles as $role )
        {
            $options[$role->id] = $lang->text('base', 'authorization_role_' . $role->name);
        }
        if ( count($options) )
        {
            $rolesField->setOptions($options);
        }
        $rolesField
            ->setRequired(true)
            ->setLabel($lang->text('membership', 'select_role'));

        $this->addElement($rolesField);

        $periodField = new TextField('period');
        $this->addElement($periodField);

        $priceField = new TextField('price');
        $this->addElement($priceField);

        $recurringField = new CheckboxField('isRecurring');
        $this->addElement($recurringField);

        // submit
        $submit = new Submit('save');
        $submit->setValue($lang->text('membership', 'add_btn'));
        $this->addElement($submit);
    }

    public function process()
    {
        $values = $this->getValues();

        $type = new MEMBERSHIP_BOL_MembershipType();
        $type->roleId = $values['role'];

        if ( isset($values['price']) && isset($values['period']) )
        {
            $plan = new MEMBERSHIP_BOL_MembershipPlan();
            $plan->price = $values['price'];
            $plan->period = $values['period'];
            $plan->recurring = isset($values['isRecurring']) ? $values['isRecurring'] : false;
        }
        else
        {
            $plan = null;
        }

        $res = MEMBERSHIP_BOL_MembershipService::getInstance()->addType($type, $plan);

        return $res;
    }
}

class AddPlanForm extends Form
{

    public function __construct( $type )
    {
        parent::__construct('add-plan-form');

        $lang = OW::getLanguage();

        $typeField = new HiddenField('type');
        $typeField->setValue($type);
        $typeField->setRequired(true);
        $this->addElement($typeField);

        $periodField = new TextField('period');
        $periodField->setRequired(true);
        $periodField->addValidator(new IntValidator(1, 7000));
        $this->addElement($periodField);

        $priceField = new TextField('price');
        $priceField->setRequired(true);
        $priceField->addValidator(new FloatValidator(0.01, 10000));
        $this->addElement($priceField);

        $recurringField = new CheckboxField('isRecurring');
        $this->addElement($recurringField);

        // submit
        $submit = new Submit('add');
        $submit->setValue($lang->text('membership', 'add_btn'));
        $this->addElement($submit);
    }

    public function process()
    {
        $values = $this->getValues();

        $plan = new MEMBERSHIP_BOL_MembershipPlan();
        $plan->typeId = $values['type'];
        $plan->price = $values['price'];
        $plan->period = $values['period'];
        $plan->recurring = isset($values['isRecurring']) ? $values['isRecurring'] : false;

        $res = MEMBERSHIP_BOL_MembershipService::getInstance()->addPlan($plan);

        return $res;
    }
}

class EditMembershipForm extends Form
{

    public function __construct( $typeId )
    {
        parent::__construct('edit-membership-form');

        $lang = OW::getLanguage();

        $typeField = new HiddenField('type');
        $typeField->setValue($typeId);
        $typeField->setRequired(true);
        $this->addElement($typeField);

        $type = MEMBERSHIP_BOL_MembershipService::getInstance()->findTypeById($typeId);

        $rolesField = new Selectbox('role');
        $roles = MEMBERSHIP_BOL_MembershipService::getInstance()->getRolesAvailableForMembership();

        foreach ( $roles as $role )
        {
            $options[$role->id] = $lang->text('base', 'authorization_role_' . $role->name);
        }
        if ( count($options) )
        {
            $rolesField->setOptions($options);
        }
        $rolesField
            ->setRequired(true)
            ->setValue($type->roleId)
            ->setLabel($lang->text('membership', 'select_role'));

        $this->addElement($rolesField);

        // submit
        $submit = new Submit('update');
        $submit->setValue($lang->text('admin', 'save_btn_label'));
        $this->addElement($submit);
    }

    public function process()
    {
        $values = $this->getValues();

        $service = MEMBERSHIP_BOL_MembershipService::getInstance();
        $type = $service->findTypeById($values['type']);

        if ( $type )
        {
            $type->roleId = $values['role'];
            $res = $service->updateType($type);

            return (bool) $res;
        }
    }
}