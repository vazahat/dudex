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
 * @package equestions.components
 */
class EQUESTIONS_CMP_UserList extends BASE_CMP_FloatboxUserList
{
    public function __construct($optionId, $hiddenUsers)
    {
        $service = EQUESTIONS_BOL_Service::getInstance();
        $userIds = $service->findAnsweredUserIdList($optionId);

        $userIds = array_diff($userIds, $hiddenUsers);

        parent::__construct($userIds);

        $this->setTemplate(OW::getPluginManager()->getPlugin('base')->getCmpViewDir() . 'floatbox_user_list.html');
    }
}