<?php

function myErrorHandler($errno, $errstr, $errfile, $errline) {
  if ( E_RECOVERABLE_ERROR===$errno ) {
    echo "'catched' catchable fatal error\n";
    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
    // return true;
  }
  return false;
}
set_error_handler('myErrorHandler');

init();

function init(){
//  Initiate curl
$ch = curl_init();
// Disable SSL verification
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
// Will return the response, if false it print the response
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// Set the url
curl_setopt($ch, CURLOPT_URL,'http://lightregions.com/data/data.json');
// Execute
$result=curl_exec($ch);
// Closing
curl_close($ch);

// Will dump a beauty json :3
$json_array = json_decode($result, true);

$categoryIndex = 0;
$imageIndex = 0;
$category_count = count($json_array['menu'][0]['sub-items']);
session_start();

if(isset($_SESSION['categoryIndex']) && $_SESSION['categoryIndex'] < $category_count)
{
	$categoryIndex = intval($_SESSION['categoryIndex']);

	$image_count = intval(count($json_array['menu'][0]['sub-items'][$categoryIndex]['images']));

	if(isset($_SESSION['imageIndex']) && $_SESSION['imageIndex'] < $image_count){
		$imageIndex = intval($_SESSION['imageIndex']);
	}
	else{
		if(($categoryIndex+1) < $category_count)
			$categoryIndex+=1;
		else
			$categoryIndex=0;
	}
}

$image_count = count($json_array['menu'][0]['sub-items'][$categoryIndex]['images']);

$nextImage = (intval($imageIndex) + 1);

$_SESSION['categoryIndex'] = (intval($categoryIndex)) . '<br/>';
$_SESSION['imageIndex'] = $nextImage . '<br/>';

$title = $json_array['menu'][0]['sub-items'][$categoryIndex]['images'][$imageIndex]['title'];

$direct_url = 'http://lightregions.com/#photography?category=' .$json_array['menu'][0]['sub-items'][$categoryIndex]['name']. '&content=&index=' . $imageIndex;

$status = $direct_url . '#lightregions #photography #LA #' . $json_array['menu'][0]['sub-items'][$categoryIndex]['name'] . ' #' . $title;

$image_path = 'http://lightregions.com' .$json_array['menu'][0]['sub-items'][$categoryIndex]['images'][$imageIndex]['url'];

$newTweet = [
  'status' => $status,
  'media' => $image_path
];

$newTweet = array(
  'status' => $status,
  'media[]' => $image_path
);

Send($newTweet);

}

function encodeURI($url) {
    // http://php.net/manual/en/function.rawurlencode.php
    // https://developer.mozilla.org/en/JavaScript/Reference/Global_Objects/encodeURI
    $unescaped = array(
        '%2D'=>'-','%5F'=>'_','%2E'=>'.','%21'=>'!', '%7E'=>'~',
        '%2A'=>'*', '%27'=>"'", '%28'=>'(', '%29'=>')'
    );
    $reserved = array(
        '%3B'=>';','%2C'=>',','%2F'=>'/','%3F'=>'?','%3A'=>':',
        '%40'=>'@','%26'=>'&','%3D'=>'=','%2B'=>'+','%24'=>'$'
    );
    $score = array(
        '%23'=>'#'
    );
    return strtr(rawurlencode($url), array_merge($reserved,$unescaped,$score));

}

function Send($params){

		echo $_SERVER['DOCUMENT_ROOT'];
		require_once($_SERVER['DOCUMENT_ROOT'].'/codebird/codebird.php');

	try{
	    $consumer_key = "iD1vnMw3VMn1ptYZx4JBWl895";
	    $consumer_secret = "CIR3jEUXXR5Yxi6K1j6HDdNsKNGXoECfARn8JasK0q09AN5WJm";
	    $access_token = "3111426681-HJXPz0Fz7Ofjd3GmBXx6tZyK9sGJBPTsmoKpKZW";
	    $access_secret = "vv23K574Re2x3p5QSZ29ugPyaNS7b3cB2CVTzlh15XHwq";
	    $cb;

	    // Fetch new Twitter Instance

		$cb = \Codebird\Codebird::getInstance();
	    $cb->setConsumerKey($consumer_key, $consumer_secret);

	        //\Codebird\Codebird::setConsumerKey($this->consumer_key, $this->consumer_secret);
	        //$this->twitter = Codebird::getInstance();

	        // Set access token

	    $cb->setToken($access_token, $access_secret);
	        //$this->twitter->setToken($this->access_token, $this->access_secret);
		//$reply = $cb->statuses_update($params);
		$reply = $cb->statuses_updateWithMedia($params);
	}
	catch (Exception $ex) {
		echo $ex;
		 file_put_contents( 'tmp/Log.txt', $ex->getMessage(), FILE_APPEND);
	}
}


?>