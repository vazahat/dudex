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
class EQUESTIONS_CTRL_Admin extends ADMIN_CTRL_Abstract
{
    public function main()
    {
        $language = OW::getLanguage();

        $this->setPageHeading($language->text('equestions', 'admin_main_page_heading'));
        $this->setPageTitle($language->text('equestions', 'admin_main_page_title'));
        $this->setPageHeadingIconClass('ow_ic_lens');

        $configs = OW::getConfig()->getValues('equestions');
        $this->assign('configs', $configs);

        $form = new EQUESTIONS_ConfigSaveForm($configs);

        $this->addForm($form);

        if ( OW::getRequest()->isPost() && $form->isValid($_POST) )
        {
            if ( $form->process($_POST) )
            {
                OW::getFeedback()->info($language->text('equestions', 'admin_settings_updated'));
                $this->redirect(OW::getRouter()->urlForRoute('equestions-admin-main'));
            }
        }
    }
}

class EQUESTIONS_ConfigSaveForm extends Form
{
    private $configs = array();

    public function __construct( $configs )
    {
        parent::__construct('EQUESTIONS_ConfigSaveForm');

        $this->configs = $configs;

        $language = OW::getLanguage();

        $field = new CheckboxField('allow_comments');
        $field->setLabel($language->text('equestions', 'admin_allow_comments_label'));
        $field->setValue($configs['allow_comments']);
        $this->addElement($field);

        $field = new CheckboxField('ask_friends');
        $field->setLabel($language->text('equestions', 'admin_enable_ask_friends_label'));
        $field->setValue($configs['ask_friends']);
        $this->addElement($field);

        $field = new Selectbox('list_order');
        foreach ( array(EQUESTIONS_CMP_Feed::ORDER_LATEST, EQUESTIONS_CMP_Feed::ORDER_POPULAR) as $v )
        {
            $field->addOption($v, $language->text('equestions', 'feed_order_' . $v));
        }
        $field->setHasInvitation(false);
        $field->setLabel($language->text('equestions', 'admin_list_order_label'));
        $field->setValue($configs['list_order']);
        $this->addElement($field);

        $field = new CheckboxField('enable_follow');
        $field->setLabel($language->text('equestions', 'admin_enable_follow_label'));
        $field->setValue($configs['enable_follow']);
        $this->addElement($field);

        $field = new CheckboxField('allow_popups');
        $field->setLabel($language->text('equestions', 'admin_allow_popups_label'));
        $field->setValue($configs['allow_popups']);
        $this->addElement($field);

        $field = new CheckboxField('attachments');
        $field->setLabel($language->text('equestions', 'admin_enable_attachments_label'));
        $field->setValue($configs['attachments']);
        $this->addElement($field);

        $field = new CheckboxField('attachments_video');
        $field->setLabel($language->text('equestions', 'admin_attachments_video_enable_label'));
        $field->setValue($configs['attachments_video']);
        $this->addElement($field);

        $field = new CheckboxField('attachments_image');
        $field->setLabel($language->text('equestions', 'admin_attachments_image_enable_label'));
        $field->setValue($configs['attachments_image']);
        $this->addElement($field);

        $field = new CheckboxField('attachments_link');
        $field->setLabel($language->text('equestions', 'admin_attachments_link_enable_label'));
        $field->setValue($configs['attachments_link']);
        $this->addElement($field);

        // submit
        $submit = new Submit('save');
        $submit->setValue($language->text('equestions', 'admin_save_btn'));
        $this->addElement($submit);
    }

    public function process( $data )
    {
        $config = OW::getConfig();

        foreach ( $this->configs as $k => $v )
        {
            $element = $this->getElement($k);

            if ( $element !== null )
            {
                $v = $element->getValue();
                $config->saveConfig('equestions', $k, $v === null ? 0 : $v);
            }
        }

        return true;
    }
}