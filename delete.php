<?php
require_once "pdo.php";
require_once "util.php";
session_start();

if ( ! isset($_SESSION['name']))  {
      die('ACCESS DENIED');
}

if(isMobile()==1) {
   require_once 'mobile.php';
} else {
   //require_once 'desktop.php';
   echo '<link rel="stylesheet" type="text/css" href="styleDesktop.css">';
}

if ( isset($_POST['delete']) && isset($_POST['profile_id']) ) {
    $sql = "DELETE FROM Profile WHERE profile_id = :zip";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':zip' => $_POST['profile_id']));
    $_SESSION['success'] = 'Record deleted';
    header( 'Location: index.php' ) ;
    return;
}

if ( ! isset($_GET['profile_id']) ) {
  $_SESSION['error'] = "Missing profile_id";
  header('Location: index.php');
  return;
}

$stmt = $pdo->prepare("SELECT profile_id, first_name, last_name FROM Profile where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = $_GET['profile_id'].'Bad choice for first and last names'.$row['profile_id'];
    header( 'Location: index.php' ) ;
    return;
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Marcel Merchat - Deleting from Profile Data Table</title>
</head>
<body>
<div id="main">
<br>
<p class="center big">Confirm: Deleting</p>
<br>
<br>
<p class="center">First name:
    <?php
        echo(htmlentities($row['first_name']));
    ?>
</p>
<p class="center">Last name:
    <?php
        echo(htmlentities($row['last_name']));
    ?>
</p>

<form method="post">
<p class="center big">
<input type="hidden" name="profile_id" value="<?= $row['profile_id'] ?>">
<input class="button-submit spacer" type="submit" value="Delete" name="delete">
</p>
<br>
<br>
<h2 class="center spacer"><a href="index.php">Cancel</h2>
</form>
</div>
</body>
</html>
