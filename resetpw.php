 <?php

require('utils.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	reset_db();
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
		$messages = $messages.'Please enter an email address.<br>';
	}
	
	if ( $result != 'error') {
		// Expiration time
		$expiry_format = mktime(date("H"), date("i"), date("s"), date("m"), date("d")+2, date("Y"));
		$expiry_time = date("Y-m-d H:i:s", $expiry_format);
		
		// Reset key
		$key = $email.rand(0,1000).$expiry_time;
	
		// Pre-process data
		$email_safe = $mysql->real_escape_string($email);
		$key_hash   = md5($key);
		
		// Update DB
		$query = "INSERT INTO reset_requests (reset_pword, expiry_time, user_email) VALUES ('$key_hash', '$expiry_time', '$email_safe')";		
		$result = $mysql->query($query);
			
		if ( $result && reset_email($email_safe, $key_hash) ) {
			$messages .= 'Please check your email for a reset link!<br/>';
		} else {
			$messages .= 'Failed to send reset email.<br/>';
		}
	}

	// Close connection to DB
	$mysql->close();
	
	// Render result.php
	header('Location: result.php?result='.$messages);
}
	
function reset_email($email, $key) {
	$link = "https://localhost/PasswordWebsite/changepw.php?email=".$email."&key=".$key;
	
	$body = "Hello, <br><br>" .
			"Please follow the link below to create a " .
			"new password:<br>" . $link . "<br><br>" .
			"This link will expire after 2 days for ".
			"security reasons.<br><br>".
			"Thanks";
			
	$result = send_email($body, $email);

	return $result;
}

?>