<?php

require('utils.php');

// Render form
singin_form();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	session_start();
	signin();
}


function singin_form() {
	include('header.php');
	echo "
	<div id='columns' class='container'>
		<!-- DOC: post so content will not be written to log file -->
		<form action='signin.php' method='POST'>
			<h2>Sign in</h2>
			<label>Email: </label><br/>
			<input type='email' name='email'><br/>
			<label>Password: </label><br/>
			<input type='password' name='password'><br/><br/>
			<input type='submit' value='Sign in'><br/><br/>
		</form>
		<p>Don't have an account? <a href='signup.html'> Sign up!</a></p>
		<p>Forgot your password? <a href='resetpw.html'> Reset password.</a></p>
	</div>
	";
	include('footer.php');
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
		$verified = $user['verified'];
		if ($verified) {
			$message = $message.'Your email is verified!<br>';
		} else {
			$result = 'error';
			$message = $message.'Please confirm your email before signing in.<br>';
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