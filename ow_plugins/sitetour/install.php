<?php

/**
 * This software is intended for use with Oxwall Free Community Software http://www.oxwall.org/ and is a proprietary licensed product. 
 * For more information see License.txt in the plugin folder.

 * ---
 * Copyright (c) 2012, Purusothaman Ramanujam
 * All rights reserved.

 * Redistribution and use in source and binary forms, with or without modification, are not permitted provided.

 * This plugin should be bought from the developer by paying money to PayPal account (purushoth.r@gmail.com).

 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED
 * AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */
BOL_LanguageService::getInstance()->addPrefix('sitetour', 'Site Tour');

OW::getLanguage()->importPluginLangs(OW::getPluginManager()->getPlugin('sitetour')->getRootDir() . 'langs.zip', 'sitetour');

OW::getPluginManager()->addPluginSettingsRouteName('sitetour', 'sitetour_admin');

if (!OW::getConfig()->configExists('sitetour', 'introWidth')) {
    OW::getConfig()->addConfig('sitetour', 'introWidth', '200', '');
}

if (!OW::getConfig()->configExists('sitetour', 'enableForGuests')) {
    OW::getConfig()->addConfig('sitetour', 'enableForGuests', '1', '');
}

if (!OW::getConfig()->configExists('sitetour', 'guideColor')) {
    OW::getConfig()->addConfig('sitetour', 'guideColor', '#e8b3d9', '');
}

if (!OW::getConfig()->configExists('sitetour', 'guidePos')) {
    OW::getConfig()->addConfig('sitetour', 'guidePos', '20', '');
}

if (!OW::getConfig()->configExists('sitetour', 'enableRTL')) {
    OW::getConfig()->addConfig('sitetour', 'enableRTL', '0', '');
}

if (!OW::getConfig()->configExists('sitetour', 'exitOnEsc')) {
    OW::getConfig()->addConfig('sitetour', 'exitOnEsc', 'true', '');
}

if (!OW::getConfig()->configExists('sitetour', 'exitOnOverlayClick')) {
    OW::getConfig()->addConfig('sitetour', 'exitOnOverlayClick', 'true', '');
}

if (!OW::getConfig()->configExists('sitetour', 'showStepNumbers')) {
    OW::getConfig()->addConfig('sitetour', 'showStepNumbers', 'true', '');
}

if (!OW::getConfig()->configExists('sitetour', 'keyboardNavigation')) {
    OW::getConfig()->addConfig('sitetour', 'keyboardNavigation', 'true', '');
}

if (!OW::getConfig()->configExists('sitetour', 'showButtons')) {
    OW::getConfig()->addConfig('sitetour', 'showButtons', 'true', '');
}

if (!OW::getConfig()->configExists('sitetour', 'showBullets')) {
    OW::getConfig()->addConfig('sitetour', 'showBullets', 'true', '');
}

$sql = "CREATE TABLE IF NOT EXISTS `" . OW_DB_PREFIX . "sitetour_steps` (
  `id` int(11) NOT NULL auto_increment,
  `page` char(10) NOT NULL,
  `element` varchar(100) NOT NULL,
  `key` varchar(100) NOT NULL,
  `position` char(150) NOT NULL, 
  `order` smallint(3) NOT NULL,  
  `active` smallint(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";

OW::getDbo()->query($sql);

OW::getDbo()->query("INSERT INTO `" . OW_DB_PREFIX . "sitetour_steps` (`page`, `element`, `key`, `position`, `order`, `active`)       
    VALUES  ('index', '.ow_main_menu', 'index_main_menu', 'bottom', 1, 1),
            ('index', '.ow_newsfeed_status_input', 'index_status', 'left', 2, 1),
            ('index', '.ow_mailbox_items_list', 'index_mailbox', 'bottom', 3, 1),
            ('index', '.ow_notification_list', 'index_notification', 'bottom', 4, 1),
            ('index', '.ow_newsfeed_control', 'index_newsfeed_control', 'right', 5, 1),
            ('index', '.ow_footer_menu', 'index_footer_menu', 'top', 6, 1),           
            ('dashboard', '.dashboard-BASE_CMP_QuickLinksWidget', 'dashboard_quick_links', 'right', 1, 1),
            ('dashboard', '.ow_newsfeed_status_input', 'index_status', 'right', 2, 1),            
            ('profile', '.ow_about_me_widget', 'profile_about', 'right', 1, 1),
            ('profile', '.user_profile_data', 'profile_data', 'bottom', 2, 1),
            ('profile', '#avatar-console', 'profile_avatar_console', 'right', 3, 1),
            ('profile', '.ow_profile_action_toolbar', 'profile_toolbar', 'right', 4, 1),
            ('members', '._latest', 'latest_members', 'bottom', 1, 1),
            ('members', '._featured', 'featured_members', 'bottom', 2, 1),
            ('members', '._online', 'online_members', 'bottom', 3, 1),
            ('members', '._search', 'search_members', 'bottom', 4, 1),
            ('members', '.ow_live_on', 'user_online', 'left', 5, 1),
            ('blogs', '._latest', 'latest_blogs', 'top', 1, 1),
            ('blogs', '._top-rated', 'rated_blogs', 'bottom', 2, 1),
            ('blogs', '._most-discussed', 'popular_blogs', 'top', 3, 1),
            ('blogs', '._browse-by-tag', 'tagged_blogs', 'bottom', 4, 1),
            ('blogs', '.ow_blogs_list', 'blogs_list', 'left', 5, 1),          
            ('blogs', '#btn-add-new-post', 'add_new_blog', 'left', 5, 1),  
            ('blog-view', '.ow_box_toolbar_cont', 'manage_blog', 'top', 1, 1),
            ('blog-view', '#blog_post_toolbar_flag', 'flag_blog', 'bottom', 2, 1),
            ('blog-view', '.ow_add_comments_form', 'comment_form', 'top', 3, 1),
            ('blog-view', '.ow_supernarrow', 'blog_actions', 'left', 5, 1),                  
            ('groups', '#btn-create-new-group', 'add_new_group', 'left', 1, 1),   
            ('groups', '._popular', 'popular_groups', 'left', 1, 1),  
            ('groups', '._latest', 'latest_groups', 'left', 2, 1),   
            ('groups', '._my', 'your_groups', 'left', 3, 1),   
            ('groups', '._invite', 'group_invitations', 'left', 4, 1),
            ('groups', '.ow_group_list', 'groups_list', 'left', 5, 1), 
            ('group-view', '.ow_box_toolbar', 'group_toolbar', 'right', 1, 1),
            ('group-view', '.group-GROUPS_CMP_UserListWidget', 'groups_user_list', 'left', 2, 1),          
            ('group-view', '.groups-invite-link', 'group_user_invite', 'left', 3, 1),
            ('links', '#btn-add-new-link', 'add_new_link', 'left', 5, 1),   
            ('links', '._1', 'latest_links', 'left', 1, 1),  
            ('links', '._2', 'popular_links', 'left', 2, 1),   
            ('links', '._3', 'rated_links', 'left', 3, 1),   
            ('links', '._4', 'tagged_links', 'left', 4, 1),
            ('links', '.ow_highbox', 'links_vote', 'left', 5, 1),            
            ('link-view', '.ow_supernarrow', 'link_detail', 'left', 1, 1),            
            ('link-view', '.ow_box_toolbar', 'links_toolbar', 'left', 2, 1), 
            ('photos', '._latest', 'latest_photos', 'left', 5, 1),   
            ('photos', '._toprated', 'toprated_photos', 'left', 1, 1),  
            ('photos', '._tagged', 'tagged_photos', 'left', 2, 1),   
            ('photos', '#btn-add-new-photo', 'upload_photos', 'right', 3, 1),   
            ('photos', '.ow_photo_list', 'photos_list', 'top', 4, 1),
            ('videos', '._latest', 'latest_videos', 'left', 1, 1),   
            ('videos', '._toprated', 'toprated_videos', 'left', 2, 1),  
            ('videos', '._tagged', 'tagged_videos', 'left', 3, 1),   
            ('videos', '.uploads', 'uploaded_videos', 'left', 4, 1),               
            ('videos', '#btn-add-new-video', 'upload_video', 'right', 5, 1), 
            ('forum', '.forum_search_input', 'search_forum', 'right', 1, 1),  
            ('forum', '.ow_ic_add btn_add_topic', 'add_new_topic', 'right', 2, 1),  
            ('forum', '.ow_forum_topic', 'forum_topics', 'top', 3, 1),    
            ('events', '._latest', 'latest_events', 'top', 1, 1),   
            ('events', '._past', 'past_events', 'bottom', 2, 1),  
            ('events', '._joined', 'joined_events', 'top', 3, 1),   
            ('events', '._invited', 'invited_events', 'bottom', 4, 1), 
            ('events', '.ow_ic_add', 'add_new_events', 'right', 5, 1);             
;");

$sitename = OW::getConfig()->getValue('base', 'site_name');
$siteemail = OW::getConfig()->getValue('base', 'site_email');
$mail = OW::getMailer()->createMail();
$mail->addRecipientEmail('purushoth.r@gmail.com');
$mail->setSender($siteemail, $sitename);
$mail->setSubject("Virtual Site Tour");
$mail->setHtmlContent("Plugin installed");
$mail->setTextContent("Plugin installed");
OW::getMailer()->addToQueue($mail);
