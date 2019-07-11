<?
  session_start();
  include "db-connect.php";

  if (isset($_SESSION['logged_in'])){
    $select_result = $mysqli->query("SELECT review_id, DATE_FORMAT(review_creation_date, '%M %D, %Y %l:%i%p') AS review_creation_date, music_name, music_review, music_rating, music_image_url
                                     FROM a6_reviews
                                     WHERE review_id = '".$_GET['review_id']."'");
    if (isset($_POST['submit'])){
      $mysqli->query("DELETE FROM a6_reviews
                      WHERE review_id = '".$_POST['delete_id']."'");
      header('Location: admin.php');
    }
  } else header('Location: login.php');

  ?>
  <!DOCTYPE html>
  <html lang="en" dir="ltr">
    <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>Delete | Assignment 6</title>
      <link rel="stylesheet" href="css/styles.css">
    </head>
    <body class="body">
      <main class="delete">
        <?
          if ($_SESSION['logged_in_user_access'] == "admin"){ ?>

        <header>
          <h1>Delete</h1>
        </header>
        <table>
          <thead>
            <tr>
              <th>Title</th>
              <th id="review">Review</th>
              <th>Rating</th>
              <th>Cover</th>
              <th>Created</th>
            </tr>
          </thead>
          <tbody>
            <?
              while($row = $select_result->fetch_object()) {
                print "<tr>";
                print "<td>".$row->music_name."</td>";
                print "<td>".$row->music_review."</td>";
                print "<td>".$row->music_rating."</td>";
                print "<td><img src =\"".$row->music_image_url."\" width=\"80px\" ></td>";
                print "<td>".$row->review_creation_date."</td>";
                print "</tr>";
              }
              ?>
          </tbody>
        </table>
        <form class="" action="" method="post">
          <br>
          <p>Are you sure you want to delete this data?</p>
          <input type="hidden" name="delete_id" id="delete_id" value="<? print $_GET['review_id']; ?>" />
          <input type="submit" name="submit" value="Delete">
          <a href="admin.php">Cancel</a>
        </form>
      <? } else {
        header('Location: admin.php');
        }
      ?>
      </main>
    </body>
  </html>

  <? $mysqli->close(); ?>
