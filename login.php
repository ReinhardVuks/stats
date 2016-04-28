<?php
session_start();
?>

<html ng-app="loginApp" ng-cloak>
<head>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
<link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
	<div class="container" ng-app ng-controller="MyCtrl" >
		<div class="row">
			<div class="col-md-4"></div>

			<div class="col-md-4" style="height: 100%">

				<div class="" id="main">

					<form ng-show="!registerOn" class="form-inline" role="form" action="login_action.php" method="post">
						<div class="form-group">
							<label for="user">Username</label>
							<input type="text" class="form-control" id="user" name="username">
						</div>
						<div class="form-group">
							<label for="password">Password</label>
							<input type="password" class="form-control" id="password" name="password">
						</div><br><br>
						<input type="submit" class="button btn-primary" id="submit" value="Enter"/>
						<button class="button btn-success" id="register" ng-click="registerOn = true" ng-show="!registerOn">New User</button>
					
					</form>

					<form ng-show="registerOn" class="form-inline" role="form" action="register_action.php" method="post">
						<div class="form-group">
							<label for="registerUser">E-Mail:</label>
							<input type="text" class="form-control" id="registerUser" name="registerUser">
						</div>
						<div class="form-group">
							<label for="registerPassword">Password:</label>
							<input type="password" class="form-control" id="registerPassword" name="registerPassword">
						</div>
						<div class="form-group">
							<label for="registerPasswordConfirm">Confirm:</label>
							<input type="password" class="form-control" id="registerPasswordConfirm" name="registerPasswordConfirm">
						</div>
						<input type="submit" class="button btn-primary" id="registerSubmit" value="Register"/>
						<a href="" ng-show="registerOn" ng-click="registerOn = false">Back</a>
					</form>

				</div>

			</div>

			<div class="col-md-4"></div>

		</div>
	</div>
	<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.9/angular.min.js"></script>
	<script src="js/loginApp.js"></script>
</body>
</html>