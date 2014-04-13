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
 * @package ow.ow_plugins.ocs_faq
 * @since 1.0
 */
OW::getRouter()->addRoute(
    new OW_Route('ocsfaq.admin_config', 'admin/plugin/ocsfaq', 'OCSFAQ_CTRL_Admin', 'index')
);

OW::getRouter()->addRoute(
    new OW_Route('ocsfaq.faq', 'faq/', 'OCSFAQ_CTRL_Faq', 'index')
);

OW::getRouter()->addRoute(
    new OW_Route('ocsfaq.admin_reorder', 'admin/plugin/ocsfaq/ajax-reorder', 'OCSFAQ_CTRL_Admin', 'ajaxReorder')
);

OW::getRouter()->addRoute(
    new OW_Route('ocsfaq.admin_cat_reorder', 'admin/plugin/ocsfaq/ajax-cat-reorder', 'OCSFAQ_CTRL_Admin', 'ajaxCatReorder')
);

OW::getRouter()->addRoute(
    new OW_Route('ocsfaq.admin_get_question', 'admin/plugin/ocsfaq/ajax-get-question', 'OCSFAQ_CTRL_Admin', 'ajaxGetQuestion')
);
