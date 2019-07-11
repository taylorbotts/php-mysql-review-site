<?
  session_start();
  include 'db-connect.php';

  if(isset($_POST['submit']) && (!isset($_SESSION['logged_in']))) {
    $select_login = $mysqli->prepare("SELECT * FROM a6_users");
    $select_login->execute();
    $select_login->bind_result($user_id, $username, $pass, $first_name, $last_name, $access_level);
    if($mysqli->error){
      print "MySQL Error: ".$mysqli->error;
    }

    while($select_login->fetch()) {
      if ((($_POST['user']) == ($username)) && (md5($_POST['pass']) == $pass)) {
        $_SESSION['logged_in'] = true;
        $_SESSION['logged_in_user'] = $username;
        $_SESSION['logged_in_name'] = $first_name;
        $_SESSION['logged_in_user_id'] = $user_id;
        $_SESSION['logged_in_user_access'] = $access_level;
      }
    }
    $select_login->close();
  }
  if(isset($_SESSION['logged_in'])){
    header("Location: admin.php");
  }

?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | Assignment 5</title>
    <link rel="stylesheet" href="css/styles.css"/>
  </head>
  <body>
    <main class="login">
      <h1>Welcome!</h1>
      <h3>Please sign in to continue</h3>
      <form class="login" action="login.php" method="post">
        <label for="user">Username:</label><br>
        <input type="text" name="user" id="user" value=""/><br>
        <label for="pass">Password:</label><br>
        <input type="password" name="pass" id="pass" value=""/><br>
        <?
          if (isset(($_POST['submit'])) && (!isset($_SESSION['logged_in']))){
            print "<span class='error'>Error: Wrong username and password combo!</span><br><br>";
          }
        ?>
        <input type="submit" name="submit" value="SIGN IN" id="submit"/>
      </form>
  </main>
  </body>
</html>

<? $mysqli->close(); ?>
