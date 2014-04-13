<?php

/**
 * (c) Alexandr Makarov, 2013
 * notengine@gmail.com
 * 
 */

//define("HIDELINKS_DEBUG","");
 
function hidelinks_replace_callback( $matches )
{
    $parts = parse_url($matches[1]);
    //dont replace local links
    if ( empty($parts["scheme"]) or ($parts["scheme"] == "javascript") )
    {
        return $matches[0];
    }

    //dont replace links if host is the same
    if ( ( $parts["host"] == $_SERVER["HTTP_HOST"] ) or ( $parts["host"] == "www.".$_SERVER["HTTP_HOST"] ) ) 
    {
        return $matches[0];
    }
    
    return str_replace($matches[1], HIDELINKS_BASEURL . "awayto/" . base64_encode($matches[1]), $matches[0]);
}

function hidelinks_global(OW_Event $event)
{
    OW::getDocument()->setBody(preg_replace_callback('/<a.+?href="(.+?)".*?>/si',"hidelinks_replace_callback", OW::getDocument()->getBody()));
}

define("HIDELINKS_BASEURL", OW::getRouter()->getBaseUrl());
OW::getRouter()->addRoute(new OW_Route('hidelinks-awayto', 'awayto/:href', 'HIDELINKS_CTRL_Links', 'awayto'));
OW::getEventManager()->bind('core.finalize', 'hidelinks_global');

?>