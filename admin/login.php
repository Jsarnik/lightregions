<!DOCTYPE html>
<html>
<head>
<title>Light Regions CMS - Login</title>
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

	$('#register').hide();
	$('#login_link').hide();
	$('#email').hide();

	$('input').on('mousedown',function(){
		$('#error_message').html('');
	})

	$('#login_link').on('click',function(){
		$('#email').hide();
		$('#register').hide();
		$('#member_login').show();
		$('#reg_link').show();
		$('#login_link').hide();
	})
	$('#reg_link').on('click',function(){
		$('#email').show();
		$('#register').show();
		$('#member_login').hide();
		$('#reg_link').hide();
		$('#login_link').show();
	})	
})

function login(response){

	switch (parseInt(response)){
		case 0:
			window.location.href = "photography.php";
		break;
		case 1:
			$("#error_message").html('Server error: could not connect.');
		break;
		case 2:
			$("#error_message").html('Could not log in, please check your user name and password or <a href="recovery.php">reset your password</a>.');
		break;
	}
}

</script>
<style>

input[type=text], input[type=password], input[type=email]{
	  width: 94%;
}

</style>
</head>
<body>
    <div class='banner'>
        <div class='container'>
          <div id="header-content" style="text-align:center;">
            <h1>Login</h1>
            <div id="header-links">
              
            </div>
          </div>
        </div>
    </div>
  <div class="container main-frame">
  	<div class="search-box row-fluid form-inline" style="background:transparent;">
	<form method="POST" style="width:100%; text-align:left;" action="login.php" enctype="multipart/form-data" target="login_frame">
		<div style="max-width:300px; margin:0 auto; position:relative;">
		<input type="text" name="username" placeholder="username" /></br>
		<input type="password" name="password"placeholder="password" /><br/>
		<input id="email" type="email" name="email" placeholder="email" /><br/>		
		<div id="error_message"></div>
<div style="right:0; position:absolute;">
		<a id="login_link" href="#">login</a>
		
</div>
<div style="width:100%; text-align:center;">		
		<input id="member_login" type="submit" value="Login" name="login">

<div>
		</div>
		<br/>

	</form>
  </div>
  </div>

<iframe id="upload_frame" name="login_frame" frameborder="0" border="0" src="" scrolling="no" scrollbar="no"> </iframe> 

</body>
</html>

<?php

session_start();

require_once('config.php');
$err = '';

if (isset($_SESSION['login_user_id'])) {
    // last request was more than 30 minutes ago
    session_unset();     // unset $_SESSION variable for the run-time 
    session_destroy();   // destroy session data in storage
}

if (isset($_POST['login'])){

	$user_name = $_POST['username'];
	$user_pass = $_POST['password'];

	$login_response = login($user_name, $user_pass);

    echo '<script>parent.login('.$login_response.');</script>';
};

if (isset($_POST['register'])){

	return;

	$user_name = $_POST['username'];
	$email = $_POST['email'];
	$user_pass = $_POST['password'];

	register($user_name, $email, $user_pass);
};

function hashed_password($password, $salt){
	// how many times the string will be hashed
	$rounds = 10000;

	return crypt($password, sprintf('$5$rounds=%d$%s$', $rounds, $salt));
}

function login($user_login, $user_password){

	if($user_login === null)
		$user_login = '';

	$conn = mysqli_connect(db_host, db_user, db_pass, db_name);
	if (mysqli_connect_errno()) {
		//echo '<script>$("#error_message").html("Server error: could not connect.");</script>';
	    return 1;
	    //die('Could not connect: ' . mysqli_error($conn));
	}

	$query = "SELECT DISTINCT userID, username, password, salt FROM users WHERE username = '". $user_login. "' OR email = '". $user_login. "'";

	$result = mysqli_fetch_assoc(mysqli_query($conn, $query));
	if (!$result) {
		//echo '<script>$("#error_message").html("Could not log in as ' . $user_login . ' please check your user name and password or register.");</script>';
		mysqli_close($conn);
		return 2;
	}

	$user_id = $result['userID'];
	$db_username = $result['username'];
	$db_password = $result['password'];
	$db_salt = $result['salt'];

	mysqli_close($conn);
	$salted_pass = hashed_password($user_password, $db_salt);

	if($db_password === $salted_pass){
		//if success set session up
		$browser = $_SERVER['HTTP_USER_AGENT'];		

		$session_array = [
    		"login_user_id" => $user_id,
    		"login_username" => $db_username,
    		"login_string" => hashed_password($user_password, $browser)
		];

		$_SESSION['current_user'] = $session_array;

		setcookie("username", $db_username, time()+3600);
		setcookie("password", hashed_password($user_password, $browser), time()+3600);
		return 0;

	}
	else{		
		return 2;
	}


}


function register($user_name,$email, $user_pass){
	$err = '';

	$conn = mysqli_connect(db_host, db_user, db_pass, db_name);
	if (mysqli_connect_errno()) {
	    echo '<script>$("#error_message").html("Server error: could not connect.");</script>';
	    return;
	}

	$salt = substr(str_replace('+','.',base64_encode(md5(mt_rand(), true))),0,16);
	$hash = hashed_password($user_pass, $salt);

	$query = "SELECT DISTINCT userid, username, email FROM users WHERE username = '". $user_name. "' OR email = '". $email. "'";

	$query2 = "INSERT INTO users (username, email, password, salt) VALUES ('". $user_name. "','". $email. "','". $hash. "','". $salt. "')";

	$result = mysqli_fetch_assoc(mysqli_query($conn, $query));

	if ($result['userid'] == null || $result['userid'] == '') {
	    $result2 = mysqli_query($conn, $query2);
    	if($result2){
		    mysqli_close($conn);
		    login($user_name, $user_pass);
		}
		else{
			echo '<script>$("#error_message").html("Server error: Could not connect.");</script>';
			mysqli_close($conn);
		}
	}
	else{
		if($email == null){
			$err = 'You must enter a valid email';
		}
		else if($result['email'] == $email){
			$err = 'A username is already registered under this email';
		}		
		else if($result['username'] == $user_name){
			$err = 'Username ' . $user_name . ' not available';
		}
		else{
			$err = 'An error occured';
		}
	}
	echo '<script>$("#error_message").html("' . $err . '");</script>';
	mysqli_close($conn);
}


?>
