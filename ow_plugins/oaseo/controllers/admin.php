<?php

/**
 * Copyright (c) 2011 Sardar Madumarov
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * @author Sardar Madumarov <madumarov@gmail.com>
 * @package oaseo.controllers
 */
class OASEO_CTRL_Admin extends ADMIN_CTRL_Abstract
{

    /**
     * @var OASEO_BOL_Service
     */
    private $service;

    /**
     * @var BASE_CMP_ContentMenu
     */
    private $menu;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->service = OASEO_BOL_Service::getInstance();

        if ( !OW::getRequest()->isAjax() )
        {
            $language = OW::getLanguage();

            $this->setPageHeading(OW::getLanguage()->text('oaseo', 'admin_index_heading'));
            $this->setPageHeadingIconClass('ow_ic_gear_wheel');
            OW::getNavigation()->activateMenuItem('admin_plugins', 'admin', 'sidebar_menu_plugins_installed');

            $menuItem = new BASE_MenuItem();
            $menuItem->setKey('index');
            $menuItem->setLabel($language->text('oaseo', 'admin_tab_index_label'));
            $menuItem->setUrl(OW::getRouter()->urlForRoute('oaseo.admin_index'));
            $menuItem->setIconClass('ow_ic_files');
            $menuItem->setOrder(1);

            $menuItem1 = new BASE_MenuItem();
            $menuItem1->setKey('advanced');
            $menuItem1->setLabel($language->text('oaseo', 'admin_tab_advanced_label'));
            $menuItem1->setUrl(OW::getRouter()->urlForRoute('oaseo.admin_advanced'));
            $menuItem1->setOrder(2);

            $menuItem2 = new BASE_MenuItem();
            $menuItem2->setKey('slugs');
            $menuItem2->setLabel($language->text('oaseo', 'admin_tab_slugs_label'));
            $menuItem2->setUrl(OW::getRouter()->urlForRoute('oaseo.admin_slugs'));
            $menuItem2->setIconClass('ow_ic_script');
            $menuItem2->setOrder(3);

            $menuItem3 = new BASE_MenuItem();
            $menuItem3->setKey('robots');
            $menuItem3->setLabel($language->text('oaseo', 'admin_tab_robots_label'));
            $menuItem3->setUrl(OW::getRouter()->urlForRoute('oaseo.admin_robots'));
            $menuItem3->setIconClass('ow_ic_lens');
            $menuItem3->setOrder(4);

            $menuItem4 = new BASE_MenuItem();
            $menuItem4->setKey('sitemap');
            $menuItem4->setLabel($language->text('oaseo', 'admin_tab_sitemap_label'));
            $menuItem4->setUrl(OW::getRouter()->urlForRoute('oaseo.admin_sitemap'));
            $menuItem4->setIconClass('ow_ic_plugin');
            $menuItem4->setOrder(5);

            $menuItem5 = new BASE_MenuItem();
            $menuItem5->setKey('sitemap_info');
            $menuItem5->setLabel($language->text('oaseo', 'admin_tab_sitemap_info_label'));
            $menuItem5->setUrl(OW::getRouter()->urlForRoute('oaseo.admin_sitemap_info'));
            $menuItem5->setIconClass('ow_ic_info');
            $menuItem5->setOrder(6);

            $this->menu = new BASE_CMP_ContentMenu(array($menuItem, $menuItem1, $menuItem2, $menuItem3, $menuItem4, $menuItem5));

            $this->assign('oaseoImageUrl', 'http://oxart.net/' . str_replace('.', '', OW::getConfig()->getValue('base', 'soft_version')) . '/oaseo/' . OW::getPluginManager()->getPlugin('oaseo')->getDto()->getBuild() . '/oa-post-it-note.jpg');
            $this->addComponent('contentMenu', $this->menu);
        }
    }

    public function index()
    {
        $language = OW::getLanguage();

        if ( OW::getRequest()->isAjax() && OW::getRequest()->isPost() )
        {
            $langService = BOL_LanguageService::getInstance(false);

            if ( isset($_POST['title']) )
            {
                $titleLangVal = $langService->getValue($langService->getCurrent()->getId(), 'oaseo', 'page_default_title');

                if ( $titleLangVal === null )
                {
                    $key = $langService->findKey('oaseo', 'page_default_title');
                    $titleLangVal = new BOL_LanguageValue();
                    $titleLangVal->setKeyId($key->getId());
                    $titleLangVal->setLanguageId($langService->getCurrent()->getId());
                }

                $titleLangVal->setValue(trim($_POST['title']));
                $langService->saveValue($titleLangVal);
            }

            if ( isset($_POST['desc']) )
            {
                $titleLangVal = $langService->getValue($langService->getCurrent()->getId(), 'oaseo', 'page_default_desc');

                if ( $titleLangVal === null )
                {
                    $key = $langService->findKey('oaseo', 'page_default_desc');
                    $titleLangVal = new BOL_LanguageValue();
                    $titleLangVal->setKeyId($key->getId());
                    $titleLangVal->setLanguageId($langService->getCurrent()->getId());
                }

                $titleLangVal->setValue(trim($_POST['desc']));
                $langService->saveValue($titleLangVal);
            }

            if ( isset($_POST['keywords']) )
            {
                $titleLangVal = $langService->getValue($langService->getCurrent()->getId(), 'oaseo', 'page_default_keywords');

                if ( $titleLangVal === null )
                {
                    $key = $langService->findKey('oaseo', 'page_default_keywords');
                    $titleLangVal = new BOL_LanguageValue();
                    $titleLangVal->setKeyId($key->getId());
                    $titleLangVal->setLanguageId($langService->getCurrent()->getId());
                }

                $titleLangVal->setValue(trim($_POST['keywords']));
                $langService->saveValue($titleLangVal);
            }

            exit(json_encode(array('message' => $language->text('oaseo', 'general_meta_submit_message'))));
        }

        $form = new Form('global_meta_form');
        $form->setAjax();
        $form->setAjaxResetOnSuccess(false);
        $form->bindJsFunction(Form::BIND_SUCCESS, "function(data){OW.info(data.message);}");

        $title = new TextField('title');
        $title->setLabel($language->text('oaseo', 'title_label'));
        $title->setDescription($language->text('oaseo', 'meta_edit_form_title_desc'));
        $title->setValue($language->text('oaseo', 'page_default_title'));
        $form->addElement($title);

        $desc = new Textarea('desc');
        $desc->setLabel($language->text('oaseo', 'desc_label'));
        $desc->setDescription($language->text('oaseo', 'meta_edit_form_desc_desc'));
        $desc->setValue($language->text('oaseo', 'page_default_desc'));
        $form->addElement($desc);

        $kewords = new TextField('keywords');
        $kewords->setLabel($language->text('oaseo', 'keywords_label'));
        $kewords->setDescription($language->text('oaseo', 'meta_edit_form_keyword_desc'));
        $kewords->setValue($language->text('oaseo', 'page_default_keywords'));
        $form->addElement($kewords);

        $submit = new Submit('submit');
        $submit->setValue(OW::getLanguage()->text('admin', 'save_btn_label'));
        $form->addElement($submit);
        $this->addForm($form);
    }

    public function advanced()
    {
        $language = OW::getLanguage();

        $defaultMetaInfo = array(
            'title' => $language->text('oaseo', 'page_default_title', array('defaultTitle' => $language->text('nav', 'page_default_title'))),
            'desc' => $language->text('oaseo', 'page_default_desc', array('defaultDesc' => $language->text('nav', 'page_default_description'))),
            'keywords' => $language->text('oaseo', 'page_default_keywords', array('defaultKeywords' => $language->text('nav', 'page_default_keywords')))
        );

        $script = "$('#oaseo_add_meta_button').click(
            function(){
                window.oaseoFB = OA_AjaxFloatBox('OASEO_CMP_MetaEdit', [" . json_encode($defaultMetaInfo) . ", $('#oaseo_add_meta_url_input').val(), false], {width:900, iconClass: 'ow_ic_gear', title: '" . $language->text('oaseo', 'meta_edit_form_cmp_title') . "'})
            }            
        );
        ";

        OW::getDocument()->addOnloadScript($script);
    }

    public function slugs()
    {
        $language = OW::getLanguage();

        if ( OW::getRequest()->isAjax() && OW::getRequest()->isPost() )
        {
            if ( isset($_POST['plugins']) && is_array($_POST['plugins']) )
            {
                OW::getConfig()->saveConfig('oaseo', OASEO_BOL_Service::CNF_SLUG_PLUGINS, json_encode($_POST['plugins']));
            }

            if ( isset($_POST['redirect']) )
            {
                OW::getConfig()->saveConfig('oaseo', OASEO_BOL_Service::CNF_SLUG_OLD_URLS_ENABLE, (bool) $_POST['redirect']);
            }

            if ( isset($_POST['words']) )
            {
                OW::getConfig()->saveConfig('oaseo', OASEO_BOL_Service::CNF_SLUG_FILTER_COMMON_WORDS, json_encode(array_map('mb_strtolower', array_map('trim', explode(',', $_POST['words'])))));
            }

            exit(json_encode(array('message' => $language->text('oaseo', 'slugs_submit_message'))));
        }

        $data = $this->service->getSlugData();
        $pluginKeys = array_keys($data);

        $event = new BASE_CLASS_EventCollector('admin.add_auth_labels');
        OW::getEventManager()->trigger($event);
        $labelData = $event->getData();
        $dataLabels = empty($labelData) ? array() : call_user_func_array('array_merge', $labelData);
        $finalData = array();

        foreach ( $dataLabels as $pluginKey => $pluginInfo )
        {
            if ( in_array($pluginKey, $pluginKeys) )
            {
                $finalData[$pluginKey] = $pluginInfo['label'];
            }
        }

        $form = new Form('slugs_form');
        $form->setAjax();
        $form->setAjaxResetOnSuccess(false);
        $form->bindJsFunction(Form::BIND_SUCCESS, "function(data){OW.info(data.message);}");

        $plugins = new CheckboxGroup('plugins');
        $plugins->setLabel($language->text('oaseo', 'slug_plugins_label'));
        $plugins->setDescription($language->text('oaseo', 'slug_plugins_desc'));
        $plugins->setOptions($finalData);
        $plugins->setValue(json_decode(OW::getConfig()->getValue('oaseo', OASEO_BOL_Service::CNF_SLUG_PLUGINS), true));
        $form->addElement($plugins);

        $redirect = new CheckboxField('redirect');
        $redirect->setLabel($language->text('oaseo', 'slug_redirect_label'));
        $redirect->setDescription($language->text('oaseo', 'slug_redirect_desc'));
        $redirect->setValue(OW::getConfig()->getValue('oaseo', OASEO_BOL_Service::CNF_SLUG_OLD_URLS_ENABLE));
        $form->addElement($redirect);

        $words = new Textarea('words');
        $words->setLabel($language->text('oaseo', 'slug_words_label'));
        $words->setDescription($language->text('oaseo', 'slug_words_desc'));
        $wordsList = json_decode(OW::getConfig()->getValue('oaseo', OASEO_BOL_Service::CNF_SLUG_FILTER_COMMON_WORDS));

        if( is_array($wordsList) )
        {
            $valString = implode(', ', $wordsList);
        }
        else
        {
            $valString = '';
        }

        $words->setValue($valString);
        
        $form->addElement($words);

        $submit = new Submit('submit');
        $submit->setValue(OW::getLanguage()->text('admin', 'save_btn_label'));
        $form->addElement($submit);

        $this->addForm($form);
    }

    public function robots()
    {
        if ( OW::getRequest()->isPost() )
        {
            $contents = trim($_POST['robots']);
            OW::getConfig()->saveConfig('oaseo', OASEO_BOL_Service::CNF_ROBOTS_CONTENTS, $contents);
            OW::getFeedback()->info(OW::getLanguage()->text('oaseo', 'admin_saved_msg'));
            $this->redirect();
        }

        $form = new Form('robots');
        $textarea = new Textarea('robots');
        $textarea->setValue(OW::getConfig()->getValue('oaseo', OASEO_BOL_Service::CNF_ROBOTS_CONTENTS));
        $form->addElement($textarea);

        $submit = new Submit('submit');
        $form->addElement($submit);

        $this->addForm($form);
    }

    public function sitemap()
    {
        $language = OW::getLanguage();
        $config = OW::getConfig();

        $form = new Form('sitemap_form');

        $sitemapUrl = new TextField('sitemap_url');
        $sitemapUrl->setLabel($language->text('oaseo', 'sitemap_url_label'));
        $sitemapUrl->setDescription($language->text('oaseo', 'sitemap_url_desc'));
        $sitemapUrl->setValue(OW_URL_HOME . $config->getValue('oaseo', 'sitemap_url'));
        $form->addElement($sitemapUrl);

//        $rorUrl = new TextField('ror_url');
//        $rorUrl->setLabel($language->text('oaseo', 'ror_url_label'));
//        $rorUrl->setDescription($language->text('oaseo', 'ror_url_desc'));
//        $rorUrl->setValue(OW_URL_HOME . $config->getValue('oaseo', 'ror_url'));
//        $form->addElement($rorUrl);

        $imageUrl = new TextField('imagemap_url');
        $imageUrl->setLabel($language->text('oaseo', 'imagemap_url_label'));
        $imageUrl->setDescription($language->text('oaseo', 'imagemap_url_desc'));
        $imageUrl->setValue(OW_URL_HOME . $config->getValue('oaseo', 'imagemap_url'));
        $form->addElement($imageUrl);

        $undateFreq = new Selectbox('update_freq');
        $options = array('86400' => 'Daily', '604800' => 'Weekly', '2419200' => 'Monthly');
        $undateFreq->setHasInvitation(false);
        $undateFreq->addOptions($options);
        $undateFreq->setLabel($language->text('oaseo', 'update_freq_label'));
        $undateFreq->setDescription($language->text('oaseo', 'update_freq_desc'));
        $form->addElement($undateFreq);
        $undateFreq->setValue($config->getValue('oaseo', 'update_freq'));

        
        
//        $prio = new CheckboxField('prio');
//        $prio->setLabel($language->text('oaseo', 'prio_label'));
//        $prio->setDescription($language->text('oaseo', 'prio_desc'));
//        $form->addElement($prio);

//        $email = new TextField('email');
//        $email->setLabel($language->text('oaseo', 'email_label'));
//        $email->setDescription($language->text('oaseo', 'email_desc'));
//        $form->addElement($email);

        $inform = new CheckboxGroup('inform');
        $inform->setLabel($language->text('oaseo', 'inform_label'));
        $inform->setDescription($language->text('oaseo', 'inform_desc'));
        $inform->setOptions(array( 'google' => 'Google', 'bing' => 'Bing', 'yahoo' => 'Yahoo', 'ask' => 'Ask' ));
        $form->addElement($inform);
        $inform->setValue(json_decode($config->getValue('oaseo', 'inform')));

//        $extlink = new CheckboxField('extlink');
//        $extlink->setLabel($language->text('oaseo', 'extlink_label'));
//        $extlink->setDescription($language->text('oaseo', 'extlink_desc'));
//        $form->addElement($extlink);
//
//        $brock = new CheckboxField('brock_link');
//        $brock->setLabel($language->text('oaseo', 'brock_link_label'));
//        $brock->setDescription($language->text('oaseo', 'brock_link_desc'));
//        $form->addElement($brock);

        $submit = new Submit('submit');
        $submit->setValue(OW::getLanguage()->text('admin', 'save_btn_label'));
        $form->addElement($submit);

        $this->addForm($form);
        
        if( OW::getRequest()->isPost() && $form->isValid($_POST) )
        {
            $data = $form->getValues();
            $config->saveConfig('oaseo', 'sitemap_url', str_replace(OW_URL_HOME, '', $data['sitemap_url']));
            $config->saveConfig('oaseo', 'imagemap_url', str_replace(OW_URL_HOME, '', $data['imagemap_url']));
            $config->saveConfig('oaseo', 'update_freq', (int)$data['update_freq']);
            $config->saveConfig('oaseo', 'inform', json_encode( $data['inform'] ? $data['inform'] : array() ));
        }
    }

    public function sitemapInfo()
    {
        $config = OW::getConfig();
        
        if( !(bool)$config->getValue('oaseo', 'sitemap_init') )
        {
            $this->assign('init', false);
            return;
        }
        
        if( $this->service->getToProcessPagesCount() > 0 )
        {
            $this->assign('in_process_message', OW::getLanguage()->text('oaseo', 'in_progress_label'));            
            $this->assign('url', $this->service->getNextUrlToProcess());
            $this->assign('processed', $this->service->getProcessedPagesCount());
            $this->assign('to_process', $this->service->getToProcessPagesCount());
            return;
        }

        $page = !empty($_GET['page']) && (int)$_GET['page'] > 0 ? (int)$_GET['page'] : 1;
        $numOnPage = 25;

        $first = $numOnPage*($page-1);
        $count = $numOnPage;
        $itemsCountArr = array(
            'pages' => $this->service->findAllPagesCount(),
            'broken_links' => $this->service->findBrokenPagesCount(),
            'images' => $this->service->findItemsCount(OASEO_BOL_Service::ITEM_VAL_IMAGE),
            'broken_images' => $this->service->findItemsCount(OASEO_BOL_Service::ITEM_VAL_BROKEN_IMAGE),
            'ext_links' => $this->service->findItemsCount(OASEO_BOL_Service::ITEM_VAL_EXT_LINK),
            'brok_ext_links' => $this->service->findItemsCount(OASEO_BOL_Service::ITEM_VAL_BROKEN_EXT_LINK)
            );
        $router = OW::getRouter();    
        $urlArr = array(
            'pages' => $router->urlFor('OASEO_CTRL_Admin', 'sitemapInfo'),
            'broken' => $router->urlFor('OASEO_CTRL_Admin', 'sitemapInfo').'?list=broken',
            'images' => $router->urlFor('OASEO_CTRL_Admin', 'sitemapInfo').'?list=images',
            'broken_images' => $router->urlFor('OASEO_CTRL_Admin', 'sitemapInfo').'?list=broken_images',
            'ext_links' => $router->urlFor('OASEO_CTRL_Admin', 'sitemapInfo').'?list=ext_links',
            'broken_ext_links' => $router->urlFor('OASEO_CTRL_Admin', 'sitemapInfo').'?list=broken_ext_links'
        );
        
        $this->assign('urlArray', $urlArr);
        $this->assign('counts', $itemsCountArr);

        $list = empty($_GET['list']) ? 'std' : trim($_GET['list']);
        $finalArray = array();
        
        switch ( $list )
        {
            case 'broken':
                $items = $this->service->findPages($first, $count, true);
                $itemsCount = $itemsCountArr['broken_links'];
                
                foreach ( $items as $item )
                {
                    if( !isset($finalArray[$item['burl']]) )
                    {
                        $finalArray[$item['burl']] = array();
                    }
                    
                    $finalArray[$item['burl']][] = $item['url'];
                }
                
                break;

            case 'images':
                $items = $this->service->findItems(OASEO_BOL_Service::ITEM_VAL_IMAGE, $first, $count);                
                $itemsCount = $itemsCountArr['images'];
                $list = 'images';
                $finalArray = $this->processPageItems($items);
                break;

            case 'broken_images':
                $items = $this->service->findItems(OASEO_BOL_Service::ITEM_VAL_BROKEN_IMAGE, $first, $count);
                $itemsCount = $itemsCountArr['broken_images'];
                $list = 'broken_images';
                $finalArray = $this->processPageItems($items);
                break;

            case 'ext_links':
                $items = $this->service->findItems(OASEO_BOL_Service::ITEM_VAL_EXT_LINK, $first, $count);
                $itemsCount = $itemsCountArr['ext_links'];
                $list = 'ext_links';
                $finalArray = $this->processPageItems($items);
                break;

            case 'broken_ext_links':
                $items = $this->service->findItems(OASEO_BOL_Service::ITEM_VAL_BROKEN_EXT_LINK, $first, $count);
                $itemsCount = $itemsCountArr['brok_ext_links'];
                $list = 'broken_ext_links';
                $finalArray = $this->processPageItems($items);
                break;

            default:
                $items = $this->service->findPages($first, $count);
                $itemsCount = $itemsCountArr['pages'];
                $list = 'pages';
                
                foreach ( $items as $item )
                {
                    $metaInfo = json_decode($item['meta'], true);

                    $keywords = empty($metaInfo['keywords']) ? '<span style="color:red;">_NO_KEYWORDS_</span>' : $metaInfo['keywords'];
                    $desc = empty($metaInfo['description']) ? '<span style="color:red;">_NO_DESCRIPTION_</span>' : $metaInfo['description'];

                    $finalArray[] = array( 'keywords' => $keywords, 'desc' => $desc, 'url' => urldecode($item['url']), 'title' => empty($item['title']) ? '<span style="color:red;">_NO_TITLE_</span>' : $item['title'] );
                }
        }
        
        $this->assign('list', $list);
        $this->assign('items', $finalArray);
        $this->addComponent('paging', new BASE_CMP_Paging($page, ceil($itemsCount / $numOnPage), 5));
    }
    
    private function processPageItems( $items )
    {
        $finalArray = array();
        
        foreach ( $items as $item )
        {
            if( !isset($finalArray[$item['value']]) )
            {
                $finalArray[$item['value']] = array();
            }

            if( !in_array($item['url'], $finalArray[$item['value']]) )
            {   
                if( sizeof($finalArray[$item['value']]) < 11 )
                {
                    $finalArray[$item['value']][] = $item['url'];
                }
            }
        }
        
        return $finalArray;
    }
}