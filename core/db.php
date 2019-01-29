<?php


function db_connect()
{
	
	$conn = pg_connect("host = " . DB_HOST . " dbname = " . DB_NAME . " user= " .DB_USER . " password= " . DB_PASS . "");
	return $conn;
}

session_start();

$conn = db_connect();


require_once BASEURL.'helpers/helpers.php';
require BASEURL.'vendor/autoload.php';

$cart_id = '';
if(isset($_COOKIE[CART_COOKIE])){
	$cart_id = sanitize($_COOKIE[CART_COOKIE]);
}

if(isset($_SESSION['TKUser'])){
	$user_id = $_SESSION['TKUser'];
	$query = pg_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
	$user_data = pg_fetch_assoc($query);
	$fn = explode(' ', $user_data['full_name']);
	$user_data['first'] = $fn[0];
	$user_data['last'] = $fn[1];

}

if(isset($_SESSION['success_flash'])){
	echo '<div class="bg-success"><p class="text-success text-center">'.$_SESSION['success_flash'].'</p></div>';
	unset($_SESSION['success_flash']);
}

if(isset($_SESSION['error_flash'])){
	echo '<div class="bg-danger"><p class="text-danger text-center">'.$_SESSION['error_flash'].'</p></div>';
	unset($_SESSION['error_flash']);
}

//session_destroy();

?>