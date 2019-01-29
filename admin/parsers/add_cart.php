<?php 

require_once $_SERVER['DOCUMENT_ROOT'].'/ecommerce/core/constants.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/ecommerce/core/db.php';

$product_id = sanitize($_POST['product_id']);
$size = sanitize($_POST['size']);
$available = sanitize($_POST['available']);
$quantity = sanitize($_POST['quantity']);
$item = array();
$item[] = array(
	'id' 	   => $product_id,
	'size' 	   => $size,
	'quantity' => $quantity,
);

$domain = (($_SERVER['HTTP_HOST'] != 'localhost')?'.'.$_SERVER['HTTP_HOST']:false);

$query = pg_query($conn, "SELECT * FROM products WHERE id = $product_id");
$product = pg_fetch_assoc($query);

$_SESSION['success_flash'] = $product['title']. ' added to your cart';

//check to see if the cart cookie exists
if($cart_id != ''){
	
	$cartQ = pg_query($conn, "SELECT * FROM cart where id = $cart_id");
	
	$cart = pg_fetch_assoc($cartQ);
	
	$previous_items = json_decode($cart['items'],true);
	var_dump($previous_items);
	$item_match = 0;
	
	$new_items = array();

	
	foreach($previous_items as $pitem){
		if($item[0]['id'] == $pitem['id'] && $item[0]['size'] == $pitem['size']){
			$pitem['quantity'] = $pitem['quantity'] + $item[0]['quantity'];
			if($pitem['quantity'] > $available){
				$pitem['quantity'] = $available;
			}
			$item_match = 1;
		}
		$new_items[] = $pitem;
	}
	
	if($item_match != 1){
		$new_items = array_merge($item, $previous_items);
	}
	
	$item_json = json_encode($new_items);
	$cart_expire = date("Y-m-d H:i:s", strtotime("+30 days"));
	
	$updateSql = "UPDATE cart SET items = '{$item_json}', expire_date = '{$cart_expire}' WHERE id = '{$cart_id}'";
	
	$updateQuery = pg_query($conn, $updateSql);
	
	setcookie(CART_COOKIE, '', 1, "/", $domain, false);
	setcookie(CART_COOKIE, $cart_id, CART_COOKIE_EXPIRE, '/', $domain, false);
	
}else{
	// add the cart to database and set cookie
	$item_json = json_encode($item);
	$cart_expire = date("Y-m-d H:i:s", strtotime("+30 days"));
	
	$insert_query = pg_query($conn, "SELECT nextval('cart_id_seq');");
	$insert_row = pg_fetch_row($insert_query);
	$cart_id = $insert_row[0] + 1;
	
	$result = pg_query($conn, "INSERT INTO cart (items, expire_date) VALUES ('{$item_json}','{$cart_expire}')");
	
	setcookie(CART_COOKIE, $cart_id, CART_COOKIE_EXPIRE, '/', $domain, false);
	
}

?>