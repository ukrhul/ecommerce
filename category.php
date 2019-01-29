<?php 

include "includes/header.php";
include "includes/navigation.php";
include "includes/headerpartial.php";
include "includes/leftsidebar.php";

if(isset($_GET['cat']))
{
	$cat_id = sanitize($_GET['cat']);
	
}else{
	$cat_id = '';
}

$sql = "SELECT * FROM products WHERE categories = '$cat_id'";
$productQ = pg_query($conn, $sql);
$category = get_category($cat_id); 

?>
		
	<!-- Main content -->
	<div class="col-md-8 text-center">
	
		<div class="row ">
		<h2><?php echo ucfirst($category['parent']) . ' ' . ucfirst($category['child']); ?></h2>
		<?php while($product = pg_fetch_assoc($productQ)) : ?>			
			<div class="col-md-3">
			
				<h4><?php echo $product['title'] ?></h4>
				<?php $photos = explode(',',$product['image']); ?>
				<img src="<?php echo '.'.$photos[0] ; ?>" class="img-responsive img-thumb"  alt="Levis Jeans" />
				<p class="list-price text-danger">List Price: $<s><?php echo $product['list_price'] ?></s></p>
				<p class="price">Our Price: $<?php echo $product['price'] ?></p>
				<button type="button" class="btn btn-sm btn-success" data-toggle="modal" onclick="detailsmodal(<?php  echo $product['id']; ?>)">Details</button>
			</div>
			
			<?php endwhile; ?>
			
		</div>
	</div>

<?php

include "includes/rightsidebar.php";
include "includes/footer.php";

?>