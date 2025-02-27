<?php 
	$servername = "localhost";
	$username = "INSERT_DATABASE_USERNAME";
	$password = "INSERT_DATABASE_PASSWORD";
	$dbname = "INSERT_DATABASE_NAME";

	$conn = new mysqli($servername, $username, $password, $dbname);
	mysqli_set_charset($conn ,'utf8');
	if ($conn->connect_error) {
	  	die("Connection failed: " . $conn->connect_error);
	}
?>