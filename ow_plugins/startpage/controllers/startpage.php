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



class STARTPAGE_CTRL_Startpage extends OW_ActionController
{

    public function ajax_text() 
    {
        echo "Test ajax OK";
exit;
    }
    public function indexstrat($params) //startpage
    {
        $curent_url=OW_URL_HOME;		
        OW::getApplication()->redirect($curent_url);        
        exit;	
    }

    public function indexstratx($params) //startpagex
    {
//        $curent_url=OW_URL_HOME;		
//        OW::getApplication()->redirect($curent_url);        
        if (OW::getUser()->getId()>0 ){return;}
//echo "sgsDG";
        STARTPAGE_BOL_Service::getInstance()->check_user_activity();
        STARTPAGE_BOL_Service::getInstance()->check_user_activity_log();

        exit;	
//        return;
    }


    public function index_ajax_showpagef($params) //whow ajax page
    {	
        $retconf=array();
        $retconf['ss']=substr(session_id(),3,5);
/*
$retconf['status']="SUCCES";
$retconf['comm']="OK";
echo json_encode($retconf);
exit;
*/
        $fileElementName = 'fileToUpload';
//        if (!empty($_FILES[$fileElementName]['error']) AND !empty($_FILES[$files]['tmp_name']) AND $_FILES[$files]['tmp_name'] == 'none')
        if (isset($_FILES[$fileElementName]) AND $_FILES[$fileElementName]['error']=="0" AND $_FILES[$fileElementName]['tmp_name'] AND $_FILES[$fileElementName]['tmp_name'] != 'none')
        {
            $resultcreate=array();
//            $resultcreate=STARTPAGE_BOL_Service::getInstance()->upload_av($fileElementName);
//            image_copy_resize($file_source="",$file_dest="",$crop=false,$width=800,$height=600)
            $uploaddir = OW::getPluginManager()->getPlugin('startpage')->getUserFilesDir();
            $img_temp=session_id().".tmpav.jpg";
            if (STARTPAGE_BOL_Service::getInstance()->image_copy_resize($_FILES[$fileElementName]['tmp_name'],$uploaddir.$img_temp,false,150,150)){
//            if ($resultcreate['comm']=="OK"){
                $retconf['status']="SUCCES";
                $retconf['comm']="OK";
            }else{
//                $retconf=$resultcreate;
                $retconf['status']="ERROR";
                $retconf['comm']="ERROR...1002s";
            }
        }else{
                $retconf['status']="ERROR";
                $retconf['comm']=STARTPAGE_BOL_Service::getInstance()->corect_for_java(OW::getLanguage()->text('startpage', 'error_select_file')." 1001s");
        }

//        if (isset($_POST['ss']) AND $_POST['ss']==substr(session_id(),3,5)){
//                $retconf['status']="SUCCES";
//                $retconf['comm']="OK";

        echo json_encode($retconf);
        exit;
    }

    public function index_ajax_showpage($params) //whow ajax page
    {	
/*
//=======================
echo "START<br>";
                        $i=rand(88888,99999999);
                        $_POST['acctype']="asdasd".$i;
                        $_POST['uname']="asdasd".$i;
                        $_POST['email']="asdasdasdasd".$i."@asdas.com";
                        $_POST['pass']="afsdfwdfsdfsd";
                        $_POST['pass2']="afsdfwdfsdfsd";
                        $_POST['ss']=substr(session_id(),3,5);
echo $this->register();
echo "<br>END";
exit;
//======================
*/
        $retconf=array();
        $retconf['ss']=substr(session_id(),3,5);
//echo "sfsdF";exit;
//        echo "sfadfsdfs";
/*
'ss':'[tt_ss]', 'pass': pass , 'uname':uname, 'email':email,'acctype': actype}
*/
//$retconf['cc']=print_r($_POST,1);
//print_r($_POST);exit;


/*
        $retconf['status']="ERROR";
        $retconf['comm']="=============".$_POST['ss']."----".substr(session_id(),3,5);
        echo json_encode($retconf);
        exit;
*/



        if (isset($_POST['ss']) AND $_POST['ss']==substr(session_id(),3,5)){
/*
            $_SESSION['joinStep'] =2;
            $_SESSION['joinData']['accountType']=$_POST['acctype'];
            $_SESSION['joinData']['username']=$_POST['uname'];
            $_SESSION['joinData']['email']=$_POST['email'];
            $_SESSION['joinData']['password']=$_POST['pass'];
*/
            $resultcreate=0;
            $_SESSION['userId']=$resultcreate;
            OW::getSession()->set('userId', $resultcreate);
///            $resultcreate=$this->register();
            $resultcreate=STARTPAGE_BOL_Service::getInstance()->register();


            if ($resultcreate>0){
                $retconf['status']="SUCCES";
                $retconf['comm']="OK";
                $_SESSION['userId']=$resultcreate;
                OW::getSession()->set('userId', $resultcreate);
            }else{
                if ($resultcreate==-100){
                    $retconf['status']="ERROR";
                    $retconf['comm']=STARTPAGE_BOL_Service::getInstance()->corect_for_java(OW::getLanguage()->text('startpage', 'error_email_already_exist'));
                }else if ($resultcreate==-200){
                    $retconf['status']="ERROR";
                    $retconf['comm']=STARTPAGE_BOL_Service::getInstance()->corect_for_java(OW::getLanguage()->text('startpage', 'error_login_already_exist'));
                }else{
                    $retconf['status']="ERROR";
                    $retconf['comm']=STARTPAGE_BOL_Service::getInstance()->corect_for_java(OW::getLanguage()->text('startpage', 'error_create_account_tryagain')." [102]");
                }
            }
        }else{//errror
            $retconf['status']="ERROR";
            $retconf['comm']=STARTPAGE_BOL_Service::getInstance()->corect_for_java(OW::getLanguage()->text('startpage', 'error_create_account_tryagain')." [101]");
        }
//                $retconf['status']="SUCCES";
//                $retconf['comm']="OK";
//$retconf['cc']=print_r($_POST,1);
        echo json_encode($retconf);
        exit;
    }

	
    public function index_ajax_showpageex($params) //whow ajax page
    {
        $content_db="";
//echo "ssssssssssssssss".print_r($_GET,1)."---".print_r($_POST,1)."---".print_r($params,1);exit;
//        $this->assign('pageurl', "http://www.otozakupy.pl/");

//        $testzmienna="aaaaaaa";
 
//        $this->setPageTitle("Contact Us"); 
//        $this->setPageHeading("Contact Us"); 
///        $this->setPageTitle(OW::getLanguage()->text('startpage', 'index_page_title')); //title menu
//        $this->setPageHeading(OW::getLanguage()->text('startpage', 'index_page_heading')); //title page

//echo "<hr>ddd";
/*
            if ($params['id_user']>0 AND $params['id_page']>0){
                $rateDao = BOL_RateDao::getInstance();    	
                $query = "SELECT * FROM " . OW_DB_PREFIX. "startpage  
                        WHERE id_owner  = '".addslashes($params['id_user'])."' AND id= '".addslashes($params['id_page'])."' ";
                $arr = OW::getDbo()->queryForList($query);
                $value=$arr[0];
            }else{
                $value=array();
            }

            if (!$value['id']){
                $content_db="Page not found or was moving...";
            }else{
//echo "ddd00";
                $content_db= stripslashes($value['content']);
                $content_db =str_replace("\r\n","<br/>",$content_db);
                $content_db =str_replace("\n","<br/>",$content_db);
            }
*/

//------------------next start
$perpage=20;
if (isset($_POST['pg']) AND $_POST['pg']>0){
    $pgs=(int)$_POST['pg'];
}else{
    $pgs=0;
}
$start=$pgs*$perpage;
if (!$start) $start=0;

if (isset($_POST['ct'])){
    $ctab=$_POST['ct'];
}else{
    $ctab="newsfeed";
}

//$start=0;

                    if ($ctab=="shop"){
                        $box_array=WALL_BOL_Service::getInstance()->make_box_shop("array",$start,$perpage);
                    }else if ($ctab=="photo"){
                        $box_array=WALL_BOL_Service::getInstance()->make_box_photo("array",$start,$perpage);
                    }else{
                        $sql = "SELECT 
                                acscet.userId as muserId, 
                                acscet.activityType, acscet.activityId, acscet.userId, acscet.actionId, acscet.timeStamp, acscet.privacy, acscet.visibility, acscet.status, 
                                ascet.*, cset.*  
                        FROM " . OW_DB_PREFIX. "newsfeed_activity acscet 
                    LEFT JOIN " . OW_DB_PREFIX. "newsfeed_action ascet ON (ascet.id=acscet.actionId) 
                    LEFT JOIN " . OW_DB_PREFIX. "newsfeed_action_set cset ON (cset.actionId=acscet.actionId) 
                        WHERE (acscet.activityType='create' OR acscet.activityType='comment') AND acscet.status='active' 
                        GROUP BY acscet.id 
                         ORDER BY acscet.timeStamp DESC 
                        LIMIT ".$start.",".$perpage;
//                        WHERE acscet.activityType='create' AND acscet.status='active' 
//echo $sql;exit;
//$xx=array();
//$xx[]=$sql;
//echo json_encode($xx);
//exit;
                        $arr = OW::getDbo()->queryForList($sql);
                        $box_array=array();
                        $box_array=WALL_BOL_Service::getInstance()->make_box($arr,"array");
                    }
//echo "ssss";exit;




//------------------nest end
//            $content_db .="dsfsdfsdf";

//            echo $content_db;
            echo json_encode($box_array);
/*
            $xx=array();
            $xx[]=$params['id_page'];
            echo json_encode($xx);
*/
//            $xx[]=$sql;
//            echo json_encode($xx);


exit;
    } 





	
	
	
	
    public function index($params)
    {
$startpage="";
        $curent_url=OW_URL_HOME;		
//        $id_user=$params['id_user'];
//        $id_pager=$params['id_page'];
        $this->setPageTitle(OW::getLanguage()->text('startpage', 'index_page_title')); //title menu
        $this->setPageHeading(OW::getLanguage()->text('startpage', 'index_page_heading')); //title page

$id_user = OW::getUser()->getId();
$is_admin = OW::getUser()->isAdmin();
$pluginStaticURL2=OW::getPluginManager()->getPlugin('startpage')->getStaticUrl();
$pluginStaticURL =OW::getPluginManager()->getPlugin('startpage')->getUserFilesUrl();
$pluginStaticDir =OW::getPluginManager()->getPlugin('startpage')->getUserFilesDir();


    if ($id_user>0 AND isset($params['newstatus']) AND isset($_POST['ss']) AND $_POST['ss']==substr(session_id(),2,3) AND isset($_POST['fstatus']) AND $_POST['fstatus']){
        if (isset($_POST['fstatus'])){
            $sql="INSERT INTO " . OW_DB_PREFIX. "base_user_status (
                id,  userId, status
            )VALUES(
                    '','".addslashes($id_user)."','".addslashes($_POST['fstatus'])."'
            ) ON DUPLICATE KEY UPDATE status='".addslashes($_POST['fstatus'])."' ";
//        $last_insert_id = OW::getDbo()->insert($sql);
            OW::getDbo()->insert($sql);
//            if (!$last_insert_id){
        }


        if (isset($_POST['burl']) AND $_POST['burl']){
            $burl=base64_decode($_POST['burl']);
            if (!$burl) $burl="";
        }else{
            $burl="";
        }

        OW::getFeedback()->info(OW::getLanguage()->text('gallery', 'status_was_changed'));
        if ($burl){
            OW::getApplication()->redirect($burl);
        }else{
            OW::getApplication()->redirect($curent_url);
        }
        exit;
    }


//$theme="darkneon";
//$theme="default";
$theme=OW::getConfig()->getValue('startpage', 'curent_theme');
if (!$theme) $theme="default";



//$pluginStaticU=OW::getPluginManager()->getPlugin('startpage')->getStaticUrl();

//    $img_bckground=$pluginStaticURL2."themes".DS.$default_theme.DS."css.css";
//    OW::getDocument()->addStyleSheet($pluginStaticURL2."themes".DS.$default_theme.DS."css.css");

/*
     OW::getDocument()->addScript($pluginStaticURL2.'markitup/jquery.markitup.js');
     OW::getDocument()->addScript($pluginStaticURL2.'markitup/sets/default/set.js');

    OW::getDocument()->addStyleSheet($pluginStaticURL2.'images/style.css');
    OW::getDocument()->addStyleSheet($pluginStaticURL2.'markitup/skins/markitup/style.css');
    OW::getDocument()->addStyleSheet($pluginStaticURL2.'markitup/sets/default/style.css');
*/

    OW::getDocument()->addScript($pluginStaticURL2.'script/jquery-ui-1.7.2.custom.min.js');
    OW::getDocument()->addScript($pluginStaticURL2.'script/jquery.startpagenotes.js');

    OW::getDocument()->addStyleSheet($pluginStaticURL2.'css/jquery-ui-1.7.2.custom.css');
    OW::getDocument()->addStyleSheet($pluginStaticURL2.'css/jquery.startpagenotes.css');

/*
$default_theme=OW::getConfig()->getValue('startpage', 'curent_theme');
if (!$default_theme) $default_theme="default";

$pluginStaticU=OW::getPluginManager()->getPlugin('startpage')->getStaticUrl();
$pluginStaticD=OW::getPluginManager()->getPlugin('startpage')->getStaticDir();


    OW::getDocument()->addScript(OW_URL_HOME.'ow_static/themes/'.$default_theme.'/js.js');
    OW::getDocument()->addStyleSheet(OW_URL_HOME.'ow_static/themes/'.$default_theme.'/css.css');
*/
//<div id=\"notes\" style=\"width:800px;height:500px;\">
//echo "fdsfSD";exit;
$startpage .="
<div id=\"notes\" style=\"min-width:800px;min-height:500px;\">
</div>
";


/*
			var edited = function(note) {
				alert('Edited note with id ' + note.id + ', new text is: ' + note.text);
			}
			var created = function(note) {
				alert('Created note with id ' + note.id + ', text is: ' + note.text);
			}
			
			var deleted = function(note) {
				alert('Deleted note with id ' + note.id + ', text is: ' + note.text);
			}
			
			var moved = function(note) {
				alert('Moved note with id ' + note.id + ', text is: ' + note.text);
			}	
			
			var resized = function(note) {
				alert('Resized note with id ' + note.id + ', text is: ' + note.text);
			}					
*/		

$script ="

			var edited = function(note) {
			}
			var created = function(note) {
			}
			
			var deleted = function(note) {
			}
			
			var moved = function(note) {
			}	
			
			var resized = function(note) {
			}					

			jQuery(document).ready(function() {
				var options = {
					notes:[{'id':1,
					      'text':'Test Internet Explorer',
						  'pos_x': 50,
						  'pos_y': 50,	
						  'width': 200,							
						  'height': 200,													
					    }]
					,resizable: true
					,controls: true 
					,editCallback: edited
					,createCallback: created
					,deleteCallback: deleted
					,moveCallback: moved					
					,resizeCallback: resized					
					
				};
				jQuery('#notes').startpageNotes(options);

			});

";
//background: url('http://mycollegesocial.com/ow_static/plugins/startpage/themes/2column_wlogin/bg1.jpg') repeat scroll 0% 0% transparent;
//$startpage .="<script>".$script."</script>";

OW::getDocument()->addOnloadScript($script);

$this->assign('content', $startpage);

    }


}