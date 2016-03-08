
<?php

session_unset();
session_start();

$_SESSION['UPLOAD_SUCCESS'] = 'false';
$basedir = $_SERVER["DOCUMENT_ROOT"];
$destFolder = $_POST['destFolder'];
$target_file = $basedir.$destFolder . basename($_FILES["fileToUpload"]["name"]);
$shouldBeImage = true;
$errorMessage = '';
$max_file_size = 50000000;


if(!isset($_FILES["fileToUpload"])){
    $_SESSION['UPLOAD_MESSAGE'] = 'Could not upload file';
    return; 
}

if (strpos($destFolder,'images') === false)
    $shouldBeImage = false;

if(fileTypeCorrect($_FILES["fileToUpload"]["name"], $shouldBeImage))
    upload();

function upload(){
    global $destFolder, $shouldBeImage, $target_file, $max_file_size, $basedir;

    if (!file_exists($basedir.$destFolder)) {
        mkdir($basedir.$destFolder, 0777, true);
    }

    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        if($shouldBeImage)
            createThumb($target_file, $basedir.$destFolder.'/thumbnails');

        $_SESSION['TEMP_UPLOAD'] = $target_file;
        $_SESSION['UPLOAD_MESSAGE'] = 'upload success';
        $_SESSION['UPLOAD_SUCCESS'] = 'true';
    } else {
        if(filesize($target_file) > $max_file_size)
            $_SESSION['UPLOAD_MESSAGE'] = 'file too large';
        else
            $_SESSION['UPLOAD_MESSAGE'] = 'specific error: ' . $_FILES["fileToUpload"]["error"];
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
    
    imagejpeg($virtual_image, $dest . '/' . basename($src, '.' . pathinfo($src, PATHINFO_EXTENSION)) .'_thumb.jpg');
    $_SESSION['TEMP_THUMBNAIL'] = $dest . '/' . basename($src, '.' . pathinfo($src, PATHINFO_EXTENSION)) .'_thumb.jpg';

}

function fileTypeCorrect($path, $shouldBeImage){    
    $path_parts = pathinfo($path);
    $ext = $path_parts['extension'];

    if($shouldBeImage){
        if($ext !== 'jpg' & $ext !== 'png' & $ext !== 'gif' & $ext !== 'jpeg' & $ext !== 'bmp'){
            $_SESSION['UPLOAD_MESSAGE'] = 'Only image files with type .gif, .png, .jpg, .bmp accepted.';
            $_SESSION['UPLOAD_SUCCESS'] = 'false';
            return false;
        }
    }
    else{
        if($ext !== 'mp4'){
            $_SESSION['UPLOAD_MESSAGE'] = 'Only video files with type .mp4 accepted.';
            $_SESSION['UPLOAD_SUCCESS'] = 'false';
            return false;
        }
    }

    return true;
}

?>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.0/jquery.js" type="text/javascript"></script>

<script>
var error = '<?php echo $_SESSION["UPLOAD_MESSAGE"] ?>';

$(document).ready(function(){
    parent.informMe(error);
})

</script>

           