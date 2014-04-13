<?php

class IVIDEO_CMP_VideoList extends OW_Component {

    private $videoService;

    public function __construct(array $params) {
        parent::__construct();
        $this->videoService = IVIDEO_BOL_Service::getInstance();

        $listType = isset($params['type']) ? $params['type'] : '';
        $count = isset($params['count']) ? $params['count'] : 5;
        $tag = isset($params['tag']) ? $params['tag'] : '';
        $userId = isset($params['userId']) ? $params['userId'] : null;
        $category = isset($params['category']) ? $params['category'] : null;

        $page = isset($_GET['page']) && (int) $_GET['page'] ? (int) $_GET['page'] : 1;

        $videosPerPage = (int) OW::getConfig()->getValue('ivideo', 'resultsPerPage');

        if ($userId) {
            $videos = $this->videoService->findUserVideosList($userId, $page, $videosPerPage);
            $records = $this->videoService->findUserVideosCount($userId);
        } else if (strlen($tag)) {
            $videos = $this->videoService->findTaggedVideosList($tag, $page, $videosPerPage);
            $records = $this->videoService->findTaggedVideosCount($tag);
        } else if (strlen($category)) {
            $videos = $this->videoService->findCategoryVideosList($category, $page, $videosPerPage);
            $records = $this->videoService->findCategoryVideosCount($category);
        } else {
            $videos = $this->videoService->findVideosList($listType, $page, $videosPerPage);
            $records = $this->videoService->findVideosCount($listType);
        }

        $this->assign('listType', $listType);

        if ($videos) {
            $this->assign('no_content', null);

            $this->assign('videos', $videos);

            $userIds = array();
            foreach ($videos as $video) {
                if (!in_array($video['owner'], $userIds))
                    array_push($userIds, $video['owner']);
            }

            $names = BOL_UserService::getInstance()->getDisplayNamesForList($userIds);
            $this->assign('displayNames', $names);
            $usernames = BOL_UserService::getInstance()->getUserNamesForList($userIds);
            $this->assign('usernames', $usernames);

            $pages = (int) ceil($records / $videosPerPage);
            $paging = new BASE_CMP_Paging($page, $pages, 10);
            $this->assign('paging', $paging->render());

            $this->assign('count', $count);
        }
        else {
            $this->assign('no_content', OW::getLanguage()->text('ivideo', 'no_video_found'));
        }

        $this->assign('getUserFilesUrl', OW::getPluginManager()->getPlugin('ivideo')->getUserFilesUrl());
        $this->assign('videoPreviewWidth', OW::getConfig()->getValue('ivideo', 'videoPreviewWidth'));
        $this->assign('videoPreviewHeight', OW::getConfig()->getValue('ivideo', 'videoPreviewHeight'));
        $this->assign('posterImage', OW::getPluginManager()->getPlugin('ivideo')->getStaticUrl() . 'poster.jpg');
        $this->assign('defaultThumb',OW::getPluginManager()->getPlugin('ivideo')->getStaticUrl() . 'video.png');
    }

}