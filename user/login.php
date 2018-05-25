<?php
require_once "../api/api.php";

if (isset($_POST['logininput'], $_POST['password'])) {
  db();
  $statement = "SELECT * FROM users WHERE UPPER(username)=UPPER(?) OR UPPER(email)=UPPER(?) OR phonenumber=?";
  $types = "sss";
  $result = dbPrepare($statement, $types, $_POST['logininput'],$_POST['logininput'],$_POST['logininput']);

  //Result:
  if (!($result->num_rows > 0)) {
    die('error');
  }
  $row = $result->fetch_assoc();
  $password = $row['password'];
  $passwordsalt = $row['passwordsalt'];
  if (hash('sha512', $_POST['password'] . $passwordsalt) == $password) {
    login(User::getUserByRowData($row));
    die($_SESSION['user']->getPrivateJSON());
  }
}
echo 'error';
 ?>
