<?php 
	require_once '../core/constants.php';
	require_once '../core/db.php';
	if(!is_logged_in()){
		login_error_redirect();
	}
	include 'includes/header.php';

	$hashed = $user_data['password'];

	$old_password = ((isset($_POST['old_password'])? sanitize($_POST['old_password']):''));
	$old_password = trim($old_password);

	$password = ((isset($_POST['password'])? sanitize($_POST['password']):''));
	$password = trim($password);

	$confirm = ((isset($_POST['confirm'])? sanitize($_POST['confirm']):''));
	$confirm = trim($confirm);
	$new_hashed = password_hash($password, PASSWORD_DEFAULT);
	$user_id = $user_data['id'];
	$errors = array();
?>
<style>
	body{
		background: url(https://i.pinimg.com/originals/16/ed/25/16ed25195dff2d891da215c28cc75fba.jpg) no-repeat fixed center;
		background-size: cover;
	}
</style>

<div id="login-form">
	<div>
		<?php 
			if($_POST){
				//form validation
				if(empty($_POST['old_password']) || empty($_POST['password']) || empty($_POST['confirm'])){
					$errors[] = "You must fill out all fields.";
				}
				
				if(strlen($password) < 6){
					$errors[] = "Password must be at least 6 characters.";
				}
								
				//password matches
				if($password != $confirm){
					$errors[] = "The new password and confirm new password does not match";
				}
				
				if(!password_verify($old_password, $hashed)){
					$errors[] = "Your old password does not match our records.";
				}
				
				if($old_password == $password)
				{
					$errors[] = "Your new password should be different from old password.";
				}
				
				if(!empty($errors))
				{
					echo display_errors($errors);
				}else{
					//change password in database
					$update_pass_query = pg_query($conn, "UPDATE users SET password = '$new_hashed' WHERE id = $user_id");
					$_SESSION['success_flash'] = 'Your password has been updated!';
					header('Location: index.php');
				}
			}
		?>
	</div>
	<h2 class="text-center">Change Password</h2><hr/>
	<form action="change_password.php" method="post" >
		<div class="form-group">
			<label for="old_password">Old Password:</label>
			<input type="password" class="form-control" name="old_password" id="old_password" value="<?php echo $old_password;?>" required />
		</div>
		<div class="form-group">
			<label for="password">New Password:</label>
			<input type="password" class="form-control" name="password" id="password" value="<?php echo $password;?>" required />
		</div>
		<div class="form-group">
			<label for="confirm">Confirm New Password:</label>
			<input type="password" class="form-control" name="confirm" id="confirm" value="<?php echo $confirm;?>" required />
		</div>
		<div class="form-group">
			<a href="index.php" class="btn btn-default">Cancel</a>
			<input type="submit" class="btn btn-primary" value="Change" />
		</div>
	</form>
	<p class="text-right"><a href="../index.php" alt="home">Visit Site</a></p>
</div>





<?php include 'includes/footer.php'; ?>