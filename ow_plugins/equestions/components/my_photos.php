<?php

class EQUESTIONS_CMP_MyPhotos extends OW_Component
{
    public function __construct( $photos )
    {
        parent::__construct();

        $this->assign('photos', $photos);
    }
}