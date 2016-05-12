var app1 = angular.module('app1', ["pubnub.angular.service"]);
app1.controller('ctrl1', function($rootScope, $scope, PubNub) {

  $scope.userId = "User " + Math.round(Math.random()*1000);
  $scope.channel =  "Channel" + $_GET('nr');

  if (!$rootScope.initialized) {
    PubNub.init({
      subscribe_key: 'sub-c-874efbca-0242-11e6-8b0b-0619f8945a4f',
      publish_key: 'pub-c-5b2ab7df-8440-4d8f-9423-07992a42ee77',
      uuid:$scope.userId
    });
    $rootScope.initialized = true;
  }

  PubNub.ngSubscribe({ channel: $scope.channel });
  $rootScope.$on(PubNub.ngMsgEv($scope.channel), function(ngEvent, payload) {
    $scope.$apply(function() {
      $scope.message = payload.message;
      $scope.team = $scope.message[0];
      $scope.gameLog = $scope.message[1];
      $scope.homeTeamName = $scope.message[2];
      $scope.awayTeamName = $scope.message[3];
    });
  });

  $scope.team = {
    "Home": [],
    "Away": []
  };

  function $_GET(param) {
    var vars = {};
    window.location.href.replace( location.hash, '' ).replace( 
    /[?&]+([^=&]+)=?([^&]*)?/gi, 
    function( m, key, value ) {
      vars[key] = value !== undefined ? value : '';
    }
    );

    if ( param ) {
      return vars[param] ? vars[param] : null;  
    }
    return vars;
  }
  $scope.getTotal = function(stat, teamName) {
    var total = 0;
    for (var i = 0; i < $scope.team[teamName].length; i++) {
      var team = $scope.team[teamName][i][stat];
      total += +team;
    }
    return total;
  }
});
app1.filter('secondsToDateTime', [function() {
  return function(seconds) {
    return new Date(1970, 0, 1).setSeconds(seconds);
  };
}])