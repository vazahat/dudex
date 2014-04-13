<?php

require_once dirname(dirname(__FILE__)) . DS . 'plugin.php';

$plugin = OW::getPluginManager()->getPlugin('attachments');

if ( !class_exists('BASE_CMP_OembedAttachment', false ) )
{
    include_once $plugin->getCmpDir() . 'oembed_attachmet.php';
}

