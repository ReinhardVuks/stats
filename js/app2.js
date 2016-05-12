var myApp = angular.module('myApp', ['cfp.hotkeys', 'pubnub.angular.service','pascalprecht.translate']);
myApp.config(['$translateProvider', function($translateProvider){
  // Adding a translation table for the English language
  $translateProvider.translations('en', {
   'SHOT_MADE' : '-point Shot Made By: ',
   'SHOT_MISSED' : '-point Shot Made By: ',
   'OFFENSIVE_REB' : 'Offensive Rebound to: ',
   'DEFENSIVE_REB' : 'Defensive Rebound to: ',
   'ASSIST' : 'Assist Made by: ',
   'STEAL' : 'Steal Made by: ',
   'BLOCK' : 'Blocked Shot to: ',
   'TURNOVER' : 'Turnover by: ',
   'FOUL' : 'Foul Made by: ',
  });
  // Adding a translation table for the Russian language
  $translateProvider.translations('ee', {
   'SHOT_MADE' : '-punkti vise sees: ',
   'SHOT_MISSED' : '-punkti vise möödas: ',
   'OFFENSIVE_REB' : 'Ründelaud: ',
   'DEFENSIVE_REB' : 'Kaitselaud: ',
   'ASSIST' : 'Resultatiivne sööt: ',
   'STEAL' : 'Vaheltlõige: ',
   'BLOCK' : 'Blokeeritud vise: ',
   'TURNOVER' : 'Pallikaotus: ',
   'FOUL' : 'Viga: ',
  });
  // Tell the module what language to use by default
  $translateProvider.preferredLanguage('en');
}])

myApp.controller('MyCtrl', function($rootScope, $scope, $timeout, $translate, hotkeys, PubNub) {

  $scope.setLang = function(langKey) {
    $translate.use(langKey);
  };


  //----------------LIVE-------------

  $scope.userId = "User " + Math.round(Math.random()*1000);
  $scope.channel = "Channel" + $_GET('nr');

   if (!$rootScope.initialized) {
    PubNub.init({
      subscribe_key: 'sub-c-874efbca-0242-11e6-8b0b-0619f8945a4f',
      publish_key: 'pub-c-5b2ab7df-8440-4d8f-9423-07992a42ee77',
      uuid:$scope.userId
    });
    $rootScope.initialized = true;
  }

  $scope.publish = function() {
    $scope.message =[$scope.team, $scope.gameLog, $scope.homeTeamName, $scope.awayTeamName];
  PubNub.ngPublish({
    channel: $scope.channel,
    message: $scope.message
  });
};

function $_GET(param) {
  var vars = {};
  window.location.href.replace( location.hash, '' ).replace( 
    /[?&]+([^=&]+)=?([^&]*)?/gi, // regexp
    function( m, key, value ) { // callback
      vars[key] = value !== undefined ? value : '';
    }
  );

  if ( param ) {
    return vars[param] ? vars[param] : null;  
  }
  return vars;
}


  //----------------LIVE-------------

  $scope.stats = [{
      "TIME" : false,
      "QUARTERS" : false,
      "POINTS" : false,
      "MISSED_SHOTS": false,
      "REBOUNDS" : false,
      "OFF_REBOUNDS" : false,
      "ASSISTS" : false,
      "STEALS" : false,
      "BLOCKS" : false,
      "FOULS" : false,
      "TURNOVERS" : false
    }
  ]

  $scope.GameOn = false;
  $scope.shotBool = true;
  $scope.fgBool = false;
  $scope.timersHome = {};
  $scope.timersAway = {};
  $scope.timerGame = {};

  $scope.gameLog = [];
  $scope.gameLogAdd = function($string){
    $scope.gameLog.push([$string]);
  }
  $scope.removeLastPlay = function(){
    if($scope.gameLog.length > 1){
      $scope.edit($scope.gameLog[$scope.gameLog.length - 1][1], $scope.gameLog[$scope.gameLog.length - 1][2], $scope.gameLog[$scope.gameLog.length - 1][3], $scope.gameLog[$scope.gameLog.length - 1][4]);
      if($scope.gameLog[$scope.gameLog.length - 1][1].indexOf("Made") > -1){
        var value = $scope.gameLog[$scope.gameLog.length - 1][0].split(":")[1][1];
        for (var i = 0; i < $scope.team[$scope.playerTeam].length; i++) {
          if ($scope.onCourt[$scope.playerTeam].indexOf(i) !== -1){
              $scope.team[$scope.gameLog[$scope.gameLog.length - 1][4]][i]["PlusMinus"] -= +value;
        
          }
        }
        for (var i = 0; i < $scope.team[getOtherTeamName($scope.playerTeam)].length; i++) {
          if ($scope.onCourt[getOtherTeamName($scope.playerTeam)].indexOf(i) !== -1){
              $scope.team[getOtherTeamName($scope.gameLog[$scope.gameLog.length - 1][4])][i]["PlusMinus"] += +value;
          }
        };
      }
      $scope.gameLog.splice(-1, 1);
    }
  }


  $scope.maxFouls = 5;
  $scope.nrOfPeriods = 4;
  $scope.maxOnCourtPlayers = 5;


  $scope.periodFouls = {"Home" : [0], "Away" : [0]};

  
  $scope.timeGame = $scope.initialGameTime;

  $scope.newTime = function(){
    var oldTime = $scope.timeGame;
    $scope.timeGame = +($scope.timeGameMin * 60) + +$scope.timeGameSec;
    var timeDiff =  (+oldTime - +$scope.timeGame);
    for (var i = 0; i < $scope.onCourt["Home"].length; i++) {
      if(i != 0){
        $scope.team["Home"][$scope.onCourt["Home"][i]]["Time"] = $scope.team["Home"][$scope.onCourt["Home"][i]]["Time"] + timeDiff;
      }
    }
    for (var i = 0; i < $scope.onCourt["Away"].length; i++) {
      if(i != 0){
        $scope.team["Away"][$scope.onCourt["Away"][i]]["Time"] = $scope.team["Away"][$scope.onCourt["Away"][i]]["Time"] + timeDiff;
      }
    }
}

  $scope.timeSplit = function(){
      $scope.timeGameMin = Math.floor(+$scope.timeGame / 60);
      $scope.timeGameSec = +$scope.timeGame % 60;

  }

  $scope.resetTime = function(){
    $scope.timeGame = $scope.initialGameTime;
  }

  $scope.period = 1;
  $scope.team = {
    "Home": [{"Nr" : "", "Name" : "Team", "TimeRuns": false,
      "Time": 0,
      "OnePtMade": 0,
      "OnePtMiss": 0,
      "TwoPtMade": 0,
      "TwoPtMiss": 0,
      "ThreePtMade": 0,
      "ThreePtMiss": 0,
      "OffReb": 0,
      "DefReb": 0,
      "Assists": 0,
      "Steals": 0,
      "Blocks": 0,
      "Turnovers" : 0,
      "Fouls": 0,
      "PlusMinus" : 0}],
    "Away": [{"Nr" : "", "Name" : "Team", "TimeRuns": false,
      "Time": 0,
      "OnePtMade": 0,
      "OnePtMiss": 0,
      "TwoPtMade": 0,
      "TwoPtMiss": 0,
      "ThreePtMade": 0,
      "ThreePtMiss": 0,
      "OffReb": 0,
      "DefReb": 0,
      "Assists": 0,
      "Steals": 0,
      "Blocks": 0,
      "Turnovers" : 0,
      "Fouls": 0,
      "PlusMinus" : 0}]
  };


  $scope.onCourt = {
    "Home": [0],
    "Away": [0]
  };

  function nrToName(nr) {
    switch(nr) {
      case 1:
        return "One";
      case 2:
        return "Two";
      case 3:
        return "Three";
      default:
        break;
    }
  }

  function getShortName(team){
    return team == "Home" ? $scope.homeTeamNameShort : $scope.awayTeamNameShort;
  }

  $scope.changePeriod = function(n){
    if(n == 1 && $scope.period < $scope.nrOfPeriods){
      $scope.period++;
      if($scope.periodFouls["Home"].length < $scope.period)
          $scope.periodFouls["Home"].push(0);
          $scope.periodFouls["Away"].push(0);
    } 
    if (n == 0 && $scope.period > 1) {
      $scope.period--;
    }
    }

  $scope.switch = function(nr, team) {
    if ($scope.onCourt[team].indexOf(nr) == -1 && $scope.onCourt[team].length <= 5) {
      $scope.onCourt[team].push(nr);
    } else {
      $scope.onCourt[team].splice($scope.onCourt[team].indexOf(nr), 1);
    }

  }

  function countdownHome(id) {
    $scope.team["Home"][id]["Time"]++;
    $scope.timersHome[id] = $timeout(countdownHome.bind(null, id), 1000);
  }

  function countdownAway(id) {
    $scope.team["Away"][id]["Time"]++;
    $scope.timersAway[id] = $timeout(countdownAway.bind(null, id), 1000);
  }

  function countdownGame(id){
    if($scope.timeGame == 0)
      $scope.stop();
    if($scope.GameOn){
    $scope.timeGame--;
    $scope.timerGame[id] = $timeout(countdownGame.bind(null, id), 1000);
  }
    
  }

  $scope.start = function() {
    $scope.GameOn = true;

    for (var i = 0; i < $scope.onCourt["Home"].length; i++) {
      if(i != 0){
        $scope.team["Home"][$scope.onCourt["Home"][i]]["TimeRuns"] = true;
        countdownHome($scope.onCourt["Home"][i]);
    }
    }
    for (var i = 0; i < $scope.onCourt["Away"].length; i++) {
      if(i != 0){
        $scope.team["Away"][$scope.onCourt["Away"][i]]["TimeRuns"] = true;
        countdownAway($scope.onCourt["Away"][i]);
      }
    }
    
    countdownGame(0);
  };

  $scope.stop = function() {

    $scope.GameOn = false;
    for (var i = 0; i < $scope.onCourt["Home"].length; i++) {

      $scope.team["Home"][$scope.onCourt["Home"][i]]["TimeRuns"] = false;
      $timeout.cancel($scope.timersHome[$scope.onCourt["Home"][i]]);
    }
    for (var i = 0; i < $scope.onCourt["Away"].length; i++) {
      $scope.team["Away"][$scope.onCourt["Away"][i]]["TimeRuns"] = false;
      $timeout.cancel($scope.timersAway[$scope.onCourt["Away"][i]]);
    }
    $timeout.cancel($scope.timerGame[0]);
  };

  $scope.playerId = null;
  $scope.playerTeam = null;
  $scope.selectedPlayer = function(id, team) {
    $scope.playerId = id;
    $scope.playerTeam = team;
  };

  $scope.addPts = function(pts) {
    if ($scope.playerId != null && $scope.onCourt[$scope.playerTeam].indexOf($scope.playerId) !== -1 && $scope.playerId != 0) {
      $scope.shotBool = false;
      $scope.fgBool = true;
      $scope.val = pts;
    }
  };

  function getOtherTeamName(teamName) {
    if (teamName == "Home") {
      return "Away";
    } else if (teamName == "Away") {
      return "Home";
    }
  }
  $scope.fg = function(made, pts) {
    var tmp = [];
    if (made) {
      $scope.team[$scope.playerTeam][$scope.playerId][nrToName(pts) + "PtMade"]++;
      tmp.push(getShortName($scope.playerTeam) + ": " + pts+ $translate.instant('SHOT_MADE') + "#" +  $scope.team[$scope.playerTeam][$scope.playerId]["Nr"]+ " " + $scope.team[$scope.playerTeam][$scope.playerId]["Name"] + " (" + $scope.getPlayerTotalPoints($scope.playerId, $scope.playerTeam) + ")");
      tmp.push(nrToName(pts) + "PtMade");
      tmp.push(false);
      tmp.push($scope.playerId);
      tmp.push($scope.playerTeam);
      tmp.push($scope.getTotalPoints("Home") + " - " + $scope.getTotalPoints("Away"));
      $scope.gameLog.push(tmp);
      for (var i = 0; i < $scope.team[$scope.playerTeam].length; i++) {
        if ($scope.onCourt[$scope.playerTeam].indexOf(i) !== -1){
            $scope.team[$scope.playerTeam][i]["PlusMinus"] += pts;
        }
      }
      for (var i = 0; i < $scope.team[getOtherTeamName($scope.playerTeam)].length; i++) {          
        if ($scope.onCourt[getOtherTeamName($scope.playerTeam)].indexOf(i) !== -1){
            $scope.team[getOtherTeamName($scope.playerTeam)][i]["PlusMinus"] -= pts;
        }
        };
    } else {
      tmp.push(getShortName($scope.playerTeam) + ": " + pts+ $translate.instant('SHOT_MISSED') + "#" + $scope.team[$scope.playerTeam][$scope.playerId]["Nr"] + " " + $scope.team[$scope.playerTeam][$scope.playerId]["Name"]);
      tmp.push(nrToName(pts) + "PtMade");
      tmp.push(null);
      tmp.push($scope.playerId);
      tmp.push($scope.playerTeam);
      $scope.gameLog.push(tmp);
      $scope.team[$scope.playerTeam][$scope.playerId][nrToName(pts) + "PtMiss"]++;
    }

    $scope.shotBool = true;
    $scope.fgBool = false;
    $scope.publish();
  }

  $scope.addRbd = function(isOff) {
    if ( $scope.playerId != null && $scope.onCourt[$scope.playerTeam].indexOf($scope.playerId) !== -1) {
      var tmp = [];
      if (isOff) {
        $scope.team[$scope.playerTeam][$scope.playerId]["OffReb"]++;
        tmp.push(getShortName($scope.playerTeam) + ": " + $translate.instant('OFFENSIVE_REB') + "#" + $scope.team[$scope.playerTeam][$scope.playerId]["Nr"] + " " + $scope.team[$scope.playerTeam][$scope.playerId]["Name"] + " (Off: " + $scope.team[$scope.playerTeam][$scope.playerId]["OffReb"] + ", Tot: " + (+$scope.team[$scope.playerTeam][$scope.playerId]["OffReb"] + +$scope.team[$scope.playerTeam][$scope.playerId]["DefReb"]) +")");
        tmp.push("OffReb");
      } else {
        $scope.team[$scope.playerTeam][$scope.playerId]["DefReb"]++;
        tmp.push(getShortName($scope.playerTeam) + ": " + $translate.instant('DEFENSIVE_REB') + "#" + $scope.team[$scope.playerTeam][$scope.playerId]["Nr"] + " " + $scope.team[$scope.playerTeam][$scope.playerId]["Name"] + " (Def: " + $scope.team[$scope.playerTeam][$scope.playerId]["DefReb"] + ", Tot: " + (+$scope.team[$scope.playerTeam][$scope.playerId]["OffReb"] + +$scope.team[$scope.playerTeam][$scope.playerId]["DefReb"]) +")");
        tmp.push("DefReb");
      }
      tmp.push(false);
      tmp.push($scope.playerId);
      tmp.push($scope.playerTeam);
      $scope.gameLog.push(tmp);
    }
    $scope.publish();
  };
  $scope.addAst = function() {
    if ( $scope.playerId != null && $scope.onCourt[$scope.playerTeam].indexOf($scope.playerId) !== -1 && $scope.playerId != 0) {
      $scope.team[$scope.playerTeam][$scope.playerId]["Assists"]++;
      var tmp = [];
      tmp.push(getShortName($scope.playerTeam) + ": " + $translate.instant('ASSIST') + "#" + $scope.team[$scope.playerTeam][$scope.playerId]["Nr"] + " " + $scope.team[$scope.playerTeam][$scope.playerId]["Name"] + " (" + $scope.team[$scope.playerTeam][$scope.playerId]["Assists"] + ")");
      tmp.push("Assists");
      tmp.push(false);
      tmp.push($scope.playerId);
      tmp.push($scope.playerTeam);
      $scope.gameLog.push(tmp);
    }
    $scope.publish();
  };
  $scope.addStl = function() {
    if ($scope.playerId != null && $scope.onCourt[$scope.playerTeam].indexOf($scope.playerId) !== -1 && $scope.playerId != 0) {
      $scope.team[$scope.playerTeam][$scope.playerId]["Steals"]++;
      var tmp = [];
      tmp.push(getShortName($scope.playerTeam) + ": " + $translate.instant('STEAL') + "#" + $scope.team[$scope.playerTeam][$scope.playerId]["Nr"] + " " + $scope.team[$scope.playerTeam][$scope.playerId]["Name"] + " (" + $scope.team[$scope.playerTeam][$scope.playerId]["Steals"] + ")");
      tmp.push("Steals");
      tmp.push(false);
      tmp.push($scope.playerId);
      tmp.push($scope.playerTeam);
      $scope.gameLog.push(tmp);
    }
    $scope.publish();
  };
  $scope.addBlk = function() {
    if ($scope.playerId != null && $scope.onCourt[$scope.playerTeam].indexOf($scope.playerId) !== -1 && $scope.playerId != 0) {
      $scope.team[$scope.playerTeam][$scope.playerId]["Blocks"]++;
      var tmp = [];
      tmp.push(getShortName($scope.playerTeam) + ": " + $translate.instant('BLOCK') + "#" + $scope.team[$scope.playerTeam][$scope.playerId]["Nr"] + " " + $scope.team[$scope.playerTeam][$scope.playerId]["Name"] + " (" + $scope.team[$scope.playerTeam][$scope.playerId]["Blocks"] + ")");  
      tmp.push("Blocks");
      tmp.push(false);
      tmp.push($scope.playerId);
      tmp.push($scope.playerTeam);
      $scope.gameLog.push(tmp);
    }
    $scope.publish();
  };
  $scope.addTo = function() {
    if ($scope.playerId != null && $scope.onCourt[$scope.playerTeam].indexOf($scope.playerId) !== -1) {
      $scope.team[$scope.playerTeam][$scope.playerId]["Turnovers"]++;
      var tmp = [];
      tmp.push(getShortName($scope.playerTeam) + ": " + $translate.instant('TURNOVER') + "#" + $scope.team[$scope.playerTeam][$scope.playerId]["Nr"] + " " + $scope.team[$scope.playerTeam][$scope.playerId]["Name"] + " (" + $scope.team[$scope.playerTeam][$scope.playerId]["Turnovers"] + ")");  
      tmp.push("Turnovers");
      tmp.push(false);
      tmp.push($scope.playerId);
      tmp.push($scope.playerTeam);
      $scope.gameLog.push(tmp);
    }
    $scope.publish();
  };
  $scope.addFls = function() {
    if ($scope.playerId != null && $scope.onCourt[$scope.playerTeam].indexOf($scope.playerId) !== -1) {
      if ($scope.team[$scope.playerTeam][$scope.playerId]["Fouls"] < $scope.maxFouls || $scope.playerId == 0){
        $scope.team[$scope.playerTeam][$scope.playerId]["Fouls"]++;
        $scope.periodFouls[$scope.playerTeam][$scope.period - 1]++;
        var tmp = [];
        tmp.push(getShortName($scope.playerTeam) + ": " + $translate.instant('FOUL') + "#" + $scope.team[$scope.playerTeam][$scope.playerId]["Nr"] + " " + $scope.team[$scope.playerTeam][$scope.playerId]["Name"] + " (" + $scope.team[$scope.playerTeam][$scope.playerId]["Fouls"] + ")");
        tmp.push("Fouls");
        tmp.push(false);
        tmp.push($scope.playerId);
        tmp.push($scope.playerTeam);
        $scope.gameLog.push(tmp);
      }
    }
    $scope.publish();
  };

  $scope.newPlayer = function(teamName, name, nr) {
    $scope.team[teamName].push({
      "Nr": nr == null ? 0 : +nr,
      "Name": name,
      "TimeRuns": false,
      "Time": 0,
      "OnePtMade": 0,
      "OnePtMiss": 0,
      "TwoPtMade": 0,
      "TwoPtMiss": 0,
      "ThreePtMade": 0,
      "ThreePtMiss": 0,
      "OffReb": 0,
      "DefReb": 0,
      "Assists": 0,
      "Steals": 0,
      "Blocks": 0,
      "Turnovers" : 0,
      "Fouls": 0,
      "PlusMinus" : 0
    });
    $scope.Name = null;
    $scope.Nr = null;
  }

  $scope.removePlayer = function(id){
    $scope.team[$scope.playerTeam].splice(id, 1);
  }


  $scope.getTotal = function(stat, teamName) {
    var total = 0;
    for (var i = 0; i < $scope.team[teamName].length; i++) {
      var team = $scope.team[teamName][i][stat];
      total += +team;
    }
    return total;
  }

  $scope.getPlayerTotalPoints = function(player, team){
    return $scope.team[team][player]["OnePtMade"] + (+$scope.team[team][player]["TwoPtMade"] * 2) + (+$scope.team[team][player]["ThreePtMade"] * 3);
  }

  $scope.getTotalPoints = function(teamName){
    return $scope.getTotal("OnePtMade", teamName) + (+$scope.getTotal("TwoPtMade", teamName) * 2) + (+$scope.getTotal("ThreePtMade", teamName) * 3);
  }

  $scope.edit = function(stat, up, playerId, team){
    if(up){
      $scope.team[team][playerId][stat]++;
    } else if (up != null) {
      if($scope.team[team][playerId][stat] > 0)
        $scope.team[team][playerId][stat]--; 
    }
  }

hotkeys.add({
    combo: '1',
    description: 'Free throw',
    callback: function(){
      if($scope.playerId != null && $scope.team[$scope.playerTeam][$scope.playerId]["Fouls"] < $scope.maxFouls){
        if(!$scope.stats["MISSED_SHOTS"]){
          $scope.fg(true, 1);
        } else {
          $scope.addPts(1);
        }
      }
        
    }
  });
  hotkeys.add({
    combo: '2',
    description: 'Two Point Shot',
    callback: function(){
      if($scope.playerId != null && $scope.team[$scope.playerTeam][$scope.playerId]["Fouls"] < $scope.maxFouls){
        if(!$scope.stats["MISSED_SHOTS"]){
          $scope.fg(true, 2);
        } else {
          $scope.addPts(2);
        }
      }
    }
  });
  hotkeys.add({
    combo: '3',
    description: 'Three Point Shot',
    callback: function(){
      if($scope.playerId != null && $scope.team[$scope.playerTeam][$scope.playerId]["Fouls"] < $scope.maxFouls){
          if(!$scope.stats["MISSED_SHOTS"]){
            $scope.fg(true, 3);
          } else {
            $scope.addPts(3);
          }
        }
      }
  });
  hotkeys.add({
    combo: 'q',
    description: 'Missed Shot',
    callback: function(){
      if($scope.playerId != null && $scope.fgBool && $scope.team[$scope.playerTeam][$scope.playerId]["Fouls"] < $scope.maxFouls){
        if($scope.stats['MISSED_SHOTS'])
          $scope.fg(false, $scope.val);
      }
    }
  });
  hotkeys.add({
    combo: 'w',
    description: 'Made Shot',
    callback: function(){
      if($scope.playerId != null && $scope.fgBool && $scope.team[$scope.playerTeam][$scope.playerId]["Fouls"] < $scope.maxFouls){
        if($scope.stats['MISSED_SHOTS'])
          $scope.fg(true, $scope.val);
      }
    }
  });
  hotkeys.add({
    combo: '4',
    description: 'Add Offensive Rebound',
    callback: function(){
      if($scope.playerId != null && $scope.team[$scope.playerTeam][$scope.playerId]["Fouls"] < $scope.maxFouls){
        if($scope.stats['OFF_REBOUNDS'])
          $scope.addRbd(true);
      }
    }
  });
  hotkeys.add({
    combo: '5',
    description: 'Add Defensive Rebound',
    callback: function(){
      if($scope.playerId != null && $scope.team[$scope.playerTeam][$scope.playerId]["Fouls"] < $scope.maxFouls){
        if($scope.stats['REBOUNDS'])
          $scope.addRbd(false);
      }
    }
  });
  hotkeys.add({
    combo: '6',
    description: 'Add Assists',
    callback: function(){
      if($scope.playerId != null && $scope.team[$scope.playerTeam][$scope.playerId]["Fouls"] < $scope.maxFouls){
        if($scope.stats['ASSISTS'])
          $scope.addAst();
      }
    }
  });
  hotkeys.add({
    combo: '7',
    description: 'Add Steals',
    callback: function(){
      if($scope.playerId != null && $scope.team[$scope.playerTeam][$scope.playerId]["Fouls"] < $scope.maxFouls){
        if($scope.stats['STEALS'])
          $scope.addStl();
      }
    }
  });
  hotkeys.add({
    combo: '8',
    description: 'Add Blocks',
    callback: function(){
      if($scope.playerId != null && $scope.team[$scope.playerTeam][$scope.playerId]["Fouls"] < $scope.maxFouls){
        if($scope.stats['BLOCKS'])
          $scope.addBlk();
      }
    }
  });
  hotkeys.add({
    combo: '9',
    description: 'Add Turnover',
    callback: function(){
      if($scope.playerId != null && $scope.team[$scope.playerTeam][$scope.playerId]["Fouls"] < $scope.maxFouls){
        if($scope.stats['TURNOVERS'])
          $scope.addTo();
      }
    }
  });
  hotkeys.add({
    combo: '0',
    description: 'Add Fouls',
    callback: function(){
      if($scope.playerId != null && $scope.team[$scope.playerTeam][$scope.playerId]["Fouls"] < $scope.maxFouls){
        if($scope.stats['FOULS'])
          $scope.addFls();
      }
    }
  });
  hotkeys.add({
    combo: 'space',
    description: 'Game Clock On/Off',
    callback: function(){
      if($scope.stats['TIME']){
        if(!$scope.GameOn && $scope.playerId != null ){
          if($scope.onCourt.Home.length > 1 || $scope.onCourt.Away.length > 1 || $scope.playerId != 0){
            
          $scope.start();
           }
        } else {
          $scope.stop();
        }
        }
    }
  });
  hotkeys.add({
    combo: 'right',
    description: 'Switch Active Player',
    callback: function(){
      if($scope.playerId < $scope.team[$scope.playerTeam].length-1){
       $scope.playerId++;
      }
    }
  });

  hotkeys.add({
    combo: 'left',
    description: 'Switch Active Player',
    callback: function(){
      if($scope.playerId > 1){
      $scope.playerId--;
    }
    }
  });

  hotkeys.add({
    combo: 'up',
    description: 'Switch Active Team',
    callback: function(){
      if($scope.team["Home"].length > 0)
      $scope.playerTeam = "Home";
    }
  });

  hotkeys.add({
    combo: 'down',
    description: 'Switch Active Team',
    callback: function(){
      if($scope.team["Away"].length > 0)
        $scope.playerTeam = "Away";
    }
  });

  $scope.topdf = function(){
        columnsInital = Object.keys($scope.team["Home"][0]);
        columnsInital.push("Points");
        var columns = ["#", "Player", "Min", "2p", "3p", "1p", "Def", "Off", "Tot", "Ast", "Stl", "Blk", "TO", "Fls", "+/-", "Pts"];
        
        var secToMin = function(totalSeconds) {
          var minutes = Math.floor(totalSeconds  / 60);
          var seconds = totalSeconds - (minutes * 60);

          // round seconds
          seconds = Math.round(seconds * 100) / 100

          var result = (minutes < 10 ? "0" + minutes : minutes);
              result += ":" + (seconds  < 10 ? "0" + seconds : seconds);
          return result;
        }

        function getData(teamName){

          var data = [];
          for (var i = 1; i <= $scope.team[teamName].length-1; i++) {
            var array = $.map($scope.team[teamName][i], function(value, index) {
              return [value];
            });
            var statline = [,,,,,,,,,,,,,,,]
            for (var j = 0; j <= array.length; j++) {
              switch(columnsInital[j]){
                case "Name":
                  statline[1] = array[j];
                  break;
                case "Nr":
                  statline[0] = array[j];
                  break;
                case "Time":
                  statline[2] = secToMin(array[j]);
                  break;
                case "Points":
                  statline[15] = array[4] + (array[6]*2) + (array[8] * 3);
                  break;
                case "OnePtMade":
                  statline[5] = array[j] + "-" + (array[j+1] + array[j]);
                  break;
                case "TwoPtMade":
                  statline[3] = array[j] + "-" + (array[j+1] + array[j]);
                  break;
                case "ThreePtMade":
                  statline[4] = array[j] + "-" + (array[j+1] + array[j]);
                  break;
                case "OffReb":
                  statline[7] = array[j];
                  break;
                case "DefReb":
                  statline[6] = array[j];
                  statline[8] = array[j] + array[j-1];
                  break;
                case "Assists":
                  statline[9] = array[j];
                  break;
                case "Steals":
                  statline[10] = array[j];
                  break;
                case "Blocks":
                  statline[11] = array[j];
                  break;
                case "Turnovers":
                  statline[12] = array[j];
                  break;
                case "Fouls":
                  statline[13] = array[j];
                  break;
                case "PlusMinus":
                  statline[14] = array[j];
                  break;
              }
            };
            data.push(statline);
          };
          team = [,,,,,,,,,,,,,,,,];
          team[1] = "Team";
          team[6] = $scope.team[teamName][0]["DefReb"];
          team[7] = $scope.team[teamName][0]["OffReb"];
          team[8] = (+$scope.team[teamName][0]["OffReb"] + +$scope.team[teamName][0]["DefReb"]);
          team[12] = $scope.team[teamName][0]["Turnovers"];
          team[13] = $scope.team[teamName][0]["Fouls"];
          data.push(team);

          total = [,,,,,,,,,,,,,,,,];
          total[1] = "Total";
          total[3] = $scope.getTotal("TwoPtMade", teamName) + "-" + (+$scope.getTotal("TwoPtMade", teamName) + +$scope.getTotal("TwoPtMiss", teamName));
          total[4] = $scope.getTotal("ThreePtMade", teamName) + "-" + (+$scope.getTotal("ThreePtMade", teamName) + +$scope.getTotal("ThreePtMiss", teamName));
          total[5] = $scope.getTotal("OnePtMade", teamName) + "-" + (+$scope.getTotal("OnePtMade", teamName) + +$scope.getTotal("OnePtMiss", teamName));
          total[6] = $scope.getTotal("DefReb", teamName);
          total[7] = $scope.getTotal("OffReb", teamName);
          total[8] = +$scope.getTotal("OffReb", teamName) + +$scope.getTotal("DefReb", teamName);
          total[9] = $scope.getTotal("Assists", teamName);
          total[10] = $scope.getTotal("Steals", teamName);
          total[11] = $scope.getTotal("Blocks", teamName);
          total[12] = $scope.getTotal("Turnovers", teamName);
          total[13] = $scope.getTotal("Fouls", teamName);
          
          total[15] = $scope.getTotal("OnePtMade", teamName) + (+$scope.getTotal("TwoPtMade", teamName) * 2) + (+$scope.getTotal("ThreePtMade", teamName) * 3);
          data.push(total)
          return data;
        }
        
        var doc = new jsPDF('p', 'pt');
        if($scope.team["Home"].length > 0){
          doc.text(35, 35, $scope.homeTeamName);
          doc.text(50, 60, ($scope.getTotal("OnePtMade", "Home") + (+$scope.getTotal("TwoPtMade", "Home") * 2) + (+$scope.getTotal("ThreePtMade", "Home") * 3)).toString());
          doc.autoTable(columns, getData("Home"), {startY: 100, pageBreak: 'avoid'});
        }
        if($scope.team["Away"].length > 0){
          doc.text(350, 35, $scope.awayTeamName);
          doc.text(365, 60, ($scope.getTotal("OnePtMade", "Away") + (+$scope.getTotal("TwoPtMade", "Away") * 2) + (+$scope.getTotal("ThreePtMade", "Away") * 3)).toString()); 
          doc.autoTable(columns, getData("Away"), {startY: 250+(15*getData("Home").length), pageBreak: 'avoid',});
        }
        doc.save("table.pdf");
  }

  $scope.gameLogPdf = function(){
      var data = [];
      for (var i = 0; i < $scope.gameLog.length; i++) {
        var tmp = [];
        if($scope.gameLog[i].length == 6){
          tmp.push($scope.gameLog[i][5]);
        } else {
          tmp.push("");
        }
        tmp.push($scope.gameLog[i][0]);
        data.push(tmp);
      };

      var doc = new jsPDF('p', 'pt');
        doc.text(255, 60, "GAME LOG");
        if($scope.team["Home"].length > 0){
          doc.text(120, 90, $scope.homeTeamName);
        }
        if($scope.team["Away"].length > 0){
          doc.text(370, 90, $scope.awayTeamName);
        }
        doc.autoTable(["Score","Play-By-Play"], data, {startY: 100, styles: {halign: 'left'}});
        doc.save("game_log.pdf");
  }


  $scope.count = 0;
});
myApp.filter('secondsToDateTime', [function() {
  return function(seconds) {
    return new Date(1970, 0, 1).setSeconds(seconds);
  };
}])
