<?php

function dbconnect(){
	$servername="mysql.hostinger.ee";
	$username="u720719028_admin";
	$password="123456789";
	$dbname="u720719028_stat";

	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	if($conn->connect_error){
    	die("connection failed:" . $conn->connect_error);
	} else {
		return $conn;
	}
}

function getAllUsers(){
	$users=array();
	$conn = dbconnect();
	$sql="SELECT * FROM user";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
	    while($row = $result->fetch_assoc()) {
	       array_push($users,$row);
    	} 
	} else {
	     echo "Tühi";
	}
	return $users;
}

function logIn($username, $password){
	$conn = dbconnect();
	$sql="SELECT * FROM user WHERE username='$username' AND password='$password'";
	$result = $conn->query($sql);
	if ($result->num_rows == 1) {
		$_SESSION['username'] = $username;
		header("location: index.php");
	} else {
	     echo "Error: " . $sql . "<br>" . $conn->error;
	}
}

function newUser($username, $password){
	$conn = dbconnect();
	$sql = "INSERT INTO user (username, password) VALUES ('$username', '$password')";
	if($conn->query($sql) && isValidUser($username)){
		header("location: login.php?newUser=true");
	} else {
		echo "Error: " . $sql . "<br>" . $conn->error;
	}
}

function isValidUser($username){
	$conn = dbconnect();
	$sql = "SELECT * FROM user WHERE username = '$username'";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		return false;

	} else {
	return true;
	}
}

function saveGameStats($gameId, $team, $name, $nr, $time, $one_pt_made, $one_pt_miss, $two_pt_made, $two_pt_miss, $three_pt_made, $three_pt_miss, $off_reb, $def_reb, $assists, $steals, $blocks, $turnovers, $fouls, $plus_minus){
	$conn = dbconnect();
	$sql = "INSERT INTO stats (game_id, team,name, number, time, one_pt_made, one_pt_miss, two_pt_made, two_pt_miss, three_pt_made, three_pt_miss, off_reb, def_reb, assists, steals, blocks, turnovers, fouls, plus_minus) 
					   VALUES ('$gameId', '$team','$name', '$nr', '$time', '$one_pt_made', '$one_pt_miss', '$two_pt_made', '$two_pt_miss', '$three_pt_made', '$three_pt_miss', '$off_reb', '$def_reb', '$assists', '$steals', '$blocks', '$turnovers', '$fouls', '$plus_minus')";
	if($conn->query($sql)){
		header("location: index.php");
	} else {
		echo "Error: " . $sql . "<br>" . $conn->error;
	}	
}

function createGame($gameNr, $userId, $hometeamname, $awayteamname){
	$conn = dbconnect();
	$sql = "INSERT INTO games (game_nr, user_id, home_team, away_team, creation_date) VALUES ('$gameNr','$userId','$hometeamname','$awayteamname',now())";
	if($conn->query($sql)){
	} else {
		echo "Error: " . $sql . "<br>" . $conn->error;
	}	
}

function getUserId($username){
	$conn = dbconnect();
	$sql = "SELECT id FROM user WHERE username = '$username'";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
	    while($row = $result->fetch_assoc()) {
	    	return $row['id'];
    	} 
	} else {
	     echo "Error: " . $sql . "<br>" . $conn->error;
	}
}

function getGames($userId){
	$games=array();
	$conn = dbconnect();
	$sql="SELECT * FROM games WHERE user_id = '$userId'";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
	    while($row = $result->fetch_assoc()) {
	       array_push($games,$row);
    	} 
	} else {
	}
	return $games;
}

function getStats($gameNr){
	$stats=array();
	$conn = dbconnect();
	$sql="SELECT * FROM stats WHERE game_id = '$gameNr'";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
	    while($row = $result->fetch_assoc()) {
	       array_push($stats,$row);
    	} 
	} else {
	     echo "Error: " . $sql . "<br>" . $conn->error;
	}
	return $stats;
}
?>