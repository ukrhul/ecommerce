<?php 
	require_once '../core/constants.php';
	require_once '../core/db.php';
	include 'includes/header.php';

	$email = (isset($_POST['email'])? sanitize($_POST['email']):'');
	$email = trim($email);
	$password = ((isset($_POST['password'])? sanitize($_POST['password']):''));
	$password = trim($password);

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
				if(empty($_POST['email']) || empty($_POST['password'])){
					$errors[] = "You must provide email and password.";
				}
				
				// validate email
				if(!filter_var($email, FILTER_VALIDATE_EMAIL))
				{
					$errors[] = "You must enter a valid email address";
				}
								
				//check if email exists in the database
				$query = pg_query($conn, "SELECT * FROM users WHERE email = '$email'");
				
				$user = pg_fetch_assoc($query);
				
				$userCount = pg_num_rows($query);
				
				if($userCount < 1)
				{
					$errors[] .= "User doesn't exists"; 
				}
				
				if(!password_verify($password, $user['password'])){
					$errors[] = "The password does not match our records. Please try Again!";
				}
				
				if(!empty($errors))
				{
					echo display_errors($errors);
				}else{
					//log in user
					$user_id = $user['id'];
					login($user_id);
				}
			}
		?>
	</div>
	<h2 class="text-center">Login</h2><hr/>
	<form action="login.php" method="post" >
		<div class="form-group">
			<label for="email">Email*:</label>
			<input type="email" class="form-control" name="email" id="email" value="<?php echo $email;?>" required />
		</div>
		<div class="form-group">
			<label for="password">Password*:</label>
			<input type="password" class="form-control" name="password" id="password" value="<?php echo $password;?>" required />
		</div>
		<div class="form-group">
			<input type="submit" class="btn btn-primary" value="Login" />
		</div>
	</form>
	<p class="text-right"><a href="../index.php" alt="home">Visit Site</a></p>
</div>





<?php include 'includes/footer.php'; ?>