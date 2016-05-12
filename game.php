<?php
session_start();
include_once 'common.php';
?>

<html ng-app="myApp">
<head>
  <meta charset="utf-8" />
  
  <script src="https://cdn.pubnub.com/pubnub.min.js"></script>
  <script src="https://cdn.pubnub.com/pubnub-crypto.min.js"></script>

  <script type="text/javascript" src="js/jquery-2.2.3.min.js"></script>
  <script type="text/javascript" src="http://netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
  
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.2.61/jspdf.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/2.0.16/jspdf.plugin.autotable.js"></script>
  <script type="text/javascript" src="js/tableToPDF.js"></script>
  <script type='text/javascript' src='js/tinycolor.js'></script>
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
  <script type="text/javascript">
  $(document).ready(function() { 
    $('#homePlayerButtons').css("background-color", "<?php echo $_POST['homeTeamColor'] ?>");
    $('#awayPlayerButtons').css("background-color", "<?php echo $_POST['awayTeamColor'] ?>");

    if(tinycolor('<?php echo $_POST['homeTeamColor'] ?>').isLight()){
      $('#homePlayerButtons').css("color", "black");
    }
    if(tinycolor('<?php echo $_POST['awayTeamColor'] ?>').isLight()){
      $('#awayPlayerButtons').css("color", "black");
    }
  });
  </script>
  <div ng-app ng-controller="MyCtrl">
    <div ng-init="setLang('<?php echo $_COOKIE['lang']; ?>')">
      <div ng-init="gameLogAdd('<?php echo $lang['GAME_STARTS']; ?>')"></div>
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
     <div ng-init="timeGame = <?php echo $_POST['PERIOD_LENGTH'] * 60; ?>; initialGameTime = <?php echo $_POST['PERIOD_LENGTH'] * 60; ?>"></div>
     <div ng-init="maxOnCourtPlayers = <?php echo $_POST['MAX_ON_COURT_PLAYERS']; ?>"></div>
     <div ng-init="nrOfPeriods = <?php echo $_POST['NR_OF_PERIODS']; ?>"></div>
     <div ng-init="maxFouls = <?php echo $_POST['MAX_FOULS']; ?>"></div>
     <span id="info_icon" ng-mouseover="showInfo = true" ng-mouseleave="showInfo = false" class="glyphicon glyphicon-info-sign" ></span>
     <div id="info_content" ng-show="showInfo"><span><?php echo $lang['INFO1']?></span><span><?php echo $lang['INFO2']?></span></div>
     <div ng-init="homeTeamName = '<?php echo $_POST['homeTeamName']; ?>'; awayTeamName = '<?php echo $_POST['awayTeamName']; ?>'; homeTeamNameShort = '<?php echo $_POST['homeTeamNameShort'] ?>'; awayTeamNameShort = '<?php echo $_POST['awayTeamNameShort'] ?>'; homeTeamColor = '<?php echo $_POST['homeTeamColor'] ?>'; awayTeamColor= '<?php echo $_POST['awayTeamColor'] ?>'"></div>
     <div id="teamStats" class="row" style="height: auto; text-align: center;">
      <div id="homeTeam" class="col-xs-4 col-sm-4 col-md-4 col-lg-4" style="text-align:center;">
        <div ng-show="team.Home.length > 0" >
          <p class="btn" ng-class="{'selectedTeam': playerId == 0 && playerTeam == 'Home'}" ng-click="playerId = 0; playerTeam = 'Home'" style="font-size: 35px;">{{homeTeamName}}</p>
          <h1><td>{{getTotal("OnePtMade", "Home") + (+getTotal("TwoPtMade", "Home") * 2) + (+getTotal("ThreePtMade", "Home") * 3)}}</td></h1>
          {{periodFouls["Home"][period - 1]}}
        </div>
      </div>
      <div id="gameTime" class="col-xs-4 col-sm-4 col-md-4 col-lg-4" style="text-align:center;">
        <div ng-show="stats.TIME">
          <h1 ng-hide="editTime" ng-click="editTime = true; timeSplit();">{{timeGame | secondsToDateTime | date:'mm:ss'}}</h1>
          <button class="btn btn-alert" ng-click="resetTime()" ng-hide="editTime"><?php echo $lang['RESET_TIME']; ?></button>
          <div ng-show="editTime" >
            <input min="0" max="99" maxlength="2" type="number" ng-model="timeGameMin" value="{{timeGameMin}}">:
            <input min="0" max="59" maxlength="2" type="number" ng-model="timeGameSec" value="{{timeGameSec}}">
            <span class="glyphicon glyphicon-ok" ng-click="editTime = false; newTime();"></span>
          </div>
        </div>
        <div ng-show="stats.QUARTERS" style="font-size: 25px">
          <span class="glyphicon glyphicon-chevron-down" style="font-size: 15px" ng-click="changePeriod(0)"></span>
          {{period}}
          <span class="glyphicon glyphicon-chevron-up" style="font-size: 15px" ng-click="changePeriod(1)"></span>
        </div>
      </div>
      <div ng-show="team.Away.length > 0" id="awayTeam" class="col-xs-4 col-sm-4 col-md-4 col-lg-4" style="text-align:center;">
        <p class="btn" ng-class="{'selectedTeam': playerId == 0 && playerTeam == 'Away'}" ng-click="playerId = 0; playerTeam = 'Away'" style="font-size: 35px;">{{awayTeamName}}</p>
        <h1><td>{{getTotal("OnePtMade", "Away") + (+getTotal("TwoPtMade", "Away") * 2) + (+getTotal("ThreePtMade", "Away") * 3)}}</td></h1>
        {{periodFouls["Away"][period - 1]}}
      </div>
    </div>
    <div style="min-height: 186px;" id="buttonContainer" ng-class="{'btn-disable' : onCourt[playerTeam].indexOf(playerId) <= -1, 'btn-disable2' : team[playerTeam][playerId]['Fouls'] >= maxFouls && playerId != 0}">
      <div ng-show="stats.TIME">
        <button ng-class="{'btn-disable3': (onCourt.Home.length == 1 && onCourt.Away.length == 1 || playerId == 0)}" class="btn" ng-click="start()" ng-hide="GameOn"><?php echo $lang['START']; ?></button>
        <button  id="stopButton" class="btn" ng-click="stop()" ng-show="GameOn"><?php echo $lang['STOP']; ?></button>
      </div>
      <div>
        <div class="btn-group" ng-model="shot" ng-show="shotBool && stats.MISSED_SHOTS">
          <button ng-class="{'btn-disable3': playerId == 0}" class="btn btn-success" ng-click="addPts(1)">1pt</button>
          <button ng-class="{'btn-disable3': playerId == 0}" class="btn btn-success" ng-click="addPts(2)">2pt</button>
          <button ng-class="{'btn-disable3': playerId == 0}" class="btn btn-success" ng-click="addPts(3)">3pt</button>
        </div>
        <div class="btn-group" ng-model="shot" ng-show="!stats.MISSED_SHOTS">
          <button ng-class="{'btn-disable3': playerId == 0}" class="btn btn-success" ng-click="fg(true, 1)">1pt</button>
          <button ng-class="{'btn-disable3': playerId == 0}" class="btn btn-success" ng-click="fg(true, 2)">2pt</button>
          <button ng-class="{'btn-disable3': playerId == 0}" class="btn btn-success" ng-click="fg(true, 3)">3pt</button>
        </div>
        <div class="btn-group" ng-model="fg" ng-show="fgBool && stats.MISSED_SHOTS">
          <span style="font-size: 41px; right: 105px; font-weight: 900; z-index: 10; position: absolute; color: white;">{{val}}</span>
          <button class="btn btn-danger" ng-click="fg(false, val)"><?php echo $lang['MISS']; ?></button>
          <button class="btn btn-success" ng-click="fg(true, val)"><?php echo $lang['MADE']; ?></button>
        </div>
      </div>
      <div ng-show="stats.REBOUNDS"  class="btn-group" >
        <button ng-show="stats.OFF_REBOUNDS" class="btn btn-success" ng-click="addRbd(true)"><?php echo $lang['OFF_REB']; ?></button>
        <button class="btn btn-success" ng-click="addRbd(false)"><?php echo $lang['DEF_REB']; ?></button>
        <button ng-show="!stats.OFF_REBOUNDS" ng-click="addRbd(false)"><?php echo $lang['REB']; ?></button>
      </div>
      <button ng-class="{'btn-disable3': playerId == 0}" ng-show="stats.ASSISTS" class="btn btn-success" ng-click="addAst()"><?php echo $lang['ASSIST']; ?></button>
      <button ng-class="{'btn-disable3': playerId == 0}" ng-show="stats.STEALS" class="btn btn-success" ng-click="addStl()"><?php echo $lang['STEAL']; ?></button>
      <button ng-class="{'btn-disable3': playerId == 0}" ng-show="stats.BLOCKS" class="btn btn-success" ng-click="addBlk()"><?php echo $lang['BLOCK']; ?></button>
      <button ng-show="stats.TURNOVERS" class="btn btn-warning" ng-click="addTo()"><?php echo $lang['TURNOVER']; ?></button>
      <button ng-show="stats.FOULS" class="btn btn-danger" ng-click="addFls()"><?php echo $lang['FOULS']; ?></button>
    </div>
    <div id="gameLog" style="text-align: center; border: 1px solid grey; width: 50%; margin: 10 auto; border-radius:10px;">
      <h4 id="lastPlay"><span ng-show="gameLog[gameLog.length - 1].charAt(0) != '$'">{{gameLog[gameLog.length - 1][0]}}</span><span ng-show="gameLog[gameLog.length - 1].charAt(0) == '$'">{{gameLog[gameLog.length - 2]}}</span><span ng-show="gameLog.length > 1" class="glyphicon glyphicon-remove pull-right" ng-click="removeLastPlay()"></span></h4>
    </div>
    <div style="text-align: center">
    </div>
    <div class="row">

      <div ng-show="team.Home.length > 0" class="col-lg-5" style="margin-left: 20px;">
        <div class="playerButtons">
          <button id="homePlayerButtons" 
          ng-class="{'inGameButton' : onCourt.Home.indexOf($index) > -1, 'selectedButton': playerId == $index && playerTeam == 'Home' }" 
          ng-click="selectedPlayer($index, 'Home')" 
          ng-repeat="player in team.Home" 
          class="btn btn-success" 
          style="width: 100px; float: left;'"
          ng-hide="$index == 0">
          {{player.Name.split(" ").pop()}}<br>{{player.Nr}}<br><input class="inGameCheckbox" ng-model="playerCheckBox" ng-disabled="onCourt.Home.length - 1 == maxOnCourtPlayers && onCourt.Home.indexOf($index) <= -1 || GameOn" ng-click="switch($index, 'Home')" type="checkbox" ng-checked="onCourt.Home.indexOf($index) > -1"></button>
        </div>
        <table id="box-score" class="table table-bordered table-condensed table-responsive">
          <tr>
            <td></td>
            <td id="name">#</td>
            <td><?php echo $lang['NAME']; ?></td>
            <td><?php echo $lang['TIME']; ?></td>
            <td><?php echo $lang['PTS']; ?></td>
            <td>2Pt</td>
            <td>3Pt</td>
            <td><?php echo $lang['FT%']; ?></td>
            <td><?php echo $lang['OFF._REB']; ?></td>
            <td><?php echo $lang['DEF._REB']; ?></td>
            <td><?php echo $lang['TOT._REB']; ?></td>
            <td><?php echo $lang['AST']; ?></td>
            <td><?php echo $lang['STL']; ?></td>
            <td><?php echo $lang['BLK']; ?></td>
            <td><?php echo $lang['TO']; ?></td>
            <td><?php echo $lang['FLS']; ?></td>
            <td>+/-</td>
          </tr>
          <tr ng-repeat="player in team.Home" 
          ng-click="selectedPlayer($index, 'Home')"
          ng-class="{'selected': (playerId == $index && playerTeam == 'Home'),
          'out':inGame, 
          'inGame' : onCourt.Home.indexOf($index) > -1, 
          'btn-disable' : onCourt.Home.indexOf($index) == -1}"
          ng-hide="$index == 0">
          <td class="inGameCheckbox">
            <input ng-model="playerCheckBox" ng-disabled="onCourt.Home.length - 1 == maxOnCourtPlayers && onCourt.Home.indexOf($index) <= -1 || GameOn" ng-click="switch($index, 'Home')" type="checkbox" ng-checked="onCourt.Home.indexOf($index) > -1">
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
            {{  +player.OnePtMade + (+player.TwoPtMade * 2) + (+player.ThreePtMade * 3)  }}
          </td>
          <td ng-click="editTwoPt = true; editThreePt = false; editFt = false; editOffReb = false; editDefReb = false; editAssists = false; editSteals = false; editBlocks = false; editturnovers = false; editFouls = false">
            {{ ( (+player.TwoPtMade)) + "/" + ( (+player.TwoPtMade) + (+player.TwoPtMiss)) }}
            <span ng-click="edit('TwoPtMade', true, $index, playerTeam)" ng-show="editTwoPt && $index == playerId" class="glyphicon glyphicon-menu-up pull-right"></span>
            <span ng-click="edit('TwoPtMade', false, $index, playerTeam)" ng-show="editTwoPt && $index == playerId" class="glyphicon glyphicon-menu-down pull-right"></span>
          </td>
          <td ng-click="editTwoPt = false; editThreePt = true; editFt = false; editOffReb = false; editDefReb = false; editAssists = false; editSteals = false; editBlocks = false; editturnovers = false; editFouls = false">
            {{ ( (+player.ThreePtMade)) + "/" + ( (+player.ThreePtMade) + (+player.ThreePtMiss)) }}
            <span ng-click="edit('ThreePtMade', true, $index, playerTeam)" ng-show="editThreePt && $index == playerId" class="glyphicon glyphicon-menu-up pull-right"></span>
            <span ng-click="edit('ThreePtMade', false, $index, playerTeam)" ng-show="editThreePt && $index == playerId" class="glyphicon glyphicon-menu-down pull-right"></span>
          </td>
          <td ng-click="editTwoPt = false; editThreePt = false; editFt = true; editOffReb = false; editDefReb = false; editAssists = false; editSteals = false; editBlocks = false; editturnovers = false; editFouls = false">
            {{+player.OnePtMade + "/" + (+player.OnePtMade + +player.OnePtMiss)}}
            <span ng-click="edit('OnePtMade', true, $index, playerTeam)" ng-show="editFt && $index == playerId" class="glyphicon glyphicon-menu-up pull-right"></span>
            <span ng-click="edit('OnePtMade', false, $index, playerTeam)" ng-show="editFt && $index == playerId" class="glyphicon glyphicon-menu-down pull-right"></span>
          </td>
          <td ng-click="editTwoPt = false; editThreePt = false; editFt = false;  editOffReb = true; editDefReb = false; editAssists = false; editSteals = false; editBlocks = false; editTurnovers = false; editFouls = false">
            {{player.OffReb}}
            <span ng-click="edit('OffReb', true, $index, playerTeam)" ng-show="editOffReb && $index == playerId" class="glyphicon glyphicon-menu-up pull-right"></span>
            <span ng-click="edit('OffReb', false, $index, playerTeam)" ng-show="editOffReb && $index == playerId" class="glyphicon glyphicon-menu-down pull-right"></span>
          </td>
          <td  ng-click=" editTwoPt = false; editThreePt = false; editFt = false; editOffReb = false; editDefReb = true; editAssists = false; editSteals = false; editBlocks = false; editTurnovers = false; editFouls = false">
            {{player.DefReb}}
            <span ng-click="edit('DefReb', true, $index, playerTeam)" ng-show="editDefReb && $index == playerId" class="glyphicon glyphicon-menu-up pull-right"></span>
            <span ng-click="edit('DefReb', false, $index, playerTeam)" ng-show="editDefReb && $index == playerId" class="glyphicon glyphicon-menu-down pull-right"></span>
          </td>
          <td>
            {{+player.OffReb + +player.DefReb}}
          </td>
          <td  ng-click=" editTwoPt = false; editThreePt = false; editFt = false; editOffReb = false; editDefReb = false; editAssists = true; editSteals = false; editBlocks = false; editTurnovers = false; editFouls = false">
            {{player.Assists}}
            <span ng-click="edit('Assists', true, $index, playerTeam)" ng-show="editAssists && $index == playerId" class="glyphicon glyphicon-menu-up pull-right"></span>
            <span ng-click="edit('Assists', false, $index, playerTeam)" ng-show="editAssists && $index == playerId" class="glyphicon glyphicon-menu-down pull-right"></span>
          </td>
          <td ng-click=" editTwoPt = false; editThreePt = false; editFt = false; editOffReb = false; editDefReb = false; editAssists = false; editSteals = true; editBlocks = false; editTurnovers = false; editFouls = false">
            {{player.Steals}}
            <span ng-click="edit('Steals', true, $index, playerTeam)" ng-show="editSteals && $index == playerId" class="glyphicon glyphicon-menu-up pull-right"></span>
            <span ng-click="edit('Steals', false, $index, playerTeam)" ng-show="editSteals && $index == playerId" class="glyphicon glyphicon-menu-down pull-right"></span>
          </td>
          <td  ng-click=" editTwoPt = false; editThreePt = false; editFt = false; editOffReb = false; editDefReb = false; editAssists = false; editSteals = false; editBlocks = true; editTurnovers = false; editFouls = false">
            {{player.Blocks}}
            <span ng-click="edit('Blocks', true, $index, playerTeam)" ng-show="editBlocks && $index == playerId" class="glyphicon glyphicon-menu-up pull-right"></span>
            <span ng-click="edit('Blocks', false, $index, playerTeam)" ng-show="editBlocks && $index == playerId" class="glyphicon glyphicon-menu-down pull-right"></span>
          </td>
          <td  ng-click=" editTwoPt = false; editThreePt = false; editFt = false; editOffReb = false; editDefReb = false; editAssists = false; editSteals = false; editBlocks = false; editTurnovers = true; editFouls = false">
            {{player.Turnovers}}
            <span ng-click="edit('Turnovers', true, $index, playerTeam)" ng-show="editTurnovers && $index == playerId" class="glyphicon glyphicon-menu-up pull-right"></span>
            <span ng-click="edit('Turnovers', false, $index, playerTeam)" ng-show="editTurnovers && $index == playerId" class="glyphicon glyphicon-menu-down pull-right"></span>
          </td>
          <td  ng-click=" editTwoPt = false; editThreePt = false; editFt = false; editOffReb = false; editDefReb = false; editAssists = false; editSteals = false; editBlocks = false; editTurnovers = false; editFouls = true">
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
          <tr>
            <td colspan="3">Team</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{team["Home"][0].OffReb}}</td>
            <td>{{team["Home"][0].DefReb}}</td>
            <td>{{+(team["Home"][0].OffReb) + +(team["Home"][0].DefReb)}}</td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{team["Home"][0].Turnovers}}</td>
            <td>{{team["Home"][0].Fouls}}</td>
            <td></td>
          </tr>
          <tr class="totalRow">
            <td colspan="3">Total</td>
            <td>{{getTotal("Time", "Home") | secondsToDateTime | date:'mm:ss'}}</td>
            <td>{{getTotal("OnePtMade", "Home") + (+getTotal("TwoPtMade", "Home") * 2) + (+getTotal("ThreePtMade", "Home") * 3)}}</td>
            <td>{{+getTotal("TwoPtMade", "Home") + "/" +(+getTotal("TwoPtMiss", "Home")+ +getTotal("TwoPtMade", "Home"))}}</td>
            <td>{{+getTotal("ThreePtMade", "Home") + "/" +(+getTotal("ThreePtMiss", "Home")+ +getTotal("ThreePtMade", "Home"))}}</td>
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

        <div class="playerButtons">
          <button id="awayPlayerButtons" ng-hide="$index == 0" ng-class="{'selectedButton': playerId == $index && playerTeam == 'Away', 'inGameButton' : onCourt.Away.indexOf($index) > -1 }" ng-click="selectedPlayer($index, 'Away')" ng-repeat="player in team.Away" class="btn btn-success" style="width: 100px; float: left;">{{player.Name.split(" ").pop()}}<br>{{player.Nr}}<br><input class="inGameCheckbox" ng-model="playerCheckBox" ng-disabled="onCourt.Away.length - 1 == maxOnCourtPlayers && onCourt.Away.indexOf($index) <= -1 || GameOn" ng-click="switch($index, 'Away')" type="checkbox" ng-checked="onCourt.Away.indexOf($index) > -1"></button>
        </div>
        <table id="box-score" class="table table-bordered table-condensed table-responsive">
          <tr>
            <td></td>
            <td id="name">#</td>
            <td><?php echo $lang['NAME']; ?></td>
            <td><?php echo $lang['TIME']; ?></td>
            <td><?php echo $lang['PTS']; ?></td>
            <td>2Pt</td>
            <td>3Pt</td>
            <td><?php echo $lang['FT%']; ?></td>
            <td><?php echo $lang['OFF._REB']; ?></td>
            <td><?php echo $lang['DEF._REB']; ?></td>
            <td><?php echo $lang['TOT._REB']; ?></td>
            <td><?php echo $lang['AST']; ?></td>
            <td><?php echo $lang['STL']; ?></td>
            <td><?php echo $lang['BLK']; ?></td>
            <td><?php echo $lang['TO']; ?></td>
            <td><?php echo $lang['FLS']; ?></td>
            <td>+/-</td>
          </tr>
          <tr ng-repeat="player in team.Away" ng-click="selectedPlayer($index, 'Away')" ng-class="{'selected': (playerId == $index && playerTeam == 'Away'),
          'out':inGame, 
          'inGame' : onCourt.Away.indexOf($index) > -1, 
          'btn-disable' : onCourt.Away.indexOf($index) == -1}" ng-hide="$index == 0">
          <td>
            <input class="inGameCheckbox" ng-model="playerCheckBox" ng-disabled="onCourt.Away.length - 1 == maxOnCourtPlayers && onCourt.Away.indexOf($index) <= -1 || GameOn" ng-click="switch($index, 'Away')" type="checkbox" ng-checked="onCourt.Away.indexOf($index) > -1">
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
            {{  +player.OnePtMade + (+player.TwoPtMade * 2) + (+player.ThreePtMade * 3)  }}
          </td>
          <td ng-click="editTwoPt = true; editThreePt = false; editFt = false; editOffReb = false; editDefReb = false; editAssists = false; editSteals = false; editBlocks = false; editturnovers = false; editFouls = false">
            {{ ( (+player.TwoPtMade)) + "/" + ( (+player.TwoPtMade) + (+player.TwoPtMiss)) }}
            <span ng-click="edit('TwoPtMade', true, $index, playerTeam)" ng-show="editTwoPt && $index == playerId" class="glyphicon glyphicon-menu-up pull-right"></span>
            <span ng-click="edit('TwoPtMade', false, $index, playerTeam)" ng-show="editTwoPt && $index == playerId" class="glyphicon glyphicon-menu-down pull-right"></span>
          </td>
          <td ng-click="editTwoPt = false; editThreePt = true; editFt = false; editOffReb = false; editDefReb = false; editAssists = false; editSteals = false; editBlocks = false; editturnovers = false; editFouls = false">
            {{ ( (+player.ThreePtMade)) + "/" + ( (+player.ThreePtMade) + (+player.ThreePtMiss)) }}
            <span ng-click="edit('ThreePtMade', true, $index, playerTeam)" ng-show="editThreePt && $index == playerId" class="glyphicon glyphicon-menu-up pull-right"></span>
            <span ng-click="edit('ThreePtMade', false, $index, playerTeam)" ng-show="editThreePt && $index == playerId" class="glyphicon glyphicon-menu-down pull-right"></span>
          </td>
          <td ng-click="editTwoPt = false; editThreePt = false; editFt = true; editOffReb = false; editDefReb = false; editAssists = false; editSteals = false; editBlocks = false; editturnovers = false; editFouls = false">
            {{+player.OnePtMade + "/" + (+player.OnePtMade + +player.OnePtMiss)}}
            <span ng-click="edit('OnePtMade', true, $index, playerTeam)" ng-show="editFt && $index == playerId" class="glyphicon glyphicon-menu-up pull-right"></span>
            <span ng-click="edit('OnePtMade', false, $index, playerTeam)" ng-show="editFt && $index == playerId" class="glyphicon glyphicon-menu-down pull-right"></span>
          </td>
          <td ng-click="editTwoPt = false; editThreePt = false; editFt = false; editOffReb = true; editDefReb = false; editAssists = false; editSteals = false; editBlocks = false; editturnovers = false; editFouls = false">
            {{player.OffReb}}
            <span ng-click="edit('OffReb', true, $index, playerTeam)" ng-show="editOffReb && $index == playerId" class="glyphicon glyphicon-menu-up pull-right"></span>
            <span ng-click="edit('OffReb', false, $index, playerTeam)" ng-show="editOffReb && $index == playerId" class="glyphicon glyphicon-menu-down pull-right"></span>
          </td>
          <td ng-click="editTwoPt = false; editThreePt = false; editFt = false; editOffReb = false; editDefReb = true; editAssists = false; editSteals = false; editBlocks = false; editturnovers = false; editFouls = false">
            {{player.DefReb}}
            <span ng-click="edit('DefReb', true, $index, playerTeam)" ng-show="editDefReb && $index == playerId" class="glyphicon glyphicon-menu-up pull-right"></span>
            <span ng-click="edit('DefReb', false, $index, playerTeam)" ng-show="editDefReb && $index == playerId" class="glyphicon glyphicon-menu-down pull-right"></span>
          </td>
          <td>
            {{+player.OffReb + +player.DefReb}}
          </td>
          <td ng-click="editTwoPt = false; editThreePt = false; editFt = false; editOffReb = false; editDefReb = false; editAssists = true; editSteals = false; editBlocks = false; editturnovers = false; editFouls = false">
            {{player.Assists}}
            <span ng-click="edit('Assists', true, $index, playerTeam)" ng-show="editAssists && $index == playerId" class="glyphicon glyphicon-menu-up pull-right"></span>
            <span ng-click="edit('Assists', false, $index, playerTeam)" ng-show="editAssists && $index == playerId" class="glyphicon glyphicon-menu-down pull-right"></span>
          </td>
          <td ng-click="editTwoPt = false; editThreePt = false; editFt = false; editOffReb = false; editDefReb = false; editAssists = false; editSteals = true; editBlocks = false; editturnovers = false; editFouls = false">
            {{player.Steals}}
            <span ng-click="edit('Steals', true, $index, playerTeam)" ng-show="editSteals && $index == playerId" class="glyphicon glyphicon-menu-up pull-right"></span>
            <span ng-click="edit('Steals', false, $index, playerTeam)" ng-show="editSteals && $index == playerId" class="glyphicon glyphicon-menu-down pull-right"></span>
          </td>
          <td ng-click="editTwoPt = false; editThreePt = false; editFt = false; editOffReb = false; editDefReb = false; editAssists = false; editSteals = false; editBlocks = true; editturnovers = false; editFouls = false">
            {{player.Blocks}}
            <span ng-click="edit('Blocks', true, $index, playerTeam)" ng-show="editBlocks && $index == playerId" class="glyphicon glyphicon-menu-up pull-right"></span>
            <span ng-click="edit('Blocks', false, $index, playerTeam)" ng-show="editBlocks && $index == playerId" class="glyphicon glyphicon-menu-down pull-right"></span>
          </td>
          <td ng-click="editTwoPt = false; editThreePt = false; editFt = false; editOffReb = false; editDefReb = false; editAssists = false; editSteals = false; editBlocks = false; editTurnovers = true; editFouls = true">
            {{player.Turnovers}}
            <span ng-click="edit('turnovers', true, $index, playerTeam)" ng-show="editTurnovers && $index == playerId" class="glyphicon glyphicon-menu-up pull-right"></span>
            <span ng-click="edit('turnovers', false, $index, playerTeam)" ng-show="editTurnovers && $index == playerId" class="glyphicon glyphicon-menu-down pull-right"></span>
          </td>
          <td ng-click="editTwoPt = false; editThreePt = false; editFt = false; editOffReb = false; editDefReb = false; editAssists = false; editSteals = false; editBlocks = false; editturnovers = false; editFouls = true">
            {{player.Fouls}}
            <span ng-click="edit('Fouls', true, $index, playerTeam)" ng-show="editFouls && $index == playerId" class="glyphicon glyphicon-menu-up pull-right"></span>
            <span ng-click="edit('Fouls', false, $index, playerTeam)" ng-show="editFouls && $index == playerId" class="glyphicon glyphicon-menu-down pull-right"></span>
          </td>
          <td>
            {{player.PlusMinus}}
          </td>
          <td ng-show="playerId == $index && playerTeam == 'Away' && team[playerTeam][playerId]['TimeRuns'] == false">
            <span class="glyphicon glyphicon-remove" ng-click="removePlayer($index)">
            </td>         
          </tr>
          <tr>
            <td colspan="3">Team</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{team["Away"][0].OffReb}}</td>
            <td>{{team["Away"][0].DefReb}}</td>
            <td>{{+(team["Away"][0].OffReb) + +(team["Away"][0].DefReb)}}</td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{team["Away"][0].Turnovers}}</td>
            <td>{{team["Away"][0].Fouls}}</td>
            <td></td>
          </tr>
          <tr class="totalRow">
            <td colspan="3">Total</td>
            <td>{{getTotal("Time", "Away") | secondsToDateTime | date:'mm:ss'}}</td>
            <td>{{getTotal("OnePtMade", "Away") + (+getTotal("TwoPtMade", "Away") * 2) + (+getTotal("ThreePtMade", "Away") * 3)}}</td>
            <td>{{+getTotal("TwoPtMade", "Away") + "/" +(+getTotal("TwoPtMiss", "Away")+ +getTotal("TwoPtMade", "Away"))}}</td>
            <td>{{+getTotal("ThreePtMade", "Away") + "/" +(+getTotal("ThreePtMiss", "Away")+ +getTotal("ThreePtMade", "Away"))}}</td>
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
      </div>
      <form action="saveGame.php" method="post">
        <div ng-repeat="player in team.Home">
          <div ng-repeat="(key,val) in player">
            <input name="playerHome{{$parent.$index}}[]" value="{{val}}" hidden>
          </div>
        </div>
        <div ng-repeat="player in team.Away">
          <div ng-repeat="(key,val) in player">
            <input name="playerAway{{$parent.$index}}[]" value="{{val}}" hidden>
          </div>
        </div>
        <div ng-repeat="item in gameLog">
          <input ng-if="item.length != 6" name="GameLog[]" value="{{item[0]}}" hidden>
          <input ng-if="item.length == 6" name="GameLog[]" value="{{item[0]}};{{item[5]}}" hidden>
        </div>
        <input name="homeTeamName" value="{{homeTeamName}}" hidden>
        <input name="awayTeamName" value="{{awayTeamName}}" hidden>
        <input name="gameNr" value="<?php echo $_GET['nr']; ?>" hidden> 
        <div style="text-align: center;">

        </div>
        <div style="text-align: center; width: 100%;" class="btn-group" >
          <div>
            <label for="linkToShare "><?php echo $lang['LINK_TO_SHARE']; ?>:</label>
            <input id="linkToShare" type="text" style="width: 350px; text-align: center; padding: 5px; border-radius: 5px; margin-top: 20px;" value="http://experienceweb.xyz/stats/live.php?nr=<?php echo $_GET['nr']; ?>">
          </div>
          <button type="button" style="width: 50%;" class="btn btn-primary" id="reportButton" ng-click="topdf()"><?php echo $lang['REPORT']; ?></button>
          <button type="button" style="width: 50%;" class="btn btn-info" id="gameLogReportButton" ng-click="gameLogPdf()"><?php echo $lang['GAME_LOG']; ?></button>
        </div>  
        <button class="btn btn-success" style="width: 100%;"><?php echo $lang['FINISH']; ?></button>
      </form>
    </div>

    <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular.min.js"></script>
    <script src="js/angular-translate.js"></script>
    <script src="js/gameApp.js"></script>
    <script src="js/popover.js"></script>
    <script src="js/hotkeys.min.js" type="text/javascript"></script>
    <script src="http://pubnub.github.io/angular-js/scripts/pubnub-angular.js"></script>
  </body>
  </html>