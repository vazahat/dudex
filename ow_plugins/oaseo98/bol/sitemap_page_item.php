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
class OASEO_BOL_SitemapPageItem extends OW_Entity
{
    /**
     * @var string
     */
    public $pageId;
    /**
     * @var string
     */
    public $itemId;
    /**
     * @var string
     */
    public $type;

    public function getPageId()
    {
        return $this->pageId;
    }

    public function setPageId( $pageId )
    {
        $this->pageId = $pageId;
    }

    public function getItemId()
    {
        return $this->itemId;
    }

    public function setItemId( $itemId )
    {
        $this->itemId = $itemId;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType( $type )
    {
        $this->type = $type;
    }
}

