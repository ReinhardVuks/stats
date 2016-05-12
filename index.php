<?php
session_start();
include_once 'common.php';

?>

<html>
<head>
	<meta charset="utf-8" />
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="css/styleProfile.css">
</head>
<body>
	<div class="container">
		<div class="row">
			<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
				<div id="languages">
				<a href="index.php?lang=en"><img src="images/eng.png" /></a>
				<a href="index.php?lang=ee"><img src="images/est.jpg" /></a>
				</div>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" style="height: 100%">
				<div style="height: 300px" id="content">
					<?php 
						if(!isset($_SESSION['username'])){
							echo '<a href="login.php">'. $lang['LOG_IN']. '</a>';
						} else {
					?>	
					<a href="main.php"><?php echo $lang['NEW_GAME']; ?></a>
					<a href="mygames.php"><?php echo $lang['MY_GAMES']; ?></a>
					<a href="tutorial.php"><?php echo $lang['TUTORIAL'];?></a>
					<a href="logout.php"><?php echo $lang['LOG_OUT']; ?></a>
				</div>

			</div>
			<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3"></div>

		</div>
	</div>
</body>
</html>

<?php } ?>