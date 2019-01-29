<h3 class="text-center">Popular Items</h3>
<?php
	$transQ = pg_query($conn, "SELECT * FROM cart WHERE paid = 1 ORDER BY id DESC LIMIT 5");
	$results = array();
	while($row = pg_fetch_assoc($transQ)){
		$results[] = $row;
	}

	$row_count = pg_num_rows($transQ);
	$used_ids = array();
	for($i = 0; $i < $row_count; $i++){
		$json_items = $results[$i]['items'];
		$items = json_decode($json_items,true);
		foreach($items as $item){
			if(!in_array($item['id'], $used_ids)){
				$used_ids[] = $item['id'];
			}
		}
	}
?>


<div id="recent_widget">
	<table class="table table-condensed">
		<?php foreach($used_ids as $id): 
			
		$productQ = pg_query($conn, "SELECT id,title FROM products WHERE id = $id");
		$product = pg_fetch_assoc($productQ);
		?>
		
		<tr>
			<td>
				<?php echo substr($product['title'],0,15); ?>
			</td>
			<td>
				<a class="text-primary" onClick="detailsmodal('<?php echo $id; ?>')">View</a>
			</td>
			
		</tr>
		
			
		
		<?php endforeach; ?>
	</table>
	
</div>