<h3 class="text-center">Shopping Cart</h3>
<div>
	<?php if(empty($cart_id)): ?>
		<p>Your Shopping cart is empty!</p>
	
	<?php else:
		$cartQ = pg_query($conn, "SELECT * FROM cart WHERE id = $cart_id");
		$result = pg_fetch_assoc($cartQ);
		$items = json_decode($result['items'],true);
		$i = 1;
		$sub_total = 0;
	?>
		<table class="table table-condensed" id="cart_widget">
			<tbody>
				<?php foreach($items as $item): 
					$productQ = pg_query($conn,"SELECT * FROM products WHERE id = '{$item['id']}'");
					$product = pg_fetch_assoc($productQ);
				?>
				<tr>
					<td><?php echo $item['quantity']; ?></td>
					<td><?php echo substr($product['title'],0,15).'...'; ?></td>
					<td><?php echo money($item['quantity'] * $product['price']); ?></td>
				</tr>
				<?php 
					$i++;
					$sub_total += ($item['quantity'] * $product['price']);
					endforeach; ?>
					<tr>
						<td></td>
						<td>Sub Total</td>
						<td><?php echo money($sub_total); ?></td>
					</tr>
			</tbody>
		</table>
	<a href="cart.php" class="btn btn-xs btn-primary pull-right">View Cart</a>
	<div class="clearfix"></div>
	
	<?php endif; ?>
</div>