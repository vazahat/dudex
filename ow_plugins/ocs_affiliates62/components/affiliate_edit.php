<?php

/**
 * Copyright (c) 2013, Oxwall CandyStore
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * Affiliate edit component
 * 
 * @author Oxwall CandyStore <plugins@oxcandystore.com>
 * @package ow.ow_plugins.ocs_affiliates.components
 * @since 1.5.3
 */
class OCSAFFILIATES_CMP_AffiliateEdit extends OW_Component
{
    public function __construct( $affiliateId, $mode = 'owner' )
    {
        parent::__construct();
        $service = OCSAFFILIATES_BOL_Service::getInstance();

        $this->assign('mode', $mode);

        $form = new OCSAFFILIATES_CLASS_EditForm('affiliate-edit', $mode);
        $this->addForm($form);

        $affiliate = $service->findAffiliateById($affiliateId);

        $form->getElement('affiliateId')->setValue($affiliateId);
        $form->getElement('name')->setValue($affiliate->name);
        $form->getElement('email')->setValue($affiliate->email);
        $form->getElement('payment')->setValue($affiliate->paymentDetails);

        if ( $mode == 'admin' )
        {
            $form->getElement('emailVerified')->setValue($affiliate->emailVerified);
            $form->getElement('status')->setValue($affiliate->status);
        }
    }
}