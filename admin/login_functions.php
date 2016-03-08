<?php
$servername = "localhost";
$username = "root";
$password = "";
$db = "lightregions";
$err = '';

function login($user_login, $user_password){
	global $servername, $username, $password, $db;

	$conn = mysqli_connect($servername, $username, $password, $db);
	if (mysqli_connect_errno()) {
	    die('Could not connect: ' . mysqli_error($conn));
	}

	$query = "SELECT DISTINCT userID, username, password, salt FROM users WHERE username = '". $user_login. "' OR email = '". $user_login. "'";

	echo $query;

	$result = mysqli_fetch_assoc(mysqli_query($conn, $query));
	if (!$result) {
	    die('Could not query:' . mysqli_error($conn));
	}

	$user_id = $result['userID'];
	$db_username = $result['username'];
	$db_password = $result['password'];
	$db_salt = $result['salt'];

	$salted_pass = hashed_password($user_password, $db_salt);

	if($db_password === $salted_pass){
		//if success set session up
		$browser = $_SERVER['HTTP_USER_AGENT'];		

		$session_array = [
    		"login_user_id" => $user_id,
    		"login_username" => $db_username,
    		"login_string" => hashed_password($user_password + $browser)
		];

		$_SESSION['current_user'] = $session_array;

		mysqli_close($conn);
	    header("Location: photography.php");
	}

	mysqli_close($conn);
}


if (isset($_POST['login'])){
	$user_name = $_POST['username'];
	$user_pass = $_POST['password'];

	login($user_name, $user_pass);
};

if (isset($_POST['register'])){
	$user_name = $_POST['username'];
	$email = $_POST['email'];
	$user_pass = $_POST['password'];

	register($user_name, $email, $user_pass);
};

function register($user_name,$email, $user_pass){
	global $servername, $username, $password, $db;

	$conn = mysqli_connect($servername, $username, $password, $db);
	if (mysqli_connect_errno()) {
	    die('Could not connect: ' . mysqli_error());
	}

	$salt = substr(str_replace('+','.',base64_encode(md5(mt_rand(), true))),0,16);
	$hash = hashed_password($user_pass, $salt);

	$query = "SELECT DISTINCT userid, username, email FROM users WHERE username = '". $user_name. "' OR email = '". $email. "'";

	$query2 = "INSERT INTO users (username, email, password, salt) VALUES ('". $user_name. "','". $email. "','". $hash. "','". $salt. "')";

	$result = mysqli_fetch_assoc(mysqli_query($conn, $query));

	if ($result['userid'] == null || $result['userid'] == '') {
	    $result2 = mysqli_query($conn, $query2);
    	if($result2){
			$browser = $_SERVER['HTTP_USER_AGENT'];		

			$session_array = [
	    		"login_user_id" => $user_id,
	    		"login_username" => $db_username,
	    		"login_string" => hashed_password($user_password + $browser)
			];

			$_SESSION['current_user'] = $session_array;
		    mysqli_close($conn);
		    header("Location: photography.php");
		}
		else{
			die('Could not query:' . mysqli_error($conn));
		}
	}
	else{
		if($email == null){
			$err = 'must enter a valid email';
		}
		else if($result['email'] == $email){
			$err = 'already registered under this email';
		}		
		else if($result['username'] == $user_name){
			$err = 'username not available';
		}
		else{
			$err = 'not available';
		}
	}

	echo '<script>error();</script>';
	mysqli_close($conn);
}

	?>