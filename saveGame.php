<?php
session_start();

include "functions.php";
header('Content-Type: text/html; charset=utf-8');


$curTeam = "";
$userId = (int)getUserId($_SESSION['username']);
createGame($_POST['gameNr'], $userId, $_POST['homeTeamName'], $_POST['awayTeamName']);
foreach ($_POST as $key => $value) {
	if(strpos($key, 'player') !== false){
		if (strpos($key, 'Home') !== false) {
	    	$curTeam = "Home";
		} else {
			$curTeam = "Away";
		}
		saveGameStats($_POST['gameNr'], $curTeam, $value[1], $value[0], $value[3], $value[4], $value[5], $value[6], $value[7], $value[8], $value[9], $value[10], $value[11],$value[12],$value[13],$value[14],$value[15],$value[16],$value[17]);


		//header("location: index.php");
	}
}

//print_r($_POST['GameLog']);
for ($i=0; $i <  count($_POST['GameLog']); $i++) {
	saveGameLog($_POST['gameNr'], $_POST['GameLog'][$i]);
}

?>