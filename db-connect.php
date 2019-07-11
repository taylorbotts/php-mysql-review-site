<?
    if (($_SERVER['SERVER_ADDR'] == '::1') || ($_SERVER['SERVER_ADDR'] == '127.0.0.1')){
        $mysqli = new mysqli('localhost', 'root', '', 'dig3134'); }
    else {
        $mysqli = new mysqli('students.cah.ucf.edu','ta741447','dY%@A&%evku9ktT53','ta741447');
  }
    if($mysqli->error) {
        print "MySQL Error: ".$mysqli->error;
    }
?>
