<?php
/**
 * Porto Social Login
 */

if ( is_user_logged_in() ) : ?>

	<h1 class="text-uppercase mb-0"><?php the_title(); ?></h1>

	<?php
else :

	$is_facebook_login = porto_nextend_facebook_login();
	$is_google_login   = porto_nextend_google_login();
	$is_twitter_login  = porto_nextend_twitter_login();
	?>

	<div class="porto-social-login-section">

		<?php
		if ( ! $is_facebook_login && ! $is_google_login && ! $is_twitter_login ) {
			echo '<h2 class="text-uppercase mb-0">' . get_the_title() . '</h2>';
		} else {
			?>
			<p><?php esc_html_e( 'Access your account through your social networks:', 'porto' ); ?></p>
		<?php } ?>
		<div class="social-login">

			<?php if ( $is_facebook_login ) { ?>
				<a href="<?php echo wp_login_url(); ?>?loginFacebook=1&redirect=<?php echo the_permalink(); ?>" class="button social-button large facebook" onclick="window.location.href = '<?php echo wp_login_url(); ?>?loginFacebook=1&redirect='+window.location.href; return false"><i class="icon-facebook"></i>
					<span><?php esc_html_e( 'Facebook', 'porto' ); ?></span></a>
			<?php } ?>

			<?php if ( $is_google_login ) { ?>

				<a class="button social-button large google-plus" href="<?php echo wp_login_url(); ?>?loginGoogle=1&redirect=<?php echo the_permalink(); ?>" onclick="window.location.href = '<?php echo wp_login_url(); ?>?loginGoogle=1&redirect='+window.location.href; return false">
					<i class="fab fa-google"></i>
					<span><?php esc_html_e( 'Google', 'porto' ); ?></span></a>
			<?php } ?>

			<?php if ( $is_twitter_login ) { ?>

				<a class="button social-button large twitter" href="<?php echo wp_login_url(); ?>?loginSocial=twitter&redirect=<?php echo the_permalink(); ?>" onclick="window.location.href = '<?php echo wp_login_url(); ?>?loginSocial=twitter&redirect='+window.location.href; return false">
					<i class="fab fa-twitter"></i>
					<span><?php esc_html_e( 'Twitter', 'porto' ); ?></span></a>
			<?php } ?>
		</div>

	</div>
<?php endif; ?>
