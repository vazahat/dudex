<?
if ($_COOKIE["urlLogoutLs"]) $urlLogout=$_COOKIE["urlLogoutLs"];

  header('Location: '.$urlLogout);
  
?>
