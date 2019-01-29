<?php 
$sql = "SELECT * FROM categories WHERE parent = 0";
$result = pg_query($conn, $sql);
?>

<!-- Top Nav Bar -->
<nav class="navbar navbar-expand-lg navbar-fixed-top" >
	<div class="container">
	<a href="index.php" class="navbar-brand" >Temp Kart</a>
			<ul class="nav navbar-nav">

			<?php while($row = pg_fetch_assoc($result)) : ?>
			<?php
				$parent_id = $row['id'];
				$sql2 = 'SELECT * FROM categories WHERE parent = '.$parent_id.'';
				$result2 = pg_query($conn, $sql2);
			?>
			
				<li class="dropdown nav-item">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo ucfirst($row['category'])  ?><span class="caret"></span></a>
					<ul class="dropdown-menu" role="menu">
					<?php while($child = pg_fetch_assoc($result2)) : ?>
						<li><a href="category.php?cat=<?php  echo $child['id']; ?>"><?php echo ucfirst($child['category'])  ?></a></li>
					<?php endwhile; ?>
					</ul>
				</li>
				<?php endwhile; ?>
				<li><a href="cart.php"><span class="glyphicon glyphicon-shopping-cart"></span>My Cart</a></li>
			</ul>
		</div>
		
	</nav>