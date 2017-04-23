<?php
//This file is simply used for establishing connection to the database.

//Function for establishing the connection to the database.
function connectToDB(){
	$servername = "localhost";
	$username = "root";
	$password = "";
	$db = "forum";
	// Create connection
	$conn = new mysqli($servername, $username, $password, $db);
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	//The variable '$conn' is used by other functions to make connection the the database.
	return $conn;
}
?>