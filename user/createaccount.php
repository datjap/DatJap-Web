<?php
require_once "../api/api.php";


class RegisterInput {
  var $name;
  var $flipRegex;
  var $regex;
  var $lengthMin;
  var $lengthMax;
  var $checkTaken;
  var $hash;
  var $output;
  var $salt;

  function __construct($name, $input, $flipRegex, $regex, $lengthMin, $lengthMax, $checkTaken, $hash){
    $this->name = $name;
    $this->input = $input;
    $this->flipRegex = $flipRegex;
    $this->regex = $regex;
    $this->lengthMin = $lengthMin;
    $this->lengthMax = $lengthMax;
    $this->checkTaken = $checkTaken;
    $this->hash = $hash;
    $this->output = $input;
    $this->salt = "";
  }

  function test(){
    if (!$this->testLength() || !$this->testRegex() || !$this->testTaken()) {
      return false;
    }
    return true;
  }

  function testLength(){
    return (strlen($this->input) >= $this->lengthMin && (strlen($this->input) <= $this->lengthMax || $this->lengthMax == -1));
  }

  function testRegex(){
    return preg_match($this->regex, $this->input) xor $this->flipRegex;
  }

  function testTaken(){
    if ($this->checkTaken) {
        return !checkTaken("users", $this->name, $this->input);
    }
    return true;
  }

  function hashInput(){
    $this->salt = uniqid(mt_rand(), true);
    $this->output = hash('sha512', $this->input . $this->salt);
  }
}


$registerInputs = array(

  new RegisterInput("username", $_POST['username'],true, preg_quote('/[^a-zA-Z0-9_.$]/ '), 4, 30, true, false),
  new RegisterInput("displayname", $_POST['displayname'],true, preg_quote('/[^a-zA-Z $]/ ') , 2, 40, false, false),
  new RegisterInput("email", $_POST['email'],false, '/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/ ', 0, -1, true, false),
  new RegisterInput("password", $_POST['password'], true, preg_quote('/[]/ '), 4, -1, false, true)
);

$error = false;

foreach ($registerInputs as $input) {
  if (!$input->test()) {
    $error = true;
    break;
  }
}



if (!$error) {
  $userInputs = array("priv"=>array("i", 0));
  $username;
  foreach ($registerInputs as $input) {
    if ($input->hash){
      $input->hashInput();
      $userInputs[$input->name . "salt"] = array("s", $input->salt);
    }
    $userInputs[$input->name] = array("s", $input->output);

    if ($input->name == "username") {
      $username = $input->input;
    }
  }
  db();
  $table = "users";
  $columns = array();

  foreach ($userInputs as $colName => $colInputs) {
    array_push($columns, new Column($colName, $colInputs[0], $colInputs[1]));
  }
  dbInsert($table, $columns);
  login(User::getUserByUsername($username));
  echo $_SESSION['user']->getPrivateJSON();
} else {
  echo 'error';
}
 ?>
