<?php

/**
 * Copyright (c) 2013, Oxwall CandyStore
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * Affiliates admin controller
 *
 * @author Oxwall CandyStore <plugins@oxcandystore.com>
 * @package ow.ow_plugins.ocs_affiliates.controllers
 * @since 1.5.3
 */
class OCSAFFILIATES_CTRL_Admin extends ADMIN_CTRL_Abstract
{
    /**
     * @var OCSAFFILIATES_BOL_Service
     */
    private $service;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->service = OCSAFFILIATES_BOL_Service::getInstance();
    }
    
    public function index ()
    {
        $this->addComponent('menu', $this->getMenu('list'));
        $lang = OW::getLanguage();
        
        $limit = 20;
        $page = !empty($_GET['page']) ? abs((int) $_GET['page']) : 1;
        $offset = ($page - 1) * $limit;

        $sortFields = $this->service->getSortFields();
        $sortBy = !empty($_GET['sort']) && in_array($_GET['sort'], $sortFields) ? $_GET['sort'] : 'registerStamp';
        $sortOrder = !empty($_GET['order']) && in_array($_GET['order'], array('asc', 'desc')) ? $_GET['order'] : 'desc';
        $sortUrls = array();
        $baseUrl = OW::getRouter()->urlForRoute('ocsaffiliates.admin') . '/?';
        foreach ( $sortFields as $field )
        {
            $sortUrls[$field] = $baseUrl . 'sort=' . $field . '&order=' . ($sortBy != $field ? 'desc' : ($sortOrder == 'desc' ? 'asc' : 'desc'));
        }
        $this->assign('sortUrls', $sortUrls);

        $list = $this->service->getAffiliateList($offset, $limit, $sortBy, $sortOrder);
        $this->assign('list', $list);

        $total = $this->service->countAffiliates();

        $unverified = $this->service->countUnverifiedAffiliates();
        $this->assign('unverified', $unverified);

        // Paging
        $pages = (int) ceil($total / $limit);
        $paging = new BASE_CMP_Paging($page, $pages, $limit);
        $this->assign('paging', $paging->render());

        $billingService = BOL_BillingService::getInstance();
        $this->assign('currency', $billingService->getActiveCurrency());
        
        $logo = OW::getPluginManager()->getPlugin('ocsaffiliates')->getStaticUrl() . 'img/oxwallcandystore-logo.jpg';
        $this->assign('logo', $logo);

        $script = '$(".action_delete").click(function(){

            if ( !confirm('.json_encode($lang->text('ocsaffiliates', 'delete_confirm')).') )
            {
                return false;
            }
            var affId = $(this).attr("affid");
            $.ajax({
                url: '.json_encode(OW::getRouter()->urlForRoute('ocsaffiliates.action_delete')).',
                type: "POST",
                data: { affiliateId: affId },
                dataType: "json",
                success: function(data)
                {
                    if ( data.result == true )
                    {
                        document.location.reload();
                    }
                    else if ( data.error != undefined )
                    {
                        OW.warning(data.error);
                    }
                }
            });
        });';
        OW::getDocument()->addOnloadScript($script);

        // TODO: remove this code when a sale event is available
        $this->service->processUntrackedSales();

        OW::getDocument()->setHeading($lang->text('ocsaffiliates', 'admin_page_heading'));
    }
    
    public function affiliate( array $params )
    {
        $affiliateId = (int) $params['affId'];
        $affiliate = $this->service->findAffiliateById($affiliateId);
        
        if ( !$affiliate )
        {
            throw new Redirect404Exception();
        }

        $lang = OW::getLanguage();
        $this->addComponent('menu', $this->getMenu('list'));
        $this->assign('affiliate', $affiliate);

        OW::getDocument()->setHeading($lang->text('ocsaffiliates', 'affiliate_info', array('name' => $affiliate->name)));

        $this->addComponent('info', new OCSAFFILIATES_CMP_AffiliateInfo($affiliateId, true));
        $this->addComponent('stats', new OCSAFFILIATES_CMP_AffiliateStats($affiliateId));
        $this->addComponent('payouts', new OCSAFFILIATES_CMP_AffiliatePayouts($affiliateId, true));

        $script =
        '$("#btn-affiliate-edit").click(function(){
            editAffiliateFloatBox = OW.ajaxFloatBox(
                "OCSAFFILIATES_CMP_AffiliateEdit",
                { affiliateId: ' . $affiliate->id . ', mode: "admin" } ,
                { width: 700, title: ' . json_encode($lang->text('ocsaffiliates', 'edit')) . ' }
            );
        });

        $("#btn-register-payout").click(function(){
            registerPayoutFloatBox = OW.ajaxFloatBox(
                "OCSAFFILIATES_CMP_RegisterPayout",
                { affiliateId: ' . $affiliate->id . ' },
                { width: 500, title: ' . json_encode($lang->text('ocsaffiliates', 'register_payout')) . ' }
            );
        });

        $("#btn-login").click(function(){
            $.ajax({
                url: '.json_encode(OW::getRouter()->urlForRoute('ocsaffiliates.action_login_as')).',
                type: "POST",
                data: { affiliateId: ' . $affiliate->id . ' },
                dataType: "json",
                success: function(data)
                {
                    if ( data.result == true )
                    {
                        document.location.href = data.url;
                    }
                    else if ( data.error != undefined )
                    {
                        OW.warning(data.error);
                    }
                }
            });
        });

        $("#btn-affiliate-delete").click(function(){
            if ( confirm('.json_encode($lang->text('ocsaffiliates', 'delete_confirm')).') )
            {
                alert("delete");
            }
        });

        $(".action_delete_payout").click(function(){
            if ( !confirm('.json_encode($lang->text('ocsaffiliates', 'payout_delete_confirm')).') ) {
                return false;
            }
            var pid = $(this).attr("pid");
            $.ajax({
                url: '.json_encode(OW::getRouter()->urlForRoute('ocsaffiliates.action_delete_payout')).',
                type: "POST",
                data: { payoutId: pid },
                dataType: "json",
                success: function(data)
                {
                    if ( data.result == true )
                    {
                        document.location.reload();
                    }
                    else if ( data.error != undefined )
                    {
                        OW.warning(data.error);
                    }
                }
            });
        });
        ';

        OW::getDocument()->addOnloadScript($script);
    }

    public function banners()
    {
        $this->addComponent('menu', $this->getMenu('banners'));

        $service = OCSAFFILIATES_BOL_Service::getInstance();
        $lang = OW::getLanguage();

        OW::getDocument()->setHeading($lang->text('ocsaffiliates', 'admin_page_heading'));

        if ( isset($_POST['action']) && $_POST['action'] == 'add_banner' )
        {
            if ( empty($_FILES['banner_file']) || !$service->validateBannerFileType($_FILES['banner_file']) )
            {
                OW::getFeedback()->error($lang->text('ocsaffiliates', 'banner_file_incorrect'));
            }
            $added = $service->addAffiliateBanner(0, $_FILES['banner_file']);

            if ( $added )
            {
                OW::getFeedback()->info($lang->text('ocsaffiliates', 'banner_added'));
            }
            else
            {
                OW::getFeedback()->error($lang->text('ocsaffiliates', 'banner_add_error'));
            }
            $this->redirect();
        }

        $this->assign('bannerList', $service->getBannerListForAffiliate(0));

        $script =
            '$(".action_delete_banner").click(function(){
                var bannerId = $(this).attr("bid");
                if ( confirm('.json_encode($lang->text('ocsaffiliates', 'banner_delete_confirm')).') )
            {
                $.ajax({
                    url: '.json_encode(OW::getRouter()->urlForRoute('ocsaffiliates.action_delete_banner')).',
                    type: "POST",
                    data: { bannerId: bannerId },
                    dataType: "json",
                    success: function(data)
                    {
                        if ( data.result == true )
                        {
                            document.location.reload();
                        }
                        else if ( data.error != undefined )
                        {
                            OW.warning(data.error);
                        }
                    }
                });
            }
        });
        ';
        OW::getDocument()->addOnloadScript($script);
    }
    
    public function settings()
    {
        $this->addComponent('menu', $this->getMenu('settings'));
        
        $lang = OW::getLanguage();
        $config = OW::getConfig();
        $form = new OCSAFFILIATES_CLASS_SettingsForm('settings');
        
        $form->getElement('period')->setValue($config->getValue('ocsaffiliates', 'period'));
        $form->getElement('clickAmount')->setValue($config->getValue('ocsaffiliates', 'click_amount'));
        $form->getElement('regAmount')->setValue($config->getValue('ocsaffiliates', 'reg_amount'));
        $form->getElement('saleCommission')->setValue($config->getValue('ocsaffiliates', 'sale_commission'));
        $form->getElement('saleAmount')->setValue($config->getValue('ocsaffiliates', 'sale_amount'));
        $form->getElement('salePercent')->setValue($config->getValue('ocsaffiliates', 'sale_percent'));
        $form->getElement('status')->setValue($config->getValue('ocsaffiliates', 'signup_status'));
        $form->getElement('showRates')->setValue($config->getValue('ocsaffiliates', 'show_rates'));
        $form->getElement('allowBanners')->setValue($config->getValue('ocsaffiliates', 'allow_banners'));
        $form->getElement('terms')->setValue($config->getValue('ocsaffiliates', 'terms_agreement'));
        $this->addForm($form);
        
        if ( OW::getRequest()->isPost() && $form->isValid($_POST) )
        {
            $values = $form->getValues();
            
            $config->saveConfig('ocsaffiliates', 'period', $values['period']);
            $config->saveConfig('ocsaffiliates', 'click_amount', $values['clickAmount']);
            $config->saveConfig('ocsaffiliates', 'reg_amount', $values['regAmount']);
            $config->saveConfig('ocsaffiliates', 'sale_commission', $values['saleCommission']);
            $config->saveConfig('ocsaffiliates', 'sale_amount', $values['saleAmount']);
            $config->saveConfig('ocsaffiliates', 'sale_percent', $values['salePercent']);
            $config->saveConfig('ocsaffiliates', 'signup_status', $values['status']);
            $config->saveConfig('ocsaffiliates', 'show_rates', (int) $values['showRates']);
            $config->saveConfig('ocsaffiliates', 'allow_banners', (int) $values['allowBanners']);
            $config->saveConfig('ocsaffiliates', 'terms_agreement', (int) $values['terms']);
            
            OW::getFeedback()->info($lang->text('ocsaffiliates', 'settings_updated'));
            $this->redirect();
        }

        $this->assign('saleComission', $config->getValue('ocsaffiliates', 'sale_commission'));

        $script =
        '$("select[name=saleCommission]").change(function(){
            var type = $(this).val();
            if ( type == "amount" ) {
                $("#tr_amount").show();
                $("#tr_percent").hide();
            }
            else {
                $("#tr_percent").show();
                $("#tr_amount").hide();
            }
        });

        $("#edit-agreement").click(function(){
            OW.editLanguageKey("ocsaffiliates", "terms_text", function(){
                OW.info('.json_encode($lang->text('ocsaffiliates', 'agreement_updated')).');
            });
        });
        ';
        OW::getDocument()->addOnloadScript($script);

        OW::getDocument()->setHeading($lang->text('ocsaffiliates', 'admin_page_heading'));
    }

    /**
     * Returns menu component
     *
     * @param $active
     * @return BASE_CMP_ContentMenu
     */
    private function getMenu( $active )
    {
        $language = OW::getLanguage();
        $menuItems = array();
        
        $item = new BASE_MenuItem();
        $item->setLabel($language->text('ocsaffiliates', 'affiliate_list'));
        $item->setUrl(OW::getRouter()->urlForRoute('ocsaffiliates.admin'));
        $item->setKey('list');
        $item->setActive($active == 'list');
        $item->setIconClass('ow_ic_script');
        $item->setOrder(0);

        array_push($menuItems, $item);
        
        $item = new BASE_MenuItem();
        $item->setLabel($language->text('ocsaffiliates', 'settings'));
        $item->setUrl(OW::getRouter()->urlForRoute('ocsaffiliates.admin_settings'));
        $item->setKey('settings');
        $item->setActive($active == 'settings');
        $item->setIconClass('ow_ic_gear_wheel');
        $item->setOrder(1);

        array_push($menuItems, $item);

        $item = new BASE_MenuItem();
        $item->setLabel($language->text('ocsaffiliates', 'banners'));
        $item->setUrl(OW::getRouter()->urlForRoute('ocsaffiliates.admin_banners'));
        $item->setKey('banners');
        $item->setActive($active == 'banners');
        $item->setIconClass('ow_ic_picture');
        $item->setOrder(2);

        array_push($menuItems, $item);
        
        $menu = new BASE_CMP_ContentMenu($menuItems);

        return $menu;
    }
}