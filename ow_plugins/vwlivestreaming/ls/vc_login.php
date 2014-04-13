<?php
//This script controls login and parameters to broadcasting inteface (is called by live_broadcast.swf)

        // initial
        $loggedin = 0;
        $msg = "";
        $userType = 1;

        // get sessionid from cookie
        if (isset ($_COOKIE ['sessionNameLs'])) {
          $sessionName = $_COOKIE ['sessionNameLs'].".vwf";
          $sessionsPath = "sessions/";
          if ($handle = opendir($sessionsPath)) {
              while (false !== ($file = readdir($handle))) {
                if (filectime($sessionsPath.$file) < (time () - 160) && (substr($file,0,3) == 'vls' ) ) 
                {
                  chmod ($sessionsPath.$file, 0666);
                  unlink($sessionsPath.$file);
                } elseif ( $sessionName == $file )
                {
                  $loggedin = 1;
                  $openFile = fopen ($sessionsPath.$file, 'r');
                  while (!feof($openFile) ) {
                    $room_name = fgets($openFile);
                  }
                }
              }
              closedir($handle);
          }
        } else $msg = "Please access from site pages and enable cookies.";

        $params = explode("^", $room_name);
        $room = $params[0];
        $description = $params [1];
        $room_limit = $params [2];
        $welcome = $params [3];
        $welcome2 = $params [4];
        $offlineMessage = $params [5];
        $camWidth = $params [6];
        $camHeight = $params [7];
        $camFPS = $params [8];
        $micRate = $params [9];
        $camBandwidth = $params [10];
        $labelColor = $params [11];
        // $layoutCode = html_entity_decode ($params [12]);
        $layoutCode = $params [12];
        $filterRegex = $params [13];
        $filterReplace = $params [14];
        $floodProtection = $params [15];
        $floodProtection2 = $params [16];
        $permissions = $params [17];
        $user_list = $params [18];
        $moderator_list = $params [19];
        $layoutCode2 = $params [20];
        $rtmp_server = $params [21];
        $rtmp_amf = $params [22];
        $rtmfp_server = $params [23];
        $p2pGroup = $params [24];
        $tokenKey = $params [25];
        $snapshotsTime = $params [26];
        $camMaxBandwidth = $params [27];
        $bufferLive = $params [28];
        $bufferFull = $params [29];
        $bufferLive2 = $params [30];
        $bufferFull2 = $params [31];
        $disableBandwidthDetection = $params [32];
        $limitByBandwidth = $params [33];
        $generateSnapshots = $params [34];
        $externalInterval = $params [35];
        $externalInterval2 = $params [36];
        $ws_ads = $params [37];
        $adsTimeout = $params [38];
        $adsInterval = $params [39];
        $statusInterval = $params [40];

        $username = $params [41];
        $usernameOwner = $params [42];
        $idUser = $params [43];
        $urlRoom = $params [44];
        $baseSwfUrl = $params [45];

        //configure a picture to show when this user is clicked
        $userLink=urlencode($params [46]);
        $userPicture=urlencode($params [47]);

        // 'permission' consists of 
        // showCamSettings:advancedCamSettings:configureSource:onlyVideo:noVideo:noEmbeds:showTimer:writeText:privateTextchat:
        // fillWindow:writeText2:enableVideo:enableChat:enableUsers:fillWindow2:verboseLevel (16)
        $permission = explode("|", $permissions);
        $showCamSettings = $permission [0];
        $advancedCamSettings = $permission [1];
        $configureSource = $permission [2];
        $onlyVideo = $permission [3];
        $noVideo = $permission [4];
        $noEmbeds = $permission [5];
        $showTimer = $permission [6];
        $writeText = $permission [7];
        $privateTextchat = $permission [8];
        $fillWindow = $permission [9];
        $write_text2 = $permission [10];
        $enable_video = $permission [11];
        $enable_chat = $permission [12];
        $enable_users = $permission [13];
        $fillWindow2 = $permission [14];
        $verboseLevel = $permission [15];

        //fill your layout code between <<<layoutEND and layoutEND;
$layoutCode=<<<layoutEND
$layoutCode
layoutEND;
        
        $linkcode=$urlRoom;
        $imagecode=$baseSwfUrl."snapshots/".$room.".jpg";
        $swfurl=$baseSwfUrl."live_watch.swf?n=".$room;
        $swfurl2=$baseSwfUrl."live_video.swf?n=".$room;

$embedcode =<<<EMBEDEND
<object width="640" height="350"><param name="movie" value="$swfurl" /><param name="base" value="$baseSwfUrl" /><param name="allowFullScreen" value="true" /><param name="allowscriptaccess" value="always" /><param name="scale" value="noscale" /><param name="salign" value="lt" /><embed src="$swfurl" base="$baseSwfUrl" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="640" height="350" scale="noscale" salign="lt"></embed></object>
EMBEDEND;
$embedvcode =<<<EMBEDEND2
<object width="320" height="240"><param name="movie" value="$swfurl2" /><param name="base" value="$baseSwfUrl" /><param name="scale" value="exactfit"/><param name="allowFullScreen" value="true" /><param name="allowscriptaccess" value="always" /><embed src="$swfurl2" base="$baseSwfUrl" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="320" height="240" scale="exactfit"></embed></object>
EMBEDEND2;

        // cSettings
        $videoCodec = $params [48];
        $codecProfile = $params [49];
        $codecLevel = $params [50];
        $soundCodec = $params [51];
        $enableRTMP = $params [52];
        $enableP2P = $params [53];
        $supportRTMP = $params [54];
        $supportP2P = $params [55];
        $alwaysRTMP = $params [56];
        $alwaysP2P = $params [57];
        $soundQuality = $params [58];
        
        $username = $room;

?>server=<?=$rtmp_server?>&serverAMF=<?=$rtmp_amf?>&tokenKey=<?=$tokenKey?>&serverRTMFP=<?=$rtmfp_server?>&videoCodec=<?=$videoCodec?>&codecProfile=<?=$codecProfile?>&codecLevel=<?=$codecLevel?>&soundCodec=<?=$soundCodec?>&enableRTMP=<?=$enableRTMP?>&enableP2P=<?=$enableP2P?>&supportRTMP=<?=$supportRTMP?>&supportP2P=<?=$supportP2P?>&alwaysRTMP=<?=$alwaysRTMP?>&alwaysP2P=<?=$alwaysP2P?>&soundQuality=<?=$soundQuality?>&p2pGroup=<?=$p2pGroup?>&room=<?=$room?>&welcome=<?=$welcome?>&username=<?=$username?>&userType=3&userPicture=<?=$userPicture?>&userLink=<?=$userLink?>&webserver=&msg=<?=$msg?>&loggedin=<?=$loggedin?>&linkcode=<?=urlencode($linkcode)?>&embedcode=<?=urlencode($embedcode)?>&embedvcode=<?=urlencode($embedvcode)?>&imagecode=<?=urlencode($imagecode)?>&room_limit=<?=$room_limit?>&showTimer=<?=$showTimer?>&showCredit=1&disconnectOnTimeout=1&camWidth=<?=$camWidth?>&camHeight=<?=$camHeight?>&camFPS=<?=$camFPS?>&micRate=<?=$micRate?>&camBandwidth=<?=$camBandwidth?>&bufferLive=<?=$bufferLive?>&bufferFull=<?=$bufferFull?>&showCamSettings=<?=$showCamSettings?>&advancedCamSettings=<?=$advancedCamSettings?>&camMaxBandwidth=<?=$camMaxBandwidth?>&disableBandwidthDetection=<?=$disableBandwidthDetection?>&limitByBandwidth=<?=$limitByBandwidth?>&configureSource=<?=$configureSource?>&generateSnapshots=<?=$generateSnapshots?>&snapshotsTime=<?=$snapshotsTime?>&onlyVideo=<?=$onlyVideo?>&noVideo=<?=$noVideo?>&noEmbeds=<?=$noEmbeds?>&labelColor=<?=$labelColor?>&writeText=<?=$writeText?>&floodProtection=<?=$floodProtection?>&externalInterval=<?=$externalInterval?>&layoutCode=<?=urlencode($layoutCode)?>&fillWindow=<?=$fillWindow?>&verboseLevel=<?=$verboseLevel?>&loadstatus=1
