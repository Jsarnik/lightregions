
<?php

require_once('config.php');
$response = '';
$err = '';
$isSuccess = false;

if (isset($_SESSION['current_user']['login_username'])) {
    // if logged in send them away to login
}

if (isset($_POST['sendEmail'])){

	$user_name = $_POST['username'];

	if($user_name != null || $user_name != '')
		$response = GenerateEmail($user_name);	
	else
		$response = "please enter a username.";	

	echo "<script>parent.response('".$response."', '". $isSuccess ."');</script>";

};

function GenerateEmail($user_login){

	global $isSuccess;

	$conn = mysqli_connect(db_host, db_user, db_pass, db_name);
	if (mysqli_connect_errno()) {
	    return 'An error occured.';
	}

	//check if user exists
	$query = "SELECT DISTINCT userid, email FROM users WHERE username = '". $user_login . "'";

	$result = mysqli_fetch_assoc(mysqli_query($conn, $query));
	if (!$result) {
		mysqli_close($conn);
		return "Username " . $user_login . " does not exist.";
	}

	$email = $result['email'];
	$userid = $result['userid'];

	if($email === null || $email === ''){
		mysqli_close($conn);
		return "No email associated with this username";
	}

	$token = getToken();
	$date = date('m/d/Y h:i:s a', time());
	$expireTime = date('Y-m-d H:i:s', strtotime('+30 minutes',$date));//30 min to reset it

	$query = "UPDATE users SET token = '" . $token . "', tokenexpiration = '". $expireTime ."' WHERE userid = '" . $userid . "'";

	if ($conn->query($query) !== TRUE) {
		mysqli_close($conn);
		return "An error occured.";
	}

	mysqli_close($conn);

	$to=$email;
    $subject="Password Reset";
    $from = 'info@lightregions.com';
    $body= 'Hi, '.$user_login.'<br><br>Please follow the link to reset your Light Regions Admin password <a href="http://lightregions.com/admin/password_reset.php?token=' . $token.'">http://lightregions.com/admin/password_reset.php?token=' . $token.'</a>.<br><br>This link will expire in 30 minutes. To request an active link, please go to <a href="http://lightregions.com/admin/recovery.php">http://lightregions.com/admin/recovery.php</a>';
    $headers = "From: " . strip_tags($from) . "\r\n";
    $headers .= "Reply-To: ". strip_tags($from) . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n"; 

	if(mail($to,$subject,$body,$headers)){
	    $isSuccess = true;
	    return "An email has been sent to your account\'s registered address";
	}
	else
		return "A mailor error has occured.";
	
}

function crypto_rand_secure($min, $max) {
        $range = $max - $min;
        if ($range < 0) return $min; // not so random...
        $log = log($range, 2);
        $bytes = (int) ($log / 8) + 1; // length in bytes
        $bits = (int) $log + 1; // length in bits
        $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter; // discard irrelevant bits
        } while ($rnd >= $range);
        return $min + $rnd;
}

function getToken($length=32){
    $token = "";
    $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
    $codeAlphabet.= "0123456789";
    for($i=0;$i<$length;$i++){
        $token .= $codeAlphabet[crypto_rand_secure(0,strlen($codeAlphabet))];
    }
    return $token;
}

?>


<!DOCTYPE html>
<html>
<head>
<title>Light Regions CMS - Request Password Reset</title>
      <script src="http://code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.2.4/angular.js"></script>
  <script type="text/javascript" src="js/cms-app/app.js"></script>
  <script type="text/javascript" src="js/cms-app/controllers/mainController.js"></script>
  <script src="js/ui-bootstrap-0.12.1.min.js"></script>
  <link rel="stylesheet" href="css/main.css">
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/bootstrap-responsive.min.css">

<script type=""text/javascript>

$(document).ready(function(){	
	$('input').on('focus',function(){
		$('#error_message').html('');
		$('#success_message').html('');
	})
})

function response(msg, isSuccess){
	console.log(isSuccess);
	if(isSuccess)
		$('#success_message').html(msg);
	else
		$('#error_message').html(msg);
}

</script>
<style>


#success_message{
	color:#000;
}

input[type=text]{
	  width: 94%;
}

</style>



</head>
<body>
    <div class='banner'>
        <div class='container'>
          <div id="header-content" style="text-align:center;">
            <h1>Change Password</h1>
            <div id="header-links">
              
            </div>
          </div>
        </div>
    </div>
  <div class="container main-frame">
  	<div class="search-box row-fluid form-inline" style="background:transparent;">
	<form method="POST" style="width:100%; text-align:left;" enctype="multipart/form-data" target="recover_frame" action="recovery.php">
		<div style="max-width:300px; margin:0 auto; position:relative;">
		<input type="text" name="username" placeholder="username" /></br>
	
		<div id="success_message"></div>
		<div id="error_message"></div>

<div style="width:100%; text-align:center;">		
	<input id="sendEmail" type="submit" value="Send Email" name="sendEmail">

<div>
		</div>
		<br/>

	</form>
  </div>
  </div>
<iframe id="recover_frame" name="recover_frame" frameborder="0" border="0" src="" scrolling="no" scrollbar="no" style="display:none;"> </iframe> 
</body>
</html>
