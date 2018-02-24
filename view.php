<?php
// 'view.php'
require_once "pdo.php";
require_once "util.php";
//require_once 'detectMobile.php';
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Marcel Merchat's Resume Registry</title>
    <?php
      //require_once 'head.php';
      //function isMobile() {
      //    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
      //}
      if(isMobile()==1) {
          require_once 'mobile.php';
      } else {
          require_once 'desktop.php';
      }
    ?>
</head>
<body>
<div id="main">
<h1>Profile Information</h1>
<br>
<?php
    $profileid = $_GET['profile_id'];
    $stmt = $pdo->prepare('SELECT first_name, last_name,
                  email, headline, summary FROM Profile WHERE profile_id = :pid');
    $stmt->execute(array(':pid' => $_GET['profile_id']));
    $row =  $stmt->fetch(PDO::FETCH_ASSOC);
        echo '<p class="big">First Name: '.htmlentities($row['first_name']).'</p>';
        echo '<p class="big">Last Name: '.htmlentities($row['last_name']).'</p>';
        echo '<p class="big">Email: '.htmlentities($row['email']).'</p>'.'</p>';
        echo '<p class="big">Headline: '.htmlentities($row['headline']).'</p>';
        echo '<p class="big">Summary: '.htmlentities($row['summary']).'</p>';

        echo '<h2 class="margin-bottom-small"> Education: </h2>';
        $sqlid = 'SELECT institution_id, year FROM Education WHERE profile_id = :pid';
        $sqlinst = 'SELECT name FROM Institution WHERE institution_id = :iid';
        $stmt_schoolid = $pdo->prepare($sqlid);
        $stmt_schoolid->execute(array(':pid' => $_GET['profile_id']));
        $school_ids = $stmt_schoolid->fetchALL(PDO::FETCH_ASSOC);
        $rows = $school_ids;
        $length = count($rows);
        if( $length !== 0) {
echo '<ul>';
          for ($i = 0; $i < $length; $i++){
                $institution_id = $rows[$i]['institution_id'];
                $stmt_schoolname = $pdo->prepare($sqlinst);
                $stmt_schoolname->execute(array(':iid' =>   $institution_id));
                $school_name = $stmt_schoolname->fetch(PDO::FETCH_ASSOC);
                $school = $school_name['name'];
                echo '<li>'.htmlentities($rows[$i]['year']).': '.$school.'</li>';
          }
echo '</ul>';
        } else {
              echo '<p style="color:orange">No education found</p>';
        }
  echo '<h2  class="margin-bottom-small"> Positions: </h2>';
  $sql = 'SELECT year, description FROM Position WHERE profile_id = :pid';
  $stmt_positions = $pdo->prepare($sql);
  $stmt_positions->execute(array(':pid' => $_GET['profile_id']));
  $row = $stmt_positions->fetchALL(PDO::FETCH_ASSOC);
  $rows = array_values($row);

  if( $row !== false) {
      echo '<ul>';
      while ( $row = $stmt_positions->fetchALL(PDO::FETCH_ASSOC) ) {
          echo '<li>'.htmlentities($rows['year']).': '.htmlentities($rows['description']).'</li>';
      }
      foreach($rows as $job){
              echo '<li>'.htmlentities($job['year']).': '.htmlentities($job['description']).'</li>';
      }
      echo '</ul>';
  } else {
             echo '<p style="color:orange">No positions found</p>';
  }
?>
<h3 class="big">
    <a href="index.php">Return to Main Page</a>
</h3>
</div>
</body>
