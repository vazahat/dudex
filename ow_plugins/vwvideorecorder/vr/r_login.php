<?php

        // initial
        $loggedin = 0;
        $msg = "";
        $userType = 1;

        // get sessionid from cookie
        if ( isset ($_COOKIE ['sessionNameVr']) ) {
          $sessionName = $_COOKIE ['sessionNameVr'].".vwf";
          $sessionsPath = "sessions/";
          if ($handle = opendir($sessionsPath)) {
              while (false !== ($file = readdir($handle))) {
                if (filectime($sessionsPath.$file) < (time () - 300) && (substr($file,0,3) == 'vrs' ) )
                {
                  chmod ($sessionsPath.$file, 0666);
                  unlink($sessionsPath.$file);
                } elseif ( $sessionName == $file )
                {
                  $loggedin = 1;
                  $openFile = fopen ($sessionsPath.$file, 'r');
                  while (!feof($openFile) ) {
                    $room = fgets($openFile);
                  }
                }
              }
              closedir($handle);
          }
        } else 
        {
        $msg = "Please access from site pages and enable cookies.";
        }

        $params = explode("^", $room);
        $username = $params[0]; // room_name
        $recordingId = $params[1];
        $description = $params [2];
        $recordLimit = $params [3];
        $camWidth = $params [4];
        $camHeight = $params [5];
        $camFPS = $params [6];
        $micRate = $params [7];
        $camBandwidth = $params [8];
        $showCamSettings = $params [10];
        $advancedCamSettings = $params [11];
        $fillWindow = $params [12];
        $rtmp_server = $params [13];
        $rtmp_amf = $params [14];
        $camMaxBandwidth = $params [15];
        $bufferLive = $params [16];
        $bufferFull = $params [17];
        $bufferLivePlayback = $params [18];
        $bufferFullPlayback = $params [19];
        // $username = $params [20];
        $usernameOwner = $params [21];
        $idUser = $params [22];

        // cSettings
        $videoCodec = $params [27];
        $codecProfile = $params [28];
        $codecLevel = $params [29];
        $soundCodec = $params [30];
        $soundQuality = $params [31];

        if ($_COOKIE["layoutCode"]) 
        {
          $layoutCodexx=$_COOKIE["layoutCode"];
          $layoutCodex = str_replace ("^", ";", $layoutCodexx);
          $layoutCode = str_replace ("|", "=", $layoutCodex);
        }

//fill your layout code between <<<layoutEND and layoutEND;
$layoutCode=<<<layoutEND
$layoutCode
layoutEND;
        
        $username=substr($username,0,32);

?>server=<?=$rtmp_server?>&serverAMF=<?=$rtmp_amf?>&videoCodec=<?=$videoCodec?>&codecProfile=<?=$codecProfile?>&codecLevel=<?=$codecLevel?>&soundCodec=<?=$soundCodec?>&soundQuality=<?=$soundQuality?>&username=<?=$username?>&recordingId=<?=$recordingId?>&msg=<?=$msg?>&loggedin=<?=$loggedin?>&camWidth=<?=$camWidth?>&camHeight=<?=$camHeight?>&camFPS=<?=$camFPS?>&camBandwidth=<?=$camBandwidth?>&showCamSettings=<?=$showCamSettings?>&camMaxBandwidth=<?=$camMaxBandwidth?>&micRate=<?=$micRate?>&advancedCamSettings=<?=$advancedCamSettings?>&recordLimit=<?=$recordLimit?>&bufferLive=<?=$bufferLive?>&bufferFull=<?=$bufferFull?>&bufferLivePlayback=<?=$bufferLivePlayback?>&bufferFullPlayback=<?=$bufferFullPlayback?>&layoutCode=<?=urlencode($layoutCode)?>&fillWindow=<?=$fillWindow?>&loadstatus=1
