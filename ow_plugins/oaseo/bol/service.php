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
 * @package oaseo.bol
 */
final class OASEO_BOL_Service
{
    const DATA_ROUTE_NAME = 'rname';
    const DATA_CALLBACK = 'callback';
    const DATA_DTO_PROP = 'dtoProp';
    const DATA_PATH_PROP = 'pathProp';
    const CNF_SLUG_FILTER_COMMON_WORDS = 'slug_filter_words';
    const CNF_SLUG_OLD_URLS_ENABLE = 'slug_old_urls_enabled';
    const CNF_SLUG_PLUGINS = 'slug_plugins';
    const CNF_ROBOTS_CONTENTS = 'robots_contents';
    const CNF_CRAWL_TIME_LIMIT = 'crawl_limit';
    const CNF_MAX_PAGES_TO_INDEX = 'max_index_count';

    /**
     * @var array
     */
    private $slugs = array('str' => array(), 'id' => array());

    /**
     * @var OASEO_BOL_SlugDao
     */
    private $slugDao;

    /**
     * @var OASEO_BOL_MetaDao
     */
    private $metaDao;

    /**
     * @var OASEO_BOL_UrlDao
     */
    private $urlDao;

    /**
     * @var OASEO_BOL_DataDao
     */
    private $dataDao;

    /**
     * @var OASEO_BOL_SitemapItemDao
     */
    private $sitemapItemDao;

    /**
     * @var OASEO_BOL_SitemapPageDao
     */
    private $sitemapPageDao;

    /**
     * @var OASEO_BOL_SitemapPageItemDao
     */
    private $sitemapPageItemDao;

    /**
     * @var array
     */
    private $configs = array();

    /**
     * @var array
     */
    private $activeEntityTypes = array();

    /**
     * Singleton instance.
     *
     * @var OASEO_BOL_Service
     */
    private static $classInstance;

    /**
     * Returns an instance of class (singleton pattern implementation).
     *
     * @return OASEO_BOL_Service
     */
    public static function getInstance()
    {
        if ( self::$classInstance === null )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    /**
     * Constructor.
     */
    private function __construct()
    {
        $this->metaDao = OASEO_BOL_MetaDao::getInstance();
        $this->slugDao = OASEO_BOL_SlugDao::getInstance();
        $this->urlDao = OASEO_BOL_UrlDao::getInstance();
        $this->dataDao = OASEO_BOL_DataDao::getInstance();
        $this->sitemapItemDao = OASEO_BOL_SitemapItemDao::getInstance();
        $this->sitemapPageDao = OASEO_BOL_SitemapPageDao::getInstance();
        $this->sitemapPageItemDao = OASEO_BOL_SitemapPageItemDao::getInstance();

        $this->configs[self::CNF_SLUG_FILTER_COMMON_WORDS] = false;
        $this->configs[self::CNF_SLUG_OLD_URLS_ENABLE] = OW::getConfig()->getValue('oaseo', self::CNF_SLUG_OLD_URLS_ENABLE);
        $this->configs[self::CNF_SLUG_PLUGINS] = json_decode(OW::getConfig()->getValue('oaseo', self::CNF_SLUG_PLUGINS), true);
        $this->configs[self::CNF_CRAWL_TIME_LIMIT] = 15;
        $this->configs[self::CNF_MAX_PAGES_TO_INDEX] = 3000;

        $slugData = $this->getSlugData();

        foreach ( $slugData as $pluginKey => $data )
        {
            if ( in_array($pluginKey, $this->configs[self::CNF_SLUG_PLUGINS]) )
            {
                $this->activeEntityTypes = array_unique(array_merge($this->activeEntityTypes, array_keys($data)));
            }
        }

        // get all active slugs
        $slugs = $this->slugDao->findWorkingSlugs($this->activeEntityTypes);

        /* @var $slug OASEO_BOL_Slug */
        foreach ( $slugs as $slug )
        {
            if ( $slug->getActive() )
            {
                $this->slugs['str'][$slug->getEntityType()][$slug->getString()] = $slug;
                $this->slugs['id'][$slug->getEntityType()][$slug->getEntityId()] = $slug;
            }
        }
    }

    /**
     * @return array
     */
    public function getConfigs()
    {
        return $this->configs;
    }

    /**
     * @return boolean
     */
    public function isAdmin()
    {
        return (OW::getUser()->isAuthenticated() && OW::getUser()->isAuthorized('admin'));
    }

    /**
     * Returns meta info for provided dispatch params.
     * 
     * @param array $dispatchParams
     * @return OASEO_BOL_Meta
     */
    public function getEntryForDispatchParams( array $dispatchParams )
    {
        return $this->getEntryByKey($this->generateKey($dispatchParams));
    }

    /**
     * Returns meta info for provided url.
     * 
     * @param string $url
     * @return OASEO_BOL_Meta
     */
    public function getEntryForUri( $uri )
    {
        $result = $this->getDispatchParamsForUri($uri);
        return $result === false ? false : $this->getEntryForDispatchParams($result);
    }

    /**
     * Returns dispatch params for url.
     * 
     * @param type $uri
     * @return array
     */
    public function getDispatchParamsForUri( $uri )
    {
        $router = OW::getRouter();
        $preUri = $router->getUri();
        $router->setUri($uri);

        // temp fix for ajax

        try
        {
            $dispatchAttrs = $router->route();
            $router->setUri($preUri);
            if ( isset($dispatchAttrs['vars']) )
            {
                $dispatchAttrs['params'] = $dispatchAttrs['vars'];
                unset($dispatchAttrs['vars']);
            }
            return $dispatchAttrs;
        }
        catch ( Exception $e )
        {
            return false;
        }
    }

    /**
     * Generates meta key for dispatch params.
     * 
     * @param type $dispatchAttrs
     */
    public function generateKey( array $dispatchAttrs )
    {
        if( empty($dispatchAttrs['action']) )
        {
            $dispatchAttrs['action'] = 'index';
        }
        
        $keyString = $dispatchAttrs['controller'] . '::' . $dispatchAttrs['action'] . '?';

        if ( !empty($dispatchAttrs['params']) )
        {
            ksort($dispatchAttrs['params']);

            foreach ( $dispatchAttrs['params'] as $paramName => $paramVal )
            {
                $keyString .= $paramName . '|' . $paramVal;
            }
        }

        return md5($keyString);
    }

    /**
     * @param array $dispatchAttrs
     * @param array $get
     * @return string
     */
    public function generateKeyWithGet( array $dispatchAttrs, array $get )
    {
        $keyString = $dispatchAttrs['controller'] . '::' . $dispatchAttrs['action'] . '?';

        if ( !empty($dispatchAttrs['params']) )
        {
            ksort($dispatchAttrs['params']);

            foreach ( $dispatchAttrs['params'] as $paramName => $paramVal )
            {
                $keyString .= $paramName . '|' . $paramVal;
            }
        }

        if ( !empty($get) )
        {
            ksort($get);

            foreach ( $get as $paramName => $paramVal )
            {
                $keyString .= $paramName . '+' . $paramVal;
            }
        }

        return md5($keyString);
    }

    /**
     * @param OASEO_BOL_Meta $entry
     */
    public function saveEntry( OASEO_BOL_Meta $entry )
    {
        $this->metaDao->save($entry);
    }

    /**
     * @param string $key
     * @return OASEO_BOL_Meta
     */
    public function getEntryByKey( $key )
    {
        return $this->metaDao->findEntryByKey($key);
    }

    /**
     * @param string $entityType
     * @param integer $entityId
     * @param callback $serviceCallback
     * @param string $dtoProperty
     * @return string
     */
    public function getSlugStringForEntity( $entityType, $entityId, $serviceCallback, $dtoProperty )
    {
        if ( !empty($this->slugs['id'][$entityType][$entityId]) )
        {
            return $this->slugs['id'][$entityType][$entityId]->getString();
        }

        $dbSlug = $this->slugDao->findActiveSlugForEntityItem($entityType, $entityId);

        if ( $dbSlug !== null )
        {
            return $dbSlug->getString();
        }

        // processing and generating new slug
        $dto = call_user_func($serviceCallback, $entityId);

        if ( $dto !== null )
        {
            $vars = get_object_vars($dto);

            if ( !empty($vars[$dtoProperty]) && is_string($vars[$dtoProperty]) )
            {
                // do not support non latin strings
//                if ( preg_match('/[^\\p{Common}\\p{Latin}]/u', $vars[$dtoProperty]) )
//                {
//                    return null;
//                }

                $slug = $this->makeSlug($vars[$dtoProperty]);

                $duplicateSlugDto = $this->slugDao->findDuplicateSlug($entityType, $slug);

                if ( $duplicateSlugDto !== null )
                {
                    $slug = $slug . '-' . $entityId;
                }

                if ( strlen($slug) > 2 )
                {
                    $slugDto = $this->slugDao->findSlug($entityType, $entityId, $slug);

                    if ( $slugDto === null )
                    {
                        $this->slugDao->updateSlugStatus($entityType, $entityId);
                        $slugDto = new OASEO_BOL_Slug();
                        $slugDto->setEntityType($entityType);
                        $slugDto->setEntityId($entityId);
                        $slugDto->setString($slug);
                        $slugDto->setActive(true);
                        $this->slugDao->save($slugDto);
                    }

                    $this->slugs['id'][$entityType][$entityId] = $slugDto;

                    return $slugDto->getString();
                }
            }
        }
    }

    /**
     * @param string $entityType
     * @param string $slugString
     * @return OASEO_BOL_Slug
     */
    public function getSlugForString( $entityType, $slugString )
    {
        if ( !empty($this->slugs['str'][$entityType][$slugString]) )
        {
            return $this->slugs['str'][$entityType][$slugString];
        }
    }

    /**
     * @param string $entityType
     * @param string $slugString
     * @return OASEO_BOL_Slug
     */
    public function findActiveSlugForInactiveOne( $entityType, $slugString )
    {
        $oldSlug = $this->slugDao->findOldSlug($entityType, $slugString);

        if ( $oldSlug !== null )
        {
            return $this->slugDao->findActiveSlugForEntityItem($entityType, $oldSlug->getEntityId());
        }
    }

    /**
     * @param string $entityType
     * @param integer $entityId
     * @return OASEO_BOL_Slug
     */
    public function findActiveSlugForEntityItem( $entityType, $entityId )
    {
        return $this->slugDao->findActiveSlugForEntityItem($entityType, $entityId);
    }

    /**
     * @param string $entityType
     * @param integer $entityId
     * @param callback $callback
     * @param string $dtoProperty
     */
    public function checkEntityUpdate( $entityType, $entityId, $callback, $dtoProperty )
    {
        $dto = call_user_func($callback, $entityId);
        $vars = get_object_vars($dto);

        if ( empty($vars[$dtoProperty]) )
        {
            return;
        }

        $procSlug = $this->makeSlug($vars[$dtoProperty]);

        $slugArr = explode('-', $this->slugs['id'][$entityType][$entityId]->getString());
        $potencialId = $slugArr[sizeof($slugArr) - 1];

        if ( is_numeric($potencialId) && (int) $potencialId === (int) $entityId )
        {
            $procSlug .= '-' . $entityId;
        }

        if ( $procSlug !== $this->slugs['id'][$entityType][$entityId]->getString() )
        {
            $this->slugDao->updateSlugStatus($entityType, $entityId);
            $slugDto = $this->slugDao->findSlug($entityType, $entityId, $procSlug);
            if ( $slugDto === null )
            {
                $slugDto = new OASEO_BOL_Slug();
                $slugDto->setEntityType($entityType);
                $slugDto->setEntityId($entityId);
                $slugDto->setString($procSlug);
            }
            $slugDto->setActive(true);
            $this->slugDao->save($slugDto);
        }
    }

    /**
     * @return array
     */
    public function getSlugData()
    {
        return array(
            'forum' => array(
                'forum-topic' => array(
                    self::DATA_ROUTE_NAME => 'topic-default',
                    self::DATA_CALLBACK => array('FORUM_BOL_ForumService', 'findTopicById'),
                    self::DATA_DTO_PROP => 'title',
                    self::DATA_PATH_PROP => 'topicId'),
                'forum-group' => array(
                    self::DATA_ROUTE_NAME => 'group-default',
                    self::DATA_CALLBACK => array('FORUM_BOL_ForumService', 'findGroupById'),
                    self::DATA_DTO_PROP => 'name',
                    self::DATA_PATH_PROP => 'groupId'),
            ),
            'blogs' => array(
                'blogs-user-post' => array(
                    self::DATA_ROUTE_NAME => 'user-post',
                    self::DATA_CALLBACK => array('PostService', 'findById'),
                    self::DATA_DTO_PROP => 'title',
                    self::DATA_PATH_PROP => 'id'),
            ),
            'blogs' => array(
                'blogs-user-post' => array(
                    self::DATA_ROUTE_NAME => 'user-post',
                    self::DATA_CALLBACK => array('PostService', 'findById'),
                    self::DATA_DTO_PROP => 'title',
                    self::DATA_PATH_PROP => 'id'),
            ),
            'event' => array(
                'event-view' => array(
                    self::DATA_ROUTE_NAME => 'event.view',
                    self::DATA_CALLBACK => array('EVENT_BOL_EventService', 'findEvent'),
                    self::DATA_DTO_PROP => 'title',
                    self::DATA_PATH_PROP => 'eventId'),
            ),
            'groups' => array(
                'groups-view' => array(
                    self::DATA_ROUTE_NAME => 'groups-view',
                    self::DATA_CALLBACK => array('GROUPS_BOL_Service', 'findGroupById'),
                    self::DATA_DTO_PROP => 'title',
                    self::DATA_PATH_PROP => 'groupId'),
            ),
            'video' => array(
                'video-view' => array(
                    self::DATA_ROUTE_NAME => 'view_clip',
                    self::DATA_CALLBACK => array('VIDEO_BOL_ClipService', 'findClipById'),
                    self::DATA_DTO_PROP => 'title',
                    self::DATA_PATH_PROP => 'id'),
            ),
        );
    }

    public function initSlugs()
    {
        $dataArray = $this->getSlugData();
        $router = OW::getRouter();
        foreach ( $dataArray as $pluginKey => $pluginData )
        {
            if ( in_array($pluginKey, $this->configs[self::CNF_SLUG_PLUGINS]) )
            {
                foreach ( $pluginData as $entityType => $entityData )
                {
                    $route = $router->getRoute($entityData[self::DATA_ROUTE_NAME]);

                    if ( $route !== null )
                    {
                        $router->removeRoute($entityData[self::DATA_ROUTE_NAME]);
                        $specRoute = new OASEO_CLASS_SlugRoute($route, array(call_user_func(array($entityData[self::DATA_CALLBACK][0], 'getInstance')), $entityData[self::DATA_CALLBACK][1]), $entityData[self::DATA_DTO_PROP], $entityData[self::DATA_PATH_PROP], $entityType);
                        $router->addRoute($specRoute);
                    }
                }
            }
        }
    }

    public function getRouteData( $uri )
    {
        $router = OW::getRouter();
        $routerArr = (array) $router;

        $staticRoutes = $routerArr["\0OW_Router\0staticRoutes"];
        $routes = $routerArr["\0OW_Router\0routes"];

        $currentRoute = null;

        foreach ( $staticRoutes as $route )
        {
            if ( $route->match($uri) )
            {
                $currentRoute = $route;
                break;
            }
        }

        if ( $currentRoute === null )
        {
            foreach ( $routes as $route )
            {
                if ( $route->match($uri) )
                {
                    $currentRoute = $route;
                    break;
                }
            }
        }

        if ( $currentRoute === null )
        {
            return null;
        }

        $rtArr = (array) $currentRoute;

        $urlDto = $this->urlDao->findByRouteName($rtArr["\0OW_Route\0routeName"]);
        return array('name' => $rtArr["\0OW_Route\0routeName"], 'path' => ( empty($urlDto) ? $rtArr["\0OW_Route\0routePath"] : $urlDto->getUrl() ));
    }

    public function initUrls()
    {
        $router = OW::getRouter();
        $urls = $this->urlDao->findAll();

        /* @var $url OASEO_BOL_Url */
        foreach ( $urls as $url )
        {
            $rt = $router->getRoute($url->getRouteName());

            if ( $rt !== null )
            {
                $slugRoute = false;
                if ( get_class($rt) == 'OASEO_CLASS_SlugRoute' )
                {
                    $origRt = $rt;
                    $rt = $origRt->getRoute();
                    $slugRoute = true;
                }

                $rtArr = (array) $rt;
                $routeName = $rtArr["\0OW_Route\0routeName"];
                $routePath = $rtArr["\0OW_Route\0routePath"];
                $routePathArray = $rtArr["\0OW_Route\0routePathArray"];
                $isStatic = $rtArr["\0OW_Route\0isStatic"];
                $dispatchAttrs = $rtArr["\0OW_Route\0dispatchAttrs"];
                $routeParamOptions = $rtArr["\0OW_Route\0routeParamOptions"];
                $newRoute = new OW_Route($routeName, $url->getUrl(), $dispatchAttrs['controller'], $dispatchAttrs['action'], $routeParamOptions);

                if ( $slugRoute === true )
                {
                    $newRoute = new OASEO_CLASS_SlugRoute($newRoute, $origRt->getServiceCallback(), $origRt->getDtoProperty(), $origRt->getPathProperty(), $origRt->getEntityType());
                }

                $router->removeRoute($url->getRouteName());
                $router->addRoute($newRoute);

                $rand = uniqid('redirect_url');
                $redirectRoute = new OASEO_CLASS_RedirectRoute($rand, $routePath, $rand, $rand);
                $redirectRoute->setNewUrl($url->getUrl());
                $router->addRoute($redirectRoute);
            }
        }
    }

    /**
     * @param string $name
     * @return OASEO_BOL_Url
     */
    public function findUrlByRouteName( $name )
    {
        return $this->urlDao->findByRouteName($name);
    }

    /**
     * @param OASEO_BOL_Url $dto
     */
    public function saveUrl( OASEO_BOL_Url $dto )
    {
        $this->urlDao->save($dto);
    }

    private function makeSlug( $string )
    {
        $charToReplace = array("'", '=', '-', '&', '.', '+', '?', ';', ':', '"', '#', '%', '^', '~', '*', '$', '@', '!', '`', '/', '\\', '<', '>', ',', '(', ')', '_');

        $wordsToFilter = json_decode($this->getConfig(self::CNF_SLUG_FILTER_COMMON_WORDS));
        $finalWordsToFilter = array();

        foreach ( $wordsToFilter as $wordToFilter )
        {
            $finalWordsToFilter[] = ' ' . $wordToFilter . ' ';
        }

        if ( !is_array($wordsToFilter) )
        {
            $finalWordsToFilter = array();
        }

        $string = mb_strtolower($string);

        $slug = str_replace($charToReplace, '', $string);
        $slug = str_replace('  ', ' ', $slug);
        $slug = str_ireplace($finalWordsToFilter, ' ', ' ' . $slug . ' ');

        $slug = str_replace(" ", "-", trim($slug));
        return $slug;
    }
    const PAGES_BROKEN_LINKS = 'broken_links';
    const PAGES_IMAGE_LIST = 'image_list';
    const PAGES_URL_LIST = 'url_list';
    const PAGES_EXT_URL_LIST = 'ext_url_list';
    const PAGES_DUMP = 'dump';

    private $pages = array();

    /* -------------------- sitemap gen ------------------------ */

    public function startSitemapGenerator()
    {
        if ( !OW::getConfig()->getValue('oaseo', 'sitemap_init') )
        {
            OW::getConfig()->saveConfig('oaseo', 'sitemap_init', 1);
        }

        $prof = UTIL_Profiler::getInstance('oaseo_sitemap');

        if ( OW::getConfig()->getValue('oaseo', 'update_info') )
        {
            OW::getConfig()->saveConfig('oaseo', 'update_info', 0);
            $this->sitemapItemDao->clearTable();
            $this->sitemapPageDao->clearTable();
            $this->sitemapPageItemDao->clearTable();
            $this->addUrlToList(UTIL_String::removeFirstAndLastSlashes(OW_URL_HOME));
        }

        if ( $this->getNextUrlToProcess() == null )
        {
            return;
        }

        while ( $prof->getTotalTime() < $this->configs[self::CNF_CRAWL_TIME_LIMIT] )
        {
            $url = $this->getNextUrlToProcess();
//
//            if ( $url == null )
//            {
//                // need to complete site generation + generate sitemaps in configs
//                OW::getConfig()->saveConfig('oaseo', 'update_maps', 1);
//                break;
//            }

            $pageDto = $this->sitemapPageDao->findByUrl($url, 0);

            if ( $pageDto != null )
            {
                $pageDto->setStatus(1);
                $pageDto->setProcessTs(time());
                $this->sitemapPageDao->save($pageDto);

                if ( $this->isBroken($url) )
                {
                    $pageDto->setBroken(true);
                    $this->sitemapPageDao->save($pageDto);

                    continue;
                }
            }

            // TODO need to check if response is ok
            $content = file_get_contents($url);

            if ( !$content )
            {
                continue;
                $this->sitemapPageDao->deleteById($pageDto->getId());
            }
            $data = $this->processContent($content);

            $urlHome = UTIL_String::removeFirstAndLastSlashes(OW_URL_HOME);

            //add meta info to the page entry
            $pageDto = $this->sitemapPageDao->findByUrl($url);
            $pageDto->setMeta(json_encode($data['meta']));
            $pageDto->setTitle($data['title']);
            $this->sitemapPageDao->save($pageDto);

            foreach ( $data['foundLinks'] as $link )
            {
                $pageItem = new OASEO_BOL_SitemapPageItem();

                if ( mb_strstr($link, $urlHome) )
                {
                    $addedItem = $this->addUrlToList($link);

                    if ( $addedItem === null )
                    {
                        continue;
                        ;
                    }

                    $pageItem->setType(OASEO_BOL_SitemapPageItemDao::TYPE_VALUE_PAGE);
                }
                else if ( mb_strstr($link, 'http://') || mb_strstr($link, 'www') )
                {
                    $addedItem = $this->addExtUrl($link);

                    if ( $addedItem === null )
                    {
                        continue;
                    }

                    $pageItem->setType(OASEO_BOL_SitemapPageItemDao::TYPE_VALUE_ITEM);
                }
                else
                {
                    continue;
                }

                $pageItem->setPageId($pageDto->getId());
                $pageItem->setItemId($addedItem->getId());
                $this->sitemapPageItemDao->save($pageItem);
            }

            foreach ( $data['foundImages'] as $image )
            {
                $pageItem = new OASEO_BOL_SitemapPageItem();
                $image = $this->addImage($image);
                $pageItem->setPageId($pageDto->getId());
                $pageItem->setItemId($image->getId());
                $pageItem->setType(OASEO_BOL_SitemapPageItemDao::TYPE_VALUE_ITEM);

                $this->sitemapPageItemDao->save($pageItem);
            }

            if ( $this->getNextUrlToProcess() == null )
            {
                // need to complete site generation + generate sitemaps in configs
                OW::getConfig()->saveConfig('oaseo', 'update_maps', 1);
                break;
            }
        }
    }

    public function updateConfigMaps()
    {
        $config = OW::getConfig();

        if ( !(bool) OW::getConfig()->getValue('oaseo', 'update_maps') )
        {
            return;
        }

        $siteMap = '<?xml version="1.0" encoding="UTF-8"?>
  <urlset
  xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
  xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
        http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . PHP_EOL;

        $chFreq = 'monthly';

        switch ( (int) $this->getConfig('update_freq') )
        {
            case 86400:;
                $chFreq = 'daily';
                break;

            case 604800:
                $chFreq = 'weekly';
                break;

            case 2419200:
                $chFreq = 'monthly';
                break;
        }

        $urlSearch = array('&', '>', '<');
        $urlReplace = array('&amp;', '&gt;', '&lt;');

        $start = 0;
        $count = 100;

        while ( true )
        {
            $pageList = $this->sitemapPageDao->findAllProcessedUrls($start, $count);

            if ( empty($pageList) || $start >= $this->configs[self::CNF_MAX_PAGES_TO_INDEX] )
            {
                break;
            }

            /* @var $page OASEO_BOL_SitemapPage */
            foreach ( $pageList as $page )
            {
                $siteMap .=
                    '<url>
    <loc>' . str_replace($urlSearch, $urlReplace, $page['url']) . '</loc>
    <lastmod>' . date('Y-m-d', $page['processTs']) . 'T' . date('H:i:sP', $page['processTs']) . '</lastmod>
    <changefreq>' . $chFreq . '</changefreq>
</url>' . PHP_EOL;
            }

            $start += 100;
        }

        $siteMap .= '</urlset>';

        file_put_contents($this->getSiteMapPath(), $siteMap);
        file_put_contents($this->getSiteMapPath(true), gzencode($siteMap));

        /* image map */

        $start = 0;
        $count = 100;

        $imageMap = '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';

        while ( true )
        {
            $imageList = $this->findItems(OASEO_BOL_Service::ITEM_VAL_IMAGE, $start, $count);

            $pageImageList = array();

            if ( empty($imageList) || $start >= $this->configs[self::CNF_MAX_PAGES_TO_INDEX] )
            {
                break;
            }

            foreach ( $imageList as $image )
            {
                if ( !isset($pageImageList[$image['url']]) )
                {
                    $pageImageList[$image['url']] = array();
                }

                $pageImageList[$image['url']][] = $image['value'];
            }

            foreach ( $pageImageList as $url => $list )
            {
                $imageMap .= '<url>' . PHP_EOL;
                $imageMap .= '  <loc>' . str_replace($urlSearch, $urlReplace, $url) . '</loc>' . PHP_EOL;

                foreach ( $list as $imageUrl )
                {
                    $imageMap .= '  <image:image>' . PHP_EOL;
                    $imageMap .= '      <image:loc>' . str_replace($urlSearch, $urlReplace, $imageUrl) . '</image:loc>' . PHP_EOL;
                    $imageMap .= '  </image:image>' . PHP_EOL;
                }

                $imageMap .= '</url>' . PHP_EOL;
            }

            $start += 100;
        }

        $imageMap .= '</urlset>';

        file_put_contents($this->getImageMapPath(), $imageMap);
        file_put_contents($this->getImageMapPath(true), gzencode($imageMap));

        OW::getConfig()->saveConfig('oaseo', 'update_maps', 0);

        // notify services

        $services = json_decode($this->getConfig('inform')) ? json_decode($this->getConfig('inform')) : array();
        $encSitemapUrl = urlencode(OW_URL_HOME . 'gz' . trim($this->getConfig('sitemap_url')));
        $encImageMap = urlencode(OW_URL_HOME . 'gz' . trim($this->getConfig('imagemap_url')));

        OW::getConfig()->saveConfig('oaseo', 'update_ts', time());

        if ( in_array('google', $services) )
        {
            get_headers('http://www.google.com/webmasters/tools/ping?sitemap=' . $encSitemapUrl);
            get_headers('http://www.google.com/webmasters/tools/ping?sitemap=' . $encImageMap);
        }

        if ( in_array('bing', $services) )
        {
            get_headers('http://www.bing.com/ping?sitemap=' . $encSitemapUrl);
        }

        if ( in_array('yahoo', $services) )
        {
            get_headers('http://search.yahooapis.com/SiteExplorerService/V1/ping?sitemap=' . $encSitemapUrl);
        }

        if ( in_array('ask', $services) )
        {
            get_headers('http://submissions.ask.com/ping?sitemap=' . $encSitemapUrl);
        }
    }

    public function scheduleInfoUpdate()
    {
        $nextUrls = $this->sitemapPageDao->getNextUrlList(1);
        if ( ((int) $this->getConfig('update_ts') + (int) $this->getConfig('update_freq')) < time() && empty($nextUrls) )
        {
            $this->saveConfig('update_info', 1);
        }
    }

    public function getSiteMapPath( $gzip = false )
    {
        return OW::getPluginManager()->getPlugin('oaseo')->getPluginFilesDir() . 'sitemap.xml' . ($gzip ? '.gz' : '');
    }

    public function getImageMapPath( $gzip = false )
    {
        return OW::getPluginManager()->getPlugin('oaseo')->getPluginFilesDir() . 'imagemap.xml' . ($gzip ? '.gz' : '');
    }

    private function getConfig( $name )
    {
        return OW::getConfig()->getValue('oaseo', $name);
    }

    private function saveConfig( $name, $value )
    {
        OW::getConfig()->saveConfig('oaseo', $name, $value);
    }

    private function isBroken( $url )
    {
        if ( !mb_strstr($url, 'http://') )
        {
            return true;
        }

        $headers = get_headers($url);

        if ( empty($headers) )
        {
            return true;
        }

        return strstr(implode(' ', $headers), '404 Not Found');
    }

    private function processContent( $content )
    {
        $resultArray = array('foundLinks' => array(), 'foundImages' => array(), 'meta' => array());

        // get all page links
        $links = array();

        preg_match_all('/<a[^<>]+href=["\']([^"\']+)["\']/i', $content, $links, PREG_PATTERN_ORDER);

        $links = array_unique($links[1]);

        foreach ( $links as $link )
        {
            if ( mb_strstr($link, 'javascript://') )
            {
                continue;
            }
            else if ( mb_strstr($link, '.') && in_array(UTIL_File::getExtension($link), array('gif', 'jpg', 'png', 'jpeg')) )
            {
                $resultArray['foundImages'][] = $link;
                continue;
            }

            if ( mb_strstr($link, '#') )
            {
                $link = mb_substr($link, 0, strpos($link, '#'));
            }

            $resultArray['foundLinks'][] = UTIL_String::removeFirstAndLastSlashes($link);
        }

        $images = array();
        preg_match_all('/<img\s+src="(.*?)"/i', $content, $images);
        $images = array_unique($images[1]);

        foreach ( $images as $image )
        {
            if ( mb_strstr($image, OW_URL_STATIC_THEMES) || !mb_strstr($image, '.') || !in_array(UTIL_File::getExtension($image), array('gif', 'jpg', 'png', 'jpeg')) )
            {
                continue;
            }

            if ( mb_strstr($image, '?') )
            {
                $image = substr($image, 0, mb_strpos($image, '?'));
            }

            $resultArray['foundImages'][] = $image;
        }

        /* spec hack to find hidden images-------------- */

        $images = array();
        preg_match_all('/showPhotoCmp\((.+)\)/i', $content, $images);
        $imageIds = array_unique($images[1]);

        if ( OW::getPluginManager()->isPluginActive('photo') )
        {
            $photoPl = OW::getPluginManager()->getPlugin('photo');

            foreach ( $imageIds as $id )
            {
                if ( intval($id) > 0 )
                {
                    $resultArray['foundImages'][] = OW_URL_PLUGIN_USERFILES . $photoPl->getModuleName() . DS . 'photo_original_' . intval($id) . '.jpg';
                }
            }
        }

        /* --------------spec hack to find hidden images */

        $metaList = array();
        preg_match_all('/<meta[^\<\>]+>/i', $content, $metaList);

        foreach ( $metaList[0] as $meta )
        {
            if ( mb_strstr($meta, 'http-equiv="') )
            {
                continue;
            }

            $nameArray = array();
            preg_match_all('/name\s*=\s*"(.*?)"/i', $meta, $nameArray);
            $valueArray = array();
            preg_match_all('/content\s*=\s*"(.*?)"/i', $meta, $valueArray);

            if ( !empty($nameArray[1][0]) && !empty($valueArray[1][0]) )
            {
                $resultArray['meta'][$nameArray[1][0]] = $valueArray[1][0];
            }
        }

        // get title
        $start = mb_strpos($content, '<title>') + mb_strlen('<title>');
        $end = mb_strpos($content, '</title>');

        if ( $start )
        {
            $resultArray['title'] = mb_substr($content, $start, $end - $start);
        }

        return $resultArray;
    }

    /**
     * @param string $url
     * @return OASEO_BOL_SitemapPage
     */
    private function addUrlToList( $url )
    {
        $page = null;

        if ( $this->sitemapPageDao->findPagesCount() > $this->configs[self::CNF_MAX_PAGES_TO_INDEX] )
        {
            return null;
        }

        try
        {
            $url = trim($url);
            $page = $this->sitemapPageDao->findByUrl($url);

            if ( $page == null )
            {
                $page = new OASEO_BOL_SitemapPage();
                $page->setUrl($url);
                $page->setStatus(0);
                $page->setProcessTs(0);
                $page->setBroken(false);

                $this->sitemapPageDao->save($page);
            }
        }
        catch ( Exception $e )
        {
            
        }

        return $page;
    }

    /**
     * @param string $url
     * @return OASEO_BOL_SitemapItem
     */
    private function addExtUrl( $url )
    {
        $url = trim($url);
        $extUrl = $this->sitemapItemDao->findItemByValue($url);

        if ( $extUrl == null )
        {
            $type = $this->isBroken($url) ? OASEO_BOL_SitemapItemDao::VALUE_BROKEN_EXT_LINK : OASEO_BOL_SitemapItemDao::VALUE_EXT_LINK;
            $extUrl = new OASEO_BOL_SitemapItem();
            $extUrl->setType($type);
            $extUrl->setValue($url);
            $extUrl->setAddTs(time());
            $this->sitemapItemDao->save($extUrl);
        }

        return $extUrl;
    }

    /**
     * @param string $url
     * @return OASEO_BOL_SitemapItem
     */
    private function addImage( $url )
    {
        $url = trim($url);
        $image = $this->sitemapItemDao->findItem($url, OASEO_BOL_SitemapItemDao::VALUE_IMAGE);

        if ( $image == null )
        {
            $image = $this->sitemapItemDao->findItem($url, OASEO_BOL_SitemapItemDao::VALUE_BROKEN_IMAGE);
        }

        if ( $image == null )
        {
            $type = $this->isBroken($url) ? OASEO_BOL_SitemapItemDao::VALUE_BROKEN_IMAGE : OASEO_BOL_SitemapItemDao::VALUE_IMAGE;
            $image = new OASEO_BOL_SitemapItem();
            $image->setValue($url);
            $image->setType($type);
            $image->setAddTs(time());
            $this->sitemapItemDao->save($image);
        }

        return $image;
    }

    /**
     * @return int
     */
    public function getProcessedPagesCount()
    {
        return $this->sitemapPageDao->findProcessedCount(true);
    }

    /**
     * @return int
     */
    public function getToProcessPagesCount()
    {
        return $this->sitemapPageDao->findProcessedCount(false);
    }

    public function getNextUrlToProcess()
    {
        $result = $this->sitemapPageDao->getNextUrlList(1);

        return empty($result) ? null : $result[0]->getUrl();
    }

    public function getDataValue( $key )
    {
        return $this->dataDao->getByKey($key);
    }

    public function saveDataValue( $key, $value )
    {
        $dto = $this->dataDao->getByKey($key);

        if ( $dto === null )
        {
            $dto = new OASEO_BOL_Data();
            $dto->setKey($key);
        }

        $dto->setData($value);
        $this->dataDao->save($dto);
    }
    const ITEM_VAL_IMAGE = OASEO_BOL_SitemapItemDao::VALUE_IMAGE;
    const ITEM_VAL_BROKEN_IMAGE = OASEO_BOL_SitemapItemDao::VALUE_BROKEN_IMAGE;
    const ITEM_VAL_BROKEN_LINK = OASEO_BOL_SitemapItemDao::VALUE_BROKEN_LINK;
    const ITEM_VAL_EXT_LINK = OASEO_BOL_SitemapItemDao::VALUE_EXT_LINK;
    const ITEM_VAL_BROKEN_EXT_LINK = OASEO_BOL_SitemapItemDao::VALUE_BROKEN_EXT_LINK;

    /**
     * @return int
     */
    public function findAllPagesCount()
    {
        return $this->sitemapPageDao->findPagesCount();
    }

    /**
     * @return int
     */
    public function findBrokenPagesCount()
    {
        return $this->sitemapPageDao->findPagesCount(true);
    }

    /**
     * @return array
     */
    public function findPages( $first, $count, $broken = false )
    {
        if ( $broken )
        {
            return $this->sitemapPageDao->findBrokenPages($first, $count);
        }

        return $this->sitemapPageDao->findPages($first, $count, $broken);
    }

    /**
     * @param string $type
     * @return int
     */
    public function findItemsCount( $type )
    {
        return $this->sitemapItemDao->findItemsCountByType($type);
    }

    /**
     * @param string $type
     * @return array
     */
    public function findItems( $type, $first, $count )
    {
        return $this->sitemapItemDao->findItemsByType($type, $first, $count);
    }
}
