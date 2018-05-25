<?php
require_once('api.php');
if(!$_POST) {
  header('Location: /');
}



$target_dir = "../images/groups/";
$fileName = $target_dir . $_POST['group']/*basename($_FILES["fileToUpload"]["name"])*/;
/*$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
//if(isset($_POST["submit"])) {
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if($check !== false) {//check if image
        $uploadOk = 1;
    } else {
        $uploadOk = 0;
    }
//}
if (file_exists($target_name)) {//test if name is taken
    $uploadOk = 0;
}
if ($_FILES["fileToUpload"]["size"] > 500000) {//check image size
    $uploadOk = 0;
}
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" ) {//test if its a image extention we allow
    $uploadOk = 0;
}
if ($uploadOk == 0) {
    //file had error
} else {*/
    $imageData = $_POST['image'];
    $ext = upload("../images/groups/", $imageData, $_POST['group']);
    //if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_name)) {
        //echo basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
        db();
        dbPrepare("INSERT INTO `chatgroups`(chatgroup,image,zoom,posx,posy) VALUES (?,?,?,?,?)","ssiii",$_POST['group'],$_POST['group'] . '.' . $ext,$_POST['zoom'],$_POST['x'],$_POST['y']);
        //dbPrepare("INSERT INTO `chatgroups`(chatgroup,image,zoom,posx,posy) VALUES (?,?,?,?,?)","ssiii",$_POST['group'],$_POST['image'],$_POST['zoom'],$_POST['x'],$_POST['y']);
    //} else {
      //problem with upload
    //}
//}
function upload($dir, $img, $name) {

  if (in_array($ext,['gif', 'png', 'jpg'])){
    define('UPLOAD_DIR', $dir);
    $ext = explode(';',explode('/', $img)[1])[0];
    $img = str_replace('data:image/'.$ext.';base64,', '', $img);
    $img = str_replace(' ', '+', $img);
    $data = base64_decode($img);
    $file = UPLOAD_DIR . $name . '.' . $ext;
    $success = file_put_contents($file, $data);
    return $ext;
  } else {
    return '';
  }


}

 ?>
