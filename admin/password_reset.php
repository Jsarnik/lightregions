<?php

session_start();
require_once('config.php');

$isSuccess = false;
$isValid  = false;
$expired = '';

$token = htmlspecialchars($_GET["token"]);

if (isset($_SESSION['current_user']['login_username'])) {
    header("Location: photograghy.php");
    return;
}

$isValid = SetupSession($token);

if ( isset($_POST['changePass']) ){
	resetPassword($_POST['newpass']);
}

function SetupSession($token){
	$conn = mysqli_connect(db_host, db_user, db_pass, db_name);
	if (mysqli_connect_errno()) {
		echo "Error connecting to database";
		return false;
	}

	$currentDate = date('m/d/Y h:i:s a', time());

	$query = "SELECT DISTINCT * FROM users WHERE token = '". $token. "' AND tokenexpiration >= '" .$currentDate. "'";

	$result = mysqli_fetch_assoc(mysqli_query($conn, $query));
	if (!$result) {
		mysqli_close($conn);
		echo "Error connecting to database";
		return false;
	}

	$browser = $_SERVER['HTTP_USER_AGENT'];		

	$session_array = [
		"_user_id" => $result['userid'],
		"_username" => $result['username']
	];

	$_SESSION['temp_user'] = $session_array;

	if(!isset($_SESSION['temp_user']['_user_id'])){
		echo "Session expired or does not exist";
		return false;
	}

	return true;
}


function resetPassword($password){

	$conn = mysqli_connect(db_host, db_user, db_pass, db_name);
	if (mysqli_connect_errno()) {
		echo "Error connecting to database";
	    return;	    
	}

	$user_id = $_SESSION['temp_user']['_user_id'];
	$salt = substr(str_replace('+','.',base64_encode(md5(mt_rand(), true))),0,16);
	$hash = hashed_password($password, $salt);

	$query = "UPDATE users SET password = '". $hash ."', salt = '".$salt. "', token = null WHERE userid = '". $user_id. "'";

	if ($conn->query($query) !== TRUE) {
		mysqli_close($conn);
		echo "Error connecting to database";
		return;
	}

	mysqli_close($conn);

	$isSuccess = true;

	echo "<script>parent.submit(".$isSuccess.");</script>";

}

function hashed_password($password, $salt){
	// how many times the string will be hashed
	$rounds = 10000;

	return crypt($password, sprintf('$5$rounds=%d$%s$', $rounds, $salt));
}


?>


<!DOCTYPE html>
<html>
<head>
<title>Light Regions CMS - Password Reset</title>
      <script src="http://code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.2.4/angular.js"></script>
  <script type="text/javascript" src="js/cms-app/app.js"></script>
  <script type="text/javascript" src="js/cms-app/controllers/mainController.js"></script>
  <script src="js/ui-bootstrap-0.12.1.min.js"></script>
  <link rel="stylesheet" href="css/main.css">
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/bootstrap-responsive.min.css">



<script type=""text/javascript>

var isValid = '<?php echo $isValid; ?>';

$(document).ready(function(){	

	if(!isValid){

		$('body').html('This page has expired. You will be redirected to <a href="LightRegions.com">LightRegions.com</a> in 10 seconds...');

		count = 9;
		setInterval(function(){
			$('body').html('This page has expired. You will be redirected to <a href="LightRegions.com">LightRegions.com</a> in ' + count + ' seconds...');
			
			if(count === 0)
				window.location.href = "http://lightregions.com";

			count-=1;

		},1000);
	}


	$('#newPassCheck').on('keyup',function(){
		if( $(this).val() === $('#newpass').val()){
			$('#message').html('Passwords match').addClass('good').removeClass('bad');
			$('#changePass').prop('disabled', false);
		}
		else{
			$('#message').html('Passwords don\'t match').addClass('bad').removeClass('good');
			$('#changePass').prop('disabled', true);
		}
	})

	$('#newpass').on('keyup',function(){
		if( $(this).val() === $('#newPassCheck').val()){
			$('#message').html('Passwords match').addClass('good').removeClass('bad');
			$('#changePass').prop('disabled', false);
		}
		else{
			$('#message').html('Passwords don\'t match').addClass('bad').removeClass('good');
			$('#changePass').prop('disabled', true);
		}
	})	

})

function submit(isSuccess){
	if(isSuccess)
		$('body').html('Password changed successfully, return to <a href="login.php">login</a>');
	else{
		$('#message').html('An error occured.');
	}
}

</script>
<style>

input[type=text],input[type=password]{
	  width: 94%;
}

.bad{color:red;}

.good{color:green;}
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
	<form method="POST" style="width:100%; text-align:left;" enctype="multipart/form-data" action="password_reset.php" target="reset_frame">
		<div style="max-width:300px; margin:0 auto; position:relative;">
		<input type="text" name="username" readonly="true" value="<?php echo $_SESSION['temp_user']['_username'] ?>">
		<input type="password" name="newpass" placeholder="New Password" id="newpass"/></br>
		<input type="password" name="newPassCheck" placeholder="Re-Enter Password" id="newPassCheck"/><div id="message" class="bad"></div>
	
		

<div style="width:100%; text-align:center;">		
	<input id="changePass" type="submit" value="Apply" name="changePass" disabled="true">

<div>

<iframe id="reset_frame" name="reset_frame" frameborder="0" border="0" src="" scrolling="no" scrollbar="no"> </iframe> 
		</div>
		<br/>

	</form>
  </div>
  </div>

</body>
</html>

