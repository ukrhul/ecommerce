<?php 
	require_once '../core/constants.php';
	require_once '../core/db.php';
	if(!is_logged_in())
	{
		header('Location: login.php');
	}
	include 'includes/header.php';
	include 'includes/navigation.php';
	
?>
<!-- Orders to Fill -->
<?php 
	$txnQ = pg_query($conn, "SELECT t.id, t.cart_id, t.full_name, t.description, t.txn_date, t.grand_total, c.items, c.paid, c.shipped FROM transactions t LEFT JOIN cart c ON t.cart_id = c.id WHERE c.paid = 1 AND c.shipped = 0 ORDER BY t.txn_date");
	
?>
<div class="col-md-12">
	<h3 class="text-center">Orders To Ship</h3>
	<table class="table table-codensed table-bordered table-striped">
		<thead>
			<th></th>
			<th>Name</th>
			<th>Description</th>
			<th>Total</th>
			<th>Date</th>
		</thead>
		<tbody>
		<?php while($order = pg_fetch_assoc($txnQ)): ?>
			<tr>
				<td><a href="orders.php?txn_id=<?php echo  $order['id']; ?>" class="btn btn-xs btn-info">Details</a></td>
				<td><?php echo $order['full_name']; ?></td>
				<td><?php echo $order['description']; ?></td>
				<td><?php echo money($order['grand_total']) ; ?></td>
				<td><?php echo pretty_date($order['txn_date']) ; ?></td>
			</tr>
			<?php endwhile; ?>
		</tbody>
	</table>
</div>

<div class="row">
<!--Sales by Month-->
<?php 
	
	$thisYr = date('Y');
	$lastYr = $thisYr - 1;
	$thisYrQ = pg_query($conn, "SELECT grand_total, txn_date FROM transactions WHERE date_part('year',txn_date) = '{$thisYr}'");
	$lastYrQ = pg_query($conn, "SELECT grand_total, txn_date FROM transactions WHERE date_part('year',txn_date) = '{$lastYr}'");
	
	$current =array();
	$last = array();
	$currentTotal = 0;
	$lastTotal = 0;
	
	while($x = pg_fetch_assoc($thisYrQ)){
		$month = date('m',strtotime($x['txn_date']));
		if(!array_key_exists($month, $current)){
			$current[(int)$month] = $x['grand_total'];
		}else{
			$current[(int)$month] += $x['grand_total']; 
		}
		$currentTotal += $x['grand_total'];
	}
	
		while($y = pg_fetch_assoc($lastYrQ)){
		$month = date('m',strtotime($x['txn_date']));
		if(!array_key_exists($month, $current)){
			$last[(int)$month] = $y['grand_total'];
		}else{
			$last[(int)$month] += $y['grand_total']; 
		}
		$lastTotal += $y['grand_total'];
	}
	
	?>
	<div class="col-md-4">
		<h3 class="text-center">Sales By Month</h3>
		<table class="table table-condensed table-striped table-bordered">
			<thead>
				<th></th>
				<th><?php echo $lastYr; ?></th>
				<th><?php echo $thisYr; ?></th>
			</thead>
			<tbody>
			<?php for($i=1; $i <= 12; $i++):
				$dt = DateTime::createFromFormat('!m', $i);
				?>
				<tr<?php echo ((date("m") == $i)? ' class="info"':''); ?>>
					<td><?php echo $dt->format("F"); ?></td>
					<td><?php echo ((array_key_exists($i, $last))?money($last[$i]):money(0)); ?></td>
					<td><?php echo ((array_key_exists($i, $current))?money($current[$i]):money(0));  ?></td>
				</tr>
			<?php endfor; ?>
			<tr>
				<td>Total</td>
				<td><?php echo money($lastTotal); ?></td>
				<td><?php echo money($currentTotal); ?></td>
			</tr>
			</tbody>
		
		</table>
	</div>
	
<!--	Inventory-->
<?php
	$iQuery = pg_query($conn,"SELECT * FROM products WHERE deleted = 0");
	$lowItems = array();
	while($product = pg_fetch_assoc($iQuery)){
		$item = array();
		$sizes = sizesToArray($product['sizes']);
		foreach($sizes as $size){
			if($size['quantity'] <= $size['threshold']){
				$cat = get_category($product['categories']);
				$item = array(
					'title' => $product['title'],
					'size' => $size['size'],
					'quantity' => $size['quantity'],
					'threshold' => $size['threshold'],
					'category' => ucfirst($cat['parent']) . ' ~ '. ucfirst($cat['child'])
				);

				$lowItems[] = $item;
				}
		}
	}
	
?>
	<div class="col-md-8">
		<h3 class="text-center">Low Inventory</h3>
		<table class="table table-condensed table-striped table-bordered">
			<thead>
				<th>Product</th>
				<th>Category</th>
				<th>Size</th>
				<th>Quantity</th>
				<th>Threshold</th>
			</thead>
			<tbody>
			<?php foreach($lowItems as $item):  ?>
				<tr<?php echo (($item['quantity'] == 0)?' class="danger"':''); ?>>
					<td><?php echo $item['title']; ?></td>
					<td><?php echo $item['category']; ?></td>
					<td><?php echo $item['size']; ?></td>
					<td><?php echo $item['quantity']; ?></td>
					<td><?php echo $item['threshold']; ?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>


<?php 	include 'includes/footer.php'; ?>
