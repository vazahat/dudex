<?php

class IVIDEO_BOL_VideoCategoryService {

    private static $classInstance;

    public static function getInstance() {
        if (self::$classInstance === null) {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    private function __construct() {
        
    }

    public function setVideoCategories($videoId, $categoryId) {
        IVIDEO_BOL_VideoCategoryDao::getInstance()->setVideoCategories($videoId, $categoryId);
    }

    public function getVideoCategories($videoId) {
        return IVIDEO_BOL_VideoCategoryDao::getInstance()->getVideoCategories($videoId);
    }

    public function getAllVideoCategories($page, $limit) {
        return IVIDEO_BOL_VideoCategoryDao::getInstance()->getAllVideoCategories($page, $limit);
    }

}

