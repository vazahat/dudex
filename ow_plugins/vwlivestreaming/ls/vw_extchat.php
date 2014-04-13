<?php

$updated = $_POST['t'];
//$room = $_POST['r'];
$room = $_POST['room_name'];

//do not allow reading from other folders
if ( strstr($room,"/") || strstr($room,"..") ) exit;

if ($room!="null")
{
$dir = "uploads";
if (!file_exists($dir)) @mkdir($dir);
@chmod($dir, 0755);
$dir .= "/".$room;
if (!file_exists($dir)) @mkdir($dir);
@chmod($dir, 0755);
$dir .= "/external";
if (!file_exists($dir)) @mkdir($dir);
@chmod($dir, 0755);

$day=date("y-M-j",time());
$fname="uploads/$room/external/$day.html";


$chatText="";

if (file_exists($fname)) 
{
$chatData = implode('', file($fname));

$chatLines=explode(";;\r\n",$chatData);

foreach ($chatLines as $line)
	{
		$items = explode("\",\"", $line);
		if (trim($items[0], " \"") > $updated) $chatText .= trim($items[1], " \"");
	}

}
$ztime = time();
}
?>chatText=<?=urlencode($chatText)?>&updateTime=<?=$ztime?>
