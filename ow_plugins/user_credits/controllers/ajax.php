<?php

/**
 * Copyright (c) 2009, Skalfa LLC
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * User credits ajax controller.
 *
 * @author Egor Bulgakov <egor.bulgakov@gmail.com>
 * @package ow.ow_plugins.user_credits.controllers
 * @since 1.5.1
 */
class USERCREDITS_CTRL_Ajax extends OW_ActionController
{
    public function setCredits()
    {
        if ( !OW::getRequest()->isAjax() )
        {
            throw new Redirect404Exception();
        }

        if ( !OW::getUser()->isAuthorized('usercredits') )
        {
            throw new AuthenticateException();
        }
        
        $form = new USERCREDITS_CLASS_SetCreditsForm();

        if ( $form->isValid($_POST) )
        {
            $lang = OW::getLanguage();
            $creditService = USERCREDITS_BOL_CreditsService::getInstance();

            $values = $form->getValues();
            $userId = (int) $values['userId'];
            $balance = abs((int) $values['balance']);

            $creditService->setBalance($userId, $balance);

            OW::getFeedback()->info($lang->text('usercredits', 'credit_balance_updated'));
            exit(json_encode(array()));
        }
    }

    public function grantCredits()
    {
        if ( !OW::getRequest()->isAjax() )
        {
            throw new Redirect404Exception();
        }

        if ( !OW::getUser()->isAuthenticated() )
        {
            throw new AuthenticateException();
        }

        $form = new USERCREDITS_CLASS_GrantCreditsForm();

        if ( $form->isValid($_POST) )
        {
            $lang = OW::getLanguage();
            $creditService = USERCREDITS_BOL_CreditsService::getInstance();

            $grantorId = OW::getUser()->getId();
            $values = $form->getValues();
            $userId = (int) $values['userId'];
            $amount = abs((int) $values['amount']);

            $granted = $creditService->grantCredits($grantorId, $userId, $amount);
            $credits = $creditService->getCreditsBalance($grantorId);

            if ( $granted )
            {
                $data = array('amount' => $amount, 'grantorId' => $grantorId, 'userId' => $userId);
                $event = new OW_Event('usercredits.grant', $data);
                OW::getEventManager()->trigger($event);

                $data = array(
                    'message' => $lang->text('usercredits', 'credits_granted', array('amount' => $amount)),
                    'credits' => $credits
                );

            }
            else
            {
                $data = array('error' => $lang->text('usercredits', 'credits_grant_error'));
            }

            exit(json_encode($data));
        }
    }
}