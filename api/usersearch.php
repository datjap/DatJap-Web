<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/api.php";

logger(varToString($_GET));
$search = "%" . $_GET['search'] . "%";
db();
$statement = "SELECT * FROM users WHERE UPPER(username) LIKE UPPER(?) or UPPER(displayname) LIKE UPPER(?)";
$types = "ss";
$result = dbPrepare($statement, $types, $search, $search);

//Result:
if ($result->num_rows > 0) {
  logger(varToString($result));
  while($row = $result->fetch_assoc()) {
    echo var_dump($row);
  }
}
 ?>
