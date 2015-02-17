<?php
include('header.php');
?>
	
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

<?php
include('footer.php');
?>