<?php
session_start();

include "functions.php";

?>

<html ng-app="myGamesApp" ng-cloak>
<head>
	<script type="text/javascript" src="js/jquery-2.2.3.min.js"></script>
  <script type="text/javascript" src="http://netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.2.61/jspdf.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/2.0.16/jspdf.plugin.autotable.js"></script>
  <script type="text/javascript" src="js/tableToPDF.js"></script>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="css/styleMyGames.css">
</head>
<body>
	<div class="container" ng-app ng-controller="Ctrl1">
		<?php 
		$myGames = getGames((int)getUserId($_SESSION['username']));
		for ($i=0; $i < count($myGames); $i++) { 
			?>

				<div ng-init="addGame('<?php echo $myGames[$i]['game_nr']; ?>', '<?php echo $myGames[$i]['creation_date'] ?>','<?php echo $myGames[$i]['home_team'] ?>','<?php echo $myGames[$i]['away_team'] ?>')"></div>

			<?php

		}
	?> 
		<div class="row">
			<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3"></div>
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" style="height: 100%">
				<div id="mainTable">
					<table class="table table-bordered table-condensed table-responsive">
						<tr>
							<th>Nr</th>
							<th>Date</th>
							<th>Box-Score</th>
							<th>Game Log</th>
						</tr>
						<tr ng-repeat="game in myGames">

							<td>{{game.GameNr}}</td>
							<td>{{game.GameDate}}</td>
							<td><button type="button" ng-click="createBoxScore(game.GameNr)" class="btn">Box-Score PDF</button></td>
							<td></td>
						</tr>	
					</table>
				</div>
			</div>
			<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3"></div>

		</div>
	</div>
	<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular.min.js"></script>
  	<script src="js/myGames.js"></script>
</body>
</html>