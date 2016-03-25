<?php
$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$src = $request->src;
$dest = $request->dest;

$src_path = $_SERVER["DOCUMENT_ROOT"] . $src;
$dest_path = $_SERVER["DOCUMENT_ROOT"] . '/' . $dest . '/' . pathinfo($src)['filename'] . '.' . pathinfo($src, PATHINFO_EXTENSION);
$src_thumb = pathinfo($src_path)['dirname'] . '/thumbnails/' . basename($src, '.' . pathinfo($src, PATHINFO_EXTENSION)) .'_thumb.' . pathinfo($src, PATHINFO_EXTENSION);
$dest_thumb = pathinfo($dest_path)['dirname'] . '/thumbnails/' . basename($src, '.' . pathinfo($src, PATHINFO_EXTENSION)) .'_thumb.' . pathinfo($src, PATHINFO_EXTENSION);


$fileMoveConditionsMet = true;
$isMoveThumb = false;
$error = "An error occurred";

if (file_exists($src_path)){
	if (!validPath($dest_path)){
		$fileMoveConditionsMet = false;
	}

	if (file_exists($src_thumb)){
		$isMoveThumb = true;
		if (!validPath($dest_thumb)){
			$fileMoveConditionsMet = false;
		}
	}	
}
else
{
	$fileMoveConditionsMet = false;
	$error =  $src_path . " does not exist";
}


if($fileMoveConditionsMet){
	rename($src_path, $dest_path);

	if($isMoveThumb){
		rename($src_thumb, $dest_thumb);
	}
}

$response_array["isSuccess"] = $fileMoveConditionsMet;
$response_array['errorMessage'] = $error;

header('Content-type: application/json');
echo json_encode($response_array);

function validPath($_path){
	// check if dest file already exists

	global $error;
	$_isValid = true;
	$_dir = pathinfo($_path)['dirname'];

	if(!file_exists($_path))
		{
		// check if dest dir even exists
		if(!file_exists($_dir)){
			//if not create it
			try {
				mkdir($_dir, 0777, true);
			}
			catch(Exception $e){
				// could not create folder invalidate
				$_isValid = false;
				$error = "Could not create destination folder at " . $_path;
			}
		}
	}
	else{
		//file already exists invalidate
		$_isValid = false;
		$error = "File with same name already exists at " . $_path;
	}

	return $_isValid;
}
?>