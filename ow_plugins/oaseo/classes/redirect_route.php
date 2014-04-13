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
class OASEO_CLASS_RedirectRoute extends OW_Route
{
    private $newUrl;

    public function getNewUrl()
    {
        return $this->newUrl;
    }

    public function setNewUrl( $newUrl )
    {
        $this->newUrl = $newUrl;
    }

    public function match( $uri )
    {
        if ( parent::match($uri) )
        {
            if( !strstr($this->newUrl, ':') )
            {
                $redirectUri = $this->newUrl;
            }
            else
            {
                $uriArr = explode('/', $uri);
                $newUrlArr = explode('/', $this->newUrl);

                foreach ( $newUrlArr as $key => $item )
                {
                    if( strstr($item, ':') )
                    {
                        $newUrlArr[$key] = $uriArr[$key];
                    }
                }

                $redirectUri = implode('/', $newUrlArr);
            }
            
            throw new RedirectException(OW_URL_HOME.$redirectUri);
        }
    }
}