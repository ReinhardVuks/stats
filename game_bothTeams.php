<?php
session_start();
?>

<html ng-app="myApp">
<head>
  <script type="text/javascript" src="js/jquery-2.2.3.min.js"></script>
  <script type="text/javascript" src="http://netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.2.61/jspdf.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/2.0.16/jspdf.plugin.autotable.js"></script>
  <script type="text/javascript" src="js/tableToPDF.js"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
  <link rel="stylesheet" type="text/css" href="css/style.css">
  <link rel="stylesheet" type="text/css" href="css/hotkeys.min.css">
  <style type="text/css">
        [ng\:cloak], [ng-cloak], [data-ng-cloak], [x-ng-cloak], .ng-cloak, .x-ng-cloak {
        display: none !important;
    }       
    </style> 
</head>
<body ng-cloak>
  <script type="text/javascript">
    window.addEventListener("keydown", function(e) {
    // space and arrow keys
    if([32, 37, 38, 39, 40].indexOf(e.keyCode) > -1) {
        e.preventDefault();
    }
}, false);
  </script>
<div ng-app ng-controller="MyCtrl">
  <?php
  if(isset($_POST['home'])){
    $teamArray = $_POST['home'];
    for ($i=0; $i < count($teamArray); $i++) { 
      $player = explode(', ', $teamArray[$i]);
      ?>
      <div ng-init="newPlayer( 'Home', '<?php echo $player[0]; ?>', <?php echo $player[1]; ?> )"></div>
      <?php
    }
  }
    if(isset($_POST['away'])){
    $teamArray = $_POST['away'];
    for ($i=0; $i < count($teamArray); $i++) { 
      $player = explode(', ', $teamArray[$i]);
      ?>
      <div ng-init="newPlayer( 'Away', '<?php echo $player[0]; ?>', <?php echo $player[1]; ?> )"></div>
      <?php
    }
  }
    $values = $_POST['stats'];
    foreach ($values as $key => $value) {
        if($value == "true"){
          ?>
          <div ng-init="stats[<?php echo $key; ?>] = true"></div>
          <?php
        }else{
         ?>
          <div ng-init="stats[<?php echo $key; ?>] = false"></div>
          <?php
        }
    }
    ?>
    <div ng-init="timeGame = <?php echo $_POST['periodLength'] * 60; ?>; initialGameTime = <?php echo $_POST['periodLength'] * 60; ?>"></div>
    <div ng-init="maxOnCourtPlayers = <?php echo $_POST['playersOnCourt']; ?>"></div>
    <div ng-init="nrOfPeriods = <?php echo $_POST['numberOfPeriods']; ?>"></div>
    <div ng-init="maxFouls = <?php echo $_POST['maxFouls']; ?>"></div>

    <span id="info_icon" ng-mouseover="showInfo = true" ng-mouseleave="showInfo = false" class="glyphicon glyphicon-info-sign" ></span>
    <div id="info_content" ng-show="showInfo">Press "?" on keyboard to see shortcuts</div>
    <div ng-init="homeTeamName = '<?php echo $_POST['homeTeamName']; ?>'; awayTeamName = '<?php echo $_POST['awayTeamName']; ?>'"></div>
    <div id="teamStats" class="row" style="height: auto; text-align: center;">
      <div id="homeTeam" class="col-xs-4 col-sm-4 col-md-4 col-lg-4" style="text-align:center;">
        <div ng-show="team.Home.length > 0" >
        <p style="font-size: 35px;">{{homeTeamName}}</p>
        <h1>{{getTotal("Points", "Home")}}</h1>
        {{getTotal("Fouls", "Home")}}
        </div>
      </div>

      <div id="gameTime" class="col-xs-4 col-sm-4 col-md-4 col-lg-4" style="text-align:center;">
        <h1 ng-hide="editTime" ng-click="editTime = true; timeSplit();">{{timeGame | secondsToDateTime | date:'mm:ss'}}</h1>
        <button class="btn btn-alert" ng-click="resetTime()" ng-hide="editTime">Reset Time</button>
        <div ng-show="editTime" >
        <input min="0" max="20" type="number" ng-model="timeGameMin" value="{{timeGameMin}}">:
        <input min="0" max="59" type="number" ng-model="timeGameSec" value="{{timeGameSec}}">
        <span class="glyphicon glyphicon-ok" ng-click="editTime = false; newTime();"></span>
        </div>
        <div ng-show="stats.QUARTERS" style="font-size: 25px">
          <span class="glyphicon glyphicon-chevron-down" style="font-size: 15px" ng-click="changePeriod(0)"></span>
            {{period}}
          <span class="glyphicon glyphicon-chevron-up" style="font-size: 15px" ng-click="changePeriod(1)"></span>
        </div>
      </div>

      <div ng-show="team.Away.length > 0" id="awayTeam" class="col-xs-4 col-sm-4 col-md-4 col-lg-4" style="text-align:center;">
          <p style="font-size: 35px;">{{awayTeamName}}</p>
          <h1>{{getTotal("Points", "Away")}}</h1>
          {{getTotal("Fouls", "Away")}}
      </div>
    </div>
    <div style="text-align: center" >
      <button class="btn btn-info" id="reportButton" ng-click="topdf()">Report</button>
      <button class="btn btn-info" id="gameLogReportButton" ng-click="gameLogPdf()">Game Log</button>
    </div>  
    <div id="buttonContainer" ng-class="{'btn-disable' : onCourt[playerTeam].indexOf(playerId) <= -1, 'btn-disable2' : team[playerTeam][playerId]['Fouls'] >= maxFouls}">
    <button ng-class="{'btn-disable3': (onCourt.Home.length == 0 && onCourt.Away.length == 0)}" class="btn" ng-click="start()" ng-hide="team[playerTeam][playerId]['TimeRuns']">Start</button>
    <button id="stopButton" class="btn" ng-click="stop()" ng-show="team[playerTeam][playerId]['TimeRuns']">Stop</button>
    <div ng-show="stats.POINTS">
    <div class="btn-group" ng-model="shot" ng-show="shotBool && stats.MISSED_SHOTS">
      <button class="btn btn-success" ng-click="addPts(1)">1pt</button>
      <button class="btn btn-success" ng-click="addPts(2)">2pt</button>
      <button class="btn btn-success" ng-click="addPts(3)">3pt</button>
    </div>
    <div class="btn-group" ng-model="shot" ng-show="!stats.MISSED_SHOTS">
      <button class="btn btn-success" ng-click="fg(true, 1)">1pt</button>
      <button class="btn btn-success" ng-click="fg(true, 2)">2pt</button>
      <button class="btn btn-success" ng-click="fg(true, 3)">3pt</button>
    </div>
    <div class="btn-group" ng-model="fg" ng-show="fgBool && stats.MISSED_SHOTS">
      <span style="font-size: 41px; right: 89px; font-weight: 900; z-index: 10; position: absolute; color: white;">{{val}}</span>
      <button class="btn btn-danger" ng-click="fg(false, val)">Miss</button>
      <button class="btn btn-success" ng-click="fg(true, val)">Made</button>
    </div>
    </div>
    <div ng-show="stats.REBOUNDS"  class="btn-group" >
      <button ng-show="stats.OFF_REBOUNDS" class="btn btn-success" ng-click="addRbd(true)">Off.</button>
      <button class="btn btn-success" ng-click="addRbd(false)">Def.</button>
      <button ng-show="!stats.OFF_REBOUNDS" ng-click="addRbd(false)">Rebound</button>
    </div>
    <button ng-show="stats.ASSISTS3" class="btn btn-success" ng-click="addAst()">Assist</button>
    <button ng-show="stats.STEALS" class="btn btn-success" ng-click="addStl()">Steal</button>
    <button ng-show="stats.BLOCKS" class="btn btn-success" ng-click="addBlk()">Block</button>
    <button ng-show="stats.TURNOVERS" class="btn btn-warning" ng-click="addTo()">Turnover</button>
    <button ng-show="stats.FOULS" class="btn btn-danger" ng-click="addFls()">Foul</button>

  </div>
  <div id="gameLog" style="text-align: center; border: 1px solid grey; width: 50%; margin: 10 auto; border-radius:10px;">
    <h4 id="lastPlay"><span ng-show="gameLog[gameLog.length - 1].charAt(0) != '$'">{{gameLog[gameLog.length - 1]}}</span><span ng-show="gameLog[gameLog.length - 1].charAt(0) == '$'">{{gameLog[gameLog.length - 2]}}</span><span ng-show="gameLog.length > 1" class="glyphicon glyphicon-remove pull-right" ng-click="removeLastPlay()"></span></h4>
  </div>
  <div style="text-align: center">
  </div>
  <div class="row">

    <div ng-show="team.Home.length > 0" class="col-lg-5" style="margin-left: 20px;">

    <div class="homePlayerButtons">
        <a ng-class="{'inGameButton' : onCourt.Home.indexOf($index) > -1, 'selectedButton': playerId == $index && playerTeam == 'Home' }" ng-click="selectedPlayer($index, 'Home')" ng-repeat="player in team.Home" class="btn btn-success" style="width: 100px; float: left;">{{player.Name.split(" ").pop()}}<br>{{player.Nr}}</a>
    </div>
      <table id="box-score" class="table table-bordered table-condensed table-responsive">
        <tr>
          <td></td>
          <td id="name">#</td>
          <td>Name</td>
          <td>Time</td>
          <td>Pts</td>
          <td>FG</td>
          <td>FT%</td>
          <td>Off. Reb</td>
          <td>Def. Reb</td>
          <td>Tot. Reb.</td>
          <td>Ast</td>
          <td>Stl</td>
          <td>Blk</td>
          <td>TO</td>
          <td>Fls</td>
          <td>+/-</td>
        </tr>
        <tr ng-repeat="player in team.Home" ng-click="selectedPlayer($index, 'Home')" ng-class="{'selected': (playerId == $index && playerTeam == 'Home'),
              'out':inGame, 
              'inGame' : onCourt.Home.indexOf($index) > -1, 
              'btn-disable' : onCourt.Home.indexOf($index) == -1}">
          <td class="inGameCheckbox">
            <input ng-disabled="onCourt.Home.length == maxOnCourtPlayers && onCourt.Home.indexOf($index) <= -1 || GameOn" ng-click="switch($index, 'Home')" type="checkbox">
          </td>
          <td>
            {{player.Nr}}
          </td>
          <td id="name">
            {{player.Name}}
          </td>
          <td>
        {{player.Time | secondsToDateTime | date:'mm:ss'}}
      </td>
      <td>
        {{player.Points}}
      </td>
      <td>
        {{ ( (+player.TwoPtMade) + (+player.ThreePtMade) ) + "/" + ( (+player.TwoPtMade) + (+player.ThreePtMade) + (+player.TwoPtMiss) + (+player.ThreePtMiss) ) }}
      </td>
      <td>
        {{+player.OnePtMade + "/" + (+player.OnePtMade + +player.OnePtMiss)}}
      </td>
      <td ng-click=" editOffReb = true; editDefReb = false; editAssists = false; editSteals = false; editBlocks = false; editTurnovers = false; editFouls = false">
        {{player.OffReb}}
        <span ng-click="edit('OffReb', true, $index, playerTeam)" ng-show="editOffReb && $index == playerId" class="glyphicon glyphicon-menu-up pull-right"></span>
        <span ng-click="edit('OffReb', false, $index, playerTeam)" ng-show="editOffReb && $index == playerId" class="glyphicon glyphicon-menu-down pull-right"></span>
      </td>
      <td  ng-click=" editOffReb = false; editDefReb = true; editAssists = false; editSteals = false; editBlocks = false; editTurnovers = false; editFouls = false">
        {{player.DefReb}}
        <span ng-click="edit('DefReb', true, $index, playerTeam)" ng-show="editDefReb && $index == playerId" class="glyphicon glyphicon-menu-up pull-right"></span>
        <span ng-click="edit('DefReb', false, $index, playerTeam)" ng-show="editDefReb && $index == playerId" class="glyphicon glyphicon-menu-down pull-right"></span>
      </td>
      <td>
        {{+player.OffReb + +player.DefReb}}
      </td>
      <td  ng-click=" editOffReb = false; editDefReb = false; editAssists = true; editSteals = false; editBlocks = false; editTurnovers = false; editFouls = false">
        {{player.Assists}}
        <span ng-click="edit('Assists', true, $index, playerTeam)" ng-show="editAssists && $index == playerId" class="glyphicon glyphicon-menu-up pull-right"></span>
        <span ng-click="edit('Assists', false, $index, playerTeam)" ng-show="editAssists && $index == playerId" class="glyphicon glyphicon-menu-down pull-right"></span>
      </td>
      <td ng-click=" editOffReb = false; editDefReb = false; editAssists = false; editSteals = true; editBlocks = false; editTurnovers = false; editFouls = false">
        {{player.Steals}}
        <span ng-click="edit('Steals', true, $index, playerTeam)" ng-show="editSteals && $index == playerId" class="glyphicon glyphicon-menu-up pull-right"></span>
        <span ng-click="edit('Steals', false, $index, playerTeam)" ng-show="editSteals && $index == playerId" class="glyphicon glyphicon-menu-down pull-right"></span>
      </td>
      <td  ng-click=" editOffReb = false; editDefReb = false; editAssists = false; editSteals = false; editBlocks = true; editTurnovers = false; editFouls = false">
        {{player.Blocks}}
        <span ng-click="edit('Blocks', true, $index, playerTeam)" ng-show="editBlocks && $index == playerId" class="glyphicon glyphicon-menu-up pull-right"></span>
        <span ng-click="edit('Blocks', false, $index, playerTeam)" ng-show="editBlocks && $index == playerId" class="glyphicon glyphicon-menu-down pull-right"></span>
      </td>
      <td  ng-click=" editOffReb = false; editDefReb = false; editAssists = false; editSteals = false; editBlocks = false; editTurnovers = true; editFouls = false">
        {{player.Turnovers}}
        <span ng-click="edit('Turnovers', true, $index, playerTeam)" ng-show="editTurnovers && $index == playerId" class="glyphicon glyphicon-menu-up pull-right"></span>
        <span ng-click="edit('Turnovers', false, $index, playerTeam)" ng-show="editTurnovers && $index == playerId" class="glyphicon glyphicon-menu-down pull-right"></span>
      </td>
      <td  ng-click=" editOffReb = false; editDefReb = false; editAssists = false; editSteals = false; editBlocks = false; editTurnovers = false; editFouls = true">
        {{player.Fouls}}
        <span ng-click="edit('Fouls', true, $index, playerTeam)" ng-show="editFouls && $index == playerId" class="glyphicon glyphicon-menu-up pull-right"></span>
        <span ng-click="edit('Fouls', false, $index, playerTeam)" ng-show="editFouls && $index == playerId" class="glyphicon glyphicon-menu-down pull-right"></span>
      </td>
      <td>
        {{player.PlusMinus}}
      </td>
      <td ng-show="playerId == $index && playerTeam == 'Home' && team[playerTeam][playerId]['TimeRuns'] == false">
          <span class="glyphicon glyphicon-remove" ng-click="removePlayer($index)">
      </td> 
        </tr>
        <tr class="totalRow">
          <td colspan="3">Total</td>
          <td>{{getTotal("Time", "Home") | secondsToDateTime | date:'mm:ss'}}</td>
          <td>{{getTotal("Points", "Home")}}</td>

          <td>{{+getTotal("TwoPtMade", "Home") + +getTotal("ThreePtMade", "Home") + "/" +(+getTotal("TwoPtMiss", "Home") + +getTotal("ThreePtMiss", "Home") + +getTotal("TwoPtMade", "Home") + +getTotal("ThreePtMade", "Home"))}}</td>
          <td>{{+getTotal("OnePtMade", "Home") + "/" + (+getTotal("OnePtMade", "Home") + +getTotal("OnePtMiss", "Home"))}}</td>
          <td>{{getTotal("OffReb", "Home")}}</td>
          <td>{{getTotal("DefReb", "Home")}}</td>
          <td>{{getTotal("OffReb", "Home") + getTotal("DefReb", "Home")}}</td>
          <td>{{getTotal("Assists", "Home")}}</td>
          <td>{{getTotal("Steals", "Home")}}</td>
          <td>{{getTotal("Blocks", "Home")}}</td>
          <td>{{getTotal("Turnovers", "Home")}}</td>
          <td>{{getTotal("Fouls", "Home")}}</td>
          <td></td>
        </tr>
      </table>
    </div>
    <div class="col-lg-1"></div>
    <div ng-show="team.Away.length > 0" class="col-lg-5" style=" margin-right: 20px;">

    <div class="awayPlayerButtons">
        <a ng-class="{'selectedButton': playerId == $index && playerTeam == 'Away', 'inGameButton' : onCourt.Away.indexOf($index) > -1 }" ng-click="selectedPlayer($index, 'Away')" ng-repeat="player in team.Away" class="btn btn-success" style="width: 100px; float: left;">{{player.Name.split(" ").pop()}}<br>{{player.Nr}}</a>
    </div>
      <table id="box-score" class="table table-bordered table-condensed table-responsive">
        <tr>
          <td></td>
          <td id="name">#</td>
          <td>Name</td>
          <td>Time</td>
          <td>Pts</td>
          <td>FG</td>
          <td>FT%</td>
          <td>Off. Reb</td>
          <td>Def. Reb</td>
          <td>Tot. Reb.</td>
          <td>Ast</td>
          <td>Stl</td>
          <td>Blk</td>
          <td>TO</td>
          <td>Fls</td>
          <td>+/-</td>

        </tr>
        <tr ng-repeat="player in team.Away" ng-click="selectedPlayer($index, 'Away')" ng-class="{'selected': (playerId == $index && playerTeam == 'Away'),
              'out':inGame, 
              'inGame' : onCourt.Away.indexOf($index) > -1, 
              'btn-disable' : onCourt.Away.indexOf($index) == -1}">
          <td>
            <input ng-disabled="onCourt.Away.length == maxOnCourtPlayers && onCourt.Away.indexOf($index) <= -1 || GameOn" ng-click="switch($index, 'Away')" type="checkbox">
          </td>
          <td>
            {{player.Nr}}
          </td>
          <td id="name">
            {{player.Name}}
          </td>
          <td>
        {{player.Time | secondsToDateTime | date:'mm:ss'}}
      </td>
      <td>
        {{player.Points}}
      </td>
      <td>
        {{ ( (+player.TwoPtMade) + (+player.ThreePtMade) ) + "/" + ( (+player.TwoPtMade) + (+player.ThreePtMade) + (+player.TwoPtMiss) + (+player.ThreePtMiss) ) }}
        
      </td>
      <td>
        {{+player.OnePtMade + "/" + (+player.OnePtMade + +player.OnePtMiss)}}
      </td>
      <td ng-click=" editOffReb = true; editDefReb = false; editAssists = false; editSteals = false; editBlocks = false; editturnovers = false; editFouls = false">
        {{player.OffReb}}
        <span ng-click="edit('OffReb', true, $index, playerTeam)" ng-show="editOffReb && $index == playerId" class="glyphicon glyphicon-menu-up pull-right"></span>
        <span ng-click="edit('OffReb', false, $index, playerTeam)" ng-show="editOffReb && $index == playerId" class="glyphicon glyphicon-menu-down pull-right"></span>
      </td>
      <td ng-click=" editOffReb = false; editDefReb = true; editAssists = false; editSteals = false; editBlocks = false; editturnovers = false; editFouls = false">
        {{player.DefReb}}
        <span ng-click="edit('DefReb', true, $index, playerTeam)" ng-show="editDefReb && $index == playerId" class="glyphicon glyphicon-menu-up pull-right"></span>
        <span ng-click="edit('DefReb', false, $index, playerTeam)" ng-show="editDefReb && $index == playerId" class="glyphicon glyphicon-menu-down pull-right"></span>
      </td>
      <td>
        
        {{+player.OffReb + +player.DefReb}}
      
      </td>
      <td ng-click=" editOffReb = false; editDefReb = false; editAssists = true; editSteals = false; editBlocks = false; editturnovers = false; editFouls = false">
        {{player.Assists}}
        <span ng-click="edit('Assists', true, $index, playerTeam)" ng-show="editAssists && $index == playerId" class="glyphicon glyphicon-menu-up pull-right"></span>
        <span ng-click="edit('Assists', false, $index, playerTeam)" ng-show="editAssists && $index == playerId" class="glyphicon glyphicon-menu-down pull-right"></span>
      </td>
      <td ng-click=" editOffReb = false; editDefReb = false; editAssists = false; editSteals = true; editBlocks = false; editturnovers = false; editFouls = false">
        {{player.Steals}}
        <span ng-click="edit('Steals', true, $index, playerTeam)" ng-show="editSteals && $index == playerId" class="glyphicon glyphicon-menu-up pull-right"></span>
        <span ng-click="edit('Steals', false, $index, playerTeam)" ng-show="editSteals && $index == playerId" class="glyphicon glyphicon-menu-down pull-right"></span>
      </td>
      <td ng-click=" editOffReb = false; editDefReb = false; editAssists = false; editSteals = false; editBlocks = true; editturnovers = false; editFouls = false">
        {{player.Blocks}}
        <span ng-click="edit('Blocks', true, $index, playerTeam)" ng-show="editBlocks && $index == playerId" class="glyphicon glyphicon-menu-up pull-right"></span>
        <span ng-click="edit('Blocks', false, $index, playerTeam)" ng-show="editBlocks && $index == playerId" class="glyphicon glyphicon-menu-down pull-right"></span>
      </td>
      <td ng-click=" editOffReb = false; editDefReb = false; editAssists = false; editSteals = false; editBlocks = false; editTurnovers = true; editFouls = true">
        {{player.Turnovers}}
        <span ng-click="edit('turnovers', true, $index, playerTeam)" ng-show="editTurnovers && $index == playerId" class="glyphicon glyphicon-menu-up pull-right"></span>
        <span ng-click="edit('turnovers', false, $index, playerTeam)" ng-show="editTurnovers && $index == playerId" class="glyphicon glyphicon-menu-down pull-right"></span>
      </td>
      <td ng-click=" editOffReb = false; editDefReb = false; editAssists = false; editSteals = false; editBlocks = false; editturnovers = false; editFouls = true">
        {{player.Fouls}}
        <span ng-click="edit('Fouls', true, $index, playerTeam)" ng-show="editFouls && $index == playerId" class="glyphicon glyphicon-menu-up pull-right"></span>
        <span ng-click="edit('Fouls', false, $index, playerTeam)" ng-show="editFouls && $index == playerId" class="glyphicon glyphicon-menu-down pull-right"></span>
      </td>
      <td>
        {{player.PlusMinus}}
      </td>
      <td ng-show="playerId == $index && playerTeam == 'Away' && team[playerTeam][playerId]['TimeRuns'] == false">
          <span class="glyphicon glyphicon-remove" ng-click="removePlayer($index)">
      </td>         </tr>
        <tr class="totalRow">
          <td colspan="3">Total</td>
          <td>{{getTotal("Time", "Away") | secondsToDateTime | date:'mm:ss'}}</td>
          <td>{{getTotal("Points", "Away")}}</td>

          <td>{{+getTotal("TwoPtMade", "Away") + +getTotal("ThreePtMade", "Away") + "/" +(+getTotal("TwoPtMiss", "Away") + +getTotal("ThreePtMiss", "Away") + +getTotal("TwoPtMade", "Away") + +getTotal("ThreePtMade", "Away"))}}</td>
          <td>{{+getTotal("OnePtMade", "Away") + "/" + (+getTotal("OnePtMade", "Away") + +getTotal("OnePtMiss", "Away"))}}</td>
          <td>{{getTotal("OffReb", "Away")}}</td>
          <td>{{getTotal("DefReb", "Away")}}</td>
          <td>{{getTotal("OffReb", "Away") + getTotal("DefReb", "Away")}}</td>
          <td>{{getTotal("Assists", "Away")}}</td>
          <td>{{getTotal("Steals", "Away")}}</td>
          <td>{{getTotal("Blocks", "Away")}}</td>
          <td>{{getTotal("Turnovers", "Away")}}</td>
          <td>{{getTotal("Fouls", "Away")}}</td>
          <td></td>
        </tr>
      </table>
      <!--
  <label>Lisa uus mängija</label>
  <form class="form-inline" role="form">
    <div class="form-group">
      <label for="name">Nimi</label>
      <input id="inputName" type="text" class="form-control" id="name" placeholder="Nimi" ng-model="Name">
    </div>
    <div class="form-group">
      <label for="nr">Number</label>
      <input type="text" class="form-control" id="nr" placeholder="Number" ng-model="Nr">
    </div>
    <button type="submit" ng-click="newPlayer(Name, Nr)" class="btn btn-primary" onClick='document.getElementById("inputName").focus();'>Sisesta</button>
  </form>
  -->
    </div>
    <button type="button" class="btn btn-success" style="width: 100%;">FINISH GAME</button>
  </div>


<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular.min.js"></script>
  <script src="js/app2.js"></script>
  <script src="js/popover.js"></script>
  <script src="js/hotkeys.min.js" type="text/javascript"></script>
</body>
</html>