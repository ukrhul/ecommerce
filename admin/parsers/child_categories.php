<?php

require_once '../../core/constants.php';
require_once '../../core/db.php';


$parentID = (int)$_POST['parentID'];
$selected = sanitize($_POST['selected']);
$childQuery = pg_query($conn, "SELECT * FROM categories WHERE parent = '$parentID' ORDER BY category");

ob_start(); ?>

<option value=""></option>
<?php while($child = pg_fetch_assoc($childQuery)): ?>
		<option value="<?php echo $child['id']; ?>" <?php echo (($selected == $child['id'])? ' selected': ''); ?>><?php echo ucfirst($child['category']); ?></option>
<?php endwhile; ?>

<?php echo ob_get_clean(); ?>