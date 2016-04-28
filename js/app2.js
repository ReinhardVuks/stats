var myApp = angular.module('myApp', ['cfp.hotkeys']);

myApp.controller('MyCtrl', function($scope, $timeout, hotkeys) {

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

  $scope.gameLog = ["Game is about to begin"];
  $scope.removeLastPlay = function(){
    if($scope.gameLog.length > 1){
      $scope.gameLog.splice(-1, 1);
    }
  }

  $scope.maxFouls = 5;
  $scope.nrOfPeriods = 4;
  $scope.maxOnCourtPlayers = 5;

  
  $scope.timeGame = $scope.initialGameTime;

  $scope.newTime = function(){
    $scope.timeGame = +($scope.timeGameMin * 60) + +$scope.timeGameSec;
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
    "Home": [],
    "Away": []
  };


  $scope.onCourt = {
    "Home": [],
    "Away": []
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

  $scope.changePeriod = function(n){
    if(n == 1 && $scope.period < $scope.nrOfPeriods){
      $scope.period++;
    } 
    if (n == 0 && $scope.nrOfPeriods > 1) {
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
      $scope.team["Home"][$scope.onCourt["Home"][i]]["TimeRuns"] = true;
      countdownHome($scope.onCourt["Home"][i]);
    }
    for (var i = 0; i < $scope.onCourt["Away"].length; i++) {
      $scope.team["Away"][$scope.onCourt["Away"][i]]["TimeRuns"] = true;
      countdownAway($scope.onCourt["Away"][i]);
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
    if ($scope.playerId != null && $scope.onCourt[$scope.playerTeam].indexOf($scope.playerId) !== -1) {
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
    if (made) {
      $scope.gameLog.push(pts+"Point Shot Made By: " + $scope.team[$scope.playerTeam][$scope.playerId]["Nr"]+ " " + $scope.team[$scope.playerTeam][$scope.playerId]["Name"]);
      $scope.gameLog.push("$" + $scope.homeTeamName + " " + $scope.getTotal("Points", "Away") + " - " + $scope.getTotal("Points", "Home") + " " + $scope.awayTeamName);
      $scope.team[$scope.playerTeam][$scope.playerId]["Points"] += pts;
      $scope.team[$scope.playerTeam][$scope.playerId][nrToName(pts) + "PtMade"]++;
      for (var i = 0; i < $scope.team[$scope.playerTeam].length; i++) {
        if ($scope.onCourt[$scope.playerTeam].indexOf(i) !== -1){
            $scope.team[$scope.playerTeam][i]["PlusMinus"] += pts;
        }
        if ($scope.onCourt[getOtherTeamName($scope.playerTeam)].indexOf(i) !== -1){
            $scope.team[getOtherTeamName($scope.playerTeam)][i]["PlusMinus"] -= pts;
        }
      }
    } else {
      $scope.gameLog.push(pts+"Point Shot Missed By: " + $scope.team[$scope.playerTeam][$scope.playerId]["Nr"] + " " + $scope.team[$scope.playerTeam][$scope.playerId]["Name"]);
      $scope.team[$scope.playerTeam][$scope.playerId][nrToName(pts) + "PtMiss"]++;
    }

    $scope.shotBool = true;
    $scope.fgBool = false;
  }

  $scope.addRbd = function(isOff) {
    if ( $scope.playerId != null && $scope.onCourt[$scope.playerTeam].indexOf($scope.playerId) !== -1) {
      if (isOff) {
        $scope.gameLog.push("Offensive Rebound to: " + $scope.team[$scope.playerTeam][$scope.playerId]["Nr"] + " " + $scope.team[$scope.playerTeam][$scope.playerId]["Name"]);
        $scope.team[$scope.playerTeam][$scope.playerId]["OffReb"]++;
      } else {
        $scope.gameLog.push("Defensive Rebound to: " + $scope.team[$scope.playerTeam][$scope.playerId]["Nr"] + " " + $scope.team[$scope.playerTeam][$scope.playerId]["Name"]);
        $scope.team[$scope.playerTeam][$scope.playerId]["DefReb"]++;
      }
    }
  };
  $scope.addAst = function() {
    if ( $scope.playerId != null && $scope.onCourt[$scope.playerTeam].indexOf($scope.playerId) !== -1) {
      $scope.gameLog.push("Assist Made by: " + $scope.team[$scope.playerTeam][$scope.playerId]["Nr"] + " " + $scope.team[$scope.playerTeam][$scope.playerId]["Name"]);
      $scope.team[$scope.playerTeam][$scope.playerId]["Assists"]++;
    }
  };
  $scope.addStl = function() {
    if ($scope.playerId != null && $scope.onCourt[$scope.playerTeam].indexOf($scope.playerId) !== -1) {
      $scope.gameLog.push(" Steal Made by: " + $scope.team[$scope.playerTeam][$scope.playerId]["Nr"] + " " + $scope.team[$scope.playerTeam][$scope.playerId]["Name"]);
      $scope.team[$scope.playerTeam][$scope.playerId]["Steals"]++;
    }
  };
  $scope.addBlk = function() {
    if ($scope.playerId != null && $scope.onCourt[$scope.playerTeam].indexOf($scope.playerId) !== -1) {
      $scope.gameLog.push("Blocked Shot to: " + $scope.team[$scope.playerTeam][$scope.playerId]["Nr"] + " " + $scope.team[$scope.playerTeam][$scope.playerId]["Name"]);  
      $scope.team[$scope.playerTeam][$scope.playerId]["Blocks"]++;
    }
  };
  $scope.addTo = function() {
    if ($scope.playerId != null && $scope.onCourt[$scope.playerTeam].indexOf($scope.playerId) !== -1) {
      $scope.gameLog.push("Turnover by: " + $scope.team[$scope.playerTeam][$scope.playerId]["Nr"] + " " + $scope.team[$scope.playerTeam][$scope.playerId]["Name"]);  
      $scope.team[$scope.playerTeam][$scope.playerId]["Turnovers"]++;
    }
  };
  $scope.addFls = function() {
    if ($scope.playerId != null && $scope.onCourt[$scope.playerTeam].indexOf($scope.playerId) !== -1) {
      if ($scope.team[$scope.playerTeam][$scope.playerId]["Fouls"] < $scope.maxFouls)
        $scope.gameLog.push("Foul Made by: " + $scope.team[$scope.playerTeam][$scope.playerId]["Nr"] + " " + $scope.team[$scope.playerTeam][$scope.playerId]["Name"]);
        $scope.team[$scope.playerTeam][$scope.playerId]["Fouls"]++;
    }
  };

  $scope.newPlayer = function(teamName, name, nr) {
    $scope.team[teamName].push({
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


  $scope.getTotal = function(x, teamName) {
    var total = 0;
    for (var i = 0; i < $scope.team[teamName].length; i++) {
      var team = $scope.team[teamName][i][x];
      total += +team;
    }
    return total;
  }

  $scope.edit = function(stat, up, playerId, team){
    if(up){
      $scope.team[team][playerId][stat]++;
    } else {
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
    combo: '6',
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
        if(!$scope.GameOn){
          $scope.start();
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
      if($scope.playerId > 0){
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
          for (var i = 0; i < $scope.team[teamName].length; i++) {
            var array = $.map($scope.team[teamName][i], function(value, index) {
              return [value];
            });

            var statline = [,,,,,,,,,,,,,,,]
            for (var j = 0; j < array.length; j++) {
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
                  statline[15] = array[j];
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
          total = [,,,,,,,,,,,,,,,,];
          total[1] = "Total:"
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
          
          total[15] = $scope.getTotal("Points", teamName);
          data.push(total)
          return data;
        }
        
        var doc = new jsPDF('p', 'pt');
        if($scope.team["Home"].length > 0){
          doc.text(35, 35, $scope.homeTeamName);
          doc.text(50, 60, $scope.getTotal("Points", "Home").toString());
          doc.autoTable(columns, getData("Home"), {startY: 100, pageBreak: 'avoid'});
        }
        if($scope.team["Away"].length > 0){
          doc.text(350, 35, $scope.awayTeamName);
          doc.text(365, 60, $scope.getTotal("Points", "Away").toString()); 
          doc.autoTable(columns, getData("Away"), {startY: 250+(15*getData("Home").length), pageBreak: 'avoid',});
        }
        doc.save("table.pdf");
  }

  $scope.gameLogPdf = function(){
      var data = [];
      for (var i = 0; i < $scope.gameLog.length; i++) {
        var tmp = [];
        $scope.gameLog[i].charAt(0) == "$" ? tmp.push($scope.gameLog[i].substring(1)) : tmp.push($scope.gameLog[i]);
        data.push(tmp);
      };

      var doc = new jsPDF('p', 'pt');
        doc.text(255, 60, "GAME LOG");
        doc.autoTable(["Play-By-Play"], data, {startY: 100, styles: {halign: 'center'}});
        doc.save("game_log.pdf");
  }
});
myApp.filter('secondsToDateTime', [function() {
  return function(seconds) {
    return new Date(1970, 0, 1).setSeconds(seconds);
  };
}])
