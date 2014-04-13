<?php

/**
 * Copyright (c) 2014, Kairat Bakytow
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/** 
 * 
 *
 * @author Kairat Bakytow <kainisoft@gmail.com>
 * @package ow_plugins.profileprogressbar.classes
 * @since 1.0
 */
class PROFILEPROGRESSBAR_CLASS_SettingsForm extends Form
{
    public function __construct()
    {
        parent::__construct('settingsForm');
        
        $themes = new Selectbox('themeList');
        $themes->setRequired();
        $themes->setLabel(OW::getLanguage()->text('profileprogressbar', 'theme_label'));
        
        $plugin = OW::getPluginManager()->getPlugin('profileprogressbar');
        $dirIterator = new RecursiveDirectoryIterator($plugin->getStaticDir() . 'css' . DS);
        $interator = new RecursiveIteratorIterator($dirIterator);
        
        $themesList = array();
        
        foreach ( $interator as $file ) 
        {
            if ( $file->getFilename() == '.' )
            {
                continue;
            }

            if ( !$file->isDir() && pathinfo($file->getPathname(), PATHINFO_EXTENSION ) == 'css')
            {
                $themeName = substr($file->getFilename(), 0, strrpos($file->getFilename(), '.'));
                
                if ( file_exists($plugin->getStaticDir() . 'img' . DS . $themeName . DS . 'background.png') &&
                    file_exists($plugin->getStaticDir() . 'img' . DS . $themeName . DS . 'complete.png') )
                {
                    $themesList[$themeName] = ucfirst($themeName);
                }
            }
        }
        
        asort($themesList);
        $themes->setOptions($themesList);
        $themes->setValue(OW::getConfig()->getValue('profileprogressbar', 'theme'));
        $this->addElement($themes);
        
        $validator = new SelectboxValidator($themesList);
        $themes->addValidator($validator);
        
        $submit = new Submit('save');
        $submit->setValue(OW::getLanguage()->text('profileprogressbar', 'save_settings'));
        $this->addElement($submit);
    }
}

class SelectboxValidator extends OW_Validator
{
    private $options;
    
    public function __construct( array $options )
    {
        $this->options = $options;
    }

    public function isValid( $value )
    {
        return array_key_exists($value, $this->options);
    }
}
