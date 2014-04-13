<?php

/**
 * Copyright (c) 2013, Oxwall CandyStore
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * Affiliates form actions controller
 *
 * @author Oxwall CandyStore <plugins@oxcandystore.com>
 * @package ow.ow_plugins.ocs_affiliates.controllers
 * @since 1.5.3
 */
class OCSAFFILIATES_CTRL_FormAction extends OW_ActionController
{
    public function signup()
    {
        if ( !OW::getRequest()->isAjax() )
        {
            exit(json_encode(array('result' => false)));
        }
        
        $lang = OW::getLanguage();
        
        $form = new OCSAFFILIATES_CLASS_SignupForm('signup');
        
        if ( !$form->isValid($_POST) )
        {
            exit(json_encode(array('result' => 'false', 'error' => $lang->text('ocsaffiliates', 'fill_required_fields'))));
        }
        
        $service = OCSAFFILIATES_BOL_Service::getInstance();
        $values = $form->getValues();
        
        $affiliate = $service->findAffiliateByEmail($values['email']);
        if ( $affiliate )
        {
            exit(json_encode(array('res' => false, 'error' => $lang->text('ocsaffiliates', 'email_exists'))));
        }
        
        $aff = new OCSAFFILIATES_BOL_Affiliate();
        $aff->email = trim($values['email']);
        $aff->name = trim($values['name']);
        $aff->password = BOL_UserService::getInstance()->hashPassword($values['password']);
        $aff->paymentDetails = trim($values['payment']);
        $aff->registerStamp = time();
        $aff->activityStamp = time();
        $aff->joinIp = ip2long($service->getRemoteAddr());
        $aff->emailVerified = 0;
        $aff->status = OW::getConfig()->getValue('ocsaffiliates', 'signup_status');

        // check association
        if ( OW::getUser()->isAuthenticated() )
        {
            $userId = OW::getUser()->getId();
            $assocAff = $service->findAffiliateByAssocUser($userId);
            if ( !$assocAff )
            {
                $aff->userId = $userId;
            }
        }
        else
        {
            $user = BOL_UserService::getInstance()->findByEmail($aff->email);
            if ( $user )
            {
                $assocAff = $service->findAffiliateByAssocUser($user->id);
                if ( !$assocAff )
                {
                    $aff->userId = $user->d;
                }
            }
        }
        
        $id = $service->registerAffiliate($aff);
        
        if ( $id )
        {
            $service->addVerificationRequest($aff->email);
            $service->loginAffiliateById($id);
            
            OW::getFeedback()->info($lang->text('ocsaffiliates', 'signup_successful'));
            exit(json_encode(array('result' => true)));
        }
    }
    
    public function signin()
    {
        if ( !OW::getRequest()->isAjax() )
        {
            exit(json_encode(array('result' => false)));
        }
        
        $lang = OW::getLanguage();
        $form = new OCSAFFILIATES_CLASS_SigninForm('signin');
        
        if ( !$form->isValid($_POST) )
        {
            exit(json_encode(array('result' => 'false', 'error' => $lang->text('ocsaffiliates', 'fill_required_fields'))));
        }
        
        $service = OCSAFFILIATES_BOL_Service::getInstance();
        $values = $form->getValues();
        
        $email = trim($values['email']);
        $password = BOL_UserService::getInstance()->hashPassword($values['password']);
        
        $affiliate = $service->findAffiliateByEmail($email);
        if ( !$affiliate )
        {
            exit(json_encode(array('res' => false, 'error' => $lang->text('ocsaffiliates', 'access_invalid'))));
        }
        
        if ( $affiliate->password == $password )
        {
            $service->loginAffiliateById($affiliate->id);
            
            OW::getFeedback()->info($lang->text('ocsaffiliates', 'login_successful'));
            exit(json_encode(array('result' => true)));
        }

        exit(json_encode(array('result' => false, 'error' => $lang->text('ocsaffiliates', 'access_invalid'))));
    }
    
    public function resend()
    {
        if ( !OW::getRequest()->isAjax() )
        {
            exit(json_encode(array('result' => false)));
        }

        $lang = OW::getLanguage();
        $service = OCSAFFILIATES_BOL_Service::getInstance();

        if ( !$service->isAuthenticated() )
        {
            exit(json_encode(array('result' => false)));
        }

        $affiliateId = $service->getAffiliateId();
        $affiliate = $service->findAffiliateById($affiliateId);

        $service->addVerificationRequest($affiliate->email);

        exit(json_encode(array('result' => true, 'message' => $lang->text('ocsaffiliates', 'verification_email_resent'))));
    }

    public function reset()
    {
        if ( !OW::getRequest()->isAjax() )
        {
            exit(json_encode(array('result' => false)));
        }

        if ( empty($_POST['email']) )
        {
            exit(json_encode(array('result' => false)));
        }
        $email = $_POST['email'];

        $service = OCSAFFILIATES_BOL_Service::getInstance();
        $lang = OW::getLanguage();

        $affiliate = $service->findAffiliateByEmail($email);

        if ( !$affiliate )
        {
            exit(json_encode(array('result' => false, 'error' => $lang->text('ocsaffiliates', 'affiliate_not_found'))));
        }

        $resetPassword = $service->getNewResetPassword($affiliate->id);

        $resetUrl = OW::getRouter()->urlForRoute('ocsaffiliates.reset_password', array('code' => $resetPassword->code));
        $vars = array('code' => $resetPassword->code, 'name' => $affiliate->name, 'resetUrl' => $resetUrl);

        $mail = OW::getMailer()->createMail();
        $mail->addRecipientEmail($email);
        $mail->setSubject($lang->text('ocsaffiliates', 'reset_password_mail_template_subject'));
        $mail->setTextContent($lang->text('ocsaffiliates', 'reset_password_mail_template_txt', $vars));
        $mail->setHtmlContent($lang->text('ocsaffiliates', 'reset_password_mail_template_html', $vars));
        OW::getMailer()->send($mail);

        exit(json_encode(array('result' => true, 'message' => $lang->text('ocsaffiliates', 'reset_password_success'))));
    }

    public function edit()
    {
        if ( !OW::getRequest()->isAjax() )
        {
            exit(json_encode(array('result' => false)));
        }

        $lang = OW::getLanguage();
        $mode = !empty($_POST['mode']) && in_array($_POST['mode'], array('owner', 'admin')) ? $_POST['mode'] : 'owner';

        $form = new OCSAFFILIATES_CLASS_EditForm('affiliate-edit', $mode);
        if ( !$form->isValid($_POST) )
        {
            exit(json_encode(array('result' => 'false', 'error' => $lang->text('ocsaffiliates', 'fill_required_fields'))));
        }

        $values = $form->getValues();
        $service = OCSAFFILIATES_BOL_Service::getInstance();
        $affiliateId = (int) $values['affiliateId'];

        switch ( $mode )
        {
            case 'owner':
                if ( !$service->isAuthenticated() || $affiliateId != $service->getAffiliateId() )
                {
                    exit(json_encode(array('result' => false)));
                }
                break;

            case 'admin':
                if ( !OW::getUser()->isAdmin() )
                {
                    exit(json_encode(array('result' => false)));
                }
                break;
        }

        $affiliate = $service->findAffiliateById($affiliateId);

        $updated = $affiliate;
        $updated->name = trim($values['name']);
        if ( !empty($values['password']) )
        {
            $updated->password = BOL_UserService::getInstance()->hashPassword($values['password']);
        }
        $updated->paymentDetails = trim($values['payment']);
        if ( $values['email'] != $affiliate->email )
        {
            $updated->email = $values['email'];
            $updated->emailVerified = 0;
            $service->addVerificationRequest($values['email']);
        }

        if ( $mode == 'admin' )
        {
            $updated->status = $values['status'];
            $updated->emailVerified = $values['emailVerified'];
        }

        $service->updateAffiliate($updated);
        OW::getFeedback()->info($lang->text('ocsaffiliates', 'affiliate_updated'));

        exit(json_encode(array('result' => true)));
    }

    public function registerPayout()
    {
        if ( !OW::getRequest()->isAjax() )
        {
            exit(json_encode(array('result' => false)));
        }

        $service = OCSAFFILIATES_BOL_Service::getInstance();
        $lang = OW::getLanguage();

        $form = new OCSAFFILIATES_CLASS_RegisterPayoutForm('register_payout');

        if ( !$form->isValid($_POST) )
        {
            exit(json_encode(array('result' => false, 'error' => $lang->text('ocsaffiliates', 'fill_required_fields'))));
        }

        $values = $form->getValues();
        $affiliateId = (int) $values['affiliateId'];
        $affiliate = $service->findAffiliateById($affiliateId);

        if ( !$affiliate )
        {
            exit(json_encode(array('result' => false)));
        }

        if ( !OW::getUser()->isAdmin() )
        {
            exit(json_encode(array('result' => false)));
        }

        $payout = new OCSAFFILIATES_BOL_Payout();
        $payout->affiliateId = $affiliateId;
        $payout->amount = abs(floatval($values['amount']));
        $payout->paymentDate = time();
        $payout->method = 'currency';

        if ( $values['byCredits'] )
        {
            $assoc = OCSAFFILIATES_BOL_Service::getInstance()->getAffiliateAssocUser($affiliateId);
            if ( OW::getPluginManager()->isPluginActive('usercredits') && $assoc )
            {
                $payout->method = 'credits';
                USERCREDITS_BOL_CreditsService::getInstance()->increaseBalance($assoc['id'], $payout->amount);
            }
        }

        $service->registerPayout($payout);
        OW::getFeedback()->info($lang->text('ocsaffiliates', 'payout_registered'));

        exit(json_encode(array('result' => true)));
    }

    public function assignUser()
    {
        if ( !OW::getRequest()->isAjax() )
        {
            exit(json_encode(array('result' => false)));
        }

        $service = OCSAFFILIATES_BOL_Service::getInstance();
        $lang = OW::getLanguage();

        $form = new OCSAFFILIATES_CLASS_AssignUserForm('assign_user');

        if ( !$form->isValid($_POST) )
        {
            exit(json_encode(array('result' => 'false', 'error' => $lang->text('ocsaffiliates', 'fill_required_fields'))));
        }

        $values = $form->getValues();
        $affiliateId = (int) $values['affiliateId'];
        $affiliate = $service->findAffiliateById($affiliateId);

        if ( !$affiliate )
        {
            exit(json_encode(array('result' => 'false')));
        }

        if ( !OW::getUser()->isAdmin() )
        {
            exit(json_encode(array('result' => 'false')));
        }

        $user = BOL_UserService::getInstance()->findByUsername($values['user']);
        if ( !$user )
        {
            exit(json_encode(array('result' => 'false', 'error' => $lang->text('ocsaffiliates', 'no_user_found', array('username' => $values['user'])))));
        }

        $userAffiliate = $service->findAffiliateByAssocUser($user->id);

        if ( $userAffiliate && $userAffiliate->id != $affiliateId )
        {
            exit(json_encode(array('result' => 'false', 'error' => $lang->text('ocsaffiliates', 'already_assigned', array('username' => $values['user'])))));
        }

        $affiliate->userId = $user->id;
        $service->updateAffiliate($affiliate);

        OW::getFeedback()->info($lang->text('ocsaffiliates', 'user_assigned'));
        exit(json_encode(array('result' => true)));
    }

    public function deletePayout()
    {
        if ( !OW::getRequest()->isAjax() )
        {
            exit(json_encode(array('result' => false)));
        }

        if ( !OW::getUser()->isAdmin() )
        {
            exit(json_encode(array('result' => false)));
        }

        $payoutId = (int) $_POST['payoutId'];
        $service = OCSAFFILIATES_BOL_Service::getInstance();
        $lang = OW::getLanguage();

        $service->deletePayoutById($payoutId);
        OW::getFeedback()->info($lang->text('ocsaffiliates', 'payout_deleted'));

        exit(json_encode(array('result' => true)));
    }

    public function loginAs()
    {
        if ( !OW::getRequest()->isAjax() )
        {
            exit(json_encode(array('result' => false)));
        }

        if ( !OW::getUser()->isAdmin() )
        {
            exit(json_encode(array('result' => false)));
        }

        $affiliateId = $_POST['affiliateId'];
        $service = OCSAFFILIATES_BOL_Service::getInstance();

        $affiliate = $service->findAffiliateById($affiliateId);
        if ( !$affiliate )
        {
            exit(json_encode(array('result' => false)));
        }

        $service->loginAffiliateById($affiliateId);

        exit(json_encode(array('result' => true, 'url' => OW::getRouter()->urlForRoute('ocsaffiliates.home'))));
    }

    public function deleteBanner()
    {
        if ( !OW::getRequest()->isAjax() )
        {
            exit(json_encode(array('result' => false)));
        }

        $bannerId = (int) $_POST['bannerId'];
        $service = OCSAFFILIATES_BOL_Service::getInstance();
        $lang = OW::getLanguage();

        $banner = $service->findBannerById($bannerId);

        if ( !$banner )
        {
            exit(json_encode(array('result' => false)));
        }

        $affiliateId = $service->getAffiliateId();
        if ( !OW::getUser()->isAdmin() && $banner->affiliateId != $affiliateId )
        {
            exit(json_encode(array('result' => false)));
        }

        $service->deleteBannerById($bannerId);
        OW::getFeedback()->info($lang->text('ocsaffiliates', 'banner_deleted'));

        exit(json_encode(array('result' => true)));
    }

    public function unregister()
    {
        if ( !OW::getRequest()->isAjax() )
        {
            exit(json_encode(array('result' => false)));
        }

        $service = OCSAFFILIATES_BOL_Service::getInstance();
        $affiliateId = $service->getAffiliateId();

        if ( !$affiliateId || !$affiliate = $service->findAffiliateById($affiliateId) )
        {
            exit(json_encode(array('result' => false)));
        }

        $service->logoutAffiliate();
        $service->deleteAffiliate($affiliateId);

        exit(json_encode(array('result' => true)));
    }

    public function delete()
    {
        if ( !OW::getRequest()->isAjax() )
        {
            exit(json_encode(array('result' => false)));
        }

        if ( !OW::getUser()->isAdmin() )
        {
            exit(json_encode(array('result' => false)));
        }

        if ( empty($_POST['affiliateId']) )
        {
            exit(json_encode(array('result' => false)));
        }

        $affiliateId = (int) $_POST['affiliateId'];

        $service = OCSAFFILIATES_BOL_Service::getInstance();

        if ( !$affiliate = $service->findAffiliateById($affiliateId) )
        {
            exit(json_encode(array('result' => false)));
        }

        $service->deleteAffiliate($affiliateId);

        OW::getFeedback()->info(OW::getLanguage()->text('ocsaffiliates', 'affiliate_removed'));

        exit(json_encode(array('result' => true)));
    }
}