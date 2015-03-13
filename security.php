<?php

if (isset($_POST['create_key_pair'])) {
    create_key_pair();
} elseif (isset($_POST['hash_file'])) {
	//hash_file();
}

function create_key_pair() {
	echo "create_key_pair";
	
	$config = array(
		"digest_alg" => "sha512",
		"private_key_bits" => 4096,
		"private_key_type" => OPENSSL_KEYTYPE_RSA,
	);
	
	$path = array (
		"config" => 'C:\Program Files (x86)\Zend\Apache2\conf\openssl.cnf',
	);
	
	// Create private & public keys
	$keys = openssl_pkey_new($config);
	
	while ($msg = openssl_error_string())
		echo $msg . "<br />\n";
	
	// Extract private key
	openssl_pkey_export($keys, $private_key, NULL, $path);
	while ($msg = openssl_error_string())
		echo $msg . "<br />\n";
	
	// Extract public key
	$public_key = openssl_pkey_get_details($keys);
	$public_key = $public_key["key"];
	
	// Enter into DB
	echo "KY: ".$keys."<br>";
	echo "PR: ".$private_key."<br>";
	echo "PB: ".$public_key."<br>";
}


function sign_hash() {
	echo "sign_hash";
}

function create_symm_key() {
	echo "create_symm_key";
}

function encrypt_file() {
	echo "encrypt_file";
}


?>