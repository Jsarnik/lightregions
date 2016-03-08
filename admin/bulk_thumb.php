
<?php
session_start();
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
    // last request was more than 30 minutes ago
    session_unset();     // unset $_SESSION variable for the run-time 
    session_destroy();   // destroy session data in storage
}
$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp

if (!isset($_SESSION['current_user']['login_user_id'])) {
  header("Location: login.php");
}

//$current_user = strtoupper($_SESSION['current_user']['login_username']);

//get unique id 
$up_id = uniqid();  

$root_dir = $_SERVER["DOCUMENT_ROOT"] . '/images';


$di = new RecursiveDirectoryIterator($root_dir);
foreach (new RecursiveIteratorIterator($di) as $filename => $file) {
    $path_parts = pathinfo($filename);
    if(fileTypeCorrect($filename, true)){
        createThumb($filename, $path_parts['dirname'] . '/thumbnails');
    }
}

function createThumb($src, $dest){
    if (!file_exists($dest)) 
        mkdir($dest, 0777, true);

    $source_image = imagecreatefromjpeg($src);
    $width = imagesx($source_image);
    $height = imagesy($source_image);

    $virtual_image = imagecreatetruecolor(100, 100);
    
    imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, 100, 100, $width, $height);
    
     if (!file_exists($dest . '/' . basename($src, '.' . pathinfo($src, PATHINFO_EXTENSION)) .'_thumb.jpg')){
        imagejpeg($virtual_image, $dest . '/' . basename($src, '.' . pathinfo($src, PATHINFO_EXTENSION)) .'_thumb.jpg');
     }     
}

function fileTypeCorrect($path, $shouldBeImage){    
    $path_parts = pathinfo($path);
    $ext = $path_parts['extension'];

    if($shouldBeImage){
        if($ext !== 'jpg' & $ext !== 'png' & $ext !== 'gif' & $ext !== 'jpeg' & $ext !== 'bmp'){
            return false;
        }
    }
    else{
        if($ext !== 'mp4'){
            return false;
        }
    }

    return true;
}

?>
           