<?php

$rtmp_server = "rtmp://localhost/videowhisper";
// rtmp://your-server-ip-or-domain/application

$rtmp_amf = "AMF3";
// AMF3 : Red5, Wowza, FMIS3, FMIS3.5
// AMF0 : FCS1.5, FMS2
// blank for flash default

$rtmfp_server="rtmfp://stratus.adobe.com/f1533cc06e4de4b56399b10d-1a624022ff71/";
// RTMFP server for negotiangin P2P connections where possible
// Get your own independent developer key/address from: https://www.adobe.com/cfusion/entitlement/index.cfm?e=stratus

$tokenKey = "VideoWhisper";
// This can be used to secure access as configured in RTMP server settings (secureTokenSharedSecret).

$ban_names=Array("ban_name1", "ban_name2");
//ban channel or user names
?>