<?php
session_start();
include 'functions.php';

if(isset($_POST['registerUser']) && isset($_POST['registerUser'])){

	newUser($_POST['registerUser'], $_POST['registerPassword']);

}

	header("Location: login.php?error=true");
?>