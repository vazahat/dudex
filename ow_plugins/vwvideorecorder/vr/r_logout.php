<?

if ($_COOKIE["urlRoomVr"]) 
{
  $urlRooms = $_COOKIE["urlRoomVr"];
  $params = explode("^", $urlRooms);
  $urlRoomx = $params[1];
}
  header('Location: '.$urlRoomx);
  
?>
