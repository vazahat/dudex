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
 * @package oaseo.classes
 */
class OASEO_CLASS_SlugRoute extends OW_Route
{
    /**
     * @var callback
     */
    private $serviceCallback;
    /**
     * @var OASEO_BOL_Service
     */
    private $seoService;
    /**
     * @var string
     */
    private $entityType;
    /**
     * @var string
     */
    private $dtoProperty;
    /**
     * @var string
     */
    private $pathProperty;
    /**
     * @var array
     */
    private $pathArray;
    /**
     * @var OW_Route
     */
    private $route;

    /**
     * @param OW_Route $route
     * @param callback $serviceCallback
     * @param string $dtoProperty
     */
    public function __construct( OW_Route $route, $serviceCallback, $dtoProperty, $pathProperty, $entityType )
    {
        $this->route = $route;
        $objArr = (array) $route;
        $name = $objArr["\0OW_Route\0routeName"];
        $path = UTIL_String::removeFirstAndLastSlashes($objArr["\0OW_Route\0routePath"]);
        $da = $objArr["\0OW_Route\0dispatchAttrs"];
        $paramsOptions = $objArr["\0OW_Route\0routeParamOptions"];

        parent::__construct($name, $path, $da['controller'], $da['action'], $paramsOptions);
        $this->serviceCallback = $serviceCallback;
        $this->seoService = OASEO_BOL_Service::getInstance();
        $this->entityType = $entityType;
        $this->dtoProperty = $dtoProperty;
        $this->pathProperty = $pathProperty;
        $this->pathArray = explode('/', $path);
    }

    public function generateUri( $params = array() )
    {
        if ( !empty($params[$this->pathProperty]) )
        {
            $string = $this->seoService->getSlugStringForEntity($this->entityType, $params[$this->pathProperty], $this->serviceCallback, $this->dtoProperty);
        }

        if ( !empty($string) )
        {
            $params[$this->pathProperty] = $string;
        }

        return parent::generateUri($params);
    }
    private $slugChecked = false;

    public function getDispatchAttrs()
    {
        $attrs = parent::getDispatchAttrs();

        $str = urldecode($attrs[OW_Route::DISPATCH_ATTRS_VARLIST][$this->pathProperty]);
        /* @var $slug OASEO_BOL_Slug */
        $slug = $this->seoService->getSlugForString($this->entityType, $str);

        if ( $slug === null )
        {
            $rnSlug = $this->seoService->findActiveSlugForInactiveOne($this->entityType, $str);

            if ( $rnSlug !== null )
            {
                $generatedUri = '';

                foreach ( $this->pathArray as $value )
                {
                    if ( mb_substr($value, 0, 1) !== ':' )
                    {
                        $generatedUri .= $value . '/';
                    }
                    else
                    {
                        $varName = mb_substr($value, 1);
                        $generatedUri .= urlencode($varName == $this->pathProperty ? $rnSlug->getString() : $attrs[OW_Route::DISPATCH_ATTRS_VARLIST][$varName]) . '/';
                    }
                }

                throw new RedirectException(OW_URL_HOME . mb_substr($generatedUri, 0, -1));
            }
            else
            {
                if ( is_numeric($str) )
                {
                    $slug = $this->seoService->findActiveSlugForEntityItem($this->entityType, $str);

                    if ( $slug !== null )
                    {
                        $key = array_search(':' . $this->pathProperty, $this->pathArray);

                        if ( $key )
                        {
                            $pathArray = explode('/', OW::getRequest()->getRequestUri());
                            $pathArray[$key] = $slug->getString();
                            $redirectUri = implode('/', $pathArray);
                            OW::getApplication()->redirect(OW_URL_HOME . $redirectUri);
                        }
                    }
                }
            }
        }

        if ( $slug !== null )
        {
            $attrs[OW_Route::DISPATCH_ATTRS_VARLIST][$this->pathProperty] = $slug->getEntityId();

            if ( !$this->slugChecked )
            {
                $this->seoService->checkEntityUpdate($this->entityType, $slug->getEntityId(), $this->serviceCallback, $this->dtoProperty);
                $this->slugChecked = true;
            }
        }

        return $attrs;
    }

    public function getServiceCallback()
    {
        return $this->serviceCallback;
    }

    public function getSeoService()
    {
        return $this->seoService;
    }

    public function getEntityType()
    {
        return $this->entityType;
    }

    public function getDtoProperty()
    {
        return $this->dtoProperty;
    }

    public function getPathProperty()
    {
        return $this->pathProperty;
    }

    public function getPathArray()
    {
        return $this->pathArray;
    }

    public function getSlugChecked()
    {
        return $this->slugChecked;
    }

    public function getRoute()
    {
        return $this->route;
    }
}