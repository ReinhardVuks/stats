<?php
session_start();
include 'functions.php';

if(isset($_POST['registerUser']) && isset($_POST['registerPassword'])){

	newUser($_POST['registerUser'], $_POST['registerPassword']);

} else {

}

?>