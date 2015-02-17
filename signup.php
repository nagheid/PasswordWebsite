<?php

require('utils.php');

// Render
singup_form();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	signup();
} else if (!empty($_GET['key'])) {
	confirm_user();
}

function signup() {
	// Initialize errors array
	$result = null;
	$messages = null;
	
	// Create and check connection
	$mysql = connectdb();
	
	// Retrieve data
	$email = $_POST['email'];
	$pword = $_POST['password'];
	
	// Validate data
	if (empty($email)) {
		$result = 'error';
		$messages = $messages.'Please enter an email address<br>';
	}
	if (empty($pword)) {
		$result = 'error';
		$messages = $messages.'Please enter a password<br>';
	}

	if ( $result != 'error') {
		// Pre-process data
		$email_safe = $mysql->real_escape_string($email);
		$pword_hash = password_hash($pword, PASSWORD_DEFAULT);
		
		// Add to DB
		$query = "INSERT INTO user (email, pword) VALUES ('$email_safe', '$pword_hash')";		
		$user = $mysql->query($query);
	
		if ($user == TRUE) {
			$messages = $messages.'User inserted successfully!<br>';
		} else {
			$result = 'error';
			$messages = $messages.'Failed to insert user.<br>';
			$messages = $messages.'Error: '.$mysql->error.'<br>';
		}

		// Verify email
		if ( $user == TRUE ) {
			if( verify_email($email_safe) ){
				//email sent
				$result = 'success';
				$messages = $messages.'Thanks for signing up. Please check your email for confirmation!<br>';
			} else {		 
				$result = 'error';
				$messages = $messages.'Could not send confirmation email<br>';
			}
		}
	} 
	
	// Close connection to DB
	$mysql->close();
	
	// Render result.php
	header('Location: result.php?result='.$messages);
	
}

function verify_email($email) {
	$confirm_key = $email.date('mY');
	$confirm_key = md5($confirm_key);
	
	$link = "https://localhost/PasswordWebsite/signup.php?email=".$email."&key=".$confirm_key;
	
	$body = "Hello, <br><br>" .
			"Please verify your email by clicking on the " .
			"following link: <br>" . $link . "<br><br>" .
			"Thanks";

	$result = send_email($body, $email);

	return $result;
}

function confirm_user() {
	$confirm_key = $_GET['key'];
	$email = $_GET['email'];
	
	// Connect to DB
	$mysql = connectdb();
	
	// Check if already verified
	$query = "SELECT user.verified FROM user WHERE user.email='$email'";
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

function singup_form() {
	include('header.php');
	echo "
	<div id='columns' class='container'
		<form action='signup.php' method='POST'>
			<h2>Sign up</h2>
			<!-- specifying type 'Email' prevents SQL injection -->
			<label>Email</label><br/>
			<input type='email' name='email'><br/>
			<label>Password: </label><br/>
			<input type='password' name='password'><br/><br/>
			<input type='submit' value='Sign up'><br/><br/>
		</form>
	</div>
	";
	include('footer.php');
}
?>