<?php
session_start();

include "functions.php";

$gameId = rand(1, 100);
$curTeam = "";
$userId = (int)getUserId($_SESSION['username']);
echo  $_POST['homeTeamName'];
createGame($gameId, $userId, $_POST['homeTeamName'], $_POST['awayTeamName']);
foreach ($_POST as $key => $value) {
	if (strpos($key, 'Home') !== false) {
    	$curTeam = "Home";
	} else {
		$curTeam = "Away";
	}
	saveGameStats($gameId, $curTeam, $value[1], $value[0], $value[3], $value[4], $value[5], $value[6], $value[7], $value[8], $value[9], $value[10], $value[11],$value[12],$value[13],$value[14],$value[15],$value[16],$value[17]);
}

?>