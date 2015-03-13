<?php

load_profile();

function get_profile() {
	// Create connection
	$mysql = connectdb();
	
	// Retrieve info
	$profile_id = $_GET['profile_id'];
	$pid_safe = $mysql->real_escape_string($profile_id);
	
	// Get profile from db
	$stmnt = "SELECT * FROM profile WHERE profile.profile_id ='$pid_safe'";
	$row = $mysql->query($stmnt);
	$profile = $row->fetch_array();
	$user_id = $profile['user_id'];
	
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

function load_profile($my_profile=false) {
	include('header.php');
	
	$profile = get_profile();
	$user = get_user($profile['user_id']);

	if ( isset($_SESSION['current_uid']) && 
		 $_SESSION['current_uid'] = $profile['user_id'] ) {
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
	
	echo "<br/><br/>";
	
	if ( $my_profile) {
		// TODO - Add "Edit" button
		
		// Add "Create key pair" button
		echo "
		<form action='security.php' method='POST'>
			<input type='submit' class='button' value='Create Key Pair' name='create_key_pair'><br/><br/>
		</form>
		";
	}
	
	
	include('footer.php');
}

?>