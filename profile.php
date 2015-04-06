<?php

load_profile();

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
	
	$profile = get_profile($_GET['profile_id']);
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
		echo "Name  : " . $profile['firstname']." ".$profile['lastname'] . "<br/>";
		echo "Email : " . $user['email'] . "<br/>";
		echo "Age	: " . $profile['age'] . "<br/>";
		echo "<br/>";
		
		// Only show my public key if the person viewing my profile
		// is logged in AND is on my access list
		if ( isset($_SESSION['current_uid']) ) {
			$access_list = get_access_list($user['user_id']);
			if ( $my_profile || 
					(!$my_profile && in_array($_SESSION['current_uid'],$access_list)) )  {
				echo "Public key: " . $profile['public_key']."<br/>";
			}
		}
		
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
			
			// Upload public key form
			echo "
			<br>
			<form action='settings.php' method='POST'>
				<input type='submit' class='button' value='Settings' name='load_settings'><br/>
			</form>
			";
		}
	
		include('footer.php');
	} else {
		$message = "Profile doesn't exit<br>";
		header('Location: result.php?result='.$message);
	}
}

?>