<?php

/**
 * Copyright (c) 2013, Oxwall CandyStore
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * /init.php
 *
 * @author Oxwall CandyStore <plugins@oxcandystore.com>
 * @package ow.ow_plugins.ocs_affiliates
 * @since 1.5.3
 */

OW::getRouter()->addRoute(
	new OW_Route('ocsaffiliates.admin', 'admin/plugins/ocsaffiliates', 'OCSAFFILIATES_CTRL_Admin', 'index')
);

OW::getRouter()->addRoute(
	new OW_Route('ocsaffiliates.admin_settings', 'admin/plugins/ocsaffiliates/settings', 'OCSAFFILIATES_CTRL_Admin', 'settings')
);

OW::getRouter()->addRoute(
	new OW_Route('ocsaffiliates.admin_affiliate', 'admin/plugins/ocsaffiliates/:affId', 'OCSAFFILIATES_CTRL_Admin', 'affiliate')
);

OW::getRouter()->addRoute(
    new OW_Route('ocsaffiliates.admin_banners', 'admin/plugins/ocsaffiliates/banners', 'OCSAFFILIATES_CTRL_Admin', 'banners')
);

OW::getRouter()->addRoute(
	new OW_Route('ocsaffiliates.home', 'affiliate', 'OCSAFFILIATES_CTRL_Affiliate', 'index')
);

OW::getRouter()->addRoute(
	new OW_Route('ocsaffiliates.home_payouts', 'affiliate/payouts', 'OCSAFFILIATES_CTRL_Affiliate', 'payouts')
);

OW::getRouter()->addRoute(
	new OW_Route('ocsaffiliates.home_profile', 'affiliate/profile', 'OCSAFFILIATES_CTRL_Affiliate', 'profile')
);

OW::getRouter()->addRoute(
    new OW_Route('ocsaffiliates.home_log', 'affiliate/log', 'OCSAFFILIATES_CTRL_Affiliate', 'log')
);

OW::getRouter()->addRoute(
    new OW_Route('ocsaffiliates.home_banners', 'affiliate/banners', 'OCSAFFILIATES_CTRL_Affiliate', 'banners')
);

OW::getRouter()->addRoute(
	new OW_Route('ocsaffiliates.verify', 'affiliate/verify/:affId/:code', 'OCSAFFILIATES_CTRL_Affiliate', 'verify')
);

OW::getRouter()->addRoute(
	new OW_Route('ocsaffiliates.logout', 'affiliate/signout', 'OCSAFFILIATES_CTRL_Affiliate', 'logout')
);

OW::getRouter()->addRoute(
	new OW_Route('ocsaffiliates.action_signup', 'affiliate/form/signup', 'OCSAFFILIATES_CTRL_FormAction', 'signup')
);

OW::getRouter()->addRoute(
	new OW_Route('ocsaffiliates.action_signin', 'affiliate/form/signin', 'OCSAFFILIATES_CTRL_FormAction', 'signin')
);

OW::getRouter()->addRoute(
    new OW_Route('ocsaffiliates.action_resend', 'affiliate/form/resend', 'OCSAFFILIATES_CTRL_FormAction', 'resend')
);

OW::getRouter()->addRoute(
    new OW_Route('ocsaffiliates.action_reset_pass', 'affiliate/form/reset-password', 'OCSAFFILIATES_CTRL_FormAction', 'reset')
);

OW::getRouter()->addRoute(
    new OW_Route('ocsaffiliates.action_edit', 'affiliate/form/edit', 'OCSAFFILIATES_CTRL_FormAction', 'edit')
);

OW::getRouter()->addRoute(
    new OW_Route('ocsaffiliates.action_delete', 'affiliate/form/delete', 'OCSAFFILIATES_CTRL_FormAction', 'delete')
);

OW::getRouter()->addRoute(
    new OW_Route('ocsaffiliates.action_unregister', 'affiliate/form/unregister', 'OCSAFFILIATES_CTRL_FormAction', 'unregister')
);

OW::getRouter()->addRoute(
    new OW_Route('ocsaffiliates.action_register_payout', 'affiliate/form/register-payout', 'OCSAFFILIATES_CTRL_FormAction', 'registerPayout')
);

OW::getRouter()->addRoute(
    new OW_Route('ocsaffiliates.action_assign_user', 'affiliate/form/assign-user', 'OCSAFFILIATES_CTRL_FormAction', 'assignUser')
);

OW::getRouter()->addRoute(
    new OW_Route('ocsaffiliates.action_delete_payout', 'affiliate/form/delete-payout', 'OCSAFFILIATES_CTRL_FormAction', 'deletePayout')
);

OW::getRouter()->addRoute(
    new OW_Route('ocsaffiliates.action_delete_banner', 'affiliate/form/delete-banner', 'OCSAFFILIATES_CTRL_FormAction', 'deleteBanner')
);

OW::getRouter()->addRoute(
    new OW_Route('ocsaffiliates.action_login_as', 'affiliate/form/login-as', 'OCSAFFILIATES_CTRL_FormAction', 'loginAs')
);

OCSAFFILIATES_CLASS_EventHandler::getInstance()->init();