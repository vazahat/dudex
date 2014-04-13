<?php
if (!$_POST["username"]||$_POST["username"]=="Studio") $username="Studio".rand(100,999);
else $username=$_POST["username"];
$username=preg_replace("/[^0-9a-zA-Z]/","-",$username);
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<style type="text/css">
<!--
body {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 15px;
	color: #EEE;
}

a
{
	color: #CC5577;
	font-weight: normal;
	text-decoration: none;
}
-->
</style>
<title>VideoWhisper Live Broadcast</title>
</head>
<body bgcolor="#333333">
<?php
$swfurl="live_broadcast.swf?room=" . $username;
$bgcolor="#333333";
?>
<object width="100%" height="500">
<param name="movie" value="<?=$swfurl?>"></param><param bgcolor="<?=$bgcolor?>"><param name="scale" value="noscale" /> </param><param name="salign" value="lt"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed width="100%" height="500" scale="noscale" salign="lt" src="<?=$swfurl?>" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" bgcolor="<?=$bgcolor?>"></embed>
</object>
      <p>This html content is editable. The flash workspace above can have any size. Various settings can be configured from vc_login.php .
	  <BR>Test <a target="_blank" href="channel.php?n=<?=urlencode($username)?>">channel page</a> (where people can also chat live), <a target="_blank" href="video.php?n=<?=urlencode($username)?>">plain video</a>, just <a target="_blank" href="htmlchat.php?n=<?=urlencode($username)?>">plain html external text only chat</a> (for old mobile access), <a target="_blank" href="videotext.php?n=<?=urlencode($username)?>">plain video with floating html text</a> (read only).</p>
	  <P>Ordering a license removes banner ads and usage limitations (for licensed domain).</P>
	  <p>For more details about this edition see VideoWhisper <a target="_blank" href="http://www.videowhisper.com/?p=PHP+Live+Streaming">PHP Live Streaming</a> page.</p></td>
</body>
</html>
