<!--top bar-->
<?php 

	$corner_image = "images/user_male.jpg";
	if(isset($USER)){
		
		if(file_exists($USER['profile_image']))
		{
			$image_class = new Image();
			$corner_image = $image_class->get_thumb_profile($USER['profile_image']);
		}else{

			if($USER['gender'] == "Female"){

				$corner_image = "images/user_female.jpg";
			}
		}
	}
?>

<div id="blue_bar">
	<form method="get" action="<?=ROOT?>search">
		<div style="width: 800px;margin:auto;display:flex;align-items:center;height:100%;">
			
			<a href="<?=ROOT?>home" class="nav-brand" style="display:flex;align-items:center;gap:10px;text-decoration:none;">
				<img src="<?=ROOT?>logo.png" alt="SYK Social" style="width:36px;height:36px;border-radius:50%;">
				<span style="color:#D4AF37;font-size:22px;font-weight:bold;">SYK Social</span>
			</a>

			<input type="text" id="search_box" name="find" placeholder="Search for people" style="margin-left:20px;" />

			<div id="nav_right" style="margin-left:auto;display:flex;align-items:center;gap:12px;">
				<?php if(isset($USER)): ?>
					<a href="<?=ROOT?>profile" style="display:flex;align-items:center;">
						<img src="<?php echo ROOT . $corner_image ?>" style="width:32px;height:32px;border-radius:50%;border:2px solid #D4AF37;">
					</a>

					<a href="<?=ROOT?>notifications" style="position:relative;display:flex;align-items:center;">
						<svg fill="#F4D66D" width="24" height="24" viewBox="0 0 24 24"><path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.63-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.64 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2zm-2 1H8v-6c0-2.48 1.51-4.5 4-4.5s4 2.02 4 4.5v6z"/></svg>
						<?php $notif = check_notifications(); ?>
						<?php if($notif > 0): ?>
							<span class="nav-badge"><?= $notif ?></span>
						<?php endif; ?>
					</a>

					<a href="<?=ROOT?>messages" style="position:relative;display:flex;align-items:center;">
						<svg fill="#F4D66D" width="24" height="24" viewBox="0 0 24 24"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H5.17L4 17.17V4h16v12z"/></svg>
						<?php $notif = check_messages(); ?>
						<?php if($notif > 0): ?>
							<span class="nav-badge"><?= $notif ?></span>
						<?php endif; ?>
					</a>

					<a href="<?=ROOT?>logout" style="color:#F4D66D;font-size:13px;">Logout</a>

				<?php else: ?>
					<a href="<?=ROOT?>login" style="color:#F4D66D;font-size:13px;">Login</a>
				<?php endif; ?>
			</div>

		</div>
	</form>
</div>
