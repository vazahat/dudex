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
OW::getAutoloader()->addClass('OA_CCLASS_TagsField', OW::getPluginManager()->getPlugin('oaseo')->getClassesDir() . 'tags_field.php');
$router = OW::getRouter();
$router->addRoute(new OW_Route('oaseo.admin_index', 'oaseo/admin', 'OASEO_CTRL_Admin', 'index'));
$router->addRoute(new OW_Route('oaseo.admin_advanced', 'oaseo/admin/advanced', 'OASEO_CTRL_Admin', 'advanced'));
$router->addRoute(new OW_Route('oaseo.admin_slugs', 'oaseo/admin/slugs', 'OASEO_CTRL_Admin', 'slugs'));
$router->addRoute(new OW_Route('oaseo.admin_robots', 'oaseo/admin/robots', 'OASEO_CTRL_Admin', 'robots'));
$router->addRoute(new OW_Route('oaseo.admin_sitemap', 'oaseo/admin/sitemap', 'OASEO_CTRL_Admin', 'sitemap'));
$router->addRoute(new OW_Route('oaseo.admin_sitemap_info', 'oaseo/admin/sitemap-info', 'OASEO_CTRL_Admin', 'sitemapInfo'));

if ( $router->getRoute('base.robots_txt') )
{
    $router->removeRoute('base.robots_txt');
}

$router->addRoute(new OW_Route('oaseo.robots', 'robots.txt', 'OASEO_CTRL_Base', 'robots'));
$router->addRoute(new OW_Route('oaseo.xmlsitemap', ( OW::getConfig()->getValue('oaseo', 'sitemap_url') ? trim(OW::getConfig()->getValue('oaseo', 'sitemap_url')) : 'sitemap.xml' ), 'OASEO_CTRL_Base', 'xmlSitemap'));
$router->addRoute(new OW_Route('oaseo.xmlimagesitemap', ( OW::getConfig()->getValue('oaseo', 'imagemap_url') ? trim(OW::getConfig()->getValue('oaseo', 'imagemap_url')) : 'sitemap_images.xml' ), 'OASEO_CTRL_Base', 'xmlImageSitemap'));
$router->addRoute(new OW_Route('oaseo.xmlsitemapgz', ( OW::getConfig()->getValue('oaseo', 'sitemap_url') ? 'gz'.trim(OW::getConfig()->getValue('oaseo', 'sitemap_url')) : 'gzsitemap.xml' ), 'OASEO_CTRL_Base', 'xmlSitemapGz'));
$router->addRoute(new OW_Route('oaseo.xmlimagesitemapgz', ( OW::getConfig()->getValue('oaseo', 'imagemap_url') ? 'gz'.trim(OW::getConfig()->getValue('oaseo', 'imagemap_url')) : 'gzsitemap_images.xml' ), 'OASEO_CTRL_Base', 'xmlImageSitemapGz'));

//output handler
function oaseo_handler()
{
    $language = OW::getLanguage();
    $document = OW::getDocument();
    $service = OASEO_BOL_Service::getInstance();
    $dispatchAttrs = OW::getDispatcher()->getDispatchAttributes();

    //$key = $service->generateKeyWithGet($dispatchAttrs, $_GET);

    $params = $service->getEntryForDispatchParams($dispatchAttrs);
    $params = empty($params) ? array() : json_decode($params->getMeta(), true);

    if ( isset($params['title']) )
    {
        $document->setTitle($params['title']);
    }
    else
    {
        $document->setTitle($language->text('oaseo', 'page_default_title', array('defaultTitle' => $document->getTitle())));
    }

    if ( isset($params['desc']) )
    {
        $document->setDescription($params['desc']);
    }
    else
    {
        $document->setDescription($language->text('oaseo', 'page_default_desc', array('defaultDesc' => $document->getDescription())));
    }

    if ( isset($params['keywords']) )
    {
        $document->setKeywords(implode(', ', $params['keywords']));
    }
    else
    {
        $document->setKeywords($language->text('oaseo', 'page_default_keywords', array('defaultKeywords' => $document->getKeywords())));
    }
}
OW::getEventManager()->bind(OW_EventManager::ON_BEFORE_DOCUMENT_RENDER, 'oaseo_handler');

// add frontend form
function oaseo_form_add()
{
    $document = OW::getDocument();
    
    if ( $document->getMasterPage() === null || $document->getMasterPage() instanceof ADMIN_CLASS_MasterPage )
    {
        return;
    }
    
    $metaData = array(
        'title' => $document->getTitle(),
        'keywords' => $document->getKeywords(),
        'desc' => $document->getDescription()
    );

    $cmp = new OASEO_CMP_MetaEdit($metaData, OW::getRequest()->getRequestUri());
    $document->prependBody($cmp->render());
}
// check if admin is logged in
if ( OASEO_BOL_Service::getInstance()->isAdmin() )
{
    OW::getEventManager()->bind(OW_EventManager::ON_BEFORE_DOCUMENT_RENDER, 'oaseo_form_add');
}

function oaseo_add_slugs( OW_Event $e )
{
    // add slugs
    OASEO_BOL_Service::getInstance()->initSlugs();

    // init urls
    OASEO_BOL_Service::getInstance()->initUrls();
}

OW::getEventManager()->bind(OW_EventManager::ON_PLUGINS_INIT, 'oaseo_add_slugs');

// add custom floatbox
function oaseo_add_floatbox( OW_Event $e )
{
    OW::getDocument()->addScriptDeclaration("
    if(typeof OA_FloatBox != 'function'){
        OA_FloatBox = function(options){
            var fb = new OW_FloatBox(options);
            fb.\$container.addClass('oafb');
            fb.bind('close', function(){this.\$container.removeClass('oafb');});
            return fb;
        };
    }

    if(typeof OA_AjaxFloatBox != 'function'){
        OA_AjaxFloatBox = function(cmpClass, params, options){
            var fb = OW.ajaxFloatBox(cmpClass, params, options);
            fb.\$container.addClass('oafb');
            fb.bind('close', function(){this.\$container.removeClass('oafb');});
            return fb;
        };
    }
    ");

    OW::getDocument()->addStyleDeclaration("
        .oafb .ow_box_cap_empty, .oafb .ow_box_cap_right, .oafb .ow_box_cap_body{
            background:none !important;
            margin-bottom:0 !important;
        }

        .oafb .ow_bg_color, .oafb .floatbox_body{
            background:white !important;
            padding:5px 5px 0 5px !important;
        }

        .oafb .floatbox_bottom{
            padding:0 !important;
            height: 0 !important;
        }

        .oafb td, .oafb label{
            border-color:#fff !important;
            color: #333333 !important;
        }

        .oafb .floatbox_header{
            padding-left:120px !important;
            background:#7984A0 url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAHcAAAAeCAYAAAAWwoEYAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyBpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNSBXaW5kb3dzIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjY0MUFBNDE3RkNEMjExRTA5RUJEODIyQzNGOUJGMUFGIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOjY0MUFBNDE4RkNEMjExRTA5RUJEODIyQzNGOUJGMUFGIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6NjQxQUE0MTVGQ0QyMTFFMDlFQkQ4MjJDM0Y5QkYxQUYiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6NjQxQUE0MTZGQ0QyMTFFMDlFQkQ4MjJDM0Y5QkYxQUYiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz7MWKN9AAAQN0lEQVR42uxbB1RVVxb9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) no-repeat 5px 2px !important;
            height:22px !important;
        }

        .oafb .floatbox_cap h3.floatbox_title{
            color:#fff !important;
            text-shadow:none !important;
            background:none !important;
            padding-left:5px !important;
            padding-top:4px !important;
            font:14px Arial !important;
            letter-spacing:1px !important;
            float: left !important;
        }

        .oafb .floatbox_header .ow_box_cap_icons{
            padding:0px !important;
            float:right !important;
            margin: 0 !important;
        }

        .oafb .floatbox_cap a.close{
            background-image:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAC20lEQVR4nIXT22+TdRzH8fdzaPu0qxuluPXgutGudGyMygBhmxqTekxquJu3hhsN7M8xxCvjreOK+CQazG5MBmpkHggERhE3V9qHre22p6fn9KsXg0pi0M/l9/W9+eXz/Uk8TXRhqXBucmQZ4Md7xmJt9bMVnsuLXAWIzi8VFqZj+ux0VgNQFUlf7S0VazcOlp75qRMT/3I1On+5MDc1rL91fkabnZkEYPAlTRNC6De5XASYmxrW3zx3Ups9kUWSJIbCwb6rp7PR5ZlcWsscHePP8g4AxzLjOK6nCSF0gDfOTmu5zBiblToA6fEUT2r7mu24y6oQPTqWTdloYLa62I5LdWeP0WSC1894Wq8nkUwmuVMq47gefp+K2erSsWyE6KGulWqLiozeo6fFY3Ea+20sx8Womxw/GgPg1t0NbMcl4FM5EgmzXnrE+h8b3bVSbVECODx3qXBmIqJnxlPaYCRK+UmDTtdBUWQAPE8Q1HwkhiPs1Xd4tPFX9+dSo1i/eWVFelZT5PylwumJQ3rqlYTmDw1x+0G5X6EiSxxPJ7Dau2yVK91bpd1i44cr/9R4kB5CCPyqSn2vRXV7ry+yIhE7MsThoA8hBNDrmwRw6LVPCydTQT2XzWhtT+W7G3dodWzUp09wPcFA0M8789OEFJf7Dx52f9/sFHd/+nxFGjr7ydtTceXryWMZremqXF+9TattMRAKkM+lAPjt/mZ/9u7CDGHV5d76w+7divehnI64V7PpUa1pw/Xv12iaJgMBmXw2QZAWQVrkswkGAjJN0zzYsSGbHtXSEfeq7DqW1DSbGNs1EA5hTSGfS+L3TIzHW5bxeMvyeyb5XJKwpoBwMLZrNM0mrmNJUnj24ntjof1rI7GRgCOHAPCJNkbVsDbagxcA/sslgPCpi+8n/dvX4vGYH6BSqdpl++ULzV+++Pb/vH8HoVc//mBErn4FYIjYR+1fv/zm+e/8Iv8bpbJoDVEV4SwAAAAASUVORK5CYII=) !important;
            background-color:transparent !important;
        }

        .oafb{
            border:12px solid #1e212a !important;
            color: #333333 !important;
        }

        .oafb span.ow_button{
            background:none !important;
            border:none !important;
        }

        .oafb input[type=button], .oafb input[type=submit]{
            line-height: 24px !important;
            color: #effaff !important;
            padding: 5px 8px !important;
            padding-left:8px !important;
            text-align: center !important;
            -webkit-border-radius: 5px !important;
            -moz-border-radius: 5px !important;
            border-radius: 5px !important;
            background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAQAAAAsCAIAAADNbI5yAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAEZJREFUeNpizKxuYoABlhu37yM4/xn+I3H+I3GQJNBkUDk4Dfj/7z8xBvz7/48oe/4Rp4deymhnD1mhQ2TAM/wn1TSAAAMAPtl9FN4Gn0oAAAAASUVORK5CYII=) 0 0 !important;
            -webkit-box-shadow:1px 1px 2px rgba(0,0,50,.5) !important;
            -moz-box-shadow:1px 1px 2px rgba(0,0,50,.5) !important;
            box-shadow:1px 1px 2px rgba(0,0,50,.5) !important;
            border:none !important;
            cursor:pointer !important;
            text-shadow:none !important;
            font-weight:normal !important;
            padding-right:10px !important;
            font-family:Arial !important;
            letter-spacing:1px !important;
        }

        html body.ow .oafb span.ow_button span{
            background:none !important;
        }

        body.ow .oafb input.ow_inprogress{
            background:#334853 url(data:image/gif;base64,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) no-repeat 50% 50% !important;
            color:transparent !important;
        }

        .oafb ul.oa_tags_field li.tag{
            background:#EEF0F5 !important;
            border:1px solid ##EEF0F5 !important;
        }

        .oa_urledit_field{
            border:1px solid #ABADB3 !important;
            border-color: #ABADB3 #CED1DA #CED1DA #ABADB3 !important;
            background:#fff !important;
            width:722px !important;
            padding:4px !important;
            cursor:text !important;
        }

        div.oa_urledit_field input[type=text]{
            border:none !important;            
            padding:0 !important;
            background:none !important;
        }

        .oa_urledit_field span.cst_nedit{
            color:red !important;
        }
        ");
}

if ( OASEO_BOL_Service::getInstance()->isAdmin() )
{
    OW::getEventManager()->bind(OW_EventManager::ON_BEFORE_DOCUMENT_RENDER, 'oaseo_add_floatbox');
}