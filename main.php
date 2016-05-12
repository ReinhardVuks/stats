<?php
session_start();	

include "functions.php";
include_once 'common.php';
?>

<html ng-app="selectionApp">
<head>
	<meta charset="utf-8" />
	<script type="text/javascript" src="js/jquery-2.2.3.min.js"></script>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="css/styleMain.css">
	<style type="text/css">
        [ng\:cloak], [ng-cloak], [data-ng-cloak], [x-ng-cloak], .ng-cloak, .x-ng-cloak {
        display: none !important;
    }       
    </style> 
</head>
<body ng-cloak>

<div class="container" ng-app ng-controller="MyCtrl" >
		<div class="row">
			<div class="col-md-3">
				<div id="languages" ng-init="setLang('<?php echo $_COOKIE['lang']; ?>')">
				<a href="index.php?lang=en"><img src="images/eng.png" /></a>
				<a href="index.php?lang=ee"><img src="images/est.jpg" /></a>
				</div>
				<div>
					<span class="glyphicon glyphicon-home" style="cursor: pointer; font-size: 25px" onclick="window.location.assign('index.php')"></span>
				</div>
			</div>

			<div class="col-md-6" style="height: 100%">

				<div id="selectSport" ng-show="!selectionDone">

					<h1 class="h1"><?php echo $lang['SELECT_SPORT']; ?></h1>
					<div class="btn-group buttonSport">
						<button class="btn btn-info btn-lg" ng-click="selectSport(1)"><?php echo $lang['BASKETBALL']; ?></button>
						<button class="btn btn-lg" ng-click="selectSport(1)" disabled><?php echo $lang['FOOTBALL']; ?></button>
						<button class="btn btn-lg" ng-click="selectSport(1)" disabled><?php echo $lang['VOLLEYBALL']; ?></button>
					</div>

				</div>

				<form class="form-inline" role="form" action="game.php?nr=<?php echo generateGameNr(); ?>" method="post">
				<div id="selectStats" ng-show="selectionDone && !showTeamList">
					<p><?php echo $lang['CHOOSE_STATS']; ?></p>

				<ul ng-repeat="item in basketballStats">
            		<ul ng-repeat="(element, value) in item">
              			<ul>{{element | translate}} <span ng-click="showGame = false" ng-show="showGame" class="glyphicon glyphicon-menu-up"></span><span ng-hide="showGame" ng-click="showGame = true" class="glyphicon glyphicon-menu-down"></span></ul>
			              	<table class="table table-bordered table-striped table-condensed" ng-show="showGame">
	            			    <tr ng-if="element != 'ADDITIONAL SETTINGS'" ng-repeat="(piece, pieceVal) in value">
	                  				<td>
	                					<div class="input-group checkbox">
	                  						<label>
		                    					<input type="checkbox" name="stats['{{piece}}']" value="false" checked hidden>
												<input type="checkbox" name="stats['{{piece}}']" value="true" checked> {{piece | translate}}
	                  						</label>
	                					</div>
	                				</td>
	                			</tr>
	                			<tr id="advSettings" ng-if="element == 'ADDITIONAL SETTINGS'" ng-repeat="(piece, pieceVal) in value">
	                				<td>
	                					<label for="{{piece}}">{{piece | translate}}</label>
										<input type="number" min="1" name="{{piece}}" id="{{piece}}" value="{{pieceVal}}">
	                				</td>
	                			</tr>
                			</table>
              		</ul>
            	</ul>
            	
                			
            	<div>
						<button type="button" class="btn btn-danger col-lg-6" ng-click="selectionDone = false;"><?php echo $lang['BACK']; ?></button>
						<button type="button" class="btn btn-success col-lg-6" ng-click="showTeamList = true"><?php echo $lang['NEXT']; ?></button>
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
								<label><?php echo $lang['ADD_TO']; ?></label>
								<label><input type="radio" name="teamSelection" value="Home" ng-model="selectedTeam" checked>{{homeTeamName}}</label>
								<label><input type="radio" name="teamSelection" value="Away" ng-model="selectedTeam">{{awayTeamName}}</label>
							</div>
						</div>
						<div class="form-group">
      						<label for="nr">#</label>
					    	<input id="numberInput" maxlength="3" type="text" ng-keydown="myFunct($event, selectedTeam, Name, Nr)" class="form-control" id="nr" placeholder="#" ng-model="Nr">
      						<label for="name"><?php echo $lang['NAME']; ?></label>
      						<input id="inputName" type="text" class="form-control" id="name" placeholder="Nimi" ng-model="Name">
    					</div>
    					<div id="file_upload">
							<label for="away_upload" id="selected_files_away" class="btn btn-info pull-right">
				    			<?php echo $lang['SELECT_AWAY_FILE']; ?>
							</label>
							<input name="away_upload" id="away_upload" type="file" on-read-file="addTeamFromFile($fileContent, 'Away')" class="pull-right"/>
    						<label for="home_upload" id="selected_files_home" class="btn btn-info pull-right">
				    			<?php echo $lang['SELECT_HOME_FILE']; ?>
							</label>
							<input name="home_upload" id="home_upload" type="file" on-read-file="addTeamFromFile($fileContent, 'Home')" class="pull-right"/>

							<span id="info_icon" ng-mouseover="showInfo = true" ng-mouseleave="showInfo = false" class="glyphicon glyphicon-info-sign pull-right" ></span>
    						<div id="info_content" ng-show="showInfo"><?php echo $lang['TEAM_FILE_INFO']; ?></div>
    					</div>
    			<button type="button" ng-click="newPlayer(selectedTeam, Name, Nr)" class="btn btn-primary" onClick='document.getElementById("inputName").focus();'><?php echo $lang['ADD_PLAYER']; ?></button>
				

				<table class="table table-bordered table-responsive">
					<tr>
						<td colspan="2">
							<span style="padding-right: 5px; float: left;" ng-click="showEditHome = true" ng-hide="showEditHome">{{homeTeamName}}</span>
							<input name="homeTeamName" style="width: 140px;" type="text" ng-show="showEditHome" ng-model="homeTeamName" value="{{homeTeamName}}" class="form-control"/>

							<span style="float: left; padding-right: 5px;" ng-click="showEditHome = true" ng-hide="showEditHome">({{homeTeamNameShort}})</span>
							<input name="homeTeamNameShort" maxlength="3" style="width: 60px;" type="text" ng-show="showEditHome" ng-model="homeTeamNameShort" value="{{homeTeamNameShort}}" class="form-control"/>
							<div style="float: left; width: 40px; height: 20px; background-color: {{homeTeamColor}}" ng-click="showEditHome = true" ng-hide="showEditHome"></div>
							<input name="homeTeamColor" style="width: 40px; padding: 0" type="color" ng-show="showEditHome" ng-model="homeTeamColor" value="{{homeTeamColor}}" class="form-control"/>
							

							<button type="button" ng-show="showEditHome" ng-click="showEditHome = false"><?php echo $lang['SAVE']; ?></button>
							<span style="font-size: 10px;" class="pull-right"><?php echo $lang['CLICK_ON_TEAM']; ?></span>
						</td>
					</tr>
					<tr>
						<td class="col-md-1">#</td>
						<td class="col-md-11"><?php echo $lang['NAME']; ?><button type="button" style="font-size: 10px;" class="btn btn-danger pull-right" ng-click="cleanTeam('Home')"><?php echo $lang['DELETE_HOME_TEAM']; ?></button></td>
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
							<span style="padding-right: 5px; float: left;" ng-click="showEditAway = true" ng-hide="showEditAway">{{awayTeamName}}</span>
							<input name="awayTeamName" style="width: 140px;" type="text" ng-show="showEditAway" ng-model="awayTeamName" value="{{awayTeamName}}" class="form-control"/>

							<span style="float: left; padding-right: 5px;" ng-click="showEditAway = true" ng-hide="showEditAway">({{awayTeamNameShort}})</span>
							<input name="awayTeamNameShort" maxlength="3" style="width: 60px;" type="text" ng-show="showEditAway" ng-model="awayTeamNameShort" value="{{awayTeamNameShort}}" class="form-control"/>
							<div style="float: left; width: 40px; height: 20px; background-color: {{awayTeamColor}}" ng-click="showEditAway = true" ng-hide="showEditAway"></div>
							<input name="awayTeamColor" style="width: 40px; padding: 0" type="color" ng-show="showEditAway" ng-model="awayTeamColor" value="{{awayTeamColor}}" class="form-control"/>
							<button type="button" ng-show="showEditAway" ng-click="showEditAway = false"><?php echo $lang['SAVE']; ?></button>
							<span style="font-size: 10px;" class="pull-right"><?php echo $lang['CLICK_ON_TEAM']; ?></span>
						</td>
					</tr>
					<tr>
						<td class="col-md-1">#</td>
						<td class="col-md-11"><?php echo $lang['NAME']; ?><button type="button" style="font-size: 10px;" class="btn btn-danger pull-right" ng-click="cleanTeam('Away')"><?php echo $lang['DELETE_AWAY_TEAM']; ?></button></td>
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
					<button type="button" class="btn btn-danger col-lg-6" ng-click="showTeamList = false; selectionDone = true"><?php echo $lang['BACK']; ?></button>
					<button class="btn btn-success col-lg-6"><?php echo $lang['SUBMIT_SETTINGS']; ?></button>
				</div>
				</div>
				</form>

			<div class="col-md-3"></div>

		</div>
	</div>
</div>
	<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.9/angular.min.js"></script>
	<script src="js/angular-translate.js"></script>
	<script src="js/selectionApp.js"></script>
	<script type="text/javascript">

		$("#home_upload").change(function() {
		    var names = [];
		    if($(this).get(0).files[0].name.length > 0){
		    	names.push($(this).get(0).files[0].name);
		    	$("#selected_files_home").html(names);
		    }
		});
		$("#away_upload").change(function() {
		    var names = [];
		    if($(this).get(0).files[0].name.length > 0){
		    	names.push($(this).get(0).files[0].name);
		    	$("#selected_files_away").html(names);
		    }
		});
	</script>
</body>
</html>