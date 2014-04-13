<?php

OW::getRouter()->addRoute(new OW_Route('credits_logs', 'your-credits/history/:type', "CREDITS_MCTRL_Action", 'logs'));
OW::getRouter()->addRoute(new OW_Route('credits_logs_default', 'your-credits/history/all', "CREDITS_MCTRL_Action", 'logs'));
OW::getRouter()->addRoute(new OW_Route('credits_admin_logs', 'admin/credits/all-logs', "CREDITS_MCTRL_Action", 'adminlogs'));
OW::getRouter()->addRoute(new OW_Route('credits_transfer', 'your-credits/transfer', "CREDITS_MCTRL_Action", 'transfer'));

CREDITS_MCLASS_EventHandler::getInstance()->init();