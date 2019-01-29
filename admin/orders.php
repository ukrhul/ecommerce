<?php
	require_once '../core/constants.php';
	require_once '../core/db.php';
	if(!is_logged_in())
	{
		header('Location: login.php');
	}
	include 'includes/header.php';
	include 'includes/navigation.php';

	//complete order
	if(isset($_GET['complete']) && $_GET['complete'] == 1)
	{
		$cart_id = sanitize((int)$_GET['cart_id']);
		$updateQ = pg_query($conn, "UPDATE cart SET shipped =1 WHERE id = $cart_id");
		$_SESSION['success_flash'] = "The Order has been completed";
		header('Location: index.php');
	}

	$txn_id = sanitize((int)$_GET['txn_id']);
	$txnQ = pg_query($conn, "SELECT * FROM transactions WHERE id = $txn_id");
	$txn = pg_fetch_assoc($txnQ);
	$cart_id = $txn['cart_id'];
	
	$cartQ = pg_query($conn,"SELECT * FROM cart WHERE id = $cart_id");

	$cart = pg_fetch_assoc($cartQ);
	$items = json_decode($cart['items'],true);
	
	$idArray = array();
	$products = array();
	
	foreach($items as $item){
		$idArray[] = $item['id'];
	}

	$ids = implode(',',$idArray);
	$productsQ = pg_query($conn, "SELECT i.id as id, i.title as title, c.id as cid, c.category as child, p.category as parent FROM products i LEFT JOIN categories c ON i.categories = c.id LEFT JOIN categories p on c.parent = p.id WHERE i.id IN ({$ids})");

	while($p = pg_fetch_assoc($productsQ)){
		foreach($items as $item){
			if($item['id'] == $p['id']){
				$x = $item;
				continue;
			}
		}
		$products[] = array_merge($x, $p);
	}
?>

<h2 class="text-center">Items Ordered</h2>
<table class="table table-condensed table-bordered table-striped">
	<thead>
		<th>Quantity</th>
		<th>Title</th>
		<th>Category</th>
		<th>Size</th>
	</thead>
	<tbody>
	<?php foreach($products as $product): ?>
		<tr>
			<td><?php echo $product['quantity']; ?></td>
			<td><?php echo $product['title']; ?></td>
			<td><?php echo ucfirst($product['parent']) . '~'. ucfirst($product['child']); ?></td>
			<td><?php echo $product['size']; ?></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>

<div class="row">
	<div class="col-md-6">
		<h3 class="text-center">Order Details</h3>
		<table class="table table-condensed table-striped table-bordered">
			<tbody>
				<tr>
					<td>Sub Total</td>
					<td><?php echo money($txn['sub_total']); ?></td>
				</tr>
				<tr>
					<td>Tax</td>
					<td><?php echo money($txn['tax']); ?></td>
				</tr>
				<tr>
					<td>Grand Total</td>
					<td><?php echo money($txn['grand_total']); ?></td>
				</tr>
				<tr>
					<td>Order Date</td>
					<td><?php echo pretty_date($txn['txn_date']); ?></td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col-md-6">
		<h3 class="text-center">Shipping Address</h3>
		<address>
			<?php echo $txn['full_name'];?><br/>
			<?php echo $txn['street'];?><br/>
			<?php echo (($txn['street2'] != '') ? $txn['street2'] : '');?><br/>
			<?php echo $txn['city']. ', '. $txn['state'] .',  '. $txn['zip_code'];?><br/>
			<?php echo $txn['country'];?><br/>
		</address>
	</div>
</div>
<div class="pull-right">
	<a href="index.php" class="btn btn-large btn-default">Cancel</a>
	<a href="orders.php?complete=1&cart_id=<?php echo $cart_id; ?>" class="btn btn-primary btn-large ">Complete Order</a>
</div>



<?php include 'includes/footer.php' ?>