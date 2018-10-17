<?php
	include_once('config/database.php');

	session_destroy();
	header("Location: index.php");
?>