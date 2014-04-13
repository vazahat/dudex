<?
if ($_COOKIE["urlRoomVr"]) 
{
  $urlRooms = $_COOKIE["urlRoomVr"];
  $params = explode("^", $urlRooms);
  $urlRoom = $params[0];
  $urlRoomx = $params[1];
}

  if ($_GET["result"] == "Saved") {
    setcookie("video_recorded", $_GET["stream"],time()+86400,'/');
    header('Location: '.$urlRoom);
  } else header('Location: '.$urlRoomx);
  
?>
