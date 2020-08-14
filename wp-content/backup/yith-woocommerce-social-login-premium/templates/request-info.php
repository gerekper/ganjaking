<?php
/**
 * Request additional info to login with provider
 *
 * @package YITH WooCommerce Social Login
 * @since   1.0.0
 * @author  YITH
 */

//login_header(__('Login', 'yith-woocommerce-social-login') );

?>
<!DOCTYPE html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title><?php echo get_bloginfo('name'); ?></title>
		<style type="text/css">
			html, body {
				height: 100%;
				margin: 0;
				padding: 0;
			}
			body {
				background: none repeat scroll 0 0 #f1f1f1;
				font-size: 14px;
				color: #444;
				font-family: "Open Sans",sans-serif;
			}
			hr {
				border-color: #eeeeee;
				border-style: none none solid;
				border-width: 0 0 1px;
				margin: 2px 0 0;
			}
			h4 {
				font-size: 14px;
				margin-bottom: 10px;
			}
			#login {
				width: 616px;
				margin: auto;
				padding: 114px 0 0;
			}
			#login-panel {
				background: none repeat scroll 0 0 #fff;
				box-shadow: 0 1px 3px rgba(0, 0, 0, 0.13);
				margin: 2em auto;
				box-sizing: border-box;
				display: inline-block;
				padding: 70px;
				position: relative;
				text-align: left;
				width: 100%;
			}
			#avatar {
				margin-left: -76px;
				top: -80px;
				left: 50%;
				padding: 4px;
				position: absolute;
			}
			#avatar img {
				background: none repeat scroll 0 0 #fff;
				border: 3px solid #f1f1f1;
				border-radius: 75px !important;
				box-shadow: 0 1px 3px rgba(0, 0, 0, 0.13);
				height: 145px;
				width: 145px;
			}
			#welcome {
				height: 55px;
				margin: 15px 0px 15px;
			}
			#idp-icon {
				position: absolute;
				margin-top: 2px;
				margin-left: -19px;
			}
			#login-form{
				margin: 0;
				padding: 0;
                text-align: left;
			}
			.button-primary {
				background-color: #21759b;
				background-image: linear-gradient(to bottom, #2a95c5, #21759b);
				border-color: #21759b #21759b #1e6a8d;
				border-radius: 3px;
				border-style: solid;
				border-width: 1px;
				box-shadow: 0 1px 0 rgba(120, 200, 230, 0.5) inset;
				box-sizing: border-box;
				color: #fff;
				cursor: pointer;
				display: inline-block;
				float: none;
				font-size: 12px;
				height: 36px;
				line-height: 23px;
				margin: 0;
				padding: 0 10px 1px;
				text-decoration: none;
				text-shadow: 0 1px 0 rgba(0, 0, 0, 0.1);
				white-space: nowrap;
			}
			.button-primary.focus, .button-primary:hover{
				background:#1e8cbe;
				border-color:#0074a2;
				-webkit-box-shadow:inset 0 1px 0 rgba(120,200,230,.6);
				box-shadow:inset 0 1px 0 rgba(120,200,230,.6);
				color:#fff
			}
			input[type="text"]{
				border: 1px solid #e5e5e5;
				box-shadow: 1px 1px 2px rgba(200, 200, 200, 0.2) inset;
				color: #555;
				font-size: 17px;
				height: 30px;
				line-height: 1;
				margin-bottom: 16px;
				margin-right: 6px;
				margin-top: 2px;
				outline: 0 none;
				padding: 3px;
                width: 100%;
			}
			input[type="text"]:focus{
				border-color:#5b9dd9;
				-webkit-box-shadow:0 0 2px rgba(30,140,190,.8);
				box-shadow:0 0 2px rgba(30,140,190,.8)
			}
			input[type="submit"]{
				float:right;
			}
			label{
				color:#777;
				font-size:14px;
				cursor:pointer;
				vertical-align:middle;
				text-align: left;
			}
			table {
				width:355px;
				margin-left:auto;
				margin-right:auto;
			}
			#mapping-options {
				width:555px;
			}
			#mapping-authenticate {
				display:none;
			}
			#mapping-complete-info {
				display:none;
			}
			.error {
				display:none;
				background-color: #fff;
				border-left: 4px solid #dd3d36;
				box-shadow: 0 1px 1px 0 rgba(0, 0, 0, 0.1);
				margin: 0 21px;
				padding: 12px;
				text-align:left;
			}
			.back-to-options {
				float: left;
				margin: 7px 0px;
			}
			.back-to-home {
				font-size: 12px;
				margin-top: -18px;
			}
			.back-to-home a {
				color: #999;
				text-decoration: none;
			}

		</style>

	</head>
	<body>
		<div id="login">
			<div id="login-panel">

				<div id="welcome">
                    <p>
                        <?php _e( "Please, fill in the form below with your information to continue", 'yith-woocommerce-social-login' ); ?>.
                    </p>
                    <?php if( !empty($errors) ):
                        foreach( $errors as $error ){
                            echo "<p>{$error}</p>";
                        }
                    endif; ?>
				</div>

         <form name="loginform" id="loginform" action="#" method="post">
         <?php if ( $show_user ): ?>
            <p>
                <label for="yith_user_login"><?php _e( 'Username', 'yith-woocommerce-social-login' ) ?><br>
                    <input type="text" name="yith_user_login" id="yith_user_login" class="input" value="" size="40"></label>
            </p>
        <?php endif ?>
        <?php if ( $show_email ): ?>
            <p>
                <label for="yith_user_email"><?php _e( 'Email', 'yith-woocommerce-social-login' ) ?><br>
                    <input type="text" name="yith_user_email" id="yith_user_email" class="input" value="" size="40"></label>
            </p>
        <?php endif ?>

                        <p class="submit">
                            <input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large" value="<?php _e( 'Continue to Login', 'yith-woocommerce-social-login' ) ?>">
                        </p>
                    <input type="hidden" name="ywsl_social" value="<?php echo $provider ?>">
                    <input type="hidden" name="redirect" value="<?php echo $redirect ?>">
                </form>


			<p class="back-to-home">
				<a href="<?php echo site_url(); ?>">&#8592; <?php printf( __( "Back to %s", 'yith-woocommerce-social-login' ), get_bloginfo('name') ); ?></a>
			</p>
		</div>

	</body>
</html>