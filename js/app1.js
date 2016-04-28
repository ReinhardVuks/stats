var myApp = angular.module('myApp', []);

myApp.controller('MyCtrl', function($scope, $timeout) {

  $scope.minuvar = "";

$scope.GameOn = false;
  $scope.shotBool = true;
  $scope.fgBool = false;
  $scope.timers = {};
  $scope.team = []
  
  $scope.onCourt = [];
  
  $scope.switch = function(nr){
    if($scope.onCourt.indexOf(nr) == -1 && $scope.onCourt.length <= 5){
    $scope.onCourt.push(nr);
    } else {
    $scope.onCourt.splice($scope.onCourt.indexOf(nr), 1);
    }
    
  }
  
  function countdown(id) {
    $scope.team[id]["Time"]++;
    $scope.timers[id] = $timeout(countdown.bind(null, id), 1000);
  }

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

  $scope.start = function() {
  $scope.GameOn = true;
  for(var i = 0; i < $scope.onCourt.length; i++){
    $scope.team[$scope.onCourt[i]]["TimeRuns"] = true;
    countdown($scope.onCourt[i]);
    }
  };

  $scope.stop = function() {
  
  $scope.GameOn = false;
  for(var i = 0; i < $scope.onCourt.length; i++){
    $scope.team[$scope.onCourt[i]]["TimeRuns"] = false;
    $timeout.cancel($scope.timers[$scope.onCourt[i]]);
    }
  };

  $scope.playerId = null;
  $scope.selectedPlayer = function(id) {
    $scope.playerId = id;

  };

  $scope.addPts = function(pts) {
    if ($scope.playerId != null && $scope.team[$scope.playerId]["TimeRuns"]) {
      $scope.shotBool = false;
      $scope.fgBool = true;
      $scope.val = pts
    }

  };

  $scope.fg = function(made, pts) {
    if (made) {
      $scope.team[$scope.playerId]["Points"] += pts;
      $scope.team[$scope.playerId][nrToName(pts) + "PtMade"]++;
      for (var i = 0; i < $scope.team.length; i++) {
        if($scope.team[i]["TimeRuns"]){
          $scope.team[i]["PlusMinus"] += pts;
          
          //TODO: vastasmeeskonna tabamise korral
      }
      }
    } else {
      $scope.team[$scope.playerId][nrToName(pts) + "PtMiss"]++;
    }
      
    $scope.shotBool = true;
    $scope.fgBool = false;
  }

  $scope.addRbd = function(isOff) {
  if($scope.team[$scope.playerId]["TimeRuns"]){
    if (isOff) {
      $scope.team[$scope.playerId]["OffReb"]++;
    } else {
      $scope.team[$scope.playerId]["DefReb"]++;
    }
  }
  };
  $scope.addAst = function() {
  if($scope.team[$scope.playerId]["TimeRuns"]){
    $scope.team[$scope.playerId]["Assists"]++;
    }
  };
  $scope.addStl = function() {
  if($scope.team[$scope.playerId]["TimeRuns"]){
    $scope.team[$scope.playerId]["Steals"]++;
   }
  };
  $scope.addBlk = function() {
  if($scope.team[$scope.playerId]["TimeRuns"]){
    $scope.team[$scope.playerId]["Blocks"]++;
    }
  };
  $scope.addFls = function() {
  if($scope.team[$scope.playerId]["TimeRuns"]){
    if ($scope.team[$scope.playerId]["Fouls"] < 5)
      $scope.team[$scope.playerId]["Fouls"]++;
  }
  };

  $scope.newPlayer = function(name, nr) {
    console.log(name);
    $scope.team.push({
      "Nr": nr == null ? 0 : +nr,
      "Name": name,
      "TimeRuns": false,
      "Time": 0,
      "Points": 0,
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
      "Fouls": 0,
      "PlusMinus" : 0
    });
    $scope.Name = null;
    $scope.Nr = null;
  }

  $scope.removePlayer = function(id){
    $scope.team.splice(id, 1);
  }

  $scope.getTotal = function(x) {
    var total = 0;
    for (var i = 0; i < $scope.team.length; i++) {
      var team = $scope.team[i][x];
      total += +team;
    }
    return total;
  }
  
  

});
myApp.filter('secondsToDateTime', [function() {
  return function(seconds) {
    return new Date(1970, 0, 1).setSeconds(seconds);
  };
}])
