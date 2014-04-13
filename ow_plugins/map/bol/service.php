<?php

/***
 * This software is intended for use with Oxwall Free Community Software
 * http://www.oxwall.org/ and is a proprietary licensed product.
 * For more information see License.txt in the plugin folder.

 * =============================================================================
 * Copyright (c) 2012 by Aron. All rights reserved.
 * =============================================================================


 * Redistribution and use in source and binary forms, with or without modification, are not permitted provided.
 * Pass on to others in any form are not permitted provided.
 * Sale are not permitted provided.
 * Sale this product are not permitted provided.
 * Gift this product are not permitted provided.
 * This plugin should be bought from the developer by paying money to PayPal account: biuro@grafnet.pl
 * Legal purchase is possible only on the web page URL: http://www.oxwall.org/store
 * Modyfing of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 * Modifying source code, all information like:copyright must remain.
 * Official website only: http://oxwall.a6.pl
 * Full license available at: http://oxwall.a6.pl


 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED
 * AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
***/
//MAP_BOL_Service::getInstance()->html2txt();

final class MAP_BOL_Service
{
    /**
     * Constructor.
     */
    private function __construct() { }
    
    /**
     * Singleton instance.
     *
     * @var OCSTOPUSERS_BOL_Service
     */
    private static $classInstance;

    /**
     * Returns an instance of class
     *
     * @return OCSTOPUSERS_BOL_Service
     */
    public static function getInstance()
    {
        if ( self::$classInstance === null )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }
    
    public static function sortArrayItemByDesc( $el1, $el2 )
    {
        if ( $el1['score'] === $el2['score'] )
        {
        	if ( $el1['rates'] === $el2['rates'] )
        	{
        		return 0;
        	}
        	
            return $el1['rates'] < $el2['rates'] ? 1 : -1;
        }

        return $el1['score'] < $el2['score'] ? 1 : -1;
    }

    public static function sortArrayItemByTimeDesc( $el1, $el2 )
    {
        if ( $el1['timeStamp'] === $el2['timeStamp'] )
        {
            return 0;
        }

        return $el1['timeStamp'] < $el2['timeStamp'] ? 1 : -1;
    }
    
    public function findList( $map, $limit )
    {
    	$first = ( $map - 1 ) * $limit;
    	
    	$topRatedList = $this->findMostRatedEntityList('user_rates', $first, $limit);

        if ( !$topRatedList )
        {
            return array();
        }
        
        $userArr = BOL_UserService::getInstance()->findUserListByIdList(array_keys($topRatedList));

        $users = array();

        foreach ( $userArr as $key => $user )
        {
            $users[$key]['dto'] = $user;
            $users[$key]['score'] = $topRatedList[$user->id]['avgScore'];
            $users[$key]['rates'] = $topRatedList[$user->id]['ratesCount'];
        }
        
        usort($users, array('OCSTOPUSERS_BOL_Service', 'sortArrayItemByDesc'));
        
        return $users;
    }
    
    public function countUsers()
    {
    	return BOL_RateService::getInstance()->findMostRatedEntityCount('user_rates');
    }
    
    public function findRateUserList( $userId, $map, $limit )
    {
    	$rateDao = BOL_RateDao::getInstance();
    	$userDao = BOL_UserDao::getInstance();
    	
    	$limit = (int) $limit;
        $first = ( $map - 1 ) * $limit;
        
        $sql = "SELECT `r`.`score`, `r`.`userId`, `r`.`timeStamp`
            FROM `" . $rateDao->getTableName() . "` AS `r`
            INNER JOIN `" . $userDao->getTableName() . "` AS `u` ON (`u`.`id` = `r`.`userId`) 
            WHERE `entityId` = :entityId AND `entityType` = 'user_rates'
            ORDER BY `timeStamp` DESC
            LIMIT :first, :limit";
        
        $list = OW::getDbo()->queryForList($sql, array('entityId' => $userId, 'first' => $first, 'limit' => $limit));
        
        if ( !$list )
        {
        	return null;
        }
        
        $idList = array();
        $keyList = array();
        foreach ( $list as $rate )
        {
        	$keyList[$rate['userId']] = $rate;
        	array_push($idList, $rate['userId']);
        }
        
        $userArr = BOL_UserService::getInstance()->findUserListByIdList($idList);

        $users = array();
        foreach ( $userArr as $key => $user )
        {
            $users[$key]['dto'] = $user;
            $users[$key]['score'] = $keyList[$user->id]['score'];
            $users[$key]['timeStamp'] = $keyList[$user->id]['timeStamp'];
        }
        
        usort($users, array('OCSTOPUSERS_BOL_Service', 'sortArrayItemByTimeDesc'));

        return $users;
    }
    
    public function countRateUsers( $userId )
    {
        $rateDao = BOL_RateDao::getInstance();
        $userDao = BOL_UserDao::getInstance();
        
        $sql = "SELECT COUNT(*)
            FROM `" . $rateDao->getTableName() . "` AS `r`
            INNER JOIN `" . $userDao->getTableName() . "` AS `u` ON (`u`.`id` = `r`.`userId`) 
            WHERE `entityId` = :entityId AND `entityType` = 'user_rates'";
        
        return (int) OW::getDbo()->queryForColumn($sql, array('entityId' => $userId));
    }
    
    public function findMostRatedEntityList( $entityType, $first, $count )
    {
    	$rateDao = BOL_RateDao::getInstance();
    	
    	$query = "SELECT `" . BOL_RateDao::ENTITY_ID . "` AS `id`, COUNT(*) as `ratesCount`, AVG(`score`) as `avgScore`
            FROM " . $rateDao->getTableName() . "
                        WHERE `" . BOL_RateDao::ENTITY_TYPE . "` = :entityType AND `" . BOL_RateDao::ACTIVE . "` = 1
            GROUP BY `" . BOL_RateDao::ENTITY_ID . "`
                        ORDER BY `avgScore` DESC, `ratesCount` DESC
                        LIMIT :first, :count";

        $arr = OW::getDbo()->queryForList($query, array('entityType' => $entityType, 'first' => (int) $first, 'count' => (int) $count));
        
        $resultArray = array();

        foreach ( $arr as $value )
        {
            $resultArray[$value['id']] = $value;
        }

        return $resultArray;
    }


    public function was_time($name="last_update",$limit=10)//minutes
    {

//        return true;
        if (!isset($_SESSION[$name])){ 
//echo "--1<br>";
            $time_start = microtime(true);
            $_SESSION[$name]= $time_start;
            return true;
        }else{
//echo "--2<br>";
            $time_start = $_SESSION[$name];
        }
        $time_end = microtime(true);
        $time = $time_end - $time_start;
        if ($time>=($limit*60)){
//echo "$time>=".($limit*60*60)."<br>";
            $_SESSION[$name]= microtime(true);
            return true;
        }else{
//echo "--4<br>";
            return false;
        }
    }

    public function update_latitude_array($sqlget="", $sqlupdate="",$sqlwas="",$type="",$limit=20 )
    {
        $was=false;
//echo "<hr>".$type."<br>";
        if (!$this->was_time('last_update_lat',1)) return ;
//echo "<br>1)".$type;
        $arr = OW::getDbo()->queryForList($sqlget);
        if (!is_array($arr) OR !$sqlupdate OR !$type) {
//echo "<br>2)".$type;
            $_SESSION['last_update_lat']="";
            return;
        }
//echo "sssssssssssssssssss<br><br>";
//exit;
        $curent=0;
        foreach ( $arr as $value ){
            $was=true;
//print_r($value);exit;
            $address ="";
            if ($type=="fanpage"){
                $region=$value['a_country'];


                if ($value['a_street']){
                    $address .=$value['a_street'];
                }
                if ($value['a_postcode']){
                    if ($address) $address .=", ";
                    $address .=$value['a_postcode'];
                }
                if ($value['a_city']){
                    if ($address) {
                        if ($value['a_postcode']){
                            $address .=" ";
                        }else{
                            $address .=", ";
                        }
                    }
                    $address .=$value['a_city'];
                }
            }else if ($type=="shoppro"){
                $region="";
                $address =$value['location'];
            }

            $address=str_replace("\r\n"," ",$address);
            $address=str_replace("\r","",$address);
            $address=str_replace("\n","",$address);
            $address=str_replace("\t","",$address);
            $address=str_replace("'","\"",$address);

//echo "<hr>";
//echo "<b>".$type.":</b><br>";
//echo $address."<br>";
            $address = str_replace(" ", "+", $address);
//            $address = urlencode($address);
//            $region = urlencode($region);
//echo $address."<br>";


            $json = file_get_contents("http://maps.google.com/maps/api/geocode/json?address=$address&sensor=false&region=$region");
            $json = json_decode($json);
//print_r($json);
            $lat ="";
            $long ="";
        if (isset($json->{'status'}) AND $json->{'status'}=="OVER_QUERY_LIMIT"){
            break;
        }else if (isset($json->{'status'}) AND $json->{'status'}=="OK"){

            if (isset($json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'})){
                $lat = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
            }
            if (isset($json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'})){
                $long = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
            }

            if (isset($lat) AND $lat!="" AND isset($long) AND $long!=""){
//                $sql="UPDATE " . OW_DB_PREFIX. "";
                $sqlupdateOK=str_replace("[lat]",addslashes($lat),$sqlupdate);
                $sqlupdateOK=str_replace("[lan]",addslashes($long),$sqlupdateOK);
                $sqlupdateOK=str_replace("[where]"," id='".addslashes($value['id'])."' ",$sqlupdateOK);
                OW::getDbo()->query($sqlupdateOK);
//echo "<br><br/>3)".$sqlupdate;
//echo $address."--".$sqlupdate."<hr>";
            }else{
                $sqlwasOK=str_replace("[where]"," id='".addslashes($value['id'])."' ",$sqlwas);
                OW::getDbo()->query($sqlwasOK);
//echo "<br><br/>1)".$sqlwas;
            }

        }else{
                $sqlwasOK=str_replace("[where]"," id='".addslashes($value['id'])."' ",$sqlwas);
                OW::getDbo()->query($sqlwasOK);
//echo "<br><br/>2)".$sqlwas;
        }
//echo "<br><br><br><br>";
            $curent=$curent+1;
            if ($curent>$limit){
                break;
            }
        }//for
        if (!$was){
            $_SESSION['last_update_lat']="";
        }

    }


    public function is_file_application()
    {
        $pluginStaticDir =OW::getPluginManager()->getPlugin('map')->getRootDir();
        if (is_file($pluginStaticDir."map_mobile.apk")){
            return true;
        }else{
            return false;
        }
    }

    public function corectforjava($content="")
    {
        $content=str_replace("\$","",$content);
        $content=str_replace("'","`",$content);
        $content=str_replace("[","(",$content);
        $content=str_replace("]",")",$content);
        $content=str_replace("\"","`",$content);
        $content=str_replace("$","",$content);
        return $content;
    }

    public function get_category($id_master=0,$selected=0, $disable_cat=0)
    {
        $id_user = OW::getUser()->getId();//citent login user (uwner)
        $is_admin = OW::getUser()->isAdmin();
        $curent_url=OW_URL_HOME;
        $content ="";
        $add="";
        if (!$is_admin){
            $add=" AND active='1' ";
        }
        
            $sql="SELECT * FROM " . OW_DB_PREFIX. "map_category  
            WHERE id2='".addslashes($id_master)."' ".$add." ORDER BY name";
            $arr2 = OW::getDbo()->queryForList($sql);
            foreach ( $arr2 as $row ){
                if ($row['id']!=$disable_cat){
                    if ($row['id']==$selected) $sel=" selected ";
                        else $sel="";
                    $content .="<option ".$sel." value=\"".$row['id']."\">".stripslashes($row['name'])."</option>";
                }
            }
        return $content;
    }

    public function get_category_list($selected=0, $disable_cat=0)
    {
        $id_user = OW::getUser()->getId();//citent login user (uwner)
        $is_admin = OW::getUser()->isAdmin();
        $curent_url=OW_URL_HOME;
        $content ="";
        $add="";
        if (!$selected){
            $content .="<option selected value=\"0\">-- ".OW::getLanguage()->text('map', 'select_category')." --</option>";
        }
        if (!$is_admin){
            $add=" AND active='1' ";
        }
            $sql="SELECT * FROM " . OW_DB_PREFIX. "map_category  
            WHERE id2='0' ".$add." ORDER BY name";
            $arr2 = OW::getDbo()->queryForList($sql);
            foreach ( $arr2 as $row ){
                if ($row['id']!=$disable_cat){
//                    if ($row['id']==$selected) $sel=" selected ";
//                        else $sel="";
                    $content .="<option disabled value=\"".$row['id']."\">".stripslashes($row['name'])."</option>";

//----2222 s        
            $sql2="SELECT * FROM " . OW_DB_PREFIX. "map_category  
            WHERE id2='".addslashes($row['id'])."' ".$add." ORDER BY name";
            $arr22 = OW::getDbo()->queryForList($sql2);
            foreach ( $arr22 as $row2 ){
                if ($row2['id']!=$disable_cat){
                    if ($row2['id']==$selected) $sel=" selected ";
                        else $sel="";
                    $content .="<option ".$sel." value=\"".$row2['id']."\">&nbsp;&nbsp;&nbsp;".stripslashes($row2['name'])."</option>";
                }
            }
//----2222 e

                }
            }
        return $content;
    }

    public function get_category_list_edit($selected=0)
    {
//echo $selected;exit;
        $id_user = OW::getUser()->getId();//citent login user (uwner)
        $is_admin = OW::getUser()->isAdmin();
        $curent_url=OW_URL_HOME;
        $content ="";
        $add="";
        if (!$is_admin){
            $add=" AND active='1' ";
        }
            $sql="SELECT * FROM " . OW_DB_PREFIX. "map_category  
            WHERE id2='0' ".$add." ORDER BY name";
            $arr2 = OW::getDbo()->queryForList($sql);
            foreach ( $arr2 as $row ){
                if ($row['id']!=$disable_cat){
//                    if ($row['id']==$selected) $sel=" selected ";
//                        else $sel="";
                    $content .="<option disabled value=\"".$row['id']."\">".stripslashes($row['name'])."</option>";

//----2222 s        
            $sql2="SELECT * FROM " . OW_DB_PREFIX. "map_category  
            WHERE id2='".addslashes($row['id'])."' ".$add." ORDER BY name";
            $arr22 = OW::getDbo()->queryForList($sql2);
            foreach ( $arr22 as $row2 ){
                if ($row2['id']!=$disable_cat){
//                    if ($row2['id']==$selected) $sel=" selected ";
//                        else $sel="";
                    if ($selected>0 AND $row2['id']==$selected)  $sel=" SELECTED ";
                    else if (!$selected AND $row2['id']==$_GET['cc'])  $sel=" SELECTED ";
//                    else if (isset($_GET['cc']) AND $_GET['cc']>0 AND $_GET['cc']==$row2['id']) $sel=" SELECTED ";
//                    else if (!isset($_GET['zo']) AND $row2['id']==$selected) $sel=" SELECTED ";
//                    else if (isset($_GET['zo']) AND $_GET['zo']>0 AND $_GET['zo']==$row2['id']) $sel=" SELECTED ";
                    else $sel="";
                    $content .="<option ".$sel." value=\"".$row2['id']."\">&nbsp;&nbsp;&nbsp;".stripslashes($row2['name'])."</option>";
                }
            }
//----2222 e

                }
            }
        return $content;
    }

    public function make_markers($master_user=0,$from_cat=0)
    {
        $id_user = OW::getUser()->getId();//citent login user (uwner)
        $is_admin = OW::getUser()->isAdmin();
        $curent_url=OW_URL_HOME;
        $content ="";
        $content_info ="";
        $content_arr ="";
        $content_arr_i =0;

/*
  var myLatlng = new google.maps.LatLng(".$lat.",".$lon.");
  var mapOptions = {
    zoom: 15,
    center: myLatlng,
    mapTypeId: google.maps.MapTypeId.ROADMAP
  }
  var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

  var marker = new google.maps.Marker({
      position: myLatlng,
      map: map,
      icon: '".$curent_url."ow_userfiles/plugins/base/avatars/avatar_1_1371045733.jpg',
      title: 'Hello World!'
  });
*/

        if ($master_user>0){
            $sql="SELECT * FROM " . OW_DB_PREFIX. "friends_friendship 
            WHERE (userId='".addslashes($master_user)."' OR friendId='".addslashes($master_user)."') AND status='active' ";
            $arr2 = OW::getDbo()->queryForList($sql);
//echo $sql;exit;
            $mark_id=0;
            foreach ( $arr2 as $row ){
                if ($row['userId']==$master_user) $friend_id=$row['friendId'];
                    else $friend_id=$row['userId'];

                $query2 = "SELECT sdd.*,ssd.active FROM " . OW_DB_PREFIX. "map_scan_data sdd 
            LEFT JOIN " . OW_DB_PREFIX. "map_scan ssd ON (ssd.id=sdd.id_scan AND sdd.id_owner='".addslashes($friend_id)."') 
                WHERE sdd.id_owner='".addslashes($friend_id)."' AND ssd.active='1' ORDER BY sdd.d_time DESC LIMIT 1";
//echo $query2;exit;
                $arrp2 = OW::getDbo()->queryForList($query2);
                if (isset($arrp2[0]['id_owner']) AND $arrp2[0]['id_owner']>0) {

                    $dname=BOL_UserService::getInstance()->getDisplayName($arrp2[0]['id_owner']);
                    $uurl=BOL_UserService::getInstance()->getUserUrl($arrp2[0]['id_owner']);
                    $uimg=BOL_AvatarService::getInstance()->getAvatarUrl($arrp2[0]['id_owner']);
                    if (!$uimg) {
                        $uimg=$curent_url."ow_static/themes/".OW::getConfig()->getValue('base', 'selectedTheme')."/images/no-avatar.png";
                    }
                    $avat=$uimg;
                    $mark_id=$friend_id;

/*
                    $content .= "var marker_im".$mark_id." = new google.maps.MarkerImage(
                        '".$avat."',
                        null,//size
                        null,//origin
                        null,//anchor
                        new google.maps.Size(40,40)//scale
                    );";
*/

/*
                    $content .= "var marker".$mark_id." = new google.maps.Marker({
                        position: new google.maps.LatLng(".$arrp2[0]['d_latitude'].", ".$arrp2[0]['d_longitude']."),
                        map: map,
                        icon: marker_im".$mark_id.",
                        title: '".$dname."[".$friend_id."]'
                    });
*/


            if ($content_arr) $content_arr .=", ";

/*
            $content_arr .="{'photo_id': ".$arrp2[0]['id_scan'].", 
            'photo_title': '".$dname."[".$friend_id."]', 
            'photo_url': '".$avat."', 
            'longitude': '".$arrp2[0]['d_longitude']."', 
            'latitude': '".$arrp2[0]['d_latitude']."', 
            'width': 500, 'height': 375, 
            'upload_date': '".$arrp2[0]['d_time']."', 
            'owner_id': ".$friend_id."
            }";
*/
/*
            $content_arr .="{'photo_id': ".$arrp2[0]['id_scan'].", 
            'photo_title': '".$this->corectforjava($dname)."', 
            'photo_url': '".$uurl."', 
            'photo_file_url': '".$avat."', 
            'longitude': '".$arrp2[0]['d_longitude']."', 
            'latitude': '".$arrp2[0]['d_latitude']."', 
            'width': 500, 'height': 375, 
            'upload_date': '".$arrp2[0]['d_time']."', 
            'owner_id': ".$friend_id.", 
            'owner_name': '".$this->corectforjava($dname)."', 
            'owner_url': '".$uurl."',
            'markerurl': '".$avat."',
            'marker_type': 'friend'
            }";
*/
            $content_arr .="{'photo_id': ".$arrp2[0]['id_scan'].", 
            'photo_title': '".$this->corectforjava($dname)."', 
            'photo_file_url': '".$avat."', 
            'longitude': '".$arrp2[0]['d_longitude']."', 
            'latitude': '".$arrp2[0]['d_latitude']."', 
            'owner_id': ".$friend_id.", 
            'owner_name': '".$this->corectforjava($dname)."', 
            'owner_url': '".$uurl."',
            'upload_date': '".$arrp2[0]['d_time']."', 
            'markerurl': '".$avat."',
            'marker_type': 'friend'
            }";


//            $content_arr .="{'photo_id': ".$row['id'].", 
//            'photo_title': '".$this->corectforjava(stripslashes($row['name']))."', 
//            'longitude': '".$row['lon']."', 
//            'latitude': '".$row['lat']."', 
//            'markerurl': '".$markerurl."',
//            'marker_type': 'poi'
//            }";


            $content_arr_i=$content_arr_i+1;

/*
         $content .="var marker".$mark_id." = new RichMarker({
          position: new google.maps.LatLng(".$arrp2[0]['d_latitude'].", ".$arrp2[0]['d_longitude']."),
          map: map,
          draggable: false,
          content: '<div class=\"aron-map-marker-frame\"><div><img class=\"aron-map-marker\" src=\"".$avat."\"/></div><div class=\"aron-map-marker-point\"></div></div>',
            title: '".$dname."[".$friend_id."]'
          });";
*/
//            $content .="
//                markers.push(marker".$mark_id.");
//            ";




/*
                $content .="var infowindow".$mark_id." = new google.maps.InfoWindow({
                         content: '<b>:: ".$dname." ::</b><br/>Last update: ".$arrp2[0]['d_time']."<br />'
                    });

                    google.maps.event.addListener(marker".$mark_id.", 'click', function() {
                        infowindow".$mark_id.".open(map, marker".$mark_id.");
                    });
                    ";
*/
                    $mark_id=$mark_id+1;
                }//if
            }//for


        }//if




        $addx=$this->make_markers_poi(1,$from_cat);
//print_r($addx);exit;
        if (isset($addx['items']) AND $addx['items']>0 AND isset($addx['content'])){
            $content_arr_i=$content_arr_i+$addx['items'];
                if ($content_arr) $content_arr .=",";
                $content_arr .=$addx['content'];
        }




        $content.= "var data = { 'count': ".$content_arr_i.", 'photos':[".$content_arr."]}";

        return $content;
    }

    public function make_markers_poi($city=1,$from_cat=0)
    {
        $id_user = OW::getUser()->getId();//citent login user (uwner)
        $is_admin = OW::getUser()->isAdmin();
        $curent_url=OW_URL_HOME;
        $content ="";
        $content_info ="";
        $content_arr ="";
        $content_arr_i =0;
        $pluginStaticURL2=OW::getPluginManager()->getPlugin('map')->getStaticUrl();

        if ($city>0){
            if ($from_cat>0){
                $adds=" AND id_cat='".addslashes($from_cat)."' ";
            }else{
                $adds="";
                $limit="LIMIT 1000";
            }
            if ($is_admin){
                $addact=" 1 ";
            }else{
                $addact=" active='1' ";
            }
            $sql="SELECT * FROM " . OW_DB_PREFIX. "map WHERE ".$addact.$adds.$limit;
//echo $sql;exit;
            $arr2 = OW::getDbo()->queryForList($sql);
            $mark_id=0;
            foreach ( $arr2 as $row ){

                if (isset($row['id']) AND $row['id']>0 AND isset($row['lat']) AND isset($row['lon']) AND $row['lat']!="" AND $row['lon']!="") {
                    
                    $dname=BOL_UserService::getInstance()->getDisplayName($row['id_owner']);
                    $uurl=BOL_UserService::getInstance()->getUserUrl($row['id_owner']);
                    $uimg=BOL_AvatarService::getInstance()->getAvatarUrl($row['id_owner']);
                    if (!$uimg) {
                        $uimg=$curent_url."ow_static/themes/".OW::getConfig()->getValue('base', 'selectedTheme')."/images/no-avatar.png";
                    }
                    $avat=$uimg;
                    $mark_id=$friend_id;


                    if ($content_arr) $content_arr .=", ";
        $markerurl="";
        if ($row['ico']){
//            $markerurl=$pluginStaticURL2."ico".DS.$row['ico'].".png";
            $markerurl=$row['ico'].".png";
//        }else{
        }


        $url_map_poi=$curent_url."map/zoom/".$row['id'];

//            'photo_url': '".$uurl."', 
//            'photo_file_url': '".$avat."', 
//            'photo_title': '".$this->corectforjava(stripslashes($row['name']))."', 
            $content_arr .="{'photo_id': ".$row['id'].", 
            'longitude': '".$row['lon']."', 
            'latitude': '".$row['lat']."', 
            'markerurl': '".$markerurl."',
            'marker_type': 'poi'
            }";



                    $content_arr_i=$content_arr_i+1;

                    $mark_id=$mark_id+1;
                }//if
            }//for


        }//if


//        $content.= "var datac = { 'count': ".$content_arr_i.", 'photos':[".$content_arr."]}";
        $ret['items']=$content_arr_i;
        $ret['content']=$content_arr;
        return $ret;
    }

    public function distance($lat1, $lon1, $lat2, $lon2, $unit) {
      $theta = $lon1 - $lon2;
      $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
      $dist = acos($dist);
      $dist = rad2deg($dist);
      $miles = $dist * 60 * 1.1515;
      $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        } 
    }
function distanceXX($lat1, $lng1, $lat2, $lng2, $miles = true)
{
    $pi80 = M_PI / 180;
    $lat1 *= $pi80;
    $lng1 *= $pi80;
    $lat2 *= $pi80;
    $lng2 *= $pi80;

    $r = 6372.797; // mean radius of Earth in km
    $dlat = $lat2 - $lat1;
    $dlng = $lng2 - $lng1;
    $a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlng / 2) * sin($dlng / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    $km = $r * $c;

    return ($miles ? ($km * 0.621371192) : $km);
}

    public function make_paths($master_user=0)
    {
        $id_user = OW::getUser()->getId();//citent login user (uwner)
        $is_admin = OW::getUser()->isAdmin();
        $curent_url=OW_URL_HOME;
        $content ="";
        $content_m ="";

        if ($master_user>0){
                $first=true;
                $last_lat="0";
                $last_lon="0";
//                $query2 = "SELECT * FROM " . OW_DB_PREFIX. "map_scan_data WHERE id_owner='".addslashes($master_user)."' AND source_post_type='online' ORDER BY d_time DESC LIMIT 100";
//                $query2 = "SELECT * FROM " . OW_DB_PREFIX. "map_scan_data WHERE id_owner='".addslashes($master_user)."' ORDER BY d_time DESC LIMIT 100";
                $days_ago = date('Y-m-d H:i:s', strtotime('-2 days', strtotime(date('Y-m-d H:i:s'))));
//                $query2 = "SELECT * FROM " . OW_DB_PREFIX. "map_scan_data WHERE id_owner='".addslashes($master_user)."' AND d_provider='gps' ORDER BY d_time DESC LIMIT 100";
                $query2 = "SELECT * FROM " . OW_DB_PREFIX. "map_scan_data WHERE id_owner='".addslashes($master_user)."' AND d_time>'".addslashes($days_ago)."' AND d_provider='gps' ORDER BY d_time DESC LIMIT 100";

//echo $query2;exit;
                $arrp2 = OW::getDbo()->queryForList($query2);
                foreach ( $arrp2 as $row ){
                    if (isset($row['id_owner']) AND $row['id_owner']>0) {
                        $show=false;
                        if ($last_lat>0 AND $last_lon>0){
//M - metry
//K - km
//N - mile
//echo $this->distance($last_lat,$last_lon,$row['d_latitude'],$row['d_longitude'],"M")."<br>";
                            if ($this->distance($last_lat,$last_lon,$row['d_latitude'],$row['d_longitude'],"M")>0.2){
//echo $row['d_time']."  -  ".$this->distance($last_lat,$last_lon,$row['d_latitude'],$row['d_longitude'],"M")."<br>";
                                $show=1;
                            }
                        }else if ($first==true){
                            $show=1;
                        }

                        if ($show){
                            if ($content) $content .=", ";
                            $content .="new google.maps.LatLng(".$row['d_latitude'].", ".$row['d_longitude'].")";

    if ($row['source_post_type']=="online"){
 $content_m .="new google.maps.Marker({
      position: new google.maps.LatLng(".$row['d_latitude'].", ".$row['d_longitude']."),
      map: friendsmapTest.map,
    icon:'http://maps.google.com/mapfiles/ms/icons/green-dot.png',
      title:'ONLINE: ".$row['d_time']."'
  });";
    }else{
 $content_m .="new google.maps.Marker({
      position: new google.maps.LatLng(".$row['d_latitude'].", ".$row['d_longitude']."),
      map: friendsmapTest.map,
    icon:'http://maps.google.com/mapfiles/ms/icons/red-dot.png',
      title:'OFFLINE: ".$row['d_time']."'
  });";
    }       
                        }

                        $last_lat=$row['d_latitude'];
                        $last_lon=$row['d_longitude'];
                        $first=false;
                    }
                }
//exit;
            
        }
        $rr=array();
        $rr[0]=$content;
        $rr[1]=$content_m;
        return $rr;
    }


    public function make_tabs($selected="",$content="")
    {
        $id_user = OW::getUser()->getId();//citent login user (uwner)
        $is_admin = OW::getUser()->isAdmin();
        $curent_url=OW_URL_HOME;
$content_t ="";

/*
if (isset($_GET['check_cat'])){
    $check_cat="?check_cat=".$_GET['check_cat'];
    $check_cata="&check_cat=".$_GET['check_cat'];
}else{
    $check_cat="";
    $check_cata="";
}
*/
if (isset($_GET['check_cat'])){
    $check_cat_ajax="";
    $check_cat_ajaxx="";
    $fox=$_GET['check_cat'];
    if (is_array($fox)){
        for ($i=0;$i<count($fox);$i++){
            if ($check_cat_ajax) $check_cat_ajax .="&";
            $check_cat_ajax .="check_cat%5B%5D=".$fox[$i];
        }
    }
    if ($check_cat_ajax) {
        $check_cat_ajaxx="?".$check_cat_ajax;
        $check_cat_ajax="&".$check_cat_ajax;
    }
}else{
    $check_cat_ajax="";
    $check_cat_ajaxx="";
}
//$check_cat_ajax=$_GET['check_cat'];

//        if ($curent_tab=="shop" AND OW::getConfig()->getValue('map', 'tabdisable_shop')!=1){
//            $tabs=WALL_BOL_Service::getInstance()->make_tabs("shop",$wall);
//        }else if ($curent_tab=="photo" AND OW::getConfig()->getValue('map', 'tabdisable_photo')!=1){
//            $tabs=WALL_BOL_Service::getInstance()->make_tabs("photo",$wall);



        $content_t .="<div class=\"ow_content\">";
/*
        if (
            OW::getConfig()->getValue('map', 'tabdisable_shop')!=1 OR 
            OW::getConfig()->getValue('map', 'tabdisable_fanpage')!=1 OR 
            OW::getConfig()->getValue('map', 'tabdisable_photo')!=1 OR 
            OW::getConfig()->getValue('map', 'tabdisable_video')!=1 OR 
            OW::getConfig()->getValue('map', 'tabdisable_blogs')!=1 OR 
            OW::getConfig()->getValue('map', 'tabdisable_forum')!=1 OR 
            OW::getConfig()->getValue('map', 'tabdisable_groups')!=1 OR 
            OW::getConfig()->getValue('map', 'tabdisable_forum')!=1 OR 
            OW::getConfig()->getValue('map', 'tabdisable_event')!=1 
        ){
*/

/*
        $page_on_top_shop=OW::getConfig()->getValue('map', 'ta_newsfeed_');
        if ($page_on_top_shop!=""){
                $content_t .="<div class=\"ow_content_menu_wrap ow_content_html\">";
                if (OW::getConfig()->getValue('shoppro', 'admin_replace_btobr')==1 AND !OW::getPluginManager()->isPluginActive('wysiwygeditor')){
                    $page_on_top_shop=SHOPPRO_BOL_Service::getInstance()->ntobr($page_on_top_shop);
                }
                $page_top_template="";
                $page_top_template .="<div class=\"ow_dnd_widget index-PAGES_CMP_MenuWidget\">";



                if (OW::getConfig()->getValue('shoppro', 'config_page_on_top_shop_title')!=""){
                $page_top_template .="<div class=\"ow_box_cap_empty ow_dnd_configurable_component clearfix\">
                    <div class=\"ow_box_cap_right\">
                        <div class=\"ow_box_cap_body\">
                            <h3 class=\"ow_ic_info\">".OW::getConfig()->getValue('shoppro', 'config_page_on_top_shop_title')."</h3>
                        </div>
                    </div>
                </div>";
        }

        $page_top_template .="<div class=\"ow_box_empty ow_stdmargin clearfix index-PAGES_CMP_MenuWidget ow_no_cap ow_break_word\" style=\"\">
            <div class=\"clearfix ow_left\" style=\"margin:10px;\">
                ".$page_on_top_shop."
            </div>
        </div>";

        $page_top_template .="</div>";

            
                $content_t .=$page_top_template;
                $content_t .="</div>";
            }
*/

        $content_t .="<div class=\"ow_content_menu_wrap\">";
        $content_t .="<ul class=\"ow_content_menu clearfix\">";

//$mapmode="edit";
//        if ($selected=="1" OR $selected=="newsfeed" OR !$selected) $sel=" active ";
        if ($selected=="map" OR !$selected) $sel=" active ";
            else $sel="";
//echo $selected."===".$sel;exit;
//        $content_t .="<li class=\"_store_my_items ".$sel."\"><a href=\"".$curent_url."wall/newsfeed\"><span class=\"ow_ic_plugin\">".OW::getLanguage()->text('map', 'ta_newsfeed')."</span></a></li>";
        $content_t .="<li class=\"_store_my_items ".$sel."\"><a href=\"".$curent_url."map".$check_cat_ajaxx."\"><span class=\"ow_ic_flag\">".OW::getLanguage()->text('map', 'ta_map')."</span></a></li>";

/*
        if ( OW::getPluginManager()->isPluginActive('shoppro') AND OW::getConfig()->getValue('map', 'tabdisable_shop')!=1){
//            if ($id_user>0 OR $is_admin){
                if ($selected=="shop") $sel=" active ";
                    else $sel="";
                $content_t .="<li class=\"_store_my_items ".$sel."\"><a href=\"".$curent_url."wall/shop\"><span class=\"ow_ic_cart\">".OW::getLanguage()->text('map', 'ta_shop')."</span></a></li>";
//            }
        }

        if ( OW::getPluginManager()->isPluginActive('fanpage') AND OW::getConfig()->getValue('map', 'tabdisable_fanpage')!=1){
//            if ($id_user>0 OR $is_admin){
                if ($selected=="fanpage") $sel=" active ";
                    else $sel="";
//                $content_t .="<li class=\"_store_my_items ".$sel."\"><a href=\"".$curent_url."wall/fanpage\"><span class=\"ow_ic_photo\">".OW::getLanguage()->text('map', 'ta_fanpage')."</span></a></li>";
                $content_t .="<li class=\"_store_my_items ".$sel."\"><a href=\"".$curent_url."wall/fanpage\"><span class=\"ow_ic_friends\">".OW::getLanguage()->text('map', 'ta_fanpage')."</span></a></li>";
//            }
        }
*/
        if ($id_user){
            if ($selected=="edit") $sel=" active ";
                else $sel="";
            if ($is_admin){
                $content_t .="<li class=\"_store_my_items ".$sel."\"><a href=\"".$curent_url."map?mapmode=ed".$check_cat_ajax."#ef\"><span class=\"ow_ic_edit\">".OW::getLanguage()->text('map', 'ta_admin_edit_markers')."</span></a></li>";
            }else{
                $content_t .="<li class=\"_store_my_items ".$sel."\"><a href=\"".$curent_url."map?mapmode=ed".$check_cat_ajax."#ef\"><span class=\"ow_ic_edit\">".OW::getLanguage()->text('map', 'ta_edityourmarkers')."</span></a></li>";
            }
        }

        if ($selected=="zoom"){
            $sel=" active ";
//            $content_t .="<li class=\"_store_my_items ".$sel."\"><a href=\"".$curent_url."map?mapmode=ed#ef\"><span class=\"ow_ic_edit\">".OW::getLanguage()->text('map', 'ta_edityourmarkers')."</span></a></li>";
            $content_t .="<li class=\"_store_my_items ".$sel."\"><a href=\"javascript:void(0);\"><span class=\"ow_ic_doc\">".OW::getLanguage()->text('map', 'ta_marker_zoom')."</span></a></li>";
//            $content_t .="<li class=\"_store_my_items ".$sel."\"><span class=\"ow_ic_doc\">".OW::getLanguage()->text('map', 'ta_marker_zoom')."</span></li>";
        }



        if ( OW::getPluginManager()->isPluginActive('shoppro') AND OW::getConfig()->getValue('map', 'tabdisable_shop')!=1){
//            if ($id_user>0 OR $is_admin){
                if ($selected=="shop") $sel=" active ";
                    else $sel="";
                $content_t .="<li class=\"_store_my_items ".$sel."\"><a href=\"".$curent_url."map/tab/shop\"><span class=\"ow_ic_cart\">".OW::getLanguage()->text('map', 'ta_shop')."</span></a></li>";
//            }
        }

        if ( OW::getPluginManager()->isPluginActive('fanpage') AND OW::getConfig()->getValue('map', 'tabdisable_fanpage')!=1){
//            if ($id_user>0 OR $is_admin){
                if ($selected=="fanpage") $sel=" active ";
                    else $sel="";
//                $content_t .="<li class=\"_store_my_items ".$sel."\"><a href=\"".$curent_url."wall/fanpage\"><span class=\"ow_ic_photo\">".OW::getLanguage()->text('map', 'ta_fanpage')."</span></a></li>";
                $content_t .="<li class=\"_store_my_items ".$sel."\"><a href=\"".$curent_url."map/tab/fanpage\"><span class=\"ow_ic_friends\">".OW::getLanguage()->text('map', 'ta_fanpage')."</span></a></li>";
//            }
        }

        if ( OW::getPluginManager()->isPluginActive('news') AND OW::getConfig()->getValue('map', 'tabdisable_news')!=1){
//            if ($id_user>0 OR $is_admin){
                if ($selected=="news") $sel=" active ";
                    else $sel="";
//                $content_t .="<li class=\"_store_my_items ".$sel."\"><a href=\"".$curent_url."wall/fanpage\"><span class=\"ow_ic_photo\">".OW::getLanguage()->text('map', 'ta_fanpage')."</span></a></li>";
                $content_t .="<li class=\"_store_my_items ".$sel."\"><a href=\"".$curent_url."map/tab/news\"><span class=\"ow_ic_files\">".OW::getLanguage()->text('map', 'ta_news')."</span></a></li>";
//            }
        }

        if ($is_admin){
                if ($selected=="category") $sel=" active ";
                    else $sel="";
//                $content_t .="<li class=\"_store_my_items ".$sel."\"><a href=\"".$curent_url."wall/fanpage\"><span class=\"ow_ic_photo\">".OW::getLanguage()->text('map', 'ta_fanpage')."</span></a></li>";
                $content_t .="<li class=\"_store_my_items ".$sel."\"><a href=\"".$curent_url."map/tabc/category\"><span class=\"ow_ic_write\">".OW::getLanguage()->text('map', 'ta_category')."</span></a></li>";
//            }
        }

/*
        if ( OW::getPluginManager()->isPluginActive('event') AND OW::getConfig()->getValue('map', 'tabdisable_event')!=1){
//            if ($id_user>0 OR $is_admin){
                if ($selected=="event") $sel=" active ";
                    else $sel="";
//                $content_t .="<li class=\"_store_my_items ".$sel."\"><a href=\"".$curent_url."wall/fanpage\"><span class=\"ow_ic_photo\">".OW::getLanguage()->text('map', 'ta_fanpage')."</span></a></li>";
                $content_t .="<li class=\"_store_my_items ".$sel."\"><a href=\"".$curent_url."map/tab/event\"><span class=\"ow_ic_calendar\">".OW::getLanguage()->text('map', 'ta_event')."</span></a></li>";
//            }
        }
*/

/*
        if ( OW::getPluginManager()->isPluginActive('photo') AND OW::getConfig()->getValue('map', 'tabdisable_photo')!=1){
//            if ($id_user>0 OR $is_admin){
                if ($selected=="photo") $sel=" active ";
                    else $sel="";
//                $content_t .="<li class=\"_store_my_items ".$sel."\"><a href=\"".$curent_url."wall/photo\"><span class=\"ow_ic_photo\">".OW::getLanguage()->text('map', 'ta_photo')."</span></a></li>";
                $content_t .="<li class=\"_store_my_items ".$sel."\"><a href=\"".$curent_url."wall/photo\"><span class=\"ow_ic_picture\">".OW::getLanguage()->text('map', 'ta_photo')."</span></a></li>";
//            }
        }

        if ( OW::getPluginManager()->isPluginActive('video') AND OW::getConfig()->getValue('map', 'tabdisable_video')!=1){
//            if ($id_user>0 OR $is_admin){
                if ($selected=="video") $sel=" active ";
                    else $sel="";
                $content_t .="<li class=\"_store_my_items ".$sel."\"><a href=\"".$curent_url."wall/video\"><span class=\"ow_ic_video\">".OW::getLanguage()->text('map', 'ta_video')."</span></a></li>";
//            }
        }

        if ( OW::getPluginManager()->isPluginActive('blogs') AND OW::getConfig()->getValue('map', 'tabdisable_blogs')!=1){
                if ($selected=="blogs") $sel=" active ";
                    else $sel="";
                $content_t .="<li class=\"_store_my_items ".$sel."\"><a href=\"".$curent_url."wall/blogs\"><span class=\"ow_ic_comment\">".OW::getLanguage()->text('map', 'ta_blogs')."</span></a></li>";
        }


        if ( OW::getPluginManager()->isPluginActive('forum') AND OW::getConfig()->getValue('map', 'tabdisable_forum')!=1){
                if ($selected=="forum") $sel=" active ";
                    else $sel="";
                $content_t .="<li class=\"_store_my_items ".$sel."\"><a href=\"".$curent_url."wall/forum\"><span class=\"ow_ic_forum\">".OW::getLanguage()->text('map', 'ta_forum')."</span></a></li>";
        }

        if ( OW::getPluginManager()->isPluginActive('groups') AND OW::getConfig()->getValue('map', 'tabdisable_groups')!=1){
                if ($selected=="groups") $sel=" active ";
                    else $sel="";
                $content_t .="<li class=\"_store_my_items ".$sel."\"><a href=\"".$curent_url."wall/groups\"><span class=\"ow_ic_groups\">".OW::getLanguage()->text('map', 'ta_groups')."</span></a></li>";
        }

        if ( OW::getPluginManager()->isPluginActive('event') AND OW::getConfig()->getValue('map', 'tabdisable_event')!=1){
                if ($selected=="event") $sel=" active ";
                    else $sel="";
                $content_t .="<li class=\"_store_my_items ".$sel."\"><a href=\"".$curent_url."wall/event\"><span class=\"ow_ic_app\">".OW::getLanguage()->text('map', 'ta_event')."</span></a></li>";
        }
*/




/*
//    if  (SHOPPRO_BOL_Service::getInstance()->check_acces()){


//        if ($id_user>0 OR $is_admin){
        if (($id_user>0 AND OW::getConfig()->getValue('shoppro', 'mode_membercanshell')==1) OR $is_admin){
            if ($selected=="myitems") $sel=" active ";
                else $sel="";
            $content_t .="<li class=\"_store_my_items ".$sel."\"><a href=\"".$curent_url."shopmy/show\"><span class=\"ow_ic_plugin\">".OW::getLanguage()->text('shoppro', 'product_table_showmyitems')."</span></a></li>";//moje zamówienia
        }


//        $content_t .="<li class=\"_plugin \"><a href=\"http://www.oxwall.org/store\"><span class=\"ow_ic_plugin\">Plugins</span></a></li>";
//        $content_t .="<li class=\"_theme \"><a href=\"http://www.oxwall.org/store/themes\"><span class=\"ow_ic_plugin\">Themes</span></a></li>";
//        $content_t .="<li class=\"_store_purchase_list \"><a href=\"http://www.oxwall.org/store/granted-list\"><span class=\"ow_ic_cart\">My purchases</span></a></li>";
//        $content_t .="<li class=\"_store_my_items  active\"><a href=\"http://www.oxwall.org/store/list/my-items\"><span class=\"ow_ic_plugin\">My items</span></a></li>";
//        $content_t .="<li class=\"_store_dev_tools \"><a href=\"http://www.oxwall.org/store/dev-tools\"><span class=\"ow_ic_gear_wheel\">Developer tools</span></a></li>";


//        if (($id_user>0 AND OW::getConfig()->getValue('shoppro', 'mode_membercanshell')==1) OR $is_admin){
//        if ($id_user>0 AND $is_admin){
//        if ($id_user>0 OR $is_admin){
        if (($id_user>0 AND OW::getConfig()->getValue('shoppro', 'mode_membercanshell')==1) OR $is_admin){
            if ($selected==10 OR $selected=="admin") $sel=" active ";
                else $sel="";
            $content_t .="<li class=\"_store_dev_tools ".$sel."\"><a href=\"".$curent_url."ordershop/showorder\"><span class=\"ow_ic_gear_wheel\">".OW::getLanguage()->text('shoppro', 'product_table_showorder')."</span></a></li>";
        }


        if (OW::getConfig()->getValue('shoppro', 'mode_ads_approval') AND $is_admin AND $is_admin){
//            if ($id_user>0 AND $is_admin){
                if ($selected==11 OR $selected=="approval") $sel=" active ";
                    else $sel="";
                $content_t .="<li class=\"_store_dev_tools ".$sel."\"><a href=\"".$curent_url."ordershop/approval\"><span class=\"ow_ic_gear_wheel\">".OW::getLanguage()->text('shoppro', 'product_table_approvedlist')."</span></a></li>";
//            }
        }

                                if ($is_admin OR ($id_user>0 AND OW::getConfig()->getValue('shoppro', 'mode_membercanshell')==1) ){
//                                    $content .="&nbsp;|&nbsp;";
                
            if ($selected=="addnewproduct") $sel=" active ";
                else $sel="";
            $content_t .="<li class=\"_store_dev_tools ".$sel."\"><a href=\"".$curent_url."product/0/add\"><span class=\"ow_ic_add\">".OW::getLanguage()->text('shoppro', 'product_table_selyourproduct')."</span></a></li>";


                                }
//    }//if  (SHOPPRO_BOL_Service::getInstance()->check_acces()){
*/
$content_t .="</ul>";
$content_t .="</div>";

//        }//if not disabled tabs

        $content_t .=$content;
        $content_t .="</div>";
        return $content_t;
    }


    public function image_resize($file_source="",$crop=false,$width=800,$height=600)
    {
        return image_copy_resize($file_source,$file_source,$crop,$width,$height);
    }

    public function image_copy_resize($file_source="",$file_dest="",$crop=false,$width=800,$height=600)
    {
        if ($file_source AND $file_dest){
            $image = new UTIL_Image($file_source);
            $mainPhoto = $image ->resizeImage($width, $height,$crop) ->saveImage($file_dest);
            return true;
        }else{
            return false;
        }
    }

    public function get_plugin_dir($plugin="")
    {
        if ($plugin){
            return OW::getPluginManager()->getPlugin($plugin)->getUserFilesDir();
        }else{
            return false;
        }
    }
    public function get_plugin_url($plugin="")
    {
        if ($plugin){
            return OW::getStorage()->getFileUrl(OW::getPluginManager()->getPlugin($plugin)->getUserFilesDir());
        }else{
            return false;
        }
    }
    public function dir_mkdir($path="",$dir="")
    {
        if ($path AND $dir){
            $storage = OW::getStorage();
            if (!$storage->isWritable($path.$dir) ){
                $storage->mkdir($path.$dir);
            }
        }
    }

    public function file_copy($src="",$dest="")
    {
        if ($src AND $dest){
            $storage = OW::getStorage();
            return $storage->copyFile($src,$dest);
        }else{
            return false;
        }
    }

    public function file_delete($src="")
    {
        if ($src){
            $storage = OW::getStorage(); 
            if ( $storage->fileExists($src) )
            {
                $storage->removeFile($src);
            }
            return true;
        }else{
            return false;
        }
    }

    public function file_exist($src="")
    {
        if ($src){
            $storage = OW::getStorage(); 
            if ( $storage->fileExists($src) )
            {
                return true;
            }else {
                return false;
            }
        }else{
            return false;
        }
    }


 public function html2txt($document){
    $search = array('@<script[^>]*?>.*?</script>@si',  // Strip out javascript
               '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
               '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
               '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments including CDATA
    );
    $text = preg_replace($search, '', $document);
    $text=preg_replace("/(&?!amp;)/i", " ", $text);
    $text=preg_replace("/(&#\d+);/i", " ", $text); // For numeric entities
    $text=preg_replace("/(&\w+);/i", " ", $text); // For literal entities
    return $text;
}


public function hex2rgb($hex) {
   $hex = str_replace("#", "", $hex);

   if(strlen($hex) == 3) {
      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
   } else {
      $r = hexdec(substr($hex,0,2));
      $g = hexdec(substr($hex,2,2));
      $b = hexdec(substr($hex,4,2));
   }
   $rgb = array($r, $g, $b);
   //return implode(",", $rgb); // returns the rgb values separated by commas
   return $rgb; // returns an array with the rgb values
}

    public static function checkcurentlang()
    {
        $curent_language_id=0;
        if (isset($_SESSION['base.language_id']) AND $_SESSION['base.language_id']>0){
            $curent_language_id=$_SESSION['base.language_id'];
        }
        return $curent_language_id;
    }

    public function make_seo_url($name,$lengthtext=100)
    {
        $seo_title=stripslashes($name);
        $seo_title=str_replace(" ","_",$seo_title);
        $seo_title=str_replace(chr(160),"_",$seo_title);
        $seo_title=str_replace("~","",$seo_title);
        $seo_title=str_replace("(","",$seo_title);
        $seo_title=str_replace(")","",$seo_title);
        $seo_title=str_replace("]","",$seo_title);
        $seo_title=str_replace("[","",$seo_title);
        $seo_title=str_replace("}","",$seo_title);
        $seo_title=str_replace("{","",$seo_title);
        $seo_title=str_replace("/","",$seo_title);
        $seo_title=str_replace("\\","",$seo_title);
        $seo_title=str_replace("+","",$seo_title);
        $seo_title=str_replace(":","",$seo_title);
        $seo_title=str_replace(";","",$seo_title);
        $seo_title=str_replace("\"","",$seo_title);
        $seo_title=str_replace("<","",$seo_title);
        $seo_title=str_replace(">","",$seo_title);
        $seo_title=str_replace("?","",$seo_title);
        $seo_title=str_replace(",",".",$seo_title);
        $seo_title=str_replace("!","",$seo_title);
        $seo_title=str_replace("`","",$seo_title);
        $seo_title=str_replace("'","",$seo_title);
        $seo_title=str_replace("@","",$seo_title);
        $seo_title=str_replace("#","",$seo_title);
        $seo_title=str_replace("$","",$seo_title);
        $seo_title=str_replace("%","",$seo_title);
        $seo_title=str_replace("^","",$seo_title);
        $seo_title=str_replace("&","",$seo_title);
        $seo_title=str_replace("*","",$seo_title);
        $seo_title=str_replace("|","",$seo_title);
        $seo_title=str_replace("=","",$seo_title);
        $seo_title=str_replace(" ","_",$seo_title);
        $seo_title=str_replace("/","",$seo_title);
        $seo_title=str_replace("?","_",$seo_title);
        $seo_title=str_replace("#","_",$seo_title);
        $seo_title=str_replace("=","_",$seo_title);
        $seo_title=str_replace("=","_",$seo_title);
        $seo_title=str_replace("&amp;","_",$seo_title);
        $seo_title=str_replace("__","_",$seo_title);
        $seo_title = preg_replace('/[^(\x20-\x7F)\x0A]*/','', $seo_title);
        $seo_title =strtolower($seo_title);
        $seo_title=mb_substr($seo_title,0,100);
        return $seo_title;
    }



    public function make_upload_image_form()
    {
        $ret="";
        $id_user = OW::getUser()->getId();//citent login user (uwner)
        $is_admin = OW::getUser()->isAdmin();
        $curent_url=OW_URL_HOME;
        $content="";        
        $pluginStaticDir = OW_DIR_STATIC .'plugins'.DS.'map'.DS;
        $pluginStaticURL2=OW::getPluginManager()->getPlugin('map')->getStaticUrl();

//    $ret .="<form action=\"".$curent_url."mobile/v2/option/newscomment/".$option_item."\" method=\"post\" enctype=\"multipart/form-data\" data-ajax=\"false\">";
    $ret .="<form action=\"".$curent_url."map/gmap?op=upload_photo_a\" method=\"post\" enctype=\"multipart/form-data\" data-ajax=\"false\">";

$ret .="<div class=\"ow_box ow_break_word\" style=\"\">";


                if ($mode=="comment" AND $option_item>0){
                    $ret .="<div class=\"ow_form_options\">
                        <textarea name=\"f_desc\" id=\"f_desc\"  placeholder=\"".OW::getLanguage()->text('mobille', 'add_comment_content')."\" data-inline=\"true\" style=\"\" ></textarea>
                    </div>";
                }else{
                    $ret .="<div class=\"ow_form_options\">
                        <textarea name=\"f_desc\" id=\"f_desc\"  placeholder=\"".OW::getLanguage()->text('mobille', 'news_post_description')."\" data-inline=\"true\" style=\"\" ></textarea>
                    </div>";
                }

//                $ret .="<div class=\"ow_form_options\">
//                    <input type=\"text\" name=\"f_tags\" id=\"f_tags\"  value=\"\" placeholder=\"".OW::getLanguage()->text('mobille', 'blog_post_tags')."\" data-inline=\"true\" style=\"\" >
//                </div>";

                $ret .="<div class=\"ow_form_options\">
                    <input type=\"file\" name=\"upload1\" value=\"file\" data-inline=\"true\" style=\"width: 200px;min-width:100px;margin-bottom:10px;\"/>
                    <input type=\"file\" name=\"upload2\" value=\"file\" data-inline=\"true\" style=\"width: 200px;min-width:100px;margin-bottom:10px;\"/>
                    <input type=\"file\" name=\"upload3\" value=\"file\" data-inline=\"true\" style=\"width: 200px;min-width:100px;margin-bottom:10px;\"/>
                    <input type=\"file\" name=\"upload4\" value=\"file\" data-inline=\"true\" style=\"width: 200px;min-width:100px;margin-bottom:10px;\"/>
                    <input type=\"file\" name=\"upload5\" value=\"file\" data-inline=\"true\" style=\"width: 200px;min-width:100px;margin-bottom:10px;\"/>
                    <input type=\"file\" name=\"upload6\" value=\"file\" data-inline=\"true\" style=\"width: 200px;min-width:100px;margin-bottom:10px;\"/>
                </div>";



                $ret .="<div class=\"ow_form_options clearfix\" style=\"margin:20px;\">
                    <div class=\"ow_right\">
                        <span class=\"ow_button\"><span class=\" ow_positive\"><input type=\"submit\" value=\"".OW::getLanguage()->text('mobille', 'form_button_send_blogpost')."\" id=\"input_29942281\" class=\"ow_positive\" name=\"submit\"></span></span>
                    </div>
                </div>
            
    <div class=\"ow_box_bottom_left\"></div>
    <div class=\"ow_box_bottom_right\"></div>
    <div class=\"ow_box_bottom_body\"></div>
    <div class=\"ow_box_bottom_shadow\"></div>
</div>";

$ret .="<input type=\"hidden\" name=\"upload\" value=\"file\" />
<input type=\"hidden\" name=\"nmod\" value=\"".$mode."\" />";

if (isset($_GET['c']) AND $_GET['c']){
    $ret .="<input type=\"hidden\" name=\"cc\" value=\"".$_GET['c']."\" />";
}

$ret .="<input type=\"hidden\" name=\"fidn\" value=\"".$option_item."\" />
<input type=\"hidden\" name=\"uploadss\" value=\"".session_id()."\" />";



$ret .="</form>";
//        $ret .="</div>";
        
        return $ret;
    }




    public function get_profile_map($ptype="profile",$for_user=0)
    {
        $id_user = OW::getUser()->getId();//citent login user (uwner)
        $is_admin = OW::getUser()->isAdmin();
        $curent_url=OW_URL_HOME;
        $content="";        
        $pluginStaticDir = OW_DIR_STATIC .'plugins'.DS.'map'.DS;
        $pluginStaticURL2=OW::getPluginManager()->getPlugin('map')->getStaticUrl();
$script ="";
        OW::getDocument()->addScript('http://maps.google.com/maps/api/js?v=3&sensor=true');


        OW::getDocument()->addScript('http://jquery-ui-map.googlecode.com/svn/trunk/ui/jquery.ui.map.js');
        OW::getDocument()->addScript('http://jquery-ui-map.googlecode.com/svn/trunk/ui/jquery.ui.map.services.js');
        OW::getDocument()->addScript('http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.9/jquery-ui.min.js');

        OW::getDocument()->addStyleSheet('http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.9/themes/base/jquery-ui.css');

        OW::getDocument()->addScript('http://google-maps-utility-library-v3.googlecode.com/svn/tags/markermanager/1.0/src/markermanager.js');
        OW::getDocument()->addScript('http://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclusterer/src/markerclusterer_compiled.js');

//        $query_add=" AND (mm.name LIKE '%".addslashes($querymap)."%' OR LOWER(mm.name) LIKE '%".addslashes($querymap)."%') ";
        if ($is_admin){
            $add=" 1 ";
        }else{
            $add=" active='1' ";
        }


//echo $for_user."==".$id_user;exit;

$perpage=10;
$records ="";
$found_self=false;
$found_friends=false;

if (isset($_GET['mode']) AND $_GET['mode']=="edit"){
    $curent_mode="edit";
}else{
    $curent_mode="normal";
}
///echo $curent_mode;exit;
//        if ($for_user>0){
//            $dname_for=BOL_UserService::getInstance()->getDisplayName($for_user);
//        }else{
//        }

//        $la="31.653381399664";
//        $ln="-42.5390625";
//        $la="52.079506";
//        $ln="10.188446";
        $la="53.928704";
        $ln="14.226509";

        $zo="2";


        $row_owner=array();
        $row_owner['id']=0;
        $row_owner['id_owner']=0;
        
        if ($for_user>0){
            $query = "SELECT * FROM " . OW_DB_PREFIX. "map_home WHERE idh_owner='".addslashes($for_user)."' LIMIT 1";
            $arr = OW::getDbo()->queryForList($query);
            if (isset($arr[0]['idh_owner']) AND $arr[0]['idh_owner']>0 AND isset($arr[0]['home_lat']) AND isset($arr[0]['home_lon']) AND $arr[0]['home_lat'] AND $arr[0]['home_lon'] ){
//echo $query;
                $found_self=true;
                if ($for_user!=$id_user){
//echo $query;
                    $row_owner=$arr[0];
                    $lat=$row_owner['home_lat'];
                    $lon=$row_owner['home_lon'];
//                if ($row_owner['ico']) $ico=$row_owner['ico'];
//                    else $ico="world";
//                $ico="world";
                    $la=$lat;
                    $ln=$lon;
                    $zo=14;

//                    $records .="var icon_owner=new google.maps.MarkerImage('".$pluginStaticURL2."ico/'+iconx+'.png',
//                        new google.maps.Size(32, 32), new google.maps.Point(0, 0),
//                        new google.maps.Point(16, 32)
//                    );";

                    $dname=BOL_UserService::getInstance()->getDisplayName($row_owner['idh_owner']);
                    $uurl=BOL_UserService::getInstance()->getUserUrl($row_owner['idh_owner']);
                    $uimg=BOL_AvatarService::getInstance()->getAvatarUrl($row_owner['idh_owner']);
                    if (!$uimg) {
                        $uimg=$curent_url."ow_static/themes/".OW::getConfig()->getValue('base', 'selectedTheme')."/images/no-avatar.png";
                    }


                    $records .="var icon_owner".$row_owner['idh_owner']."=new google.maps.MarkerImage('".$uimg."',
                        null,
                        null,
                        null,
                        new google.maps.Size(48, 48)
                    );";

//                    $records .="addMarker('1','".$lat."', '".$lon."','0','oldicon','','".$row_owner['idh_owner']."','map',icon_owner".$value['idh_owner'].");\n";
                    $records .="addMarker('1','".$lat."', '".$lon."','0','oldicon','','".$row_owner['idh_owner']."','map',icon_owner".$row_owner['idh_owner'].");\n";
                }else{
                    $row_owner=$arr[0];
                    $lat=$row_owner['home_lat'];
                    $lon=$row_owner['home_lon'];

                    $la=$lat;
                    $ln=$lon;
                    $zo=14;

                    $uimg=BOL_AvatarService::getInstance()->getAvatarUrl($row_owner['idh_owner']);

                    $row_owner=array();
                    $row_owner['id']=0;
                    $row_owner['id_owner']=0;


//                    $ico="world";
//                    $records .="addMarker('1','".$lat."', '".$lon."','0','oldico','','".$row_owner['idh_owner']."','map',icon_owner".$row_owner['idh_owner'].");\n";
//                    $records .="addMarker('1','".$lat."', '".$lon."','0','".$ico."','','".$row_owner['idh_owner']."','map','');\n";
//                    $records .="addMarker('1','".$lat."', '".$lon."','0','none','','".$row_owner['idh_owner']."','map','');\n";
                    if ($curent_mode!="edit"){
                        $records .="var icon_ownerx".$id_user."=new google.maps.MarkerImage('".$uimg."',
                            null,
                            null,
                            null,
                            new google.maps.Size(48, 48)
                        );";
//                        $records .="addMarker('1','".$lat."', '".$lon."','0','oldicon','','".$row_owner['idh_owner']."','map',icon_ownerx".$row_owner['idh_owner'].");\n";
                        $records .="addMarker('1','".$lat."', '".$lon."','0','oldicon','','".$id_user."','map',icon_ownerx".$id_user.");\n";
                    }
                }
//TODO!!!!!!!!!!!!!!!!!!!!!!!!
/*
            }else {
                 $curent_mode="edit";
                    $zo=18;
//                    $records .="addMarker('1','".$lat."', '".$lon."','0','oldico','','".$row_owner['idh_owner']."','map',icon_owner".$row_owner['idh_owner'].");\n";
                    $ico="world";
//                    $records .="addMarker('1','".$la."', '".$ln."','0','oldico','','".$row_owner['idh_owner']."','map','');\n";
//                    $records .="addMarker('1','".$la."', '".$ln."','0','".$ico."','','1','map','');\n";
                    $records .="addMarker('1','".$la."', '".$ln."','0','none','','1','map','');\n";
*/
            }
        }//if ($for_user>0){


//echo $curent_mode;exit;

        $found=false;
        $total_was=0;
        if ($ptype=="profile" AND OW::getPluginManager()->isPluginActive('friends') AND $for_user>0){


//        if ($for_user!=$id_user){
            $query = "SELECT * FROM " . OW_DB_PREFIX. "friends_friendship WHERE (userId='".addslashes($for_user)."' OR friendId='".addslashes($for_user)."') AND status='active' GROUP BY userId, friendId;";
//echo $query ;exit;
            $arr = OW::getDbo()->queryForList($query);
            $fr_arr="";
            foreach ( $arr as $value ){
                if ($value['userId']==$for_user  AND $value['friendId']>0){
                    if ($fr_arr) $fr_arr .=",";
                    $fr_arr .=$value['friendId'];
                }else if ($value['friendId']==$for_user AND $value['userId']>0){
                    if ($fr_arr) $fr_arr .=",";
                    $fr_arr .=$value['userId'];
                }
            }
//        }
            if ($fr_arr){
//                    LEFT JOIN " . OW_DB_PREFIX. "map_images mmi ON (mmi.id_map=mm.id AND is_default='1') 
//                $query = "SELECT * FROM " . OW_DB_PREFIX. "map mm 
//                WHERE ".$add." ANS id_owner IN (".$fr_arr.") AND home_lat IS NOT NULL AND home_lon IS NOT NULL GROUP BY mm.id LIMIT 1000";
                $query = "SELECT * FROM " . OW_DB_PREFIX. "map_home WHERE idh_owner IN (".$fr_arr.") LIMIT 1000";
//echo $query;exit;
                $arr = OW::getDbo()->queryForList($query);
                foreach ( $arr as $value ){
                    if(!$found_friends) $found_friends=true;
//                    if ($value['home_lat'] AND $value['home_lon'] 
                    $lat=$value['home_lat'];
                    $lon=$value['home_lon'];
//                    if ($value['ico']) $ico=$value['ico'];
//                        else $ico="world";
//                    $ico="world";
//                    $records .="addMarker('1','".$lat."', '".$lon."','0','".$ico."','','".$value['idh_owner']."','map');\n";

                    $dname=BOL_UserService::getInstance()->getDisplayName($value['idh_owner']);
                    $uurl=BOL_UserService::getInstance()->getUserUrl($value['idh_owner']);
                    $uimg=BOL_AvatarService::getInstance()->getAvatarUrl($value['idh_owner']);
                    if (!$uimg) {
                        $uimg=$curent_url."ow_static/themes/".OW::getConfig()->getValue('base', 'selectedTheme')."/images/no-avatar.png";
                    }


                    $records .="var icon_member".$value['idh_owner']."=new google.maps.MarkerImage('".$uimg."',
                        null,
                        null,
                        null,
                        new google.maps.Size(32, 32)
                    );";
/*
            $records .="var icon_member".$value['idh_owner']."=new google.maps.MarkerImage('".$pluginStaticURL2."ico/'+iconx+'.png',
                new google.maps.Size(32, 32), new google.maps.Point(0, 0),
                new google.maps.Point(16, 32)
            );";
*/

//                        new google.maps.Size(32, 32),
//                        new google.maps.Point(0, 0),
//                        new google.maps.Point(16, 32)

/*
        var markerImage = new google.maps.MarkerImage(
            imageUrl,
            null,//size
            null,//origin
            null,//anchor
            new google.maps.Size(32,32)//scale
        );
*/
//echo $for_user;
//echo $uimg;
// var icon = new google.maps.MarkerImage('http://maps.google.com/mapfiles/ms/micons/blue.png',
// new google.maps.Size(32, 32), new google.maps.Point(0, 0),
// new google.maps.Point(16, 32));


                    $records .="addMarker('1','".$lat."', '".$lon."','0','oldico','','".$value['idh_owner']."','map',icon_member".$value['idh_owner'].");\n";



                    if (!$found) $found=true;
            
                    $total_was=$total_was+1;
                    if ($perpage-$total_was<0){
                        break;
                        $perpagex=0;
                    }
    
                } 
                unset($fr_arr);
                unset($arr);  
            }
        }else if ($ptype=="all" AND $for_user>0){//if if ( OW::getPluginManager()->isPluginActive('//friends_friendship')
//            if ($fr_arr){
                $query = "SELECT * FROM " . OW_DB_PREFIX. "map_home WHERE idh_owner<>'".addslashes($for_user)."' LIMIT 1200";
                $arr = OW::getDbo()->queryForList($query);
                foreach ( $arr as $value ){
                    if(!$found_friends) $found_friends=true;
                    $lat=$value['home_lat'];
                    $lon=$value['home_lon'];

                    $dname=BOL_UserService::getInstance()->getDisplayName($value['idh_owner']);
                    $uurl=BOL_UserService::getInstance()->getUserUrl($value['idh_owner']);
                    $uimg=BOL_AvatarService::getInstance()->getAvatarUrl($value['idh_owner']);
                    if (!$uimg) {
                        $uimg=$curent_url."ow_static/themes/".OW::getConfig()->getValue('base', 'selectedTheme')."/images/no-avatar.png";
                    }

                    $records .="var icon_member".$value['idh_owner']."=new google.maps.MarkerImage('".$uimg."',
                        null,
                        null,
                        null,
                        new google.maps.Size(32, 32)
                    );";
                    $records .="addMarker('1','".$lat."', '".$lon."','0','oldico','','".$value['idh_owner']."','map',icon_member".$value['idh_owner'].");\n";

                    if (!$found) $found=true;
            
                    $total_was=$total_was+1;
                    if ($perpage-$total_was<0){
                        break;
                        $perpagex=0;
                    }
    
                }   
//                unset($fr_arr);
                unset($arr);
//            }        

        }//end if ($ptype=="all"){

        $content .="<a name=\"mapx\"></a>";

$check_cat_ajax="";


        $script .="
 var last_found;
 var icon = new google.maps.MarkerImage('http://maps.google.com/mapfiles/ms/micons/blue.png',
 new google.maps.Size(32, 32), new google.maps.Point(0, 0),
 new google.maps.Point(16, 32));
 var center = null;
 var map = null;
 var currentPopup;
 var bounds = new google.maps.LatLngBounds();

 var marker;
        ";

        if ($for_user==$id_user AND $id_user>0 AND $curent_mode=="edit"){

            $script .="var mapmode='edit';";
        }else{
//echo "sdfsdfsdf";exit;
            $script .="var mapmode='map';";
//        $script .="var mapmode='edit';";
        }


        $script .="
    var gmarkers = [];
var mc = null;
var map = null;
var showMarketClusterer = false;
var foundresults=true;
//var xmarkers = [];


    

    function addMarker(mcategory,lat, lng, info, iconx,seekp,idmarker,pname,ico_image) {
        if (ico_image!=undefined && ico_image!='') {
            var iconok=ico_image;
        }else if (iconx!=undefined && iconx!='') {
            var iconok=new google.maps.MarkerImage('".$pluginStaticURL2."ico/'+iconx+'.png',
                new google.maps.Size(32, 32), new google.maps.Point(0, 0),
                new google.maps.Point(16, 32)
            );
        }else{
            var iconok=iconx;
        }

        if (seekp!=undefined && seekp=='random'){
            var min = .999999;
            var max = 1.000001;
            lat=lat*(Math.random() * (max - min) + min);
            lng=lng*(Math.random() * (max - min) + min);
        }


         var pt = new google.maps.LatLng(lat, lng);
         bounds.extend(pt);

         var marker = new google.maps.Marker({
             position: pt,
             icon: iconok,
             map: map,
//title:mcategory,
zIndex: Math.round(pt.lat()*-100000)<<5

        });

marker.mycategory = 'c_'+mcategory;
marker.myname = name;
gmarkers.push(marker);





















         var popup = new google.maps.InfoWindow({
             content: info,
             maxWidth: 300
         });
/*
         google.maps.event.addListener(marker, 'click', function() {
            if (currentPopup != null) {
                 currentPopup.close();
                 currentPopup = null;
            }
            popup.open(map, marker);
            currentPopup = popup;
         });
*/

        google.maps.event.addListener(marker, 'click', function() {

                var popup = new google.maps.InfoWindow({
                 content: '<div id=\"marker-info\"><img src=\"".$pluginStaticURL2."loading.gif\"> ".OW::getLanguage()->text('map', 'loading_pleasewait')."</div>',
                 maxWidth: 300
                });
                if (currentPopup != null) {
                 currentPopup.close();
                 currentPopup = null;
                }
                popup.open(map, marker);
                currentPopup = popup;
              load_content_profile(marker, info,idmarker,pname);
        });

         google.maps.event.addListener(popup, 'closeclick', function() {
//                 map.panTo(center);
             currentPopup = null;
         });
    }





    function addres2lat(seachaddres,title,description,icon){
        geocoder_map = new google.maps.Geocoder();
        if (icon==undefined) var icon='blue';
        return geocoder_map.geocode( { 'address':seachaddres}, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                addMarker('',results[0].geometry.location.lat(),results[0].geometry.location.lng(),'<b>'+title+'</b><br/>'+description,icon);
            }else {
                return;
//                alert('Geocode for '+seachaddres+' was not successful for the following reason: ' + status);
            }
        });
    }



    function placeMarker(location) {
      if ( marker ) {
        marker.setPosition(location);
      } else {
        marker = new google.maps.Marker({
          position: location,
          map: map
        });
      }
    }



    function load_content(marker, info,id,pname){
      $.ajax({
        url: '".$curent_url."map/get/'+id+'/'+info+'/'+pname+'?mm='+mapmode+'".$check_cat_ajax."',
        success: function(data){

            var popup = new google.maps.InfoWindow({
             content: data,
             maxWidth: 300
            });


            if (currentPopup != null) {
                 currentPopup.close();
                 currentPopup = null;
            }
            popup.open(map, marker);
            currentPopup = popup;


        }
      });
    }


    function load_content_profile(marker, info,id,pname){
      $.ajax({
        url: '".$curent_url."map/getprofile/'+id+'/".substr(session_id(),2,5)."',
        success: function(data){

            var popup = new google.maps.InfoWindow({
             content: data,
             maxWidth: 300
            });


            if (currentPopup != null) {
                 currentPopup.close();
                 currentPopup = null;
            }
            popup.open(map, marker);
            currentPopup = popup;


        }
      });
    }

    function save_owner_home_position(m_lat,m_lon){

        $.ajax({  
            type: 'POST',
            url: '".$curent_url."map/saveprofile',  
            data: { 'per_lan':m_lat , 'per_lon': m_lon,'ss':'".substr(session_id(),2,5)."','us':".$id_user." },  
            dataType: 'json',
            success: function(data) {
            }
        });  

    }















    function initMap() {

var trafficLayer = new google.maps.TrafficLayer();

        document.getElementById('map_canvas').innerHTML = '<div id=\"map\" width=\"1024\" height=\"550\"></div>'; 


        if (mapmode=='edit'){
             map = new google.maps.Map(document.getElementById('map'), {
                 center: new google.maps.LatLng(".$la.", ".$ln."),
                 zoom: ".$zo.",
panControl: true,
mapTypeControl: true,
                 mapTypeId: google.maps.MapTypeId.ROADMAP,
                 mapTypeControlOptions: {
                    style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR
                },
                 navigationControl: true,
                 navigationControlOptions: {
//                    style: google.maps.NavigationControlStyle.SMALL
style: google.maps.NavigationControlStyle.ZOOM_PAN
                }
            });
//        }else if (foundresults==true){
        }else{
             map = new google.maps.Map(document.getElementById('map'), {
                 center: new google.maps.LatLng(".$la.", ".$ln."),
                 zoom: ".$zo.",
panControl: true,
mapTypeControl: true,
                 mapTypeId: google.maps.MapTypeId.ROADMAP,
                 mapTypeControlOptions: {
                    style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR
                },
                 navigationControl: true,
                 navigationControlOptions: {

style: google.maps.NavigationControlStyle.ZOOM_PAN
                }
            });
//         map.panTo(center);
        }

//    infoWindow = new google.maps.InfoWindow();
        var geocoder = new google.maps.Geocoder();  
        $('#searchbox').autocomplete({
           source: function(request, response) {

              if (geocoder == null){
                   geocoder = new google.maps.Geocoder();
              } 
             geocoder.geocode( {'address': request.term }, function(results, status) {
               if (status == google.maps.GeocoderStatus.OK) {

                  var searchLoc = results[0].geometry.location;
               var lat = results[0].geometry.location.lat();
                  var lng = results[0].geometry.location.lng();
                  var latlng = new google.maps.LatLng(lat, lng);
                  var bounds = results[0].geometry.bounds;

                  geocoder.geocode({'latLng': latlng}, function(results1, status1) {
                      if (status1 == google.maps.GeocoderStatus.OK) {
                        if (results1[1]) {
                         response($.map(results1, function(loc) {
                        return {
                            label  : loc.formatted_address,
                            value  : loc.formatted_address,
                            bounds   : loc.geometry.bounds
                          }
                        }));
                        }
                      }
                    });
            }
              });
           },
           select: function(event,ui){
              var pos = ui.item.position;
              var lct = ui.item.locType;
              var bounds = ui.item.bounds;
              if (bounds){
                    map.fitBounds(bounds);
              }
           }
        });

        if (mapmode=='edit'){

            placeMarker(new google.maps.LatLng(".$la.", ".$ln."));

            google.maps.event.addListener(map, 'click', function(event) {

                $('#latMap').val(event.latLng.lat());
                $('#lngMap').val(event.latLng.lng());
                $('#zoomMap').val(map.getZoom());
                placeMarker(event.latLng);
                save_owner_home_position(event.latLng.lat(),event.latLng.lng());
            }); 
        }

    
        ".$records."        

        if (mapmode!='edit' && foundresults==true){
             center = bounds.getCenter();
             map.fitBounds(bounds);
        }

        map.panTo(new google.maps.LatLng(".$la.", ".$ln."));
        map.setZoom(".$zo.");









var controlDiv = document.createElement('DIV');
$(controlDiv).addClass('gmap-control-container')
             .addClass('gmnoprint');
          
var controlUI = document.createElement('DIV');
$(controlUI).addClass('gmap-control');
$(controlUI).text('".OW::getLanguage()->text('map', 'traffic_button')."');
$(controlDiv).append(controlUI);
          
var legend = '<ul>'
           + '<li><span style=\"background-color: #30ac3e\">&nbsp;&nbsp;</span><span style=\"color: #30ac3e\"> &gt; 80 km ".OW::getLanguage()->text('map', 'traffic_perhour')."</span></li>'
           + '<li><span style=\"background-color: #ffcf00\">&nbsp;&nbsp;</span><span style=\"color: #ffcf00\"> 40 - 80 km ".OW::getLanguage()->text('map', 'traffic_perhour')."</span></li>'
           + '<li><span style=\"background-color: #ff0000\">&nbsp;&nbsp;</span><span style=\"color: #ff0000\"> &lt; 40 km ".OW::getLanguage()->text('map', 'traffic_perhour')."</span></li>'
           + '<li><span style=\"background-color: #c0c0c0\">&nbsp;&nbsp;</span><span style=\"color: #c0c0c0\"> ".OW::getLanguage()->text('map', 'traffic_notavaiable')."</span></li>'
           + '</ul>';
          
var controlLegend = document.createElement('DIV');
$(controlLegend).addClass('gmap-control-legend');
$(controlLegend).html(legend);
$(controlLegend).hide();
$(controlDiv).append(controlLegend);
$(controlUI).mouseenter(function() {
    $(controlLegend).show();
})
.mouseleave(function() {
    $(controlLegend).hide();
});
          
google.maps.event.addDomListener(controlUI, 'click', function() {
    if (typeof trafficLayer.getMap() == 'undefined' || trafficLayer.getMap() === null) {
        $(controlUI).addClass('gmap-control-active');
        trafficLayer.setMap(map);
    } else {
        trafficLayer.setMap(null);
        $(controlUI).removeClass('gmap-control-active');
    }

});
          
map.controls[google.maps.ControlPosition.TOP_RIGHT].push(controlDiv);







if (gmarkers.length>0){
    gtoggleMarkerClusterer();//tutn on clusters
}




";
        if ($found_self==false){
//            $script .="
//                map.panTo(new google.maps.LatLng(0,0));
//                google.map.setZoom(2);
//            map.fitBounds(bounds);
//            map.setZoom(3);
//alert('sss');
//            ";
        }




$script .="

    }//end init mp




function gtoggleMarkerClusterer() {
  showMarketClusterer = !showMarketClusterer;
  if (showMarketClusterer) {
    if (mc) {
      mc.addMarkers(gmarkers);
    } else {
      mc = new MarkerClusterer(map, gmarkers, {'maxZoom': 13});
    }
  } else {
    mc.clearMarkers();
  }
}


      function show_category(category) {
        for (var i=0; i<gmarkers.length; i++) {
          if (gmarkers[i].mycategory == category) {
            gmarkers[i].setVisible(true);
          }
        }
      }

      function hide_category(category) {
        for (var i=0; i<gmarkers.length; i++) {
          if (gmarkers[i].mycategory == category) {
            gmarkers[i].setVisible(false);
          }
        }
//        document.getElementById('swc_'+category).checked = false;
      }



$(function(){
    $('#map_thumb_ico').css('background-image', 'url(".$curent_url."ow_static/plugins/map/ico/'+$('#f_iconmarker').val()+'.png)');  
    $('#map_thumb_ico').css('background-repeat', 'no-repeat');  



    $('#f_iconmarker').change(function() {
        $('#map_thumb_ico').css('background-image', 'url(".$curent_url."ow_static/plugins/map/ico/'+$(this).val()+'.png)');  
        $('#map_thumb_ico').css('background-repeat', 'no-repeat');  
    });


    $('.showhide_category').click(function() {
            if ($(this).attr('checked')=='checked'){
                show_category('c_'+$(this).attr('swc'));
            }else{
                hide_category('c_'+$(this).attr('swc'));
            }
    });

        initMap();







var gmaps_full = 0;
var ffs='<a href=\"#\" title=\"".str_replace("'","",str_replace("\"","",OW::getLanguage()->text('map', 'togglefullscreenview')))."\" id=\"gmaps_fullscreen\">'
    + '<img src=\"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH1gMQEw014nffmgAAADV0RVh0Q29tbWVudAAoYykgMjAwNCBKYWt1YiBTdGVpbmVyCgpDcmVhdGVkIHdpdGggVGhlIEdJTVCQ2YtvAAAB1klEQVQ4y52TO2iTURTHf/+bGzUVadMlSPEFcQgIgiCUKnYqFF9QxcnH4NDBKtXFQXBwSPma4KhgBsFBUALt4LNWUXRQcQ0tiiUtFkVqTVMVTUz7dfiS2LwccuDAhXPP7/7PuefIiUVvAP00ZwmcWNRt1pxY1LUl1O7+2xXoJ8O9AEgGcOm5+Kgi/i5xHABTrckY/UsGJJDE0/h+jFFNDaY6eUPAz4Xrb7yXZVDRTzkvaFlrsT7VB0gw5vSysT1AuKOVe69nkDwZd55/ZMfWdkLBAOPxg/hWKSn3YDx2AIBrg3sZfTVNKBjg/adFrDWEO9pYY31cOrELF/Hs6qFagORVLaBv3zYmpjNEtrRhZPgwu0BnJFy+566qvAywtnT0IH6/xfr8yIj1LevwWQsSwgVX9XpgkISRIfFggrmFP0x9zjI795PsrzwjL6eKv2JKcisB3edHkQzHrjxmciZDKv2d7ZuCbA61kkrP83byK0cuPwRgz9lkLcB1oXtwhMyPHOkvi5w7uhNJSOJkT4RUep5v2d90DSQbz8GyC7m/S9x3DnudKAIkMRbvY2nZ/f8glaxrIIkxwshzZOg8c7fuNtnq2W5kjeK2UCjcHI4PnW5ml/O5/K0VjtO5n1HbI70AAAAASUVORK5CYII=\" style=\"z-index: 2; border: none; position: absolute; top: 5px; left: 5px;\">'
    + '<span style=\"z-index: 3; border: none; position: absolute; top: 2px;left: 20px;font-weight: bold;\">&nbsp;".str_replace("'","",str_replace("\"","",OW::getLanguage()->text('map', 'fillscreen')))."</span>'
    + '</a>';
$('#map').append(ffs);
$('#map #gmaps_fullscreen').click(function() {
  if (!jQuery('#map').data('fullscreen')) {
    jQuery('#map').data('fullscreen', jQuery('#map').attr('style'));
  }
  if (gmaps_full == 1) {
    jQuery('#map').attr('style', jQuery('#map').data('fullscreen'));
    jQuery('object').show();
    gmaps_full = 0;
  } else {
    jQuery('object').hide();
    gmaps_full = 1;
    jQuery('#map').data('fullscreen', jQuery('#map').attr('style'));
    jQuery('#map').css('position', 'fixed').css('z-index', parseInt(10100, 10));
    jQuery('#map').css('width', '100%').css('height', '100%');
    jQuery('#map').css('top', '0').css('left', '0');
  }
  google.maps.event.trigger(eval('map'), 'resize');
  return false;
    });







$('#filter').keyup(function(){

        // Retrieve the input field text and reset the count to zero
        var filter = $(this).val(), count = 0;
 
        // Loop through the comment list
        $('#check_category .fil_check').each(function(){
            // If the list item does not contain the text phrase fade it out
            if ($(this).text().search(new RegExp(filter, 'i')) < 0) {
                $(this).fadeOut();
 
            // Show the list item if the phrase matches and increase the count by 1
            } else {
                $(this).show();
                count++;
            }
        });
 
        // Update the count
        var numberItems = count;
        if (filter!=undefined && filter!=null && filter!=''){
            $('#filter-count').text('".OW::getLanguage()->text('map', 'numberoffound')." = '+count);
        }else{
            $('#filter-count').text('');
        }
});













});


//    $(window).load(function(){
//        initMap();
//        hide_category('aa');
//    });

//google.maps.event.addDomListener(window, 'load', initMap);    

";

        OW::getDocument()->addOnloadScript($script);


        $content_css ="
#map {
width: 100%;
height: 550px;
border: 0px;
padding: 0px;
}


.gmap-control-container {
    margin: 5px;
}
.gmap-control {
    cursor: pointer;
    background-color: -moz-linear-gradient(center top , #FEFEFE, #F3F3F3);
    background-color: #FEFEFE;
    border: 1px solid #A9BBDF;
    border-radius: 2px;
    padding: 0 6px;
    line-height: 160%;
    font-size: 12px;
    font-family: Arial,sans-serif;
    box-shadow: 2px 2px 3px rgba(0, 0, 0, 0.35);
    -webkit-user-select: none;
    -khtml-user-select: none;
    -moz-user-select: none;
    -o-user-select: none;
    user-select: none;
}
.gmap-control:hover {
    border: 1px solid #678AC7;
}
.gmap-control-active {
    background-color: -moz-linear-gradient(center top , #6D8ACC, #7B98D9);
    background-color: #6D8ACC;
    color: #fff;
    font-weight: bold;
    border: 1px solid #678AC7;
}
.gmap-control-legend {
    position: absolute;
    text-align: left;
    z-index: -1;
    top: 20px;
    right: 0;
    width: 150px;
    height: 66px;
    font-size: 10px;
    background: #FEFEFE;
    border: 1px solid #A9BBDF;
    padding: 10px;
    box-shadow: 2px 2px 3px rgba(0, 0, 0, 0.35);
}
.gmap-control-legend ul {
    margin: 0;
    padding: 0;
    list-style-type: none;
}
.gmap-control-legend li {
    line-height: 160%;
}















.popup_deal_box {
width: 290px;
height: 110px;
color: #5b5b5b;
position: relative;
overflow: hidden;
margin: 5px;
}
div.olMapViewport {
text-align: left;
}
Xdiv.olMap {
cursor: default;
}
.shadow_main_item, .map_shadow_box {
zoom: 1;
}
.popup_deal_navigation {
display: none;
border-bottom: 1px solid #D5D5D5;
margin-bottom: 2px;
padding-bottom: 2px;
}
.popup_deal_headline {
font-size: 13px;
border-bottom: 1px solid #D5D5D5;
padding-bottom: 1px;
margin-bottom: 5px;
height: 16px;
overflow: hidden;
}
.popup_deal_content_right {
width: 82px;
float: right;
}
.popup_deal_content {
width: 202px;
height: 80px;
overflow: hidden;
}
.popup_deal_box p {
margin: 0;
padding: 0;
}
.popup_deal_box p {
margin: 0;
padding: 0;
}
.popup_deal_address {
font-size: 11px;
display: none;
}
.popup_deal_shrink, .popup_deal_enlarge {
font-size: 10px;
text-decoration: underline;
cursor: pointer;
position: absolute;
bottom: 0;
left: 0;
}
.popup_deal_shrink {
display: none;
}
.popup_deal_box p {
margin: 0;
padding: 0;
}

popup_deal_street p, popup_deal_city_zip p {
display: block;
-webkit-margin-before: 1em;
-webkit-margin-after: 1em;
-webkit-margin-start: 0px;
-webkit-margin-end: 0px;
}
.popup_deal_navigation span {
font-weight: bold;
}

.popup_deal_image {
border: 1px solid #EBEBEB;
}

.popup_deal_buy_button {
background: red;
width: 72px;
height: 19px;
line-height: 19px;
text-align: center;
color: #fff;
margin: 5px auto 0 auto;
background: rgb(170, 0, 0) url('".$pluginStaticURL2."button_popup.png') 0 0 no-repeat;
cursor: pointer;
}
.popup_deal_merchant {
font-weight: bold;
display: inline-block;
margin-bottom: 2px !important;
}
.popup_deal_title {
font-size: 11px;
}
.popup_deal_headline {
font-size: 13px;
border-bottom: 1px solid #D5D5D5;
padding-bottom: 1px;
margin-bottom: 5px;
height: 16px;
overflow: hidden;
}
.popup_deal_headline {
font-size: 13px;
}
.popup_deal_disount_text {
float: right;
margin-right: 5px;
}
.popup_deal_disount_percent {
float: right;
}
.popup_deal_category {
font-weight: bold;
}
.popup_deal_price {
font-weight: bold;
}
.popup_deal_disount_percent {
font-weight: bold;
}




</style>";

        $content .="<style>".$content_css."</style>";

//if ($ptype=="profile"){
//        if (isset($_GET['mode']) AND $_GET['mode']=="edit"){
        if ($curent_mode=="edit"){
        $content .="<div class=\"clearfix ow_submit ow_smallmargin\">
                <div class=\"ow_center\">
<a href=\"".$curent_url."my-profile#mapx\" style=\"display:inline-block;\">
                    <span class=\"ow_button\">
                        <span class=\"ow_negative\">
                            <input type=\"submit\" name=\"saveb\" value=\"".OW::getLanguage()->text('map', 'finish_changing_position_onthemap')."\" class=\"ow_ic_gear_wheel ow_negative\">
                        </span>
                    </span>
</a>
                </div>
            </div>";
        }else{
        $content .="<div class=\"clearfix ow_submit ow_smallmargin\">
                <div class=\"ow_center\">
<a href=\"".$curent_url."my-profile?mode=edit#mapx\" style=\"display:inline-block;\">
                    <span class=\"ow_button\">
                        <span class=\"ow_positive\">
                            <input type=\"submit\" name=\"saveb\" value=\"".OW::getLanguage()->text('map', 'change_your_position_onthemap')."\" class=\"ow_ic_gear_wheel ow_positive\">
                        </span>
                    </span>
</a>
                </div>
            </div>";
        }
//}


        if ($curent_mode=="edit"){
            $modex="border:2px solid #f00;";
        }else{
            $modex="";
        }

        $content .="<div id=\"map_canvas\" style=\"width: 100%;min-height: 550px;".$modex."\">";
        $content .="<div id=\"map\" style=\"width:100%;min-height: 550px;\"></div>";
        $content .="</div>";




        if ($for_user==$id_user OR ($id_user>0 AND ($found_self OR $found_friends))){
            return $content;
        }else if (!$id_user){
            return "<a href=\"".$curent_url."sign-in?back-url=index\"><h1>".OW::getLanguage()->text('map', 'for_see_map_you_mustloginfirst')."</h1><br/><img src=\"".$pluginStaticURL2."login_first.jpg\" style=\"widt:100%;margin:auto;\"></a>";
        }else{
            return "<h1>".OW::getLanguage()->text('map', 'member_not_set_self_locationyet')."</h1><br/><img src=\"".$pluginStaticURL2."login_first.jpg\" style=\"widt:100%;margin:auto;\">";
        }
    }

    public function get_full_category($checked_arr,$max_check_category=3)
    {
        $id_user = OW::getUser()->getId();//citent login user (uwner)
        $is_admin = OW::getUser()->isAdmin();
        $curent_url=OW_URL_HOME;
        $content ="";
        $add="";

        $checkedwas=0;
        $sel=" CHECKED ";
        $locat=0;
        $disabled="";
        
        $font_we="";

        if ($is_admin){
            $adda=" 1 ";
        }else{
            $adda=" active='1' ";
        }


        $queryc = "SELECT * FROM " . OW_DB_PREFIX. "map_category WHERE ".$adda." AND id2='0' ORDER BY name ";
        $arrc = OW::getDbo()->queryForList($queryc);
        foreach ( $arrc as $valuec ){
            $locat=$locat+1;

//            $content .="<div class=\"ow_box clearfix ow_left\" style=\"min-width:40px;min-height:20px;margin:0px;padding:0;\">";
            $content .="<div class=\"fil_check ow_box clearfix ow_left\" style=\"min-height:20px;margin:0px;padding:0px;margin-right:5px;\">";
/*
            if ($ctab!="all"){

                if (isset($_GET['searchmap']) AND $_GET['searchmap']){
                        $sel="";
                        $font_we="";
                        $disabled=" disabled=\"disabled\" ";
                }else if ($locat>1 AND !count($checked_arr)) {
                    $sel="";
                    $font_we="";
                }else if (in_array($valuec['id'],$checked_arr)){
                    if ($checkedwas<$max_check_category){
//echo "Sx";
                        $sel=" CHECKED ";
                        $checkedwas=$checkedwas+1;
                        $disabled="";
                        $font_we="font-weight:bold;";
                    }else{
                        $sel="";
                        $font_we="";
                        $disabled=" disabled=\"disabled\" ";
                    }
                }else{
                    $sel="";
                    $font_we="";
                    if (count($checked_arr)>=$max_check_category AND !in_array($valuec['id'],$checked_arr)){                        
                        $disabled=" disabled=\"disabled\" ";
                    }else{
                        $disabled="";
                    }
                }
//                if ($locat>3) {
//                    $disabled=" disabled=\"disabled\" ";
//                }
                
                $content .="<input ".$disabled."  name=\"check_cat[]\" type=\"checkbox\" ".$sel." swc=\"".$valuec['id']."\" value=\"".$valuec['id']."\">";
                $content .="&nbsp;<span id=\"t_".$valuec['id']."\" style=\"".$font_we."\">".stripslashes($valuec['name'])."</span>; ";
            }else{
                $content .="<input type=\"checkbox\" ".$sel." class=\"showhide_category\" swc=\"".$valuec['id']."\" value=\"".$valuec['id']."\">";
                $content .="&nbsp;<span id=\"t_".$valuec['id']."\">".stripslashes($valuec['name'])."</span>; ";
            }
*/

if (!$valuec['active']){
    $active="color:#f00;";
}else{
    $active="";
}

                $disabled=" disabled=\"disabled\" ";
$content .="<div style=\"
float: left;
display: inline-block;
width: 100%;
margin: auto;
backgroud-color:#eee;
\">";
                $content .="&nbsp;<span style=\"".$active."\" id=\"t_".$valuec['id']."\"><b>".stripslashes($valuec['name'])."</b></span>; ";
$content .="</div>";

//---------------------------------------------------22 start
        $queryc2 = "SELECT * FROM " . OW_DB_PREFIX. "map_category WHERE ".$adda." AND id2='".addslashes($valuec['id'])."' ORDER BY name ";
        $arrc2 = OW::getDbo()->queryForList($queryc2);
        foreach ( $arrc2 as $valuec2 ){
            $locat=$locat+1;

if (!$valuec2['active']){
    $active="color:#f00;";
}else{
    $active="";
}


//            $content .="<div class=\"ow_box clearfix ow_left\" style=\"min-width:40px;min-height:20px;margin:0px;padding:0;\">";
            $content .="<div class=\"fil_check ow_box clearfix ow_left\" style=\"min-height:20px;margin:0px;padding:0px;margin-right:5px;\">";

            if ($ctab!="all"){
                if (isset($_GET['searchmap']) AND $_GET['searchmap']){
                        $sel="";
                        $font_we="";
                        $disabled=" disabled=\"disabled\" ";
                }else if ($locat>1 AND !count($checked_arr)) {
                    $sel="";
                    $font_we="";
                }else if (in_array($valuec2['id'],$checked_arr)){
                    if ($checkedwas<$max_check_category){
//echo "Sx";
                        $sel=" CHECKED ";
                        $checkedwas=$checkedwas+1;
                        $disabled="";
                        $font_we="font-weight:bold;";
                    }else{
                        $sel="";
                        $font_we="";
                        $disabled=" disabled=\"disabled\" ";
                    }
                }else{
                    $sel="";
                    $font_we="";
                    if (count($checked_arr)>=$max_check_category AND !in_array($valuec2['id'],$checked_arr)){                        
                        $disabled=" disabled=\"disabled\" ";
                    }else{
                        $disabled="";
                    }
                }
//                if ($locat>3) {
//                    $disabled=" disabled=\"disabled\" ";
//                }

                if ($valuec2['active']){
                    $content .="<input ".$disabled."  name=\"check_cat[]\" type=\"checkbox\" ".$sel." swc=\"".$valuec2['id']."\" value=\"".$valuec2['id']."\">";
                }
                $content .="&nbsp;<span style=\"".$active."\" id=\"t_".$valuec2['id']."\" style=\"".$font_we."\">".stripslashes($valuec2['name'])."</span>; ";
            }else{
                if ($valuec2['active']){
                    $content .="<input type=\"checkbox\" ".$sel." class=\"showhide_category\" swc=\"".$valuec2['id']."\" value=\"".$valuec2['id']."\">";
                }
                $content .="&nbsp;<span style=\"".$active."\" id=\"t_".$valuec2['id']."\">".stripslashes($valuec2['name'])."</span>; ";
            }

            $content .="</div>";
        }//for2
//---------------------------------------------------22 end
            $content .="</div>";

        }//for
        return $content;
    }//function


}