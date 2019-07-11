<?
    if (($_SERVER['SERVER_ADDR'] == '::1') || ($_SERVER['SERVER_ADDR'] == '127.0.0.1')){
        $mysqli = new mysqli('localhost', 'root', '', 'dig3134'); }
    else {
        // Connect to UCF database
  }
    if($mysqli->error) {
        print "MySQL Error: ".$mysqli->error;
    }
?>
