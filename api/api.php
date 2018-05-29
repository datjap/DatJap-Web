<?php
enforceHttps();
session_start();

error_reporting(E_ALL);
ini_set("display_errors", "On");

class User{
  var $id;
  var $username;
  var $email;
  var $displayName;
  var $priv;
  var $salt;
  var $hashedPassword;
  var $emailToken;
  var $emailVerified;
  var $cacheToken;
  var $lastAction;
  var $firstJoin;

  function __construct($id, $username, $email, $displayName, $priv, $salt, $hashedPassword, $emailToken, $emailVerified, $cacheToken, $lastAction, $firstJoin) {
    $this->id = $id;
    $this->username = $username;
    $this->email = $email;
    $this->displayName = $displayName;
    $this->priv = $priv;
    $this->salt = $salt;
    $this->hashedPassword = $hashedPassword;
    $this->emailToken = $emailToken;
    $this->emailVerified = $emailVerified;
    $this->cacheToken = $cacheToken;
    $this->lastAction = $lastAction;
    $this->firstJoin = $firstJoin;
  }



  function getOnline() {

  }

  function getFriendStates() {
    db();
    $statement = "SELECT * FROM friends WHERE user1 LIKE $this->id OR user2 LIKE $this->id AND ?";
    $types = "i";
    $result = dbPrepare($statement, $types, 1);

    if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      logger(varToString($row));
    }
  }

  function getFriends() {
    return array();
  }

  function getPending() {
    return array();
  }

  function getRequests() {
    return array();
  }

  function getBlocked() {
    return array();
  }

  function getGameStats() {

  }

  function getIpAddresses() {

  }

  function getPrivateJSON() {
    $object = (object) [
      'username' => $this->username,
      'email' => $this->email,
      'displayName' => $this->displayName,
      'priv' => $this->priv,
      'emailVerified' => $this->emailVerified,
      'friends' => $this->getFriends(),
      'pending' => $this->getPending(),
      'requests' => $this->getRequests(),
      'blocked' => $this->getBlocked(),
    ];

    //logger(varToString($object));

    $json = json_encode($object);
    echo $json;
  }

  function getPublicJSON() {
    $object = (object) [
      'username' => $this->username,
      'displayName' => $this->displayName,
      'priv' => $this->priv,
      'friends' => $this->getFriends(),
    ];

    $json = json_encode($object);
    echo $json;
  }

  public static function getUserByRowData($row) {
    return(new User($row['id'], $row['username'], $row['email'], $row['displayname'], $row['priv'],
    $row['passwordsalt'], $row['password'], $row['emailtoken'], $row['emailverified'], $row['token'],
    $row['lastseen'], $row['timecreated']));
  }

  public static function getUserById($id) {
    db();
    $statement = "SELECT * FROM users WHERE id=?";
    $types = "i";
    $result = dbPrepare($statement, $types, $id);

    //Result:
    if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      return(User::getUserByRowData($row));
    }
    return null;
  }

  public static function getUserByUsername($username) {
    db();
    $statement = "SELECT * FROM users WHERE username=?";
    $types = "s";
    $result = dbPrepare($statement, $types, $username);

    //Result:
    if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      return(User::getUserByRowData($row));
    }
    return null;
  }

  public static function getUserByEmail($email) {
    db();
    $statement = "SELECT * FROM users WHERE email=?";
    $types = "s";
    $result = dbPrepare($statement, $types, $email);

    //Result:
    if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      return(User::getUserByRowData($row));
    }
    return null;
  }
}

function varToString($input) {
  return(var_export($input, true));
}

function logger($input) {
  $myfile = fopen("./logger.txt", "a+") or die("Unable to open file!");
  $dbgt=debug_backtrace();
  $date = date('m/d/Y h:i:s a', time());
  $input = str_replace("\n", "\n \t", $input);
  $txt = $date . "UTC | " . $dbgt[0]['file'] . " [" . $dbgt[0]['line'] . "] \n \t" . $input . "\n";
  fwrite($myfile, $txt);
  fclose($myfile);
}

function enforceHttps(){
  if($_SERVER["HTTPS"] != "on"){
    header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
    exit();
  }
}

function login($user) {
  $_SESSION['user'] = $user;
}

function logout() {
  $_SESSION['user'] = null;
}

function imports($title) {
  ?>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $title; ?></title>
  <link href="https://fonts.googleapis.com/css?family=Roboto:100,100i,300,300i,400,400i,500,500i,700,700i,900,900i" rel="stylesheet">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="/css/main.css">
  <link rel="shortcut icon" href="favicon.ico" />
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="/scripts/reload.js"></script>
  <script>
    var personalData;
  </script>
  <script class="reload reloadPersonalData">
    personalData =
    <?php
      if (isset($_SESSION['user'])) {
        ?>
          <?php echo $_SESSION['user']->getPrivateJSON();?>;
        <?php
      } else {
        ?>
          null;
        <?php
      }
    ?>
    ;
  </script>
  <?php
}

function scripts() {
  ?>
  <script src="/scripts/main.js"></script>
  <script src="/scripts/login.js"></script>
  <script src="/scripts/cards.js"></script>
  <script src="/scripts/friends.js"></script>
  <script src="/scripts/logout.js"></script>
  <?php
}

function cleanInput($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

function createChatCard($group){
  db();
  $result = dbPrepare("SELECT * FROM `chatgroups` WHERE chatgroup=?","s",$group);
  $row = $result->fetch_assoc();
  ?>
  <div class="card" style="<?php echo getGroupDivImageStyles($group); ?>">
    <div class="overlay">
      <div class="title">
        <h1><?php echo $group;?></h1></div>
      <span class="description"><?php echo $row['desc']; ?></span>
    </div>
  </div>
  <?php
}

function createGameCard($id){
  db();
  $result = dbPrepare("SELECT * FROM `games` WHERE id=?","s",$id);
  $row = $result->fetch_assoc();
  ?>
  <a href="<?php echo "/games" . $row['link']?>" class="clean">
    <div class="card" style="<?php echo "background-repeat: no-repeat;background-image: url(/images/games/" . $row['image'] .");background-size: ". 100 ."%;"; ?>">
      <div class="overlay">
        <div class="title">
          <h1><?php echo $row['name'];?></h1>
          <span class="cardArrow pulse"><i class="material-icons">keyboard_arrow_down</i></span>
        </div>
        <span class="description"><?php echo $row['desc']; ?></span>
      </div>
    </div>
  </a>
  <?php
}

function db($dbId = 0){
  include_once "database.php";
  dbConnect($dbId);
}

function pageSetup(){
  include_once "page.php";
  pageInit();
}

function getGroupDivImageStyles($group) {
  /*
  Usage:
  getGroupDivImageStyles($groupName);
  */
  if(!$group) {
    return null;
  }
  if(!$conn) {
    db();
  }
  $result = dbPrepare("SELECT * FROM `chatgroups` WHERE chatgroup=?","s",$group);
  $row = $result->fetch_assoc();
  $size_percent = $row['zoom'] * 100;
  return "background-repeat: no-repeat;background-image: url(/images/groups/" . $row['image'] .");background-size: ".$size_percent."%;background-position: ".$row['posx']."% ".$row['posy']."%;";
  //return "background-repeat: no-repeat;background-image: url(".$row['image'].");background-size: ".$size_percent."%;background-position: ".$row['posx']."% ".$row['posy']."%;";
}

function checkTaken($table, $type, $input) {
  db();
  $statement = "SELECT * FROM ". $table . " WHERE UPPER(" . $type . ")=UPPER(?)";
  $types = "s";
  $result = dbPrepare($statement, $types, $input);

  return ($result->num_rows > 0);
}

function getTextContent($name) {
  $replacements = array(
    '%t' => '<span class="tab"></span>',
    //'property' => 'value'
  );

  db();
  $statement = "SELECT * FROM text WHERE name LIKE ?";
  $types = "s";
  $result = dbPrepare($statement, $types, $name);
  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    if ($row['html'] == 0) {
      $row['input'] = htmlspecialchars($row['input']);
    } else {
      $row['input'] = $row['input'];
    }

    foreach($replacements as $find => $replace) {
      $row['input'] = str_replace($find,$replace,$row['input']);
    }
    return $row['input'];
  }
  return "";
}
?>
