<?php 

	include("classes/autoload.php");

	$login = new Login();
	$_SESSION['mybook_userid'] = isset($_SESSION['mybook_userid']) ? $_SESSION['mybook_userid'] : 0;
	
	$user_data = $login->check_login($_SESSION['mybook_userid'],false);
 
 	$USER = $user_data;
 	
 	if(isset($URL[1]) && is_numeric($URL[1])){

	 	$profile = new Profile();
	 	$profile_data = $profile->get_profile($URL[1]);

	 	if(is_array($profile_data)){
	 		$user_data = $profile_data[0];
	 	}

 	}
 	

	$group_name = "";
 
	if($_SERVER['REQUEST_METHOD'] == 'POST')
	{


		$group = new Group();
		$result = $group->evaluate($_POST);
		
		if($result != "")
		{

			echo "<div style='text-align:center;font-size:12px;color:white;background-color:grey;'>";
			echo "<br>The following errors occured:<br><br>";
			echo $result;
			echo "</div>";
		}else
		{

			header("Location:" . ROOT ."profile/".$_SESSION['mybook_userid']. "/groups");
			die;
		}
 

		$group_name = $_POST['group_name'];
		

	}


	

?>

<html> 

	<head>
		
		<title>SYK Social | Create Group</title>
		<link rel="stylesheet" href="styles.css">
	</head>

	<body style="font-family: tahoma;background-color: #032F2E;">
		
		<?php include("header.php"); ?>

		<div class="auth-card">
			
			Create Group<br><br>

			<form method="post" action="">

				<input value="<?php echo $group_name ?>" name="group_name" type="text" placeholder="Group Name" autofocus required><br><br>
 
 				<select name="group_type">
 					<option>Public</option>
 					<option>Private</option>
 				</select><br>
  				<br>
				<input type="submit" class="btn-submit" value="Create">
				<br><br><br>

			</form>

		</div>

	</body>


</html>