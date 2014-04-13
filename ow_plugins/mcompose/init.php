<?php

/**
 * Copyright (c) 2012, Sergey Kambalin
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

require 'plugin.php';

MCOMPOSE_Plugin::getInstance()->init();
MCOMPOSE_CLASS_BaseBridge::getInstance()->init();
MCOMPOSE_CLASS_FriendsBridge::getInstance()->init();
MCOMPOSE_CLASS_GroupsBridge::getInstance()->init();
MCOMPOSE_CLASS_EventsBridge::getInstance()->init();
