var myApp = angular.module('selectionApp', []);

myApp.controller('MyCtrl', function($scope) {

	$scope.homeTeamName = "Home";
	$scope.awayTeamName = "Away";

	$scope.selectionDone = false;
	$scope.selectSport = function(nr){
			if(nr == 1){
				$scope.selectedSport = "basketball";
			}
			else if(nr == 2){
				$scope.selectedSport = "football";
			}
			else if(nr == 3){
				$scope.selectedSport = "volleyball";
			}
			
			$scope.selectionDone = true;
		}


	$scope.basketballStats = [{
		"Game" : {
			"TIME" : false,
			"QUARTERS" : false,
		}},
		{"Induvidual" : {
			"POINTS" : false,
			"MISSED_SHOTS": false,
			"REBOUNDS" : false,
			"OFF_REBOUNDS" : false,
			"ASSISTS" : false,
			"STEALS" : false,
			"BLOCKS" : false,
			"FOULS" : false,
			"TURNOVERS" : false
		}}
	]


	$scope.team = {"Home":[],
			
				   "Away":[]
				   };

	$scope.selectedTeam = "Home";

/*
	$scope.newPlayer = function(name, nr) {
    $scope.team.push({
      Nr: nr == null ? 0 : +nr,
      Name: name
    });
    $scope.Name = null;
    $scope.Nr = null;
  }
  */
  $scope.newPlayer = function(teamName, name, nr){
  	$scope.team[teamName].push({
  		Nr: nr == null ? 0 : +nr,
  		Name: capitalizeEachWord(name)
  	});
  	$scope.Name = null;
  	$scope.Nr = null;
  }

  $scope.removePlayer = function(nr, teamName){
    for (var i = 0; i < $scope.team[teamName].length; i++) {
    	if (nr == $scope.team[teamName][i].Nr){
    		$scope.team[teamName].splice(i, 1);
    	}
    };
  }


  $scope.myFunct = function(keyEvent, selectedTeam, Name, Nr) {	
  if (keyEvent.which === 13){
    $scope.newPlayer(selectedTeam, Name, Nr);
    document.getElementById("inputName").focus();
	}
}

function capitalizeEachWord(str) {
    return str.replace(/\w\S*/g, function(txt) {
        return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
    });
}

$scope.addTeamFromFile = function($fileContent, team){
	$scope.content = $fileContent.split("\n");
	for (var i = 0; i < $scope.content.length; i++) {
		var val = $scope.content[i].trim();
		var player = val.split(",");
		if(i == 0){
			if(team == "Home"){
				$scope.homeTeamName = capitalizeEachWord(val);
			} else {
				$scope.awayTeamName = capitalizeEachWord(val);
			}
		}
		if(player.length > 1){
			$scope.newPlayer(team, player[0].trim(), player[1].trim());
		}
	};
}

$scope.showContent = function($fileContent){
        $scope.content = $fileContent.split("\n");
        var curTeam = "";
        for (var i = 0; i < $scope.content.length; i++) {
        	var val = $scope.content[i].trim();
        	
			var player = val.split(",");
			if(i == 0){
				$scope.homeTeamName = capitalizeEachWord(val);
				curTeam = "Home";
			} else if(val == ""){
				if($scope.content.length > i){
					$scope.awayTeamName = capitalizeEachWord($scope.content[i+1].trim());
					curTeam = "Away";
				}
  			}

        	if(player.length > 1){
        		$scope.newPlayer(curTeam, player[0].trim(), player[1].trim());
        	}

        };
    };



 function typeOf (obj) {
  return {}.toString.call(obj).split(' ')[1].slice(0, -1).toLowerCase();
}

	/*
		"Time": false,
	    "Points": false,
	    "OnePtMade": false,
	    "OnePtMiss": false,
	    "TwoPtMade": false,
	    "TwoPtMiss": false,
	    "ThreePtMade": false,
		"ThreePtMiss": false,
	    "OffReb": false,
	    "DefReb": false,
	    "Assists": false,
	    "Steals": false,
	    "Blocks": false,
	    "Fouls": false,*/
});


myApp.directive('onReadFile', function ($parse) {
	return {
		restrict: 'A',
		scope: false,
		link: function(scope, element, attrs) {
            var fn = $parse(attrs.onReadFile);
            
			element.on('change', function(onChangeEvent) {
				var reader = new FileReader();
                
				reader.onload = function(onLoadEvent) {
					scope.$apply(function() {
						fn(scope, {$fileContent:onLoadEvent.target.result});
					});
				};

				reader.readAsText((onChangeEvent.srcElement || onChangeEvent.target).files[0]);
			});
		}
	};
});
