<?php
session_start();	

?>

<html ng-app="selectionApp" ng-cloak>
<head>
	<script type="text/javascript" src="js/jquery-2.2.3.min.js"></script>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="css/styleMain.css">
</head>
<body>

<div class="container" ng-app ng-controller="MyCtrl" >
		<div class="row">
			<div class="col-md-3"></div>

			<div class="col-md-6" style="height: 100%">

				<div id="selectSport" ng-show="!selectionDone">

					<div class="btn-group buttonSport">
						<h1 class="h1">SELECT SPORT</h1>
						<button class="btn btn-info btn-lg" ng-click="selectSport(1)">BASKETBALL</button>
						<button class="btn btn-info btn-lg" ng-click="selectSport(2)">FOOTBALL</button>
						<button class="btn btn-info btn-lg" ng-click="selectSport(3)">VOLLEYBALL</button>
					</div>

				</div>

				<form class="form-inline" role="form" action="game_bothTeams.php" method="post">
				<div id="selectStats" ng-show="selectionDone && !showTeamList">
					<p>Choose statistical elements you want to keep track of: </p>

				<ul ng-repeat="item in basketballStats">
            		<ul ng-repeat="(element, value) in item">
              			<ul>{{element}} <span ng-click="showGame = false" ng-show="showGame" class="glyphicon glyphicon-menu-up"></span><span ng-hide="showGame" ng-click="showGame = true" class="glyphicon glyphicon-menu-down"></span></ul>
			              	<table class="table table-bordered table-striped table-condensed" ng-show="showGame">
	            			    <tr ng-repeat="(piece, pieceVal) in value">
	                  				<td>
	                					<div class="input-group checkbox">
	                  						<label>
		                    					<input type="checkbox" name="stats['{{piece}}']" value="false" checked hidden>
												<input type="checkbox" name="stats['{{piece}}']" value="true" checked> {{piece}}
	                  						</label>
	                					</div>
	                				</td>
	                			</tr>
                			</table>
              		</ul>
            	</ul>
            	<div id="advSettings">
            	<span><p>Additional Settings</p><span ng-click="showSettings = false" ng-show="showSettings" class="glyphicon glyphicon-menu-up"></span><span ng-hide="showSettings" ng-click="showSettings = true" class="glyphicon glyphicon-menu-down"></span></span>
            	<table class="table table-bordered table-striped table-condensed" ng-show="showSettings">
            		<tr>
            			<td>
            				<label for="periodLength">Period Length (minutes)</label>
							<input type="number" min="1" name="periodLength" id="periodLength" value="10">
            			</td>
            		</tr>
            		<tr>
            			<td>
            				<label for="numberOfPeriods">Number of periods</label>
							<input type="number" min="1" name="numberOfPeriods" id="numberOfPeriods" value="4">
            			</td>
            		</tr>
            		<tr>
            			<td>
            				<label for="playersOnCourt">Maximum number of players on court</label>
							<input type="number" min="1" name="playersOnCourt" id="playersOnCourt" value="5">
            			</td>
            		</tr>
            		<tr>
            			<td>
            				<label for="maxFouls">Maximum number of personal fouls</label>
							<input type="number" id="maxFouls" name="maxFouls" min="1" value="5">
            			</td>
            		</tr>
            	</table>
            	</div>
            	<div>
						<button type="button" class="btn btn-danger col-lg-6" ng-click="selectionDone = false;">Back</button>
						<button type="button" class="btn btn-success col-lg-6" ng-click="showTeamList = true">Next</button>
            	</div>

				</div>

<!--
            	<ul ng-repeat="item in basketballStats">
							<ul ng-repeat="(element, value) in item">
								<ul>{{element}}</ul>
								<ul ng-repeat="(piece, pieceVal) in value">
									{{piece}} 
									<input type="checkbox" name="{{piece}}" value="false" checked hidden>
									<input type="checkbox" name="{{piece}}" value="true">
								</ul>
							</ul>
						</ul>
-->
				<div id="selectPlayers" ng-show="showTeamList">
						<div class="form-group">
							<div class="radio">
								<label><input type="radio" name="teamSelection" value="Home" ng-model="selectedTeam">Home</label>
								<label><input type="radio" name="teamSelection" value="Away" ng-model="selectedTeam">Away</label>
							</div>
						</div>
						<div class="form-group">
      						<label for="nr">#</label>
					    	<input id="numberInput" maxlength="3" type="text" ng-keydown="myFunct($event, selectedTeam, Name, Nr)" class="form-control" id="nr" placeholder="#" ng-model="Nr">
      						<label for="name">Name</label>
      						<input id="inputName" type="text" class="form-control" id="name" placeholder="Nimi" ng-model="Name">
    					</div>
    					<div id="file_upload">
							<label for="away_upload" id="selected_files_away" class="btn btn-info pull-right">
				    			Select Away File
							</label>
							<input name="away_upload" id="away_upload" type="file" on-read-file="addTeamFromFile($fileContent, 'Away')" class="pull-right"/>
    						<label for="home_upload" id="selected_files_home" class="btn btn-info pull-right">
				    			Select Home File
							</label>
							<input name="home_upload" id="home_upload" type="file" on-read-file="addTeamFromFile($fileContent, 'Home')" class="pull-right"/>

							<span id="info_icon" ng-mouseover="showInfo = true" ng-mouseleave="showInfo = false" class="glyphicon glyphicon-info-sign pull-right" ></span>
    						<div id="info_content" ng-show="showInfo">You can add a team(s) from ".txt" file.<br><br>Example:<br>Team1 Name<br>Player1, 1<br>Player2, 2<br>...</div>
    					</div>
    			<button type="button" ng-click="newPlayer(selectedTeam, Name, Nr)" class="btn btn-primary" onClick='document.getElementById("inputName").focus();'>ADD PLAYER</button>
				

				<table class="table table-bordered table-responsive">
					<tr>
						<td colspan="2">
							<span  ng-click="showEditHome = true" ng-hide="showEditHome">{{homeTeamName}}</span>
							<input type="text" ng-show="showEditHome" ng-model="homeTeamName" value="{{homeTeamName}}" class="form-control"/>
							<button type="button" ng-show="showEditHome" ng-click="showEditHome = false">Save</button>
							<span style="font-size: 10px;" class="pull-right">Click on team name to change it!</span>
						</td>
					</tr>
					<tr>
						<td class="col-md-1">#</td>
						<td class="col-md-11">NAME</td>
					</tr>
					<tr ng-repeat="player in team['Home'] | orderBy:'Nr'" ng-mouseenter="showRemove=true" ng-mouseleave="showRemove=false">
						<input type="hidden" name="home[]" value="{{player.Name}}, {{player.Nr}}">
						<td>
							{{player.Nr}}
						</td>
						<td>
							{{player.Name}}
							<span class="glyphicon glyphicon-remove pull-right" ng-show="showRemove" ng-click="removePlayer(player.Nr, 'Home')"></span>
						</td>
					</tr>
				</table>
				<table class="table table-bordered table-responsive">
					<tr>
						<td colspan="2">
							<span  ng-click="showEditAway = true" ng-hide="showEditAway">{{awayTeamName}}</span>
							<input type="text" ng-show="showEditAway" ng-model="awayTeamName" value="{{awayTeamName}}" class="form-control"/>
							<button type="button" ng-show="showEditAway" ng-click="showEditAway = false">Save</button>
							<span style="font-size: 10px;" class="pull-right">Click on team name to change it!</span>
						</td>
					</tr>
					<tr>
						<td class="col-md-1">#</td>
						<td class="col-md-11">NAME</td>
					</tr>
					<tr ng-repeat="player in team['Away'] | orderBy:'Nr'" ng-mouseenter="showRemove=true" ng-mouseleave="showRemove=false">
						<input type="hidden" name="away[]" value="{{player.Name}}, {{player.Nr}}">
						<td>
							{{player.Nr}}
						</td>
						<td>
							{{player.Name}}
							<span class="glyphicon glyphicon-remove pull-right" ng-show="showRemove" ng-click="removePlayer(player.Nr, 'Away')"></span>
						</td>
					</tr>
				</table>
				<div>
					<button type="button" class="btn btn-danger col-lg-6" ng-click="showTeamList = false; selectionDone = true">Back</button>
					<button class="btn btn-success col-lg-6">Submit Settings</button>
				</div>
				<input type="hidden" name="homeTeamName" value="{{homeTeamName}}">
				<input type="hidden" name="awayTeamName" value="{{awayTeamName}}">
				</div>
				</form>

			<div class="col-md-3"></div>

		</div>
	</div>
</div>
	<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.9/angular.min.js"></script>
	<script src="js/selectionApp.js"></script>
	<script type="text/javascript">

		$("#home_upload").change(function() {
		    var names = [];
		    for (var i = 0; i < $(this).get(0).files.length; ++i) {
		        names.push($(this).get(0).files[i].name);
		        if(i != $(this).get(0).files.length-1)
		        names.push(", ");
		    }
		    $("#selected_files_home").html(names);
		});
		$("#away_upload").change(function() {
		    var names = [];
		    for (var i = 0; i < $(this).get(0).files.length; ++i) {
		        names.push($(this).get(0).files[i].name);
		        if(i != $(this).get(0).files.length-1)
		        names.push(", ");
		    }
		    $("#selected_files_away").html(names);
		});
	</script>
</body>
</html>