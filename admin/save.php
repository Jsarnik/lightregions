<?php
$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$data = $request->data;
$data_path = $request->path;
$data_path = $_SERVER["DOCUMENT_ROOT"] . $data_path;
$backup_path = $_SERVER["DOCUMENT_ROOT"] . '/data/backup_log';

//create a backup file of old data and save it with the date
$date = date_create();
$dateString = date_format($date, 'Y-m-d H-i-s');

copy($data_path, $backup_path . '/' . $dateString . '.json.bak');

$fh = fopen($data_path, 'w')
      or die("Error opening output file");

fwrite($fh, json_encode($data,JSON_UNESCAPED_UNICODE));
fclose($fh);

if(isset($_SESSION['TEMP_THUMBNAIL']))
	session_unset($_SESSION['TEMP_THUMBNAIL']);
if(isset($_SESSION['TEMP_UPLOAD']))
	session_unset($_SESSION['TEMP_UPLOAD']);
?>