<?php

function dbConnection() {
	$server = "localhost";
	$username = "root";
	$password = "";
	$db = "AdminPanel";

	$conn = new mysqli($server,$username,$password,$db);

	return $conn;
}

?>
