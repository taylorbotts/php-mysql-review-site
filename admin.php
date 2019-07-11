<?
  session_start();
  include 'db-connect.php';

  $missing_info = 0;
  if (empty($_POST['music_name'])){
    $missing_info = 1; }
  if (empty($_POST['music_review'])){
    $missing_info = 1; }
  if (isset($_POST['submit']) && (strlen($_POST['music_rating']) == 0)){
    $missing_info = 1; }
  if (empty($_POST['music_image_url'])){
    $missing_info = 1; }

  class User {
    public $name;

    public function __construct($n){
      $this->name = $n;
    }

    public function getName() {
      return $this->name;
    }
  }

  if(isset($_SESSION['logged_in'])) {

    $user = new User($_SESSION['logged_in_name']);

    if ($_SESSION['logged_in_user_access'] == "admin") {
      $select_result = $mysqli->query("SELECT r.review_id, DATE_FORMAT(r.review_creation_date, '%M %D, %Y %l:%i%p') AS review_creation_date, r.music_name, r.music_review, r.music_rating, r.music_image_url, r.user_id, u.user_id, u.username
                                       FROM a6_reviews r, a6_users u
                                       WHERE r.user_id = u.user_id");
    }
    elseif ($_SESSION['logged_in_user_access'] == "review"){
      $select_result = $mysqli->query("SELECT r.review_id, DATE_FORMAT(r.review_creation_date, '%M %D, %Y') AS review_creation_date, r.music_name, r.music_review, r.music_rating, r.music_image_url, r.user_id, u.user_id, u.username
                                       FROM a6_reviews r, a6_users u
                                       WHERE r.user_id = u.user_id AND u.user_id = '".$_SESSION['logged_in_user_id']."'");
    }
    if ((isset($_POST['submit'])) && ($missing_info == 0)){
      $insert = $mysqli->prepare("INSERT INTO a6_reviews (music_name, music_review, music_rating, music_image_url, user_id)
                                  VALUES (?, ?, ?, ?, ?)");

      $insert->bind_param("ssiss", $_POST['music_name'], $_POST['music_review'], $_POST['music_rating'], $_POST['music_image_url'], $_SESSION['logged_in_user_id']);
      $insert->execute();
      if ($mysqli->error) print $mysqli->error;

      $select_new = $mysqli->query("SELECT review_id, music_name
                                    FROM a6_reviews
                                    WHERE music_name = '".$_POST['music_name']."'");

      while ($row = $select_new->fetch_object()){
        $newID = $row->review_id;
      }
      if ($mysqli->error){
        print $mysqli->error;
      }

      /* This works local but doesn't work on the server

      $rss = "reviews.xml";
      $xml = simplexml_load_file($rss);

      $newItem = $xml->channel->addChild("item");
      $newItem->addChild("title", $_POST['music_name']);
      $newItem->addChild("link", "http://localhost/dig3134/assignment06/review.php?review_id=$newID");
      $newItem->addChild("description", $_POST['music_review']);

      $xml->asXML('reviews.xml');*/

      $rss_file = file_get_contents("reviews.xml");
      $exploded_rss_file = explode("</channel>", $rss_file);
      $rss_start = $exploded_rss_file[0];
      $rss_end = "  </channel>".$exploded_rss_file[1];


      $submitted_title = $_POST['music_name'];
      $submitted_description = $_POST['music_review'];
      $submitted_link = "http://students.cah.ucf.edu/~ta741447/dig3134/assignment06/review.php?review_id=$newID";
      $new_rss_item = "\n<item>\n <title>".$submitted_title."</title>\n <link>".$submitted_link."</link>\n <description>".$submitted_description."</description>\n </item>\n";
      $updated_rss = $rss_start.$new_rss_item.$rss_end;
      file_put_contents("reviews.xml", $updated_rss);

      header("Location: admin.php");
    }
  } else header("Location: login.php");
  if ($mysqli->error){
    print "MySQL Error: ".$mysqli->error;
  }

?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard | Assignment 6</title>
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
      <main>
        <h1 class="page-h1"><? if($_SESSION['logged_in_user_access'] == "admin")print "Admin"; ?>
          Dashboard
        </h1>
        <p class="welcome">Welcome back, <? print $user->getName(); ?>!</p>
          <table>
            <thead>
              <tr>
                <th>Title</th>
                <th id="review">Review</th>
                <th>Rating</th>
                <th>Cover</th>
                <th>Created</th>
                <th>Comments</th>
                <? if ($_SESSION['logged_in_user_access'] == "admin"){ ?>
                  <th>Delete</th>
                <? } ?>
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
                  print "<td><a href=\"review.php?review_id=".$row->review_id."\">View Comments</a></td>";
                  if ($_SESSION['logged_in_user_access'] == "admin"){
                    print "<td><a href=\"delete.php?review_id=".$row->review_id."\">Delete</a></td>";
                  }
                  print "</tr>";
                }
                ?>
            </tbody>
          </table>
          <? if($_SESSION['logged_in_user_access'] == "review") { ?>
            <form class="admin" action="" method="post" id="user-form">
              <h2>Create a New Review:</h2>
              <? if((isset($_POST['submit'])) && ($missing_info == 1)){
                print "<span class='error'>Error: Form submitted incorrectly!</span><br><br>";
              } ?>
              <label for="music_name">Title:</label><br>
              <input type="text" name="music_name" id="music_name" value=""/><br>

              <label for="music_review">Review:</label><br>
              <textarea name="music_review" id="music_review" rows="8" cols="80" form="user-form"></textarea><br>

              <label for="music_rating">Rating (0-10):</label><br>
              <input type="number" name="music_rating" id="music_rating" min="0" max="10"><br>

              <label for="music_image_url">Cover Image URL:</label><br>
              <input type="text" name="music_image_url" id="music_image_url" value=""/><br>

              <input type="submit" name="submit" value="Submit"/>
            </form>
          <? } ?>
      </main>
    </body>
</html>

<?
  $mysqli->close(); ?>
