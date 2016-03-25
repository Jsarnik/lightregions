
<?php

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$data_path = $request->path;
$data_path = $_SERVER["DOCUMENT_ROOT"] . $data_path;

$response_array["isSuccess"] = true;
$response_array['errorMessage'] = "";


if (file_exists($data_path)){
	try{
		unlink($data_path); //delete it
		$thumb_path = pathinfo($data_path)['dirname'] . '/thumbnails/' . basename($data_path, '.' . pathinfo($data_path, PATHINFO_EXTENSION)) .'_thumb.' . pathinfo($data_path, PATHINFO_EXTENSION);

		if (file_exists($thumb_path)){
			try{
				unlink($thumb_path); //delete it
			}
			catch(Exception $e){
				$response_array["isSuccess"] = false;
				$response_array['errorMessage'] = "An error occured attempting to delete " . $data_path;
			}
		}
	}
	catch(Exception $e){
		$response_array["isSuccess"] = false;
		$response_array['errorMessage'] = "An error occured attempting to delete " . $data_path;
	}
}
else{
	$response_array['errorMessage'] = "No file found at " . $data_path;
}

header('Content-type: application/json');
echo json_encode($response_array);

?>
