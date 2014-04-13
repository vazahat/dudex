<?php

/**
 * Copyright (c) 2009, Skalfa LLC
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * User credits administration action controller
 *
 * @author Egor Bulgakov <egor.bulgakov@gmail.com>
 * @package ow.plugin.user_credits.controllers
 * @since 1.0
 */
class USERCREDITS_CTRL_Admin extends ADMIN_CTRL_Abstract
{
    private function getMenu()
    {
        $language = OW::getLanguage();

        $item = new BASE_MenuItem();
        $item->setLabel($language->text('usercredits', 'tab_actions'));
        $item->setUrl(OW::getRouter()->urlForRoute('usercredits.admin'));
        $item->setKey('actions');
        $item->setOrder(1);
        $item->setIconClass('ow_ic_info');

        $item2 = new BASE_MenuItem();
        $item2->setLabel($language->text('usercredits', 'packs'));
        $item2->setUrl(OW::getRouter()->urlForRoute('usercredits.admin_packs'));
        $item2->setKey('packs');
        $item2->setOrder(2);
        $item2->setIconClass('ow_ic_folder');
        
        return new BASE_CMP_ContentMenu(array($item, $item2));
    } 
    
    /**
     * Default action
     */
    public function index()
    {
        $menu = $this->getMenu();
        $this->addComponent('menu', $menu);
        $lang = OW::getLanguage();
        
        $creditService = USERCREDITS_BOL_CreditsService::getInstance();
        
        $losing = $creditService->findCreditsActions('lose');
        $this->assign('losing', $losing);
        
        $earning = $creditService->findCreditsActions('earn');
        $this->assign('earning', $earning);
        
        $unset = $creditService->findCreditsActions('unset');
        $this->assign('unset', $unset);
        
        $this->setPageHeading($lang->text('usercredits', 'admin_config'));
        $this->setPageHeadingIconClass('ow_ic_gear_wheel');
        
        $script = '$("a.ow_action_price").click(function(){
            var actionId = $(this).attr("rel");
            var $input = $("#price_input_" + actionId);
            $(this).hide();
            $input.show();
            $input.focus();
            
            $input.bind("blur", function (){
                $(this).data("owTipHide", true);
            });
            
            OW.showTip($input, {side: "right", width:200, timeout:7000, hideEvent: "blur",  show: "'.$lang->text('usercredits', 'setup_price_tip').'"});
        });';
        
        $script .= 'var func = function(){
            var $input = $(this);
            var $link = $input.parent().find("a.ow_action_price");
            var actionId = $link.attr("rel");

            OW.hideTip($input);
            $input.hide();
            
            if ( $link.html() == $input.val() )
            {
                $link.show();
                return;
            }
            
            $link.html($input.val());
            $link.show();
            
            OW.inProgressNode($input.parent());
            
            $.ajax({
                type: "POST",
                url: ' . json_encode(OW::getRouter()->urlFor('USERCREDITS_CTRL_Admin', 'ajaxUpdateAmount')) . ',
                data: "actionId=" + actionId + "&price=" + $input.val(),
                dataType: "json",
                success : function(data){
                    if ( data.reload != undefined && data.reload ){
                        document.location.reload();
                    }
                    else { OW.activateNode($input.parent()); }
                }
            });
        };
        
        $("input.price_input").keyup(function(e) { 
            if ( e.which == 13 )
            {
                func.apply(this);
            } 
        });
        
        $("input.price_input").blur(function(){
            func.apply(this);
        });';
        
        OW::getDocument()->addOnloadScript($script);
        
        $this->assign('imagesUrl', OW::getThemeManager()->getCurrentTheme()->getStaticImagesUrl());
    }
    
    public function ajaxUpdateAmount( )
    {
        if ( !empty($_POST['actionId']) )
        {
            $creditService = USERCREDITS_BOL_CreditsService::getInstance();
            
            $action = $creditService->findActionById((int) $_POST['actionId']);
            
            if ( $action )
            {
                $oldAmount = $action->amount;
                $action->amount = (int) $_POST['price'];
                
                if ( $oldAmount == $action->amount )
                {
                    exit;
                }
                
                $creditService->updateCreditsAction($action);
                $params = array(
                    'pluginKey' => $action->pluginKey,
                    'actionKey' => $action->actionKey,
                    'amount' => $action->amount
                );
                $event = new OW_Event('usercredits.action_update_amount', $params);
                OW::getEventManager()->trigger($event);
                
                $result['reload'] = false;
                
                if ( $oldAmount * $action->amount <= 0 )
                {
                    $result['reload'] = true;
                }
                
                exit(json_encode($result));
            }
        }
    }
    
    public function packs()
    {
        $menu = $this->getMenu();
        $this->addComponent('menu', $menu);

        $creditService = USERCREDITS_BOL_CreditsService::getInstance();
        $lang = OW::getLanguage();
        
        if ( !empty($_GET['delPack']) )
        {
            if ( $creditService->deletePackById((int)$_GET['delPack']) )
            {
                OW::getFeedback()->info($lang->text('usercredits', 'pack_deleted'));
            }
            
            $this->redirectToAction('packs');
        }
        
        $form = new AddPackForm();
        $this->addForm($form);
        
        if ( OW::getRequest()->isPost() )
        {
            if ( $_POST['form_name'] == 'add-pack-form' && $form->isValid($_POST) )
            {
                $values = $form->getValues();
                
                $pack = new USERCREDITS_BOL_Pack();
                $pack->credits = (int) $values['credits'];
                $pack->price = floatval($values['price']);
                
                if ( $creditService->addPack($pack) )
                {
                    OW::getFeedback()->info($lang->text('usercredits', 'pack_added'));
                }
                
                $this->redirect();
            }
            else if ( $_POST['form_name'] == 'update-packs-form' )
            {
                if ( !empty($_POST['credits']) && !empty($_POST['price']) )
                {
                    foreach ( $_POST['credits'] as $packId => $credits )
                    {
                        if ( !$pack = $creditService->findPackById($packId) )
                        {
                            continue;
                        }

                        $pack->credits = (int) $credits;
                        $pack->price = floatval($_POST['price'][$packId]);
                        $creditService->addPack($pack);
                    }
                    
                    OW::getFeedback()->info($lang->text('usercredits', 'packs_updated'));
                }
                
                $this->redirect();
            }
        }
        
        $this->setPageHeading(OW::getLanguage()->text('usercredits', 'admin_config'));
        $this->setPageHeadingIconClass('ow_ic_gear_wheel');
        
        $packs = $creditService->getPackList();
        $this->assign('packs', $packs);
        
        $this->assign('currency', BOL_BillingService::getInstance()->getActiveCurrency());
    }
}


class AddPackForm extends Form
{
    public function __construct()
    {
        parent::__construct('add-pack-form');
        
        $lang = OW::getLanguage();
        
        $credits = new TextField('credits');
        $credits->setRequired(true);
        $credits->setLabel($lang->text('usercredits', 'credits'));
        $this->addElement($credits);
        
        $price = new TextField('price');
        $price->setRequired(true);
        $price->setLabel($lang->text('usercredits', 'price'));
        $this->addElement($price);
        
        $submit = new Submit('add');
        $submit->setValue($lang->text('usercredits', 'add'));
        $this->addElement($submit);
    }
}
