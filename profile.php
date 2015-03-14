<?php

load_profile();

function get_profile() {
	// Create connection
	$mysql = connectdb();
	
	// Retrieve info
	// TODO should not be set if user logged out
	$profile_id = $_GET['profile_id'];
	$pid_safe = $mysql->real_escape_string($profile_id);
	
	// Get profile from db
	$stmnt = "SELECT * FROM profile WHERE profile.profile_id ='$pid_safe'";
	$row = $mysql->query($stmnt);
	$profile = $row->fetch_array();
	
	return $profile;
}

function get_user($user_id) {
	// Create connection
	$mysql = connectdb();
	
	// Retrieve info
	$uid_safe = $mysql->real_escape_string($user_id);
	
	// Get user from db
	$stmnt = "SELECT * FROM user WHERE user.user_id ='$uid_safe'";
	$row = $mysql->query($stmnt);
	$user = $row->fetch_array();
	
	return $user;
}

function load_profile() {
	include('header.php');
	
	$profile = get_profile();
	$user = get_user($profile['user_id']);

	if ( $profile ) {
		$my_profile = false;
		if ( isset($_SESSION['current_uid']) && 
			 $_SESSION['current_uid'] == $profile['user_id'] ) {
			$my_profile = true;
		}
		
		if ( $my_profile ) {
			echo "Welcome, ".$profile['firstname']. "!<br/><br/>";
		} else {
			echo $profile['firstname']. "'s profile!<br/><br/>";
		}
		
		// Load first and last name
		echo "Name: " . $profile['firstname']." ".$profile['lastname'] . "<br/>";
		echo "Email: " . $user['email'] . "<br/>";
		
		echo "Public key: " . $profile['public_key']."<br/>";
		
		echo "<br/>";
		
		if ( $my_profile) {
			echo "<hr>";
		
			// Upload public key form
			echo "
			<form action='security.php' method='POST'>
				<input type='text' name='public_key' required>
				<input type='submit' class='button' value='Upload Public Key' name='upload_public_key'><br/>
			</form>
			";
			/*
			// Create key pair form
			echo "
			<form action='security.php' method='POST'>
				<input type='submit' class='button' value='Create Key Pair' name='create_key_pair'><br/>
			</form>
			";
			
			echo "<hr>";
			
			// Upload & sign a file
			echo "
			<form action='security.php' method='POST' enctype='multipart/form-data'>
				<!--<input type='text' name='plaintext'>-->
				Please upload a text file:
				<input type='file' name='fileToSend' id='fileToSend' accept='.txt'>
				<input type='submit' class='button' value='Sign File' name='sign_file'><br/>
				<p>Note: this will sign the file using your private key</p>
	
			</form>
			";
			
			echo "<hr>";
			
			// Generate symmetric key & encrypt file
			echo "
			<form action='security.php' method='POST'>
				<input type='submit' class='button' value='Encrypt File' name='encrypt_file'><br/>
				<p>Note: this will generate a symmetric key to encrypt the file</p>
			</form>
			";
			
			echo "<hr>";
			
			// Encrypt symm key
			echo "
			<form action='security.php' method='POST'>
				Please enter receiver's public key:
				<input type='text' name='rcv_key' required>
				<input type='submit' class='button' value='Encrypt Symmetric Key' name='encrypt_symm_key'><br/><br/>
			</form>
			";
			
			echo "<hr>";
			
			// Send file
			echo "
			<form action='security.php' method='POST'>
				Please enter receiver's enail:
				<input type='text' name='rcv_email' required>
				<input type='submit' class='button' value='Send Data' name='send_data'><br/>
				<p>Note: this will send the signed hash, encrypted symmetric key and encrypted file.</p>
			</form>
			";
			*/
		}
	
		include('footer.php');
	} else {
		$message = "Profile doesn't exit<br>";
		header('Location: result.php?result='.$message);
	}
}

?>