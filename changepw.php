 <?php

require('utils.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	update_pw();
} else {
	check_pw();
}

function check_pw() {
	// Retrieve data
	$email = $_GET['email'];
	$key = $_GET['key'];
	
	// Time
	$current_time = date("Y-m-d H:i:s"); //, $current_format);
	
	// Connect to DB
	$mysql = connectdb();
	
	// Check if already verified
	$stmnt = "SELECT reset_pword, expiry_time FROM reset_requests WHERE user_email='$email' AND expiry_time>='$current_time'";
	$query = $mysql->query($stmnt);
	//$rows = $query->fetch_all();
	
	$found = false;
	while ($row = $query->fetch_array()) {
		$reset_pword = $row['reset_pword'];
		$reset_expiry = $row['expiry_time'];
		
		if ($reset_pword == $key) {
			$found = true;
			break;
		}
	}
	
	$mysql->close();
	
	// Render html page
	if ($found) {
		header('Location: changepwform.php?email='.$email);
	} else {
		$messages = "Reset link is either invalid or expired. Please reset again.<br>";
		header('Location: result.php?result='.$messages);
	} 
}

function update_pw() {
	$result = null;
	$messages = '';
	
	// Create and check connection
	$mysql = connectdb();
	
	// Retrieve data
	$email = $_POST['email'];
	$email_confirm = $_POST['email_confirm'];
	$pword = $_POST['password'];
	$pword_confirm = $_POST['password_confirm'];
	
	// Validate data
	if (empty($email_confirm) || empty($pword)|| empty($pword_confirm)) {
		$result = 'error';
		$messages .= 'Please fill in all fields<br>';
	}
	if ($email != $email_confirm) {
		$result = 'error';
		$messages .= 'Email entered must be the same as the one in reset link<br>';
	}
	if ($pword != $pword_confirm) {
		$result = 'error';
		$messages .= 'Both passwords must match<br>';
	}
	
	// Update DB
	if ( $result != 'error') {
		// Pre-process data
		$email_safe = $mysql->real_escape_string($email);
		$pword_hash = password_hash($pword, PASSWORD_DEFAULT);
		
		// Add to DB
		$query = "UPDATE user SET pword='$pword_hash' WHERE email='$email_safe'";		
		$user = $mysql->query($query);

		if ($user == TRUE) {
			$messages .= 'Password updated successfully!<br>';
		} else {
			$result = 'error';
			$messages .= 'Failed to update password.<br>';
			$messages .= 'Error: '.$mysql->error.'<br>';
		}
	}
	
	header('Location: result.php?result='.$messages);	
}
?>