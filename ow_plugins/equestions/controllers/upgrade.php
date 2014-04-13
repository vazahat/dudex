<?php

/**
 * Copyright (c) 2012, Sergey Kambalin
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * @author Sergey Kambalin <greyexpert@gmail.com>
 * @package equestions.controllers
 */
class EQUESTIONS_CTRL_Upgrade extends ADMIN_CTRL_Abstract
{
    public function index()
    {
        if ( EQUESTIONS_Plugin::getInstance()->isReady() )
        {
            $this->redirect(OW::getRouter()->urlForRoute('equestions-admin-main'));
        }

        OW::getDocument()->setHeading(OW::getLanguage()->text('equestions', 'admin_upgrade_page_heading'));
        OW::getDocument()->setHeadingIconClass('ow_ic_lens');

        $form = new EQUESTIONS_SetupForm();

        if ( OW::getRequest()->isPost() )
        {
            $result = $form->process($_POST);

            if ( $result )
            {
                OW::getFeedback()->info(OW::getLanguage()->text('equestions', 'admin_upgrade_complete'));

                $this->redirect(OW::getRouter()->urlForRoute('equestions-admin-main'));
            }
        }

        $this->addForm($form);
    }
}

class EQUESTIONS_SetupForm extends Form
{
    public function __construct()
    {
        parent::__construct('EQUESTIONS_SetupForm');

        $language = OW::getLanguage();

        $field = new CheckboxField('copyData');
        $field->setValue(true);
        $field->setLabel($language->text('equestions', 'admin_setup_copy_data_label'));
        $this->addElement($field);

        $button = new Submit('upgrade');
        $button->setValue($language->text('equestions', 'admin_setup_upgrade_label'));
        $this->addElement($button);
    }

    public function process( $data )
    {
        try
        {
            if ( !empty($data['copyData']) )
            {
                $this->copyData();
                $this->copyFeed();
            }

            $this->copyConfig();

            $this->uninstallOld();
        }
        catch( Exception $e )
        {
            return false;
        }

        return true;
    }

    private function copyData()
    {
        $tables = array(
            array( QUESTIONS_BOL_QuestionDao::getInstance()->getTableName(), EQUESTIONS_BOL_QuestionDao::getInstance()->getTableName() , array(
                'id', 'userId', 'text', 'settings', 'timeStamp'
            )),
            array( QUESTIONS_BOL_OptionDao::getInstance()->getTableName(), EQUESTIONS_BOL_OptionDao::getInstance()->getTableName() , array(
                'id', 'userId', 'questionId', 'text', 'timeStamp'
            )),
            array( QUESTIONS_BOL_AnswerDao::getInstance()->getTableName(), EQUESTIONS_BOL_AnswerDao::getInstance()->getTableName() , array(
                'id', 'userId', 'optionId', 'timeStamp'
            )),
            array( QUESTIONS_BOL_FollowDao::getInstance()->getTableName(), EQUESTIONS_BOL_FollowDao::getInstance()->getTableName() , array(
                'id', 'userId', 'questionId', 'timeStamp'
            )),
            array( QUESTIONS_BOL_ActivityDao::getInstance()->getTableName(), EQUESTIONS_BOL_ActivityDao::getInstance()->getTableName() , array(
                'id', 'questionId', 'activityType', 'activityId', 'userId', 'timeStamp', 'privacy', 'data'
            ))
        );

        foreach ( $tables as $t )
        {
            OW::getDbo()->query('REPLACE INTO ' . $t[1] . ' (`' . implode('` ,`', $t[2]) . '`) SELECT `' . implode('` ,`', $t[2]) . '` FROM ' . $t[0]);
        }
    }

    private function copyFeed()
    {
        if ( OW::getPluginManager()->isPluginActive('newsfeed') )
        {
            $tbl = NEWSFEED_BOL_ActionDao::getInstance()->getTableName();

            $query = 'UPDATE ' . $tbl . ' SET pluginKey="equestions" WHERE pluginKey="questions"';
            OW::getDbo()->query($query);
        }
    }

    private function copyConfig()
    {
        $systemConf = array('plugin_installed');

        $configs = OW::getConfig()->getValues('questions');

        foreach ( $configs as $name => $value )
        {
            if ( !in_array($name, $systemConf) && OW::getConfig()->configExists('equestions', $name) )
            {
                OW::getConfig()->saveConfig('equestions', $name, $value);
            }
        }
    }

    private function uninstallOld()
    {
        BOL_PluginService::getInstance()->uninstall('questions');
    }

    private function installNew()
    {
        EQUESTIONS_Plugin::getInstance()->completeInstall();
        EQUESTIONS_Plugin::getInstance()->fullActivate();
    }
}
