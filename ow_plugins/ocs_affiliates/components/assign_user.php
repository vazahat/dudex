<?php

/**
 * Copyright (c) 2013, Oxwall CandyStore
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * Affiliate assign user component
 * 
 * @author Oxwall CandyStore <plugins@oxcandystore.com>
 * @package ow.ow_plugins.ocs_affiliates.components
 * @since 1.5.3
 */
class OCSAFFILIATES_CMP_AssignUser extends OW_Component
{
    public function __construct( $affiliateId )
    {
        parent::__construct();

        $form = new OCSAFFILIATES_CLASS_AssignUserForm('assign_user');
        $form->getElement('affiliateId')->setValue($affiliateId);

        $this->addForm($form);
    }
}