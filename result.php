<?php
include('header.php');
?>

<div class="container">
	<p>
	<?php
		//echo $_SESSION['message'];
		$result = $_GET['result'];
		print_messages($result);
	?>
	</p>
</div>
	
<?php
include('footer.php');
?>