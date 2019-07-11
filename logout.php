<?
  session_start();

  if(isset($_SESSION)) {
    $_SESSION = array();
    session_destroy();
  }

  header("Location: login.php");
  ?>
