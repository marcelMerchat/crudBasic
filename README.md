# crudBasic

To get started run the following SQL commands:

CREATE DATABASE misc;
GRANT ALL ON misc.* TO 'fred'@'localhost' IDENTIFIED BY 'zap';
GRANT ALL ON misc.* TO 'fred'@'127.0.0.1' IDENTIFIED BY 'zap';

USE misc; (Or select misc in phpMyAdmin)

CREATE TABLE users (
   user_id INTEGER NOT NULL
     AUTO_INCREMENT KEY,
   name VARCHAR(128),
   email VARCHAR(128),
   password VARCHAR(128),
   INDEX(email)
) ENGINE=InnoDB CHARSET=utf8;

CREATE TABLE Position (
position_id INTEGER NOT NULL AUTO_INCREMENT,
profile_id INTEGER,
rank INTEGER,
year INTEGER,
description TEXT,
PRIMARY KEY(position_id),

CONSTRAINT position_ibfk_1
FOREIGN KEY (profile_id)
REFERENCES Profile (profile_id)
ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE Institution (
institution_id INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
name VARCHAR(255),
UNIQUE(name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


Many-to-Many table:

CREATE TABLE Education (
profile_id INTEGER,
institution_id INTEGER,
rank INTEGER,
year INTEGER,

CONSTRAINT education_ibfk_1
FOREIGN KEY (profile_id)
REFERENCES Profile (profile_id)
ON DELETE CASCADE ON UPDATE CASCADE,

CONSTRAINT education_ibfk_2
FOREIGN KEY (institution_id)
REFERENCES Institution (institution_id)
ON DELETE CASCADE ON UPDATE CASCADE,

PRIMARY KEY(profile_id, institution_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


$(document).ready(function() {
// http://stackoverflow.com/questions/17650776/add-remove-html-inside-div-using-javascript
//<!--onclick="$(\'#position'+counter+'\').remove(); return false;-->
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
                        '<div id=\"position'+positionCount+'\"><p>Year: <input type="text" name="year'+positionCount+'" size="10" id="yr"/> <input \
                        type=\"button\" value="-" onclick="$(\'#position'+positionCount+'\').remove(); return false;"/></p> \
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
              //countEdu = 1;
              window.console && console.log("Adding education "+countEdu);
              var source = $('#edu-template').html();
              window.console && console.log("Adding education2");
              $('#edu_fields').append(source.replace(/@COUNT@/g, countEdu));
              countEdu++;
              // auto-completion handler for new additions
             window.console && console.log("Adding education3");
                      //getData =  function(request, response) {
                      //var term1 = decodeURIComponent(temp);
              var y = "school.php";
              $(document).on('click', '.school', 'input[type="text"]', function(){
                      eyedee = $(this).attr("id");
                      term = document.getElementById(id=eyedee).value;
                      $.getJSON('school.php?ter'+'m='+term, function(data) {
                      //var y = data.Result;
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
                    //$.getJSON('http://localhost/js/json/proj/school.php', function(data) {
                              //var source = data.Result;
});
