<?php 

	include("classes/autoload.php");
	$image_class = new Image();

	$ERROR = "";

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
	 
	$msg_class = new Messages();

  	//new message//check if thread already exists
  	if(isset($URL[1]) && $URL[1] == "new"){

  		$old_thread = $msg_class->read($URL[2]);
  		if(is_array($old_thread)){

  			//redirect the user
  			header("Location: ".ROOT."messages/read/". $URL[2]);
			die;
  		}
  	}

	//if a message was posted
	if($ERROR == "" && $_SERVER['REQUEST_METHOD'] == "POST"){
 
		$user_class = new User();
		if(is_array($user_class->get_user($URL[2]))){

			$ERROR = $msg_class->send($_POST,$_FILES,$URL[2]);

			header("Location: ".ROOT."messages/read/". $URL[2]);
			die;
		}else{
			$ERROR = "The requested user could not be found!";
		}

		
	}

?>

<!DOCTYPE html>
	<html>
	<head>
		<title>SYK Social | Messages</title>
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

  						<form method="post" enctype="multipart/form-data">
 							
  								<?php

 									if($ERROR != ""){

								 		echo $ERROR;
								 	}else{

								 		if(isset($URL[1]) && $URL[1] == "read"){

 								 			echo "Chatting with:<br><br>";
	  										if(isset($URL[2]) && is_numeric($URL[2])){
								 			
 								 				$data = $msg_class->read($URL[2]);
	  											
	  											$user = new User();
		 										$FRIEND_ROW = $user->get_user($URL[2]);

		 										include "user.php";

		 										echo "<a href='".ROOT."messages'>";
		 										echo '<input id="post_button" type="button" style="width:auto;cursor:pointer;margin:4px;" value="All Messages">';
		 										echo "</a>";

		 										if(is_array($data)){
			 										echo "<a href='".ROOT."delete/thread/". $data[0]['msgid'] ."'>";
		 									echo '<input id="post_button" type="button" style="width:auto;cursor:pointer;background-color:#A87C17;margin:4px;" value="Delete Thread">';
			 										echo "</a>";
			 									}


		 										echo '
 		 										<div>';
 		 											$user = new User();

 		 											if(is_array($data)){
	 		 											foreach ($data as $MESSAGE) {
	 		 												# code...
	  		 												//show($MESSAGE);
			 												$ROW_USER = $user->get_user($MESSAGE['sender']);

			 												if(i_own_content($MESSAGE)){
	 		 													include "message_right.php";
	 		 												}else{

	  		 													include "message_left.php";
			 												}
	 		 											}
 		 											}

		 										echo '
		 										</div>';

		 										echo '
		 										<div style="border:solid thin #aaa; padding: 10px;background-color: white;">

 								 						<textarea name="message" placeholder="Write your message here"></textarea>
								 						<input type="file" name="file" >
								 						<input id="post_button" type="submit" value="Send">
								 						<br>
 							 						
							 					</div>

							 					';

	  										}else{

	  											echo "That user could not be found<br><br>";
	  										}

								 		}else
								 		if(isset($URL[1]) && $URL[1] == "new"){

	  										echo "Start New Message with:<br><br>";
	  										if(isset($URL[2]) && is_numeric($URL[2])){
	  											
	  											$user = new User();
		 										$FRIEND_ROW = $user->get_user($URL[2]);

		 										include "user.php";

		 										echo '
		 										<div style="border:solid thin #aaa; padding: 10px;background-color: white;">

 								 						<textarea name="message" placeholder="Write your message here"></textarea>
								 						<input type="file" name="file" >
 								 						<input id="post_button" type="submit" value="Send">
								 						<br>
 							 						
							 					</div>

							 					';

	  										}else{

	  											echo "That user could not be found<br><br>";
	  										}
	  										

								 		}else{

	  										echo "Messages<br><br>";
		  									$data = $msg_class->read_threads();
		  									$user = new User();
		  									$me = esc($_SESSION['mybook_userid']);

		  									if(is_array($data)){
			  									foreach ($data as $MESSAGE) {
			  										# code...
			  										$myid = ($MESSAGE['sender'] == $me) ? $MESSAGE['receiver'] : $MESSAGE['sender'];

			 										$ROW_USER = $user->get_user($myid);

			  										include("thread.php");
			  									}
		  									}else{
		  										echo "You have no messages!";
		  									}

		  									echo "<br style='clear:both;'>";
								 		}

										
 									}
 								?>
  							
	 						
	 						<br>
 						</form>
 					</div>
  

 				</div>
			</div>

		</div>

	</body>
</html>