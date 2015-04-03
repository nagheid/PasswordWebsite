<?php

set_include_path('phpseclib');
include('Crypt/RSA.php');
include('Crypt/AES.php');
include('utils.php');

if (isset($_POST['upload_public_key'])) {
	session_start();
	upload_public_key();
}

//////////////////////////////////
// WEB FUNCTIONS
//////////////////////////////////

function upload_public_key() {
	// Get profile_id
	$profile_id = $_SESSION['profile_id'];
	
	// htmlspecialchars to espace symbols in the public key
	// urlencode to process new lines
	//$public_key = htmlspecialchars(urlencode($_POST['public_key']));
	$public_key = $_POST['public_key'];
	
	// Enter into DB
	$mysql = connectdb();
	$pkey_safe = $mysql->real_escape_string($public_key);
	$query = "UPDATE profile SET public_key='$pkey_safe' WHERE profile_id='$profile_id'";		
	$profile = $mysql->query($query);

	if ( $profile ) {
		$message = 'Public key uploaded successfully!<br>';
	} else {
		$message = 'Failed to upload public key<br>';
	}
	
	// Close connection to DB
	$mysql->close();
	
	// Results page
	header('Location: result.php?result='.$message);
}

//////////////////////////////////
// CMD LINE FUNCTIONS
//////////////////////////////////

function create_rsa_keys() {
	// Create RSA keys
	$rsa = new Crypt_RSA();

	// Extract $privatekey & $publicKey
	extract($rsa->createKey());
	
	echo "Generating RSA keys...\n";
	echo "----------------------\n";
	echo "Private Key:\n";
	echo $privatekey;
	echo "\n\n";
	echo "Public Key:\n";
	echo $publickey;
	echo "\n\n";
	
	return array($privatekey, $publickey);
}

function sign($fname, $snd_privatekey) {
	// Load RSA key
	$rsa = new Crypt_RSA();
	$rsa->loadKey($snd_privatekey); // private key

	// Read file
	//$fname = $_FILES['fileToSend']['tmp_name'];
	$fdata = file_get_contents($fname);
	
	// Sign file - PSS is more secure
	$rsa->setSignatureMode(CRYPT_RSA_SIGNATURE_PSS);
	$signature = $rsa->sign($fdata);
	$signed_file = base64_encode($signature);

	echo "Signed file:\n".$signature."\n\n";
	echo "Encoded data:\n".$signed_file."\n\n";
	
	return $signature;
	//return $signed_file;
}

function verify($dec_file, $signed_file, $snd_publickey) {
	// Load RSA key
	$rsa = new Crypt_RSA();
	$rsa->loadKey($snd_publickey); // public key

	// Read data
	//$vdata = base64_decode($signed_file);
	
	echo "Signed file:\n".$signed_file."\n\n";
	echo "Decrypted file:\n".$dec_file."\n\n";
	
	// Verify file
	if ( $rsa->verify($dec_file, $signed_file) ) {
		echo 'Verified!\n';
		return true;
	} else {
		echo 'Unverified\n';
		return false;
	}

}

function create_symm_key() {
	// Create symmetric key
	$key = openssl_random_pseudo_bytes(256);
	
	return $key;
}

/*
 * Encrypts the file using public key AES CTR
 */
function enc_file($data, $key) {
	// AES Encryption
	$aes = new Crypt_AES(CRYPT_AES_MODE_CTR);
	$aes->setKey($key);
	
	$enc_data = $aes->encrypt($data);
	//$enc_data = base64_encode($enc_data);
	
	echo "Encrypted file:\n".$enc_data."\n\n";
	
	return $enc_data;
}

/*
 * Encrypts the key using RSA
 */
function enc_key($data, $key) {
	// RSA Encryption
	$rsa = new Crypt_RSA();
	$rsa->loadKey($key);
	
	$enc_data = $rsa->encrypt($data);
	//$enc_data = base64_encode($enc_data);
	
	echo "Encrypted key:\n".$enc_data."\n\n";
	
	return $enc_data;
}

function sign_n_encrypt($fname, $snd_privatekey, $rcv_publickey) {
	// Hash file and sign with private key
	echo "Signing original file...\n";
	echo "------------------------\n";
	$signed_file = sign($fname, $snd_privatekey);
	
	// Generate symm key and encrypt file
	echo "Encrypting original file...\n";
	echo "---------------------------\n";
	$symm_key = create_symm_key();
	$enc_file = enc_file(file_get_contents($fname), $symm_key);

	// Encrypt symmm key with receiver's public key
	echo "Encrypting symmetric key...\n";
	echo "---------------------------\n";
	$enc_symm  = enc_key($symm_key, $rcv_publickey);
	
	return array($signed_file, $enc_file, $enc_symm);
}

/*
 * Decrypts the file using public key AES CTR
 */
function dec_file($data, $key) {
	// AES Decryption
	$aes = new Crypt_AES(CRYPT_AES_MODE_CTR);
	$aes->setKey($key);
	
	$dec_data = $aes->decrypt($data);
	
	// When decoded, will get back original encrypted data
	echo "Decrypted file:\n".$dec_data."\n\n";
	
	return $dec_data;
}

/*
 * Decrypts the symmetric key using RSA
 */
function dec_key($data, $key) {
	// AES Decryption
	$rsa = new Crypt_RSA();
	$rsa->loadKey($key);
	
	$dec_data = $rsa->decrypt($data);
	
	// When decoded, will get back original encrypted data
	echo "Decrypted key:\n".$dec_data."\n\n";
	
	return $dec_data;
}

// BOB
function decrypt_n_verify($signed_file, $enc_file, $enc_symm, $snd_publickey, $rcv_privatekey) {
	// Decrypts symm key
	echo "Decrypting symmetric key...\n";
	echo "---------------------------\n";
	$dec_key = dec_key($enc_symm, $rcv_privatekey);
	
	// Decrypt file
	echo "Decrypting original file...\n";
	echo "---------------------------\n";
	$dec_file = dec_file($enc_file, $dec_key);
	
	// Verify signed file
	echo "Verifying signed file...\n";
	echo "------------------------\n";
	$verified = verify($dec_file, $signed_file, $snd_publickey);
}

/*
function send_data($snd_name, $rcv_email, $signed_file, $enc_file, $enc_symm) {
	$body = "Hello,<br>".
			$snd_name." would like to share with you the following data:<br>".
			"<b>Signed File:</b><br>".
			$signed_file."<br><br>".
			"<b>Encrypted File:</b><br>".
			$enc_file."<br><br>".
			"<b>Encrypted key:</b><br>".
			$enc_symm."<br><br>".
			"Thank you!";
	return send_email($body, $rcv_email);
}
*/
?>