<?php
require_once "pdo.php";
require_once "util.php";
session_start();
?>
<!DOCTYPE html>
<html>
<head>
  <title>Marcel Merchat's Resume Registry</title>
  <?php
      if(isMobile()==1) {
          require_once 'mobile.php';
      } else {
          echo '<link rel="stylesheet" type="text/css" href="styleDesktop.css">';
      }
?>
</head>
<body>
<div id="main">
<h2>Marcel Merchat's Resume Registry</h2>
<?php
// logged-in case
//echo $_SERVER['HTTP_USER_AGENT'];
if ( isset($_SESSION['user_id']) && (strlen($_SESSION['user_id']) > 0) ) {
    echo '<h2>Profiles for '.$_SESSION['full_name'].'</h2>';
    //echo('<br>');
    flashMessages();
}
    $stmt1 = $pdo->query("SELECT COUNT(*) FROM Profile");
    $row =  $stmt1->fetch(PDO::FETCH_ASSOC);
    $row_count = array_values($row)[0];
    if($row_count >= 1) {
        echo('<table class="quad-space" border=2>');
        echo "<tr><th>";
        echo('Name');
        echo("</th><th>");
        echo('Headline');
        echo('</th>');
        echo('<th>Action</th>');
        echo('</tr>');
        $sql = "SELECT profile_id, user_id, first_name, last_name, email,
                       headline FROM Profile ORDER BY last_name";
        $stmt = $pdo->query($sql);
        while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
            echo "<tr><td>";
            echo(htmlentities($row['first_name']).' '.htmlentities($row['last_name']));
            echo("</td><td>");
            echo(htmlentities($row['headline']));
            echo("</td>");
            echo("<td>");
            if ( isset($_SESSION['user_id']) && (strlen($_SESSION['user_id']) > 0) ) {
                if ( $_SESSION['user_id'] == $row['user_id'] ) {
                  echo '<a href="edit.php?profile_id='.$row['profile_id'].'"';
                  echo '> Edit</a> / <a';
                  echo ' href="delete.php?profile_id='.$row['profile_id'].'"';
                  echo '> Delete</a>  / <a';
                  echo ' href="view.php?profile_id='.$row['profile_id'].'"';
                  echo '> View</a> ';
                }
           }
            echo('</td>');
            echo("</tr>\n");
      }
      echo("</table>");
    } else {
          echo ('<p style="color:green">No rows found</p>');
    }
      echo('<p>');
      //echo('<a href="add.php">Add New Entry</a>');
      echo('</p>');
if ( isset($_SESSION['user_id']) && (strlen($_SESSION['user_id']) > 0) ) {
      echo('<p class="big">');
      echo '<span>';
          echo '<a class="anchor-button" href="add.php">Add New Entry</a> <a' ;
          echo ' class="anchor-button" href="logout.php">Logout</a>' ;
      echo '</span></p>';
} else {
      echo('<h1 class="double-space">');
      echo('<a href="login.php">Go to login</a>');
      echo('</h1>');
      echo '<p class="big quad-space">In order to view resumes, add new ';
      echo 'profiles, or make changes to the ';
      echo 'database, you can log in as ';
      echo '"guest@mycompany.com" using password "php123" if you don\'t ';
      echo 'have a password.';
      echo '</p>';
}
?>
</div>
</body>
</html>
