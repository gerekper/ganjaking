<?php
/**
 * Login Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-login.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 4.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$lightbox = wp_doing_ajax();
global $is_facebook_login;
global $is_google_login;
global $is_twitter_login;
$is_facebook_login = porto_nextend_facebook_login();
$is_google_login   = porto_nextend_google_login();
$is_twitter_login  = porto_nextend_twitter_login();

add_action( 'porto_social_login', 'print_porto_social_login' );
/**
 * Echo Social login
 *
 * @return void
 */
function print_porto_social_login() {
	global $is_facebook_login, $is_google_login, $is_twitter_login;
	?>
	<div class="porto-social-login-section false-modal bg-transparent p-0">
	<?php if ( $is_facebook_login ) { ?>
		<a href="<?php echo wp_login_url(); ?>?loginFacebook=1&redirect=<?php echo the_permalink(); ?>" class="button social-button text-decoration-none large text-md font-weight-semibold facebook w-100" onclick="window.location.href = '<?php echo wp_login_url(); ?>?loginFacebook=1&redirect='+window.location.href; return false"><i class="fab fa-facebook-f"></i>
			<span><?php esc_html_e( 'Login With Facebook', 'porto' ); ?></span></a>
	<?php } ?>

	<?php if ( $is_google_login ) { ?>

		<a class="button social-button text-decoration-none large google-plus font-weight-semibold text-md w-100" href="<?php echo wp_login_url(); ?>?loginGoogle=1&redirect=<?php echo the_permalink(); ?>" onclick="window.location.href = '<?php echo wp_login_url(); ?>?loginGoogle=1&redirect='+window.location.href; return false">
			<i class="fab fa-google"></i>
			<span><?php esc_html_e( 'Login With Google', 'porto' ); ?></span></a>
	<?php } ?>

	<?php if ( $is_twitter_login ) { ?>
		<a class="button social-button text-decoration-none large twitter font-weight-semibold text-md w-100" href="<?php echo wp_login_url(); ?>?loginSocial=twitter&redirect=<?php echo the_permalink(); ?>" onclick="window.location.href = '<?php echo wp_login_url(); ?>?loginSocial=twitter&redirect='+window.location.href; return false">
			<i class="fab fa-twitter"></i>
			<span><?php esc_html_e( 'Login With Twitter', 'porto' ); ?></span></a>
	<?php } ?>
	</div>
	<?php
}
?>
<div class="<?php echo ( true == $lightbox ? '' : ( 'no' === get_option( 'woocommerce_enable_myaccount_registration' ) ? esc_html( 'col-md-6 mx-auto mb-4' ) : esc_html( 'col-lg-10 mx-auto mb-4' ) ) ); ?>">
	<?php wc_print_notices(); ?>
	<div class="align-left <?php echo ( true == $lightbox ? esc_html( 'featured-box' ) : '' ); ?>">
		<div class="box-content">
			<?php do_action( 'woocommerce_before_customer_login_form' ); ?>

			<?php if ( false === $lightbox && 'yes' === get_option( 'woocommerce_enable_myaccount_registration' ) ) : ?>

			<div class="u-columns col2-set" id="customer_login">

				<div class="u-column1 col-1">

			<?php endif; ?>
					<form class="woocommerce-form woocommerce-form-login login <?php echo false === $lightbox && 'yes' === get_option( 'woocommerce_enable_myaccount_registration' ) ? 'pr-lg-4 pr-0' : ''; ?>" method="post">
						<h3 class="account-sub-title mb-2 font-weight-bold text-capitalize text-v-dark"><?php esc_html_e( 'Login', 'woocommerce' ); ?></h3>
						<?php do_action( 'woocommerce_login_form_start' ); ?>
						<?php if ( true == $lightbox ) : ?>
							<?php if ( $is_facebook_login || $is_google_login || $is_twitter_login ) : ?>
								<?php do_action( 'porto_social_login' ); ?>
								<div class="heading heading-border heading-middle-border heading-middle-border-center m-b-md">
									<h6 class="heading-tag font-weight-semibold text-md login-more">or</h6>
								</div>
							<?php endif; ?>
						<?php endif; ?>
						<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
							<label class="mb-1 font-weight-medium" for="username"><?php esc_html_e( 'Username or email address', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
							<input type="text" class="woocommerce-Input woocommerce-Input--text input-text line-height-xl" name="username" id="username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
						</p>
						<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide mb-2">
							<label class="mb-1 font-weight-medium" for="password"><?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
							<input class="woocommerce-Input woocommerce-Input--text input-text line-height-xl" type="password" name="password" id="password" autocomplete="current-password" />
						</p>

						<?php do_action( 'woocommerce_login_form' ); ?>

						<p class="status" style="display: none;"></p>

						<div class="woocommerce-LostPassword lost_password d-flex flex-column flex-sm-row justify-content-between mb-4">
							<div class="porto-checkbox my-2 my-sm-0">
								<input type="checkbox" name="rememberme" id="rememberme" value="forever" class="porto-control-input woocommerce-form__input woocommerce-form__input-checkbox">
								<label class="porto-control-label no-radius" for="rememberme"><?php esc_html_e( 'Remember me', 'woocommerce' ); ?></label>
							</div>
							<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" class="text-v-dark font-weight-semibold"><?php esc_html_e( 'Forgot Password?', 'porto' ); ?></a>
						</div>
						<p class="form-row mb-3 mb-lg-0 pb-1 pb-lg-0">
							<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
							<button type="submit" class="woocommerce-Button button login-btn btn-v-dark py-3 text-md w-100" name="login" value="<?php esc_attr_e( 'Login', 'woocommerce' ); ?>"><?php esc_html_e( 'Login', 'woocommerce' ); ?></button>
						</p>
						<?php if ( false == $lightbox ) : ?>
							<?php if ( $is_facebook_login || $is_google_login || $is_twitter_login ) : ?>
								<div class="heading heading-border heading-middle-border heading-middle-border-center pt-lg-1 mt-lg-3 m-b-md">
									<h6 class="heading-tag font-weight-semibold text-md login-more">or</h6>
								</div>
								<?php do_action( 'porto_social_login' ); ?>
							<?php endif; ?>
					<?php elseif ( true == $lightbox && 'yes' === get_option( 'woocommerce_enable_myaccount_registration' ) ) : ?>
							<p class="woocommerce-form-row form-row mb-0">
								<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
								<input type="email" class="d-none" name="email" id="email" />
								<button type="submit" class="woocommerce-Button button register-btn bg-transparent border-0 text-decoration-none text-md py-3 font-weight-bold w-100" name="register" value="<?php esc_attr_e( 'Register', 'woocommerce' ); ?>"><?php esc_html_e( 'Register Now!', 'woocommerce' ); ?></button>
							</p>
						<?php endif; ?>
						<?php do_action( 'woocommerce_login_form_end' ); ?>
					</form>
			<?php if ( false === $lightbox && 'yes' === get_option( 'woocommerce_enable_myaccount_registration' ) ) : ?>

				</div>
				<div class="u-column2 col-2">
					<form method="post" class="woocommerce-form woocommerce-form-register register pl-lg-4 pr-0" <?php do_action( 'woocommerce_register_form_tag' ); ?> >
						<h3 class="account-sub-title mb-2 font-weight-bold"><?php esc_html_e( 'Register', 'woocommerce' ); ?></h3>
						<?php do_action( 'woocommerce_register_form_start' ); ?>

						<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>

							<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
								<label class="font-weight-medium mb-1" for="reg_username"><?php esc_html_e( 'Username', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
								<input type="text" class="woocommerce-Input woocommerce-Input--text line-height-xl input-text" name="username" id="reg_username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
							</p>

						<?php endif; ?>

						<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
							<label class="font-weight-medium mb-1" for="reg_email"><?php esc_html_e( 'Email address', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
							<input type="email" class="woocommerce-Input woocommerce-Input--text line-height-xl input-text" name="email" id="reg_email" autocomplete="email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
						</p>

						<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>

							<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
								<label class="font-weight-medium mb-1" for="reg_password"><?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
								<input type="password" class="woocommerce-Input woocommerce-Input--text line-height-xl input-text" name="password" id="reg_password" autocomplete="new-password" />
							</p>

						<?php else : ?>

							<p><?php esc_html_e( 'A password will be sent to your email address.', 'woocommerce' ); ?></p>

						<?php endif; ?>

						<?php do_action( 'woocommerce_register_form' ); ?>

						<p class="status" style="display: none;"></p>

						<p class="woocommerce-form-row form-row mb-0">
							<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
							<button type="submit" class="woocommerce-Button button register-btn btn-v-dark text-md py-3 w-100" name="register" value="<?php esc_attr_e( 'Register', 'woocommerce' ); ?>"><?php esc_html_e( 'Register', 'woocommerce' ); ?></button>
						</p>

						<?php do_action( 'woocommerce_register_form_end' ); ?>

					</form>

				</div>
			</div>
			<?php endif; ?>

			<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
		</div>
	</div>
</div>
