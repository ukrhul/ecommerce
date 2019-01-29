<?php 
	require_once '../core/constants.php';
	require_once '../core/db.php';
	if(!is_logged_in())
	{
		login_error_redirect();
	}
	include 'includes/header.php';
	include 'includes/navigation.php';
	//get brand from database
	
	$sql1 = "SELECT * FROM brand ORDER BY brand";
	$result1 = pg_query($conn, $sql1);
	
	$errors = array();

	//Edit brand
	if(isset($_GET['edit']) && !empty($_GET['edit'])){	
		$edit_id = (int)$_GET['edit'];
		$edit_id = sanitize($edit_id);
		$sqledit = "SELECT * FROM brand WHERE id = $edit_id";
		$edit_result = pg_query($conn, $sqledit);
		$eBrand = pg_fetch_assoc($edit_result);
	 }

	//delete a brand
	if(isset($_GET['delete']) && !empty($_GET['delete'])){
		$delete_id = (int)$_GET['delete'];
		$delete_id = sanitize($delete_id);
		
		$sqldeletebrand = "DELETE FROM brand WHERE id = $delete_id";
		$delete_result = pg_query($conn, $sqldeletebrand);
		header('Location: brands.php');
	}


	//if add form is submitted
	if(isset($_POST['add_submit'])){
		$brandadd = sanitize($_POST['brand']);
		//check if brand is blanck
		if($brandadd == '')
		{
			$errors[] .= 'You must enter a brand!';
		}
		// check if brand exists in database
		$sql = "SELECT * FROM brand WHERE brand = '$brandadd'";
		if(isset($_GET['edit']))
		{
			$sql = "SELECT * FROM brand WHERE brand = '$brandadd' AND id != '$edit_id'";			
		}
		
		$result = pg_query($conn, $sql);
		$count = pg_num_rows($result);
		
		if ($count > 0)
		{
			$errors[] .= ucfirst($brandadd).' already exists!';
		}
		
		//display errors
		if(!empty($errors))
		{
			echo display_errors($errors);
		}else{
			//Add brand to database
			$brandadd =  strtolower($brandadd);
			$sqlinsert = "INSERT INTO brand (brand) VALUES ('$brandadd')";
			if(isset($_GET['edit']))
			{
				$sqlinsert = "UPDATE brand SET brand = '$brandadd' WHERE id = $edit_id";
			}
			$insert = pg_query($conn, $sqlinsert);
			
			
			header('Location: brands.php');
			

		}
	}
?>
<h2 class="text-center">BRANDS</h2><hr>

<!-- Brand Form-->
<div style="margin-left:30%;">
	<form action="brands.php<?php echo ((isset($_GET['edit'])))? '?edit='.$edit_id.'': ''; ?>" class="form-inline" method="post">
		<div class="form-group">
		<?php 
		
		$brand_value = '';
			
		if(isset($_GET['edit'])){
			$brand_value = ucfirst($eBrand['brand']);
		}else{
			if(isset($_POST['brand']))
			{
				$brand_value = sanitize($_POST['brand']);
			}
		} 
			
		?>
		
			<label for="brand"><strong><?php echo ((isset($_GET['edit']))) ? 'Edit' : 'Add A'; ?> Brand:</strong> </label>
			<input type="text" name="brand" id="brand" class="form-control" value="<?php echo $brand_value ?>" />
			<?php if(isset($_GET['edit'])): ?>
				<a href="brands.php" class="btn btn-default">Cancel</a>
			<?php endif; ?>
			<input type="submit" name="add_submit" value="<?php echo ((isset($_GET['edit']))) ? 'Edit' : 'Add '; ?> Brand"  class="btn btn-success" />
		</div>
	</form>
</div>
<hr/>
<table class="table table-bordered table-striped table-auto table-condensed">
	<thead>
		<th></th>
		<th>Brand</th>
		<th></th>
	</thead>
	
	<tbody>
		<?php while($brand1 = pg_fetch_assoc($result1)): ?>
		<tr>
			<td><a href="brands.php?edit=<?php echo $brand1['id'] ?>" class="btn btn-xs btn-default"><span class="fa fa-pencil"></span></a></td>
			<td><?php echo ucfirst($brand1['brand']); ?></td>
			<td><a href="brands.php?delete=<?php echo $brand1['id'] ?>" class="btn btn-xs btn-default"><span class="fa fa-remove"></span></a></td>
		</tr>
		<?php endwhile; ?>
	</tbody>
</table>
<?php 	include 'includes/footer.php'; ?>