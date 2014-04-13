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
class ATTACHMENTS_CMP_YoutubeSearchResult extends OW_Component
{
    const ITEMS_COUNT = 20;

    private $uniqId, $panelUniqId, $query, $response, $window = array();


    public function __construct( $panelUniqId, $query, $response )
    {
        parent::__construct();

        $this->uniqId = uniqid('ytsearch');
        $this->panelUniqId = $panelUniqId;
        $this->response = $response;
        $this->query = $query;
    }

    public function initJs()
    {
        $data = array(
            'rsp' => OW::getRouter()->urlFor('ATTACHMENTS_CTRL_Attachments', 'rsp'),
            'delegate' => $this->panelUniqId,
            'data' => array(
                'start' => 1,
                'offset' => self::ITEMS_COUNT,
                'query' => $this->query
            )
        );

        $js = UTIL_JsGenerator::newInstance()->newObject('ytList',
                'ATTP.YouTubeList',
                array(
                    $this->uniqId,
                    $data
                ));

        ATTACHMENTS_Plugin::getInstance()->addJs($js);
    }

    public function setWindowOptions( $options )
    {
        $this->window = $options;
    }

    public function onBeforeRender()
    {
        parent::onBeforeRender();

        $this->initJs();

        $noVideo = OW::getLanguage()->text('attachments', 'attacments_yt_no_video');
        $listHtml = '<div class="yt-empty-result ow_nocontent">' . $noVideo . '</div>';
        $itemsCount = 0;

        if ( !empty($this->response) )
        {
            $response = json_decode($this->response, true);

            if ( !empty($response['feed']['entry']) )
            {
                $list = new ATTACHMENTS_CMP_YoutubeList($response);
                $listHtml = $list->render();

                $itemsCount = $list->getItemsCount();
            }
        }

        $this->assign('window', array
        (
            'height' => $this->window['height'] - 300
        ));

        $this->assign('list', $listHtml);
        $this->assign('uniqId', $this->uniqId);
        $this->assign('query', $this->query);
        $this->assign('viewMore', $itemsCount == self::ITEMS_COUNT);
    }
}
