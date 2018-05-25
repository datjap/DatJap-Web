<?php
require_once "api/api.php";
 ?>

<!DOCTYPE html>
<html>

<head>
  <?php
    imports("Dat Jap");

   ?>
</head>

<body>
<?php
  pageSetup();
?>

  <div id="cards">
    <?php
    db();
    $result = dbPrepare("SELECT chatgroup FROM chatgroups WHERE ?", "i", 1);

    if ($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
        createChatCard($row["chatgroup"]);
      }
    }
     ?>
  </div>
</body>
<!--Scripts-->
<?php
  scripts();
 ?>

</html>
