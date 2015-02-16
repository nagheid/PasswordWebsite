<!DOCTYPE html>
<html lang="en">

<head>
	<!-- META -->
	<title>CSI4139 Labs</title>
	<meta charset="utf-8">
	
	<!-- LINK BOOTSTRAP -->
	<link 	rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
	<script src="https://code.jquery.com/jquery.js"></script>
	<script src="bootstrap/js/bootstrap.min.js"></script>
	
	<!-- CUSTOM -->
	<!--<link rel="icon" href="../../key.ico">-->
	 <link rel="stylesheet" href="stylesheet.css">
</head>

<body>
	<nav id="header" class="navbar navbar-inverse navbar-fixed-top">
		<div class="container">
			<div class="navbar-header">
				<a class="navbar-brand" href="#">CSI4139 Labs</a>
			</div>
		</div>
	</nav>
	
	<div id="intro" class="container">
		</br></br>
		<h1>Lab 1</h1>
		</br></br>
	</div>
	
	<div id="columns" class="container">
		<form action="changepw.php" method="POST">
			<h2>Change passoword</h2>
			<p>Please enter a new password</p>
			<input type="hidden" name="email" value="<?php echo $_GET['email']?>">
			<label>Re-enter Email</label><br/>
			<input type="email" name="email_confirm"><br/>
			<label>Password: </label><br/>
			<input type="password" name="password"><br/>
			<label>Confirm Password: </label><br/>
			<input type="password" name="password_confirm"><br/><br/>
			<input type="submit" value="Change password"><br/><br/>
		</form>
	</div>

</body>
</html>