<?php

/////////////////////////
// INCLUDES
/////////////////////////
require('password_compat\lib\password.php');
require('swiftmailer\lib\swift_required.php');

/////////////////////////
// DEFAULTS
/////////////////////////
date_default_timezone_set('Canada/Eastern');

/////////////////////////
// UTILITY FUNCTIONS
/////////////////////////
function connectdb() {
	$db_host = 'localhost';
	$db_user = 'root';
	$db_pass = 'root';
	$db_name = 'lab1';

	// Check and create connection
	$mysql = new mysqli($db_host, $db_user, $db_pass, $db_name);
	if ( $mysql->connect_error ) {
		die("Connection to database failed: " . $mysql->connect_error);
	}
	echo "Connected to database successfully!<br>";

	return $mysql;
}

function send_email($body, $email_to) {
	$email_host = 'smtp.gmail.com';
	$email_port = 465;
	$email_secu = "ssl";
	$email_user = 'neid.1993@gmail.com';
	$email_pass = 'temppassword';

	$transport = Swift_SmtpTransport::newInstance($email_host, $email_port, $email_secu)
					->setUsername($email_user)
					// TODO change password before demo
					->setPassword($email_pass);
	$mailer    = Swift_Mailer::newInstance($transport);
	$message   = Swift_Message::newInstance()
					->setSubject($email_user)
					->setFrom(array($email_user => 'Naglaa'))
					->setTo(array($email_to => $email_to))
					->setBody($body)
					->addPart($body, 'text/html');
	
	$result = $mailer->send($message);
	
	return $result;
}

function print_messages($result) {
	echo $result;
	if ( is_array($result) && array_key_exists('messages', $result) ) {
		var_dump($result['messages']);
		foreach ($result['messages'] as $msg) {
			echo $msg."<br>";
		}
	}
}

?>