<?php

include('header.php');

// User not logged in
if ( !isset($_SESSION['current_uid']) ) {
	$message = "User is not logged in<br>";
	header('Location: result.php?result='.$message);
}

// Has to be coming from the settings
// button in the profile page
if (isset($_POST['update_info'])) {
	update_info();
} else if (isset($_POST['update_access'])) {
	update_access();
} else {
	load_settings();
}

function load_settings() {	
	$profile = get_profile($_SESSION['profile_id']);
	$fname = $profile['firstname'];
	$lname = $profile['lastname'];
	$age = $profile['age'];
	
	echo "<h3>Update your profile information:</h3>";
	echo "
	<form action='settings.php' method='POST'>
		<p>First Name:</p>
		<p><input type='text' name='fname' value='$fname'></p>
		<p>Last Name:</p>
		<p><input type='text' name='lname' value='$lname'></p>
		<p>Age:</p>
		<p><input type='text' name='age' value='$age'></p>
		<p><input type='submit' class='button' value='Update Info' name='update_info'></p>
	</form><br>
	";
	
	$profiles_list = get_profiles_list();
	$access_list = get_access_list();
	
	echo "<h3>Update which users are allowed to view your public key:</h3>";
	echo "<form action='settings.php' method='POST'>";
	foreach ($profiles_list as $p) {
		$id = $p['id'];
		$name = $p['name'];
		$c = '';
		if ( in_array($id,$access_list) )  {
			$c = 'checked';
		}
		
		if ( $id != $_SESSION['current_uid'] ) {
			echo "<p><input type='checkbox' name='access[]' value='$id' $c>$name</p>";
		}
	}
	echo "	<input type='submit' class='button' value='Update User Access' name='update_access'>";
	echo "</form><br>";
}

function update_info() {
	// Get info
	$profile_id = $_SESSION['profile_id'];
	$fname = ($_POST['fname'] ? real_escape_string($_POST['fname']) : 'NULL');
	$lname = ($_POST['lname'] ? real_escape_string($_POST['lname']) : 'NULL');
	$age = ($_POST['age'] ? real_escape_string($_POST['age']) : 'NULL');

	// Connect to DB
	$mysql = connectdb();
	
	// Update DB
	$query = "UPDATE profile SET firstname='$fname', lastname='$lname', age=$age WHERE profile_id='$profile_id'";
	$result = $mysql->query($query);
	
	if ( $result ) {
		$message = 'Your profile was updated successfully!<br>';
	} else {
		$message = 'Failed to update profile.<br>';
	}
	
	$mysql->close();
	
	// Render result.php
	header('Location: result.php?result='.$message);
}

function update_access() {
	// Connect to DB
	$mysql = connectdb();
	
	// Get users currently accessing profile
	$access_list = get_access_list();
	$my_id = $_SESSION['current_uid'];
	
	$added = 0;
	// if new id checked but not in access list, add it
	if ( !empty($_POST['access']) ) {
		foreach($_POST['access'] as $a) {
			if ( !in_array($a,$access_list) ) {
				// Update DB
				$query = "INSERT INTO lab1.user_access (owner_id, viewer_id) VALUES ('$my_id', '$a')";
				$result = $mysql->query($query);
				if ( $result ) {
					$added++;
				}
			}
		}
	}
	
	// if id in access_list but not checked, delete it
	$removed = 0;
	foreach ($access_list as $a) {
		if ( !in_array($a,$_POST['access']) ) { 
			// Update DB
			$query = "DELETE FROM lab1.user_access WHERE owner_id='$my_id' AND viewer_id='$a'";
			$result = $mysql->query($query);
			if ( $result ) {
				$removed++;
			}
		}
	}
		
	$mysql->close();
				
	// Render result.php
	$message = 'Added '.$added.' and removed '.$removed.' users from accessing your profile!<br>';
	header('Location: result.php?result='.$message);
}

include('footer.php');
	
?>