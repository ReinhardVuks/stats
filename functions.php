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
		header("location: main.php");
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

?>