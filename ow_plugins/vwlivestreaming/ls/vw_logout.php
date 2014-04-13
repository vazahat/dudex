<?
if ($_COOKIE["urlLogout"]) $urlLogout=$_COOKIE["urlLogout"];

  header('Location: '.$urlLogout);
  
?>
