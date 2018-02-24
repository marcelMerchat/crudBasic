<?php
// 'edit.php'
require_once "pdo.php";
require_once "util.php";
session_start();

// If the user is not logged-in
if ( ! isset($_SESSION['user_id']))  {
      die('ACCESS DENIED');
      return;
}
// If the user requested cancel, go back to index.php
if ( isset($_POST['cancel']) ) {
    header('Location: index.php');
    return;
}
// Make sure the REQUEST parameter is present
if ( ! isset($_REQUEST['profile_id']) ) {
    $_SESSION['error'] = "Missing profile_id";
    header('Location: index.php');
    return;
}
if ( isset($_GET['profile_id']) && strlen($_GET['profile_id'] > 0)) {
    $_SESSION['profile_id'] = $_GET['profile_id'];
}

$uid = $_SESSION['user_id'];
$profileid = $_SESSION['profile_id'];
$posCount = get_position_count($pdo);
$_SESSION['posCount'] =  $posCount;

// Get profile from database
$profile = get_profile_information($pdo, $profileid, $uid);
if($profile===false){
    $_SESSION['error'] = 'Could not load profile';
    header('Location: index.php');
    return;
}
$fn = htmlentities($profile['first_name']);
$ln = htmlentities($profile['last_name']);
$em = htmlentities($profile['email']);
$hl = htmlentities($profile['headline']);
$sum = htmlentities($profile['summary']);

// Get educations and positions from database
$educations = loadEdu($pdo, $_SESSION['profile_id']);
$positions = loadPos($pdo, $_SESSION['profile_id']);

// Validation Data
if (isset($_POST['first_name']) && isset($_POST['last_name']) &&
    isset($_POST['email']) && isset($_POST['headline']) &&
    isset($_POST['summary'])) {

    $msg = validateProfile();
    if (is_string($msg) ) {
      $_SESSION['error'] = $msg;
      header('Location: edit.php?profile_id='.$_REQUEST['profile_id']);
      return;
    }
    //Validate position entries when present.
    $msg = validatePos();
    if (is_string($msg) ) {
       $_SESSION['error'] = $msg;
       header('Location: edit.php?profile_id='.$_REQUEST['profile_id']);
       return;
    }
    $msg = validateEducation();
    if (is_string($msg) ) {
       $_SESSION['error'] = $msg;
       header('Location: edit.php?profile_id='.$_REQUEST['profile_id']);
       return;
    }
    $msg = validateInstitution();
    if (is_string($msg) ) {
       $_SESSION['error'] = $msg;
       header('Location: edit.php?profile_id='.$_REQUEST['profile_id']);
       return;
    }
    // Update profiles
    $sql = "UPDATE Profile SET first_name = :fn, last_name = :lnm, email = :em, headline = :hl, summary = :sum WHERE profile_id = :prof_id ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':fn' => $_POST['first_name'], ':lnm' => $_POST['last_name'], ':em' => $_POST['email'],
                  ':hl' => $_POST['headline'], ':sum' => $_POST['summary'],  ':prof_id' => $_GET['profile_id']) );
  // Delete old education entries; recreate new list
    $stmt = $pdo->prepare('DELETE FROM Education WHERE profile_id = :pid');
    $stmt->execute(array(':pid' => $_REQUEST['profile_id']));
    // Clear old position entries; recreate new list
    $stmt = $pdo->prepare('DELETE FROM Position WHERE profile_id = :pid');
    $stmt->execute(array(':pid' => $_REQUEST['profile_id']));
    //Insert new position entries; create replacement list
    insertPositions($pdo, $_REQUEST['profile_id']);
    $posCount = get_position_count($pdo);
    $_SESSION['posCount'] =  $posCount;
    $_SESSION["success"] = 'Record edited: there are now '.$posCount.' positions.';

    insertEducations($pdo, $_REQUEST['profile_id']);
    $_SESSION['success'] = "Position added";
    header("Location: index.php");
    return;
}
?>

<!-- ---------------------------- VIEW ------------------------------------>

<!DOCTYPE html>
<html>
<head>
  <title>Marcel Merchat's Resume Registry</title>
<?php
   require_once 'jquery.php';
   if(isMobile()==1) {
      require_once 'mobile.php';
   } else {
      echo '<link rel="stylesheet" type="text/css" href="styleDesktop.css">';
   }
?>
<script src="script.js"></script>
</head>
<body>
  <div id="main">
          <h2>Editing profile: by <?= $_SESSION['full_name'] ?></h2>
<?php
          flashMessages();
          if(  $_SESSION['posCount'] === 9 ){
                $_SESSION['error'] = 'The number of position entries is at the limit of nine.';
                unset($_SESSION['error']);
                echo '<br>';
          }
?>
   <form method="post">
          <p><input type='hidden' name='profile_id' value='<?= $profileid ?>' ></p>
          <p><input type="hidden" name="user_id" value='<?= $uid ?>' ></p>
          <p>First Name: <input class="text-box" type="text" name="first_name" value='<?= $fn ?>' id="fn" size="30"></p>
          <p>Last Name: <input class="text-box"  type="text" name="last_name" value='<?= $ln ?>' id="ln" size="30"></p>
          <p class="small-bottom-pad">E-mail:</p>
          <input class="emale-entry-box"  type="text" name="email" value='<?= $em ?>' id="em">
          <p class="small-bottom-pad"> Headline:</p>
          <input class="big headline-box" type="text" name="headline" value='<?= $hl ?>' id="he">
          <p class="small-bottom-pad"> Summary:</p>
          <textarea class="big" name="summary" rows="8" cols="80"  id="su"> <?= $sum ?> </textarea>
<p>Add Education: <button class="click-plus" id="addEdu" >+</button></p>
<div id="edu_fields">
<?php
$countEdu = 1;
    foreach($educations as $education){
            $_SESSION['education_count'] = $countEdu;
            echo '<div id=\"edu'.$countEdu.'\">'."\n";
            echo '<p>Year: <input class="year-entry-box" type="text" name="edu_year'.$countEdu.'"';
            echo ' value="'.$education['year'].'">'."\n";
            echo '<input class="click-plus" type="button" value="-" ';
            echo 'onclick="$(\'#edu'.$countEdu.'\').remove(); return false;">'."\n";
            echo "</p>\n";
            echo '<p>School: <input class="text-box" type="text" name="edu_school'.$countEdu.'" value="'.htmlentities($education['name']).'" rows="8" cols="80"></p>';
            $countEdu++;
            echo "</div></p>\n";
    }
echo "</div>";
//Grab some HTML with hot spots and insert in the DOM
?>
<script id="edu-template" type="text">
    <div id="edu@COUNT@">
    <p>Year: <input class="year-entry-box" type="text" name="edu_year@COUNT@" value="" />
    <input class="click-plus" type="button" value="-" onclick="$('#edu@COUNT@').remove(); return false;"/><p>
    <p>School: <input class="school" type="text" size="80" name="edu_school@COUNT@" value="" id="school@COUNT@" /></p>
</script>
<!--<p>Position: <input type="submit" id="addPos" value="+">;-->
<p>Add Position: <button class="click-plus" id="addPos" >+</button></p>
<div id="position_fields">
<?php
$pos = 1;
foreach($positions as $position){
    $_SESSION['position_count'] = $pos;
    echo '<div id="position'.$pos.'">'."\n";
    echo '<p>Year: <input class="year-entry-box" type="text" name="year'.$pos.'"';
    echo ' value="'.$position['year'].'">'."\n";
    echo '<input class="click-plus" type="button" value="-" ';
    echo 'onclick="$(\'#position'.$pos.'\').remove(); return false;">'."\n";
    echo "</p>\n";
    echo '<textarea name="desc'.$pos.'" rows="8" cols="80">'.htmlentities($position['description']).'</textarea>';
    $pos++;
    echo "</div></p>\n";
}
?>
  </div>
  <div>
          <p>
          <input class="button-submit" type="submit" onclick="return doValidate();" value="Save">
          <input class="button-submit" type="submit" name="cancel" value="Cancel" size="40">
          </p>
  </div>
  </form>
</div>
<script>
$(document).ready(function() {
    window.console && console.log('Document ready called');
    var temp = "<?php echo $pos      ?>";
    $(document).ready(function() {
        window.console && console.log('Document ready called');
        countPos = 9;
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
                '<div id=\"position'+positionCount+'\"><p>Year: <input class="year-entry-box" type="text" name="year'+positionCount+'" size="10" id="yr"/> <input \
                 class="click-plus" type="button" value="-" onclick="$(\'#position'+positionCount+'\').remove(); return false;"/></p> \
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
              //window.console && console.log('Adding education '.$countEdu);
            $('#edu_fields').append(source.replace(/@COUNT@/g, countEdu));
                countEdu++;
                // auto-completion handler for new additions
                window.console && console.log("Appending to education");
                var y = "school.php";
                $(document).on('click', '.school', 'input[type="text"]', function(){
                    eyedee = $(this).attr("id");
                    term = document.getElementById(id=eyedee).value;
                    window.console && console.log('preparing json for '+term);
                $.getJSON('school.php?ter'+'m='+term, function(data) {
                    var y = data;
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
