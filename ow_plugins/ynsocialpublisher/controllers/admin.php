<?php

/**
 * Social Publisher Admin
 *
 * @author trunglt
 * @package ow_plugins.ynsocialpublisher.controllers
 * @since 1.01
 */
class YNSOCIALPUBLISHER_CTRL_Admin extends ADMIN_CTRL_Abstract
{
    public function index()
    {
        $language = OW::getLanguage();

        $this->setPageHeading($language->text('ynsocialpublisher', 'admin_config'));
        $this->setPageHeadingIconClass('ow_ic_picture');

        $item = new BASE_MenuItem();
        $item->setLabel($language->text('ynsocialpublisher', 'admin_menu_general'));
        $item->setUrl(OW::getRouter()->urlForRoute('ynsocialpublisher.admin'));
        $item->setKey('general');
        $item->setIconClass('ow_ic_gear_wheel');
        $item->setOrder(0);
        $item->setActive(true);

        $menu = new BASE_CMP_ContentMenu(array($item));
        $this->addComponent('menu', $menu);

        $service = YNSOCIALPUBLISHER_BOL_Service::getInstance();
        $plugins = $service->getEnabledPlugins();
        $this->assign('plugins', $plugins);

        $form_url = OW::getRouter()->urlForRoute('ynsocialpublisher.admin');
        $this->assign('form_url', $form_url);

        if ( OW::getRequest()->isPost())
        {
            // get plugins data from post
            $params = $_POST['params'];
            foreach ($params as $key => $settings) {
                if (!isset($settings['providers']))
                {
                    $settings['providers'] = array();
                }
                OW::getConfig()->saveConfig('ynsocialpublisher', $key, json_encode($settings));
            }

            OW::getFeedback()->info($language->text('ynsocialpublisher', 'settings_updated'));
            $this->redirect($form_url);

        }
    }
}