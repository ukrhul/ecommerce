<?php 

	require_once $_SERVER['DOCUMENT_ROOT'].'/ecommerce/core/constants.php';
	require_once $_SERVER['DOCUMENT_ROOT'].'/ecommerce/core/db.php';
	include "includes/header.php";
	include "includes/navigation.php";
	include "includes/headerpartial.php";
	
	if($cart_id != '')
	{
		$cartQ = pg_query($conn, "SELECT * FROM cart WHERE id = $cart_id");
		$result = pg_fetch_assoc($cartQ);
		$items = json_decode($result['items'], true);
		$i = 1;
		$sub_total = 0;
		$item_count = 0;
		$available = 0;
		
	}
?>

<div class="col-md-12">
	<div class="row">
		<h2 class="text-center">My Shopping Cart</h2><hr/>
		<?php if($cart_id == ''): ?>
		<div class="bg-danger">
			<p class="text-center text-danger">
				Your shopping cart is empty!
			</p>
		</div>
		<?php else: ?>
		<table class="table table-bordered table-condensed table-striped">
			<thead>
				<th>#</th>
				<th>Items</th>
				<th>Price</th>
				<th>Quantity</th>
				<th>Size</th>
				<th>Sub Total</th>
			</thead>
			<tbody>
			<?php foreach($items as $item){
					$product_id = $item['id'];
					$productQ = pg_query($conn, "SELECT * FROM products WHERE id = $product_id");
					$product = pg_fetch_assoc($productQ);
					$sArray = explode('.',$product['sizes']);
					
					foreach($sArray as $sizeString){
						$s = explode(":", $sizeString);
						if($s[0] == $item['size']){
							$available = $s[1];
						}
					}
					?>
					
				<tr>
					<td><?php echo $i; ?></td>
					<td><?php echo $product['title']; ?></td>
					<td><?php echo money($product['price']); ?></td>
					<td>
						<button class="btn btn-xs btn-default" onClick="update_cart('removeone','<?php echo $product['id']; ?>', '<?php echo $item['size']; ?>');" >-</button>
						<?php echo $item['quantity']; ?>
						
						<?php if($item['quantity'] < $available): ?>
							<button class="btn btn-xs btn-default" onClick="update_cart('addone','<?php echo $product['id']; ?>', '<?php echo $item['size']; ?>');" >+</button>
							
						<?php else: ?>
							
							<span class="text-danger">Max</span>
						
						<?php endif; ?>
						
					</td>
					<td><?php echo $item['size']; ?></td>
					<td><?php echo money($item['quantity'] * $product['price']); ?></td>
				</tr>
					
					<?php 
						$i++;
						$item_count += $item['quantity'];
						$sub_total += ($product['price']*$item['quantity']);
					} 
					$tax = TAXRATE * $sub_total;
					$tax = number_format($tax, 2);
					$grand_total = $tax + $sub_total;
				?>
				
			</tbody>
		</table>
		<table class="table table-border table-condensed text-right">
		<legend class="text-center">Totals</legend>
		
			<thead >
				<th style="text-align: center;">Total Items</th>
				<th style="text-align: center;">Sub Total</th>
				<th style="text-align: center;">Tax</th>
				<th style="text-align: center;">Grand Total</th>
			</thead>
			<tbody>
				<tr>
					<td><?php echo $item_count; ?></td>
					<td><?php echo money($sub_total); ?></td>
					<td><?php echo money($tax); ?></td>
					<td class="bg-success"><?php echo money($grand_total); ?></td>
				</tr>
			</tbody>
		</table>
<!-- check out button -->
<button type="button" class="btn btn-primary  pull-right" data-toggle="modal" data-target="#checkoutModal">
 <span class="glyphicon glyphicon-shopping-cart"></span> Check Out >>
</button>

<!-- Modal -->
<div class="modal fade" id="checkoutModal" tabindex="-1" role="dialog" aria-labelledby="checkoutModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="checkoutModalLabel">Shipping Address</h4>
      </div>
      <div class="modal-body">
       <div class="row">
        <form action="thankyou.php" method="post" id="payment-form">
        <span class="bg-danger" id="payment-errors"></span>
        <input type="hidden" name="tax" value="<?php echo $tax; ?>" />
        <input type="hidden" name="sub_total" value="<?php echo $sub_total; ?>" />
        <input type="hidden" name="grand_total" value="<?php echo $grand_total; ?>" />
        <input type="hidden" name="cart_id" value="<?php echo $cart_id; ?>" />
        <input type="hidden" name="description" value="<?php echo $item_count.' item' . (($item_count > 1)?'s':''). ' from TempKart'; ?>" />
        	<div id="step1" style="display: block;">
        		<div class="form-group col-md-6">
        			<label for="full_name">Full Name:</label>
        			<input type="text" class="form-control" id="full_name" name="full_name" required />
        		</div>
        		<div class="form-group col-md-6">
        			<label for="email">Email:</label>
        			<input type="email" class="form-control" id="email" name="email" required />
        		</div>
        		<div class="form-group col-md-6">
        			<label for="street">Street Address:</label>
        			<input type="text" class="form-control" id="street" name="street" data-stripe="address_line1" required />
        		</div>
        		<div class="form-group col-md-6">
        			<label for="street2">Street Address 2:</label>
        			<input type="text" class="form-control" id="street2" name="street2" data-stripe="address_line2" required />
        		</div>
        		<div class="form-group col-md-6">
        			<label for="city">City:</label>
        			<input type="text" class="form-control" id="city" name="city" data-stripe="address_city" required />
        		</div>
        		<div class="form-group col-md-6">
        			<label for="state">State:</label>
        			<input type="text" class="form-control" id="state" name="state" data-stripe="address_state" required />
        		</div>
        		<div class="form-group col-md-6">
        			<label for="zipcode">Zip Code:</label>
        			<input type="text" class="form-control" id="zipcode" name="zipcode" data-stripe="address_zip" required />
        		</div>
        		<div class="form-group col-md-6">
        			<label for="country">Country:</label>
        			<input type="text" class="form-control" id="country" name="country" data-stripe="address_country" required />
        		</div>
        	</div>
        	<div id="step2" style="display: none;">
        		
        		<div class="form-row">
					
					<div class="form-group col-md-3">
						<label for="name">Name on Card:</label>
						<input type="text" id="name" class="form-control" data-stripe="name" required />
	        		</div>
					<div class="form-group col-md-6" id="card-element">
					  <!-- A Stripe Element will be inserted here. -->
					</div>

					<!-- Used to display Element errors. -->
					<div id="card-errors" role="alert"></div>
				  </div>

        	</div>
        
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onClick="check_address();" id="next_button">Next >></button>

        <button type="button" class="btn btn-primary" onClick="back_address();" id="back_button" style="display: none;">>> Back</button>
        <button type="submit" class="btn btn-primary" id="checkout_button" style="display: none;">Check Out >></button>

      </div>
      </form>
    </div>
  </div>
</div>
		<?php endif; ?>
	</div>
</div>

<script>

	function back_address(){
		jQuery('#payment-errors').html("");
		jQuery('#step1').css({"display":"block"});
		jQuery('#step2').css({"display":"none"});
		jQuery('#next_button').css({"display":"inline-block"});
		jQuery('#back_button').css({"display":"none"});
		jQuery('#checkout_button').css({"display":"none"});
		jQuery('#checkoutModalLabel').html("Shipping Address");
	}
	
	function check_address(){
		var data = {
			'full_name' : jQuery('#full_name').val(),
			'email' : jQuery('#email').val(),
			'street' : jQuery('#street').val(),
			'street2' : jQuery('#street2').val(),
			'city' : jQuery('#city').val(),
			'state' : jQuery('#state').val(),
			'country' : jQuery('#country').val(),
			'zipcode' : jQuery('#zipcode').val(),
		};
		
		jQuery.ajax({
			url	    : '/ecommerce/admin/parsers/check_address.php',
			method	: 'POST',
			data	: data,
			success	: function(data){
				if(data != 'passed'){
					jQuery('#payment-errors').html(data);
				}
				
				if(data == 'passed'){
					jQuery('#payment-errors').html("");
					jQuery('#step1').css({"display":"none"});
					jQuery('#step2').css({"display":"block"});
					jQuery('#next_button').css({"display":"none"});
					jQuery('#back_button').css({"display":"inline-block"});
					jQuery('#checkout_button').css({"display":"inline-block"});
					jQuery('#checkoutModalLabel').html("Enter Your Card Details");
				}
			},
			error	: function(){
				alert("Something went wrong!");
			}
			
		});
	}


	
	var stripe = Stripe('<?php echo STRIPE_PUBLIC; ?>');
	var elements = stripe.elements();
	
	
	// Custom styling can be passed to options when creating an Element.
	var style = {
	  base: {
		// Add your base input styles here. For example:
		fontSize: '16px',
		color: "#32325d",
	  }
	};

	// Create an instance of the card Element.
	var card = elements.create('card', {style: style});

	// Add an instance of the card Element into the `card-element` <div>.
	card.mount('#card-element');
	
	
	card.addEventListener('change', function(event) {
	  var displayError = document.getElementById('card-errors');
	  if (event.error) {
		displayError.textContent = event.error.message;
	  } else {
		displayError.textContent = '';
	  }
	});
	
	
	// Create a token or display an error when the form is submitted.
	var form = document.getElementById('payment-form');
	form.addEventListener('submit', function(event) {
	  event.preventDefault();

	  stripe.createToken(card).then(function(result) {
		if (result.error) {
		  // Inform the customer that there was an error.
		  var errorElement = document.getElementById('card-errors');
		  errorElement.textContent = result.error.message;
		} else {
		  // Send the token to your server.
		  stripeTokenHandler(result.token);
		}
	  });
	});
	
	
	function stripeTokenHandler(token) {
	  // Insert the token ID into the form so it gets submitted to the server
	  var form = document.getElementById('payment-form');
	  var hiddenInput = document.createElement('input');
	  hiddenInput.setAttribute('type', 'hidden');
	  hiddenInput.setAttribute('name', 'stripeToken');
	  hiddenInput.setAttribute('value', token.id);
	  form.appendChild(hiddenInput);

	  // Submit the form
	  form.submit();
	}
	
</script>



<?php include "includes/footer.php"; ?>