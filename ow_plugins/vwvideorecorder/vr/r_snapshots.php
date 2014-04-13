<?php
if (strstr($_GET['name'],"/")) exit;
if (strstr($_GET['name'],".")) exit;

if (isset($GLOBALS["HTTP_RAW_POST_DATA"]))
{

  $stream=$_GET['name'];

	// get bytearray
	$jpg = $GLOBALS["HTTP_RAW_POST_DATA"];

	// save file
  $fp=fopen("snapshots/$stream.jpg","w");
  if ($fp)
  {
    fwrite($fp,$jpg);
    fclose($fp);
  }
}
?>loadstatus=1
