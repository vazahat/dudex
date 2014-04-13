<?php

/**
 * This software is intended for use with Oxwall Free Community Software http://www.oxwall.org/ and is a proprietary licensed product. 
 * For more information see License.txt in the plugin folder.

 * ---
 * Copyright (c) 2012, Purusothaman Ramanujam
 * All rights reserved.

 * Redistribution and use in source and binary forms, with or without modification, are not permitted provided.

 * This plugin should be bought from the developer by paying money to PayPal account (purushoth.r@gmail.com).

 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED
 * AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */
class SITETOUR_CTRL_Admin extends ADMIN_CTRL_Abstract {

    public function __construct() {
        parent::__construct();

        if (OW::getRequest()->isAjax()) {
            return;
        }

        $language = OW::getLanguage();

        $menu = new BASE_CMP_ContentMenu();

        $menuItem = new BASE_MenuItem();
        $menuItem->setKey('steps');
        $menuItem->setLabel($language->text('sitetour', 'admin_steps'));
        $menuItem->setUrl(OW::getRouter()->urlForRoute('sitetour_admin'));
        $menuItem->setIconClass('ow_ic_gear_wheel');
        $menuItem->setOrder(1);
        $menu->addElement($menuItem);

        $menuItem = new BASE_MenuItem();
        $menuItem->setKey('settings');
        $menuItem->setLabel($language->text('sitetour', 'admin_settings'));
        $menuItem->setUrl(OW::getRouter()->urlForRoute('sitetour_settings'));
        $menuItem->setIconClass('ow_ic_gear_wheel');
        $menuItem->setOrder(2);
        $menu->addElement($menuItem);

        $menuItem = new BASE_MenuItem();
        $menuItem->setKey('console');
        $menuItem->setLabel($language->text('sitetour', 'admin_console'));
        $menuItem->setUrl(OW::getRouter()->urlForRoute('sitetour_console'));
        $menuItem->setIconClass('ow_ic_gear_wheel');
        $menuItem->setOrder(3);
        $menu->addElement($menuItem);

        $this->addComponent('menu', $menu);
        $this->menu = $menu;
    }

    public function settings() {
        $language = OW::getLanguage();
        $config = OW::getConfig();

        $adminForm = new Form('adminForm');

        $element = new TextField('introWidth');
        $element->setValue($config->getValue('sitetour', 'introWidth'));
        $element->setLabel($language->text('sitetour', 'intro_width'));
        $element->setRequired();
        $element->addValidator(new IntValidator(1));
        $adminForm->addElement($element);
        
        $element = new TextField('guidePos');
        $element->setValue($config->getValue('sitetour', 'guidePos'));
        $element->setLabel($language->text('sitetour', 'guide_pos'));
        $element->setRequired();
        $element->addValidator(new IntValidator(1));
        $adminForm->addElement($element);        

        $element = new TextField('guideColor');
        $element->setValue($config->getValue('sitetour', 'guideColor'));
        $element->setLabel($language->text('sitetour', 'guide_color'));
        $element->setId('colorBox');
        $adminForm->addElement($element);

        $element = new CheckboxField('enableForGuests');
        $element->setValue($config->getValue('sitetour', 'enableForGuests'));
        $element->setLabel($language->text('sitetour', 'enable_for_guests'));
        $adminForm->addElement($element);

        $element = new CheckboxField('enableRTL');
        $element->setValue($config->getValue('sitetour', 'enableRTL'));
        $element->setLabel($language->text('sitetour', 'enable_rtl'));
        $adminForm->addElement($element);

        $element = new Selectbox('exitOnEsc');
        $element->addOptions(array('true' => $language->text('sitetour', 'enabled'), 'false' => $language->text('sitetour', 'disabled')));
        $element->setValue($config->getValue('sitetour', 'exitOnEsc'));
        $element->setLabel($language->text('sitetour', 'exit_esc'));
        $element->setRequired();
        $adminForm->addElement($element);

        $element = new Selectbox('exitOnOverlayClick');
        $element->addOptions(array('true' => $language->text('sitetour', 'enabled'), 'false' => $language->text('sitetour', 'disabled')));
        $element->setValue($config->getValue('sitetour', 'exitOnOverlayClick'));
        $element->setLabel($language->text('sitetour', 'exit_overlay_click'));
        $element->setRequired();
        $adminForm->addElement($element);

        $element = new Selectbox('showStepNumbers');
        $element->addOptions(array('true' => $language->text('sitetour', 'enabled'), 'false' => $language->text('sitetour', 'disabled')));
        $element->setValue($config->getValue('sitetour', 'showStepNumbers'));
        $element->setLabel($language->text('sitetour', 'show_step_numbers'));
        $element->setRequired();
        $adminForm->addElement($element);

        $element = new Selectbox('keyboardNavigation');
        $element->addOptions(array('true' => $language->text('sitetour', 'enabled'), 'false' => $language->text('sitetour', 'disabled')));
        $element->setValue($config->getValue('sitetour', 'keyboardNavigation'));
        $element->setLabel($language->text('sitetour', 'keyboard_navigation'));
        $element->setRequired();
        $adminForm->addElement($element);

        $element = new Selectbox('showButtons');
        $element->addOptions(array('true' => $language->text('sitetour', 'enabled'), 'false' => $language->text('sitetour', 'disabled')));
        $element->setValue($config->getValue('sitetour', 'showButtons'));
        $element->setLabel($language->text('sitetour', 'show_buttons'));
        $element->setRequired();
        $adminForm->addElement($element);

        $element = new Selectbox('showBullets');
        $element->addOptions(array('true' => $language->text('sitetour', 'enabled'), 'false' => $language->text('sitetour', 'disabled')));
        $element->setValue($config->getValue('sitetour', 'showBullets'));
        $element->setLabel($language->text('sitetour', 'show_bullets'));
        $element->setRequired();
        $adminForm->addElement($element);

        $element = new Submit('saveSettings');
        $element->setValue($language->text('sitetour', 'admin_save_settings'));
        $adminForm->addElement($element);

        if (OW::getRequest()->isPost()) {
            if ($adminForm->isValid($_POST)) {
                $values = $adminForm->getValues();

                $config->saveConfig('sitetour', 'enableForGuests', $values['enableForGuests']);
                $config->saveConfig('sitetour', 'enableRTL', $values['enableRTL']);
                $config->saveConfig('sitetour', 'exitOnEsc', $values['exitOnEsc']);
                $config->saveConfig('sitetour', 'exitOnOverlayClick', $values['exitOnOverlayClick']);
                $config->saveConfig('sitetour', 'showStepNumbers', $values['showStepNumbers']);
                $config->saveConfig('sitetour', 'keyboardNavigation', $values['keyboardNavigation']);
                $config->saveConfig('sitetour', 'showButtons', $values['showButtons']);
                $config->saveConfig('sitetour', 'showBullets', $values['showBullets']);
                $config->saveConfig('sitetour', 'introWidth', $values['introWidth']);
                $config->saveConfig('sitetour', 'guideColor', $values['guideColor']);
      $config->saveConfig('sitetour', 'guidePos', $values['guidePos']);
      
                OW::getFeedback()->info($language->text('sitetour', 'user_save_success'));
            }
        }

        $this->addForm($adminForm);

                OW::getDocument()->addStyleSheet(OW::getPluginManager()->getPlugin('sitetour')->getStaticCssUrl() . 'spectrum.css');
        OW::getDocument()->addScript(OW::getPluginManager()->getPlugin('sitetour')->getStaticJsUrl() . 'spectrum.js');
        
        $this->setPageHeading(OW::getLanguage()->text('sitetour', 'admin_settings_title'));
        $this->setPageTitle(OW::getLanguage()->text('sitetour', 'admin_settings_title'));
        $this->setPageHeadingIconClass('ow_ic_gear_wheel');
    }

    public function index() {
        $language = OW::getLanguage();

        $steps = SITETOUR_BOL_StepDao::getInstance()->getAllSteps('all', 0);
        $allSteps = array();

        foreach ($steps as $step) {
            $id = $step->id;
            $allSteps[$id]['id'] = $id;
            $allSteps[$id]['page'] = $step->page;
            $allSteps[$id]['pageText'] = $language->text('sitetour', $step->page);
            $allSteps[$id]['text'] = $language->text('sitetour', $step->key);
            $allSteps[$id]['position'] = ucfirst($step->position);
            $allSteps[$id]['active'] = (int) $step->active;
        }

        $this->assign('allSteps', $allSteps);
        $this->assign('categories', SITETOUR_BOL_StepDao::getInstance()->getCategories());
        $this->assign('updateUrl1',OW::getRouter()->urlForRoute('sitetour_save_updates'));
        $this->assign('updateUrl2',OW::getRouter()->urlForRoute('sitetour_save_positions'));
        
        OW::getDocument()->addStyleSheet(OW::getPluginManager()->getPlugin('sitetour')->getStaticCssUrl() . 'tip-yellowsimple.css');
        OW::getDocument()->addStyleSheet(OW::getPluginManager()->getPlugin('sitetour')->getStaticCssUrl() . 'jquery-editable.css');

        OW::getDocument()->addScript(OW::getPluginManager()->getPlugin('sitetour')->getStaticJsUrl() . 'jquery.poshytip.js');
        OW::getDocument()->addScript(OW::getPluginManager()->getPlugin('sitetour')->getStaticJsUrl() . 'jquery-editable-poshytip.min.js');
        OW::getDocument()->addScript(OW::getPluginManager()->getPlugin('sitetour')->getStaticJsUrl() . 'jquery.tablednd.js');

        $this->setPageHeading(OW::getLanguage()->text('sitetour', 'admin_steps_title'));
        $this->setPageTitle(OW::getLanguage()->text('sitetour', 'admin_steps_title'));
        $this->setPageHeadingIconClass('ow_ic_gear_wheel');
    }

    public function console() {
        $language = OW::getLanguage();
        $config = OW::getConfig();

        $adminForm = new Form('adminForm');

        $element = new Textarea('sqlQuery');
        $element->setLabel($language->text('sitetour', 'sql_query'));
        $adminForm->addElement($element);

        $element = new Submit('saveSettings');
        $element->setValue($language->text('sitetour', 'admin_execute_sql'));
        $adminForm->addElement($element);

        if (OW::getRequest()->isPost()) {
            if ($adminForm->isValid($_POST)) {
                $values = $adminForm->getValues();

                try {
                    OW::getDbo()->insert($values['sqlQuery']);
                    $rowsCount = OW::getDbo()->getAffectedRows();

                    OW::getFeedback()->info($language->text('sitetour', 'user_sql_success', array('count' => $rowsCount)));
                } catch (Exception $e) {
                    OW::getFeedback()->error($language->text('sitetour', 'user_sql_error'));
                }
            }
        }

        $this->addForm($adminForm);
        $this->assign('dbPrefix', OW_DB_PREFIX);

        $this->setPageHeading(OW::getLanguage()->text('sitetour', 'admin_console_title'));
        $this->setPageTitle(OW::getLanguage()->text('sitetour', 'admin_console_title'));
        $this->setPageHeadingIconClass('ow_ic_gear_wheel');
    }

    public function save1() {
        $name = $_POST['name'];
        $id = $_POST['pk'];
        $value = isset($_POST['value']) ? $_POST['value'] : '';

        switch ($name) {
            case 'position':
                $query = "UPDATE " . SITETOUR_BOL_StepDao::getInstance()->getTableName() . " SET position = :position WHERE id=:id ;";

                OW::getDbo()->query($query, array('position' => $value, 'id' => $id));

                return "HTTP status 200 OK";
                break;
            case 'status':
                $query = "UPDATE " . SITETOUR_BOL_StepDao::getInstance()->getTableName() . " SET active = :status WHERE id=:id ;";

                OW::getDbo()->query($query, array('status' => (int) $value, 'id' => $id));
                return "HTTP status 200 OK";
                break;
            case 'page':
                $query = "UPDATE " . SITETOUR_BOL_StepDao::getInstance()->getTableName() . " SET page = :page WHERE id=:id ;";

                OW::getDbo()->query($query, array('page' => $value, 'id' => $id));
                return "HTTP status 200 OK";
                break;
        }

        header('HTTP 400 Bad Request', true, 400);
        echo "Updated Failed";
    }

    public function save2() {
        $tables = $_POST;

        foreach ($tables as $table => $orders) {
            $i = 0;

            foreach ($orders as $order) {
                $i++;

                $query = "UPDATE " . SITETOUR_BOL_StepDao::getInstance()->getTableName() . " SET `order` = :id WHERE id=:order ;";
                OW::getDbo()->query($query, array('order' => $order, 'id' => $i));
            }
        }
    }

}
