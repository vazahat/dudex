<?php

OW::getRouter()->addRoute(new OW_Route('ivideo_view_list_main', 'uploaded-video', "IVIDEO_MCTRL_Action", 'viewList'));
OW::getRouter()->addRoute(new OW_Route('ivideo_view_list', 'uploaded-video/:type', "IVIDEO_MCTRL_Action", 'viewList'));
OW::getRouter()->addRoute(new OW_Route('ivideo_tag_list', 'uploaded-video/tagged', "IVIDEO_MCTRL_Action", 'taglist'));
OW::getRouter()->addRoute(new OW_Route('ivideo_view_tagged_list', 'uploaded-video/tagged/:tag', "IVIDEO_MCTRL_Action", 'taglist'));
OW::getRouter()->addRoute(new OW_Route('ivideo_view_video', 'uploaded-video/view/:id', "IVIDEO_MCTRL_Action", 'viewvideo'));
OW::getRouter()->addRoute(new OW_Route('ivideo_user_video_list', 'uploaded-video/user/:user', 'IVIDEO_MCTRL_Action', 'viewUserVideoList'));
OW::getRouter()->addRoute(new OW_Route('ivideo_list_category', 'uploaded-video/category', "IVIDEO_MCTRL_Action", 'listCategory'));
OW::getRouter()->addRoute(new OW_Route('ivideo_category_items', 'uploaded-video/category/:category', "IVIDEO_MCTRL_Action", 'listCategoryVideos'));

OW::getThemeManager()->addDecorator('ivideo_list_item', 'ivideo');

OW_ViewRenderer::getInstance()->registerFunction('display_rate', array('BASE_CTRL_Rate', 'displayRate'));