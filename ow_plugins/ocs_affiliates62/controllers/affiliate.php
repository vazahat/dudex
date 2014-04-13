<?php

/**
 * Copyright (c) 2013, Oxwall CandyStore
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * Affiliates main controller
 *
 * @author Oxwall CandyStore <plugins@oxcandystore.com>
 * @package ow.ow_plugins.ocs_affiliates.controllers
 * @since 1.5.3
 */
class OCSAFFILIATES_CTRL_Affiliate extends OW_ActionController
{
    public function index ()
    {
        $lang = OW::getLanguage();
        $service = OCSAFFILIATES_BOL_Service::getInstance();
        
        $online = $service->isAuthenticated();
        $this->assign('online', $online);
        
        if ( $online )
        {
            $affiliateId = $service->getAffiliateId();
            $affiliate = $service->findAffiliateById($affiliateId);

            if ( !$affiliate )
            {
                $service->logoutAffiliate();
                $this->redirect();
            }
            $this->assign('affiliate', $affiliate);
            
            $this->setPageHeading($lang->text('ocsaffiliates', 'affiliate_area'));
            
            $menu = $this->getMenu('dashboard');
            $this->addComponent('menu', $menu);
            
            if ( !$affiliate->emailVerified )
            {
                $form = new OCSAFFILIATES_CLASS_VerificationForm('verify');
                $this->addForm($form);
            }
            else
            {
                $this->addComponent('stats', new OCSAFFILIATES_CMP_AffiliateStats($affiliateId));

                $dayStart = strtotime("midnight");
                $endOfDay = strtotime("tomorrow", $dayStart) - 1;

                $dayEnd = $endOfDay + date('Z');

                $start = time();

                if ( !empty($_GET['range']) && in_array($_GET['range'], array('month', '2months')) )
                {
                    switch ( $_GET['range'] )
                    {
                        case 'month':
                            $start = strtotime("-1 month", $dayEnd);
                            break;
                        case '2months':
                            $start = strtotime("-2 months", $dayEnd);
                    }
                }
                else
                {
                    $_GET['range'] = 'week';
                    $start = strtotime("-1 week", $dayEnd);
                }
                $this->assign('range', $_GET['range']);

                $end = $dayEnd;
                $stat = $service->getAffiliateEarningForPeriod($affiliateId, $start, $end);

                $sumData = '';
                $max = $service->getAffiliateEarningMax($stat);
                foreach ( $stat as $ts => $data )
                {
                    $sumData .= '['.($ts * 1000).', '.$data['sum'].', 0, '.(24 * 60 * 60 * 1000).'], ';
                }

                $code =
                'var datasets = {
                    "sum": {
                        lines: { show: true, lineWidth: 2, fill: true },
                        points: { show: true, radius: 2 },
                        shadowSize: 1,
                        data: ['.$sumData.'],
                        color: "rgba(153, 204, 255, 1)"
                    }
                };

                var data = [];
                data.push(datasets["sum"]);

                var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
                var opt = {
                    yaxis: { min: 0, max: '.$max.', tickColor: "#cfcfcf", position: "left" },
                    xaxis: { mode: "time", tickColor: "#cfcfcf", minTickSize: [1, "day"], timeformat: "%d %b", monthNames: monthNames },
                    grid: { hoverable: true, clickable: true, borderWidth: 1, borderColor: "#afafaf" },
                    points: { show: true },
                    legend: { position: "nw" }
                };

                var options = $.extend(true, {}, opt,
                    { hooks: { processRawData: function(a, b, c, d) {
                        b.datapoints.format = [
                            { x: true, number: true, required: true },
                            { y: true, number: true, required: true },
                            { y: true, number: true, required: false, defaultValue: 0 },
                            { x: false, number: true, required: false }
                        ];
                    }}}
                );

                $.plot($("#stats_plot"), data, options);
                var previousPoint = null;

                $("#stats_plot").bind("plothover", function (event, pos, item) {
                    $("#x").text(pos.x.toFixed(3));
                    $("#y").text(pos.y.toFixed(3));

                    if ( item ) {
                        if ( previousPoint != item.datapoint ) {
                            previousPoint = item.datapoint;
                            $("#tooltip").remove();
                            var x = item.datapoint[0].toFixed(0), y = item.datapoint[1];
                            var objDate = new Date(item.datapoint[0]);
                            showTooltip(item.pageX, item.pageY, monthNames[objDate.getMonth()] + " " + objDate.getDate() + " - " + "$" + y);
                        }
                    }
                    else {
                        $("#tooltip").remove();
                        previousPoint = null;
                    }
                });

                function showTooltip(x, y, contents) {
                    $(\'<div id="tooltip">\' + contents + \'</div>\').css( {
                        position: \'absolute\',
                        display: \'none\',
                        top: y - 35,
                        color: \'#fff\',
                        left: x + 5,
                        padding: \'1px 3px\',
                        \'background-color\': \'#007196\',
                        \'font-weight\': \'bold\',
                        \'border-radius\': \'3px\',
                        \'font-size\': \'11px\',
                        opacity: 0.90
                    }).appendTo("body").fadeIn(200);
                };

                $("#stats-date-range").change(function(){
                    document.location.href = '.json_encode(OW::getRouter()->urlForRoute('ocsaffiliates.home')).' + "/?range=" + $(this).val();
                });
                ';

                $jsUrl = OW::getPluginManager()->getPlugin('ocsaffiliates')->getStaticJsUrl();
                OW::getDocument()->addScript($jsUrl . 'jquery.flot.min.js');
                OW::getDocument()->addScript($jsUrl . 'jquery.flot.time.min.js');
                OW::getDocument()->addOnloadScript($code);
            }

            $service->updateAffiliateActivity($affiliateId);

            // TODO: remove this code when a sale event is available
            $service->processUntrackedSales();
        }
        else
        {
            $this->setPageHeading($lang->text('ocsaffiliates', 'earn_with_us'));
            
            $form = new OCSAFFILIATES_CLASS_SignupForm('signup');
            if ( OW::getUser()->isAuthenticated() )
            {
                $email = OW::getUser()->getEmail();
                $affiliate = $service->findAffiliateByEmail($email);
                if ( !$affiliate )
                {
                    $form->getElement('email')->setValue($email);
                }
            }
            $this->addForm($form);
            
            $form = new OCSAFFILIATES_CLASS_SigninForm('affiliate-signin');
            $this->addForm($form);

            $script =
            '$("#btn-forgot-password").click(function(){
                document.forgotPasswordFloatBox = OW.ajaxFloatBox(
                    "OCSAFFILIATES_CMP_ForgotPassword", { }, { width: 400, title: ' . json_encode($lang->text('ocsaffiliates', 'forgot_password')) . ' }
                );
            });
            ';

            $agreement = OW::getConfig()->getValue('ocsaffiliates', 'terms_agreement');
            $this->assign('agreement', $agreement);

            if ( $agreement )
            {
                $content = '<div>'.$lang->text('ocsaffiliates', 'terms_text').'</div>';
                $script .=
                '$("#affiliate-terms-link").click(function(){
                    fb = new OW_FloatBox({
                        $title: '.json_encode($lang->text('ocsaffiliates', 'terms_of_use')).',
                        $contents: $('.json_encode($content).'),
                        width: 520
                    });
                });
                ';
            }

            OW::getDocument()->addOnloadScript($script);
        }
    }

    public function log()
    {
        $service = OCSAFFILIATES_BOL_Service::getInstance();
        $lang = OW::getLanguage();

        if ( !$service->checkAccess() )
        {
            $this->redirect(OW::getRouter()->urlForRoute('ocsaffiliates.home'));
        }

        $affiliateId = $service->getAffiliateId();

        $menu = $this->getMenu('log');
        $this->addComponent('menu', $menu);

        $limit = 20;
        $page = !empty($_GET['page']) ? abs((int) $_GET['page']) : 1;
        $offset = ($page - 1) * $limit;

        $log = $service->getAffiliateEventsLog($affiliateId, $offset, $limit);
        $this->assign('log', $log);

        $total = $service->countAffiliateEventsLog($affiliateId);

        // Paging
        $pages = (int) ceil($total / $limit);
        $paging = new BASE_CMP_Paging($page, $pages, $limit);
        $this->assign('paging', $paging->render());

        $billingService = BOL_BillingService::getInstance();
        $this->assign('currency', $billingService->getActiveCurrency());

        $this->setPageHeading($lang->text('ocsaffiliates', 'affiliate_area'));

        $service->updateAffiliateActivity($affiliateId);

        // TODO: remove this code when a sale event is available
        $service->processUntrackedSales();
    }
    
    public function profile()
    {
        $service = OCSAFFILIATES_BOL_Service::getInstance();
        $lang = OW::getLanguage();
        
        if ( !$service->checkAccess() )
        {
            $this->redirect(OW::getRouter()->urlForRoute('ocsaffiliates.home'));
        }
        
        $affiliateId = $service->getAffiliateId();
        $this->addComponent('info', new OCSAFFILIATES_CMP_AffiliateInfo($affiliateId));

        $menu = $this->getMenu('profile');
        $this->addComponent('menu', $menu);

        $this->assign('configs', OW::getConfig()->getValues('ocsaffiliates'));

        $billingService = BOL_BillingService::getInstance();
        $this->assign('currency', $billingService->getActiveCurrency());

        $script =
            '$("#btn-affiliate-edit").click(function(){
                editAffiliateFloatBox = OW.ajaxFloatBox(
                    "OCSAFFILIATES_CMP_AffiliateEdit",
                    { affiliateId: ' . $affiliateId . ' } ,
                { width: 700, title: ' . json_encode($lang->text('ocsaffiliates', 'edit')) . ' }
            );
        });

        $("#btn-affiliate-unregister").click(function(){
            if ( confirm('.json_encode($lang->text('ocsaffiliates', 'unregister_confirm')).') )
            {
                $.ajax({
                    url: '.json_encode(OW::getRouter()->urlForRoute('ocsaffiliates.action_unregister')).',
                    type: "POST",
                    data: { affiliateId: '.json_encode($affiliateId).' },
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
        $this->setPageHeading($lang->text('ocsaffiliates', 'affiliate_area'));

        $service->updateAffiliateActivity($affiliateId);
    }

    public function banners()
    {
        $service = OCSAFFILIATES_BOL_Service::getInstance();
        $lang = OW::getLanguage();

        if ( !$service->checkAccess() )
        {
            $this->redirect(OW::getRouter()->urlForRoute('ocsaffiliates.home'));
        }

        $affiliateId = $service->getAffiliateId();

        if ( isset($_POST['action']) && $_POST['action'] == 'add_banner' )
        {
            if ( empty($_FILES['banner_file']) || !$service->validateBannerFileType($_FILES['banner_file']) )
            {
                OW::getFeedback()->error($lang->text('ocsaffiliates', 'banner_file_incorrect'));
            }
            $added = $service->addAffiliateBanner($affiliateId, $_FILES['banner_file']);

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

        $affiliate = $service->findAffiliateById($affiliateId);
        $this->assign('affiliate', $affiliate);

        $this->assign('homeUrl', OW_URL_HOME);
        $this->assign('param', OCSAFFILIATES_BOL_Service::AFFILIATE_GET_PARAM);

        $menu = $this->getMenu('banners');
        $this->addComponent('menu', $menu);

        $this->assign('bannerList', $service->getBannerListForAffiliate($affiliateId));
        $this->assign('defBannerList', $service->getDefaultBannerList());

        $service->updateAffiliateActivity($affiliateId);

        $canAdd = OW::getConfig()->getValue('ocsaffiliates', 'allow_banners');
        $this->assign('canAdd', $canAdd);

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
        $this->setPageHeading($lang->text('ocsaffiliates', 'affiliate_area'));
    }

    public function payouts()
    {
        $service = OCSAFFILIATES_BOL_Service::getInstance();

        if ( !$service->checkAccess() )
        {
            $this->redirect(OW::getRouter()->urlForRoute('ocsaffiliates.home'));
        }

        $affiliateId = $service->getAffiliateId();

        $menu = $this->getMenu('payouts');
        $this->addComponent('menu', $menu);

        $this->addComponent('payouts', new OCSAFFILIATES_CMP_AffiliatePayouts($affiliateId));

        $this->setPageHeading(OW::getLanguage()->text('ocsaffiliates', 'affiliate_area'));

        $service->updateAffiliateActivity($affiliateId);
    }
    
    public function verify( array $params )
    {
        $lang = OW::getLanguage();
        
        if ( empty($params['affId']) || empty($params['code']) )
        {
            $error = $lang->text('ocsaffiliates', 'code_invalid');
            $this->assign('error', $error);
            
            return;
        }
        
       $affiliateId = (int) $params['affId'];
       $code = (int) $params['code'];
       
       $service = OCSAFFILIATES_BOL_Service::getInstance();
       
       $affiliate = $service->findAffiliateById($affiliateId);
       if ( !$affiliate )
       {
           $error = $lang->text('ocsaffiliates', 'code_invalid');
           $this->assign('error', $error);
           
           return;
       }
       
       if ( $affiliate->emailVerified )
       {
           $error = $lang->text('ocsaffiliates', 'affiliateActive');
           $this->assign('error', $error);
           
           return;
       }
       
       $verified = $service->processVerificationCode($code);
       
       if ( $verified )
       {
           OW::getFeedback()->info($lang->text('ocsaffiliates', 'email_verification_successful'));
       }
       else
       {
           OW::getFeedback()->error($lang->text('ocsaffiliates', 'email_verification_failed'));
       }
       
       $this->redirect(OW::getRouter()->urlForRoute('ocsaffiliates.home'));
    }
    
    public function logout()
    {
        $service = OCSAFFILIATES_BOL_Service::getInstance();
        $homeUrl = OW::getRouter()->urlForRoute('ocsaffiliates.home');
        
        if ( !$service->isAuthenticated() )
        {
            $this->redirect($homeUrl);
        }
        
        $service->logoutAffiliate();
        $this->redirect($homeUrl);
    }
    
    private function getMenu( $active )
    {
        $lang = OW::getLanguage();
        $router = OW::getRouter();
        
        $menuItem = new BASE_MenuItem();
        $menuItem->setKey('dashboard');
        $menuItem->setActive($active == 'dashboard');
        $menuItem->setOrder(0);
        $menuItem->setUrl($router->urlForRoute('ocsaffiliates.home'));
        $menuItem->setLabel($lang->text('ocsaffiliates', 'dashboard'));
        $menuItem->setIconClass('ow_ic_dashboard');
        $menuItems[] = $menuItem;

        $menuItem = new BASE_MenuItem();
        $menuItem->setKey('log');
        $menuItem->setActive($active == 'log');
        $menuItem->setOrder(1);
        $menuItem->setUrl($router->urlForRoute('ocsaffiliates.home_log'));
        $menuItem->setLabel($lang->text('ocsaffiliates', 'log'));
        $menuItem->setIconClass('ow_ic_script');
        $menuItems[] = $menuItem;

        $menuItem = new BASE_MenuItem();
        $menuItem->setKey('banners');
        $menuItem->setActive($active == 'banners');
        $menuItem->setOrder(2);
        $menuItem->setUrl($router->urlForRoute('ocsaffiliates.home_banners'));
        $menuItem->setLabel($lang->text('ocsaffiliates', 'banners'));
        $menuItem->setIconClass('ow_ic_picture');
        $menuItems[] = $menuItem;
        
        $menuItem = new BASE_MenuItem();
        $menuItem->setKey('profile');
        $menuItem->setActive($active == 'profile');
        $menuItem->setOrder(3);
        $menuItem->setUrl($router->urlForRoute('ocsaffiliates.home_profile'));
        $menuItem->setLabel($lang->text('ocsaffiliates', 'profile'));
        $menuItem->setIconClass('ow_ic_user');
        $menuItems[] = $menuItem;
        
        $menuItem = new BASE_MenuItem();
        $menuItem->setKey('payouts');
        $menuItem->setActive($active == 'payouts');
        $menuItem->setOrder(4);
        $menuItem->setUrl($router->urlForRoute('ocsaffiliates.home_payouts'));
        $menuItem->setLabel($lang->text('ocsaffiliates', 'payouts'));
        $menuItem->setIconClass('ow_ic_');
        $menuItems[] = $menuItem;
        
        $menuItem = new BASE_MenuItem();
        $menuItem->setKey('logout');
        $menuItem->setActive($active == 'logout');
        $menuItem->setOrder(5);
        $menuItem->setUrl($router->urlForRoute('ocsaffiliates.logout'));
        $menuItem->setLabel($lang->text('ocsaffiliates', 'signout'));
        $menuItem->setIconClass('ow_ic_right_arrow');
        $menuItems[] = $menuItem;
        
        return new BASE_CMP_ContentMenu($menuItems);
    }
}