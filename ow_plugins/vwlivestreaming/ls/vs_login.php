<?php
//vs_login.php controls watch interface (video & chat & user list) login called by live_watch.swf

        // initial
        $loggedin = 1;
        $msg = "";
        $userType = 1;
        
        // get parameters from session/params_roomname
        $sessionsPath = "sessions/";
        $sessionNamex = "params".$_GET ['room_name'].".vwf";
        if ($handle = opendir($sessionsPath)) {
              while (false !== ($file = readdir($handle))) {
                if ( $sessionNamex == $file ) {
                  $openFile = fopen ($sessionsPath.$file, 'r');
                  while (!feof($openFile) ) {
                    $room_namex = fgets($openFile);
                  }
                  $msg = "";
                  $params = explode("^", $room_namex);
                  $room = $params[0];
                  $description = $params [1];
                  $room_limit = $params [2];
                  //$welcome = $params [3];
                  $welcome = $params [4];
                  $offlineMessage = $params [5];
                  $camWidth = $params [6];
                  $camHeight = $params [7];
                  $camFPS = $params [8];
                  $micRate = $params [9];
                  $camBandwidth = $params [10];
                  $labelColor = $params [11];
                  //$layoutCode = $params [12];
                  $filterRegex = $params [13];
                  $filterReplace = $params [14];
                  //$floodProtection = $params [15];
                  $floodProtection = $params [16];
                  $permissions = $params [17];
                  $user_list = $params [18];
                  $moderator_list = $params [19];
                  //$layoutCode = html_entity_decode ($params [20]);
                  $layoutCode = $params [20];
                  $rtmp_server = $params [21];
                  $rtmp_amf = $params [22];
                  $rtmfp_server = $params [23];
                  $p2pGroup = $params [24];
                  $tokenKey = $params [25];
                  $snapshotsTime = $params [26];
                  $camMaxBandwidth = $params [27];
                  //$bufferLive = $params [28];
                  //$bufferFull = $params [29];
                  $bufferLive = $params [30];
                  $bufferFull = $params [31];
                  $disableBandwidthDetection = $params [32];
                  $limitByBandwidth = $params [33];
                  $generateSnapshots = $params [34];
                  //$externalInterval = $params [35];
                  $externalInterval = $params [36];
                  $ws_ads = $params [37];
                  $adsTimeout = $params [38];
                  $adsInterval = $params [39];
                  $statusInterval = $params [40];

                  // cSettings
                  $videoCodec = $params [41];
                  $codecProfile = $params [42];
                  $codecLevel = $params [43];
                  $soundCodec = $params [44];
                  $enableRTMP = $params [45];
                  $enableP2P = $params [46];
                  $supportRTMP = $params [47];
                  $supportP2P = $params [48];
                  $alwaysRTMP = $params [49];
                  $alwaysP2P = $params [50];
                  $soundQuality = $params [51];

                }
              }
              closedir($handle);
        }

        // get sessionid from cookie
        if (isset ($_COOKIE ['sessionNameLs'])) {
          $sessionName = $_COOKIE ['sessionNameLs'].".vwf";
          if ($handle = opendir($sessionsPath)) {
              while (false !== ($file = readdir($handle))) {
                if (filectime($sessionsPath.$file) < (time () - 160) && (substr($file,0,3) == 'vls' ) ) 
                {
                  chmod ($sessionsPath.$file, 0666);
                  unlink($sessionsPath.$file);
                } elseif ( $sessionName == $file )
                {
                  $openFile = fopen ($sessionsPath.$file, 'r');
                  while (!feof($openFile) ) {
                    $room_name = fgets($openFile);
                  }

                }
              }
              closedir($handle);
          }
          $params2 = explode("^", $room_name);
          $username = $params2 [41];
          $usernameOwner = $params2 [42];
          //$idUser = $params2 [43];
          //$urlRoom = $params2 [44];
          //$baseSwfUrl = $params2 [45];
          
          //configure a picture to show when this user is clicked
          $userLink=urlencode($params2 [46]);
          $userPicture=urlencode($params2 [47]);
        } // end get sessionid 
        else {
          $usernameOwner = 0;
    	    $username="VW".base_convert((time()-1224350000).rand(0,10),10,36);
          $userPicture=urlencode("defaultpicture.png");
          $userLink=urlencode("http://www.videowhisper.com/");
        }

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

        	function get_perm_disable ($userisowner, $userinmoderator, $permtype) {
    			$returnperm = 1;
    			if ($permtype == 0) {	// all
    				$returnperm = 0;
    			}
    			if ($permtype == 1) {	// moderator
    				if ($userinmoderator == 1) $returnperm = 0;
    			}
    			if ($permtype == 2) {	// owner
    				if ($userisowner == 1) $returnperm = 0;
    			}
    			return $returnperm;
    		}
		  
        	function get_perm($userisowner, $userinmoderator, $permtype) {
    			$returnperm = 1;
    			if ($permtype == 0) {	// all
    				$returnperm = 0;
    			}
    			if ($permtype == 1) {	// moderator
    				if ($userinmoderator == 1) $returnperm = 0;
    			}
    			if ($permtype == 2) {	// owner
    				if ($userisowner == 1) $returnperm = 0;
    			}
    			return $returnperm;
    		}

        // 'permission' consists of 
        // showCamSettings:advancedCamSettings:configureSource:onlyVideo:noVideo:noEmbeds:showTimer:writeText:privateTextchat:
        // fillWindow:writeText2:enableVideo:enableChat:enableUsers:fillWindow2:verboseLevel (16)
        $permission = explode("|", $permissions);
        $showCamSettings = get_perm($userisowner, $userinmoderator, $permission [0]);
        $advancedCamSettings = get_perm($userisowner, $userinmoderator, $permission [1]);
        $configureSource = get_perm($userisowner, $userinmoderator, $permission [2]);
        $onlyVideo = get_perm($userisowner, $userinmoderator, $permission [3]);
        $noVideo = get_perm($userisowner, $userinmoderator, $permission [4]);
        $noEmbeds = get_perm($userisowner, $userinmoderator, $permission [5]);
        $showTimer = get_perm($userisowner, $userinmoderator, $permission [6]);
        //$writeText = get_perm($userisowner, $userinmoderator, $permission [7]);
        $privateTextchat = get_perm($userisowner, $userinmoderator, $permission [8]);
        //$fillWindow = get_perm($userisowner, $userinmoderator, $permission [9]);
        $writeText = get_perm($userisowner, $userinmoderator, $permission [10]);
        $enableVideo = get_perm_disable($userisowner, $userinmoderator, $permission [11]);
        $enableChat = get_perm_disable($userisowner, $userinmoderator, $permission [12]);
        $enableUsers = get_perm_disable($userisowner, $userinmoderator, $permission [13]);
        $fillWindow = get_perm($userisowner, $userinmoderator, $permission [14]);
        $verboseLevel = $permission [15];

        //fill your layout code between <<<layoutEND and layoutEND;
$layoutCode=<<<layoutEND
$layoutCode
layoutEND;

?>server=<?=$rtmp_server?>&serverAMF=<?=$rtmp_amf?>&tokenKey=<?=$tokenKey?>&serverRTMFP=<?=$rtmfp_server?>&videoCodec=<?=$videoCodec?>&codecProfile=<?=$codecProfile?>&codecLevel=<?=$codecLevel?>&soundCodec=<?=$soundCodec?>&enableRTMP=<?=$enableRTMP?>&enableP2P=<?=$enableP2P?>&supportRTMP=<?=$supportRTMP?>&supportP2P=<?=$supportP2P?>&alwaysRTMP=<?=$alwaysRTMP?>&alwaysP2P=<?=$alwaysP2P?>&soundQuality=<?=$soundQuality?>&p2pGroup=<?=$p2pGroup?>&bufferLive=<?=$bufferLive?>&bufferFull=<?=$bufferFull?>&welcome=<?=urlencode($welcome)?>&username=<?=$username?>&userType=<?=$userType?>&userPicture=<?=$userPicture?>&userLink=<?=$userLink?>&msg=<?=$msg?>&visitor=0&loggedin=<?=$loggedin?>&showCredit=1&disconnectOnTimeout=1&offlineMessage=<?=$offlineMessage?>&disableVideo=<?=$enableVideo?>&disableChat=<?=$enableChat?>&disableUsers=<?=$enableUsers?>&layoutCode=<?=urlencode($layoutCode)?>&fillWindow=<?=$fillWindow?>&filterRegex=<?=$filterRegex?>&filterReplace=<?=$filterReplace?>&writeText=<?=$writeText?>&floodProtection=<?=$floodProtection?>&privateTextchat=<?=$privateTextchat?>&externalInterval=<?=$externalInterval?>&ws_ads=<?=urlencode($ws_ads)?>&adsTimeout=<?=$adsTimeout?>&adsInterval=<?=$adsInterval?>&statusInterval=<?=$statusInterval?>&verboseLevel=<?=$verboseLevel?>&loadstatus=1
