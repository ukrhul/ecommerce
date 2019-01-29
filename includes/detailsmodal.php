<?php 
require_once('../core/constants.php');
require_once '../core/db.php';
ob_start(); 
$id = $_POST['id'];
$id = (int)$id;

$sql = "SELECT * FROM products WHERE id = '$id'";

$resultmodal = pg_query($conn, $sql);

$product = pg_fetch_assoc($resultmodal);

$brand_id = $product['brand']; 
$sql2 = "SELECT brand FROM brand WHERE id = '$brand_id'";

$resultbrand = pg_query($conn, $sql2);

$brand = pg_fetch_assoc($resultbrand);

$sizestring = $product['sizes'];
$sizestring = rtrim($sizestring,',');
$size_array = explode(',',$sizestring);



?>


<!-- Details modal -->

<div class="modal fade details-1" id="details-modal" tabindex="-1" role="dialog" aria-labelledby="details-1" aria-hidden="true">
	
	<div class="modal-dialog modal-lg">
	<div class="modal-content">
		<div class="modal-header">
		<h4 class="modal-title text-center"><?php echo ucfirst($product['title']); ?></h4>
			<button class="close" type="button"	onclick="closeModal()" aria-label="close">
				<span aria-hidden="true" >&times;</span>
			</button>
			
		</div>
		<div class="modal-body">
			<div class="container-fluid">
				<div class="row">
				<span id="modal_errors" class="bg-danger"></span>
					<div class="col-sm-6 fotorama">
					<?php $photos = explode(',', $product['image']);
						foreach($photos as $photo): ?>
					
						<img src="<?php echo '.'.$photo; ?>" alt="<?php echo ucfirst($product['title']); ?>" class="details img-responsive" width="50%" />
						
						<?php endforeach; ?>
					</div>
					<div class="col-sm-6">
						<h4>Details</h4>
						<p><?php echo nl2br(ucfirst($product['description'])); ?></p>
						<hr>
						<p>Price : <?php echo $product['price']; ?></p>
						<p>Brand: <?php echo $brand['brand']; ?></p>
						
						<form action="add_cart.php" method="post" id="add_product_form">
						
						<input type="hidden" name="product_id" value="<?php echo $id; ?>" />
						
						<input type="hidden" name="available" id="available" value="" />
						
						<div class="form-group">
							<div class="col-xs-3 ">
								<label for="quantity">Quantity:</label>
								<input type="number" min="1" class="form-control" id="quantity" name="quantity"  />
							</div>
							
						</div>
						<br/><br/><br/>
						<div class="form-group">
							<label for="size">Size:</label>
							<select name="size" id="size" class="form-control">
								<option value=""></option>
								<?php foreach($size_array as $string) {
									$string_array = explode(':', $string);
									$size = $string_array[0];
									$available = $string_array[1];
									if($available > 0){
										echo '<option value="'.$size.'" data-available="'.$available.'">'.$size.' ('.$available.' Available)</option>'; 
									}
								
								} ?>
								
							</select>
						</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<button class="btn btn-default" onclick="closeModal()" >Close</button>
			<button class="btn btn-warning" type="submit" onClick="add_to_cart(); return false;"><span class="glyphicon glyphicon-shopping-cart"></span>Add To Cart</button>
		</div>
	</div>
	</div>
	
</div>

<script>
	jQuery('#size').change(function(){
		var available = jQuery('#size option:selected').data('available');
		jQuery('#available').val(available);
	});
	
	$(function () {
  		$('.fotorama').fotorama({'loop':true, 'autoplay':true});
	});
	
	function closeModal(){
		jQuery('#details-modal').modal('hide');
		setTimeout(function(){
			jQuery('#details-modal').remove(); 
			jQuery('.modal-backdrop').remove();
		},500);
	}
</script>
<?php echo ob_get_clean(); ?>