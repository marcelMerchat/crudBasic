<?php
// 'add.php'
require_once "pdo.php";
require_once "util.php";
session_start();

if ( ! isset($_SESSION['user_id']))  {
      die('ACCESS DENIED');
}
// If the user requested cancel, go back to view.php
if ( isset($_POST['cancel']) ) {
    header('Location: index.php');
    return;
}
if (isset($_POST['first_name']) && isset($_POST['last_name']) &&
    isset($_POST['email']) && isset($_POST['headline']) &&
    isset($_POST['summary'])) {

    $msg = validateProfile();
    if (is_string($msg) ) {
      $_SESSION['error'] = $msg;
      header( 'Location: add.php' );
      return;
    }
    $msg = validateEducation();
    if (is_string($msg) ) {
       $_SESSION['error'] = $msg;
       header('Location: add.php');
       return;
    }
    $msg = validatePos();
    if (is_string($msg) ) {
       $_SESSION['error'] = $msg;
       header('Location: add.php');
       return;
    }
    $sql = 'INSERT INTO Profile
           (user_id, first_name, last_name, email, headline, summary)
           VALUES ( :uid, :fn, :ln, :em, :he, :su)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
          ':uid' => $_SESSION['user_id'], ':fn' => $_POST['first_name'],
          ':ln'  => $_POST['last_name'],  ':em' => $_POST['email'],
          ':he'  => $_POST['headline'],   ':su' => $_POST['summary']) );
    $profileid = $pdo->lastInsertId();
    insertPositions($pdo, $profileid);
    insertEducations($pdo, $profileid);
    $_SESSION['success'] = "Position added";
    header("Location: index.php");
    return;
}
$countEdu = 1;
$pos = 1;

?>
<!-------------------------------- VIEW ------------------------------------>
<!DOCTYPE html>
<html>
<head>
<title>Marcel Merchat's Profile Entry</title>
<?php
  require_once 'head.php';
?>
<script src="script.js"></script>
</head>
<body>
<div id="two">
<?php
    if ( isset($_SESSION['name']) ) {
              echo ('<h2>Adding Profile for '.$_SESSION['full_name']);
              htmlentities($_SESSION['name']);
              echo '</h2>';
    }
    flashMessages();
?>
<form method="post">
    <input type="hidden" name="user_id" value=$_SESSION['user_id']></p>
    <p class="big">First Name: <input class="name-entry-box" type="text" name="first_name" value='<?= htmlentities("") ?>' id="fn"/></p>
        <p class="big">Last Name: <input class="name-entry-box" type="text" name="last_name" value='<?= htmlentities("") ?>' id="ln"/></p>
        <p class="big">Email: <input class="email-entry-box" type="text" name="email" value='<?= htmlentities("") ?>' id="em"/></p>
        <p class="big small-bottom-pad">Headline:</p>
        <p> <input class="name-box" type="text" name="headline" value='<?= htmlentities("") ?>'id="he" /></p>
        <p class="big small-bottom-pad">Summary: </p>
        <p><textarea rows="3" name="summary" value='<?= htmlentities("") ?>' id="su"></textarea></p>
        <p>Add Education: <button class="button-plus" id="addEdu" >+</button></p>
<?php
        echo '<div id="edu_fields">'."\n";
        echo "</div>";
?>
<!--Grab some HTML with hot spots and insert in the DOM-->
<script id="edu-template" type="text">
        <div id="edu@COUNT@">
            <p>Year: <input class="name-box-small" type="text" name="edu_year@COUNT@" value="" />
            <input type="button" class="button-plus" value="-" onclick="$('#edu@COUNT@').remove(); return false;"/><p>
            <p>School: <input type="text" size="80" name="edu_school@COUNT@" class="name-entry-box school" value="" id="school@COUNT@" />
            </p>
</script>
        <p>Add Position: <button class="button-plus" id="addPos" >+</button></p>
        <div id="position_fields">
        </div>
        </p>
        <p>
          <input class="button-cancel" type="submit" onclick="return doValidate();" value="Add"/> <input
          class="button-cancel" type="submit" name="cancel" value="Cancel">
        </p>
</form>
</div>
<script>
$(document).ready(function() {
    window.console && console.log('Document ready called');
    var temp = "<?php echo $pos ?>";
    $(document).ready(function() {
        window.console && console.log('Document ready called');
        //countPos = 9;
        countphp = 1;
        var temp = "<?php echo $pos  ?>";
        window.console && console.log('Document ready called');
        var positionCount = Number("<?php echo $pos      ?>");
        var countEdu =      Number("<?php echo $countEdu ?>");
        $('#addPos').click(function(event){
                  event.preventDefault();
                  if( positionCount > 9){
                      alert('Maximum of nine position entries exceeded');
                      return;
                  }
                  window.console && console.log("Adding position "+positionCount);
                  $('#position_fields').append(
                        '<div id=\"position'+positionCount+'\"><p>Year: <input type="text" name="year'+positionCount+'" size="10" id="yr"/> <input \
                        type=\"button\" class="button-plus" value="-" onclick="$(\'#position'+positionCount+'\').remove(); return false;"/></p> \
                        <p>Description: </p> \
                        <textarea name=\"desc'+positionCount+'\" rows = "8" cols="80" id="de" ></textarea> \
                  </div>');
                  positionCount++;
          });
        $('#addEdu').click(function(event) {
              event.preventDefault();
              if(countEdu >= 9){
                    alert('Maximum of nine education entries exceeded');
                    return;
              }
              window.console && console.log("Adding education "+countEdu);
              var source = $('#edu-template').html();
              window.console && console.log("Adding education2");
              $('#edu_fields').append(source.replace(/@COUNT@/g, countEdu));
              countEdu++;
              // auto-completion handler for new additions
             window.console && console.log("Adding education3");
              var y = "school.php";
              $(document).on('click', '.school', 'input[type="text"]', function(){
                      eyedee = $(this).attr("id");
                      term = document.getElementById(id=eyedee).value;
                      $.getJSON('school.php?ter'+'m='+term, function(data) {
                           var y =data;
                           $('.school').autocomplete({ source: y });
                      });
              });
          });  //end of addedu
        $(document).on('click', '.school', 'input[type="text"]', function(){
              eyedee = $(this).attr("id");
              term = document.getElementById(id=eyedee).value;
              $.getJSON('school.php?ter'+'m='+term, function(data) {
                   var y = data.Result;
                   var y =data;
                   $('.school').autocomplete({ source: y });
                          });
        });
    });
});
</script>
</body>
</html>
