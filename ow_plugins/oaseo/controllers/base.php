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
class OASEO_CTRL_Base extends OW_ActionController
{
    /**
     * @var OASEO_BOL_Service
     */
    private $metaService;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->metaService = OASEO_BOL_Service::getInstance();
    }

    /**
     * Frontend update meta form responder.
     */
    public function updateMeta()
    {
        $language = OW::getLanguage();

        if ( !OW::getRequest()->isAjax() || !$this->metaService->isAdmin() || !isset($_POST['uri']) )
        {
            throw new Redirect404Exception();
        }

        if ( OW::getRequest()->isPost() )
        {
            try
            {
                $this->processMetaInfo();
            }
            catch ( Exception $e )
            {
                exit(json_encode(array('status' => false, 'msg' => $e->getMessage())));
            }
        }

        exit(json_encode(array('status' => true, 'msg' => $language->text('oaseo', 'frontend_submit_success_message'))));
    }

    public function robots()
    {
        header("Content-Type: text/plain");
        echo(OW::getConfig()->getValue('oaseo', OASEO_BOL_Service::CNF_ROBOTS_CONTENTS));
        exit;
    }

    private function processMetaInfo()
    {
        /* hotfix - add static pages for ajax */
        $staticDocs = BOL_NavigationService::getInstance()->findAllStaticDocuments();
        $staticPageDispatchAttrs = OW::getRequestHandler()->getStaticPageAttributes();

        /* @var $value BOL_Document */
        foreach ( $staticDocs as $value )
        {
            OW::getRouter()->addRoute(new OW_Route($value->getKey(), $value->getUri(), $staticPageDispatchAttrs['controller'], $staticPageDispatchAttrs['action'], array('documentKey' => array(OW_Route::PARAM_OPTION_HIDDEN_VAR => $value->getKey()))));
        }
        /* ------------------------------ */

        $language = OW::getLanguage();

        // edit route path
        if ( !empty($_POST['routeName']) && !empty($_POST['url']) )
        {
            $urlUpdArr = json_decode($_POST['url'], true);

            // suuport only latin chars and '-' symbol
            if ( preg_match("/[^a-zA-Z0-9\-]+/", implode('', $urlUpdArr)) )
            {
                throw new Exception(OW::getLanguage()->text('oaseo', 'use_only_latin_for_urls'));
            }

            foreach ( $urlUpdArr as $item )
            {
                if ( strlen($item) < 1 )
                {
                    throw new Exception(OW::getLanguage()->text('oaseo', 'url_empty_path_element'));
                }
            }

            $route = OW::getRouter()->getRoute($_POST['routeName']);

            if ( $route !== null )
            {
                $urlDto = $this->metaService->findUrlByRouteName($route->getRouteName());

                $rtArr = (array) $route;
                $path = empty($urlDto) ? $rtArr["\0OW_Route\0routePath"] : $urlDto->getUrl();

                $pathArr = explode('/', $path);
                $pathUpdate = false;

                foreach ( $pathArr as $pathKey => $pathItem )
                {
                    if ( strstr($pathItem, ':') )
                    {
                        continue;
                    }

                    $currPathItem = array_shift($urlUpdArr);

                    if ( $pathItem != $currPathItem )
                    {
                        $pathArr[$pathKey] = $currPathItem;
                        $pathUpdate = true;
                    }
                }

                if ( $pathUpdate )
                {
                    if ( $urlDto === null )
                    {
                        $urlDto = new OASEO_BOL_Url();
                        $urlDto->setRouteName($route->getRouteName());
                    }

                    $urlDto->setUrl(implode('/', $pathArr));
                    $this->metaService->saveUrl($urlDto);
                }
            }
        }

        // edit meta info
        $entryArr = array();

        if ( !empty($_POST['title']) )
        {
            $entryArr['title'] = trim($_POST['title']);
        }

        if ( !empty($_POST['desc']) )
        {
            $entryArr['desc'] = trim($_POST['desc']);
        }

        if ( !empty($_POST['keywords']) )
        {
            $entryArr['keywords'] = $_POST['keywords'];
        }

        if ( !empty($entryArr) )
        {
            /* spec case for empty URI */
            if ( in_array(trim($_POST['uri']), array('', '/')) )
            {
                $item = BOL_NavigationService::getInstance()->findFirstLocal((OW::getUser()->isAuthenticated() ? BOL_NavigationService::VISIBLE_FOR_MEMBER : BOL_NavigationService::VISIBLE_FOR_GUEST));

                if ( $item !== null )
                {
                    if ( $item->getRoutePath() )
                    {
                        $route = OW::getRouter()->getRoute($item->getRoutePath());
                        $dispatchAttrs = $route->getDispatchAttrs();
                    }
                    else
                    {
                        $dispatchAttrs = OW::getRequestHandler()->getStaticPageAttributes();
                    }
                }
                else
                {
                    $dispatchAttrs = array('controller' => 'BASE_CTRL_ComponentPanel', 'action' => 'index');
                }
            }
            /* ------------------------- */
            else
            {
                $dispatchAttrs = $this->metaService->getDispatchParamsForUri(trim($_POST['uri']));
            }

            $entry = $this->metaService->getEntryForDispatchParams($dispatchAttrs);
           
            
            if ( $entry === null )
            {
                $entry = new OASEO_BOL_Meta();
                $entry->setKey($this->metaService->generateKey($dispatchAttrs));
                $entry->setUri(trim($_POST['uri']));
                $entry->setDispatchAttrs(json_encode($dispatchAttrs));
            }

            $entry->setMeta(json_encode($entryArr));
            $this->metaService->saveEntry($entry);
        }
    }

    public function xmlSitemap()
    {
        if ( file_exists($this->metaService->getSiteMapPath()) )
        {
            header("Content-Type: text/xml");
            exit(file_get_contents($this->metaService->getSiteMapPath()));
        }
        else
        {
            header("HTTP/1.0 404 Not Found");
            exit('Sitemap generation in progress...');
        }
    }

    public function xmlImageSitemap()
    {
        if ( file_exists($this->metaService->getImageMapPath()) )
        {
            header("Content-Type: text/xml");
            exit(file_get_contents($this->metaService->getImageMapPath()));
        }
        else
        {
            header("HTTP/1.0 404 Not Found");
            exit('Sitemap generation in progress...');
        }
    }
    
    public function xmlSitemapGz()
    {
        if ( file_exists($this->metaService->getSiteMapPath(true)) )
        {
            header("Content-Type: text/xml");
            header("Content-Encoding: gzip");
            exit(file_get_contents($this->metaService->getSiteMapPath(true)));
        }
        else
        {
            header("HTTP/1.0 404 Not Found");
            exit('Sitemap generation in progress...');
        }
    }

    public function xmlImageSitemapGz()
    {
        if ( file_exists($this->metaService->getImageMapPath(true)) )
        {
            header("Content-Encoding: gzip");
            header("Content-Type: text/xml");
            exit(file_get_contents($this->metaService->getImageMapPath(true)));
        }
        else
        {
            header("HTTP/1.0 404 Not Found");
            exit('Sitemap generation in progress...');
        }
    }
}