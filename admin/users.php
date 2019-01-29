<?php 
	require '../core/constants.php';
	require_once '../core/db.php';
	if(!is_logged_in())
	{
		login_error_redirect();
	}

	if(!has_permission('admin')){
		permission_error_redirect('index.php');
	}
	include 'includes/header.php';
	include 'includes/navigation.php';
	if(isset($_GET['delete']))
	{
		$delete_id = sanitize($_GET['delete']);
		$delete_query = pg_query($conn, "DELETE FROM users WHERE id = $delete_id");
		$_SESSION['success_flash'] = "User has been deleted";
		header('Location: users.php');
		
	}
	if(isset($_GET['add']))
	{
		$name = ((isset($_POST['name']))?sanitize($_POST['name']) : '');
		$email = ((isset($_POST['email']))?sanitize($_POST['email']) : '');
		$password = ((isset($_POST['password']))?sanitize($_POST['password']) : '');
		$confirm = ((isset($_POST['confirm']))?sanitize($_POST['confirm']) : '');
		$permission = ((isset($_POST['permission']))?sanitize($_POST['permission']) : '');
		
		$errors = array();
		
		if($_POST){
			
			$emailQuery = pg_query($conn, "SELECT * FROM users WHERE email = '$email'");
			$emailCount = pg_num_rows($emailQuery);
			
			if($emailCount != 0)
			{
				$errors[] .= "Email already exists";
			}
			
			$required = array('name', 'email', 'password', 'confirm', 'permission');
			
			foreach($required as $f){
				if(empty($_POST[$f])){
					$errors[] = "You must fill out all fields.";
					break;
				}
			}
			
			if(strlen($password) < 6)
			{
				$errors[] .= "Your password must be atleast 6 characters long.";
			}
			
			if($password != $confirm)
			{
				$errors[] .= "Password and confirm password must be same.";
			}
			
			if(!filter_var($email,FILTER_VALIDATE_EMAIL))
			{
				$errors[] .= "You must enter a valid email.";
			}
			
			if(!empty($errors)){
				echo display_errors($errors);
			}else{
				//add user to database
				$hashed = password_hash($password, PASSWORD_DEFAULT);
				
				$last_login = date("Y-m-d h:i:s");
				$add_query = pg_query($conn, "INSERT INTO users (full_name, email, password, last_login, permission) VALUES ('$name','$email','$hashed','$last_login','$permission')");
				
				$_SESSION['success_flash']="User has been added!";
				header('Location: users.php');
					
			}
		}
		
		?>
		<h2 class="text-center">Add a New User</h2><hr/>
		<form action="users.php?add=1" method="post" >
			<div class="form-group col-md-6">
				<label for="name" >Full Name:</label>
				<input type="text" name="name" id="name" class="form-control" value="<?php echo $name; ?>" required />
			</div>
			<div class="form-group col-md-6">
				<label for="email" >Email:</label>
				<input type="email" name="email" id="email" class="form-control" value="<?php echo $email; ?>" required />
			</div>
			<div class="form-group col-md-6">
				<label for="password" >Password:</label>
				<input type="password" name="password" id="password" class="form-control" value="<?php echo $password; ?>" required />
			</div>
			<div class="form-group col-md-6">
				<label for="confirm" >Confirm Password:</label>
				<input type="password" name="confirm" id="confirm" class="form-control" value="<?php echo $confirm; ?>" required />
			</div>
			<div class="form-group col-md-6">
				<label for="permission" >Permissions:</label>
				<select class="form-control" name="permission">
					<option value=""<?php echo (($permission == '')? ' selected':'')?>></option>
					<option value="editor"<?php echo (($permission == 'editor')? ' selected':'')?>>Editor</option>
					<option value="admin,editor"<?php echo (($permission == 'admin,editor')? ' selected':'')?>>Admin</option>
				</select>
			</div>
			<div class="form-group col-md-6 text-right" style="margin-top: 25px;">
				<a href="users.php" class="btn btn-default">Cancel</a>
				<input type="submit" class="btn btn-primary" value="Add User" />
			</div>
		</form>
		<?php
	}else{
		
	$userQuery = pg_query($conn, "SELECT * FROM users ORDER BY full_name");

?>

<h2 class="text-center">USERS</h2>
<a href="users.php?add=1" class="btn btn-success pull-right" style="margin-top: -35px;">Add New User</a>
<div class="clearfix"></div>
<hr/>
<table class="table table-bordered table-stripped table-condensed">
	<thead>
	<th></th>
	<th>Name</th>
	<th>Email</th>
	<th>Join Date</th>
	<th>Last Login</th>
	<th>Permission</th>
	</thead>
	<tbody>
	<?php while($user = pg_fetch_assoc($userQuery)): ?>
		<tr>
			<td>
				<?php if($user['id'] != $user_data['id']): ?>
					<a href="users.php?delete=<?php echo $user['id']; ?>" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-remove"></span></a> 
				
				<?php endif; ?>
			</td>
			<td><?php echo $user['full_name']; ?></td>
			<td><?php echo $user['email']; ?></td>
			<td><?php echo pretty_date($user['join_date']); ?></td>
			<td><?php echo pretty_date($user['last_login']); ?></td>
			<td><?php echo $user['permission']; ?></td>
		</tr>
		<?php endwhile; ?>
	</tbody>
</table>



<?php }	include 'includes/footer.php'; ?>