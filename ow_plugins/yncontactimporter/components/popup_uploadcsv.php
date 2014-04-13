<?php
class YNCONTACTIMPORTER_CMP_PopupUploadcsv extends OW_Component
{
    public function __construct()
    {
    	parent::__construct();
		$this->assign('urlAction', OW::getRouter() -> urlForRoute('yncontactimporter-upload'));
    }
}
