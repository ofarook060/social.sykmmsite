<?php 

	include("classes/autoload.php");
 
	$login = new Login();
	$user_data = $login->check_login($_SESSION['mybook_userid']);
 
 	$USER = $user_data;
 	
 	if(isset($URL[1]) && is_numeric($URL[1])){

	 	$profile = new Profile();
	 	$profile_data = $profile->get_profile($URL[1]);

	 	if(is_array($profile_data)){
	 		$user_data = $profile_data[0];
	 	}

 	}
 	
	
	$Message = new Messages();
	$Post = new Post();
	$ROW = false;

	$ERROR = "";
	if(isset($URL[1]) && $URL[1] == "msg"){

		$ROW = $Message->read_one($URL[2]);
		if(is_array($ROW)){
			$ROW['image'] = $ROW['file'];
		}
	}else
	if(isset($URL[1])){

		$ROW = $Post->get_one_post($URL[1]);
	
	}else{

		$ERROR = "No image was found!";
	}
 
?>

<!DOCTYPE html>
	<html>
	<head>
		<title>SYK Social | Image</title>
		<link rel="stylesheet" href="styles.css">
	</head>

	<body style="font-family: tahoma; background-color: #032F2E;">

		<br>
		<?php include("header.php"); ?>

		<!--cover area-->
		<div style="width: 800px;margin:auto;min-height: 400px;">
		 
			<!--below cover area-->
			<div style="display: flex;">	

				<!--posts area-->
 				<div style="min-height: 400px;flex:2.5;padding: 20px;padding-right: 0px;">
 					
 					<div style="border:solid thin #F4D66D; padding: 10px;background-color: white;">

  					 <?php 

  					 		$user = new User();
  					 		$image_class = new Image();

  					 		if(is_array($ROW)){

								echo "<img src='".ROOT."$ROW[image]' style='width:100%;' />";  					 			
  					 		}

  					 ?>

  					 <br style="clear: both;">
 					</div>
  

 				</div>
			</div>

		</div>

	</body>
</html>