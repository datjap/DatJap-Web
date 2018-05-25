<?php
require_once "../api/api.php";

if (!empty($_GET["type"]) && !empty($_GET["input"])){
  echo checkTaken("users", $_GET["type"], $_GET["input"]) ? "taken" : "untaken";
} else {
  echo "taken";
}
 ?>
