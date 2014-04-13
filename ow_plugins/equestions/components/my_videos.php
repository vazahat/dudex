<?php

class EQUESTIONS_CMP_MyVideos extends OW_Component
{
    public function __construct( $videos )
    {
        parent::__construct();

        $this->assign('list', $videos);
    }
}