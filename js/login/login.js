//Logic for showing/hiding OTP on confirm login page with Password

  var showPasswdTxt = $(".showPwd");
  var passwordField = $(".password");
  var pwdId = document.getElementById("password");
  var messageArray = ["show password", "hide password"];
  ShowHideLoginPassword(showPasswdTxt, passwordField, pwdId, messageArray);

  passwordField.bind("input", function () {
    ShowHideLoginPassword(showPasswdTxt, passwordField, pwdId, messageArray);
  });

  showPasswdTxt.bind("click", function () {
    if (pwdId.type === "password") {
      pwdId.type = "text";
      showPasswdTxt.text(messageArray[1]);
    } else {
      pwdId.type = "password";
      showPasswdTxt.text(messageArray[0]);
    }
  });

  function ShowHideLoginPassword(showPasswordTxt, pwdField, passwordId, dataArr) {
      let passwd = pwdField.val();

      if (passwd.length > 0) {

        if(passwordId.type === "password"){
          showPasswordTxt.text(dataArr[0]);
        }

        else if(passwordId.type != "password"){
          showPasswordTxt.text(dataArr[1]);
        }

        else{
          showPasswordTxt.text(dataArr[0]);
        }

        showPasswordTxt.show();
       
      }else{
        showPasswordTxt.hide();
      }

  }