<!-- Top Nav Bar -->
<nav class="navbar navbar-expand-lg navbar-fixed-top" >
	<div class="container">
		<a href="index.php" class="navbar-brand">Temp Kart</a>
			<ul class="nav navbar-nav">
				<li><a href="index.php">My Dashboard</a></li>
				<li><a href="brands.php">Brands</a></li>
				<li><a href="categories.php">Categories</a></li>
				<li><a href="products.php">Products</a></li>
				<li><a href="archived.php">Archived</a></li><?php if(has_permission('admin')): ?>	
					<li><a href="users.php">Users</a></li>	
				<?php endif; ?>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">Hello <?php echo ucfirst($user_data['first']); ?>
					<span class="caret"></span>
					</a>
					<ul class="dropdown-menu" role="menu">
						<li><a href="change_password.php">Change Password</a></li>
						<li><a href="logout.php">Log Out</a></li>
					</ul>
				</li>
			</ul>
		</div>
		
	</nav>
<div class="container-fluid">	