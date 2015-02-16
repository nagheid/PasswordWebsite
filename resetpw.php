 <?php

require('utils.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	session_start();
	reset_db();
} else {
	change_pw();
}

function reset_db() {
	// Initialize errors array
	$result = null;
	$messages = null;
	
	// Create and check connection
	$mysql = connectdb();
	
	// Retrieve data
	$email = $_POST['email'];
	
	// Validate data
	if (empty($email)) {
		$result = 'error';
		$messages = $messages.'Please enter an email address<br>';
	}
	
	if ( $result != 'error') {
		// Expiration time
		$expiry_format = mktime(date("H"), date("i"), date("s"), date("m")  , date("d")+3, date("Y"));
        $expiry_time = date("Y-m-d H:i:s",$expFormat);
		
		// Reset key
		$key = $email.rand(0,1000).$expiry_time;
	
		// Pre-process data
		$email_safe = $mysql->real_escape_string($email);
		$key_hash   = md5($key);
		
		// Update DB
		$query = "INSERT INTO reset_requests (reset_pword, expiry_time, user_email) VALUES ('$key_hash', '$expiry_time', '$email_safe')";		
		$result = $mysql->query($query);
			
		if ( $result && reset_email($email_safe, $key_hash) ) {
			$message = 'Please check your email for a reset link!<br>';
		} else {
			$message = 'Failed to send rest email.<br>';
		}
	}

	// Close connection to DB
	$mysql->close();
	
	// Render result.php
	header('Location: result.php?result='.$messages);
}
	
function reset_email($email, $key) {
	$link = "http://localhost/CSI4139L1/resetpw.php?email=".$email."&key=".$key;
	
	$body = "Hello, <br><br>" .
			"Please follow the link below to create a " .
			"new password:<br>" . $link . "<br><br>" .
			"Thanks";
			
	$result = send_email($body, $email);

	return $result;
}

function change_pw() {
	// Retrieve data
	$email = $_GET['email'];
	$key = $_GET['key'];
	
	// Connect to DB
	$mysql = connectdb();
	
	// Check if already verified
	$query = "SELECT  FROM reset_requests WHERE user_email='$email' ";
	$row = $mysql->query($query);
	$user = $row->fetch_array();
	$verified = $user['verified'];
	if( $verified ) {
		$message = 'Your email is already confirmed.<br>';
	} else {
		// Update DB
		$query = "UPDATE user SET verified=1 WHERE email='$email'";
		$result = $mysql->query($query);
		
		if ( $result ) {
			$message = 'Your email is confirmed!<br>';
		} else {
			$message = 'Failed to confirm email.<br>';
		}
	}
	
	$mysql->close();
	
	// Render result.php
	header('Location: result.php?result='.$message);
}

?>