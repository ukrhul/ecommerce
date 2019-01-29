<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/ecommerce/core/constants.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/ecommerce/core/db.php';

// Set your secret key: remember to change this to your live secret key in production
// See your keys here: https://dashboard.stripe.com/account/apikeys
\Stripe\Stripe::setApiKey(STRIPE_PRIVATE);

// Token is created using Checkout or Elements!
// Get the payment token ID submitted by the form:
$token = $_POST['stripeToken'];

// GET THE REST OF THE POST DATA


$full_name = sanitize($_POST['full_name']);
$email = sanitize($_POST['email']);
$street = sanitize($_POST['street']);
$street2 = sanitize($_POST['street2']);
$city = sanitize($_POST['city']);
$state = sanitize($_POST['state']);
$country = sanitize($_POST['country']);
$zipcode = sanitize($_POST['zipcode']);
$tax = sanitize($_POST['tax']);
$sub_total = sanitize($_POST['sub_total']);
$grand_total = sanitize($_POST['grand_total']);
$cart_id = sanitize($_POST['cart_id']);
$description = sanitize($_POST['description']);
$charge_amount = number_format($grand_total,2) * 100;
$metadata = array(
	"cart_id"   => $cart_id,
	"tax"       => $tax,
	"sub_total" => $sub_total,
);

try{	
	$charge = \Stripe\Charge::create([
		'amount' => $charge_amount,
		'currency' => CURRENCY,
		'description' => $description,
		'source' => $token,
		'receipt_email' => $email,
		'metadata' => $metadata
	]);
	
	//adjust inventory
	$itemQ = pg_query($conn, "SELECT * FROM cart WHERE id = $cart_id");
	$iresults = pg_fetch_assoc($itemQ);
	
	$items = json_decode($iresults['items'],true);
	
	foreach($items as $item){
		$newSizes = array();
		$item_id = $item['id'];
		
		$productQ = pg_query($conn,"SELECT sizes FROM products WHERE id = $item_id");
		$product = pg_fetch_assoc($productQ);
		$sizes = sizesToArray($product['sizes']);
		
		foreach($sizes as $size){
			if($size['size'] == $item['size']){
				$q = $size['quantity'] - $item['quantity'];
				$newSizes[] = array('size' => $size['size'], 'quantity' => $q);
			}else{
				$newSizes[] = array('size' => $size['size'], 'quantity' => $size['quantity']);
			}	
		}
		
		$sizeString = sizesToString($newSizes);
		$updateQ = pg_query($conn, "UPDATE products SET sizes = '{$sizeString}' WHERE id = $item_id");
	}
	
	//update cart
	$chargeQ = pg_query($conn,"UPDATE cart SET paid = 1 WHERE id = $cart_id");
	$tansQ = pg_query($conn, "INSERT INTO transactions (charge_id, cart_id, full_name, email, street, street2, city, state, zip_code, country, sub_total, tax, grand_total, description, txn_type) VALUES ('$charge->id',$cart_id,'$full_name','$email','$street','$street2','$city','$state','$zipcode','$country','$sub_total','$tax','$grand_total','$description','$charge->object')");
	
	$domain = (($_SERVER['HTTP_HOST'] != 'localhost')? '.'.$_SERVER['HTTP_HOST']: false);
	
	setcookie(CART_COOKIE,'',1,'/',$domain, false);
	
	include 'includes/header.php';
	include 'includes/navigation.php';
	include 'includes/headerpartial.php';
	?>
	
	<h1 class="text-center text-success">Thank You!</h1>
	<p>Your card has been successfully charged <?php money($grand_total); ?>. You have been emailed a receipt. Please check your spam folder if it is not in your inbox. Additionally you can print this page as a receipt.</p>
	
	<p>Your receipt number is: <strong><?php echo $cart_id; ?></strong></p>
	<p>Your order will be shipped to the address below:</p>
	<address>
	<?php echo $full_name; ?><br/>
	<?php echo $street; ?><br/>
	<?php echo (($street2 != '')? $street2 . '<br/>' : ''); ?>
	<?php echo $city. ','. $state . ',' . $zipcode; ?><br/>
	<?php echo $country; ?><br/>
	</address>
	
	<?php
	include 'includes/footer.php';
	
}
catch(\Stripe\Error\Card $e)
{
	echo $e;
}



?>