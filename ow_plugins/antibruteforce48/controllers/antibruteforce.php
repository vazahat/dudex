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
 * @package ow_plugins.antibruteforce.controllers
 * @since 1.0
 */
class ANTIBRUTEFORCE_CTRL_Antibruteforce extends OW_ActionController
{
    private $service;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->service = ANTIBRUTEFORCE_BOL_Service::getInstance();
    }
    
    public function index( $params = NULL )
    {
        if ( !$this->service->isLocked() )
        {
            $this->redirect( OW_URL_HOME );
        }
        
        OW::getDocument()->setJavaScripts( array('added' => array()) );
        
        $configs = OW::getConfig()->getValues( 'antibruteforce' );
        
        $this->setPageTitle( $configs['lock_title'] );
        $this->assign( 'configs', $configs );
        $masterPageFileDir = OW::getThemeManager()->getMasterPageTemplate('blank');
        OW::getDocument()->getMasterPage()->setTemplate($masterPageFileDir);
    }
}
