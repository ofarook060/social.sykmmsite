<?php 

	include("classes/autoload.php");
	$image_class = new Image();

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
	
	$Post = new Post();
	$msg_class = new Messages();


	if(isset($_SERVER['HTTP_REFERER']) && !strstr($_SERVER['HTTP_REFERER'], "/delete/")){

		$_SESSION['return_to'] = $_SERVER['HTTP_REFERER'];
	}

	$ERROR = "";
	if(isset($URL[1])){

		if($URL[1] == "msg")
		{
			$MESSAGE = $msg_class->read_one($URL[2]);

			 if(!$MESSAGE){

			 	$ERROR = "Accesss denied! you cant delete this message!";
			 }
		}else
		if($URL[1] == "thread")
		{
			$MESSAGE = false;

			if(isset($URL[2])){
				$MESSAGE = $msg_class->read_one_thread($URL[2]);
			}
			if(!$MESSAGE){

			 	$ERROR = "Accesss denied! you cant delete this thread!";
			}
		
		}else{

	 		 $ROW = $Post->get_one_post($URL[1]);

			 if(!$ROW){

			 	$ERROR = "No such post was found!";
			 }else{

			 	if(!i_own_content($ROW)){

			 		$ERROR = "Accesss denied! you cant delete this file!";
			 	}
			 }
		 }

	}else{

		$ERROR = "No such post was found!";
	}


	//if something was posted
	if($ERROR == "" && $_SERVER['REQUEST_METHOD'] == "POST"){

		if($URL[1] == "msg")
		{
			$msg_class->delete_one($_POST['id']);

		}else
		if($URL[1] == "thread")
		{
			$msg_class->delete_one_thread($_POST['id']);
 		
		}else{

			$Post->delete_post($_POST['postid']);
			
		}

		header("Location: ".$_SESSION['return_to']);
		die;		

	}

?>

<!DOCTYPE html>
	<html>
	<head>
		<title>SYK Social | Delete</title>
		<link rel="stylesheet" href="styles.css">
	</head>

	<body style="font-family: tahoma;background-color: #032F2E;">

		<br>
		<?php include("header.php"); ?>

		<!--cover area-->
		<div style="width: 800px;margin:auto;min-height: 400px;">
		 
			<!--below cover area-->
			<div style="display: flex;">	

				<!--posts area-->
 				<div style="min-height: 400px;flex:2.5;padding: 20px;padding-right: 0px;">
 					
 					<div style="border:solid thin #aaa; padding: 10px;background-color: white;">

  						<form method="post">
 							
  								<?php

 									if($ERROR != ""){

								 		echo $ERROR;
								 	}else{

								 		if(isset($URL[1]) && $URL[1] == "msg")
										{

		  									echo "Are you sure you want to delete this message??<br><br>";

											$user = new User();
		 									$ROW_USER = $user->get_user($MESSAGE['sender']);
		 									
		  									include("message_left.php");

		  									echo "<input type='hidden' name='id' value='$MESSAGE[id]'>";
		 									echo "<input id='post_button' type='submit' value='Delete'>";
		 								}else
	 									if(isset($URL[1]) && $URL[1] == "thread")
										{

		  									echo "Are you sure you want to delete this thread??<br><br>";

											$user = new User();
		 									$ROW_USER = $user->get_user($MESSAGE['sender']);
		 									
		  									include("message_left.php");

		  									echo "<input type='hidden' name='id' value='$MESSAGE[msgid]'>";
		 									echo "<input id='post_button' type='submit' value='Delete'>";
	 									
										}else
										{

		  									echo "Are you sure you want to delete this post??<br><br>";

											$user = new User();
		 									$ROW_USER = $user->get_user($ROW['userid']);
		 									
		  									include("post_delete.php");

		  									echo "<input type='hidden' name='postid' value='$ROW[postid]'>";
		 									echo "<input id='post_button' type='submit' value='Delete'>";
	 									
										}
 									}
 								?>
  							
	 						
	 						<br style="clear: both;">
 						</form>
 					</div>
  

 				</div>
			</div>

		</div>

	</body>
</html>