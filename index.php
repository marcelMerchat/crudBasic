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
    require_once 'head.php';
  ?>
</head>
<body>
<div id="two">
<?php
// logged-in case
if ( isset($_SESSION['user_id']) && (strlen($_SESSION['user_id']) > 0) ) {
    echo('<br>');
    echo '<h2>Profiles for '.$_SESSION['full_name'].'</h2>';
    //echo('<br>');
    flashMessages();
    $stmt1 = $pdo->query("SELECT COUNT(*) FROM Profile");
    $row =  $stmt1->fetch(PDO::FETCH_ASSOC);
    $row_count = array_values($row)[0];
    if($row_count >= 1) {
        echo('<table border="1">');
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
      echo('<a href="add.php">Add New Entry</a><br/>');
      echo('</p>');
      echo('<p>');
      echo('<a href="logout.php">Logout</a>');
      echo('</p>');
} else {
      echo('<br>');
      echo '<h2>Marcel Merchat\'s Resume Registry</h2>';
      echo('<br>');
      echo('<p>');
      echo('<a href="login.php">Please log in</a>');
      echo('</p>');
}
?>
</div>
</body>
