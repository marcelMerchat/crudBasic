<?php
require_once "pdo.php";
require_once "util.php";
require_once 'detectmobile.php';
session_start();
?>
<!DOCTYPE html>
<html>
<head>
  <title>Marcel Merchat's Resume Registry</title>
  <?php
      function isMobile() {
          return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
      }
      if(isMobile()==1) {
          require_once 'mobile.php';
      } else {
          require_once 'headindex.php';
      }
?>
</head>
<body>
<div id="two">
<?php
// logged-in case
echo $_SERVER['HTTP_USER_AGENT'];
if ( isset($_SESSION['user_id']) && (strlen($_SESSION['user_id']) > 0) ) {
    echo('<br>');
    echo '<h1>Profiles for '.$_SESSION['full_name'].'</h1>';
    //echo('<br>');
    flashMessages();
    $stmt1 = $pdo->query("SELECT COUNT(*) FROM Profile");
    $row =  $stmt1->fetch(PDO::FETCH_ASSOC);
    $row_count = array_values($row)[0];
    if($row_count >= 1) {
        echo('<table border=2>');
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
            if ( $_SESSION['user_id'] == $row['user_id'] ) {
              echo '<a href="edit.php?profile_id='.$row['profile_id'].'"';
              echo '> Edit</a> / <a';
              echo ' href="delete.php?profile_id='.$row['profile_id'].'"';
              echo '> Delete</a>  / <a';
              echo ' href="view.php?profile_id='.$row['profile_id'].'"';
              echo '> View</a> ';
            }
            echo('</td>');
            echo("</tr>\n");
      };
      echo("</table>");
    } else {
          echo ('<p style="color:green">No rows found</p>');
    }
      echo('<br>');
      echo('<p>');
      //echo('<a href="add.php">Add New Entry</a>');
      echo('</p>');
      echo('<p class="big">');
      echo '<span>';
          echo '<a class="anchor-button" href="add.php">Add New Entry</a> <a' ;
          echo ' class="anchor-button" href="logout.php">Logout</a>' ;
      echo '</span></p>';
} else {
      echo('<br>');
      echo '<h2>Marcel Merchat\'s Resume Registry</h2>';
      echo('<br>');
      echo('<h2>');
      echo('<a href="login.php">Please log in</a>');
      echo('</h2>');
}
?>
</div>
</body>
</html>
