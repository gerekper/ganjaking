<?php
/**
 * Eventon Subscription Page Template
 *
 * this template can be copied to 
 * .../wp-content/themes/<--yourtheme-->/eventon/subscriber/
 * folder and edit the layout template
 *
 * @version 0.2
 */
?>
<!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width">
	<title><?php wp_title( '|', true, 'right' ); ?></title>
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<!--[if lt IE 9]>
	<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js"></script>
	<![endif]-->
	<?php wp_head(); ?>
	<style type="text/css">
	.evosb_content{
		text-align: center;
		width: 300px;
		margin: 50px auto
	}
	</style>
</head>
<body <?php body_class(); ?>>
<div class='evosb_content'>
	<h2><?php evo_lang_e('Subscriber Manager');?></h2>

	<?php 

	global $eventon_sb;

	$notice = false;
	$email = false;
	$status =  evo_lang('Not Subscribed');
	$content = false;
	$signinform = false;
	$options = false;
	$subscriber_id = '';
	$user_subscribed = false;

	// check if email in our system
		if(isset($_REQUEST['email'])){
			$subscriber_id = EVOSB()->frontend->email_exist($_REQUEST['email']);

			if(!$subscriber_id){
				$signinform = true;
			}else{
				if(EVOSB()->frontend->is_currently_subscribed($subscriber_id)){
					$status = evo_lang('Subscribed');
					$user_subscribed = true;
				}else{
					$status = evo_lang('Not Subscribed');
				}
			}
		}else{	$signinform = true;	}


// action
	if(isset($_REQUEST['action'])){

		switch($_REQUEST['action']){
			case 'verify':
				// when first subscribe verify subscription
				$notice = (!$notice)? $eventon_sb->frontend->verify_subscription($_REQUEST['email'], $_REQUEST['key']): $notice;
			break;
			case 'unsubscribe_conf':
				$_link = $eventon_sb->frontend->subscriber_url(array('action'=>'unsubscribe', 'email'=>urlencode($_REQUEST['email'])) );
				ob_start();
				?>
					<h4><?php evo_lang_e('Unsubscribe from our calendar');?></h4>
					<p><a href='<?php echo $_link;?>' class='nylon_btn button'><?php evo_lang_e('Confirm Unsubscription');?></a></p>
				<?php
				$content = ob_get_clean();
			break;
			case 'unsubscribe':
				$email_address = isset($_REQUEST['email']) ? $_REQUEST['email']: false;

				if( !$email_address){
					$notice = evo_lang('Please type in your email address to unsubscribe!');
				}else{
					$notice = (!$notice)? $eventon_sb->frontend->unsubscribe(array('email'=>urlencode($_REQUEST['email'])) ): $notice;
					$status = ($eventon_sb->frontend->is_currently_subscribed($subscriber_id))? 'Subscribed':'Not Subscribed';
				}
				

			break;
			case 'subscribeback':
				$notice = (!$notice)? $eventon_sb->frontend->do_subscribe_back(array('email'=>urlencode($_REQUEST['email'])) ): $notice;
				$status = ($eventon_sb->frontend->is_currently_subscribed($subscriber_id))? 'Subscribed':'Not Subscribed';
			break;
			case 'signin':
				if(!$_REQUEST['email'] ) $notice = evo_lang('Incorrect Email Address!');
				if(isset($_REQUEST['email']) && !$subscriber_id){
					$notice = evo_lang('Incorrect Email Address!');
				} 
			break;
		}
	}

// DISPLAY
	if($signinform){
		if($notice) echo '<div class="notice">'.$notice.'</div>';
		?>
			<form action='' method='post'>
				<p><label for=""><?php evo_lang_e('Type your email address');?></label>
				<input type="text" name='email' style='width:100%' value='<?php echo isset($_REQUEST['email'])?$_REQUEST['email']:'';?>'/></p>
				<input type="hidden" name='action' value='signin'>
				<p><button class='evosb_btn button'><?php evo_lang_e('Access Subscription System');?></button></p>
			</form>
		<?php
	}else{
		
		if($content){ echo $content;}else{

			if($notice) echo '<div class="notice">'.$notice.'</div>';
		?>
			<h4><?php evo_lang_e('Your subscription information');?></h4>
			
			<div class='evosb_subscription_info_section'>
				<p>
					<span><?php echo $_REQUEST['email'];?><em><?php evo_lang_e('Email Address');?></em></span>
					<span><?php echo $status;?><em><?php evo_lang_e('Subscription Status');?></em></span>
				</p>
			</div>
	
			<?php if($user_subscribed){
				$_link = EVOSB()->frontend->subscriber_url(array('action'=>'unsubscribe', 'email'=>urlencode($_REQUEST['email'])) );

				EVOSB()->frontend->manage_subscribe_categories();

			}else{ // not subscribed
				
				$email = EVOSB()->frontend->email_exist($_REQUEST['email']);
				if($email != false):
					$_link = EVOSB()->frontend->subscriber_url(array('action'=>'subscribeback', 'email'=>urlencode($_REQUEST['email'])) );
			?>
				<p><a href='<?php echo $_link;?>' class='evosb_btn button'><?php evo_lang_e('Subscribe back to our calendar');?></a></p>
			<?php 
				endif;

			}// endif?>
		<?php
		}		
	}

	echo "<p>".evo_lang('Event Subscription System')."</p>";
?>
</div>
<?php wp_footer(); ?>
</body>
</html>