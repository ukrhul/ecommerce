<?php 

require_once $_SERVER['DOCUMENT_ROOT'].'/ecommerce/core/constants.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/ecommerce/core/db.php';

$name = sanitize($_POST['full_name']);
$email = sanitize($_POST['email']);
$street = sanitize($_POST['street']);
$street2 = sanitize($_POST['street2']);
$city = sanitize($_POST['city']);
$state = sanitize($_POST['state']);
$country = sanitize($_POST['country']);
$zipcode = sanitize($_POST['zipcode']);

$errors = array();
$required = array(
	'full_name' => 'Full Name',
	'email'     => 'Email',
	'street'    => 'Street Address',
	'street2'   => 'Street Address 2',
	'city'      => 'City',
	'state'     => 'State',
	'country'   => 'Country',
	'zipcode'   => 'Zip Code',
);

//check if all required fields are filled out

foreach($required as $f => $d){
	if(empty($_POST[$f]) || $_POST[$f] == ''){
		$errors[] = $d . ' is required.'; 
	}
}

//check if valid email address
if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
	$errors[] = "Please enter a valid email.";
}

if(!empty($errors)){
	echo display_errors($errors);
}else{
	echo 'passed';
}

?>