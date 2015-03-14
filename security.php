<?php

set_include_path('phpseclib');
include('Crypt/RSA.php');
include('Crypt/AES.php');
include('utils.php');

session_start();

if (isset($_POST['create_key_pair'])) {
	create_key_pair();
} elseif (isset($_POST['sign_file'])) {
	sign_file();
} elseif (isset($_POST['encrypt_file'])) {
	encrypt_file();
} elseif (isset($_POST['upload_public_key'])) {
	upload_public_key();
}

function create_rsa_keys() {
	// Create RSA keys
	$rsa = new Crypt_RSA();

	// Extract $privatekey & $publicKey
	extract($rsa->createKey());
	
	echo "Private Key:\n";
	echo $privatekey;
	echo "\n\n";
	echo "Public Key:\n";
	echo $publickey;
	echo "\n";
	
	$array['private_key'] = $privatekey;
	$array['public_key'] = $publickey;
	
	return $array;
}


function create_key_pair() {
	// Get profile_id
	$profile_id=$_SESSION['profile_id'];
	
	// Create RSA keys
	$rsa = new Crypt_RSA();

	// Extract $privatekey & $publicKey
	extract($rsa->createKey());
	
	// Enter into DB
	$mysql = connectdb();
	$query = "UPDATE profile SET private_key='$privatekey', public_key='$publickey' WHERE profile_id='$profile_id'";		
	$profile = $mysql->query($query);

	if ( $profile ) {
		$message = 'Key pair created successfully!<br>';
	} else {
		$message = 'Failed to create key pair<br>';
	}

	// Close connection to DB
	$mysql->close();
	
	// Results page
	header('Location: result.php?result='.$message);
}

function sign_file() {
	// Get profile_ids
	$profile_id=$_SESSION['profile_id'];
	
	// Get private key
	$mysql = connectdb();
	$query = "SELECT user_id, private_key, public_key FROM profile WHERE profile_id='$profile_id'";		
	$row = $mysql->query($query);
	$profile = $row->fetch_array();
	$privatekey = $profile['private_key'];
	$user_id = $profile['user_id'];
	
	// Load RSA key
	$rsa = new Crypt_RSA();
	$rsa->loadKey($privatekey); // private key

	// Read file
	$fname = $_FILES['fileToSend']['tmp_name'];
	$fdata = file_get_contents($fname);
	
	// Sign file - PSS is more secure
	$rsa->setSignatureMode(CRYPT_RSA_SIGNATURE_PSS);
	$signature = $rsa->sign($fdata);
	$stor_data = base64_encode($signature);
	//file_put_contents('signed_data.txt', $signature);

	// Add plaintext & signature to db
	$query = "INSERT INTO lab1.transfers (sender_id, signed_file) VALUES ('$user_id', '$stor_data')";		
	$trsfr = $mysql->query($query);
	
	if ( $trsfr ) {
		$message = 'File was signed successfully!<br>';
	} else {
		$message = 'Failed to sign file<br>';
	}
	
	
	
	/*
	$query = "SELECT signed_file FROM transfers WHERE transfer_id='2'";		
	$row = $mysql->query($query);
	$data = $row->fetch_array();==
	
	$rsa->loadKey($profile['public_key']); // public key
	$vdata = base64_decode($data['signed_file']);
	echo $rsa->verify($fdata, $vdata) ? 'verified' : 'unverified';
	*/

	// Close connection to DB
	$mysql->close();
	
	// Results page
	header('Location: result.php?result='.$message);
}

function encrypt_file() {
	// Create symmetric key
	$key = openssl_random_pseudo_bytes (256);
	
	// AES Encryption
	$aes = new Crypt_AES(CRYPT_AES_MODE_CTR);
	$aes->setKey($key);
	
	$size = 10*1024;
	
	// Get signed file
	$mysql = connectdb();
	$query = "SELECT signed_file FROM transfers WHERE profile_id='$profile_id'";		
	$row = $mysql->query($query);
	$profile = $row->fetch_array();
	$privatekey = $profile['private_key'];
	
	$aes->encrypt($ptext);
	
	echo $aes->decrypt($ptext);
}


?>