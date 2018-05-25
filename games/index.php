<?php
require_once "../api/api.php";
 ?>

 <html>

 <head>
   <?php
     imports("Dat Jap - Games");

    ?>
 </head>

 <body>

 <?php
   pageSetup();
 ?>

   <div id="cards">
     <?php
     db();
     $result = dbPrepare("SELECT id FROM games WHERE ?", "i", 1);

     if ($result->num_rows > 0) {
       while($row = $result->fetch_assoc()) {
         createGameCard($row["id"]);
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
