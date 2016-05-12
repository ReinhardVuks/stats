<!doctype html>
<html ng-app="app1">
<head>
  <title>Live feed</title>
  <script src="https://cdn.pubnub.com/pubnub.min.js"></script>
  <script src="https://cdn.pubnub.com/pubnub-crypto.min.js"></script>

  <script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>

  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.9/angular.min.js"></script>
  
  <script src="js/liveApp.js"></script>
  
  <script src="http://pubnub.github.io/angular-js/scripts/pubnub-angular.js"></script>
  
  <link rel="stylesheet" href="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="styles.css">
  <link href='http://fonts.googleapis.com/css?family=Lato' rel='stylesheet' type='text/css'>
  <style type="text/css">
  [ng\:cloak], [ng-cloak], [data-ng-cloak], [x-ng-cloak], .ng-cloak, .x-ng-cloak {
    display: none !important;
  }       
  </style> 
</head>
<body ng-cloak>
 
  
  <div ng-controller="ctrl1" class="container">
   <div class="row" ng-show="team.Home.length > 0 || team.Away.length > 0">
    <div style="float: left">
      <h1>{{homeTeamName}}</h1>
      <h1 style="text-align: center">{{getTotal("OnePtMade", "Home") + (+getTotal("TwoPtMade", "Home") * 2) + (+getTotal("ThreePtMade", "Home") * 3)}}</h1>
    </div>
    <div style="float: right">
      <h1>{{awayTeamName}}</h1>
      <h1 style="text-align: center">{{getTotal("OnePtMade", "Away") + (+getTotal("TwoPtMade", "Away") * 2) + (+getTotal("ThreePtMade", "Away") * 3)}}</h1>
    </div>
    <div  id="gameLog" style="overflow: scroll; text-align: center; border: 1px solid grey; width: 50%; height: 132px; margin: 20px auto; border-radius:10px;">
      <h4 ng-repeat="el in gameLog" id="lastPlay" style="width: 100%; border-bottom: 1px solid black; padding-bottom: 9px;">
        <span ng-show="gameLog[gameLog.length - 1].charAt(0) != '$'">
          {{el[0]}}
        </span>
      </h4>
    </div>
    <div class="col-lg-5">
      <table id="box-score" class="table table-striped table-bordered table-condensed table-responsive">
        <tr>
          <td id="name">#</td>
          <td>Name</td>
          <td>Time</td>
          <td>Pts</td>
          <td>2Pt</td>
          <td>3Pt</td>
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
        <tr ng-hide="$index == 0" ng-repeat="player in team.Home">
        	<td>{{player.Nr}}</td>
        	<td>{{player.Name}}</td>
        	<td>{{player.Time | secondsToDateTime | date:'mm:ss'}}</td>
        	<td>{{  +player.OnePtMade + (+player.TwoPtMade * 2) + (+player.ThreePtMade * 3)  }}</td>
        	<td>
            {{ ( (+player.TwoPtMade)) + "/" + ( (+player.TwoPtMade) + (+player.TwoPtMiss)) }}
          </td>
          <td>
            {{ ( (+player.ThreePtMade)) + "/" + ( (+player.ThreePtMade) + (+player.ThreePtMiss)) }}
          </td>
          <td> {{+player.OnePtMade + "/" + (+player.OnePtMade + +player.OnePtMiss)}}</td>
          <td>{{player.OffReb}}</td>
          <td>{{player.DefReb}}</td>
          <td>{{+player.OffReb + +player.DefReb}}</td>
          <td>{{player.Assists}}</td>
          <td>{{player.Steals}}</td>
          <td>{{player.Blocks}}</td>
          <td>{{player.Turnovers}}</td>
          <td>{{player.Fouls}}</td>
          <td>{{player.PlusMinus}}</td>
        </tr>
        <tr>
         <td colspan="3">Team</td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td>{{team["Home"][0].OffReb}}</td>
         <td>{{team["Home"][0].DefReb}}</td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td>{{team["Home"][0].Turnovers}}</td>
         <td>{{team["Home"][0].Fouls}}</td>
         <td></td>
       </tr>
       <tr class="totalRow">
        <td colspan="2">Total</td>
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
  <div class="col-lg-5">
    <table id="box-score" class="table table-striped table-bordered table-condensed table-responsive">
      <tr>
        <td id="name">#</td>
        <td>Name</td>
        <td>Time</td>
        <td>Pts</td>
        <td>2Pt</td>
        <td>3Pt</td>
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
      <tr ng-hide="$index == 0" ng-repeat="player in team.Away">
       <td>{{player.Nr}}</td>
       <td>{{player.Name}}</td>
       <td>{{player.Time | secondsToDateTime | date:'mm:ss'}}</td>
       <td>{{  +player.OnePtMade + (+player.TwoPtMade * 2) + (+player.ThreePtMade * 3)  }}</td>
       <td>
        {{ ( (+player.TwoPtMade)) + "/" + ( (+player.TwoPtMade) + (+player.TwoPtMiss)) }}
      </td>
      <td>
        {{ ( (+player.ThreePtMade)) + "/" + ( (+player.ThreePtMade) + (+player.ThreePtMiss)) }}
      </td>
      <td> {{+player.OnePtMade + "/" + (+player.OnePtMade + +player.OnePtMiss)}}</td>
      <td>{{player.OffReb}}</td>
      <td>{{player.DefReb}}</td>
      <td>{{+player.OffReb + +player.DefReb}}</td>
      <td>{{player.Assists}}</td>
      <td>{{player.Steals}}</td>
      <td>{{player.Blocks}}</td>
      <td>{{player.Turnovers}}</td>
      <td>{{player.Fouls}}</td>
      <td>{{player.PlusMinus}}</td>
    </tr>
    <tr>
      <td colspan="3">Team</td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td>{{team["Home"][0].OffReb}}</td>
      <td>{{team["Home"][0].DefReb}}</td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td>{{team["Home"][0].Turnovers}}</td>
      <td>{{team["Home"][0].Fouls}}</td>
      <td></td>
    </tr>
    <tr class="totalRow">
      <td colspan="2">Total</td>
      <td>{{getTotal("Time", "Away") | secondsToDateTime | date:'mm:ss'}}</td>
      <td>{{getTotal("OnePtMade", "Away") + (+getTotal("TwoPtMade", "Away") * 2) + (+getTotal("ThreePtMade", "Away") * 3)}}</td>

      <td>{{+getTotal("TwoPtMade", "Home") + "/" +(+getTotal("TwoPtMiss", "Home")+ +getTotal("TwoPtMade", "Home"))}}</td>
      <td>{{+getTotal("ThreePtMade", "Home") + "/" +(+getTotal("ThreePtMiss", "Home")+ +getTotal("ThreePtMade", "Home"))}}</td>
      <td>{{+getTotal("OnePtMade", "Away") + "/" + (+getTotal("OnePtMade", "Away") + +getTotal("OnePtMiss", "Away"))}}</td>
      <td>{{getTotal("OffReb", "Away")}}</td>
      <td>{{getTotal("DefReb", "Away")}}</td>
      <td>{{getTotal("OffReb", "Away") + getTotal("DefReb", "Away")}}</td>
      <td>{{getTotal("Assists", "Away")}}</td>
      <td>{{getTotal("Steals", "Away")}}</td>
      <td>{{getTotal("Blocks", "Away")}}</td>
      <td>{{getTotal("Turnovers", "Away")}}</td>
      <td>{{getTotal("Fouls", "Home")}}</td>
      <td></td>
    </tr>
  </table>
</div>
</div>
<div ng-show="team.Home.length == 0 && team.Away.length == 0">No Live Feed Available</div>
</div>

</body>
</html>