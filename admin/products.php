<?php 
	require_once '../core/constants.php';
	require_once '../core/db.php';
	if(!is_logged_in())
	{
		login_error_redirect();
	}
	include 'includes/header.php';
	include 'includes/navigation.php';

	//Delete Product
	if(isset($_GET['delete']))
	{
		$delete_id = sanitize($_GET['delete']);
		$delete_result = pg_query($conn, "UPDATE products SET deleted = 1, featured = 0 WHERE id = $delete_id");
		header('Location: products.php');
	}

	$dbPath = '';
	if(isset($_GET['add']) || isset($_GET['edit']))
	{
		$brandQuery = pg_query($conn,"SELECT * FROM brand ORDER BY brand");
		$parentQuery = pg_query($conn, "SELECT * FROM categories WHERE parent = 0 ORDER BY category");
		
		$title = ((isset($_POST['title']) && $_POST['title'] != '')? sanitize($_POST['title']):'');
		$brand = ((isset($_POST['brand']) && $_POST['brand'] != '')? sanitize($_POST['brand']):'');
		$parent = ((isset($_POST['parent']) && $_POST['parent'] != '')? sanitize($_POST['parent']):'');
		$category = ((isset($_POST['child']) && $_POST['child'] != '')? sanitize($_POST['child']):'');
		$price = ((isset($_POST['price']) && $_POST['price'] != '')? sanitize($_POST['price']):'');
		$list_price = ((isset($_POST['list_price']) && $_POST['list_price'] != '')? sanitize($_POST['list_price']):'');
		$description = ((isset($_POST['description']) && $_POST['description'] != '')? sanitize($_POST['description']):'');
		$sizes = ((isset($_POST['size']) && $_POST['size'] != '')? sanitize($_POST['size']):'');
		$sizes = rtrim($sizes,",");

		$saved_image = '';
		
		if(isset($_GET['edit'])){
			
			$edit_id = (int)$_GET['edit'];
			$product_result = pg_query($conn, "SELECT * FROM products WHERE id = $edit_id");
			$product = pg_fetch_assoc($product_result);
			
			if(isset($_GET['delete_image'])){
				$imgi = (int)$_GET['imgi'] - 1;
				$images = explode(',',$product['image']);
				$image_url = $_SERVER['DOCUMENT_ROOT'].$images[$imgi];
				unlink($image_url);
				unset($images[$imgi]);
				$imageString = implode(',', $images);
				$image_remove = pg_query($conn, "UPDATE products SET image = '{$imageString}' WHERE id = $edit_id");
				
				header('Location: products.php?edit='.$edit_id);
 			}
			
			$category = ((isset($_POST['child']) && $_POST['child'] != '') ? sanitize($_POST['child']) : $product['categories']);
			
			$title = ((isset($_POST['title']) && !empty($_POST['title'])) ? sanitize($_POST['title']) : $product['title']);
		
			$brand = ((isset($_POST['brand']) && !empty($_POST['brand'])) ? sanitize($_POST['brand']) : $product['brand']);

			$price = ((isset($_POST['price']) && !empty($_POST['price'])) ? sanitize($_POST['price']) : $product['price']);

			$list_price = ((isset($_POST['list_price'])) ? sanitize($_POST['list_price']) : $product['list_price']);
			
			$description = ((isset($_POST['description'])) ? sanitize($_POST['description']) : $product['description']);

			$sizes = ((isset($_POST['size']) && !empty($_POST['size'])) ? sanitize($_POST['size']) : $product['sizes']);
			
			$sizes = rtrim($sizes,",");

			$parentQ = pg_query($conn, "SELECT * FROM categories WHERE id = '$category'");
			
			$parentResult = pg_fetch_assoc($parentQ);
			
			$parent = ((isset($_POST['parent']) && !empty($_POST['parent']))?sanitize($_POST['parent']) : $parentResult['parent']);
			
			$saved_image = (($product['image'] != '')? $product['image']:'');
			
			$dbPath = $saved_image;
		}
		
		if(!empty($sizes)){
			$sizeString = sanitize($sizes);
			$sizeString = rtrim($sizeString, ','); 
			$sizesArray = explode(',',$sizeString);
			$sArray = array();
			$qArray = array();
			$tArray = array();
			
			foreach($sizesArray as $ss)
			{
				$s = explode(":", $ss);
				$sArray[] = $s[0];
				$qArray[] = $s[1];
				$tArray[] = $s[2];
			}
		}else{$sizesArray = array();}

		if ($_POST){
			
			$errors = array();
			
			$required = array('title', 'brand', 'price', 'parent', 'child', 'size');
			$allowed = array('png','jpg','jpeg','gif');
			$uploadPath = array();
			$tmpLoc = array();
			foreach($required as $field)
			{
				if($_POST[$field] == '')
				{
					$errors[] = 'All Fields With and Astrisk are required.';
					break;
				}
			}
			$photoCount = count($_FILES['photo']['name']);
			
			if($photoCount > 0){
				for($i=0; $i < $photoCount; $i++)
				{
					$name = $_FILES['photo']['name'][$i];
					$nameArray = explode('.',$name);
					$fileName = $nameArray[0];
					$fileExt = $nameArray[1];
					$mime = explode('/',$_FILES['photo']['type'][$i]);
					$mimeType = $mime[0];
					$mimeExt = $mime[1];
					$tmpLoc[] = $_FILES['photo']['tmp_name'][$i];
					$fileSize = $_FILES['photo']['size'][$i];
					$uploadName = md5(microtime().$i).'.'.$fileExt;
					$uploadPath[] = '../images/products/'.$uploadName;
					if($i != 0){
						$dbPath .= ',';
					}
					$dbPath .= '/images/products/'.$uploadName;
					if($mimeType != 'image'){
						$errors[] = 'The file must be an image.';
					}
					if(!in_array($fileExt,$allowed)){
						$errors[] = "The photo must be a png, jpg, jpeg or gif.";
					}
					if($fileSize > 1000000){
						$errors[] = "The file size must be under 10 MB";
					}
					if($fileExt != $mimeExt && ($mimeExt == 'jpeg' && $fileExt != 'jpg')){
						$errors[] = "File extension does not match the file";
					}
				}

			}
			
			if(!empty($errors))
			{
				echo display_errors($errors);
			}else{
				//upload file and insert into database
				if($photoCount > 0){
					for($i=0; $i < $photoCount; $i++)
					{
						move_uploaded_file($tmpLoc[$i],"$uploadPath[$i]");
					}
				}
				$insertSql = "INSERT INTO products (title, price, list_price, brand, categories, image, description, sizes) VALUES ('$title','$price','$list_price','$brand','$category','$dbPath','$description','$sizes')";
				
				if(isset($_GET['edit']))
				{
					$insertSql = "UPDATE products SET title = '$title',price = '$price', list_price = '$list_price', brand = '$brand', categories = '$category', image = '$dbPath', description = '$description', sizes = '$sizes' WHERE id = $edit_id";
				}
				
				$result = pg_query($conn, $insertSql);
				header('Location: products.php');
			}
		}
	?>
		<h2 class="text-center"><?php echo ((isset($_GET['edit']))?'Edit ':'Add A New '); ?>Product</h2><hr/>
		<form action="products.php?<?php echo ((isset($_GET['edit']))? 'edit='.$edit_id : 'add=1'); ?>" method="post" enctype="multipart/form-data">
			<div class="form-group col-md-3">
				<label for="title">Title*:</label>
				<input type="text" class="form-control" name="title" id="title" value="<?php echo $title; ?>" required />
			</div>
			<div class="form-group col-md-3">
				<label for="brand">Brand*:</label>
				<select class="form-control" id="brand" name="brand">
					<option value=""<?php echo (($brand == '') ? ' selected' :''); ?></option>
					<?php while($b = pg_fetch_assoc($brandQuery)) : ?>
					<option value="<?php echo $b['id']; ?>" <?php echo (($brand == $b['id'])? ' selected' : '');  ?>><?php echo ucfirst($b['brand']); ?> </option>
					<?php endwhile; ?>
				</select>
			</div>
			<div class="form-group col-md-3">
				<label for="parent">Parent*:</label>
				<select class="form-control" id="parent" name="parent">
					<option value=""<?php echo ((isset($_POST['parent']) && $_POST['parent'] == '') ? ' selected' :''); ?></option>
					<?php while($p = pg_fetch_assoc($parentQuery)): ?>
						<option value="<?php echo $p['id']; ?>" <?php echo (($parent == $p['id'])? ' selected' : '');  ?>><?php echo ucfirst($p['category']); ?> </option>
					<?php endwhile; ?>
				</select>
			</div>
			<div class="form-group col-md-3">
				<label for="child">Child Category*:</label>
				<select id="child" name="child" class="form-control">
				</select>
			</div>
			<div class="form-group col-md-3">
				<label for="price">Price*:</label>
				<input type="text" id="price" name="price" class="form-control" value="<?php echo $price; ?>" required/>
			</div>
			<div class="form-group col-md-3">
				<label for="price">List Price:</label>
				<input type="text" id="list_price" name="list_price" class="form-control" value="<?php echo $list_price; ?>" />
			</div>
	
		<div class="form-group col-md-3">
		<label>Quantity & Sizes*:</label>
			<button class="btn btn-default form-control" onClick="jQuery('#sizesModal').modal('toggle'); return false;" >Quantity & Sizes</button>
		</div>

		<div class="form-group col-md-3">
			<label for="size">Sizes & Qty Preview</label>
			<input type="text" class="form-control" name="size" id="size" value="<?php echo $sizes; ?>" readonly />
		</div>		
		<div class="form-group col-md-6">
			
			<?php if($saved_image != ''): ?>
				<?php 
					$imgi = 1;
					$images = explode(',', $saved_image);
				?>
				<?php foreach($images as $image) : ?>
				<div class="saved-image col-md-4">
				<img src="<?php echo '..'.$image; ?>" alt="saved image" class="img-responsive" ><br/>
				<a href="products.php?delete_image=1&edit=<?php echo $edit_id; ?>&imgi=<?php echo $imgi; ?>" class="text-danger">Delete Image</a>
				</div>
				<?php 
					$imgi++; 
					endforeach; 
				?>
			<?php else: ?>
			<label for="photo">Product Photo:</label>
			<input type="file" name="photo[]" id="photo" class="form-control" multiple />
			<?php endif; ?>
		</div>
		<div class="form-group col-md-6">
			<label for="description">Description:</label>
			<textarea id="description" name="description" class="form-contol" rows="6" cols="65" 
			><?php echo $description; ?></textarea>
		</div>
		<div class="pull-right form-group">
		<a href="products.php" class="btn btn-default ">Cancel</a>
			<input type="submit" value="<?php echo (isset($_GET['edit'])? 'Edit' : 'Add' ); ?> Product" class="btn btn-success " />
		<div class="clearfix"></div>
		</div>
		
		<!-- Modal -->
		<div class="modal fade " id="sizesModal" tabindex="-1" role="dialog" aria-labelledby="sizesModalLabel">
		  <div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
			  <div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="sizesModalLabel">Size & Quantity</h4>
			  </div>
			  <div class="modal-body">
			  <div class="container-fluid">
				<?php for($i=1; $i <= 12; $i++): ?>
					<div class="form-group col-md-2">
						<label for="size<?php echo $i; ?>">Size:</label>					
					<input type="text" name="size<?php echo $i; ?>" id="size<?php echo $i; ?>" value="<?php echo ((!empty($sArray[$i-1]))?$sArray[$i-1]:''); ?>" class="form-control" />
					</div>
					<div class="form-group col-md-2">
						<label for="qty<?php echo $i; ?>">Quantity:</label>					
					<input type="number" name="qty<?php echo $i; ?>" id="qty<?php echo $i; ?>" value="<?php echo ((!empty($qArray[$i-1]))?$qArray[$i-1]:''); ?>" min="0" class="form-control"/>
					</div>
					<div class="form-group col-md-2">
						<label for="threshold<?php echo $i; ?>">Threshold:</label>					
					<input type="number" name="threshold<?php echo $i; ?>" id="threshold<?php echo $i; ?>" value="<?php echo ((!empty($tArray[$i-1]))?$tArray[$i-1]:''); ?>" min="0" class="form-control"/>
					</div>
				<?php endfor; ?>
				</div>
			  </div>
			  <div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" onClick="updateSizes(); jQuery('#sizesModal').modal('toggle'); return false;">Save changes</button>
			  </div>
			</div>
		  </div>
		</div>
		</form> 
	</div>
	<?php }else{
		
	$sql = "SELECT * FROM products WHERE deleted = 0";
	$preresults= pg_query($conn, $sql);

	if(isset($_GET['featured'])) 
	{
		$id = (int)$_GET['id'];
		$featured = (int)$_GET['featured'];
		$featuredsql = "UPDATE products SET featured = '$featured' WHERE id = '$id'";
		$featuredresult = pg_query($conn, $featuredsql);
		header('Location: products.php');
	}


?>

<h2 class="text-center">PRODUCTS</h2>

<a href="products.php?add=1" class="btn btn-success pull-right" style="margin-top: -35px;" >Add Product</a><div class="clearfix"></div>
<hr/>
<table class="table table-bordered table-condensed table-striped">
	<thead>
		<th></th>
		<th>Products</th>
		<th>Price</th>
		<th>Category</th>
		<th>Featured</th>
		<th>Sold</th>
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
					<a href="products.php?edit=<?php echo $products['id'] ?>" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-pencil"></span></a> 
					<a href="products.php?delete=<?php echo $products['id'] ?>" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-remove"></span></a> 
				</td>
				<td><?php echo $products['title']; ?></td>
				<td><?php echo money($products['price']); ?></td>
				<td><?php echo $category; ?></td>
				<td><a href="products.php?featured=<?php echo (($products['featured'] == 0)? '1' : '0'); ?>&id=<?php echo $products['id']; ?>" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-<?php echo (($products['featured'] == 1 ) ? 'minus' : 'plus' ); ?>" ></span>
				&nbsp; <?php echo (($products['featured'] == 1)? 'Featured Product':''); ?>
				</a></td>
				<td><?php echo $products['deleted']; ?></td>

			</tr>
		<?php endwhile; ?>
	</tbody>
</table>



<?php }	include 'includes/footer.php'; ?>


<script>
	jQuery('document').ready(function(){
		get_child_options('<?php echo $category; ?>');
	});
</script>