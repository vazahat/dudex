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
class IVIDEO_CTRL_Admin extends ADMIN_CTRL_Abstract {

    public function __construct() {
        parent::__construct();

        if (OW::getRequest()->isAjax()) {
            return;
        }

        $language = OW::getLanguage();

        $menu = new BASE_CMP_ContentMenu();

        $menuItem = new BASE_MenuItem();
        $menuItem->setKey('admin-index');
        $menuItem->setLabel($language->text('ivideo', 'admin_tab_general_title'));
        $menuItem->setUrl(OW::getRouter()->urlForRoute('ivideo_admin'));
        $menuItem->setIconClass('ow_ic_files');
        $menuItem->setOrder(1);
        $menu->addElement($menuItem);

        $menuItem = new BASE_MenuItem();
        $menuItem->setKey('categories');
        $menuItem->setLabel($language->text('ivideo', 'admin_category_tab_title'));
        $menuItem->setUrl(OW::getRouter()->urlForRoute('ivideo_categories'));
        $menuItem->setIconClass('ow_ic_gear_wheel');
        $menuItem->setOrder(2);
        $menu->addElement($menuItem);

        $menuItem = new BASE_MenuItem();
        $menuItem->setKey('approval');
        $menuItem->setLabel($language->text('ivideo', 'admin_pending_approval'));
        $menuItem->setUrl(OW::getRouter()->urlForRoute('ivideo_admin_approval'));
        $menuItem->setIconClass('ow_ic_gear_wheel');
        $menuItem->setOrder(3);
        $menu->addElement($menuItem);

        $this->addComponent('menu', $menu);
        $this->menu = $menu;

        $this->assign('videosPerRow', OW::getConfig()->getValue('ivideo', 'videosPerRow'));
        $this->assign('addItemAuthorized', OW::getUser()->isAuthenticated() && OW::getUser()->isAuthorized('ivideo', 'add'));

        $this->setPageHeading(OW::getLanguage()->text('ivideo', 'admin_settings_title'));
        $this->setPageTitle(OW::getLanguage()->text('ivideo', 'admin_settings_title'));
        $this->setPageHeadingIconClass('ow_ic_gear_wheel');
    }

    public function index() {
        $language = OW::getLanguage();
        $config = OW::getConfig();

        $adminForm = new Form('adminForm');

        $element = new TextField('allowedFileSize');
        $element->setRequired(true);
        $element->setValue($config->getValue('ivideo', 'allowedFileSize'));
        $element->setLabel($language->text('ivideo', 'admin_allowed_file_size'));
        $element->setDescription($language->text('ivideo', 'admin_allowed_file_size_desc'));
        $validator = new FloatValidator(1);
        $validator->setErrorMessage($language->text('ivideo', 'admin_invalid_number_error'));
        $element->addValidator($validator);
        $adminForm->addElement($element);

        $element = new Multiselect('allowedExtensions');
        $element->setRequired(true);
        $element->setValue(explode(",", $config->getValue('ivideo', 'allowedExtensions')));
        $element->setLabel($language->text('ivideo', 'admin_allowed_extension'));
        $element->setDescription($language->text('ivideo', 'admin_allowed_extension_desc'));
        $element->addOption('mp4', 'MP4');
        $element->addOption('flv', 'FLV');
        $element->addOption('avi', 'AVI');
        $element->addOption('wmv', 'WMV');
        $element->addOption('swf', 'SWF');        
        $element->addOption('mov', 'MOV');
        $element->addOption('mpg', 'MPG');
        $element->addOption('3g2', '3G2');
        $element->addOption('ram', 'RAM');
        $element->setSize(6);
        $adminForm->addElement($element);

        $element = new TextField('videosPerRow');
        $element->setValue($config->getValue('ivideo', 'videosPerRow'));
        $element->setLabel($language->text('ivideo', 'admin_videos_per_row'));
        $element->setDescription($language->text('ivideo', 'admin_videos_per_row_desc'));
        $validator = new IntValidator();
        $validator->setErrorMessage($language->text('ivideo', 'admin_invalid_number_error'));
        $element->addValidator($validator);
        $adminForm->addElement($element);

        $element = new TextField('videoPreviewWidth');
        $element->setValue($config->getValue('ivideo', 'videoPreviewWidth'));
        $element->setLabel($language->text('ivideo', 'admin_video_preview_size'));
        $element->setDescription($language->text('ivideo', 'admin_video_preview_size_desc'));
        $validator = new IntValidator();
        $validator->setErrorMessage($language->text('ivideo', 'admin_invalid_number_error'));
        $element->addValidator($validator);
        $adminForm->addElement($element);

        $element = new TextField('videoPreviewHeight');
        $element->setValue($config->getValue('ivideo', 'videoPreviewHeight'));
        $element->setLabel($language->text('ivideo', 'admin_video_preview_height'));
        $validator = new IntValidator();
        $validator->setErrorMessage($language->text('ivideo', 'admin_invalid_number_error'));
        $element->addValidator($validator);
        $adminForm->addElement($element);

        $element = new TextField('videoWidth');
        $element->setValue($config->getValue('ivideo', 'videoWidth'));
        $element->setLabel($language->text('ivideo', 'admin_video_size'));
        $element->setDescription($language->text('ivideo', 'admin_video_size_desc'));
        $validator = new IntValidator();
        $validator->setErrorMessage($language->text('ivideo', 'admin_invalid_number_error'));
        $element->addValidator($validator);
        $adminForm->addElement($element);

        $element = new TextField('videoHeight');
        $element->setValue($config->getValue('ivideo', 'videoHeight'));
        $element->setLabel($language->text('ivideo', 'admin_video_height'));
        $validator = new IntValidator();
        $validator->setErrorMessage($language->text('ivideo', 'admin_invalid_number_error'));
        $element->addValidator($validator);
        $adminForm->addElement($element);

        $element = new Selectbox('videoApproval');
        $element->setRequired(true);
        $element->setValue($config->getValue('ivideo', 'videoApproval'));
        $element->setLabel($language->text('ivideo', 'admin_video_approval'));
        $element->addOption('auto', $language->text('ivideo', 'auto_approve'));
        $element->addOption('admin', $language->text('ivideo', 'admin_approve'));
        $element->setDescription($language->text('ivideo', 'admin_video_approval_desc'));
        $adminForm->addElement($element);

        $element = new Selectbox('theme');
        $element->setRequired(true);
        $element->setValue($config->getValue('ivideo', 'theme'));
        $element->setLabel($language->text('ivideo', 'admin_video_theme'));
        $element->addOption('baseTheme', $language->text('ivideo', 'baseTheme'));
        $element->addOption('classicTheme', $language->text('ivideo', 'classicTheme'));
        $element->addOption('fancyTheme', $language->text('ivideo', 'fancyTheme'));
        $element->addOption('listTheme', $language->text('ivideo', 'listTheme'));
        $element->setDescription($language->text('ivideo', 'admin_video_theme_desc'));
        $adminForm->addElement($element);

        $element = new TextField('resultsPerPage');
        $element->setRequired(true);
        $element->setLabel($language->text('ivideo', 'admin_results_per_page'));
        $element->setDescription($language->text('ivideo', 'admin_results_per_page_desc'));
        $element->setValue($config->getValue('ivideo', 'resultsPerPage'));
        $adminForm->addElement($element);

        $element = new TextField('ffmpegPath');
        $element->setLabel($language->text('ivideo', 'admin_ffmpeg_path'));
        $element->setDescription($language->text('ivideo', 'admin_ffmpeg_path_desc'));
        $element->setValue($config->getValue('ivideo', 'ffmpegPath'));
        $adminForm->addElement($element);

        $element = new CheckboxField('makeUploaderMain');
        $element->setLabel($language->text('ivideo', 'admin_make_uploader_main'));
        $element->setDescription($language->text('ivideo', 'admin_make_uploader_main_desc'));
        $element->setValue($config->getValue('ivideo', 'makeUploaderMain'));
        $adminForm->addElement($element);

        $element = new Submit('saveSettings');
        $element->setValue(OW::getLanguage()->text('ivideo', 'admin_save_settings'));
        $adminForm->addElement($element);

        if (OW::getRequest()->isPost()) {
            if ($adminForm->isValid($_POST)) {
                $values = $adminForm->getValues();
                $config->saveConfig('ivideo', 'allowedFileSize', $values['allowedFileSize']);
                $config->saveConfig('ivideo', 'allowedExtensions', implode(",", $values['allowedExtensions']));
                $config->saveConfig('ivideo', 'videoWidth', $values['videoWidth']);
                $config->saveConfig('ivideo', 'videoHeight', $values['videoHeight']);
                $config->saveConfig('ivideo', 'videoPreviewWidth', $values['videoPreviewWidth']);
                $config->saveConfig('ivideo', 'videoPreviewHeight', $values['videoPreviewHeight']);
                $config->saveConfig('ivideo', 'resultsPerPage', $values['resultsPerPage']);
                $config->saveConfig('ivideo', 'videoApproval', $values['videoApproval']);
                $config->saveConfig('ivideo', 'theme', $values['theme']);
                $config->saveConfig('ivideo', 'videosPerRow', $values['videosPerRow']);
                $config->saveConfig('ivideo', 'makeUploaderMain', $values['makeUploaderMain']);
                $config->saveConfig('ivideo', 'ffmpegPath', $values['ffmpegPath']);

                OW::getFeedback()->info($language->text('ivideo', 'user_save_success'));
            }
        }

        $this->addForm($adminForm);
    }

    public function categories() {
        $adminForm = new Form('categoriesForm');

        $language = OW::getLanguage();

        $element = new TextField('categoryName');
        $element->setRequired();
        $element->setInvitation($language->text('ivideo', 'admin_category_name'));
        $element->setHasInvitation(true);
        $adminForm->addElement($element);

        $element = new TextField('categoryDesc');
        $element->setRequired();
        $element->setInvitation($language->text('ivideo', 'admin_category_desc'));
        $element->setHasInvitation(true);
        $adminForm->addElement($element);

        $element = new Submit('addCategory');
        $element->setValue($language->text('ivideo', 'admin_add_category'));
        $adminForm->addElement($element);

        if (OW::getRequest()->isPost()) {
            if ($adminForm->isValid($_POST)) {
                $values = $adminForm->getValues();
                $name = ucwords(strtolower($values['categoryName']));
                $desc = ucwords(strtolower($values['categoryDesc']));
                if (IVIDEO_BOL_CategoryService::getInstance()->addCategory($name, $desc))
                    OW::getFeedback()->info($language->text('ivideo', 'admin_add_category_success'));
                else
                    OW::getFeedback()->error($language->text('ivideo', 'admin_add_category_error'));

                $this->redirect();
            }
        }

        $this->addForm($adminForm);

        $allCategories = array();
        $deleteUrls = array();

        $categories = IVIDEO_BOL_CategoryService::getInstance()->getCategoriesList();

        foreach ($categories as $category) {
            $allCategories[$category->id]['id'] = $category->id;
            $allCategories[$category->id]['name'] = $category->name;
            $allCategories[$category->id]['description'] = $category->description;
            $deleteUrls[$category->id] = OW::getRouter()->urlFor(__CLASS__, 'delete', array('id' => $category->id));
        }

        $this->assign('allCategories', $allCategories);
        $this->assign('deleteUrls', $deleteUrls);
    }

    public function approve() {
        OW::getDocument()->addStyleSheet(OW::getPluginManager()->getPlugin('ivideo')->getStaticCssUrl() . 'video-js.css');
        OW::getDocument()->addCustomHeadInfo('<script src="' . OW::getPluginManager()->getPlugin('ivideo')->getStaticJsUrl() . 'video.js" type="text/javascript"></script>');
        OW::getDocument()->addCustomHeadInfo('<script>  videojs.options.flash.swf="' . OW::getPluginManager()->getPlugin('ivideo')->getStaticJsUrl() . 'video-js.swf"</script>');
        OW::getDocument()->addCustomHeadInfo('<!--[if IE 9]> <script type="text/javascript"> videojs.options.techOrder = ["flash", "html5", "links"]</script> <![endif]-->');
    }

    public function delete($params) {
        if (isset($params['id'])) {
            IVIDEO_BOL_CategoryService::getInstance()->deleteCategory((int) $params['id']);
        }

        $this->redirect(OW::getRouter()->urlForRoute('ivideo_categories'));
    }

}
