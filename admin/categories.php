<?php 
	require_once '../core/constants.php';
	require_once '../core/db.php';
	if(!is_logged_in())
	{
		login_error_redirect();
	}
	include 'includes/header.php';
	include 'includes/navigation.php';

	$sql_parent = "SELECT * FROM categories WHERE parent = 0";
	$result_parent = pg_query($conn, $sql_parent); 
	$errors = array();
	$display = '';
	$category = '';
	$post_parent = 0;

	//Edit category
	if(isset($_GET['edit']) && !empty($_GET['edit'])){	
		$edit_id = (int)$_GET['edit'];
		$edit_id = sanitize($edit_id);
		$sqledit = "SELECT * FROM categories WHERE id = $edit_id";
		$edit_result = pg_query($conn, $sqledit);
		$eCategory = pg_fetch_assoc($edit_result);
	 }

	//Delete category
	if(isset($_GET['delete']) && !empty($_GET['delete']))
	{
		$delete_id = (int)$_GET['delete'];
		$delete_id = sanitize($delete_id);
		$sql = "SELECT * FROM categories WHERE id = $delete_id";
		$result = pg_query($conn, $sql);
		$category = pg_fetch_assoc($result);
		if($category['parent'] == 0);
		{
			$sql = "DELETE FROM categories WHERE PARENT = $delete_id";
			$result = pg_query($conn, $sql);
		}
		$sql_delete = "DELETE FROM categories WHERE id = $delete_id";
		$delete_result = pg_query($conn, $sql_delete);
		header('Location: categories.php');
	}
	
	//Process Form
	if(isset($_POST)&& !empty($_POST))
	{
		
		$post_parent = strtolower(sanitize($_POST['parent']));
		$category = strtolower(sanitize($_POST['category']));
		
		$sql_category = "SELECT * FROM categories WHERE category = '$category' AND parent = '$post_parent'";
		if(isset($_GET['edit']))
		{
			$id = $eCategory['id'];
			$sql_category = "SELECT * FROM categories WHERE category = '$category' AND parent = '$post_parent' AND id != $id";
		}
		$cresult = pg_query($conn, $sql_category);
		$ccount = pg_num_rows($cresult);
		
		//if category is blank
		if($category == '')
		{
			$errors[] .= 'The category cannot be left blank.';
		}
		
		//If exists in the database
		if($ccount > 0){
			$errors[] .= ucfirst($category). ' already exists. Please choose a new category.';
		}
		
		//Display Errors or Update Database
		if(!empty($errors)){
			//display errors
			$display = display_errors($errors); 
		
		}else{
			//update database
			$updatesql = "INSERT INTO categories (category, parent) VALUES ('$category',$post_parent)";
			
			if(isset($_GET['edit'])){
				$updatesql = "UPDATE categories SET category = '$category', parent = $post_parent WHERE id = $edit_id";
			}
			
			$update_result = pg_query($conn, $updatesql);
			header('Location: categories.php');
		}
	}
	
	$category_value = '';
	$parent_value = 0;
	if(isset($_GET['edit']))
	{
		$category_value = $eCategory['category'];
		$parent_value = $eCategory['parent'];
	}else{
		if(isset($_POST))
		{
			$category_value = $category;
			$parent_value = $post_parent;
			
		}
	}
?>

	
<h2 class="text-center">CATEGORIES</h2>	
<hr/>

<div class="row">
	<div class="col-md-6">
		<form class="form" action="categories.php<?php echo ((isset($_GET['edit']))) ? '?edit='.$edit_id.'':''; ?>" method="post" >
		<legend class="text-center"><?php echo ((isset($_GET['edit']))) ? 'Edit':'Add A'; ?> Category</legend>
		<div id="errors"><?php echo $display; ?></div>
			<div class="form-group">
				<label for="parent">Parent</label>
				<select class="form-control" name="parent" id="parent">
					<option value="0"<?php echo (($parent_value == 0) ? ' selected="selected"':''); ?>>Parent</option>
					 <?php while($row_parent = pg_fetch_assoc($result_parent)) {
						 echo '<option value="'. $row_parent['id'] . '"' .(($parent_value == $row_parent['id']) ? ' selected="selected"' : ''). '>'. ucfirst($row_parent['category']) .'</option>'; } ?>
										
				</select>
			</div>	
			<div class="form-group">
				<label for="category" >Category</label>
				<input type="text" class="form-control" id="category" name="category" value="<?php echo ucfirst($category_value); ?>" />
			</div>	
			<div class="form-group">
				<input type="submit" value="<?php echo ((isset($_GET['edit']))) ? 'Edit':'Add'; ?> Category" class="btn btn-success" />
			</div>	
		</form>
	</div>

	<!-- Category Table-->
	<div class="col-md-6">
		<table class="table table-bordered">
			<thead>
				<th>Category</th>
				<th>Parent</th>
				<th></th>
			</thead>
			<tbody>
			 <?php 
				
				$sql_parent = "SELECT * FROM categories WHERE parent = 0";
				$result_parent = pg_query($conn, $sql_parent); 
				
				
				while($row_parent = pg_fetch_assoc($result_parent)) : 
				$parent_id = (int)$row_parent['id'];
				$sql_child = "SELECT * FROM categories WHERE parent = $parent_id";
				
				$result_child = pg_query($conn, $sql_child);
			
			 ?>
				 <tr class="bg-primary">
				 	<td><?php echo ucfirst($row_parent['category']); ?></td>
				 	<td>Parent</td>
				 	<td>
				 		<a href="categories.php?edit=<?php echo $row_parent['id'] ?>" class="btn btn-xs btn-default" ><span class="glyphicon glyphicon-pencil"></span></a>
						<a href="categories.php?delete=<?php echo $row_parent['id'] ?>" class="btn btn-xs btn-default" ><span class="glyphicon glyphicon-remove"></span></a>
				 	</td>
				 </tr>
				 	<?php while($row_child = pg_fetch_assoc($result_child)) : ?>
				 		
				 	<tr class="bg-info">
				 		<td><?php echo ucfirst($row_child['category']); ?></td>
				 		<td><?php echo ucfirst($row_parent['category']);  ?></td>
				 		<td>
				 		<a href="categories.php?edit=<?php echo $row_child['id'] ?>" class="btn btn-xs btn-default" ><span class="glyphicon glyphicon-pencil"></span></a>
						<a href="categories.php?delete=<?php echo $row_child['id'] ?>" class="btn btn-xs btn-default" ><span class="glyphicon glyphicon-remove"></span></a>
				 		</td>
				 	</tr>
				 
					<?php endwhile; ?>
				 <?php endwhile; ?>
			</tbody>	
		</table>
	</div>
</div>



<?php 	include 'includes/footer.php'; ?>