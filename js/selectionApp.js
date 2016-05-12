var myApp = angular.module('selectionApp', ['pascalprecht.translate']);
myApp.config(['$translateProvider', function($translateProvider){
  // Adding a translation table for the English language
  $translateProvider.translations('en', {
    "TIME" : 'Use game clock',
	"QUARTERS" : 'Keep count of quarters',
	"MISSED_SHOTS" : 'Mark missed shots',
	"REBOUNDS" : 'Mark rebounds',
	"OFF_REBOUNDS" : 'Mark offensive rebounds',
	"ASSISTS" : 'Mark assists',
	"STEALS" : 'Mark steals',
	"BLOCKS" : 'Mark blocks',
	"FOULS" : 'Mark fouls',
	"TURNOVERS" : 'Mark turnovers',
	"PERIOD_LENGTH" : 'Set period length in minutes',
	"NR_OF_PERIODS" : 'Set number of periods',
	"MAX_ON_COURT_PLAYERS" : 'Set maximum number of on court players',
	"MAX_FOULS" : 'Set maximum number of fouls allowed to commit',
	"GAME" : 'Game',
	"INDIVIDUAL" : 'Individual',
	"ADDITIONAL SETTINGS" : 'Additional Settings',
  });
  // Adding a translation table for the Russian language
  $translateProvider.translations('ee', {
    "TIME" : 'Kasuta mängu kella',
	"QUARTERS" : 'Pea arvestust perioodide üle',
	"MISSED_SHOTS" : 'Märgi möödaläinud viskeid',
	"REBOUNDS" : 'Märgi lauapalle',
	"OFF_REBOUNDS" : 'Märgi ründelaua palle',
	"ASSISTS" : 'Märgi resultatiivseid sööte',
	"STEALS" : 'Märgi vaheltlõikeid',
	"BLOCKS" : 'Märgi viskeblokeeringuid',
	"FOULS" : 'Märgi vigu',
	"TURNOVERS" : 'Märgi pallikaotusi',
	"PERIOD_LENGTH" : 'Määra ühe perioodi pikkus',
	"NR_OF_PERIODS" : 'Määra mitu perioodi mängitakse',
	"MAX_ON_COURT_PLAYERS" : 'Määra maksimaalne väljakumängijate arv',
	"MAX_FOULS" : 'Määra maksimaalne isiklike vigade arv',
	"GAME" : 'Mäng',
	"INDIVIDUAL" : 'Individuaalne',
	"ADDITIONAL SETTINGS" : 'Lisa sätted',
  });
  // Tell the module what language to use by default
  $translateProvider.preferredLanguage('en');
}])
myApp.controller('MyCtrl',  ['$scope', '$translate', function($scope, $translate) {

	$scope.homeTeamName = "Home";
	$scope.homeTeamNameShort = "HME";
	$scope.awayTeamName = "Away";
	$scope.awayTeamNameShort = "AWY";
	$scope.homeTeamColor = "#FFFFFF";
	$scope.awayTeamColor  = "#000000";

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
		"GAME" : {
			"TIME" : false,
			"QUARTERS" : false,
		}},
		{"INDIVIDUAL" : {
			"MISSED_SHOTS": false,
			"REBOUNDS" : false,
			"OFF_REBOUNDS" : false,
			"ASSISTS" : false,
			"STEALS" : false,
			"BLOCKS" : false,
			"FOULS" : false,
			"TURNOVERS" : false
		}},
		{"ADDITIONAL SETTINGS" : {
			"PERIOD_LENGTH" : 10,
			"NR_OF_PERIODS" : 4,
			"MAX_ON_COURT_PLAYERS" : 5,
			"MAX_FOULS" : 5
		}}
	];

	$scope.basketballStatsDict =  {
		"TIME" : 'Use game clock',
		"QUARTERS" : 'Keep count of quarters',
		'POINTS' : 'Mark points',
		"MISSED_SHOTS" : 'Mark missed shots',
		"REBOUNDS" : 'Mark rebounds',
		"OFF_REBOUNDS" : 'Mark offensive rebounds',
		"ASSISTS" : 'Mark assists',
		"STEALS" : 'Mark steals',
		"BLOCKS" : 'Mark blocks',
		"FOULS" : 'Mark fouls',
		"TURNOVERS" : 'Mark turnovers',
		"PERIOD_LENGTH" : 'Set period length in minutes',
		"NR_OF_PERIODS" : 'Set number of periods',
		"MAX_ON_COURT_PLAYERS" : 'Set maximum number of on court players',
		"MAX_FOULS" : 'Set maximum number of fouls allowed to commit'
	};



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
   	if($scope.numberExist(teamName, nr)){
   		alert("Player with number " + nr + " already exist.\nChoose another!")
   		return;
   	}
  	$scope.team[teamName].push({
  		Nr: nr == null ? 0 : +nr,
  		Name: capitalizeEachWord(name)
  	});
  	$scope.Name = null;
  	$scope.Nr = null;
  }

  $scope.numberExist = function(teamName, nr){
  	if($scope.team[teamName].length > 0){
		for (var i = 0; i < $scope.team[teamName].length; i++) {
			if($scope.team[teamName][i].Nr == nr){
				return true
			}
		};
	}
	return false;
}

  $scope.removePlayer = function(nr, teamName){
    for (var i = 0; i < $scope.team[teamName].length; i++) {
    	if (nr == $scope.team[teamName][i].Nr){
    		$scope.team[teamName].splice(i, 1);
    	}
    };
  }

  $scope.cleanTeam = function(teamName){
  	if(teamName == "Home"){
  		$scope.homeTeamName = "Home";
  	} else {
  		$scope.awayTeamName = "Away";
  	}
  	while($scope.team[teamName].length > 0){
		$scope.team[teamName].splice(0, 1);
	}
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

$scope.addTeamFromFile = function($fileContent, teamName){
	$scope.cleanTeam(teamName);
	$scope.content = $fileContent.split("\n");
	for (var i = 0; i < $scope.content.length; i++) {
		var val = $scope.content[i].trim();
		var player = val.split(",");
		if(i == 0){
			if(teamName == "Home"){
				$scope.homeTeamName = capitalizeEachWord(val);
			} else {
				$scope.awayTeamName = capitalizeEachWord(val);
			}
		}
		if(player.length > 1){
			$scope.newPlayer(teamName, player[0].trim(), player[1].trim());
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

$scope.setLang = function(langKey) {
    $translate.use(langKey);
  };

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
}]);


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
