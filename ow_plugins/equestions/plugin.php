<?php

/**
 * Copyright (c) 2012, Sergey Kambalin
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

class EQUESTIONS_Plugin
{
    const PLUGIN_KEY = 'equestions';
    const PLUGIN_VERSION = 490;

    const PRIVACY_ACTION_VIEW_MY_QUESTIONS = 'view_my_questions';

    private static $classInstance;

    /**
     * Returns class instance
     *
     * @return EQUESTIONS_Plugin
     */
    public static function getInstance()
    {
        if ( null === self::$classInstance )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    private function __construct()
    {

    }

    private $staticAdded = false;

    public function addStatic( $ajax = false )
    {
        if ( $this->staticAdded )
        {
            return;
        }

        $staticUrl = OW::getPluginManager()->getPlugin(self::PLUGIN_KEY)->getStaticUrl();
        $scriptUrl = $staticUrl . 'equestions.js' . '?' . self::PLUGIN_VERSION;
        $styleUrl = $staticUrl . 'equestions.css' . '?' . self::PLUGIN_VERSION;

        $imagesUrl = OW::getThemeManager()->getThemeImagesUrl();
        $css = 'html body div .q_ic_preloader { background-image: url(' . $imagesUrl . 'ajax_preloader_button.gif) };';

        OW::getDocument()->addStyleDeclaration($css);

        if ( !$ajax )
        {
            OW::getDocument()->addScript($scriptUrl);
            OW::getDocument()->addStyleSheet($styleUrl);
        }
        else
        {
            OW::getDocument()->addOnloadScript(UTIL_JsGenerator::composeJsString('
                if ( !window.QUESTIONS_Loaded )
                {

                    OW.addScriptFiles([{$scriptUrl}], function(){
                        if ( window.EQAjaxLoadCallbacksRun )
                        {
                            window.EQAjaxLoadCallbacksRun();
                        }
                    });
                    OW.addCssFile({$styleUrl});

                 }
            ', array(
                'styleUrl' => $styleUrl,
                'scriptUrl' => $scriptUrl
            )));
        }


        $messages = EQUESTIONS_CLASS_CreditsBridge::getInstance()->getAllPermissionMessages();
        $actions = EQUESTIONS_CLASS_CreditsBridge::getInstance()->getAllPermissions();

        $js = UTIL_JsGenerator::newInstance();
        $js->addScript(UTIL_JsGenerator::composeJsString('UTILS.Credits = new UTILS.CreditsConstructor({$actions}, {$messages}); ', array(
            'messages' => $messages,
            'actions' => $actions
        )));

        $friendMode = (bool) OW::getEventManager()->call('plugin.friends');

        $js->setVariable(array('QUESTIONS', 'friendMode'), $friendMode);


        if ( !$ajax )
        {
            OW::getDocument()->addOnloadScript($js);
        }
        else
        {
            OW::getDocument()->addOnloadScript('window.EQAjaxLoadCallbackQueue = [];');

            OW::getDocument()->addOnloadScript('(function() {
                var loaded = function() {
                    ' . $js->generateJs() . '
                };

                if ( window.QUESTIONS_Loaded )
                    loaded.call();
                else
                    window.EQAjaxLoadCallbackQueue.push(loaded);
            })();');
        }


        OW::getLanguage()->addKeyForJs('equestions', 'selector_title_friends');
        OW::getLanguage()->addKeyForJs('equestions', 'selector_title_users');
        OW::getLanguage()->addKeyForJs('equestions', 'followers_fb_title');

        OW::getLanguage()->addKeyForJs('equestions', 'toolbar_unfollow_btn');
        OW::getLanguage()->addKeyForJs('equestions', 'toolbar_follow_btn');

        $this->staticAdded = true;
    }

    public function isReady()
    {
        $installed = OW::getConfig()->getValue('equestions', 'plugin_installed');
        return $installed || !OW::getPluginManager()->isPluginActive('questions');
    }

    public function init()
    {
        OW::getRouter()->addRoute(new OW_Route('equestions-preview', 'admin/plugins/questions/upgrade', 'EQUESTIONS_CTRL_Upgrade', 'index'));

        if ( $this->isReady() )
        {
            $this->fullInit();
        }
        else
        {
            $this->shortInit();
        }
    }

    private function shortInit()
    {
        OW::getRouter()->addRoute(new OW_Route('equestions-upgrade', 'admin/plugins/questions/upgrade', 'EQUESTIONS_CTRL_Upgrade', 'index'));
        OW::getRouter()->addRoute(new OW_Route('equestions-admin-main', 'admin/plugins/questions/upgrade', 'EQUESTIONS_CTRL_Upgrade', 'index'));

        OW::getEventManager()->bind('admin.add_admin_notification', array($this, 'onSetupAdminNotification'));
        OW::getEventManager()->bind(OW_EventManager::ON_BEFORE_PLUGIN_UNINSTALL, array($this, 'onPluginUninstall'));
    }

    private function fullInit()
    {
        OW::getRouter()->addRoute(new OW_Route('equestions-index', 'questions', 'EQUESTIONS_CTRL_List', 'all'));
        OW::getRouter()->addRoute(new OW_Route('equestions-all', 'questions', 'EQUESTIONS_CTRL_List', 'all'));
        OW::getRouter()->addRoute(new OW_Route('equestions-my', 'questions/my', 'EQUESTIONS_CTRL_List', 'my'));
        OW::getRouter()->addRoute(new OW_Route('equestions-friends', 'questions/friends', 'EQUESTIONS_CTRL_List', 'friends'));
        OW::getRouter()->addRoute(new OW_Route('equestions-admin-main', 'admin/plugins/questions', 'EQUESTIONS_CTRL_Admin', 'main'));

        OW::getRouter()->addRoute(new OW_Route('equestions-question', 'questions/:qid', 'EQUESTIONS_CTRL_Questions', 'question'));

        $newsfeedBridge = EQUESTIONS_CLASS_NewsfeedBridge::getInstance();

        OW::getEventManager()->bind('feed.get_status_update_cmp', array($newsfeedBridge, 'onStatusCmp'));
        OW::getEventManager()->bind('feed.on_item_render', array($newsfeedBridge, 'onItemRender'));
        OW::getEventManager()->bind('feed.on_entity_add', array($newsfeedBridge, 'onEntityAdd'));
        OW::getEventManager()->bind('feed.on_activity', array($newsfeedBridge, 'onActivity'));
        OW::getEventManager()->bind(EQUESTIONS_BOL_Service::EVENT_ANSWER_ADDED, array($newsfeedBridge, 'onAnswerAdd'));
        OW::getEventManager()->bind(EQUESTIONS_BOL_Service::EVENT_ANSWER_REMOVED, array($newsfeedBridge, 'onAnswerRemove'));
        OW::getEventManager()->bind(EQUESTIONS_BOL_Service::EVENT_FOLLOW_ADDED, array($newsfeedBridge, 'onFollowAdd'));
        OW::getEventManager()->bind(EQUESTIONS_BOL_Service::EVENT_FOLLOW_REMOVED, array($newsfeedBridge, 'onFollowRemove'));
        OW::getEventManager()->bind(EQUESTIONS_BOL_Service::EVENT_QUESTION_REMOVED, array($newsfeedBridge, 'onQuestionRemove'));
        OW::getEventManager()->bind(EQUESTIONS_BOL_Service::EVENT_POST_ADDED, array($newsfeedBridge, 'onPostAdd'));
        OW::getEventManager()->bind(EQUESTIONS_BOL_Service::EVENT_POST_REMOVED, array($newsfeedBridge, 'onPostRemove'));
        OW::getEventManager()->bind('feed.collect_configurable_activity', array($newsfeedBridge, 'configurableActivity'));
        OW::getEventManager()->bind(EQUESTIONS_BOL_Service::EVENT_QUESTION_ASKED, array($newsfeedBridge, 'onAsk'));

        $activityBridge = EQUESTIONS_CLASS_ActivityBridge::getInstance();

        OW::getEventManager()->bind(EQUESTIONS_BOL_Service::EVENT_QUESTION_ADDED, array($activityBridge, 'onQuestionAdd'));
        OW::getEventManager()->bind(EQUESTIONS_BOL_Service::EVENT_QUESTION_REMOVED, array($activityBridge, 'onQuestionRemove'));
        OW::getEventManager()->bind(EQUESTIONS_BOL_Service::EVENT_ANSWER_ADDED, array($activityBridge, 'onAnswerAdd'));
        OW::getEventManager()->bind(EQUESTIONS_BOL_Service::EVENT_ANSWER_REMOVED, array($activityBridge, 'onAnswerRemove'));
        OW::getEventManager()->bind(EQUESTIONS_BOL_Service::EVENT_FOLLOW_ADDED, array($activityBridge, 'onFollowAdd'));
        OW::getEventManager()->bind(EQUESTIONS_BOL_Service::EVENT_FOLLOW_REMOVED, array($activityBridge, 'onFollowRemove'));
        OW::getEventManager()->bind(EQUESTIONS_BOL_Service::EVENT_POST_ADDED, array($activityBridge, 'onPostAdd'));
        OW::getEventManager()->bind(EQUESTIONS_BOL_Service::EVENT_POST_REMOVED, array($activityBridge, 'onPostRemove'));
        OW::getEventManager()->bind(EQUESTIONS_BOL_Service::EVENT_QUESTION_ASKED, array($activityBridge, 'onAsk'));

        $commentBridge = EQUESTIONS_CLASS_CommentsBridge::getInstance();

        OW::getEventManager()->bind('base_add_comment', array($commentBridge, 'onCommentAdd'));
        OW::getEventManager()->bind('base_delete_comment', array($commentBridge, 'onCommentRemove'));
        OW::getEventManager()->bind(EQUESTIONS_BOL_Service::EVENT_QUESTION_REMOVED, array($commentBridge, 'onQuestionRemove'));

        $groupsBridge = EQUESTIONS_CLASS_GroupsBridge::getInstance();
        OW::getEventManager()->bind(EQUESTIONS_BOL_Service::EVENT_BEFORE_QUESTION_ADDED, array($groupsBridge, 'onBeforeQuestionAdd'));
        OW::getEventManager()->bind(EQUESTIONS_BOL_Service::EVENT_ON_INTERACT_PERMISSION_CHECK, array($groupsBridge, 'onCheckInteractPermission'));

        EQUESTIONS_CLASS_InvitationsBridge::getInstance()->init();

        OW::getEventManager()->bind('admin.add_auth_labels', array($this, 'onAuthLabelsCollect'));

        OW::getEventManager()->bind(OW_EventManager::ON_FINALIZE, array($this, 'onFinalize'));

        $creditsBridge = EQUESTIONS_CLASS_CreditsBridge::getInstance();

        OW::getEventManager()->bind(EQUESTIONS_BOL_Service::EVENT_QUESTION_ADDED, array($creditsBridge, 'onQuestionAdd'));
        OW::getEventManager()->bind(EQUESTIONS_BOL_Service::EVENT_ANSWER_ADDED, array($creditsBridge, 'onAnswerAdd'));
        //OW::getEventManager()->bind(EQUESTIONS_BOL_Service::EVENT_FOLLOW_ADDED, array($creditsBridge, 'onFollowAdd'));
        OW::getEventManager()->bind(EQUESTIONS_BOL_Service::EVENT_POST_ADDED, array($creditsBridge, 'onPostAdd'));
        OW::getEventManager()->bind(EQUESTIONS_BOL_Service::EVENT_QUESTION_ASKED, array($creditsBridge, 'onAsk'));
        OW::getEventManager()->bind(EQUESTIONS_BOL_Service::EVENT_OPTION_ADDED, array($creditsBridge, 'onOptionAdd'));

        EQUESTIONS_CLASS_EnotificationBridge::getInstance()->init();

        //Privacy
        OW::getEventManager()->bind('plugin.privacy.get_action_list', array($this, 'collectPrivacyActions'));
        OW::getEventManager()->bind('feed.collect_privacy', array($newsfeedBridge, 'collectPrivacy'));
        OW::getEventManager()->bind('plugin.privacy.on_change_action_privacy', array($this, 'onPrivacyChange'));

        $credits = new EQUESTIONS_CLASS_Credits();
        OW::getEventManager()->bind('usercredits.on_action_collect', array($credits, 'bindCreditActionsCollect'));
    }

    public function activate()
    {
        if ( $this->isReady() )
        {
            $this->fullActivate();
        }
        else
        {
            $this->shortActivate();
        }
    }

    public function fullActivate()
    {
        $navigation = OW::getNavigation();

        $navigation->addMenuItem(
            OW_Navigation::MAIN,
            'equestions-index',
            'equestions',
            'main_menu_list',
            OW_Navigation::VISIBLE_FOR_ALL);

        $widgetService = BOL_ComponentAdminService::getInstance();
        $widget = $widgetService->addWidget('EQUESTIONS_CMP_IndexWidget', false);
        $widgetService->addWidgetToPlace($widget, BOL_ComponentService::PLACE_INDEX);

        require_once dirname(__FILE__) . DS . 'classes' . DS . 'credits.php';

        $credits = new EQUESTIONS_CLASS_Credits();
        $credits->triggerCreditActionsAdd();
    }

    public function shortActivate()
    {

    }

    public function deactivate()
    {
        OW::getNavigation()->deleteMenuItem('equestions', 'main_menu_list');

        $widgetService = BOL_ComponentAdminService::getInstance();
        $widgetService->deleteWidget('EQUESTIONS_CMP_IndexWidget');
    }

    public function install()
    {
        OW::getConfig()->addConfig('equestions', 'plugin_installed', '0');

        if ( $this->isReady() )
        {
            $this->startInstall();
            $this->completeInstall();
        }
        else
        {
            $this->startInstall();
        }
    }

    public function startInstall()
    {
        $plugin = OW::getPluginManager()->getPlugin(self::PLUGIN_KEY);

        $sql = array();

        $sql[] = 'CREATE TABLE `' . OW_DB_PREFIX . 'equestions_question` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `userId` int(11) NOT NULL,
            `text` text NOT NULL,
            `settings` text NOT NULL,
            `timeStamp` int(11) NOT NULL,
            `attachment` text,
            PRIMARY KEY (`id`),
            KEY `userId` (`userId`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;';

        $sql[] = 'CREATE TABLE `' . OW_DB_PREFIX . 'equestions_option` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `userId` int(11) NOT NULL,
            `questionId` int(11) NOT NULL,
            `text` text CHARACTER SET utf8 NOT NULL,
            `timeStamp` int(11) NOT NULL,
            PRIMARY KEY (`id`),
            KEY `questionId` (`questionId`,`timeStamp`)
        ) ENGINE = MYISAM CHARSET=utf8 ;';

        $sql[] = 'CREATE TABLE `' . OW_DB_PREFIX . 'equestions_answer` (
            `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
            `userId` INT NOT NULL ,
            `optionId` INT NOT NULL ,
            `timeStamp` INT NOT NULL ,
            INDEX ( `optionId` , `timeStamp` )
        ) ENGINE = MYISAM CHARSET=utf8 ;';

        $sql[] = 'CREATE TABLE `' . OW_DB_PREFIX . 'equestions_follow` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `userId` int(11) NOT NULL,
            `questionId` int(11) NOT NULL,
            `timeStamp` int(11) NOT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `userId` (`userId`,`questionId`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;';

        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . OW_DB_PREFIX . 'equestions_activity` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `questionId` int(11) NOT NULL,
            `activityType` varchar(100) CHARACTER SET utf8 NOT NULL,
            `activityId` int(11) NOT NULL,
            `userId` int(11) NOT NULL,
            `timeStamp` int(11) NOT NULL,
            `privacy` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT "everybody",
            `data` text CHARACTER SET utf8,
            PRIMARY KEY (`id`),
            UNIQUE KEY `activityUniq` (`questionId`,`activityType`,`activityId`),
            KEY `userId` (`userId`),
            KEY `timeStamp` (`timeStamp`),
            KEY `questionId` (`questionId`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;';

        $sql[] = 'CREATE TABLE `' . OW_DB_PREFIX . 'equestions_notification` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `userId` int(11) NOT NULL,
            `senderId` int(11) NOT NULL,
            `type` varchar(100) NOT NULL,
            `questionId` int(11) NOT NULL,
            `timeStamp` int(11) NOT NULL,
            `viewed` tinyint(4) NOT NULL,
            `special` tinyint(4) NOT NULL,
            `data` text NOT NULL,
            PRIMARY KEY (`id`),
            KEY `userId` (`userId`),
            KEY `senderId` (`senderId`),
            KEY `type` (`type`),
            KEY `questionId` (`questionId`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;';

        foreach ( $sql as $q )
        {
            OW::getDbo()->query($q);
        }

        OW::getConfig()->addConfig('equestions', 'allow_comments', '1');
        OW::getConfig()->addConfig('equestions', 'enable_follow', '1');
        OW::getConfig()->addConfig('equestions', 'ask_friends', '1');
        OW::getConfig()->addConfig('equestions', 'list_order', 'latest');
        OW::getConfig()->addConfig('equestions', 'attachments', '1');
        OW::getConfig()->addConfig('equestions', 'attachments_video', '1');
        OW::getConfig()->addConfig('equestions', 'attachments_image', '1');
        OW::getConfig()->addConfig('equestions', 'attachments_link', '1');
        OW::getConfig()->addConfig('equestions', 'allow_popups', '1');

        BOL_LanguageService::getInstance()->importPrefixFromZip($plugin->getRootDir() . 'langs.zip', 'equestions');
    }

    public function completeInstall()
    {
        if ( OW::getConfig()->getValue('equestions', 'plugin_installed') )
        {
            return;
        }

        $authorization = OW::getAuthorization();
        $groupName = self::PLUGIN_KEY;
        $authorization->addGroup($groupName);

        $authorization->addAction($groupName, 'add_comment');
        $authorization->addAction($groupName, 'ask');
        $authorization->addAction($groupName, 'answer');
        $authorization->addAction($groupName, 'add_answer');
        $authorization->addAction($groupName, 'delete_comment_by_content_owner');

        OW::getPluginManager()->addPluginSettingsRouteName('equestions', 'equestions-admin-main');

        OW::getConfig()->saveConfig('equestions', 'plugin_installed', '1');
    }

    //Callbacks

    public function onSetupAdminNotification( BASE_CLASS_EventCollector $e )
    {
        $language = OW::getLanguage();

        $e->add($language->text(self::PLUGIN_KEY, 'admin_setup_required_notification', array(
            'href' => OW::getRouter()->urlForRoute('equestions-upgrade')
        )));
    }

    public function onAuthLabelsCollect( BASE_CLASS_EventCollector $event )
    {
        $language = OW::getLanguage();
        $event->add(
            array(
                'equestions' => array(
                    'label' => $language->text('equestions', 'auth_group_label'),
                    'actions' => array(
                        'add_comment' => $language->text('equestions', 'auth_add_comment'),
                        'ask' => $language->text('equestions', 'auth_ask'),
                        'answer' => $language->text('equestions', 'auth_answer'),
                        'add_answer' => $language->text('equestions', 'auth_add_answer'),
                        'delete_comment_by_content_owner' => $language->text('equestions', 'auth_answer_delete_comment')
                    )
                )
            )
        );
    }

    public function onPluginUninstall( OW_Event $event )
    {
        $params = $event->getParams();
        $pluginKey = $params['pluginKey'];

        if ( $pluginKey != 'questions' )
        {
            return;
        }

        $this->completeInstall();
        $this->fullActivate();
    }

    public function onFinalize( OW_Event $event )
    {

    }


    public function collectPrivacyActions( BASE_CLASS_EventCollector $event )
    {
        $language = OW::getLanguage();

        $action = array(
            'key' => self::PRIVACY_ACTION_VIEW_MY_QUESTIONS,
            'pluginKey' => self::PLUGIN_KEY,
            'label' => $language->text(self::PLUGIN_KEY, 'privacy_action_view_my_questions'),
            'description' => '',
            'defaultValue' => EQUESTIONS_BOL_FeedService::PRIVACY_EVERYBODY
        );

        $event->add($action);
    }

    public function onPrivacyChange( OW_Event $e )
    {
        $params = $e->getParams();

        $userId = (int) $params['userId'];
        $actionList = $params['actionList'];
        $actionList = is_array($actionList) ? $actionList : array();

        if ( empty($actionList[self::PRIVACY_ACTION_VIEW_MY_QUESTIONS]) )
        {
            return;
        }

        EQUESTIONS_BOL_FeedService::getInstance()->setPrivacy($userId, $actionList[self::PRIVACY_ACTION_VIEW_MY_QUESTIONS]);
    }
}