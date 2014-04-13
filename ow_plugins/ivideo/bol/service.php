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
class IVIDEO_BOL_Service {

    private static $classInstance;
    private $videoDao;

    public static function getInstance() {
        if (self::$classInstance === null) {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    private function __construct() {
        $this->videoDao = IVIDEO_BOL_VideoDao::getInstance();
    }

    public function getPageMenu($isMobile = 0) {
        $language = OW::getLanguage();
        $menuItems = array();


        if (OW::getPluginManager()->isPluginActive('videoplus') && $isMobile == 0) {
            $menuItems = VIDEOPLUS_BOL_Service::getInstance()->getMenu();

            $item = new BASE_MenuItem();
            $item->setLabel($language->text('ivideo', 'video_uploads'));
            $item->setUrl(OW::getRouter()->urlForRoute('ivideo_view_list', array('type' => 'latest')));
            $item->setKey('uploads');
            $item->setIconClass("ow_ic_video");

            if (OW::getConfig()->getValue('ivideo', 'makeUploaderMain') == '1') {
                array_unshift($menuItems, $item);
            } else {
                $item->setOrder(count($menuItems) + 1);
                array_push($menuItems, $item);
            }

            $menu = new BASE_CMP_ContentMenu($menuItems);
            return $menu;
        }

        if (!OW::getPluginManager()->isPluginActive('video')) {
            return FALSE;
        }

        $validLists = array('featured', 'latest', 'toprated', 'tagged');
        $classes = array('ow_ic_push_pin', 'ow_ic_clock', 'ow_ic_star', 'ow_ic_tag');

        if (!VIDEO_BOL_ClipService::getInstance()->findClipsCount('featured')) {
            array_shift($validLists);
            array_shift($classes);
        }

        $order = 0;
        foreach ($validLists as $type) {
            $item = new BASE_MenuItem();
            $item->setLabel($language->text('video', 'menu_' . $type));
            $item->setUrl(OW::getRouter()->urlForRoute('video_view_list', array('listType' => $type)));
            $item->setKey($type);
            $item->setIconClass($classes[$order]);
            $item->setOrder($order);

            array_push($menuItems, $item);

            $order++;
        }

        $item = new BASE_MenuItem();
        $item->setLabel($language->text('ivideo', 'video_uploads'));
        $item->setUrl(OW::getRouter()->urlForRoute('ivideo_view_list', array('type' => 'latest')));
        $item->setKey('uploads');
        $item->setIconClass("ow_ic_video");
        $item->setOrder($order);

        array_push($menuItems, $item);

        $menu = new BASE_CMP_ContentMenu($menuItems);

        return $menu;
    }

    public function deleteVideo($id) {
        $video = $this->videoDao->findById($id);

        unlink(OW::getPluginManager()->getPlugin('ivideo')->getUserFilesDir() . $video->filename . ".png");
        unlink(OW::getPluginManager()->getPlugin('ivideo')->getUserFilesDir() . $video->filename);

        $this->videoDao->deleteById($id);

        BOL_CommentService::getInstance()->deleteEntityComments('ivideo-comments', $id);
        BOL_RateService::getInstance()->deleteEntityRates($id, 'ivideo-rates');
        BOL_TagService::getInstance()->deleteEntityTags($id, 'ivideo');

        BOL_FlagService::getInstance()->deleteByTypeAndEntityId('ivideo_video', $id);

        OW::getEventManager()->trigger(new OW_Event('feed.delete_item', array(
            'entityType' => 'ivideo_comments',
            'entityId' => $id
        )));

        return true;
    }

    public function getPendingVideosList() {
        return $this->videoDao->getPendingVideosList();
    }

    public function findCategoryVideosList($category, $page, $limit) {
        $videos = $this->videoDao->findCategoryVideosList($category, $page, $limit);
        $list = array();
        if (is_array($videos)) {
            foreach ($videos as $key => $video) {
                $list[$key] = (array) $video;
                $list[$key]['imageFile'] = file_exists(OW::getPluginManager()->getPlugin('ivideo')->getUserFilesDir() . $video->filename . ".png") ? $video->filename : "NULL";
            }
        }

        return $list;
    }

    public function findCategoryVideosCount($category) {
        return $this->videoDao->findCategoryVideosCount($category);
    }

    public function findOwner($id) {
        $video = $this->videoDao->findById($id);
        return $video ? $video->owner : null;
    }

    public function addVideo(IVIDEO_BOL_Video $video) {
        $this->videoDao->save($video);

        return $video->id;
    }

    public function updateVideo(IVIDEO_BOL_Video $video) {
        $this->videoDao->save($video);

        return $video->id;
    }

    public function findVideoById($id) {
        return $this->videoDao->findById($id);
    }

    public function findVideoOwner($id) {
        $video = $this->videoDao->findById($id);

        return $video ? $video->owner : null;
    }

    public function findVideosList($type, $page, $limit) {

        if ($type == 'toprated') {
            $first = ( $page - 1 ) * $limit;
            $topRatedList = BOL_RateService::getInstance()->findMostRatedEntityList('ivideo-rates', $first, $limit);

            $videoArr = $this->videoDao->findByIdList(array_keys($topRatedList));

            $videos = array();

            foreach ($videoArr as $key => $video) {
                $videoArrItem = (array) $video;
                $videos[$key] = $videoArrItem;
                $videos[$key]['score'] = $topRatedList[$videoArrItem['id']]['avgScore'];
            }

            usort($videos, array($this, 'sortArrayItemByDesc'));
        } else {
            $videos = $this->videoDao->getVideosList($type, $page, $limit);
        }

        $list = array();
        if (is_array($videos)) {
            foreach ($videos as $key => $video) {
                $list[$key] = (array) $video;
                //$filename = isset($video['filename']) ? $video['filename'] : $video->filename;
                $filename = $video->filename;
                $list[$key]['imageFile'] = file_exists(OW::getPluginManager()->getPlugin('ivideo')->getUserFilesDir() . $filename . ".png") ? $filename : "NULL";
            }
        }

        return $list;
    }

    public function deleteUserVideos($userId) {
        if (!$userId) {
            return false;
        }

        $videosCount = $this->findUserVideosCount($userId);

        if (!$videosCount) {
            return true;
        }

        $videos = $this->findUserVideosList($userId, 1, $videosCount);

        foreach ($videos as $video) {
            $this->deleteVideo($video['id']);
        }

        return true;
    }

    public static function sortArrayItemByDesc($el1, $el2) {
        if ($el1['score'] === $el2['score']) {
            return 0;
        }

        return $el1['score'] < $el2['score'] ? 1 : -1;
    }

    public function findUserVideosList($userId, $page, $itemsNum, $exclude = null) {
        $videos = $this->videoDao->getUserVideosList($userId, $page, $itemsNum, $exclude);

        if (is_array($videos)) {
            $list = array();
            foreach ($videos as $key => $video) {
                $list[$key] = (array) $video;
                $list[$key]['imageFile'] = file_exists(OW::getPluginManager()->getPlugin('ivideo')->getUserFilesDir() . $video->filename . ".png") ? $video->filename : "NULL";
            }

            return $list;
        }

        return null;
    }

    public function findTaggedVideosList($tag, $page, $limit) {
        $first = ($page - 1 ) * $limit;

        $videoIdList = BOL_TagService::getInstance()->findEntityListByTag('ivideo-video', $tag, $first, $limit);

        $videos = $this->videoDao->findByIdList($videoIdList);

        if (is_array($videos)) {
            $list = array();
            foreach ($videos as $key => $video) {
                $list[$key] = (array) $video;
                $list[$key]['imageFile'] = file_exists(OW::getPluginManager()->getPlugin('ivideo')->getUserFilesDir() . $video->filename . ".png") ? $video->filename : "NULL";
            }
        }

        return $list;
    }

    public function findVideosCount($type) {
        if ($type == 'toprated') {
            return BOL_RateService::getInstance()->findMostRatedEntityCount('ivideo-video');
        }

        return $this->videoDao->countVideos($type);
    }

    public function findUserVideosCount($userId) {
        return $this->videoDao->countUserVideos($userId);
    }

    public function findTaggedVideosCount($tag) {
        return BOL_TagService::getInstance()->findEntityCountByTag('ivideo-video', $tag);
    }

    public function updateVideoStatus($id, $status) {
        $video = $this->videoDao->findById($id);

        $newStatus = $status == 'approved' ? 'approved' : 'pending';

        $video->status = $newStatus;

        $this->videoDao->save($video);

        return $video->id ? true : false;
    }

    public function updateVideoFeaturedStatus($id, $status) {
        $video = $this->videoDao->findById($id);

        if ($video) {
            $videoFeaturedService = IVIDEO_BOL_VideoFeaturedService::getInstance();

            if ($status == 'mark_featured') {
                return $videoFeaturedService->markFeatured($id);
            } else {
                return $videoFeaturedService->markUnfeatured($id);
            }
        }

        return false;
    }

    public function updateUserVideosPrivacy($userId, $privacy) {
        if (!$userId || !mb_strlen($privacy)) {
            return false;
        }

        $clips = $this->videoDao->findByUserId($userId);

        if (!$clips) {
            return true;
        }

        $this->videoDao->updatePrivacyByUserId($userId, $privacy);

        $this->cleanListCache();

        $status = $privacy == 'everybody';
        $event = new OW_Event(
                'base.update_entity_items_status', array('entityType' => 'ivideo-rates', 'entityIds' => $clips, 'status' => $status)
        );
        OW::getEventManager()->trigger($event);

        return true;
    }

    public function cleanupPluginContent() {
        BOL_CommentService::getInstance()->deleteEntityTypeComments('ivideo-comments');
        BOL_RateService::getInstance()->deleteEntityTypeRates('ivideo-rates');
        BOL_TagService::getInstance()->deleteEntityTypeTags('ivideo-video');

        BOL_FlagService::getInstance()->deleteByType('ivideo_video');
    }

}
