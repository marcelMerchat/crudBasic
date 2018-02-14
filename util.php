<?php
// util.php utilities

function flashMessages(){
    if ( isset($_SESSION['error']) ) {
          echo '<p class="big" style="color:red">'.$_SESSION['error'].'</p>';
          unset($_SESSION['error']);
    }
    if ( isset($_SESSION['success']) ) {
          echo '<p class="big" style="color:green">'.$_SESSION['success'].'</p>';
          unset($_SESSION['success']);
    }
  }
function validateProfile() {
  if ( (strlen($_POST['first_name']) > 0) && (strlen($_POST['last_name']) > 0)
                                          &&
            (strlen($_POST['email']) > 0) && (strlen($_POST['headline']) > 0)
                                          &&
            (strlen($_POST['summary']) > 0) )   {

       if ( strpos($_POST['email'],'@') === false ) {
            return "Invalid email address";
       }
  } else {
      return "All values are required";
  }
  return true;
}
function validatePos() {
    for($i=1; $i<=9; $i++) {
        if ( ! isset($_POST['year'.$i]) ) continue;
        if ( ! isset($_POST['desc'.$i]) ) continue;
        $year = $_POST['year'.$i];
        $desc = $_POST['desc'.$i];
        if ( ! is_numeric($year) ) {
            return "Position year must be numeric";
        }
        if ( strlen($year) == 0 || strlen($desc) == 0 ) {
            return "All fields are required";
        }
    }
    return true;
}
function validateEducation() {
    for($i=1; $i<=9; $i++) {
        if ( ! isset($_POST['edu_year'.$i]) ) continue;
        if ( ! isset($_POST['edu_school'.$i]) ) continue;
        $year = $_POST['edu_year'.$i];
        if ( ! is_numeric($year) ) {
            return "Education year must be numeric";
        }
        if ( strlen($year) == 0 ) {
            return "The education year is required";
        }
        $school = $_POST['edu_school'.$i];
        if ( strlen($school) == 0 ) {
            return "The name of the educational institution is required";
        }
    }
    return true;
}
function validateInstitution() {
    for($i=1; $i<=9; $i++) {
        if ( ! isset($_POST['name'.$i]) ) continue;
        $institution = $_POST['name'.$i];
        if ( strlen($institution) == 0 ) {
            return "The name of the educational institution is required";
        }
    }
    return true;
}
function loadPos($pdo, $profile_id) {
  $stmt = $pdo->prepare('SELECT * FROM Position
      WHERE profile_id = :prof ORDER BY rank');
  $stmt->execute(array(':prof' => $profile_id) );
  $positions = $stmt->fetchALL(PDO::FETCH_ASSOC);
  return $positions;
}
function loadEdu($pdo, $profile_id) {
  $sql = 'SELECT Education.year, Institution.name FROM Education JOIN Institution
      ON Education.institution_id = Institution.institution_id
      WHERE Education.profile_id = :prof ORDER BY `rank`';
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array(':prof' => $profile_id) );
  $education = $stmt->fetchALL(PDO::FETCH_ASSOC);
  return $education;
}
function insertPositions($pdo, $profile_id) {
    $rank = 1;
    for($i=1; $i<=9; $i++) {
      if ( isset($_POST['year'.$i]) && isset($_POST['desc'.$i]) &&
        strlen($_POST['year'.$i]) > 0 && (strlen($_POST['desc'.$i]) > 0) ) {
                $stmt = $pdo->prepare('INSERT INTO Position
                    (profile_id, `rank`, year, description)
                    VALUES ( :pid, :rnk, :yr, :de)');
                $stmt->execute(array(
                    ':pid' => $profile_id,  ':rnk' => $rank,
                    ':yr' => $_POST['year'.$i], ':de' => $_POST['desc'.$i])  );
                $rank++;
      } else {
        continue;
      }
    }
}
function insertEducations($pdo, $profile_id) {
    $rank = 1;
    for($i=1; $i<=9; $i++) {
      if ( ! isset($_POST['edu_year'.$i]) ) continue;
      if ( ! isset($_POST['edu_school'.$i]) ) continue;
      $year = $_POST['edu_year'.$i];
      $school = $_POST['edu_school'.$i];
      //lookup the school
      $institution_id = false;
      $sql = 'SELECT institution_id FROM Institution WHERE name = :nme';
      $stmt = $pdo->prepare($sql);
      $stmt->execute(array(':nme' => $school) );
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      if($row !== false) {
          $institution_id = $row['institution_id'] + 0;
      }
      //if school not found, insert it
      if($institution_id === false) {
          $sql = 'INSERT INTO Institution (`name`) VALUES (:nme)';
          $stmt = $pdo->prepare($sql);
          $stmt->execute(array(':nme' => $school) );
          $institution_id = $pdo->lastInsertId() + 0;
      }
      $sql = 'INSERT INTO Education (profile_id, institution_id, `rank`, year)
                    VALUES ( :pid, :iid, :rnk, :yr)';
      $stmt = $pdo->prepare($sql);
      $stmt->execute(array(
                  ':pid' => $profile_id, ':iid' => $institution_id,
                  ':rnk' => $rank,       ':yr' => $year ) );
      $rank++;
      }
}
function get_name($pdo, $user_id) {
    $stmt = $pdo->prepare('SELECT name FROM users WHERE user_id= :id');
    $stmt->execute(array(':id' => $user_id) );
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $fullname = array_values($row)[0];
    return $fullname;
}
function get_profile_information($pdo, $profile_id, $user_id) {
  // Retrieve the profile information for the profile_id
  $sql = 'SELECT * FROM Profile WHERE profile_id = :pid AND user_id = :uid';
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array(':pid' => $profile_id, ':uid' => $user_id));
  $profile = $stmt->fetch(PDO::FETCH_ASSOC);
  if($profile==false){
      $_SESSION['error'] = 'Could not load profile';
  }
  return $profile;
}
// Count the number of existing conditions
function get_position_count($pdo) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM Position where profile_id = :profileid");
    $stmt->execute(array(':profileid' => $_REQUEST['profile_id']));
    $obj = $stmt->fetch(PDO::FETCH_NUM);
    return $obj[0];
}
function get_json($pdo) {
    require_once "pdo.php";
    session_start();
    header('Content-Type: application/json; charset=utf-8');
    $t = $_GET['term'];
    echo $t;
    error_log('Looking for type-ahead term '.$t);
    $stmt = $pdo->prepare('SELECT name FROM Institution
                      WHERE name LIKE :prefix');
    $stmt->execute(array( ':prefix' => "%$t%" ) );
    $row = $stmt->fetchColumn();
    $retval[] = array();
    while( $row = $stmt->fetchColumn()) {
        $retval[] = $row['name'];
    }
return json_encode($retval, JSON_PRETTY_PRINT);
}
