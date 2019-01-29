<?php 

require_once $_SERVER['DOCUMENT_ROOT'].'/ecommerce/core/constants.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/ecommerce/core/db.php';

$mode = sanitize($_POST['mode']);

$edit_size = sanitize($_POST['edit_size']);

$edit_id = sanitize($_POST['edit_id']);

$cartQ = pg_query($conn, "SELECT * FROM cart WHERE id = $cart_id");

$result = pg_fetch_assoc($cartQ);

$items = json_decode($result['items'],true);

$update_items = array();

$domain = (($_SERVER['HTTP_HOST'] != 'localhost')?'.'.$_SERVER['HTTP_HOST']:false);

if($mode == 'removeone'){
	foreach($items as $item){
		if($item['id'] == $edit_id && $item['size'] == $edit_size){
			$item['quantity'] = $item['quantity'] - 1;
		}
		if($item['quantity'] > 0){
			$update_items[] = $item;
		}
	}
	
}

if($mode == 'addone'){
	foreach($items as $item){
		if($item['id'] == $edit_id && $item['size'] == $edit_size){
			$item['quantity'] = $item['quantity'] + 1;
		}
		$update_items[] = $item;
	}
}

if(!empty($update_items)){
	$json_update = json_encode($update_items);
	$updateQ = pg_query($conn, "UPDATE cart SET items = '{$json_update}' WHERE id = $cart_id");
	
	$_SESSION['success_flash'] = "Your shopping cart has been updated";
}

if(empty($update_items)){
	$deleteQ = pg_query($conn, "DELETE FROM cart WHERE id = $cart_id");
	
	setcookie(CART_COOKIE, '', 1, '/', $domain, false);
}

?>