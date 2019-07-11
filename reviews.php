<?
  session_start();
  include 'db-connect.php';

  $select_result = $mysqli->query("SELECT music_name, review_id
                                   FROM a6_reviews
                                   ORDER BY music_name");
  if ($mysqli->error) {
    print $mysqli->error;
  }
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reviews | Assignment 6</title>
    <link rel="stylesheet" href="css/styles.css"/>
  </head>
    <body>
      <header>
        <nav>
          <ul>
            <li><a href="admin.php">Dashboard</a></li>
            <li><a href="reviews.php">Reviews</a></li>
            <li id="logout"><a href="logout.php">Logout</a></li>
          </ul>
        </nav>
      </header>
      <main class="reviews">
        <h1 class="page-h1">Reviews (a-z)</h1>
          <?
            while($row = $select_result->fetch_object()) {
              print "<p>- <a href=\"review.php?review_id=".$row->review_id."\">".$row->music_name."</a></p>";
            }
          ?>
      </main>
    </body>
</html>

<?
  $mysqli->close(); ?>
