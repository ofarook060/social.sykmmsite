<?php 

session_start();

	include("classes/connect.php");
	include("classes/login.php");
 
	$email = "";
	$password = "";
	
	if($_SERVER['REQUEST_METHOD'] == 'POST')
	{


		$login = new Login();
		$result = $login->evaluate($_POST);
		
		if($result != "")
		{

			echo "<div style='text-align:center;font-size:12px;color:white;background-color:grey;'>";
			echo "<br>The following errors occured:<br><br>";
			echo $result;
			echo "</div>";
		}else
		{

			header("Location: ".ROOT."profile");
			die;
		}
 

		$email = $_POST['email'];
		$password = $_POST['password'];
		

	}


	

?>

<html> 

	<head>
		
		<title>SYK Social | Log in</title>
		<link rel="stylesheet" href="styles.css">
	</head>

	<body class="auth-container">
		
		<div class="auth-header">

			<a href="<?=ROOT?>home" class="logo-link">
				<img src="<?=ROOT?>logo.png" alt="SYK Social" style="width:44px;height:44px;border-radius:50%;">
				<span style="color:#D4AF37;font-size:28px;font-weight:bold;">SYK Social</span>
			</a>

			<a href="<?=ROOT?>signup" style="margin-left:auto;">
				<div style="background-color:#D4AF37;color:#032F2E;width:70px;text-align:center;padding:8px;border-radius:8px;font-weight:bold;">Signup</div>
			</a>
		</div>

		<div class="auth-card">
			
		<form method="post">
			<h2>Log in to SYK Social</h2>

			<input name="email" value="<?php echo $email ?>" type="text" placeholder="Email"><br><br>
			<input name="password" value="<?php echo $password ?>" type="password" placeholder="Password"><br><br>

			<input type="submit" class="btn-submit" value="Log in">
			<br><br><br>

		</form>
		</div>

	</body>


</html>