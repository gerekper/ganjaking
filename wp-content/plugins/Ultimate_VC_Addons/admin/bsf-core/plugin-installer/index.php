<?php
/**
 * Admin functions for bsf core.
 *
 * @package BSF core
 */

$plugin_action = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

if ( 'install' === $plugin_action ) {
	$request_product_id = ( isset( $_GET['id'] ) ) ? intval( $_GET['id'] ) : '';// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( '' !== $request_product_id ) {
		?>
				<div class="clear"></div>
				<div class="wrap">
				<h2><?php esc_html_e( 'Installing Extension', 'bsf' ); ?></h2>
			<?php
				$installed = install_bsf_product( $request_product_id );
			?>
			<?php if ( isset( $installed['status'] ) && true === $installed['status'] ) : ?>
					<?php $current_name = strtolower( bsf_get_current_name( $installed['init'], $installed['type'] ) ); ?>
					<?php
					$current_name      = preg_replace( '![^a-z0-9]+!i', '-', $current_name );
					$manage_plugin_url = is_multisite() ? network_admin_url( 'plugins.php#' . $current_name ) : admin_url( 'plugins.php#' . $current_name );
					?>
					<a href="<?php echo esc_url( $manage_plugin_url ); ?>"><?php esc_html_e( 'Manage plugin here', 'bsf' ); ?></a>
				<?php endif; ?>
				</div>
			<?php
			require_once ABSPATH . 'wp-admin/admin-footer.php';
			exit;
	}
}

global $bsf_theme_template;

if ( is_multisite() ) {
	$template = $bsf_theme_template;
} else {
	$template = get_template();
}

$current_page = '';

if ( isset( $_GET['page'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$current_page = esc_attr( $_GET['page'] );  // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	$arr        = explode( 'bsf-extensions-', $current_page );
	$product_id = $arr[1];
}

$redirect_url = network_admin_url( 'admin.php?page=' . $current_page );

$extensions_installer_heading = apply_filters( "bsf_extinstaller_heading_{$product_id}", 'iMedica Extensions' );

$extensions_installer_subheading = apply_filters( "bsf_extinstaller_subheading_{$product_id}", 'iMedica is already very flexible & feature rich theme. It further aims to be all-in-one solution for your WordPress needs. Install any necessary extensions you like from below and take it on the steroids.' );

$product_status    = check_bsf_product_status( $product_id );
$reset_bundled_url = bsf_exension_installer_url( $product_id . '&remove-bundled-products&redirect=' . $redirect_url );

?>
<div class="clear"></div>
<div class="wrap about-wrap bsf-sp-screen bend <?php echo 'extension-installer-' . esc_attr( $product_id ); ?>">

	<div class="bend-heading-section extension-about-header">

		<h1><?php echo esc_html( $extensions_installer_heading ); ?></h1>
		<h3><?php echo esc_html( $extensions_installer_subheading ); ?></h3>

		<div class="bend-head-logo">
			<div class="bend-product-ver"><?php esc_html_e( 'Extensions ', 'bsf' ); ?></div>
		</div>
	</div>  <!--heading section-->

	<div class="bend-content-wrap">
	<hr class="bsf-extensions-lists-separator">
	<h3 class="bf-ext-sub-title"><?php esc_html_e( 'Available Extensions', 'bsf' ); ?></h3>

	<?php

	$nonce                       = wp_create_nonce( 'bsf_install_extension_nonce' );
	$brainstrom_bundled_products = ( get_option( 'brainstrom_bundled_products' ) ) ? (array) get_option( 'brainstrom_bundled_products' ) : array();

	if ( isset( $brainstrom_bundled_products[ $product_id ] ) ) {
		$brainstrom_bundled_products = $brainstrom_bundled_products[ $product_id ];
	}

	usort( $brainstrom_bundled_products, 'bsf_sort' );

	if ( ! empty( $brainstrom_bundled_products ) ) :
		$global_plugin_installed = 0;
		$global_plugin_activated = 0;
		$total_bundled_plugins   = count( $brainstrom_bundled_products );
		foreach ( $brainstrom_bundled_products as $key => $product ) {
			if ( ! isset( $product->id ) || empty( $product->id ) ) {
				continue;
			}
			if ( isset( $request_product_id ) && $request_product_id !== $product->id ) {
				continue;
			}
			$plugin_abs_path = WP_PLUGIN_DIR . '/' . $product->init;
			if ( is_file( $plugin_abs_path ) ) {
				$global_plugin_installed++;

				if ( is_plugin_active( $product->init ) ) {
					$global_plugin_activated++;
				}
			}
		}
		?>
		<input type="hidden" name="bsf_install_nonce" id="bsf_install_nonce_input" value="<?php echo esc_attr( $nonce ); ?>" >
		<ul class="bsf-extensions-list">
		<?php

		foreach ( $brainstrom_bundled_products as $key => $product ) :

			if ( ! isset( $product->id ) || empty( $product->id ) ) {
					continue;
			}

			if ( isset( $request_product_id ) && $request_product_id !== $product->id ) {
				continue;
			}

			$is_plugin_installed = false;
			$is_plugin_activated = false;

			$plugin_abs_path = WP_PLUGIN_DIR . '/' . $product->init;
			if ( is_file( $plugin_abs_path ) ) {
				$is_plugin_installed = true;

				if ( is_plugin_active( $product->init ) ) {
					$is_plugin_activated = true;
				}
			}

			if ( $is_plugin_installed ) {
				continue;
			}

			if ( $is_plugin_installed && $is_plugin_activated ) {
				$class = 'active-plugin';
			} elseif ( $is_plugin_installed && ! $is_plugin_activated ) {
				$class = 'inactive-plugin';
			} else {
				$class = 'plugin-not-installed';
			}
			?>
			<li id="ext-<?php echo esc_attr( $key ); ?>" class="bsf-extension <?php echo esc_attr( $class ); ?>">
					<?php if ( ! $is_plugin_installed ) : ?>
								<div class="bsf-extension-start-install">
									<div class="bsf-extension-start-install-content">
										<h2><?php esc_html_e( 'Downloading', 'bsf' ); ?><div class="bsf-css-loader"></div></h2>
									</div>
								</div>
							<?php endif; ?>
							<div class="top-section">
				<?php if ( ! empty( $product->product_image ) ) : ?>
									<div class="bsf-extension-product-image">
										<div class="bsf-extension-product-image-stick">
											<img src="<?php echo esc_url( $product->product_image ); ?>" class="img" alt="image"/>
										</div>
									</div>
								<?php endif; ?>
								<div class="bsf-extension-info">
									<?php $name = ( isset( $product->short_name ) ) ? $product->short_name : $product->name; ?>
									<h4 class="title"><?php echo esc_html( $name ); ?></h4>
									<p class="desc"><?php echo esc_html( $product->description ); ?><span class="author"><cite>By <?php echo esc_html( $product->author ); ?></cite></span></p>
								</div>
							</div>
							<div class="bottom-section">
						<?php
						$button_class = '';
						if ( ! $is_plugin_installed ) {
							if ( ( ! $product->licence_require || 'false' === $product->licence_require ) || 'registered' === $product_status ) {

								$installer_url = bsf_exension_installer_url( $product_id );
								$button        = __( 'Install', 'bsf' );
								$button_class  = 'bsf-install-button';
							} elseif ( ( $product->licence_require || 'true' === $product->licence_require ) && 'registered' !== $product_status ) {

								$installer_url = bsf_registration_page_url( '&id=' . $product_id, $product_id );
								$button        = __( 'Validate Purchase', 'bsf' );
								$button_class  = 'bsf-validate-licence-button';
							}
						} else {
							$current_name = strtolower( bsf_get_current_name( $product->init, $product->type ) );
							$current_name = preg_replace( '![^a-z0-9]+!i', '-', $current_name );
							if ( is_multisite() ) {
								$installer_url = network_admin_url( 'plugins.php#' . $current_name );
							} else {
								$installer_url = admin_url( 'plugins.php#' . $current_name );
							}
							$button = __( 'Installed', 'bsf' );
						}

						?>
								<a class="button button-primary extension-button <?php echo esc_attr( $button_class ); ?>" href="<?php echo esc_url( $installer_url ); ?>" data-ext="<?php echo esc_attr( $key ); ?>" data-pid="<?php echo esc_attr( $product->id ); ?>" data-bundled="true" data-action="install"><?php echo esc_html( $button ); ?></a>
							</div>
						</li>
				<?php endforeach; ?>
				<?php
				if ( $total_bundled_plugins === $global_plugin_installed ) :
					?>
					<div class="bsf-extensions-no-active">
						<div class="bsf-extensions-title-icon"><span class="dashicons dashicons-smiley"></span></div>
						<p class="bsf-text-light"><em><?php esc_html_e( 'All available extensions have been installed!', 'bsf' ); ?></em></p>
					</div>
				<?php endif; ?>
		</ul>


		<!-- Stat - Just Design Purpose -->
		<hr class="bsf-extensions-lists-separator">
		<h3 class="bf-ext-sub-title"><?php esc_html_e( 'Installed Extensions', 'bsf' ); ?></h3>
		<ul class="bsf-extensions-list">
			<?php
			if ( 0 !== $global_plugin_installed ) :
				foreach ( $brainstrom_bundled_products as $key => $product ) :
					if ( ! isset( $product->id ) || empty( $product->id ) ) {
						continue;
					}

					if ( isset( $request_product_id ) && $request_product_id !== $product->id ) {
						continue;
					}

					$is_plugin_installed = false;
					$is_plugin_activated = false;

					$plugin_abs_path = WP_PLUGIN_DIR . '/' . $product->init;
					if ( is_file( $plugin_abs_path ) ) {
						$is_plugin_installed = true;

						if ( is_plugin_active( $product->init ) ) {
							$is_plugin_activated = true;
						}
					}

					if ( ! $is_plugin_installed ) {
						continue;
					}

					if ( $is_plugin_installed && $is_plugin_activated ) {
						$class = 'active-plugin';
					} elseif ( $is_plugin_installed && ! $is_plugin_activated ) {
						$class = 'inactive-plugin';
					} else {
						$class = 'plugin-not-installed';
					}
					?>
						<li id="ext-<?php echo esc_attr( $key ); ?>" class="bsf-extension <?php echo esc_attr( $class ); ?>">
							<?php if ( ! $is_plugin_installed ) : ?>
								<div class="bsf-extension-start-install">
									<div class="bsf-extension-start-install-content">
										<h2><?php esc_html_e( 'Downloading', 'bsf' ); ?><div class="bsf-css-loader"></div></h2>
									</div>
								</div>
							<?php endif; ?>
							<div class="top-section">
								<?php if ( ! empty( $product->product_image ) ) : ?>
									<div class="bsf-extension-product-image">
										<div class="bsf-extension-product-image-stick">
											<img src="<?php echo esc_url( $product->product_image ); ?>" class="img" alt="image"/>
										</div>
									</div>
								<?php endif; ?>
								<div class="bsf-extension-info">
									<?php $name = ( isset( $product->short_name ) ) ? $product->short_name : $product->name; ?>
									<h4 class="title"><?php echo esc_html( $name ); ?></h4>
									<p class="desc"><?php echo esc_html( $product->description ); ?><span class="author"><cite>By <?php echo esc_html( $product->author ); ?></cite></span></p>
								</div>
							</div>
							<div class="bottom-section">
								<?php
									$button_class = '';
								if ( ! $is_plugin_installed ) {
									if ( ( ! $product->licence_require || 'false' === $product->licence_require ) || 'registered' === $product_status ) {
										$installer_url = bsf_exension_installer_url( $product_id );
										$button        = __( 'Install', 'bsf' );
										$button_class  = 'bsf-install-button';
									} elseif ( ( $product->licence_require || 'true' === $product->licence_require ) && 'registered' !== $product_status ) {
										$installer_url = bsf_registration_page_url( '&id=' . $product_id );
										$button        = __( 'Validate Purchase', 'bsf' );
										$button_class  = 'bsf-validate-licence-button';
									}
								} else {
									$current_name = strtolower( bsf_get_current_name( $product->init, $product->type ) );
									$current_name = preg_replace( '![^a-z0-9]+!i', '-', $current_name );
									if ( is_multisite() ) {
										$installer_url = network_admin_url( 'plugins.php#' . $current_name );
									} else {
										$installer_url = admin_url( 'plugins.php#' . $current_name );
									}
									$button = __( 'Installed', 'bsf' );
								}

								?>
								<a class="button button-primary extension-button <?php echo esc_attr( $button_class ); ?>" href="<?php echo esc_url( $installer_url ); ?>" data-ext="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $button ); ?></a>
							</div>
						</li>
					<?php
					endforeach;
				else :
					?>
					<div class="bsf-extensions-no-active">
						<div class="bsf-extensions-title-icon"><span class="dashicons dashicons-download"></span></div>
						<p class="bsf-text-light"><em><?php esc_html_e( 'No extensions installed yet!', 'bsf' ); ?></em></p>
					</div>
				<?php endif; ?>
		</ul>

		<!-- End - Just Design Purpose -->
	<?php else : ?>
		<div class="bsf-extensions-no-active">
			<div class="bsf-extensions-title-icon"><span class="dashicons dashicons-download"></span></div>
			<p class="bsf-text-light"><em><?php esc_html_e( 'No extensions available yet!', 'bsf' ); ?></em></p>

			<div class="bsf-cp-rem-bundle" style="margin-top: 30px;">
				<a class="button-primary" href="<?php echo esc_url( $reset_bundled_url ); ?>"><?php esc_html_e( 'Refresh Bundled Products', 'bsf' ); ?></a>
			</div>
		</div>

	<?php endif; ?>
</div>

</div>

<?php if ( isset( $_GET['noajax'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
	<script type="text/javascript">
	(function($){
		$(document).ready(function(){
			$('.bsf-install-button').on('click',function(e){
				if((typeof $(this).attr('disabled') !== 'undefined' && $(this).attr('disabled') === 'disabled'))
					return false;
				$('.bsf-install-button').attr('disabled',true);
				var ext = $(this).attr('data-ext');
				var $ext = $('#ext-'+ext);
				$ext.find('.bsf-extension-start-install').addClass('show-install');
			});
		});
	})(jQuery);
	</script>
<?php else : ?>
	<script type="text/javascript">
	(function($){
		$(document).ready(function(){
			$('.bsf-install-button').on('click',function(e){
				e.preventDefault();

				var is_plugin_installed = is_plugin_activated = false;

				if((typeof $(this).attr('disabled') !== 'undefined' && $(this).attr('disabled') === 'disabled'))
					return false;
				$(this).attr('disabled',true);
				var ext = $(this).attr('data-ext');
				var product_id = $(this).attr('data-pid');
				var action = 'bsf_'+$(this).attr('data-action');
				var bundled = $(this).attr('data-bundled');
				var security = $( "#bsf_install_nonce_input" ).val();
				var $ext = $('#ext-'+ext);
				$ext.find('.bsf-extension-start-install').addClass('show-install');
				var data = {
					'action': action,
					'product_id': product_id,
					'bundled' : bundled,
					'security' : security
				};

				var $link = $(this).attr('href');

				// We can also pass the url value separately from ajaxurl for front end AJAX implementations
				jQuery.post(ajaxurl, data, function(response) {
					console.log(response);

					var redirect = /({.+})/img;
					var matches = redirect.exec(response);
					if ( typeof matches[1] != "undefined" ) {
						var responseObj = jQuery.parseJSON( matches[1] );

						if ( responseObj.redirect != "" ) {
							window.location = responseObj.redirect;
						}
					}

					var blank_response = true;
					var plugin_status = response.split('|');
					var is_ftp = false;
					$.each(plugin_status, function(i,res){
						if(res === 'bsf-plugin-installed') {
							is_plugin_installed = true;
							blank_response = false;
						}
						if(res === 'bsf-plugin-activated') {
							is_plugin_activated = true;
							blank_response = false;
						}
						if(/Connection Type/i.test(response)) {
							is_ftp = true;
						}
					});
					if(is_plugin_installed) {
						$ext.addClass('bsf-plugin-installed');
						$ext.find('.bsf-install-button').addClass('bsf-plugin-installed-button').html('Installed <i class="dashicons dashicons-yes"></i>');
						$ext.find('.bsf-extension-start-install').removeClass('show-install');
					}
					if(is_plugin_activated) {
						$ext.addClass('bsf-plugin-activated');
					}
					if(blank_response) {
						//$ext.find('.bsf-extension-start-install').find('.bsf-extension-start-install-content').html(response);
						if(is_ftp == true) {
							$ext.find('.bsf-extension-start-install').find('.bsf-extension-start-install-content').html('<h3>FTP protected, <br/>redirecting to traditional installer.</h3>');
							$('.bsf-install-button').attr('disabled',true);
							setTimeout(function(){
								window.location = $link;
							},2000);
						} else {
							$ext.find('.bsf-extension-start-install').find('.bsf-extension-start-install-content').html('<h3>Something went wrong! Contact plugin author.</h3>');
						}
					}
				});
			});
		});
	})(jQuery);
	</script>
<?php endif; ?>
