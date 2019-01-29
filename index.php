<?php 

include "includes/header.php";
include "includes/navigation.php";
include "includes/headerfull.php";
include "includes/leftsidebar.php";

$sql = "SELECT * FROM products WHERE featured = 1";
$featured = pg_query($conn, $sql);

?>
		
	<!-- Main content -->
	<div class="col-md-8 text-center">
	<h2>Featured Products</h2>
		<div class="row ">
		<?php while($product = pg_fetch_assoc($featured)) : ?>			
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