//Function For Validating Date
function isDate(value){
    // regular expression to match required date format
    re = /^(\d{1,2})\-(\d{1,2})\-(\d{4})$/;
    var date = value;

    if(value != '') {
        if(regs = date.match(re)) {
        // day value between 1 and 31
            if(regs[1] < 1 || regs[1] > 31) {
              alert("Invalid value for day: " + regs[1]);
              
              return false;
            }
            if (regs[1] < (new Date()).getDate() ) {
                alert("Cannot Use Past Date");
                return false;
            }

            // month value between 1 and 12
            if(regs[2] < 1 || regs[2] > 12) {
              alert("Invalid value for month: " + regs[2]);
     
              return false;
            }

            if (regs[2] < (new Date()).getMonth() ) {
                alert("Month cannot be in the past");
                return false;
            }
          
            if(regs[3] < (new Date()).getFullYear()) {
              alert("Cannot use past year");
              
              return false;
            }
        }else {
        alert("Invalid date format ");
        return false;
      }
    } 
}//End Of Function