<?php
session_start();
?>

<html ng-app="myApp" ng-cloak>
<head>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
<link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
	<div class="container" ng-app ng-controller="MyCtrl" >
    <?php
    $teamArray = $_POST['team'];
    for ($i=0; $i < count($teamArray); $i++) { 
      $player = explode(', ', $teamArray[$i]);
      ?>
      <div ng-init="newPlayer(  '<?php echo $player[0]; ?>', <?php echo $player[1]; ?> )"></div>
      <?php
    }
    $values = array();
    foreach ($_POST as $key => $value) {
      if($key != "team"){
        if($value == "true"){
          $values[$key] = true;
        }else{
          $values[$key] = false;
        }
      }
    }
    ?>
<div class="row">
  <div class="col-md-5">
  </div>

  <div class="col-md-5">
  <div ng-class="{'btn-disable' : onCourt.indexOf(playerId) <= -1, 'btn-disable2' : team[playerId]['Fouls'] >= 5}">
    <button class="btn" ng-click="start()" ng-hide="team[playerId]['TimeRuns']">Start</button>
    <button id="stopButton" class="btn" ng-click="stop()" ng-show="team[playerId]['TimeRuns']">Stop</button>
    <div ng-show="<?php echo $values['points']; ?>">
    <div class="btn-group" ng-model="shot" ng-show="shotBool">
      <button ng-show="<?php echo $values['free_throws']; ?>" class="btn btn-success" ng-click="addPts(1)">1pt</button>
      <button ng-show="<?php echo $values['two_pt_shots']; ?>" class="btn btn-success" ng-click="addPts(2)">2pt</button>
      <button ng-show="<?php echo $values['three_pt_shots']; ?>" class="btn btn-success" ng-click="addPts(3)">3pt</button>
    </div>
    <div class="btn-group" ng-model="fg" ng-show="fgBool">
      <button class="btn btn-danger" ng-click="fg(false, val)">Miss</button>
      <button class="btn btn-success" ng-click="fg(true, val)">Made</button>
    </div>
    </div>
    <div ng-show="<?php echo $values['rebounds']; ?>" class="btn-group" >
      <button class="btn btn-success" ng-click="addRbd(true)">Off.</button>
      <button ng-show="<?php echo $values['off_rebounds']; ?>" class="btn btn-success" ng-click="addRbd(false)">Def.</button>
    </div>
    <button ng-show="<?php echo $values['assists']; ?>" class="btn btn-success" ng-click="addAst()">Assist</button>
    <button ng-show="<?php echo $values['steals']; ?>" class="btn btn-success" ng-click="addStl()">Steal</button>
    <button ng-show="<?php echo $values['blocks']; ?>" class="btn btn-success" ng-click="addBlk()">Block</button>
    <button ng-show="<?php echo $values['fouls']; ?>" class="btn btn-warning" ng-click="addFls()">Foul</button>

  </div>
  <table class="table table-bordered table-responsive">
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
      <td>Fls</td>
      <td>+/-</td>
    </tr>
    <tr ng-repeat="player in team" ng-click="selectedPlayer($index)" ng-class="{'selected': playerId == $index, 'out':inGame, 'inGame' : onCourt.indexOf($index) > -1, 'btn-disable' : onCourt.indexOf($index) == -1}">
    <td>
      <input ng-disabled="onCourt.length == 5 && onCourt.indexOf($index) <= -1 || GameOn" ng-click="switch($index)" type="checkbox">
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
      <td>
        {{player.OffReb}}
      </td>
      <td>
        {{player.DefReb}}
      </td>
      <td>
        
        {{+player.OffReb + +player.DefReb}}
      
      </td>
      <td>
        {{player.Assists}}
      </td>
      <td>
        {{player.Steals}}
      </td>
      <td>
        {{player.Blocks}}
      </td>
      <td>
        {{player.Fouls}}
      </td>
      <td>
        {{player.PlusMinus}}
      </td>
      <td ng-show="playerId == $index">
          <span class="glyphicon glyphicon-remove" ng-click="removePlayer($index)">
      </td> 
    </tr>
    <tr class="totalRow">
      <td colspan="3">Total</td>
      <td>{{getTotal("Time") | secondsToDateTime | date:'mm:ss'}}</td>
      <td>{{getTotal("Points")}}</td>
      <td>{{+getTotal("TwoPtMade") + +getTotal("ThreePtMade") + "/" +(+getTotal("TwoPtMiss") + +getTotal("ThreePtMiss") + +getTotal("TwoPtMade") + +getTotal("ThreePtMade"))}}</td>
      <td>{{+getTotal("OnePtMade") + "/" + (+getTotal("OnePtMade") + +getTotal("OnePtMiss"))}}</td>
      <td>{{getTotal("OffReb")}}</td>
      <td>{{getTotal("DefReb")}}</td>
      <td>{{getTotal("OffReb") + getTotal("DefReb")}}</td>
      <td>{{getTotal("Assists")}}</td>
      <td>{{getTotal("Steals")}}</td>
      <td>{{getTotal("Blocks")}}</td>
      <td>{{getTotal("Fouls")}}</td>
      <td></td>
    </tr>
  </table>
  </div>
</div>

</div>


	<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.9/angular.min.js"></script>
	<script src="js/app1.js"></script>
</body>
</html>