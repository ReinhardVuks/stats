<?php
session_start();
?>

<html>
<head>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="css/styleProfile.css">
</head>
<body>
	<div class="container">
		<div class="row">
			<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3"></div>
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" style="height: 100%">
				<div style="height: 300px" id="content">
					<?php 
						if(!isset($_SESSION['username'])){
							echo '<a href="login.php">PLEASE LOG-IN</a>';
						} else {
					?>	
					<a href="main.php">CREATE NEW GAME</a>
					<a href="mygames.php">MY GAMES</a>
					<a href="logout.php">LOG OUT</a>
				</div>

			</div>
			<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3"></div>

		</div>
	</div>
</body>
</html>

<?php } ?>