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
class EVENTX_CTRL_Base extends OW_ActionController {

    private $eventService;
    private $ajaxResponder;

    public function __construct() {
        parent::__construct();
        $this->eventService = EVENTX_BOL_EventService::getInstance();
        $this->ajaxResponder = OW::getRouter()->urlFor('EVENTX_CTRL_Base', 'ajaxResponder');

        $this->assign('add_new_url', OW::getRouter()->urlForRoute('eventx.add'));
        $this->assign('calendar_url', OW::getRouter()->urlForRoute('eventx_view_calendar'));
        $this->assign('enableTagsList', OW::getConfig()->getValue('eventx', 'enableTagsList') == '1' ? 1 : 0);
        $this->assign('enableCategoryList', OW::getConfig()->getValue('eventx', 'enableCategoryList') == '1' ? 1 : 0);

        OW::getNavigation()->activateMenuItem(OW_Navigation::MAIN, 'eventx', 'main_menu_item');
    }

    public function taglist(array $params = null) {
        $modPermissions = OW::getUser()->isAuthorized('eventx');

        if (!OW::getUser()->isAuthorized('eventx', 'view_event') && !$modPermissions) {
            $this->setTemplate(OW::getPluginManager()->getPlugin('base')->getCtrlViewDir() . 'authorization_failed.html');
            return;
        }

        if (!OW::getUser()->isAuthorized('eventx', 'add_event')) {
            $this->assign('noButton', true);
        }

        $contentMenu = $this->eventService->getContentMenu();
        $contentMenu->getElement('tagged')->setActive(true);
        $this->addComponent('contentMenu', $contentMenu);

        $listUrl = OW::getRouter()->urlForRoute('eventx_tag_list');

        OW::getDocument()->addScript(OW::getPluginManager()->getPlugin('eventx')->getStaticJsUrl() . 'eventx_tag_search.js');

        $objParams = array('listUrl' => $listUrl);

        $script = "$(document).ready(function(){
                     var itemSearch = new itemTagSearch(" . json_encode($objParams) . ");
                   });";

        OW::getDocument()->addOnloadScript($script);

        $tag = !empty($params['tag']) ? trim(htmlspecialchars(urldecode($params['tag']))) : '';

        if (strlen($tag)) {
            $this->assign('tag', $tag);

            $page = isset($_GET['page']) && (int) $_GET['page'] ? (int) $_GET['page'] : 1;

            $itemsPerPage = 10;

            $items = $this->eventService->findTaggedItemsList($tag, $page, $itemsPerPage);
            $records = $this->eventService->findTaggedItemsCount($tag);

            if (empty($items)) {
                $this->assign('no_events', true);
            }

            $pages = (int) ceil($records / $itemsPerPage);
            $paging = new BASE_CMP_Paging($page, $pages, 10);

            $this->assign('paging', $paging->render());
            $this->assign('events', $this->eventService->getListingDataWithToolbar($items, array()));
            $this->setPageTitle(OW::getLanguage()->text('eventx', 'meta_description_item_tagged_as', array('tag' => $tag)));
            $this->setPageHeading(OW::getLanguage()->text('eventx', 'meta_description_item_tagged_as', array('tag' => $tag)));
        } else {
            $tags = new BASE_CMP_EntityTagCloud('eventx');
            $tags->setRouteName('eventx_view_tagged_list');
            $this->addComponent('tags', $tags);

            $this->setPageTitle(OW::getLanguage()->text('eventx', 'meta_title_item_tagged'));
            $tagsArr = BOL_TagService::getInstance()->findMostPopularTags('eventx', 20);

            foreach ($tagsArr as $t) {
                $labels[] = $t['label'];
            }
            $tagStr = $tagsArr ? implode(', ', $labels) : '';
            $this->setPageTitle(OW::getLanguage()->text('eventx', 'meta_title_item_tagged'));
            $this->setPageHeading(OW::getLanguage()->text('eventx', 'meta_description_item_tagged'));
        }

        OW::getDocument()->setHeadingIconClass('ow_ic_tag');
    }

    public function calendar() {
        $language = OW::getLanguage();
        $config = OW::getConfig();

        if (!OW::getUser()->isAuthorized('eventx', 'view_event') && !OW::getUser()->isAuthorized('eventx')) {
            $this->assign('authErrorText', $language->text('event', 'event_view_permission_error_message'));
            return;
        }

        $eventsCount = (int) $config->getValue('eventx', 'eventsCount');

        $showPast = $config->getValue('eventx', 'showPastEvents') == '1' ? true : false;

        $events = $this->eventService->findPublicEvents(null, $eventsCount, $showPast);

        $resultArray = array();

        foreach ($events as $eventItem) {
            $resultArray[$eventItem->getId()] = array(
                'title' => addslashes(UTIL_String::truncate(strip_tags($eventItem->getTitle()), 80, "...")),
                'startTimeStamp' => date("Y-m-d H:i:s", $eventItem->startTimeStamp),
                'endTimeStamp' => date("Y-m-d H:i:s", $eventItem->endTimeStamp),
                'endDateFlag' => $eventItem->endDateFlag,
                'eventUrl' => OW::getRouter()->urlForRoute('eventx.view', array('eventId' => $eventItem->getId())),
            );
        }

        $this->assign('events', $resultArray);
        $this->assign('openLinksType', $config->getValue('eventx', 'openLinksType'));
        $this->assign('isRTLLanguage', $config->getValue('eventx', 'isRTLLanguage'));
        $this->assign('showWeekends', $config->getValue('eventx', 'showWeekends'));
        $this->assign('calendarHeight', $config->getValue('eventx', 'calendarHeight'));
        $this->assign('firstWeekDay', $config->getValue('eventx', 'firstWeekDay'));
        $this->assign('militaryTime', (bool) OW::getConfig()->getValue('base', 'military_time') ? '1' : '0');

        OW::getDocument()->addStyleSheet(OW::getPluginManager()->getPlugin("eventx")->getStaticCssUrl() . 'fullcalendar.css');
        OW::getDocument()->addScript(OW::getPluginManager()->getPlugin("eventx")->getStaticJsUrl() . 'fullcalendar.min.js');

        $fullMonth = $halfMonth = $fullWeek = $halfWeek = array();

        for ($i = 0; $i <= 6; $i++) {
            $fullWeek[] = $language->text('base', 'date_time_week_' . $i);
            $halfWeek[] = $language->text('eventx', 'half_week_' . $i);
        }

        for ($i = 1; $i <= 12; $i++) {
            $fullMonth[] = $language->text('base', 'month_' . $i);
            $halfMonth[] = $language->text('base', 'date_time_month_short_' . $i);
        }

        $this->assign('fullWeek', UTIL_String::arrayToDelimitedString($fullWeek, ",", "'", "'"));
        $this->assign('halfWeek', UTIL_String::arrayToDelimitedString($halfWeek, ",", "'", "'"));
        $this->assign('fullMonth', UTIL_String::arrayToDelimitedString($fullMonth, ",", "'", "'"));
        $this->assign('halfMonth', UTIL_String::arrayToDelimitedString($halfMonth, ",", "'", "'"));

        $contentMenu = $this->eventService->getContentMenu();
        $this->addComponent('contentMenu', $contentMenu);

        if (!OW::getUser()->isAuthorized('eventx', 'add_event')) {
            $this->assign('noButton', true);
        }

        $this->setPageTitle($language->text('eventx', 'calendar_page_title'));
        $this->setPageHeading($language->text('eventx', 'calendar_page_heading'));
        $this->setPageHeadingIconClass('ow_ic_calendar');
    }

    public function add() {
        $language = OW::getLanguage();
        $this->setPageTitle($language->text('eventx', 'add_page_title'));
        $this->setPageHeading($language->text('eventx', 'add_page_heading'));
        $this->setPageHeadingIconClass('ow_ic_add');

        OW::getNavigation()->activateMenuItem(OW_Navigation::MAIN, 'eventx', 'main_menu_item');

        if (!OW::getUser()->isAuthenticated() || !OW::getUser()->isAuthorized('eventx', 'add_event')) {
            $this->assign('err_msg', OW::getLanguage()->text('base', 'authorization_failed_feedback'));
            return;
        }

        $eventParams = array('pluginKey' => 'eventx', 'action' => 'add_event');
        $credits = OW::getEventManager()->call('usercredits.check_balance', $eventParams);

        if ($credits === false) {
            $this->assign('err_msg', OW::getEventManager()->call('usercredits.error_message', $eventParams));
            return;
        }

        $form = new EventAddForm('event_add');

        if (date('n', time()) == 12 && date('j', time()) == 31) {
            $defaultDate = (date('Y', time()) + 1) . '/1/1';
        } else if (( date('j', time()) + 1 ) > date('t')) {
            $defaultDate = date('Y', time()) . '/' . ( date('n', time()) + 1 ) . '/1';
        } else {
            $defaultDate = date('Y', time()) . '/' . date('n', time()) . '/' . ( date('j', time()) + 1 );
        }

        $form->getElement('start_date')->setValue($defaultDate);
        $form->getElement('end_date')->setValue($defaultDate);
        $form->getElement('start_time')->setValue('all_day');
        $form->getElement('end_time')->setValue('all_day');
        $form->getElement('who_can_view')->setValue(EVENTX_BOL_EventService::CAN_VIEW_ANYBODY);
        $form->getElement('who_can_invite')->setValue(EVENTX_BOL_EventService::CAN_INVITE_PARTICIPANT);
        $form->getElement('max_invites')->setValue(0);

        $checkboxId = UTIL_HtmlTag::generateAutoId('chk');
        $tdId = UTIL_HtmlTag::generateAutoId('td');
        $this->assign('tdId', $tdId);
        $this->assign('chId', $checkboxId);

        OW::getDocument()->addScript(OW::getPluginManager()->getPlugin("eventx")->getStaticJsUrl() . 'eventx.js');

        $enableMapSuggestion = OW::getConfig()->getValue('eventx', 'enableMapSuggestion');
        if ($enableMapSuggestion == '1') {
            OW::getDocument()->addScript("http://maps.googleapis.com/maps/api/js?sensor=false&amp;libraries=places");
            OW::getDocument()->addScript(OW::getPluginManager()->getPlugin("eventx")->getStaticJsUrl() . 'jquery.geocomplete.min.js');
        }

        $this->assign('enableMapSuggestion', $enableMapSuggestion);

        OW::getDocument()->addOnloadScript("new eventAddForm(" . json_encode(array('checkbox_id' => $checkboxId, 'end_date_id' => $form->getElement('end_date')->getId(), 'tdId' => $tdId)) . ")");

        if (OW::getRequest()->isPost()) {
            if (!empty($_POST['endDateFlag'])) {
                $this->assign('endDateFlag', true);
            }

            if ($form->isValid($_POST)) {
                $data = $form->getValues();

                $serviceEvent = new OW_Event(EVENTX_BOL_EventService::EVENTX_BEFORE_EVENTX_CREATE, array(), $data);
                OW::getEventManager()->trigger($serviceEvent);
                $data = $serviceEvent->getData();

                $dateArray = explode('/', $data['start_date']);

                $startStamp = mktime(0, 0, 0, $dateArray[1], $dateArray[2], $dateArray[0]);

                if ($data['start_time'] != 'all_day') {
                    $startStamp = mktime($data['start_time']['hour'], $data['start_time']['minute'], 0, $dateArray[1], $dateArray[2], $dateArray[0]);
                }

                if (!empty($_POST['endDateFlag']) && !empty($data['end_date'])) {
                    $dateArray = explode('/', $data['end_date']);
                    $endStamp = mktime(0, 0, 0, $dateArray[1], $dateArray[2], $dateArray[0]);

                    $endStamp = strtotime("+1 day", $endStamp);

                    if ($data['end_time'] != 'all_day') {
                        $hour = 0;
                        $min = 0;

                        if ($data['end_time'] != 'all_day') {
                            $hour = $data['end_time']['hour'];
                            $min = $data['end_time']['minute'];
                        }

                        $dateArray = explode('/', $data['end_date']);
                        $endStamp = mktime($hour, $min, 0, $dateArray[1], $dateArray[2], $dateArray[0]);
                    }
                }

                $imageValid = true;
                $datesAreValid = true;
                $imagePosted = false;

                if (!empty($_FILES['image']['name'])) {
                    if ((int) $_FILES['image']['error'] !== 0 || !is_uploaded_file($_FILES['image']['tmp_name']) || !UTIL_File::validateImage($_FILES['image']['name'])) {
                        $imageValid = false;
                        OW::getFeedback()->error($language->text('base', 'not_valid_image'));
                    } else {
                        $imagePosted = true;
                    }
                }

                if (empty($endStamp)) {
                    $endStamp = strtotime("+1 day", $startStamp);
                    $endStamp = mktime(0, 0, 0, date('n', $endStamp), date('j', $endStamp), date('Y', $endStamp));
                }

                if (!empty($endStamp) && $endStamp < $startStamp) {
                    $datesAreValid = false;
                    OW::getFeedback()->error($language->text('eventx', 'add_form_invalid_end_date_error_message'));
                }

                if ($imageValid && $datesAreValid) {
                    $event = new EVENTX_BOL_Event();
                    $event->setStartTimeStamp($startStamp);
                    $event->setEndTimeStamp($endStamp);
                    $event->setCreateTimeStamp(time());
                    $event->setTitle(htmlspecialchars($data['title']));
                    $event->setLocation(UTIL_HtmlTag::autoLink(strip_tags($data['location'])));
                    $event->setWhoCanView((int) $data['who_can_view']);
                    $event->setWhoCanInvite((int) $data['who_can_invite']);
                    $event->setDescription($data['desc']);
                    $event->setUserId(OW::getUser()->getId());
                    $event->setEndDateFlag(!empty($_POST['endDateFlag']));
                    $event->setStartTimeDisable($data['start_time'] == 'all_day');
                    $event->setEndTimeDisable($data['end_time'] == 'all_day');
                    $event->setMaxInvites($data['max_invites']);
                    $event->status = OW::getConfig()->getValue('eventx', 'itemApproval') == 'auto' ? 'approved' : 'pending';

                    if ($imagePosted) {
                        $event->setImage(uniqid());
                    }

                    $serviceEvent = new OW_Event(EVENTX_BOL_EventService::EVENTX_ON_CREATE_EVENT, array('eventDto' => $event));
                    OW::getEventManager()->trigger($serviceEvent);

                    $this->eventService->saveEvent($event);

                    if ($imagePosted) {
                        $this->eventService->saveEventImage($_FILES['image']['tmp_name'], $event->getImage());
                    }

                    $eventUser = new EVENTX_BOL_EventUser();
                    $eventUser->setEventId($event->getId());
                    $eventUser->setUserId(OW::getUser()->getId());
                    $eventUser->setTimeStamp(time());
                    $eventUser->setStatus(EVENTX_BOL_EventService::USER_STATUS_YES);
                    $this->eventService->saveEventUser($eventUser);

                    $eventCategory = isset($data['event_category']) ? $data['event_category'] : 1;
                    $this->eventService->setItemCategories($event->getId(), $eventCategory);

                    if (isset($data['tags'])) {
                        $tags = array();

                        $tags = $data['tags'];
                        foreach ($tags as $id => $tag) {
                            $tags[$id] = UTIL_HtmlTag::stripTags($tag);
                        }

                        BOL_TagService::getInstance()->updateEntityTags($event->id, 'eventx', $tags);
                    }

                    OW::getFeedback()->info($language->text('eventx', 'add_form_success_message'));

                    if ($event->getWhoCanView() == EVENTX_BOL_EventService::CAN_VIEW_ANYBODY) {
                        $eventObj = new OW_Event('feed.action', array(
                            'pluginKey' => 'eventx',
                            'entityType' => 'eventx',
                            'entityId' => $event->getId(),
                            'userId' => $event->getUserId()
                        ));
                        OW::getEventManager()->trigger($eventObj);
                    }

                    if ($credits === true) {
                        OW::getEventManager()->call('usercredits.track_action', $eventParams);
                    }

                    $serviceEvent = new OW_Event(EVENTX_BOL_EventService::EVENTX_AFTER_CREATE_EVENT, array('eventDto' => $event));
                    OW::getEventManager()->trigger($serviceEvent);

                    $this->redirect(OW::getRouter()->urlForRoute('eventx.view', array('eventId' => $event->getId())));
                }
            }
        }

        if (empty($_POST['endDateFlag'])) {
            $form->getElement('end_date')->addAttribute('disabled', 'disabled');
            $form->getElement('end_date')->addAttribute('style', 'display:none;');

            $form->getElement('end_time')->addAttribute('disabled', 'disabled');
            $form->getElement('end_time')->addAttribute('style', 'display:none;');
        }

        $this->addForm($form);
    }

    public function listCategoryItems($params) {
        $modPermissions = OW::getUser()->isAuthorized('eventx');

        if (!OW::getUser()->isAuthorized('eventx', 'view_event') && !$modPermissions) {
            $this->setTemplate(OW::getPluginManager()->getPlugin('base')->getCtrlViewDir() . 'authorization_failed.html');
            return;
        }

        $category = !empty($params['category']) ? trim(htmlspecialchars(urldecode($params['category']))) : '';
        $this->assign('category', $category);

        $page = ( empty($_GET['page']) || (int) $_GET['page'] < 0 ) ? 1 : (int) $_GET['page'];
        $pageCount = (int) OW::getConfig()->getValue('eventx', 'resultsPerPage');
        $events = EVENTX_BOL_EventService::getInstance()->findCategoryEvents($category, $page, $pageCount);
        $eventsCount = EVENTX_BOL_EventService::getInstance()->findCategoryEventsCount($category);

        $this->addComponent('paging', new BASE_CMP_Paging($page, ceil($eventsCount / $pageCount), 5));
        $this->assign('page', $page);
        $toolbarList = array();
        $this->assign('events', EVENTX_BOL_EventService::getInstance()->getListingDataWithToolbar($events, $toolbarList));

        $contentMenu = $this->eventService->getContentMenu();
        $this->addComponent('contentMenu', $contentMenu);
        if (OW::getConfig()->getValue('eventx', 'enableCategoryList') == '1') {
            $contentMenu->getElement('categories')->setActive(true);
        }

        $this->setPageTitle(OW::getLanguage()->text('eventx', 'meta_title_item_category', array('category' => $category)));
        $this->setPageHeading(OW::getLanguage()->text('eventx', 'meta_description_item_category', array('category' => $category)));
    }

    public function listCategory() {
        $modPermissions = OW::getUser()->isAuthorized('eventx');

        if (!OW::getUser()->isAuthorized('eventx', 'view_event') && !$modPermissions) {
            $this->setTemplate(OW::getPluginManager()->getPlugin('base')->getCtrlViewDir() . 'authorization_failed.html');
            return;
        }

        $contentMenu = $this->eventService->getContentMenu();
        $contentMenu->getElement('categories')->setActive(true);
        $this->addComponent('contentMenu', $contentMenu);

        $this->setPageTitle(OW::getLanguage()->text('eventx', 'meta_title_item_categories'));
        $this->setPageHeading(OW::getLanguage()->text('eventx', 'meta_description_item_categories'));

        $categories = $this->eventService->getAllItemCategories(1, 200);
        $details = array();

        foreach ($categories as $category) {
            $id = $category['categoryId'];
            $details[$id]['id'] = $id;
            $details[$id]['name'] = $category['name'];
            $details[$id]['description'] = $category['description'];
            $details[$id]['count'] = $category['count'];
            $catValue = trim(htmlspecialchars($category['name']));

            $details[$id]['url'] = OW::getRouter()->urlForRoute('eventx_category_items', array('category' => $catValue));
        }
        $this->assign('details', $details);

        $enable3DTagCloud = OW::getConfig()->getValue('eventx', 'enable3DTagCloud');
        $this->assign('enable3DTagCloud', $enable3DTagCloud);

        if ($enable3DTagCloud == '1') {
            OW::getDocument()->addScript(OW::getPluginManager()->getPlugin('eventx')->getStaticJsUrl() . 'tagcanvas.min.js');
        }
    }

    private function getEventForParams($params) {
        if (empty($params['eventId'])) {
            throw new Redirect404Exception();
        }

        $event = $this->eventService->findEvent($params['eventId']);

        if ($event === null) {
            throw new Redirect404Exception();
        }

        return $event;
    }

    public function edit($params) {
        $event = $this->getEventForParams($params);
        $language = OW::getLanguage();

        $modPermissions = OW::getUser()->isAuthorized('eventx');
        $ownerMode = $event->getUserId() == OW::getUser()->getId();

        if (!$ownerMode && !$modPermissions) {
            $this->setTemplate(OW::getPluginManager()->getPlugin('base')->getCtrlViewDir() . 'authorization_failed.html');
            return;
        }

        $form = new EventAddForm('event_edit');

        $form->getElement('title')->setValue($event->getTitle());
        $form->getElement('desc')->setValue($event->getDescription());
        $form->getElement('location')->setValue($event->getLocation());
        $form->getElement('who_can_view')->setValue($event->getWhoCanView());
        $form->getElement('who_can_invite')->setValue($event->getWhoCanInvite());
        $form->getElement('who_can_invite')->setValue($event->getWhoCanInvite());
        $form->getElement('max_invites')->setValue($event->getMaxInvites());

        if (OW::getConfig()->getValue('eventx', 'enableCategoryList') == '1') {
            $catIds = $this->eventService->getItemCategoryId($event->id);

            $eventCategories = array();

            foreach ($catIds as $categoryObj) {
                $eventCategories[] = $categoryObj->categoryId;
            }

            if (OW::getConfig()->getValue('eventx', 'enableMultiCategories') == 1) {
                $form->getElement('event_category')->setValue($eventCategories);
            } else {
                $form->getElement('event_category')->setValue($eventCategories[0]);
            }
        }

        if (OW::getConfig()->getValue('eventx', 'enableTagsList') == '1') {
            $entityTags = BOL_TagService::getInstance()->findEntityTags($event->getId(), 'eventx');
            if ($entityTags) {
                $tags = array();
                foreach ($entityTags as $entityTag) {
                    $tags[] = $entityTag->getLabel();
                }
                $form->getElement('tags')->setValue($tags);
            }
        }

        $startTimeArray = array('hour' => date('G', $event->getStartTimeStamp()), 'minute' => date('i', $event->getStartTimeStamp()));
        $form->getElement('start_time')->setValue($startTimeArray);

        $startDate = date('Y', $event->getStartTimeStamp()) . '/' . date('n', $event->getStartTimeStamp()) . '/' . date('j', $event->getStartTimeStamp());
        $form->getElement('start_date')->setValue($startDate);

        if ($event->getEndTimeStamp() !== null) {
            $endTimeArray = array('hour' => date('G', $event->getEndTimeStamp()), 'minute' => date('i', $event->getEndTimeStamp()));
            $form->getElement('end_time')->setValue($endTimeArray);


            $endTimeStamp = $event->getEndTimeStamp();
            if ($event->getEndTimeDisable()) {
                $endTimeStamp = strtotime("-1 day", $endTimeStamp);
            }

            $endDate = date('Y', $endTimeStamp) . '/' . date('n', $endTimeStamp) . '/' . date('j', $endTimeStamp);
            $form->getElement('end_date')->setValue($endDate);
        }

        if ($event->getStartTimeDisable()) {
            $form->getElement('start_time')->setValue('all_day');
        }

        if ($event->getEndTimeDisable()) {
            $form->getElement('end_time')->setValue('all_day');
        }

        $form->getSubmitElement('submit')->setValue(OW::getLanguage()->text('eventx', 'edit_form_submit_label'));

        $checkboxId = UTIL_HtmlTag::generateAutoId('chk');
        $tdId = UTIL_HtmlTag::generateAutoId('td');
        $this->assign('tdId', $tdId);
        $this->assign('chId', $checkboxId);

        OW::getDocument()->addScript(OW::getPluginManager()->getPlugin("eventx")->getStaticJsUrl() . 'eventx.js');

        $enableMapSuggestion = OW::getConfig()->getValue('eventx', 'enableMapSuggestion');
        if ($enableMapSuggestion == '1') {
            OW::getDocument()->addScript("http://maps.googleapis.com/maps/api/js?sensor=false&amp;libraries=places");
            OW::getDocument()->addScript(OW::getPluginManager()->getPlugin("eventx")->getStaticJsUrl() . 'jquery.geocomplete.min.js');
        }

        $this->assign('enableMapSuggestion', $enableMapSuggestion);

        OW::getDocument()->addOnloadScript("new eventAddForm(" . json_encode(array('checkbox_id' => $checkboxId, 'end_date_id' => $form->getElement('end_date')->getId(), 'tdId' => $tdId)) . ")");

        if ($event->getImage()) {
            $this->assign('imgsrc', $this->eventService->generateImageUrl($event->getImage(), true));
        }

        $endDateFlag = $event->getEndDateFlag();

        if (OW::getRequest()->isPost()) {
            $endDateFlag = !empty($_POST['endDateFlag']);

            if ($form->isValid($_POST)) {
                $data = $form->getValues();

                $serviceEvent = new OW_Event(EVENTX_BOL_EventService::EVENTX_BEFORE_EVENTX_EDIT, array('eventId' => $event->id), $data);
                OW::getEventManager()->trigger($serviceEvent);
                $data = $serviceEvent->getData();

                $dateArray = explode('/', $data['start_date']);

                $startStamp = mktime(0, 0, 0, $dateArray[1], $dateArray[2], $dateArray[0]);

                if ($data['start_time'] != 'all_day') {
                    $startStamp = mktime($data['start_time']['hour'], $data['start_time']['minute'], 0, $dateArray[1], $dateArray[2], $dateArray[0]);
                }

                if (!empty($_POST['endDateFlag']) && !empty($data['end_date'])) {
                    $dateArray = explode('/', $data['end_date']);
                    $endStamp = mktime(0, 0, 0, $dateArray[1], $dateArray[2], $dateArray[0]);
                    $endStamp = strtotime("+1 day", $endStamp);

                    if ($data['end_time'] != 'all_day') {
                        $hour = 0;
                        $min = 0;

                        if ($data['end_time'] != 'all_day') {
                            $hour = $data['end_time']['hour'];
                            $min = $data['end_time']['minute'];
                        }

                        $dateArray = explode('/', $data['end_date']);
                        $endStamp = mktime($hour, $min, 0, $dateArray[1], $dateArray[2], $dateArray[0]);
                    }
                }

                $event->setStartTimeStamp($startStamp);

                if (empty($endStamp)) {
                    $endStamp = strtotime("+1 day", $startStamp);
                    $endStamp = mktime(0, 0, 0, date('n', $endStamp), date('j', $endStamp), date('Y', $endStamp));
                }

                if ($startStamp > $endStamp) {
                    OW::getFeedback()->error($language->text('eventx', 'add_form_invalid_end_date_error_message'));
                    $this->redirect();
                } else {
                    $event->setEndTimeStamp($endStamp);

                    if (!empty($_FILES['image']['name'])) {
                        if ((int) $_FILES['image']['error'] !== 0 || !is_uploaded_file($_FILES['image']['tmp_name']) || !UTIL_File::validateImage($_FILES['image']['name'])) {
                            OW::getFeedback()->error($language->text('base', 'not_valid_image'));
                            $this->redirect();
                        } else {
                            $event->setImage(uniqid());
                            $this->eventService->saveEventImage($_FILES['image']['tmp_name'], $event->getImage());
                        }
                    }

                    $event->setTitle(htmlspecialchars($data['title']));
                    $event->setLocation(UTIL_HtmlTag::autoLink(strip_tags($data['location'])));
                    $event->setWhoCanView((int) $data['who_can_view']);
                    $event->setWhoCanInvite((int) $data['who_can_invite']);
                    $event->setDescription($data['desc']);
                    $event->setEndDateFlag(!empty($_POST['endDateFlag']));
                    $event->setStartTimeDisable($data['start_time'] == 'all_day');
                    $event->setEndTimeDisable($data['end_time'] == 'all_day');
                    $event->setMaxInvites($data['max_invites']);

                    $this->eventService->saveEvent($event);

                    $eventCategory = isset($data['event_category']) ? $data['event_category'] : 1;
                    $this->eventService->setItemCategories($event->getId(), $eventCategory);

                    if (isset($data['tags'])) {
                        $tags = array();

                        $tags = $data['tags'];
                        foreach ($tags as $id => $tag) {
                            $tags[$id] = UTIL_HtmlTag::stripTags($tag);
                        }

                        BOL_TagService::getInstance()->updateEntityTags($event->id, 'eventx', $tags);
                    }

                    $e = new OW_Event(EVENTX_BOL_EventService::EVENTX_AFTER_EVENTX_EDIT, array('eventId' => $event->id));
                    OW::getEventManager()->trigger($e);

                    OW::getFeedback()->info($language->text('eventx', 'edit_form_success_message'));
                    $this->redirect(OW::getRouter()->urlForRoute('eventx.view', array('eventId' => $event->getId())));
                }
            }
        }

        if (!$endDateFlag) {
            $form->getElement('end_date')->addAttribute('disabled', 'disabled');
            $form->getElement('end_date')->addAttribute('style', 'display:none;');

            $form->getElement('end_time')->addAttribute('disabled', 'disabled');
            $form->getElement('end_time')->addAttribute('style', 'display:none;');
        }

        $this->assign('endDateFlag', $endDateFlag);

        $this->setPageHeading($language->text('eventx', 'edit_page_heading'));
        $this->setPageTitle($language->text('eventx', 'edit_page_title'));

        $this->addForm($form);
    }

    public function delete($params) {
        $event = $this->getEventForParams($params);

        if (!OW::getUser()->isAuthenticated() || ( OW::getUser()->getId() != $event->getUserId() && !OW::getUser()->isAuthorized('eventx') )) {
            throw new Redirect403Exception();
        }

        $this->eventService->deleteEvent($event->getId());
        OW::getFeedback()->info(OW::getLanguage()->text('eventx', 'delete_success_message'));
        $this->redirect(OW::getRouter()->urlForRoute('eventx.main_menu_route'));
    }

    public function view($params) {
        $event = $this->getEventForParams($params);

        $cmpId = UTIL_HtmlTag::generateAutoId('cmp');

        $this->assign('contId', $cmpId);

        $language = OW::getLanguage();

        if (!OW::getUser()->isAuthorized('eventx', 'view_event') && $event->getUserId() != OW::getUser()->getId()) {
            $this->assign('authErrorText', OW::getLanguage()->text('eventx', 'event_view_permission_error_message'));
            return;
        }

        // guest gan't view private events
        if ((int) $event->getWhoCanView() === EVENTX_BOL_EventService::CAN_VIEW_INVITATION_ONLY && !OW::getUser()->isAuthenticated()) {
            $this->redirect(OW::getRouter()->urlForRoute('eventx.private_event', array('eventId' => $event->getId())));
        }

        $eventInvite = $this->eventService->findEventInvite($event->getId(), OW::getUser()->getId());
        $eventUser = $this->eventService->findEventUser($event->getId(), OW::getUser()->getId());

        // check if user can view event
        if ((int) $event->getWhoCanView() === EVENTX_BOL_EventService::CAN_VIEW_INVITATION_ONLY && $eventUser === null && $eventInvite === null && !OW::getUser()->isAuthorized('eventx')) {
            $this->redirect(OW::getRouter()->urlForRoute('eventx.private_event', array('eventId' => $event->getId())));
        }

        $modPermissions = OW::getUser()->isAuthorized('eventx');
        $ownerMode = $event->getUserId() == OW::getUser()->getId();
        $whoCanDeleteEvent = explode(",", OW::getConfig()->getValue('eventx', 'eventDelete'));

        $toolbar = array();

        if (OW::getUser()->isAuthenticated()) {
            array_push($toolbar, array(
                'href' => 'javascript://',
                'id' => 'btn-eventx-flag',
                'label' => OW::getLanguage()->text('base', 'flag')
            ));
        }

        if ($ownerMode || $modPermissions) {
            array_push($toolbar, array(
                'href' => OW::getRouter()->urlForRoute('eventx.edit', array('eventId' => $event->getId())),
                'label' => OW::getLanguage()->text('eventx', 'edit_button_label')
            ));
        }

        if ($modPermissions) {

            if ($event->status == 'approved') {
                array_push($toolbar, array(
                    'href' => 'javascript://',
                    'id' => 'eventx-set-approval-staus',
                    'rel' => 'disapprove',
                    'label' => $language->text('base', 'disapprove')
                ));
            } else {
                array_push($toolbar, array(
                    'href' => 'javascript://',
                    'id' => 'eventx-set-approval-staus',
                    'rel' => 'approve',
                    'label' => $language->text('base', 'approve')
                ));
            }
        }

        $canDelete = FALSE;

        if ($ownerMode && in_array(3, $whoCanDeleteEvent)) {
            $canDelete = TRUE;
        }

        if (OW::getUser()->isAuthorized('eventx') && in_array(2, $whoCanDeleteEvent)) {
            $canDelete = TRUE;
        }

        if (OW::getUser()->isAdmin() && in_array(1, $whoCanDeleteEvent)) {
            $canDelete = TRUE;
        }

        if ($canDelete) {
            array_push($toolbar, array(
                'href' => 'javascript://',
                'id' => 'eventx-delete',
                'label' => OW::getLanguage()->text('eventx', 'delete_button_label')
            ));
        }

        $this->assign('toolbar', $toolbar);

        OW::getNavigation()->activateMenuItem(OW_Navigation::MAIN, 'eventx', 'main_menu_item');
        $this->setPageHeading($event->getTitle());
        $this->setPageTitle(OW::getLanguage()->text('eventx', 'event_view_page_heading', array('event_title' => $event->getTitle())));
        $this->setPageHeadingIconClass('ow_ic_calendar');
        OW::getDocument()->setDescription(UTIL_String::truncate(strip_tags($event->getDescription()), 200, '...'));

        $maxInvites = $event->getMaxInvites();
        $currentInvites = $this->eventService->findEventUsersCount($event->getId(), EVENTX_BOL_EventService::USER_STATUS_YES);

        $isFullyBooked = $currentInvites >= $maxInvites && $maxInvites > 0;

        $infoArray = array(
            'id' => $event->getId(),
            'image' => ( $event->getImage() ? $this->eventService->generateImageUrl($event->getImage(), false) : null ),
            'date' => $this->eventService->formatSimpleDate($event->getStartTimeStamp(), $event->getStartTimeDisable()),
            'endDate' => $event->getEndTimeStamp() === null || !$event->getEndDateFlag() ? null : $this->eventService->formatSimpleDate($event->getEndTimeDisable() ? strtotime("-1 day", $event->getEndTimeStamp()) : $event->getEndTimeStamp(), $event->getEndTimeDisable()),
            'location' => $event->getLocation(),
            'desc' => UTIL_HtmlTag::autoLink($event->getDescription()),
            'title' => $event->getTitle(),
            'maxInvites' => $maxInvites,
            'currentInvites' => $currentInvites,
            'availableInvites' => $maxInvites - $currentInvites,
            'creatorName' => BOL_UserService::getInstance()->getDisplayName($event->getUserId()),
            'creatorLink' => BOL_UserService::getInstance()->getUserUrl($event->getUserId())
        );

        $this->assign('info', $infoArray);

        // event attend form
        if (OW::getUser()->isAuthenticated() && $event->getEndTimeStamp() > time()) {
            if ($eventUser !== null) {
                $this->assign('currentStatus', OW::getLanguage()->text('eventx', 'user_status_label_' . $eventUser->getStatus()));
            }
            $this->addForm(new AttendForm($event->getId(), $cmpId));

            $onloadJs = "
                var \$context = $('#" . $cmpId . "');";

            $onloadJs .=" $('#event_attend_yes_btn').click(
                    function(){
                        $('input[name=attend_status]', \$context).val(" . EVENTX_BOL_EventService::USER_STATUS_YES . ");
                    }
                );
                
                $('#event_attend_maybe_btn').click(
                    function(){
                        $('input[name=attend_status]', \$context).val(" . EVENTX_BOL_EventService::USER_STATUS_MAYBE . ");
                    }
                );
                $('#event_attend_no_btn').click(
                    function(){
                        $('input[name=attend_status]', \$context).val(" . EVENTX_BOL_EventService::USER_STATUS_NO . ");
                    }
                );

                $('.current_status a', \$context).click(
                    function(){
                        $('.attend_buttons .buttons', \$context).fadeIn(500);
                    }
                );
            ";

            OW::getDocument()->addOnloadScript($onloadJs);
        } else {
            $this->assign('no_attend_form', true);
        }

        if ($event->getEndTimeStamp() > time() && ((int) $event->getUserId() === OW::getUser()->getId() || ( (int) $event->getWhoCanInvite() === EVENTX_BOL_EventService::CAN_INVITE_PARTICIPANT && $eventUser !== null) )) {
            $params = array(
                $event->id
            );

            $this->assign('inviteLink', true);
            OW::getDocument()->addOnloadScript("
                var eventFloatBox;
                $('#inviteLink', $('#" . $cmpId . "')).click(
                    function(){
                        eventFloatBox = OW.ajaxFloatBox('EVENTX_CMP_InviteUserListSelect', " . json_encode($params) . ", {width:600, height:400, iconClass: 'ow_ic_user', title: '" . OW::getLanguage()->text('eventx', 'friends_invite_button_label') . "'});
                    }
                );
                OW.bind('base.avatar_user_list_select',
                    function(list){
                        eventFloatBox.close();
                        $.ajax({
                            type: 'POST',
                            url: " . json_encode(OW::getRouter()->urlFor('EVENTX_CTRL_Base', 'inviteResponder')) . ",
                            data: 'eventId=" . json_encode($event->getId()) . "&userIdList='+JSON.stringify(list),
                            dataType: 'json',
                            success : function(data){
                                if( data.messageType == 'error' ){
                                    OW.error(data.message);
                                }
                                else{
                                    OW.info(data.message);
                                }
                            },
                            error : function( XMLHttpRequest, textStatus, errorThrown ){
                                OW.error(textStatus);
                            }
                        });
                    }
                );
            ");
        }

        $cmntParams = new BASE_CommentsParams('eventx', 'eventx');
        $cmntParams->setEntityId($event->getId());
        $cmntParams->setOwnerId($event->getUserId());
        $this->addComponent('comments', new BASE_CMP_Comments($cmntParams));
        $this->addComponent('userListCmp', new EVENTX_CMP_EventUsers($event->getId()));

        $tagCloud = new BASE_CMP_EntityTagCloud('eventx');

        $tagCloud->setEntityId($event->id);
        $tagCloud->setRouteName('eventx_view_tagged_list');
        $this->addComponent('tagCloud', $tagCloud);

        OW::getDocument()->addScript(OW::getPluginManager()->getPlugin("eventx")->getStaticJsUrl() . 'eventx.js');
        OW::getDocument()->addScript("http://maps.google.com/maps/api/js?sensor=false");
        OW::getDocument()->addScript(OW::getPluginManager()->getPlugin("eventx")->getStaticJsUrl() . 'jquery.gmap.min.js');

        $objParams = array(
            'ajaxResponder' => $this->ajaxResponder,
            'id' => $event->getId(),
            'txtDelConfirm' => $language->text('eventx', 'confirm_delete'),
            'txtApprove' => $language->text('base', 'approve'),
            'txtDisapprove' => $language->text('base', 'disapprove')
        );

        $script = "$(document).ready(function(){
                   var item = new eventxItem( " . json_encode($objParams) . ");
                 });";

        OW::getDocument()->addOnloadScript($script);

        $js = UTIL_JsGenerator::newInstance()
                ->jQueryEvent('#btn-eventx-flag', 'click', 'OW.flagContent(e.data.entity, e.data.id, e.data.title, e.data.href, "eventx+flags");', array('e'), array('entity' => 'eventx_event', 'id' => $event->getId(), 'title' => $event->getTitle(), 'href' => OW::getRouter()->urlForRoute('eventx.view', array('eventId' => $event->getId()))
        ));

        OW::getDocument()->addOnloadScript($js, 1001);

        $categoryList = $this->eventService->getItemCategories($event->id);

        $i = 0;
        $categoryUrlList = array();

        foreach ($categoryList as $category) {
            $catName = $this->eventService->getCategoryName($category->categoryId);
            $categoryUrlList[$i]['id'] = $category->categoryId;
            $categoryUrlList[$i]['name'] = $catName;
            $categoryUrlList[$i]['url'] = OW::getRouter()->urlForRoute('eventx_category_items', array('category' => $catName));
            $i+=1;
        }
        $this->assign('categoryUrl', $categoryUrlList);
        $this->assign('mapWidth', OW::getConfig()->getValue('eventx', 'mapWidth'));
        $this->assign('mapHeight', OW::getConfig()->getValue('eventx', 'mapHeight'));
    }

    public function eventsList($params) {
        if (empty($params['list'])) {
            throw new Redirect404Exception();
        }

        $configs = $this->eventService->getConfigs();
        $page = ( empty($_GET['page']) || (int) $_GET['page'] < 0 ) ? 1 : (int) $_GET['page'];

        $language = OW::getLanguage();

        $toolbarList = array();

        switch (trim($params['list'])) {
            case 'created':
                if (!OW::getUser()->isAuthenticated()) {
                    throw new Redirect403Exception();
                }

                $this->setPageHeading($language->text('eventx', 'event_created_by_me_page_heading'));
                $this->setPageTitle($language->text('eventx', 'event_created_by_me_page_title'));
                $this->setPageHeadingIconClass('ow_ic_calendar');
                $events = $this->eventService->findUserEvents(OW::getUser()->getId(), $page);
                $eventsCount = $this->eventService->findLatestEventsCount();
                break;

            case 'joined':
                if (!OW::getUser()->isAuthenticated()) {
                    throw new Redirect403Exception();
                }
                $contentMenu = EVENTX_BOL_EventService::getInstance()->getContentMenu();
                $this->addComponent('contentMenu', $contentMenu);
                $this->setPageHeading($language->text('eventx', 'event_joined_by_me_page_heading'));
                $this->setPageTitle($language->text('eventx', 'event_joined_by_me_page_title'));
                $this->setPageHeadingIconClass('ow_ic_calendar');

                $events = $this->eventService->findUserParticipatedEvents(OW::getUser()->getId(), $page);
                $eventsCount = $this->eventService->findUserParticipatedEventsCount(OW::getUser()->getId());
                break;

            case 'latest':
                $contentMenu = EVENTX_BOL_EventService::getInstance()->getContentMenu();
                $contentMenu->getElement('latest')->setActive(true);
                $this->addComponent('contentMenu', $contentMenu);
                $this->setPageHeading($language->text('eventx', 'latest_events_page_heading'));
                $this->setPageTitle($language->text('eventx', 'latest_events_page_title'));
                $this->setPageHeadingIconClass('ow_ic_calendar');
                OW::getDocument()->setDescription($language->text('eventx', 'latest_events_page_desc'));
                $events = $this->eventService->findPublicEvents($page);
                $eventsCount = $this->eventService->findPublicEventsCount();
                break;

            case 'user-participated-events':

                if (empty($_GET['userId'])) {
                    throw new Redirect404Exception();
                }

                $user = BOL_UserService::getInstance()->findUserById($_GET['userId']);

                if ($user === null) {
                    throw new Redirect404Exception();
                }

                $eventParams = array(
                    'action' => 'event_view_attend_events',
                    'ownerId' => $user->getId(),
                    'viewerId' => OW::getUser()->getId()
                );

                OW::getEventManager()->getInstance()->call('privacy_check_permission', $eventParams);

                $displayName = BOL_UserService::getInstance()->getDisplayName($user->getId());

                $this->setPageHeading($language->text('eventx', 'user_participated_events_page_heading', array('display_name' => $displayName)));
                $this->setPageTitle($language->text('eventx', 'user_participated_events_page_title', array('display_name' => $displayName)));
                OW::getDocument()->setDescription($language->text('eventx', 'user_participated_events_page_desc', array('display_name' => $displayName)));
                $this->setPageHeadingIconClass('ow_ic_calendar');
                $events = $this->eventService->findUserParticipatedPublicEvents($user->getId(), $page);
                $eventsCount = $this->eventService->findUserParticipatedPublicEventsCount($user->getId());
                break;

            case 'past':
                $contentMenu = EVENTX_BOL_EventService::getInstance()->getContentMenu();
                $this->addComponent('contentMenu', $contentMenu);
                $this->setPageHeading($language->text('eventx', 'past_events_page_heading'));
                $this->setPageTitle($language->text('eventx', 'past_events_page_title'));
                $this->setPageHeadingIconClass('ow_ic_calendar');
                OW::getDocument()->setDescription($language->text('eventx', 'past_events_page_desc'));
                $events = $this->eventService->findPublicEvents($page, null, true);
                $eventsCount = $this->eventService->findPublicEventsCount(true);
                break;

            case 'invited':
                if (!OW::getUser()->isAuthenticated()) {
                    throw new Redirect403Exception();
                }

                $this->eventService->hideInvitationByUserId(OW::getUser()->getId());

                $contentMenu = EVENTX_BOL_EventService::getInstance()->getContentMenu();
                $this->addComponent('contentMenu', $contentMenu);
                $this->setPageHeading($language->text('eventx', 'invited_events_page_heading'));
                $this->setPageTitle($language->text('eventx', 'invited_events_page_title'));
                $this->setPageHeadingIconClass('ow_ic_calendar');
                $events = $this->eventService->findUserInvitedEvents(OW::getUser()->getId(), $page);
                $eventsCount = $this->eventService->findUserInvitedEventsCount(OW::getUser()->getId());

                foreach ($events as $event) {
                    $toolbarList[$event->getId()] = array();

                    $paramsList = array('eventId' => $event->getId(), 'page' => $page, 'list' => trim($params['list']));

                    $acceptUrl = OW::getRequest()->buildUrlQueryString(OW::getRouter()->urlForRoute('eventx.invite_accept', $paramsList), array('page' => $page));
                    $ignoreUrl = OW::getRequest()->buildUrlQueryString(OW::getRouter()->urlForRoute('eventx.invite_decline', $paramsList), array('page' => $page));

                    $toolbarList[$event->getId()][] = array('label' => $language->text('eventx', 'accept_request'), 'href' => $acceptUrl);
                    $toolbarList[$event->getId()][] = array('label' => $language->text('eventx', 'ignore_request'), 'href' => $ignoreUrl);
                }

                break;

            default:
                throw new Redirect404Exception();
        }

        $this->addComponent('paging', new BASE_CMP_Paging($page, ceil($eventsCount / $configs[EVENTX_BOL_EventService::CONF_EVENTS_COUNT_ON_PAGE]), 5));

        if (!OW::getUser()->isAuthorized('eventx', 'add_event')) {
            $this->assign('noButton', true);
        }

        if (empty($events)) {
            $this->assign('no_events', true);
        }

        $this->assign('listType', trim($params['list']));
        $this->assign('page', $page);
        $this->assign('events', $this->eventService->getListingDataWithToolbar($events, $toolbarList));
        $this->assign('toolbarList', $toolbarList);
        OW::getNavigation()->activateMenuItem(OW_Navigation::MAIN, 'eventx', 'main_menu_item');
    }

    public function inviteListAccept($params) {
        if (!OW::getUser()->isAuthenticated()) {
            throw new Redirect404Exception();
        }

        $userId = OW::getUser()->getId();
        $feedback = array('messageType' => 'error');
        $exit = false;
        $attendedStatus = 1;

        if (!empty($attendedStatus) && !empty($params['eventId']) && $this->eventService->canUserView($params['eventId'], $userId)) {
            $event = $this->eventService->findEvent($params['eventId']);

            if ($event->getEndTimeStamp() < time()) {
                throw new Redirect404Exception();
            }

            $eventUser = $this->eventService->findEventUser($params['eventId'], $userId);

            if ($eventUser !== null && (int) $eventUser->getStatus() === (int) $attendedStatus) {
                $feedback['message'] = OW::getLanguage()->text('eventx', 'user_status_not_changed_error');
            }

            if ($event->getUserId() == OW::getUser()->getId() && (int) $attendedStatus == EVENTX_BOL_EventService::USER_STATUS_NO) {
                $feedback['message'] = OW::getLanguage()->text('eventx', 'user_status_author_cant_leave_error');
            }

            if (!$exit) {
                if ($eventUser === null) {
                    $eventUser = new EVENTX_BOL_EventUser();
                    $eventUser->setUserId($userId);
                    $eventUser->setEventId((int) $params['eventId']);
                }

                $eventUser->setStatus((int) $attendedStatus);
                $eventUser->setTimeStamp(time());
                $this->eventService->saveEventUser($eventUser);
                $this->eventService->deleteUserEventInvites((int) $params['eventId'], OW::getUser()->getId());

                $feedback['message'] = OW::getLanguage()->text('eventx', 'user_status_updated');
                $feedback['messageType'] = 'info';

                if ($eventUser->getStatus() == EVENTX_BOL_EventService::USER_STATUS_YES && $event->getWhoCanView() == EVENTX_BOL_EventService::CAN_VIEW_ANYBODY) {
                    $userName = BOL_UserService::getInstance()->getDisplayName($event->getUserId());
                    $userUrl = BOL_UserService::getInstance()->getUserUrl($event->getUserId());
                    $userEmbed = '<a href="' . $userUrl . '">' . $userName . '</a>';

                    OW::getEventManager()->trigger(new OW_Event('feed.activity', array(
                        'activityType' => 'event-join',
                        'activityId' => $eventUser->getId(),
                        'entityId' => $event->getId(),
                        'entityType' => 'eventx',
                        'userId' => $eventUser->getUserId(),
                        'pluginKey' => 'eventx'
                            ), array(
                        'eventId' => $event->getId(),
                        'userId' => $eventUser->getUserId(),
                        'eventUserId' => $eventUser->getId(),
                        'string' => OW::getLanguage()->text('eventx', 'feed_actiovity_attend_string', array('user' => $userEmbed)),
                        'feature' => array()
                    )));
                }
            }
        } else {
            $feedback['message'] = OW::getLanguage()->text('eventx', 'user_status_update_error');
        }

        if (!empty($feedback['message'])) {
            switch ($feedback['messageType']) {
                case 'info':
                    OW::getFeedback()->info($feedback['message']);
                    break;
                case 'warning':
                    OW::getFeedback()->warning($feedback['message']);
                    break;
                case 'error':
                    OW::getFeedback()->error($feedback['message']);
                    break;
            }
        }

        $paramsList = array();

        if (!empty($params['page'])) {
            $paramsList['page'] = $params['page'];
        }

        if (!empty($params['list'])) {
            $paramsList['list'] = $params['list'];
        }

        $this->redirect(OW::getRouter()->urlForRoute('eventx.view_event_list', $paramsList));
    }

    public function inviteListDecline($params) {
        if (!empty($params['eventId'])) {
            $this->eventService->deleteUserEventInvites((int) $params['eventId'], OW::getUser()->getId());
            OW::getLanguage()->text('eventx', 'user_status_updated');
        } else {
            OW::getLanguage()->text('eventx', 'user_status_update_error');
        }

        if (!empty($params['page'])) {
            $paramsList['page'] = $params['page'];
        }

        if (!empty($params['list'])) {
            $paramsList['list'] = $params['list'];
        }

        $this->redirect(OW::getRouter()->urlForRoute('eventx.view_event_list', $paramsList));
    }

    public function eventUserLists($params) {
        if (empty($params['eventId']) || empty($params['list'])) {
            throw new Redirect404Exception();
        }

        $event = $this->eventService->findEvent((int) $params['eventId']);

        if ($event === null) {
            throw new Redirect404Exception();
        }

        $listArray = array_flip($this->eventService->getUserListsArray());

        if (!array_key_exists($params['list'], $listArray)) {
            throw new Redirect404Exception();
        }

        if (!OW::getUser()->isAuthorized('eventx', 'view_event') && $event->getUserId() != OW::getUser()->getId() && !OW::getUser()->isAuthorized('eventx')) {
            $this->assign('authErrorText', OW::getLanguage()->text('eventx', 'event_view_permission_error_message'));
            return;
        }

// guest gan't view private events
        if ((int) $event->getWhoCanView() === EVENTX_BOL_EventService::CAN_VIEW_INVITATION_ONLY && !OW::getUser()->isAuthenticated()) {
            $this->redirect(OW::getRouter()->urlForRoute('eventx.private_event', array('eventId' => $event->getId())));
        }

        $eventInvite = $this->eventService->findEventInvite($event->getId(), OW::getUser()->getId());
        $eventUser = $this->eventService->findEventUser($event->getId(), OW::getUser()->getId());

// check if user can view event
        if ((int) $event->getWhoCanView() === EVENTX_BOL_EventService::CAN_VIEW_INVITATION_ONLY && $eventUser === null && $eventInvite === null && !OW::getUser()->isAuthorized('eventx')) {
            $this->redirect(OW::getRouter()->urlForRoute('eventx.private_event', array('eventId' => $event->getId())));
        }

        $language = OW::getLanguage();
        $configs = $this->eventService->getConfigs();
        $page = ( empty($_GET['page']) || (int) $_GET['page'] < 0 ) ? 1 : (int) $_GET['page'];
        $status = $listArray[$params['list']];
        $eventUsers = $this->eventService->findEventUsers($event->getId(), $status, $page);
        $eventUsersCount = $this->eventService->findEventUsersCount($event->getId(), $status);

        $userIdList = array();

        foreach ($eventUsers as $eventUser) {
            $userIdList[] = $eventUser->getUserId();
        }

        $userDtoList = BOL_UserService::getInstance()->findUserListByIdList($userIdList);

        $this->addComponent('users', new EVENTX_CMP_EventUsersList($userDtoList, $eventUsersCount, $configs[EVENTX_BOL_EventService::CONF_EVENTX_USERS_COUNT_ON_PAGE], true));

        $this->setPageHeading($language->text('eventx', 'user_list_page_heading_' . $status, array('eventTitle' => $event->getTitle())));
        $this->setPageTitle($language->text('eventx', 'user_list_page_heading_' . $status, array('eventTitle' => $event->getTitle())));
        OW::getDocument()->setDescription($language->text('eventx', 'user_list_page_desc_' . $status, array('eventTitle' => $event->getTitle())));

        OW::getNavigation()->activateMenuItem(OW_Navigation::MAIN, 'eventx', 'main_menu_item');
    }

    public function privateEvent($params) {
        $language = OW::getLanguage();

        $this->setPageTitle($language->text('eventx', 'private_page_title'));
        $this->setPageHeading($language->text('eventx', 'private_page_heading'));
        $this->setPageHeadingIconClass('ow_ic_lock');

        $eventId = $params['eventId'];
        $event = $this->eventService->findEvent((int) $eventId);

        $avatarList = BOL_AvatarService::getInstance()->getDataForUserAvatars(array($event->userId));
        $displayName = BOL_UserService::getInstance()->getDisplayName($event->userId);
        $userUrl = BOL_UserService::getInstance()->getUserUrl($event->userId);

        $this->assign('eventx', $event);
        $this->assign('avatar', $avatarList[$event->userId]);
        $this->assign('displayName', $displayName);
        $this->assign('userUrl', $userUrl);
        $this->assign('creator', $language->text('eventx', 'creator'));
    }

    public function attendFormResponder() {
        if (!OW::getRequest()->isAjax() || !OW::getUser()->isAuthenticated()) {
            throw new Redirect404Exception();
        }

        $userId = OW::getUser()->getId();
        $respondArray = array('messageType' => 'error');

        if (!empty($_POST['attend_status']) && in_array((int) $_POST['attend_status'], array(1, 2, 3)) && !empty($_POST['eventId']) && $this->eventService->canUserView($_POST['eventId'], $userId)) {
            $event = $this->eventService->findEvent($_POST['eventId']);

            if ($event->getEndTimeStamp() < time()) {
                throw new Redirect404Exception();
            }

            $eventUser = $this->eventService->findEventUser($_POST['eventId'], $userId);

            if ($eventUser !== null && (int) $eventUser->getStatus() == (int) $_POST['attend_status']) {
                $respondArray['message'] = OW::getLanguage()->text('eventx', 'user_status_not_changed_error');
                exit(json_encode($respondArray));
            }

            if ($event->getUserId() == OW::getUser()->getId() && (int) $_POST['attend_status'] == EVENTX_BOL_EventService::USER_STATUS_NO) {
                $respondArray['message'] = OW::getLanguage()->text('eventx', 'user_status_author_cant_leave_error');
                exit(json_encode($respondArray));
            }

            $maxInvites = $event->getMaxInvites();
            $currentInvites = $this->eventService->findEventUsersCount($event->getId(), EVENTX_BOL_EventService::USER_STATUS_YES);

            if ($currentInvites >= $maxInvites && $maxInvites > 0 && $eventUser->getStatus() > 1) {
                $respondArray['message'] = OW::getLanguage()->text('eventx', 'all_seats_full_error');
                exit(json_encode($respondArray));
            }

            if ($eventUser === null) {
                $eventUser = new EVENTX_BOL_EventUser();
                $eventUser->setUserId($userId);
                $eventUser->setEventId((int) $_POST['eventId']);
            }

            $eventUser->setStatus((int) $_POST['attend_status']);
            $eventUser->setTimeStamp(time());
            $this->eventService->saveEventUser($eventUser);

            $this->eventService->deleteUserEventInvites((int) $_POST['eventId'], OW::getUser()->getId());

            $e = new OW_Event(EVENTX_BOL_EventService::EVENTX_ON_CHANGE_USER_STATUS, array('eventId' => $event->id, 'userId' => $eventUser->userId));
            OW::getEventManager()->trigger($e);

            $respondArray['message'] = OW::getLanguage()->text('eventx', 'user_status_updated');
            $respondArray['messageType'] = 'info';
            $respondArray['currentLabel'] = OW::getLanguage()->text('eventx', 'user_status_label_' . $eventUser->getStatus());
            $respondArray['eventId'] = (int) $_POST['eventId'];
            $respondArray['newInvCount'] = $this->eventService->findUserInvitedEventsCount(OW::getUser()->getId());

            if ($eventUser->getStatus() == EVENTX_BOL_EventService::USER_STATUS_YES && $event->getWhoCanView() == EVENTX_BOL_EventService::CAN_VIEW_ANYBODY) {
                $userName = BOL_UserService::getInstance()->getDisplayName($event->getUserId());
                $userUrl = BOL_UserService::getInstance()->getUserUrl($event->getUserId());
                $userEmbed = '<a href="' . $userUrl . '">' . $userName . '</a>';

                OW::getEventManager()->trigger(new OW_Event('feed.activity', array(
                    'activityType' => 'event-join',
                    'activityId' => $eventUser->getId(),
                    'entityId' => $event->getId(),
                    'entityType' => 'eventx',
                    'userId' => $eventUser->getUserId(),
                    'pluginKey' => 'eventx'
                        ), array(
                    'eventId' => $event->getId(),
                    'userId' => $eventUser->getUserId(),
                    'eventUserId' => $eventUser->getId(),
                    'string' => OW::getLanguage()->text('eventx', 'feed_actiovity_attend_string', array('user' => $userEmbed)),
                    'feature' => array()
                )));
            }
        } else {
            $respondArray['message'] = OW::getLanguage()->text('eventx', 'user_status_update_error');
        }

        exit(json_encode($respondArray));
    }

    public function inviteResponder() {
        $respondArray = array();

        if (empty($_POST['eventId']) || empty($_POST['userIdList']) || !OW::getUser()->isAuthenticated()) {
            $respondArray['messageType'] = 'error';
            $respondArray['message'] = '_ERROR_';
            echo json_encode($respondArray);
            exit;
        }

        $idList = json_decode($_POST['userIdList']);

        if (empty($_POST['eventId']) || empty($idList)) {
            $respondArray['messageType'] = 'error';
            $respondArray['message'] = '_EMPTY_EVENTX_ID_';
            echo json_encode($respondArray);
            exit;
        }

        $event = $this->eventService->findEvent($_POST['eventId']);

        if ($event->getEndTimeStamp() < time()) {
            throw new Redirect404Exception();
        }

        if ($event === null) {
            $respondArray['messageType'] = 'error';
            $respondArray['message'] = '_EMPTY_EVENTX_';
            echo json_encode($respondArray);
            exit;
        }

        if ((int) $event->getUserId() === OW::getUser()->getId() || (int) $event->getWhoCanInvite() === EVENTX_BOL_EventService::CAN_INVITE_PARTICIPANT) {
            $count = 0;

            $userList = BOL_UserService::getInstance()->findUserListByIdList($idList);

            foreach ($userList as $user) {
                $userId = $user->id;
                $eventInvite = $this->eventService->findEventInvite($event->getId(), $userId);

                if ($eventInvite === null) {
                    $eventInvite = $this->eventService->inviteUser($event->getId(), $userId, OW::getUser()->getId());
                    $eventObj = new OW_Event('eventx.invite_user', array('userId' => $userId, 'inviterId' => OW::getUser()->getId(), 'eventId' => $event->getId(), 'imageId' => $event->getImage(), 'eventTitle' => $event->getTitle(), 'eventDesc' => $event->getDescription(), 'dispalyInvitation' => $eventInvite->displayInvitation));
                    OW::getEventManager()->trigger($eventObj);
                    $count++;
                }
            }
        }

        $respondArray['messageType'] = 'info';
        $respondArray['message'] = OW::getLanguage()->text('eventx', 'users_invite_success_message', array('count' => $count));

        exit(json_encode($respondArray));
    }

    public function ajaxResponder() {
        if (isset($_POST['ajaxFunc']) && OW::getRequest()->isAjax()) {
            $callFunc = (string) $_POST['ajaxFunc'];

            $result = call_user_func(array($this, $callFunc), $_POST);
        } else {
            throw new Redirect404Exception();
            exit;
        }

        exit(json_encode($result));
    }

    public function ajaxDeleteItem($params) {
        $eventId = $params['id'];
        $event = $this->eventService->findEvent($eventId);

        $isOwner = $event->getUserId() == OW::getUser()->getId();
        $isModerator = OW::getUser()->isAuthorized('eventx');

        if (!$isOwner && !$isModerator) {
            throw new Redirect404Exception();
            return;
        }

        $delResult = $this->eventService->deleteEvent($eventId);

        if ($delResult) {
            $return = array(
                'result' => true,
                'msg' => OW::getLanguage()->text('eventx', 'item_deleted'),
                'url' => OW_Router::getInstance()->urlForRoute('eventx.main_menu_route')
            );
        } else {
            $return = array(
                'result' => false,
                'error' => OW::getLanguage()->text('eventx', 'item_not_deleted')
            );
        }

        return $return;
    }

    public function ajaxSetApprovalStatus($params) {
        $itemId = $params['id'];
        $status = $params['status'];

        $isModerator = OW::getUser()->isAuthorized('eventx');

        if (!$isModerator) {
            throw new Redirect404Exception();
            return;
        }

        $setStatus = $this->eventService->updateEventStatus($itemId, $status);

        if ($setStatus) {
            $return = array('result' => true, 'msg' => OW::getLanguage()->text('eventx', 'status_changed'));
        } else {
            $return = array('result' => false, 'error' => OW::getLanguage()->text('eventx', 'status_not_changed'));
        }

        return $return;
    }

}

class AttendForm extends Form {

    public function __construct($eventId, $contId) {
        parent::__construct('event_attend');
        $this->setAction(OW::getRouter()->urlFor('EVENTX_CTRL_Base', 'attendFormResponder'));
        $this->setAjax();
        $hidden = new HiddenField('attend_status');
        $this->addElement($hidden);
        $eventIdField = new HiddenField('eventId');
        $eventIdField->setValue($eventId);
        $this->addElement($eventIdField);
        $this->setAjaxResetOnSuccess(false);
        $this->bindJsFunction(Form::BIND_SUCCESS, "function(data){
            var \$context = $('#" . $contId . "');

            if(data.messageType == 'error'){
                OW.error(data.message);
            }
            else{
                $('.current_status span.status', \$context).empty().html(data.currentLabel);
                $('.current_status span.link', \$context).css({display:'inline'});
                $('.attend_buttons .buttons', \$context).fadeOut(500);

                if ( data.eventId != 'undefuned' )
                {
                    OW.loadComponent('EVENTX_CMP_EventUsers', {eventId: data.eventId},
                    {
                      onReady: function( html ){
                         $('.userList', \$context).empty().html(html);

                      }
                    });
                }

                $('.userList', \$context).empty().html(data.eventUsersCmp);
                OW.trigger('event_notifications_update', {count:data.newInvCount});
                OW.info(data.message);
            }
        }");
    }

}

class EventAddForm extends Form {

    const EVENTX_NAME = 'eventx.event_add_form.get_element';

    public function __construct($name) {
        parent::__construct($name);

        $militaryTime = Ow::getConfig()->getValue('base', 'military_time');

        $language = OW::getLanguage();

        $currentYear = date('Y', time());

        $title = new TextField('title');
        $title->setRequired();
        $title->setLabel($language->text('eventx', 'add_form_title_label'));

        $event = new OW_Event(self::EVENTX_NAME, array('name' => 'title'), $title);
        OW::getEventManager()->trigger($event);
        $title = $event->getData();

        $this->addElement($title);

        $startDate = new DateField('start_date');
        $startDate->setMinYear($currentYear);
        $startDate->setMaxYear($currentYear + 5);
        $startDate->setRequired();

        $event = new OW_Event(self::EVENTX_NAME, array('name' => 'start_date'), $startDate);
        OW::getEventManager()->trigger($event);
        $startDate = $event->getData();

        $this->addElement($startDate);

        $startTime = new EventTimeField('start_time');
        $startTime->setMilitaryTime($militaryTime);

        if (!empty($_POST['endDateFlag'])) {
            $startTime->setRequired();
        }

        $event = new OW_Event(self::EVENTX_NAME, array('name' => 'start_time'), $startTime);
        OW::getEventManager()->trigger($event);
        $startTime = $event->getData();

        $this->addElement($startTime);

        $endDate = new DateField('end_date');
        $endDate->setMinYear($currentYear);
        $endDate->setMaxYear($currentYear + 5);

        $event = new OW_Event(self::EVENTX_NAME, array('name' => 'end_date'), $endDate);
        OW::getEventManager()->trigger($event);
        $endDate = $event->getData();

        $this->addElement($endDate);

        $endTime = new EventTimeField('end_time');
        $endTime->setMilitaryTime($militaryTime);

        $event = new OW_Event(self::EVENTX_NAME, array('name' => 'end_time'), $endTime);
        OW::getEventManager()->trigger($event);
        $endTime = $event->getData();

        $this->addElement($endTime);

        if (OW::getConfig()->getValue('eventx', 'enableCategoryList') == '1') {
            if (OW::getConfig()->getValue('eventx', 'enableMultiCategories') == 1) {
                $element = new CheckboxGroup('event_category');
                $element->setColumnCount(3);
            } else {
                $element = new SelectBox('event_category');
            }
            $element->setRequired(true);
            $element->setLabel($language->text('eventx', 'event_category_label'));

            foreach (EVENTX_BOL_EventService::getInstance()->getCategoriesList() as $category)
                $element->addOption($category->id, $category->name);

            $this->addElement($element);
        }

        $maxInvites = new TextField('max_invites');
        $maxInvites->setRequired();
        $validator = new IntValidator(0);
        $validator->setErrorMessage($language->text('eventx', 'invalid_integer_value'));
        $maxInvites->addValidator($validator);
        $maxInvites->setLabel($language->text('eventx', 'add_form_maxinvites_label'));
        $this->addElement($maxInvites);

        $location = new TextField('location');
        $location->setRequired();
        $location->setId('location');
        $location->setLabel($language->text('eventx', 'add_form_location_label'));

        $event = new OW_Event(self::EVENTX_NAME, array('name' => 'location'), $location);
        OW::getEventManager()->trigger($event);
        $location = $event->getData();

        $this->addElement($location);

        $whoCanView = new RadioField('who_can_view');
        $whoCanView->setRequired();
        $whoCanView->addOptions(
                array(
                    '1' => $language->text('eventx', 'add_form_who_can_view_option_anybody'),
                    '2' => $language->text('eventx', 'add_form_who_can_view_option_invit_only')
                )
        );
        $whoCanView->setLabel($language->text('eventx', 'add_form_who_can_view_label'));

        $event = new OW_Event(self::EVENTX_NAME, array('name' => 'who_can_view'), $whoCanView);
        OW::getEventManager()->trigger($event);
        $whoCanView = $event->getData();

        $this->addElement($whoCanView);

        $whoCanInvite = new RadioField('who_can_invite');
        $whoCanInvite->setRequired();
        $whoCanInvite->addOptions(
                array(
                    EVENTX_BOL_EventService::CAN_INVITE_PARTICIPANT => $language->text('eventx', 'add_form_who_can_invite_option_participants'),
                    EVENTX_BOL_EventService::CAN_INVITE_CREATOR => $language->text('eventx', 'add_form_who_can_invite_option_creator')
                )
        );
        $whoCanInvite->setLabel($language->text('eventx', 'add_form_who_can_invite_label'));

        $event = new OW_Event(self::EVENTX_NAME, array('name' => 'who_can_invite'), $whoCanInvite);
        OW::getEventManager()->trigger($event);
        $whoCanInvite = $event->getData();

        $this->addElement($whoCanInvite);

        $desc = new WysiwygTextarea('desc');
        $desc->setLabel($language->text('eventx', 'add_form_desc_label'));
        $desc->setRequired();

        $event = new OW_Event(self::EVENTX_NAME, array('name' => 'desc'), $desc);
        OW::getEventManager()->trigger($event);
        $desc = $event->getData();

        $this->addElement($desc);

        $imageField = new FileField('image');
        $imageField->setLabel($language->text('eventx', 'add_form_image_label'));
        $this->addElement($imageField);

        if (OW::getConfig()->getValue('eventx', 'enableTagsList') == '1') {
            $tags = new TagsInputField('tags');
            $tags->setId('tags');
            $tags->setLabel($language->text('base', 'tags_cloud_cap_label'));
            $this->addElement($tags);
        }

        $submit = new Submit('submit');
        $submit->setValue($language->text('eventx', 'add_form_submit_label'));
        $this->addElement($submit);

        $event = new OW_Event(self::EVENTX_NAME, array('name' => 'image'), $imageField);
        OW::getEventManager()->trigger($event);
        $imageField = $event->getData();

        $this->setEnctype(Form::ENCTYPE_MULTYPART_FORMDATA);
    }

}

class EventTimeField extends FormElement {

    private $militaryTime;
    private $allDay = false;

    public function __construct($name) {
        parent::__construct($name);
        $this->militaryTime = false;
    }

    public function setMilitaryTime($militaryTime) {
        $this->militaryTime = (bool) $militaryTime;
    }

    public function setValue($value) {
        if ($value === null) {
            $this->value = null;
        }

        $this->allDay = false;

        if ($value === 'all_day') {
            $this->allDay = true;
            $this->value = null;
            return;
        }

        if (is_array($value) && isset($value['hour']) && isset($value['minute'])) {
            $this->value = array_map('intval', $value);
        }

        if (is_string($value) && strstr($value, ':')) {
            $parts = explode(':', $value);
            $this->value['hour'] = (int) $parts[0];
            $this->value['minute'] = (int) $parts[1];
        }
    }

    public function getValue() {
        if ($this->allDay === true) {
            return 'all_day';
        }

        return $this->value;
    }

    public function getElementJs() {
        $jsString = "var formElement = new OwFormElement('" . $this->getId() . "', '" . $this->getName() . "');";

        foreach ($this->validators as $value) {
            $jsString .= "formElement.addValidator(" . $value->getJsValidator() . ");";
        }

        return $jsString;
    }

    private function getTimeString($hour, $minute) {
        if ($this->militaryTime) {
            $hour = $hour < 10 ? '0' . $hour : $hour;
            return $hour . ':' . $minute;
        } else {
            if ($hour == 12) {
                $dp = 'pm';
            } else if ($hour > 12) {
                $hour = $hour - 12;
                $dp = 'pm';
            } else {
                $dp = 'am';
            }

            $hour = $hour < 10 ? '0' . $hour : $hour;
            return $hour . ':' . $minute . $dp;
        }
    }

    public function renderInput($params = null) {
        parent::renderInput($params);

        for ($hour = 0; $hour <= 23; $hour++) {
            $valuesArray[$hour . ':0'] = array('label' => $this->getTimeString($hour, '00'), 'hour' => $hour, 'minute' => 0);
            $valuesArray[$hour . ':30'] = array('label' => $this->getTimeString($hour, '30'), 'hour' => $hour, 'minute' => 30);
        }

        $optionsString = UTIL_HtmlTag::generateTag('option', array('value' => ""), true, OW::getLanguage()->text('eventx', 'time_field_invitation_label'));

        $allDayAttrs = array('value' => "all_day");

        if ($this->allDay) {
            $allDayAttrs['selected'] = 'selected';
        }

        $optionsString = UTIL_HtmlTag::generateTag('option', $allDayAttrs, true, OW::getLanguage()->text('eventx', 'all_day'));

        foreach ($valuesArray as $value => $labelArr) {
            $attrs = array('value' => $value);

            if (!empty($this->value) && $this->value['hour'] === $labelArr['hour'] && $this->value['minute'] === $labelArr['minute']) {
                $attrs['selected'] = 'selected';
            }

            $optionsString .= UTIL_HtmlTag::generateTag('option', $attrs, true, $labelArr['label']);
        }

        return UTIL_HtmlTag::generateTag('select', $this->attributes, true, $optionsString);
    }

}

class EVENTX_CMP_EventUsersList extends BASE_CMP_Users {

    public function getFields($userIdList) {
        $fields = array();

        $qs = array();

        $qBdate = BOL_QuestionService::getInstance()->findQuestionByName('birthdate', 'sex');

        if ($qBdate->onView)
            $qs[] = 'birthdate';

        $qSex = BOL_QuestionService::getInstance()->findQuestionByName('sex');

        if ($qSex->onView)
            $qs[] = 'sex';

        $questionList = BOL_QuestionService::getInstance()->getQuestionData($userIdList, $qs);

        foreach ($questionList as $uid => $q) {

            $fields[$uid] = array();

            $age = '';

            if (!empty($q['birthdate'])) {
                $date = UTIL_DateTime::parseDate($q['birthdate'], UTIL_DateTime::MYSQL_DATETIME_DATE_FORMAT);

                $age = UTIL_DateTime::getAge($date['year'], $date['month'], $date['day']);
            }

            if (!empty($q['sex'])) {
                $fields[$uid][] = array(
                    'label' => '',
                    'value' => BOL_QuestionService::getInstance()->getQuestionValueLang('sex', $q['sex']) . ' ' . $age
                );
            }

            if (!empty($q['birthdate'])) {
                $dinfo = date_parse($q['birthdate']);
            }
        }

        return $fields;
    }

}