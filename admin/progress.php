<?php






if(isset($_GET['progress_key'])) { 
    $status = apc_fetch('upload_'.$_GET['progress_key']); 
    echo $status['current']/$status['total']*100; 
    die; 
} 
else{
    echo "error";
}


return;


session_start();

echo $_SESSION["UPLOAD_PROGRESS_my_form"];
return;
 
$key = ini_get("session.upload_progress.prefix") . "my_form";
if (!empty($_SESSION[$key])) {
    $current = $_SESSION[$key]["bytes_processed"];
    $total = $_SESSION[$key]["content_length"];
    echo $current < $total ? ceil($current / $total * 100) : 100;
}
else {
    echo $_SESSION[$key];
}

?>