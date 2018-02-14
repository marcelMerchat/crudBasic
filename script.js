function doValidate() {
        //console.log('Validating...');
        try {
          firstname = document.getElementById('fn').value;
          lastname = document.getElementById('ln').value;
          addr = document.getElementById('em').value;
          headlines = document.getElementById('he').value;
          summaries = document.getElementById('su').value;
          if (firstname == null || firstname == "" || lastname == null || lastname == ""
                                         ||
              addr == null || addr == "" || headlines == null || headlines == ""
                                         ||
              summaries == null || summaries == "" ) {
                alert("All fields must be filled out");
                return false;
            }
            if ( addr.indexOf('@') == -1 ) {
                alert("Invalid email address");
                return false;
            }
            return true;
        } catch(e) {
            return false;
        }
        return false;
    }
