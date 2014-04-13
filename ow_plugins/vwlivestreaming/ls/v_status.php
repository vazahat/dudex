<?
/*
POST Variables:
u=Username
s=Session, usually same as username
r=Room
ct=session time (in milliseconds)
lt=last session time received from this script in (milliseconds)
*/

$room=$_POST[r];
$session=$_POST[s];
$username=$_POST[u];
$message=$_POST[m];

$currentTime=$_POST[ct];
$lastTime=$_POST[lt];

$maximumSessionTime=0; //900000ms=15 minutes; 0 for unlimited

// videowhisper
$time = time();
$xtime=$time-30;

$file = "ls_status.txt";
if(date(filemtime($file))>$xtime){
  $fh = fopen($file, 'a') or die("can't open file");
  $data = $time.":".$room.":".$username."|";
  fwrite($fh, $data);
  fclose($fh);
} else {
  $fh = fopen($file, 'w') or die("can't open file");
  $data = $time.":".$room.":".$username."|";
  fwrite($fh, $data);
  fclose($fh);
}

$disconnect=""; //anything else than "" will disconnect with that message
?>timeTotal=<?=$maximumSessionTime?>&timeUsed=<?=$currentTime?>&lastTime=<?=$currentTime?>&disconnect=<?=$disconnect?>&loadstatus=1
