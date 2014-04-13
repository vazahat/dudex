<?php

/**
 * Copyright (c) 2013, Kairat Bakytow
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 *
 * @author Kairat Bakytow
 * @package ow_plugins.smileys.components
 * @since 1.0
 */
class SMILEYS_CMP_Panel extends OW_Component
{
    public function __construct( $params = NULL )
    {
        parent::__construct();
        
        OW::getDocument()->addStyleSheet( OW::getPluginManager()->getPlugin('smileys')->getStaticCssUrl() . 'ui/'. OW::getConfig()->getValue('smileys', 'theme') . '/jquery-ui-1.10.3.custom.min.css' );
        $this->assign( 'sections', SMILEYS_BOL_Service::getInstance()->getSmilesCategories() );
    }
}
