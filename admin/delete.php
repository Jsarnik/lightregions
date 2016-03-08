
<?php

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$data_path = $request->path;
$data_path = $_SERVER["DOCUMENT_ROOT"] . $data_path;

if (isset($_SESSION['TEMP_UPLOAD'])) {
    if (file_exists($_SESSION['TEMP_UPLOAD']))
		unlink($_SESSION['TEMP_UPLOAD']); //delete it
}
if (isset($_SESSION['TEMP_THUMBNAIL'])) {
    if (file_exists($_SESSION['TEMP_THUMBNAIL']))
		unlink($_SESSION['TEMP_THUMBNAIL']); //delete it
}
if($data_path === "")
	return;

if (file_exists($data_path))
	unlink($data_path); //delete it

?>
