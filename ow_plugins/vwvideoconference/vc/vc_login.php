<?php

        // initial
        $loggedin = 0;
        $msg = "";
        $userType = 1;

        // get sessionid from cookie
        if (isset ($_COOKIE ['sessionNameVc'])) {
          $sessionName = $_COOKIE ['sessionNameVc'].".vwf";
          $sessionsPath = "sessions/";
          if ($handle = opendir($sessionsPath)) {
              while (false !== ($file = readdir($handle))) {
                if (filectime($sessionsPath.$file) < (time () - 160) && (substr($file,0,3) == 'vrs' ) ) 
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
        } else $msg = "Please access from site pages and enable cookies.";

        $params = explode("^", $room);
        $room = $params[0];
        $description = $params [1];
        $welcome = $params [2];
        $camWidth = $params [3];
        $camHeight = $params [4];
        $camFPS = $params [5];
        $micRate = $params [6];
        $camBandwidth = $params [7];
        $background_url = $params [8];
        // $layoutCode = html_entity_decode ($params [9]);
        $layoutCode = $params [9];
        $filterRegex = $params [10];
        $filterReplace = $params [11];
        $floodProtection = $params [12];
        $permissions = $params [13];
        $user_list = $params [14];
        $moderator_list = $params [15];
        $p2pGroup = $params [16];
        $camMaxBandwidth = $params [17];
        $bufferLive = $params [18];
        $bufferFull = $params [19];
        $bufferLivePlayback = $params [20];
        $bufferFullPlayback = $params [21];
        $disableBandwidthDetection = $params [22];
        $disableUploadDetection = $params [23];
        $limitByBandwidth = $params [24];
        $ws_ads = $params [25];
        $adsTimeout = $params [26];
        $adsInterval = $params [27];
        $statusInterval = $params [28];
        $videos_per_page = $params [29];
        $rtmp_server = $params [30];
        $rtmp_amf = $params [31];
        $rtmfp_server = $params [32];
        $username = $params [33];
        $usernameOwner = $params [34];
        $idUser = $params [35];

        // check, is user in user_list?
    	  $userinlist = 0;
    		$userlist = explode(",", $user_list);
    		if (trim($user_list) != '') {
    			$found = 0;
    			 foreach ($userlist as $key => $val) { 
    				if ($username == trim($val)) {
    					$found = 1;
    					$userinlist = 1;
    				}
    			 }
    			 if ($found === 0) {
    				$message=urlencode("Access to '$room' is limited to certain users!");
    				$loggedin=0;
    			 }
    		  } else {
    			 $userinlist = 1;
    		  }

        // room owner check
        $userisowner = 0;
        if ($usernameOwner == $username) $userisowner = 1;

        // check, is user in moderator_list?
        $userinmoderator = 0;
    		$moderatorlist = explode(",", $moderator_list);
  		  if (trim($moderator_list) != '') {
  			 foreach ($moderatorlist as $key => $val) { 
  				if ($username == trim($val)) {
  					$userinmoderator = 1;
  				}
  			 }
  		  }	

        	function get_perm($userisowner, $userinmoderator, $permtype) {
    			$returnperm = 0;
    			if ($permtype == 0) {	// all
    				$returnperm = 1;
    			}
    			if ($permtype == 1) {	// moderator
    				if ($userinmoderator == 1) $returnperm = 1;
    			}
    			if ($permtype == 2) {	// owner
    				if ($userisowner == 1) $returnperm = 1;
    			}
    			return $returnperm;
    		}

        /** 'permission' consists of fillWindow:advancedCamSettings:
        * showCamSettings:configureSource:disableVideo:disableSound:panelRooms:panelUsers:panelFiles:file_upload:file_delete:tutorial:
        * autoViewCams:showTimer:writeText:regularWatch:newWatch:privateTextchat:administrator:verboseLevel (20)
        */
        $permission = explode("|", $permissions);
        $fillWindow = get_perm($userisowner, $userinmoderator, $permission [0]);
        $advancedCamSettings = get_perm($userisowner, $userinmoderator, $permission [1]);
        $showCamSettings = get_perm($userisowner, $userinmoderator, $permission [2]);
        $configureSource = get_perm($userisowner, $userinmoderator, $permission [3]);
        $disableVideo = get_perm($userisowner, $userinmoderator, $permission [4]);
        $disableSound = get_perm($userisowner, $userinmoderator, $permission [5]);
        $panelRooms = get_perm($userisowner, $userinmoderator, $permission [6]);
        $panelUsers = get_perm($userisowner, $userinmoderator, $permission [7]);
        $panelFiles = get_perm($userisowner, $userinmoderator, $permission [8]);
        $file_upload = get_perm($userisowner, $userinmoderator, $permission [9]);
        $file_delete = get_perm($userisowner, $userinmoderator, $permission [10]);
        $tutorial = get_perm($userisowner, $userinmoderator, $permission [11]);
        $autoViewCams = get_perm($userisowner, $userinmoderator, $permission [12]);
        $showTimer = get_perm($userisowner, $userinmoderator, $permission [13]);
        $writeText = get_perm($userisowner, $userinmoderator, $permission [14]);
        $regularWatch = get_perm($userisowner, $userinmoderator, $permission [15]);
        $newWatch = get_perm($userisowner, $userinmoderator, $permission [16]);
        $privateTextchat = get_perm($userisowner, $userinmoderator, $permission [17]);
        $admin = get_perm($userisowner, $userinmoderator, $permission [18]);
        $verboseLevel = $permission [19];

// fill your layout code between <<<layoutEND and layoutEND;
$layoutCode=<<<layoutEND
$layoutCode
layoutEND;


// configure a picture to show when this user is clicked
$userLink=urlencode($params [36]);
$userPicture=urlencode($params [37]);

        // cSettings
        $videoCodec = $params [38];
        $codecProfile = $params [39];
        $codecLevel = $params [40];
        $soundCodec = $params [41];
        $enableRTMP = $params [42];
        $enableP2P = $params [43];
        $supportRTMP = $params [44];
        $supportP2P = $params [45];
        $alwaysRTMP = $params [46];
        $alwaysP2P = $params [47];
        $soundQuality = $params [48];

?>firstParameter=fix&server=<?=$rtmp_server?>&serverAMF=<?=$rtmp_amf?>&serverRTMFP=<?=$rtmfp_server?>&videoCodec=<?=$videoCodec?>&codecProfile=<?=$codecProfile?>&codecLevel=<?=$codecLevel?>&soundCodec=<?=$soundCodec?>&enableRTMP=<?=$enableRTMP?>&enableP2P=<?=$enableP2P?>&supportRTMP=<?=$supportRTMP?>&supportP2P=<?=$supportP2P?>&alwaysRTMP=<?=$alwaysRTMP?>&alwaysP2P=<?=$alwaysP2P?>&soundQuality=<?=$soundQuality?>&p2pGroup=<?=$p2pGroup?>&supportRTMP=1&supportP2P=1&alwaysRTMP=0&alwaysP2P=0&username=<?=urlencode($username)?>&loggedin=<?=$loggedin?>&userType=<?=$userType?>&administrator=<?=$admin?>&room=<?=urlencode($room)?>&welcome=<?=urlencode($welcome)?>&userPicture=<?=$userPicture?>&userLink=<?=$userLink?>&webserver=&msg=<?=$msg?>&tutorial=<?=$tutorial?>&room_delete=0&room_create=0&file_upload=<?=$file_upload?>&file_delete=<?=$file_delete?>&panelFiles=<?=$panelFiles?>&panelRooms=<?=$panelRooms?>&panelUsers=<?=$panelUsers?>&showTimer=<?=$showTimer?>&showCredit=1&disconnectOnTimeout=0&camWidth=<?=$camWidth?>&camHeight=<?=$camHeight?>&camFPS=<?=$camFPS?>&micRate=<?=$micRate?>&camBandwidth=<?=$camBandwidth?>&bufferLive=<?=$bufferLive?>&bufferFull=<?=$bufferFull?>&bufferLivePlayback=<?=$bufferLivePlayback?>&bufferFullPlayback=<?=$bufferFullPlayback?>&showCamSettings=<?=$showCamSettings?>&advancedCamSettings=<?=$advancedCamSettings?>&camMaxBandwidth=<?=$camMaxBandwidth?>&configureSource=<?=$configureSource?>&disableVideo=<?=$disableVideo?>&disableSound=<?=$disableSound?>&disableBandwidthDetection=<?=$disableBandwidthDetection?>&disableUploadDetection=<?=$disableUploadDetection?>&limitByBandwidth=<?=$limitByBandwidth?>&background_url=<?=$background_url?>&autoViewCams=<?=$autoViewCams?>&layoutCode=<?=urlencode($layoutCode)?>&fillWindow=<?=$fillWindow?>&filterRegex=<?=$filterRegex?>&filterReplace=<?=$filterReplace?>&writeText=<?=$writeText?>&floodProtection=<?=$floodProtection?>&regularWatch=<?=$regularWatch?>&newWatch=<?=$newWatch?>&privateTextchat=<?=$privateTextchat?>&ws_ads=<?=$ws_ads?>&adsTimeout=<?=$adsTimeout?>&adsInterval=<?=$adsInterval?>&statusInterval=<?=$statusInterval?>&verboseLevel=<?=$verboseLevel?>&loadstatus=1
