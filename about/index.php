<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/api.php";
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
<div class="content">
  <div class="fill">
    <h1>About</h1>
    <div class="left-align" style="margin: 15px">
      <?php
      echo getTextContent('about');
       ?>
    </div>
  </div>
</div>
 </body>
 <!--Scripts-->
 <?php
   scripts();
  ?>

 </html>
