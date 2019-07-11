<?
  session_start();
  include 'db-connect.php';


  $select_result = $mysqli->query("SELECT review_id, DATE_FORMAT(review_creation_date, '%M %D, %Y %l:%i%p') AS review_creation_date, music_name, music_review, music_rating, music_image_url
                                   FROM a6_reviews
                                   WHERE review_id = '".$_GET['review_id']."'");

  $select_comments = $mysqli->query("SELECT r.review_id, c.comment_id, c.comment, c.comment_creation_date, c.user_id, u.user_id, u.first_name, u.last_name, DATE_FORMAT(c.comment_creation_date, '%M %D, %Y %l:%i%p') AS comment_creation_date
                                 FROM a6_reviews r, a6_comments c, a6_users u
                                 WHERE r.review_id = '".$_GET['review_id']."' AND r.review_id = c.review_id AND c.user_id = u.user_id");

  if (isset($_POST['submit']) && isset($_POST['comment'])) {
    $insert = $mysqli->prepare("INSERT INTO a6_comments(user_id, review_id, comment)
                                VALUES (?, ?, ?)");
    $insert->bind_param("iis", $_SESSION['logged_in_user_id'], $_GET['review_id'], $_POST['comment']);
    $insert->execute();
    $review_id = $_GET['review_id'];
    header("Location: review.php?review_id=$review_id");
  };
  if ($mysqli->error) {
    print $mysqli->error;
  }
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Review | Assignment 6</title>
    <link rel="stylesheet" href="css/styles.css"/>
  </head>
  <header>
    <nav>
      <ul>
        <li><a href="admin.php">Dashboard</a></li>
        <li><a href="reviews.php">Reviews</a></li>
        <li id="logout"><a href="logout.php">Logout</a></li>
      </ul>
    </nav>
  </header>
    <body>
      <main class="review">
        <h1 class="page-h1"><? while($row = $select_result->fetch_object()) {
          print $row->music_name;
         ?> </h1>
         <?
          print "<span class=\"review-date\"><em>Posted ".$row->review_creation_date."</em></span>";
          ?>
          <br><br>
          <div class = "review">
            <?
              print "<img src =\"".$row->music_image_url."\" width=\"80px\" >";
              print "<br><br><br>Rating:";
              print "<h2>".$row->music_rating."</h2><br>";
              print "<p>".$row->music_review."</p>";
              }
            ?>

          </div>
          <div class = "comments">
            <h2 class="page-h1">Comments</h2>
              <? while($row = $select_comments->fetch_object()){
                print "<span class=\"comment-author\">".$row->first_name." ".$row->last_name."</span><br>";
                print "<span class=\"comment-date\"><em>".$row->comment_creation_date."</em></span>";
                print "<p>".$row->comment."</p>";
              } ?>
          </div>
          <?
            if (isset($_SESSION['logged_in'])) {
          ?>
              <form class="comment" action="" method="post" id="comment-form">
                <label for="comment">Post a Comment:</label><br>
                <textarea name="comment" id="comment" rows="8" cols="50" form="comment-form"></textarea>
                <br><br>
                <input type="hidden" name="review_id" value="<? print $_GET['review_id']; ?>">
                <input type="submit" name="submit" value="Submit">
              </form>
          <?
            } else {
          ?>
              <h2 id="login-comment"><a href="login.php">Login</a> to post a comment!</h2>
          <?
            }
          ?>
      </main>
    </body>
</html>

<?
  $mysqli->close(); ?>
