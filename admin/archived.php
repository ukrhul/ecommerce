<?php 
	require_once '../core/constants.php';
	require_once '../core/db.php';
	if(!is_logged_in())
	{
		login_error_redirect();
	}
	include 'includes/header.php';
	include 'includes/navigation.php';

	$sql = "SELECT * FROM products WHERE deleted = 1";
	$preresults= pg_query($conn, $sql);

	if(isset($_GET['archived']))
	{
		$id = sanitize($_GET['archived']);
		$unarchivedSql = "UPDATE products SET deleted = 0 WHERE id = $id";
		$result_archived = pg_query($conn, $unarchivedSql);
		header('Location: archived.php');
	}


?>

<h2 class="text-center">ARCHIVED PRODUCTS</h2>

<hr/>
<table class="table table-bordered table-condensed table-striped">
	<thead>
		<th></th>
		<th>Products</th>
		<th>Price</th>
		<th>Category</th>
	</thead>
	<tbody>
	<?php while($products = pg_fetch_assoc($preresults)) : 
			$childID = $products['categories'];
			
			$catSql = "SELECT * FROM categories WHERE id = $childID";
			$result = pg_query($conn, $catSql);
			$child = pg_fetch_assoc($result);
		
		    $parentID = $child['parent'];
		    $pSql = "SELECT * FROM categories WHERE id = $parentID";
			$presult = pg_query($conn, $pSql);
			$parent = pg_fetch_assoc($presult);
			
			$category = ucfirst($parent['category']).'-'.ucfirst($child['category']);
		?>
			<tr>
				<td>
					<a href="archived.php?archived=<?php echo $products['id'] ?>" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-refresh"></span></a> 
				</td>
				<td><?php echo $products['title']; ?></td>
				<td><?php echo money($products['price']); ?></td>
				<td><?php echo $category; ?></td>

			</tr>
		<?php endwhile; ?>
	</tbody>
</table>



<?php include 'includes/footer.php'; ?>