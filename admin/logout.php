<?php 
	require_once '../core/constants.php';
	require_once '../core/db.php';
	unset($_SESSION['TKUser']);
	header('Location: login.php');

?>