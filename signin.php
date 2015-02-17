<?php

require('utils.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	session_start();
	signin();
}

function signin() {
	$result = null;
	
	// Create connection
	$mysql = connectdb();
	
	// Retrieve data
	$email = $_POST['email'];
	$pword = $_POST['password'];
	
	// Pre-process
	$email_safe = $mysql->real_escape_string($email);
	
	// Select from DB
	$stmnt = "SELECT user.verified, user.pword FROM user WHERE user.email='$email_safe'";
	$row = $mysql->query($stmnt);
	$user = $row->fetch_array();
	$pword_hash = $user['pword'];
	
	if ($user) {
		// Check password
		if (password_verify($pword, $pword_hash)) {
			$message = 'Password is valid!<br>';
		} else {
			$result = 'error';
			$message = 'Invalid password.<br>';
		}
		
		// Check if confirmed email
		if ($result != 'error' ) {
			$verified = $user['verified'];
			if ($verified) {
				$message = $message.'Your email is verified!<br>';
			} else {
				$result = 'error';
				$message = $message.'Please confirm your email before signing in.<br>';
			}
		}
	} else {
		$result = 'error';
		$message = $message.'User not registered in system<br>';
	}
	
	if ( $result != 'error' ) {
		$message = $message.'<br><h1>Signed In!</h1><br>';
	}
	
	// Close connection to DB
	$mysql->close();
	
	// Redirect to "result" page
	header('Location: result.php?result='.$message);
}
?>