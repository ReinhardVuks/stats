<?php
session_start();
include_once 'common.php';
?>

<html ng-app="loginApp">
<head>

	<meta charset="utf-8" />
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<style type="text/css">
        [ng\:cloak], [ng-cloak], [data-ng-cloak], [x-ng-cloak], .ng-cloak, .x-ng-cloak {
        display: none !important;
    }       
    </style> 
</head>
<body ng-cloak>
	<div class="container" ng-app ng-controller="MyCtrl" >
		<?php
		if(isset($_GET['error'])){
			if($_GET['error'] == "rf"){
				echo "<div ng-init='registerOn = true'></div>";
			}
		}
		?>
		<div ng-init=""></div>
		<div class="row">
			<div class="col-md-4">
				<div id="languages">
				<a href="index.php?lang=en"><img src="images/eng.png" /></a>
				<a href="index.php?lang=ee"><img src="images/est.jpg" /></a>
				</div>
			</div>

			<div class="col-md-4" style="padding-top: 120px; height: 100%">

				<div class="" id="main">

					<form ng-show="!registerOn" class="form-inline" role="form" action="login_action.php" method="post">
						
							<?php
							if(isset($_GET['error'])){
								if($_GET['error'] == "lf")
									echo "<a style='color:red'>".$lang["INVALID_LOGIN"]."</a>";
							}
							?>
						<div class="form-group" style="width: 100%;">
							<label for="user"><?php echo $lang['USERNAME']; ?></label>
							<input type="text" class="form-control pull-right" id="user" name="username" required>
						</div>
						<div class="form-group" style="width: 100%;">
							<label for="password"><?php echo $lang['PASSWORD']; ?></label>
							<input type="password" class="form-control pull-right" id="password" name="password" required>
						</div>
						<div style="display: block; text-align: right;">
						<input type="submit" class="btn btn-primary" id="submit" value="<?php echo $lang['ENTER']; ?>"/>
						<button type="button" class="btn btn-success" id="register" ng-click="registerOn = true" ng-show="!registerOn"><?php echo $lang['NEW_USER']; ?></button>
						</div>
					</form>

					<form ng-show="registerOn" class="form-inline" role="form" action="register_action.php" method="post">
						<?php
							if(isset($_GET['error'])){
								if($_GET['error'] == "rf")
									echo "<a style='color:red'>".$lang["INVALID_REGISTRATION"]."</a>";
							}
							?>
						<div class="form-group" style="width: 100%;">
							<label for="registerUser"><?php echo $lang['USERNAME']; ?></label>
							<input type="text" class="form-control pull-right" id="registerUser" name="registerUser" required>
						</div>
						<div class="form-group" style="width: 100%;">
							<label for="registerPassword"><?php echo $lang['PASSWORD']; ?></label>
							<input type="password" class="form-control pull-right" id="registerPassword" name="registerPassword" required>
						</div>
						<div class="form-group" style="width: 100%;">
							<label for="registerPasswordConfirm"><?php echo $lang['REPEAT_PASSOWRD']; ?></label>
							<input type="password" class="form-control pull-right" id="registerPasswordConfirm" name="registerPasswordConfirm" required>
						</div>
						<div style="display: block; text-align: right;">
						<input type="submit" class="btn btn-primary" id="registerSubmit" value="<?php echo $lang['REGISTER']; ?>"/>
						<button type="button" class="btn btn-danger" ng-show="registerOn" ng-click="registerOn = false"><?php echo $lang['BACK']; ?></button>
						</div>
					</form>

				</div>

			</div>

			<div class="col-md-4"></div>

		</div>

	</div>
	<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.9/angular.min.js"></script>
	
	<script src="js/loginApp.js"></script>
	<script type="text/javascript">
		window.onload = function () {
			document.getElementById("registerPassword").onchange = validatePassword;
			document.getElementById("registerPasswordConfirm").onchange = validatePassword;
		}
		function validatePassword(){
			var pass2=document.getElementById("registerPasswordConfirm").value;
			var pass1=document.getElementById("registerPassword").value;
			if(pass1!=pass2)
				document.getElementById("registerPasswordConfirm").setCustomValidity('<?php echo $lang["PW_MATCH"]?>');
			else
				document.getElementById("registerPasswordConfirm").setCustomValidity('');
		}
	</script>
	<footer>
		<div>
			<span>Developed by: Alfred-Reinhard Vuks</span>
			<span>University of Tartu</span>
			<span>2016</span>
		</div>	
	</footer>
</body>
</html>