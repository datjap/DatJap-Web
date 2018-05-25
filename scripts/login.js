var loginBtn = document.getElementById("login");
var loginShield = document.getElementById("loginShield");
var loginShown = false;
var clickout = document.getElementById("clickout");

load(function() {
  $("#login").click(function() {
    $("#loginShield").toggleClass("open", true);
    $("#clickout").toggleClass("open", true);
  });

  $("#clickout").click(function() {
    $("#loginShield").toggleClass("open", false);
    $("#clickout").toggleClass("open", false);
  });
}, true);

class RegisterInput {
  constructor(elementId, flipRegex, regex, errorMessage, lengthMin, lengthMax, checkTaken, checkType) {
    this.element = document.getElementById(elementId);
    this.regex = regex;
    this.flipRegex = flipRegex;
    this.lengthMin = lengthMin;
    this.lengthMax = lengthMax;
    this.errorMessage = errorMessage;
    if (checkTaken == undefined) {
      checkTaken = false;
      checkType = null;
    }
    this.checkTaken = checkTaken;
    this.checkType = checkType;
    this.takenTested = false;
    this.errors = [];
    this.element.addEventListener("change", function(regex) {
      this.checkValid();
    }.bind(this, this.regex));

    this.element.addEventListener("click", function() {
      if (this.errors.length > 0) {
        var output = "";
        for (var i = 0; i < this.errors.length; i++) {
          output += this.errors[i] + ". ";
        }

        createToast(output);
      }
    }.bind(this));
  }

  takenTest() {
    var xhttp = new XMLHttpRequest();
    this.takenTested = false;
    var $this = this;
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        $this.takenCallback(this.responseText == "taken");
      }
    };

    xhttp.open("GET", "/user/checktaken.php?type=" + this.checkType + "&input=" + this.element.value, true);
    xhttp.send();
  }

  takenCallback(taken) {
    if (taken){
      this.takenTested = true;
      this.errors.push(this.checkType + " Taken");
      this.element.classList.add(["input-error"]);
    }
  }

  checkValid() {
    this.errors = [];
    this.element.classList.remove(["input-error"]);
    if (!this.regex.test(this.element.value)) {
      if (this.flipRegex) {

      } else {
        this.errors.push(this.errorMessage);
      }
    } else {
      if (this.flipRegex) {
        this.errors.push("Invalid Characters in Username");
      } else {

      }
    }
    if (this.element.value.length >= this.lengthMin && (this.lengthMax == -1 || this.element.value.length <= this.lengthMax)) {

    } else {
      if (this.lengthMax == -1) {
        this.errors.push("Length must be greater than " + this.lengthMin);
      } else {
        this.errors.push("Length must be between " + this.lengthMin + " and " + this.lengthMax);
      }
    }
    if (this.errors.length > 0) {
      this.element.classList.add(["input-error"]);
    } else {
      if (this.checkTaken) {
        this.takenTest();
      }
    }
  }
}

var regInputs = [
  new RegisterInput("registerName", true, /[^a-zA-Z $]/, "Invalid Characters, must use ABC's", 2, 40),
  new RegisterInput("registerUsername", true, /[^a-zA-Z0-9_.$]/, "Invalid Characters, must use ABC's", 4, 30, true, "Username"),
  new RegisterInput("registerEmail", false, /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/, "Invalid Email", 0, -1, true, "Email"),
  new RegisterInput("registerPassword", true, /[]/, null, 4, -1)
];

$('#registerForm').submit(function(e){
    e.preventDefault();
    var errorExists = false;
    for (var i = 0; i < regInputs.length; i++) {
      regInputs[i].checkValid();
      if (regInputs[i].errors.length > 0) {
        errorExists = true;
      }
    }
    if (errorExists) {
      createToast("<h3>Registration Failed</h3>");
    } else {

      $.post("/user/createaccount.php", {
        displayname: regInputs[0].element.value,
        username: regInputs[1].element.value,
        email: regInputs[2].element.value,
        password: regInputs[3].element.value
      }, function(data, status){
          console.log("Session Data: " + data + "\nStatus: " + status);
          reload();
          $("#loginShield").toggleClass("open");
          $("#clickout").toggleClass("open");
          loginShown = !loginShown;
      });
    }
});

$('#loginForm').submit(function(e){
  e.preventDefault();
  $.post("/user/login.php", {
    logininput: $('#loginInput').val(),
    password: $('#loginPassword').val()
  }, function(data, status){
      console.log("Session Data: " + data + "\nStatus: " + status);
      reload();
      $("#loginShield").toggleClass("open");
      $("#clickout").toggleClass("open");
      loginShown = !loginShown;
  });
});
