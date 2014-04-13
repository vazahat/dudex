<?php

/**
 * Copyright (c) 2012, Sergey Kambalin
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * @author Sergey Kambalin <greyexpert@gmail.com>
 * @package attachments.components
 */
class ATTACHMENTS_CMP_YoutubeList extends OW_Component
{
    private $response;

    public function __construct( $response )
    {
        parent::__construct();

        $this->response = $response;
    }

    public function getItemsCount()
    {
        return count($this->response['feed']['entry']);
    }

    public function onBeforeRender()
    {
        parent::onBeforeRender();

        $tplList = array();

        foreach ( $this->response['feed']['entry'] as $item )
        {
            $vid = $item['media$group']['yt$videoid']['$t'];
            $uploaded = strtotime($item['media$group']['yt$uploaded']['$t']);
            $duration = $item['media$group']['yt$duration']['seconds'];
            $description = UTIL_String::truncate($item['media$group']['media$description']['$t'], 130, ' ...');
            $title = UTIL_String::truncate($item['media$group']['media$title']['$t'], 65, ' ...');
            $thumb = $item['media$group']['media$thumbnail'][0]['url'];
            $image = $item['media$group']['media$thumbnail'][1]['url'];

            $oembed = array(
                'thumbnail_url' => $image,
                'type' => 'video',
                'title' => $title,
                'description' => $description,
                'html' => '<iframe class="attp-yt-iframe" width="300" height="230" src="http://www.youtube.com/embed/' . $vid . '?autoplay=1" frameborder="0" allowfullscreen></iframe>'
            );

            $tplList[] = array
            (
                'title' => $title,
                'description' => $description,
                'thumb' => $thumb,
                'video' => $vid,
                'duration' => round($duration / 60),
                'uploaded' => $uploaded,
                'date' => UTIL_DateTime::formatDate($uploaded),
                'oembed' => json_encode($oembed)
            );
        }

        $this->assign('list', $tplList);
    }
}
