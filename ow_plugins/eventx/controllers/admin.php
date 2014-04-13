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
class EVENTX_CTRL_Admin extends ADMIN_CTRL_Abstract {

    public function __construct() {
        parent::__construct();

        if (OW::getRequest()->isAjax()) {
            return;
        }

        $language = OW::getLanguage();
        $config = OW::getConfig();

        $menu = new BASE_CMP_ContentMenu();

        $menuItem = new BASE_MenuItem();
        $menuItem->setKey('admin-index');
        $menuItem->setLabel($language->text('eventx', 'admin_tab_general_title'));
        $menuItem->setUrl(OW::getRouter()->urlForRoute('eventx_admin_index'));
        $menuItem->setIconClass('ow_ic_files');
        $menuItem->setOrder(1);
        $menu->addElement($menuItem);

        if ($config->getValue('eventx', 'enableCategoryList') == '1') {
            $menuItem = new BASE_MenuItem();
            $menuItem->setKey('categories');
            $menuItem->setLabel($language->text('eventx', 'admin_category_tab_title'));
            $menuItem->setUrl(OW::getRouter()->urlForRoute('eventx_admin_categories'));
            $menuItem->setIconClass('ow_ic_gear_wheel');
            $menuItem->setOrder(2);
            $menu->addElement($menuItem);
        }

        $menuItem = new BASE_MenuItem();
        $menuItem->setKey('approval');
        $menuItem->setLabel($language->text('eventx', 'admin_pending_approval'));
        $menuItem->setUrl(OW::getRouter()->urlForRoute('eventx_admin_approval'));
        $menuItem->setIconClass('ow_ic_gear_wheel');
        $menuItem->setOrder(3);
        $menu->addElement($menuItem);

        if ($config->getValue('eventx', 'enableCalendar') == '1') {
            $menuItem = new BASE_MenuItem();
            $menuItem->setKey('calendar');
            $menuItem->setLabel($language->text('eventx', 'admin_calendar_tab'));
            $menuItem->setUrl(OW::getRouter()->urlForRoute('eventx_admin_calendar'));
            $menuItem->setIconClass('ow_ic_calendar');
            $menuItem->setOrder(4);
            $menu->addElement($menuItem);
        }

        if (is_dir(OW_DIR_PLUGIN . 'event')) {
            $menuItem = new BASE_MenuItem();
            $menuItem->setKey('import');
            $menuItem->setLabel($language->text('eventx', 'admin_import_tab'));
            $menuItem->setUrl(OW::getRouter()->urlForRoute('eventx_admin_import'));
            $menuItem->setIconClass('ow_ic_gear_wheel');
            $menuItem->setOrder(5);
            $menu->addElement($menuItem);
        }

        $this->addComponent('menu', $menu);
        $this->menu = $menu;

        $this->setPageHeading(OW::getLanguage()->text('eventx', 'admin_settings_title'));
        $this->setPageTitle(OW::getLanguage()->text('eventx', 'admin_settings_title'));
        $this->setPageHeadingIconClass('ow_ic_gear_wheel');
    }

    public function index() {
        $language = OW::getLanguage();
        $config = OW::getConfig();

        $adminForm = new Form('adminForm');

        $element = new Selectbox('itemApproval');
        $element->setRequired(true);
        $element->setValue($config->getValue('eventx', 'itemApproval'));
        $element->setLabel($language->text('eventx', 'admin_event_approval'));
        $element->addOption('auto', $language->text('eventx', 'auto_approve'));
        $element->addOption('admin', $language->text('eventx', 'admin_approve'));
        $adminForm->addElement($element);

        $element = new CheckboxGroup('eventDelete');
        $element->setRequired(true);
        $element->setColumnCount(3);
        $element->setValue(explode(",", $config->getValue('eventx', 'eventDelete')));
        $element->setLabel($language->text('eventx', 'admin_event_delete'));
        $element->addOption(1, $language->text('eventx', 'admin'));
        $element->addOption(2, $language->text('eventx', 'moderator'));
        $element->addOption(3, $language->text('eventx', 'creator_del'));
        $adminForm->addElement($element);

        $element = new TextField('resultsPerPage');
        $element->setRequired(true);
        $element->setLabel($language->text('eventx', 'admin_results_per_page'));
        $element->setValue($config->getValue('eventx', 'resultsPerPage'));
        $validator = new IntValidator(1);
        $validator->setErrorMessage($language->text('eventx', 'invalid_numeric_format'));
        $element->addValidator($validator);
        $adminForm->addElement($element);

        $element = new TextField('mapWidth');
        $element->setRequired(true);
        $element->setValue($config->getValue('eventx', 'mapWidth'));
        $validator = new IntValidator(0);
        $validator->setErrorMessage($language->text('eventx', 'invalid_numeric_format'));
        $element->addValidator($validator);
        $adminForm->addElement($element);

        $element = new TextField('mapHeight');
        $element->setRequired(true);
        $element->setValue($config->getValue('eventx', 'mapHeight'));
        $validator = new IntValidator(0);
        $validator->setErrorMessage($language->text('eventx', 'invalid_numeric_format'));
        $element->addValidator($validator);
        $adminForm->addElement($element);

        $element = new CheckboxField('enableCategoryList');
        $element->setLabel($language->text('eventx', 'admin_enable_category_listing'));
        $element->setValue($config->getValue('eventx', 'enableCategoryList'));
        $adminForm->addElement($element);

        $element = new CheckboxField('enableCalendar');
        $element->setLabel($language->text('eventx', 'admin_enable_calendar'));
        $element->setValue($config->getValue('eventx', 'enableCalendar'));
        $adminForm->addElement($element);

        $element = new CheckboxField('enableTagsList');
        $element->setLabel($language->text('eventx', 'admin_enable_tag_listing'));
        $element->setValue($config->getValue('eventx', 'enableTagsList'));
        $adminForm->addElement($element);

        $element = new CheckboxField('enableMultiCategories');
        $element->setLabel($language->text('eventx', 'enable_multi_categories'));
        $element->setValue($config->getValue('eventx', 'enableMultiCategories'));
        $adminForm->addElement($element);

        $element = new CheckboxField('enable3DTagCloud');
        $element->setLabel($language->text('eventx', 'enable_3d_cloud_categories'));
        $element->setValue($config->getValue('eventx', 'enable3DTagCloud'));
        $adminForm->addElement($element);

        $element = new CheckboxField('enableMapSuggestion');
        $element->setLabel($language->text('eventx', 'enable_map_suggestion'));
        $element->setValue($config->getValue('eventx', 'enableMapSuggestion'));
        $adminForm->addElement($element);

        $element = new Submit('saveSettings');
        $element->setValue(OW::getLanguage()->text('eventx', 'admin_save_settings'));
        $adminForm->addElement($element);

        if (OW::getRequest()->isPost()) {
            if ($adminForm->isValid($_POST)) {
                $values = $adminForm->getValues();
                $config->saveConfig('eventx', 'resultsPerPage', $values['resultsPerPage']);
                $config->saveConfig('eventx', 'itemApproval', $values['itemApproval']);
                $config->saveConfig('eventx', 'enableCategoryList', $values['enableCategoryList']);
                $config->saveConfig('eventx', 'enableMultiCategories', $values['enableMultiCategories']);
                $config->saveConfig('eventx', 'enable3DTagCloud', $values['enable3DTagCloud']);
                $config->saveConfig('eventx', 'enableMapSuggestion', $values['enableMapSuggestion']);
                $config->saveConfig('eventx', 'mapWidth', $values['mapWidth']);
                $config->saveConfig('eventx', 'mapHeight', $values['mapHeight']);
                $config->saveConfig('eventx', 'eventDelete', implode(",", $values['eventDelete']));
                $config->saveConfig('eventx', 'enableTagsList', $values['enableTagsList']);
                $config->saveConfig('eventx', 'enableCalendar', $values['enableCalendar']);

                OW::getFeedback()->info($language->text('eventx', 'user_save_success'));

                $this->redirect(OW::getRouter()->urlForRoute('eventx_admin_index'));
            }
        }

        $this->addForm($adminForm);
    }

    public function categories() {
        $categories = EVENTX_BOL_EventService::getInstance()->getCategoriesList();

        $adminForm = new Form('categoriesForm');

        $language = OW::getLanguage();

        $element = new TextField('categoryName');
        $element->setRequired();
        $element->setLabel($language->text('eventx', 'admin_category_name'));
        $adminForm->addElement($element);

        $element = new TextField('categoryDesc');
        $element->setRequired();
        $element->setLabel($language->text('eventx', 'admin_category_desc'));
        $adminForm->addElement($element);

        $element = new Submit('addCategory');
        $element->setValue($language->text('eventx', 'admin_add_category'));
        $adminForm->addElement($element);

        if (OW::getRequest()->isPost()) {
            if ($adminForm->isValid($_POST)) {
                $values = $adminForm->getValues();
                $name = UTIL_HtmlTag::stripJs($values['categoryName']);
                $desc = UTIL_HtmlTag::stripJs($values['categoryDesc']);

                $category = new EVENTX_BOL_Category();
                $category->name = $name;
                $category->description = $desc;
                $category->master = 0;

                if (EVENTX_BOL_EventService::getInstance()->addCategory($category))
                    OW::getFeedback()->info($language->text('eventx', 'admin_add_category_success'));
                else
                    OW::getFeedback()->error($language->text('eventx', 'admin_add_category_error'));

                $this->redirect();
            }
        }

        $this->addForm($adminForm);

        $allCategories = array();

        foreach ($categories as $category) {
            $allCategories[$category->id]['id'] = $category->id;
            $allCategories[$category->id]['name'] = $category->name;
            $allCategories[$category->id]['description'] = $category->description;
            $allCategories[$category->id]['editUrl'] = OW::getRouter()->urlFor(__CLASS__, 'edit', array('id' => $category->id));
            $allCategories[$category->id]['deleteUrl'] = OW::getRouter()->urlFor(__CLASS__, 'delete', array('id' => $category->id));
        }

        $this->assign('allCategories', $allCategories);

        $reassignForm = new Form('reassignForm');

        $element = new Selectbox('oldCategory');
        foreach ($categories as $category)
            $element->addOption($category->id, $category->name);

        $element->setRequired();
        $reassignForm->addElement($element);

        $element = new Selectbox('newCategory');
        $element->setRequired();
        foreach ($categories as $category)
            $element->addOption($category->id, $category->name);
        $reassignForm->addElement($element);

        $element = new Submit('reassignCategory');
        $element->setValue($language->text('eventx', 'admin_reassign_category'));
        $reassignForm->addElement($element);

        if (OW::getRequest()->isPost()) {
            if ($reassignForm->isValid($_POST)) {
                $values = $reassignForm->getValues();

                $oldCategory = $values['oldCategory'];
                $newCategory = $values['newCategory'];

                if ($oldCategory == $newCategory) {
                    OW::getFeedback()->error($language->text('eventx', 'admin_same_category_error'));
                } else {
                    EVENTX_BOL_EventService::getInstance()->reassignCategory($oldCategory, $newCategory);
                    OW::getFeedback()->info($language->text('eventx', 'admin_reassign_category_ok'));
                }
                $this->redirect();
            }
        }

        $this->addForm($reassignForm);
    }

    public function approve() {
        $page = ( empty($_GET['page']) || (int) $_GET['page'] < 0 ) ? 1 : (int) $_GET['page'];
        $pageCount = OW::getConfig()->getValue('eventx', 'resultsPerPage');
        $events = EVENTX_BOL_EventService::getInstance()->findPendingEvents($page, $pageCount);
        $eventsCount = EVENTX_BOL_EventService::getInstance()->findPendingEventsCount();

        $this->addComponent('paging', new BASE_CMP_Paging($page, ceil($eventsCount / $pageCount), 5));
        $this->assign('page', $page);

        if (empty($events)) {
            $this->assign('no_events', true);
        }
        $toolbarList = array();
        $this->assign('events', EVENTX_BOL_EventService::getInstance()->getListingDataWithToolbar($events, $toolbarList));
    }

    public function calendar() {
        $language = OW::getLanguage();
        $config = OW::getConfig();

        $adminForm = new Form('adminForm');

        $element = new TextField('eventsCount');
        $element->setValue($config->getValue('eventx', 'eventsCount'));
        $element->setLabel($language->text('eventx', 'events_count_label'));
        $element->setRequired(true);
        $adminForm->addElement($element);

        $element = new TextField('calendarHeight');
        $element->setValue($config->getValue('eventx', 'calendarHeight'));
        $element->setLabel($language->text('eventx', 'calendar_height_label'));
        $element->addValidator(new FloatValidator(0));
        $adminForm->addElement($element);

        $element = new CheckboxField('showPastEvents');
        $element->setLabel($language->text('eventx', 'show_past_events_label'));
        $element->setValue($config->getValue('eventx', 'showPastEvents'));
        $adminForm->addElement($element);

        $element = new CheckboxField('openLinksType');
        $element->setLabel($language->text('eventx', 'open_links_type_label'));
        $element->setValue($config->getValue('eventx', 'openLinksType'));
        $adminForm->addElement($element);

        $element = new CheckboxField('isRTLLanguage');
        $element->setLabel($language->text('eventx', 'is_rtl_label'));
        $element->setValue($config->getValue('eventx', 'isRTLLanguage'));
        $adminForm->addElement($element);

        $element = new CheckboxField('showWeekends');
        $element->setLabel($language->text('eventx', 'show_weekends_label'));
        $element->setValue($config->getValue('eventx', 'showWeekends'));
        $adminForm->addElement($element);

        $element = new Selectbox('firstWeekDay');
        $element->setLabel($language->text('eventx', 'first_weekday_label'));

        for ($i = 0; $i <= 6; $i++) {
            $element->addOption($i, $language->text('base', 'date_time_week_' . $i));
        }

        $element->setValue($config->getValue('eventx', 'firstWeekDay'));
        $adminForm->addElement($element);

        $element = new Submit('saveSettings');
        $element->setValue($language->text('eventx', 'admin_save_settings'));
        $adminForm->addElement($element);

        if (OW::getRequest()->isPost()) {
            if ($adminForm->isValid($_POST)) {
                $values = $adminForm->getValues();

                $config->saveConfig('eventx', 'showPastEvents', $values['showPastEvents']);
                $config->saveConfig('eventx', 'eventsCount', $values['eventsCount']);
                $config->saveConfig('eventx', 'openLinksType', $values['openLinksType']);
                $config->saveConfig('eventx', 'isRTLLanguage', $values['isRTLLanguage']);
                $config->saveConfig('eventx', 'showWeekends', $values['showWeekends']);
                $config->saveConfig('eventx', 'calendarHeight', $values['calendarHeight']);
                $config->saveConfig('eventx', 'firstWeekDay', $values['firstWeekDay']);

                OW::getFeedback()->info($language->text('eventx', 'user_save_success'));
            }
        }

        $this->addForm($adminForm);
    }

    public function delete($params) {
        if (isset($params['id'])) {
            OW::getFeedback()->info(OW::getLanguage()->text('eventx', 'delete_category_ok'));
            EVENTX_BOL_EventService::getInstance()->deleteCategory((int) $params['id']);
        }

        $this->redirect(OW::getRouter()->urlForRoute('eventx_admin_categories'));
    }

    public function edit($params) {
        if (!isset($params['id']) || !($id = (int) $params['id'])) {
            throw new Redirect404Exception();
            return;
        }

        $catItem = EVENTX_BOL_EventService::getInstance()->findCategoryById($id);

        if (!$catItem) {
            throw new Redirect404Exception();
            return;
        }

        $categories = EVENTX_BOL_EventService::getInstance()->getCategoriesList(true);

        $adminForm = new Form('categoriesForm');

        $language = OW::getLanguage();

        $element = new TextField('categoryName');
        $element->setRequired();
        $element->setLabel($language->text('eventx', 'admin_category_name'));
        $element->setValue($catItem->name);
        $adminForm->addElement($element);

        $element = new TextField('categoryDesc');
        $element->setRequired();
        $element->setLabel($language->text('eventx', 'admin_category_desc'));
        $element->setValue($catItem->description);
        $adminForm->addElement($element);

        $element = new Submit('editCategory');
        $element->setValue($language->text('eventx', 'admin_edit_category'));
        $adminForm->addElement($element);

        if (OW::getRequest()->isPost()) {
            if ($adminForm->isValid($_POST)) {
                $values = $adminForm->getValues();
                $name = UTIL_HtmlTag::stripJs($values['categoryName']);
                $desc = UTIL_HtmlTag::stripJs($values['categoryDesc']);

                $catItem->name = $name;
                $catItem->description = $desc;

                if (EVENTX_BOL_EventService::getInstance()->addCategory($catItem))
                    OW::getFeedback()->info($language->text('eventx', 'admin_edit_category_success'));
                else
                    OW::getFeedback()->error($language->text('eventx', 'admin_edit_category_error'));
            }

            $this->redirect(OW::getRouter()->urlForRoute('eventx_admin_categories'));
        }

        $this->addForm($adminForm);

        $this->setPageHeading(OW::getLanguage()->text('eventx', 'admin_edit_category_title'));
        $this->setPageTitle(OW::getLanguage()->text('eventx', 'admin_edit_category_title'));
        $this->setPageHeadingIconClass('ow_ic_gear_wheel');
    }

    public function import() {

        if (!is_dir(OW_DIR_PLUGIN . 'event')) {
            throw new Redirect404Exception();
            return;
        }

        $importForm = new Form('importForm');
        $language = OW::getLanguage();

        $element = new Submit('importVideos');
        $element->setValue($language->text('eventx', 'admin_import_events'));
        $importForm->addElement($element);

        if (OW::getRequest()->isPost()) {
            if ($importForm->isValid($_POST)) {
                $sql = "INSERT INTO " . OW_DB_PREFIX . "eventx_item (title,description,location,createTimeStamp,startTimeStamp,endTimeStamp,userId,whoCanView,whoCanInvite,maxInvites,status,image,endDateFlag,startTimeDisabled,endTimeDisabled,importId,importStatus)  
                        SELECT title,description,location,createTimeStamp,startTimeStamp,endTimeStamp,userId,whoCanView,whoCanInvite,0,'approved',image,endDateFlag,startTimeDisabled,endTimeDisabled,id,1 FROM " . OW_DB_PREFIX . "event_item c
                           WHERE NOT EXISTS (SELECT 1 FROM " . OW_DB_PREFIX . "eventx_item WHERE importId = c.id)";

                OW::getDbo()->query($sql);

                $sourcePath = OW_DIR_USERFILES . 'plugins' . DS . 'event' . DS;
                $destPath = OW::getPluginManager()->getPlugin('eventx')->getUserFilesDir();
                UTIL_File::copyDir($sourcePath, $destPath);

                EVENTX_BOL_EventService::getInstance()->importAll();

                OW::getFeedback()->info($language->text('eventx', 'admin_import_ok'));
            }
        }

        $this->addForm($importForm);
    }

}
