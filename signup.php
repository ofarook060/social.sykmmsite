<?php 

	include("classes/connect.php");
	include("classes/signup.php");

	$first_name = "";
	$last_name = "";
	$gender = "";
	$email = "";

	if($_SERVER['REQUEST_METHOD'] == 'POST')
	{


		$signup = new Signup();
		$result = $signup->evaluate($_POST);
		
		if($result != "")
		{

			echo "<div style='text-align:center;font-size:12px;color:white;background-color:grey;'>";
			echo "<br>The following errors occured:<br><br>";
			echo $result;
			echo "</div>";
		}else
		{

			header("Location:" . ROOT ."login");
			die;
		}
 

		$first_name = $_POST['first_name'];
		$last_name = $_POST['last_name'];
		$gender = $_POST['gender'];
		$email = $_POST['email'];

	}


	

?>

<html> 

	<head>
		
		<title>SYK Social | Signup</title>
		<link rel="stylesheet" href="styles.css">
	</head>

	<body class="auth-container">
		
		<div class="auth-header">

			<a href="<?=ROOT?>home" class="logo-link">
				<img src="<?=ROOT?>logo.png" alt="SYK Social" style="width:44px;height:44px;border-radius:50%;">
				<span style="color:#D4AF37;font-size:28px;font-weight:bold;">SYK Social</span>
			</a>

			<a href="login.php" style="margin-left:auto;">
				<div style="background-color:#D4AF37;color:#032F2E;width:70px;text-align:center;padding:8px;border-radius:8px;font-weight:bold;">Log in</div>
			</a>
		</div>

		<div class="auth-card">
			
		Sign up to SYK Social<br><br>

		<form method="post" action="">

			<input value="<?php echo $first_name ?>" name="first_name" type="text" placeholder="First name"><br><br>
			<input value="<?php echo $last_name ?>" name="last_name" type="text" placeholder="Last name"><br><br>

			<span style="font-weight: normal;">Gender:</span><br>
			<select name="gender">
				
				<option><?php echo $gender ?></option>
				<option>Male</option>
				<option>Female</option>

			</select>
			<br><br>
			<input value="<?php echo $email ?>" name="email" type="text" placeholder="Email"><br><br>
			
			<input name="password" type="password" placeholder="Password"><br><br>
			<input name="password2" type="password" placeholder="Retype Password"><br><br>

			<input type="submit" class="btn-submit" value="Sign up">
			<br><br><br>

		</form>

		</div>

	</body>


</html>