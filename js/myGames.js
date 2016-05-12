var myApp = angular.module('myGamesApp', []);

myApp.controller('Ctrl1', function($scope, $http) {

	$scope.myGames = [];

	$scope.addGame = function(nr, date, homeTeamName, awayTeamName){
		$scope.myGames.push({
			"GameNr" : nr,
			"GameDate" : date,
      "HomeTeamName" : homeTeamName,
      "AwayTeamName" : awayTeamName
		});
	}
  $scope.createGameLog = function(gameNr){
    $http.get("http://experienceweb.xyz/stats/getGame.php?gameid="+gameNr)
   .then(function (response) {
    $scope.gameLog = response.data.game_log;
    for (var i = 0; i < $scope.myGames.length; i++) {
      if($scope.myGames[i].GameNr == gameNr){
        $scope.homeTeamName = $scope.myGames[i].HomeTeamName;
        $scope.awayTeamName = $scope.myGames[i].AwayTeamName;
        
      }
    };
    var data = [];
      for (var i = 0; i < $scope.gameLog.length; i++) {
        var items = $scope.gameLog[i][0].split(";");
      
        var tmp = [];
        if(items.length == 2){
          tmp.push(items[1]);
        } else {
          tmp.push("");
        }
        tmp.push(items[0]);
        data.push(tmp);
      }
        var doc = new jsPDF('p', 'pt');
        doc.text(255, 60, "GAME LOG");
        if($scope.homeTeamName.length > 0){
          doc.text(120, 90, $scope.homeTeamName);
        }
        if($scope.awayTeamName.length > 0){
          doc.text(370, 90, $scope.awayTeamName);
        }
        doc.autoTable(["Score","Play-By-Play"], data, {startY: 100, styles: {halign: 'left'}});
        doc.save("game_log.pdf");

  })};

  $scope.createBoxScore = function(gameNr){
    $http.get("http://experienceweb.xyz/stats/getGame.php?gameid="+gameNr)
   .then(function (response) {
    $scope.players = response.data.stats;
    for (var i = 0; i < $scope.myGames.length; i++) {
      if($scope.myGames[i].GameNr == gameNr){
        $scope.homeTeamName = $scope.myGames[i].HomeTeamName;
        $scope.awayTeamName = $scope.myGames[i].AwayTeamName;
        
      }
    };

    $scope.team = {
      "Home": [],
      "Away": []
    };
    for (var i = 0; i < $scope.players.length; i++) {
      $scope.newPlayer($scope.players[i].Team, $scope.players[i].Name, $scope.players[i].Nr, $scope.players[i].Time, $scope.players[i].OnePtMade, $scope.players[i].OnePtMiss, $scope.players[i].TwoPtMade, $scope.players[i].TwoPtMiss, $scope.players[i].ThreePtMade, $scope.players[i].ThreePtMiss, $scope.players[i].OffRebs, $scope.players[i].DefRebs, $scope.players[i].Assists, $scope.players[i].Steals, $scope.players[i].Blocks, $scope.players[i].Turnovers, $scope.players[i].Fouls, $scope.players[i].PlusMinus);
    };
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
          for (var i = 1; i < $scope.team[teamName].length-1; i++) {
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
                  statline[15] = +array[3] + ((+array[5])*2) + ((+array[7]) * 3);
                  break;
                case "OnePtMade":
                  statline[5] = +array[j] + "-" + (+array[j+1] + +array[j]);
                  break;
                case "TwoPtMade":
                  statline[3] = +array[j] + "-" + (+array[j+1] + +array[j]);
                  break;
                case "ThreePtMade":
                  statline[4] = +array[j] + "-" + (+array[j+1] + +array[j]);
                  break;
                case "OffReb":
                  statline[7] = array[j];
                  break;
                case "DefReb":
                  statline[6] = array[j];
                  statline[8] = +array[j] + +array[j-1];
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
          total[1] = "Total:"
          total[3] = $scope.getTotal("TwoPtMade", teamName) + "-" + (+$scope.getTotal("TwoPtMade", teamName) + +$scope.getTotal("TwoPtMiss", teamName));
          total[4] = $scope.getTotal("ThreePtMade", teamName) + "-" + (+$scope.getTotal("ThreePtMade", teamName) + +$scope.getTotal("ThreePtMiss", teamName));
          total[5] = $scope.getTotal("OnePtMade", teamName) + "-" + (+$scope.getTotal("OnePtMade", teamName) + +$scope.getTotal("OnePtMiss", teamName));
          total[6] = $scope.getTotal("DefReb", teamName);
          total[7] = $scope.getTotal("OffReb", teamName);
          total[8] = (+$scope.getTotal("OffReb", teamName)) + (+$scope.getTotal("DefReb", teamName));
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
  });
  }

	$scope.newPlayer = function(teamName, name, nr, time, one_pt_made, one_pt_miss, two_pt_made, two_pt_miss, three_pt_made, three_pt_miss, off_reb, def_reb, assists, steals, blocks, turnovers, fouls, plus_minus) {
    $scope.team[teamName].push({
      "Nr": nr,
      "Name": name,
      "Time": time,
      "OnePtMade": one_pt_made,
      "OnePtMiss": one_pt_miss,
      "TwoPtMade": two_pt_made,
      "TwoPtMiss": two_pt_miss,
      "ThreePtMade": three_pt_made,
      "ThreePtMiss": three_pt_miss,
      "OffReb": off_reb,
      "DefReb": def_reb,
      "Assists": assists,
      "Steals": steals,
      "Blocks": blocks,
      "Turnovers" : turnovers,
      "Fouls": fouls,
      "PlusMinus" : plus_minus
    });
  }

  $scope.getTotal = function(x, teamName) {
    var total = 0;
    for (var i = 0; i < $scope.team[teamName].length; i++) {
      var team = $scope.team[teamName][i][x];
      total += +team;
    }
    return total;
  }

  $scope.getTotalPoints = function(teamName){
    return $scope.getTotal("OnePtMade", teamName) + (+$scope.getTotal("TwoPtMade", teamName) * 2) + (+$scope.getTotal("ThreePtMade", teamName) * 3);
  }

  

});