<?php
$tab = '';
if ( isset( $_POST['bsf-advanced-form-btn'] ) ) {
	$bsf_settings = $_POST['bsf_settings'];
	update_option( 'bsf_settings', $bsf_settings );
}
?>
<?php
$request_product_id = isset( $_GET['id'] ) ? intval( $_GET['id'] ) : '';
$updgrate_action = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';

if ( $updgrate_action === 'upgrade' && $request_product_id !== '' ) {
    $bundled = ( isset( $_GET['bundled'] ) && $_GET['bundled'] !== '' ) ? rest_sanitize_boolean( $_GET['bundled'] ) : false;
    ?>
    <div class="clear"></div>
    <div class="wrap bsf-sp-screen">
        <h2><?php echo __( 'Upgrading Extension', 'bsf' ); ?></h2>
        <?php
        $response = upgrade_bsf_product( $request_product_id, $bundled );
        ?>
        <?php
        if ( isset( $response['status'] ) && $response['status'] ) :
            $url  = ( $response['type'] === 'theme' ) ? 'themes.php' : 'plugins.php';
            $txt  = ( $response['type'] === 'theme' ) ? 'theme' : 'plugin';
            $name = ( isset( $response['name'] ) ) ? $response['name'] : '';
            if ( $name !== '' ) {
                $hashname = preg_replace( '![^a-z0-9]+!i', '-', $name );
                $url     .= '#' . $hashname;
            }
            $reg_url = bsf_registration_page_url();
            $url = ( is_multisite() ) ? network_admin_url( $url ) : admin_url( $url );
            ?>
            <a href="<?php echo( htmlentities($url,  ENT_QUOTES,  "utf-8") ); ?>"><?php echo ( htmlentities('Manage ' . $txt . ' here', ENT_QUOTES,  "utf-8" )); ?></a> |
            <a href="<?php echo $reg_url; ?>"><?php echo __( 'Back to Registration', 'bsf' ); ?></a>
        <?php endif; ?>
    </div>
    <?php
    require_once ABSPATH . 'wp-admin/admin-footer.php';
    exit;
}

$author = ( isset( $_GET['author'] ) ) ? true : false;
if ( $author ) {
	$tab = 'author';
}

$is_product_theme = false;
?>
<script type="text/javascript">
	(function ($) {
		"use strict";
		$(document).ready(function () {
			if (window.location.hash) {
				var hash = window.location.hash;
				$('.nav-tab').removeClass('nav-tab-active');
				$('.nav-tab').each(function (i, nav) {
					var href = $(nav).attr('href');
					if (href === hash) {
						$(nav).addClass('nav-tab-active');
					}
				});
				$('.bsf-tab').hide();
				$(hash).show();
			}

			if ($('body').find('bsf-popup').length == 0)
				$('body').append('<div class="bsf-popup"></div><div class="bsf-popup-message"><div class="bsf-popup-message-inner"></div><span class="bsf-popup-close dashicons dashicons-no-alt"></span></div>');
			$('body').on('click', '.bsf-popup, .bsf-popup-close', function () {
				$('.bsf-popup, .bsf-popup-message').fadeOut(300);
			});
			$('body').on('click', '.bsf-close-reload', function () {
				location.reload();
				$('.bsf-popup, .bsf-popup-message').fadeOut(300);
			});

			/* local storage */
			if (localStorage["bsf_username"]) {
				$('#bsf_username').val(localStorage["bsf_username"]);
			}
			if (localStorage["bsf_useremail"]) {
				$('#bsf_useremail').val(localStorage["bsf_useremail"]);
			}
			if (localStorage["bsf_useremail_reenter"]) {
				$('#bsf_useremail_reenter').val(localStorage["bsf_useremail_reenter"]);
			}
			$('.bsf-pr-input.stored').keyup(function () {
				localStorage[$(this).attr('name')] = $(this).val();
			});
			$('.bsf-pr-input.stored').change(function () {
				localStorage[$(this).attr('name')] = $(this).val();
			});
			/* local storage */

			$('body').on('click', '.bsf-pr-form-submit', function () {
				var form_id = $(this).attr('data-form-id');
				var $form = $('#' + form_id);
				var $wrapper = $form.parent().parent();

				$wrapper.find('.bsf-spinner').toggleClass('bsf-spinner-show');

				var errors = 0;
				$form.parent().find('.bsf-pr-input').each(function (i, input) {
					var type = $(input).attr('type');
					var required = $(input).attr('data-required');
					if (required === 'true' || required === true) {
						if (type === 'text') {
							$(input).removeClass('bsf-pr-input-required');
							if ($(input).val() === '') {
								$(input).addClass('bsf-pr-input-required');
								errors++;
							}
						}
					}
				});
				if (errors > 0) {
					$wrapper.find('.bsf-spinner').toggleClass('bsf-spinner-show');
					return false;
				}
				var data = $form.serialize();
				$.post(ajaxurl, data, function (response) {
					//console.log($.parseJSON(response));
					//return false;
					localStorage.clear(); // clear local storage on success
					var result = $.parseJSON(response);
					console.log(result);

					if (typeof result === 'undefined' || result === null)
						return false;

					var step = $form.find('input[name="step"]').val();

					var state = '';

					//result.proceed = true;

					if (result.proceed === 'false' || result.proceed === false) {
						var html = '';
						if (typeof result.response.title !== 'undefined')
							html += '<h2><span class="dashicons dashicons-info" style="transform: scale(-1);-web-kit-transform: scale(-1);margin-right: 10px;color: rgb(244, 0, 0);  font-size: 25px;"></span>' + result.response.title + '</h2>';
						if (typeof result.response.message_html !== 'undefined')
							html += '<div class="bsf-popup-message-inner-html">' + result.response.message_html + '</div>';
						$('.bsf-popup-message-inner').html(html);
						$('.bsf-popup, .bsf-popup-message').fadeIn(300);
					}
					else if (result.proceed === 'true' || result.proceed === true) {
						if (step === 'step-product-registration') {
							$wrapper.slideUp(200);
							setTimeout(function () {
								$wrapper.append(result.next_action_html);
								$wrapper.find('.bsf-step-1').remove();
								$wrapper.slideDown(300);
							}, 300);

						}
						else {
							var html = '';
							if (typeof result.response.title !== 'undefined')
								html += '<h2><span class="dashicons dashicons-yes" style="margin-right: 10px;color: rgb(0, 213, 0);  font-size: 25px;"></span>' + result.response.title + '</h2>';
							if (typeof result.response.message_html !== 'undefined')
								html += '<div class="bsf-popup-message-inner-html">' + result.response.message_html + '</div>';
							$('.bsf-popup-message-inner').html(html);

							if (typeof result.state !== 'undefined')
								state = result.state;

							$('.bsf-popup-message').addClass(state);
							$('.bsf-popup, .bsf-popup-message').fadeIn(300);
							$('.bsf-popup').addClass('bsf-close-reload');
							$('.bsf-popup-close').addClass('bsf-close-reload');
							//setTimeout(function(){
							//location.reload();
							//},4000);
						}
					}
					$wrapper.find('.bsf-spinner').toggleClass('bsf-spinner-show');
				});
			});

			$('body').on('click', '.bsf-registration-form-toggle', function () {
				var toggle = $(this).attr('data-toggle');
				if (toggle === 'show-form') {
					//$(this).find('span').removeClass('dashicons-arrow-down-alt2').addClass('dashicons-arrow-up-alt2');
					$(this).find('span').addClass('toggle-icon');
					$(this).next('.bsf-pr-form-wrapper').slideDown(300);
					$(this).attr('data-toggle', 'hide-form');
				}
				else {
					//$(this).find('span').removeClass('dashicons-arrow-up-alt2').addClass('dashicons-arrow-down-alt2');
					$(this).find('span').removeClass('toggle-icon');
					$(this).next('.bsf-pr-form-wrapper').slideUp(300);
					$(this).attr('data-toggle', 'show-form');
				}
			});

			$("input[name='bsf_useremail_reenter']").bind("cut copy paste", function (e) {
				e.preventDefault();
			});
		});
	})(jQuery);
</script>
<div class="clear"></div>
<div class="wrap bsf-sp-screen">

	<?php

	$brainstrom_users      = ( get_option( 'brainstrom_users' ) ) ? get_option( 'brainstrom_users' ) : array();
	$brainstorm_users_skip = get_site_option( 'bsf_skip_author', false );
	$bsf_user_name         = $bsf_user_email = $bsf_token = '';
	$hide_license_screen   = false;
	$skip_brainstorm_menu  = get_site_option( 'bsf_skip_braisntorm_menu', false );
	if ( ( defined( 'BSF_UNREG_MENU' ) && ( BSF_UNREG_MENU === true || BSF_UNREG_MENU === 'true' ) ) ||
		$skip_brainstorm_menu == true ) {

		$hide_license_screen = true;
	}
	if ( empty( $brainstrom_users ) && ( ! $author ) && $brainstorm_users_skip != true ) :
		?>
		<div class="bsf-pr-header">
			<h2><?php echo __( "Let's Get Started!", 'imedica' ); ?></h2>

			<div
				class="bsf-pr-decription"><?php echo __( 'Please register using the form below and get instant access to our support portal, updates, extensions and more!', 'bsf' ); ?></div>
		</div>
		<div class="bsf-user-registration-form-wrapper">
			<div class="bsf-user-registration-inner-wrapper">
				<div class="bsf-ur-wrap">
					<div class="bsf-reg-form-wrap">
						<form action="" method="post" id="bsf-user-form" class="bsf-pr-form">
							<input type="hidden" name="action" value="bsf_register_user"/>

							<div class="bsf-pr-form-row">
								<input type="text" id="bsf_username" name="bsf_username" value="" spellcheck="false"
									   placeholder="<?php echo __( 'Your Name', 'bsf' ); ?>" class="bsf-pr-input stored"
									   data-required="true"/>
							</div>
							<div class="bsf-pr-form-row">
								<input type="text" id="bsf_useremail" name="bsf_useremail" value="" spellcheck="false"
									   placeholder="<?php echo __( 'Your Email Address', 'bsf' ); ?>"
									   class="bsf-pr-input stored" data-required="true"/>
							</div>
							<div class="bsf-pr-form-row">
								<input type="text" id="bsf_useremail_reenter" name="bsf_useremail_reenter" value=""
									   spellcheck="false"
									   placeholder="<?php echo __( 'Verify Email Address', 'bsf' ); ?>"
									   class="bsf-pr-input stored" data-required="true"/>
							</div>
							<div class="bsf-pr-form-row">
								<input type="checkbox" name="ultimate_user_receive" id="checkbox-subscribe" value="true"
									   checked="checked" data-required="false"/>
								<label class="checkbox-subscribe"
									   for="checkbox-subscribe"><?php echo __( 'Receive important news, updates & freebies on email.', 'bsf' ); ?></label>
							</div>

						</form>
						<div class="bsf-pr-submit-row">
							<input type="button" class="bsf-pr-form-submit button-primary bsf-button-primary"
								   data-form-id="bsf-user-form"
								   value="<?php echo __( 'Register and Proceed', 'bsf' ); ?>"/>
							<span class="spinner bsf-spinner"></span>
						</div>

						<a href="<?php echo bsf_registration_page_url( '&bsf-skip-author' ); ?>" class="bsf-skip-author">Skip Registration</a>
					</div>
					<div
						class="bsf-pr-form-row bsf-privacy-stat"><?php echo __( 'We respect your privacy & of course you can unsubscribe at any time.', 'bsf' ); ?></div>
				</div>
			</div>
		</div>
		<?php
		return false;
	else :
		$bsf_user_email = ( isset( $brainstrom_users[0]['email'] ) ) ? $brainstrom_users[0]['email'] : '';
		$bsf_user_name  = ( isset( $brainstrom_users[0]['name'] ) ) ? $brainstrom_users[0]['name'] : '';
		if ( empty( $brainstrom_users ) && $brainstorm_users_skip == false ) {
			$hide_license_screen = true;
		}
	endif;
	?>
	<?php
	$brainstrom_bundled_products      = ( get_option( 'brainstrom_bundled_products' ) ) ? get_option( 'brainstrom_bundled_products' ) : array();
	$brainstrom_bundled_products_keys = array();
	if ( ! empty( $brainstrom_bundled_products ) ) :
		foreach ( $brainstrom_bundled_products as $bkeys => $bps ) {
			if ( strlen( $bkeys ) > 1 ) {
				foreach ( $bps as $key => $bp ) {
					if ( ! isset( $bp->id ) || $bp->id == '' ) {
						continue;
					}
					array_push( $brainstrom_bundled_products_keys, $bp->id );
				}
			} else {
				if ( ! isset( $bps->id ) || $bps->id == '' ) {
					continue;
				}
				array_push( $brainstrom_bundled_products_keys, $bps->id );
			}
		}
	endif;

	$brainstrom_products = ( get_option( 'brainstrom_products' ) ) ? get_option( 'brainstrom_products' ) : array();

	$bsf_product_plugins = $bsf_product_themes = array();

	if ( ! empty( $brainstrom_products ) ) :
		$bsf_product_plugins = ( isset( $brainstrom_products['plugins'] ) ) ? $brainstrom_products['plugins'] : array();
		$bsf_product_themes  = ( isset( $brainstrom_products['themes'] ) ) ? $brainstrom_products['themes'] : array();
	endif;

	$plugins = get_plugins();
	$themes  = wp_get_themes();
	?>
	<div class="bsf-pr-header bsf-left-header">
		<h2><?php printf( __( 'Welcome to %s', 'bsf' ), BSF_UPDATER_FULLNAME ); ?></h2>

		<div
			class="bsf-pr-decription"><?php echo __( 'Validate your purchase keys and get eligible for receiving one click updates, extensions and freebies.', 'bsf' ); ?></div>
	</div>

	<div class="inside">

		<?php if ( isset( $bsf_user_email ) && $bsf_user_email !== '' ) : ?>
			<div class="bsf-logged-user-email"><?php echo $bsf_user_email; ?></div>
		<?php endif ?>

		<?php
		foreach ( $plugins as $plugin => $plugin_data ) {
			if ( trim( $plugin_data['Author'] ) === 'Brainstorm Force' ) {
				if ( ! empty( $bsf_product_plugins ) ) :
					foreach ( $bsf_product_plugins as $key => $bsf_product_plugin ) {
						$temp = array();
						if ( ! isset( $bsf_product_plugin['template'] ) ) {
							continue;
						}
						if ( isset( $bsf_product_plugin['is_product_free'] ) && $bsf_product_plugin['is_product_free'] === 'true' ) {
							continue;
						}
						$bsf_template = $bsf_product_plugin['template'];
						if ( $plugin == $bsf_template ) {
							$temp['product_info'] = $bsf_product_plugin;
							$plugin_data          = array_merge( $plugin_data, $temp );
						}
					}
				endif;
				$bsf_plugins[ $plugin ] = $plugin_data;
			}
		}

		foreach ( $themes as $theme => $theme_data ) {
			$data         = wp_get_theme( $theme );
			$theme_author = trim( $data->display( 'Author', false ) );
			if ( $theme_author === 'Brainstorm Force' ) {
				if ( ! empty( $bsf_product_themes ) ) :
					foreach ( $bsf_product_themes as $key => $bsf_product_theme ) {
						$temp = array();
						if ( ! isset( $bsf_product_theme['template'] ) ) {
							continue;
						}
						if ( isset( $bsf_product_theme['is_product_free'] ) && $bsf_product_theme['is_product_free'] === 'true' ) {
							continue;
						}
						$bsf_template = $bsf_product_theme['template'];
						if ( $theme == $bsf_template ) {
							$temp['product_info'] = $bsf_product_theme;
							$theme_data           = array_merge( (array) $theme_data, $temp );
						}
					}
				endif;
				$bsf_themes[ $theme ] = $theme_data;
			}
		}
		?>
		<h2 class="nav-tab-wrapper">

		<?php if ( $hide_license_screen == false ) : ?>
			<a href="#bsf-licenses"
			   class="nav-tab <?php echo ( $tab !== 'author' ) ? 'nav-tab-active' : ''; ?>"><?php echo __( 'Licenses', 'bsf' ); ?></a>
		<?php endif ?>

			<a href="#bsf-help" class="nav-tab"><?php echo __( 'Help', 'bsf' ); ?></a>
			<!--<a href="#bsf-advanced-tab" class="nav-tab"><?php // echo __('Custom Scripts','bsf'); ?></a>-->
			<?php if ( $author ) : ?>
				<a href="#bsf-author"
				   class="nav-tab <?php echo ( $tab === 'author' ) ? 'nav-tab-active' : ''; ?>"><?php echo __( 'Debug', 'bsf' ); ?></a>
			<?php endif; ?>
			<a href="#bsf-system" class="nav-tab"><?php echo __( 'System Info', 'bsf' ); ?></a>
		</h2>

		<div id="bsf-licenses" class="bsf-tab <?php echo ( $tab !== 'author' ) ? 'bsf-tab-active' : ''; ?>">
			<div class="inner">
				<table class="wp-list-table widefat fixed licenses">
					<thead>
					<tr>
						<th scope="col" class="manage-column column-product_name">Product</th>
						<th scope="col" class="manage-column column-product_version">Version</th>
						<th scope="col" class="manage-column column-product_status">Purchase code / License Key</th>
						<th scope="col" class="manage-column column-product_action">Action</th>
					</tr>
					</thead>
					<tbody>
					<?php
					$count = $registered_licence = 0;
					if ( ! empty( $bsf_plugins ) ) :
						foreach ( $bsf_plugins as $plugin => $plugin_data ) :

							$readonly = '';

							if ( ! isset( $plugin_data['product_info'] ) ) {
								continue;
							}

							$info = $plugin_data['product_info'];

							$status = ( isset( $info['status'] ) ) ? $info['status'] : '';

							$purchase_key = ( isset( $info['purchase_key'] ) ) ? $info['purchase_key'] : '';
							$type         = ( isset( $info['type'] ) ) ? $info['type'] : 'plugin';
							$template     = ( isset( $info['template'] ) ) ? $info['template'] : $plugin;
							$id           = ( isset( $info['id'] ) ) ? $info['id'] : '';
							$version      = ( isset( $plugin_data['Version'] ) ) ? $plugin_data['Version'] : '';
							$name         = apply_filters( "agency_updater_productname_{$id}", $plugin_data['Name'] );
							$purchase_url = ( isset( $info['purchase_url'] ) ) ? $info['purchase_url'] : 'javascript:void(0)';

							$bsf_username  = ( isset( $info['bsf_username'] ) ) ? $info['bsf_username'] : $bsf_user_name;
							$bsf_useremail = ( isset( $info['bsf_useremail'] ) ) ? $info['bsf_useremail'] : $bsf_user_email;

							if ( $request_product_id != '' ) {
								$init_single_product_show = true;
							} else {
								$init_single_product_show = false;
							}

							if ( $id === '' ) {
								continue;
							}

							if ( in_array( $id, $brainstrom_bundled_products_keys ) ) {
								continue;
							}

							if ( $init_single_product_show && $request_product_id !== $id ) {
								continue;
							}

							$constant = 'BSF_REMOVE_' . $id . '_FROM_REGISTRATION_LISTING';
							if ( defined( $constant ) && ( $constant == 'true' || $constant == true ) ) {
								continue;
							}

							$remove_frm_registration = apply_filters( 'bsf_remove_' . $id . '_from_registration_listing', false, $id );
							if ( $remove_frm_registration ) {
								continue;
							}

							$step = ( isset( $plugin_data['step'] ) && $plugin_data['step'] != '' ) ? $plugin_data['step'] : 'step-product-registration';

							$common_data  = ' data-product-id="' . $id . '" ';
							$common_data .= ' data-bsf_username="' . $bsf_username . '" ';
							$common_data .= ' data-bsf_useremail="' . $bsf_useremail . '" ';
							$common_data .= ' data-product-type="' . $type . '" ';
							$common_data .= ' data-template="' . $template . '" ';
							$common_data .= ' data-version="' . $version . '" ';
							$common_data .= ' data-step="' . $step . '" ';
							$common_data .= ' data-product-name="' . $name . '" ';

							$mod       = ( $count % 2 );
							$alternate = ( $mod ) ? 'alternate' : '';
							$row_id    = 'bsf-row-' . $count;

							if ( $status === 'registered' ) {
								$readonly     = ' readonly="readonly" ';
								$common_data .= ' data-action="bsf_deregister_product" ';
								$registered_licence ++;
							} else {
								$common_data .= ' data-action="bsf_register_product" ';
							}

							?>
							<tr id="<?php echo $row_id; ?>" class="<?php echo $alternate . ' ' . $status; ?>">
								<td><?php echo $name; ?></td>
								<td><?php echo $plugin_data['Version']; ?></td>
								<td>
								<?php if ( $purchase_key !== '' && $status === 'registered' ) : ?>
									Purchase Verified
								<?php else : ?>
									<input type="text" class="bsf-form-input" name="purchase_key" spellcheck="false"
										   data-required="true"
										   value="<?php echo $purchase_key; ?>" <?php echo $readonly; ?>/>
								<?php endif ?>
								</td>
								<td>
									<?php if ( $status !== 'registered' ) : ?>
													<input type="button" class="button button-primary bsf-submit-button" value="Register" data-row-id="<?php echo $row_id; ?>" <?php echo $common_data; ?>/>
													<a href="<?php echo $purchase_url; ?>" target="_blank" class="bsf-purchase-link" data-row-id="<?php echo $row_id; ?>" />Buy License</a> <span class="spinner bsf-spinner"></span>
												<?php else : ?>
													<input type="button" class="button bsf-submit-button-deregister" value="De-register" data-row-id="<?php echo $row_id; ?>" <?php echo $common_data; ?>/> <span class="spinner bsf-spinner"></span>
												<?php endif; ?>
								</td>
							</tr>
							<?php
							$count ++;
						endforeach;
					endif;

					if ( ! empty( $bsf_themes ) ) :

						foreach ( $bsf_themes as $theme => $theme_data ) :

							// echo '<pre>';
							// print_r($theme_data);
							// echo '</pre>';
							$readonly = '';

							if ( isset( $theme_data['product_info'] ) ) {
								$info = $theme_data['product_info'];
							} else {
								continue;
							}
							$status = ( isset( $info['status'] ) ) ? $info['status'] : '';

							$bsf_username  = ( isset( $info['bsf_username'] ) ) ? $info['bsf_username'] : $bsf_user_name;
							$bsf_useremail = ( isset( $info['bsf_useremail'] ) ) ? $info['bsf_useremail'] : $bsf_user_email;
							$purchase_key  = ( isset( $info['purchase_key'] ) ) ? $info['purchase_key'] : '';
							$type          = ( isset( $info['type'] ) ) ? $info['type'] : 'theme';
							$template      = ( isset( $info['template'] ) ) ? $info['template'] : $plugin;
							$id            = ( isset( $info['id'] ) ) ? $info['id'] : '';
							$purchase_url  = ( isset( $info['purchase_url'] ) ) ? $info['purchase_url'] : 'javascript:void(0)';

							if ( $request_product_id != '' ) {
								$init_single_product_show = true;
							} else {
								$init_single_product_show = false;
							}

							if ( $init_single_product_show && $request_product_id !== $id ) {
								continue;
							}

							$constant = 'BSF_REMOVE_' . $id . '_FROM_REGISTRATION';
							if ( defined( $constant ) && ( $constant == 'true' || $constant == true ) ) {
								continue;
							}

							$version = bsf_get_current_version( $template, $type );
							$name    = bsf_get_current_name( $template, $type );

							$step = ( isset( $theme_data['step'] ) && $theme_data['step'] != '' ) ? $theme_data['step'] : 'step-product-registration';

							$common_data  = ' data-product-id="' . $id . '" ';
							$common_data .= ' data-bsf_username="' . $bsf_username . '" ';
							$common_data .= ' data-bsf_useremail="' . $bsf_useremail . '" ';
							$common_data .= ' data-product-type="' . $type . '" ';
							$common_data .= ' data-template="' . $template . '" ';
							$common_data .= ' data-version="' . $version . '" ';
							$common_data .= ' data-step="' . $step . '" ';
							$common_data .= ' data-product-name="' . $name . '" ';

							$mod       = ( $count % 2 );
							$alternate = ( $mod ) ? 'alternate' : '';
							$row_id    = 'bsf-row-' . $count;

							if ( $status === 'registered' ) {
								$readonly     = ' readonly="readonly" ';
								$common_data .= ' data-action="bsf_deregister_product" ';
								$registered_licence ++;
							} else {
								$common_data .= ' data-action="bsf_register_product" ';
							}

							if ( $type === 'theme' ) {
								$is_product_theme = true;
							}
							?>
							<tr id="<?php echo $row_id; ?>" class="<?php echo $alternate . ' ' . $status; ?>">
								<td><?php echo $name; ?></td>
								<td><?php echo $version; ?></td>
								<td>
								<?php if ( $purchase_key !== '' && $status === 'registered' ) : ?>
									Purchase Verified
								<?php else : ?>
									<input type="text" class="bsf-form-input" name="purchase_key" spellcheck="false"
										   data-required="true"
										   value="<?php echo $purchase_key; ?>" <?php echo $readonly; ?>/>
								<?php endif ?>
								</td>
								<td>
									<?php if ( $status !== 'registered' ) : ?>
													<input type="button" class="button button-primary bsf-submit-button" value="Register" data-row-id="<?php echo $row_id; ?>" <?php echo $common_data; ?>/>
													<a href="<?php echo $purchase_url; ?>" target="_blank" class="bsf-purchase-link" data-row-id="<?php echo $row_id; ?>" />Buy License</a> <span class="spinner bsf-spinner"></span>
												<?php else : ?>
													<input type="button" class="button bsf-submit-button-deregister" value="De-register" data-row-id="<?php echo $row_id; ?>" <?php echo $common_data; ?>/> <span class="spinner bsf-spinner"></span>
												<?php endif; ?>
								</td>
							</tr>
							<?php
							$count ++;
						endforeach;
					endif;
					?>
					</tbody>
				</table>
				<div class="bsf-listing-cta">
					<a href="https://support.brainstormforce.com/license-registration-faqs/" target="_blank">Questions?
						Having Issues?</a>
				</div>
			</div>

		</div>
		<!-- bsf-licence-tab -->
		<div id="bsf-help" class="bsf-tab">
			<div class="inner">
				<div class="bsf-row">
					<div class="bsf-column">
						<div class="bsf-column-inner">
							<h2>Developer Access</h2>
							<span class="bsf-span"><?php echo __( 'Enable Developer access', 'bsf' ); ?>
								,<br/><?php echo __( 'Read more about developer access ', 'bsf' ); ?><a
									href="<?php echo bsf_get_api_site() . 'license-registration-faqs/#developer-access'; ?>"
									target="_blank"><?php echo __( 'here', 'bsf' ); ?></a></span>

							<form action="" class="bsf-cp-dev-access" method="post">
								<?php
								$title = '';
								if ( $registered_licence > 0 ) {
									$disabled = '';
								} else {
									$disabled = 'disabled="disabled"';
									$title    = __( 'Activate your license to enable!', 'bsf' );
									update_option( 'developer_access', false );
								}

								$developer_access = get_option( 'developer_access' );

								if ( $developer_access ) {
									$button_text = 'Revoke developer access';
									$action      = 'revoke';
								} else {
									$button_text = 'Allow developer access';
									$action      = 'grant';
								}
								?>
							</form>
						</div>
					</div>
					<div class="bsf-column">
						<div class="bsf-column-inner">
							<h2>Force Check Updates</h2>
							<span
								class="bsf-span"><?php printf( __( 'Check if there are updates available of plugins by %s.', 'bsf' ), BSF_UPDATER_FULLNAME ); ?></span>
							<?php
								$reset_url = add_query_arg( 'force-check-update', '' );
							?>
							<a class="button-primary bsf-cp-update-btn"
							   href="<?php echo $reset_url; ?>"><?php echo __( 'Check Updates Now', 'bsf' ); ?></a>
						</div>
					</div>
					<div class="bsf-column">
						<div class="bsf-column-inner">
							<h2>Request Support</h2>
							<?php
							$request_support = apply_filters( 'agency_updater_request_support', bsf_get_api_site() . 'request-support/' );
							?>
							<span
								class="bsf-span"><?php echo __( 'Having any trouble using our products? Head to our support center to get your issues resolved.', 'bsf' ); ?></span>
							<a class="button-primary bsf-cp-support-btn"
							   href="<?php echo $request_support; ?>"
							   target="_blank"><?php echo __( 'Request Support', 'bsf' ); ?></a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php if ( $author ) : ?>
			<div id="bsf-author" class="bsf-tab <?php echo ( $tab == 'author' ) ? 'bsf-tab-active' : ''; ?>">
				<div class="inner">
					<div class="bsf-row">
						<div class="bsf-column">
							<div class="bsf-column-inner">
								<?php

								$reset_url = bsf_registration_page_url( '&reset-bsf-users' );
								?>
								<h2>Reset <?php echo BSF_UPDATER_SHORTNAME; ?> Registration</h2>
								<span class="bsf-span">
									Reset all the <?php echo BSF_UPDATER_SHORTNAME; ?> registration data?
								</span>
								<a class="button-primary"
								   href="<?php echo $reset_url; ?>"><?php printf( __( 'Reset %s Registration', 'bsf' ), BSF_UPDATER_SHORTNAME ); ?></a>
							</div>
						</div>
						<div class="bsf-column">
							<div class="bsf-column-inner">
								<?php
									$url = add_query_arg( 'remove-bundled-products', '' );
								?>
								<h2>Reset Bundled Products data</h2>
								<span class="bsf-span">
									Reload the bundled products data from <?php echo BSF_UPDATER_SHORTNAME; ?>'s Servers?
								</span>
								<a class="button-primary"
								   href="<?php echo $url; ?>"><?php echo __( 'Check Bundled Products', 'bsf' ); ?></a>
							</div>
						</div>

						<div class="bsf-column">
							<div class="bsf-column-inner">
								<div class="brainstorm-updater-switch">
									<?php echo brainstorm_switch( 'brainstorm_menu', false ); ?>
								</div>
								<h2><?php printf( __( '%s Menu in Settings?', 'bsf' ), BSF_UPDATER_SHORTNAME ); ?></h2>
								<span class="bsf-span">
									<?php printf( __( 'Move the %s menu to WordPress settings?', 'bsf' ), BSF_UPDATER_SHORTNAME ); ?>
									</span>
								<a class="button-primary brainstorm_menu"
								   href="#"><?php echo __( 'Update', 'bsf' ); ?></a>
								<span class="spinner bsf-spinner"></span>

							</div>
						</div>
					</div>
				</div>
			</div><!-- bsf-author-tab -->
		<?php endif; ?>
		<div id="bsf-system" class="bsf-tab">
			<div class="inner">
				<?php
				echo bsf_systeminfo();
				?>
			</div>
		</div>
		<!-- bsf-system-tab -->
	</div>
</div>
<script type="text/javascript">
	(function ($) {
		"use strict";
		$(document).ready(function () {


			$('body').on('click', '.brainstorm_menu.button-primary', function (event) {
				event.preventDefault();
				var spinner = jQuery( this ).siblings('.bsf-spinner');
				spinner.toggleClass('bsf-spinner-show');
				var val = jQuery('.brainstorm_menu.bsf-switch-input').val();

				jQuery.ajax({
					url: ajaxurl,
					type: 'POST',
					dataType: 'JSON',
					data: {
						action: 'update_bsf_core_options',
						option: 'brainstorm_menu',
						value: val
					},
				})
					.done(function (result) {
						if ( window.location.href !== result.redirect ) {
							window.location.replace(result.redirect);
						}
						spinner.toggleClass('bsf-spinner-show');
					})
					.fail(function (e) {
						console.log("error");
						console.log( e );
						spinner.toggleClass('bsf-spinner-show');
					});

			});

			$('body').on('click', '.bsf-submit-button', function () {
				var row_id = $(this).attr('data-row-id');
				var $row = $('#' + row_id);

				var errors = 0;

				$row.find('.bsf-spinner').toggleClass('bsf-spinner-show');

				var purchase_key = $row.find('input[name="purchase_key"]').val();

				var product_id = $(this).attr('data-product-id');
				var username = $(this).attr('data-bsf_username');
				var useremail = $(this).attr('data-bsf_useremail');
				var product_type = $(this).attr('data-product-type');
				var template = $(this).attr('data-template');
				var version = $(this).attr('data-version');
				var step = $(this).attr('data-step');
				var product_name = $(this).attr('data-product-name');

				var action = $(this).attr('data-action');

				var admin_url = '<?php echo ( is_multisite() ) ? rtrim( network_admin_url(), '/' ) : rtrim( admin_url(), '/' ); ?>';

				$row.find('.bsf-form-input').each(function (i, input) {
					var type = $(input).attr('type');
					var required = $(input).attr('data-required');
					if (required === 'true' || required === true) {
						if (type === 'text') {
							$(input).removeClass('bsf-pr-input-required');
							if ($(input).val() === '') {
								$(input).addClass('bsf-pr-input-required');
								errors++;
							}
						}
					}
				});
				if (errors > 0) {
					$row.find('.bsf-spinner').toggleClass('bsf-spinner-show');
					return false;
				}

				var data = {
					action: action,
					bsf_username: username,
					bsf_useremail: useremail,
					purchase_key: purchase_key,
					type: product_type,
					id: product_id,
					product: template,
					version: version,
					step: step,
					product_name: product_name
				};

				$.post(ajaxurl, data, function (response) {
					console.log(response);
					//return false;
					var result = $.parseJSON(response);
					console.log(result);

					if (typeof result === 'undefined' || result === null)
						return false;

					//result.proceed = true;

					if (result.proceed === 'false' || result.proceed === false) {
						var html = '';
						if (typeof result.response.title !== 'undefined')
							html += '<h2><span class="dashicons dashicons-info" style="transform: scale(-1);-web-kit-transform: scale(-1);margin-right: 10px;color: rgb(244, 0, 0);  font-size: 25px;"></span>' + result.response.title + '</h2>';
						if (typeof result.response.message_html !== 'undefined')
							html += '<div class="bsf-popup-message-inner-html">' + result.response.message_html + '</div>';
						$('.bsf-popup-message-inner').html(html);
						$('.bsf-popup, .bsf-popup-message').fadeIn(300);
					}
					else if (result.proceed === 'true' || result.proceed === true) {
						var html = '';
						if (typeof result.response.title !== 'undefined')
							html += '<h2><span class="dashicons dashicons-yes" style="margin-right: 10px;color: rgb(0, 213, 0);  font-size: 25px;"></span>' + result.response.title + '</h2>';
						if (typeof result.response.message_html !== 'undefined')
							html += '<div class="bsf-popup-message-inner-html">' + result.response.message_html + '</div>';
						$('.bsf-popup-message-inner').html(html);
						$('.bsf-popup, .bsf-popup-message').fadeIn(300);
						if (typeof result.after_registration_action !== 'undefined' && result.after_registration_action !== '')
							if (result.after_registration_action == 'admin.php?page=bsf-extensions') {
								window.location.href = admin_url + '/' + result.after_registration_action + '?product_id=10395942';
							} else {
								window.location.href = admin_url + '/' + result.after_registration_action;
							}
						else
							location.reload();
					}
					$row.find('.bsf-spinner').toggleClass('bsf-spinner-show');
				});
			}); //end of submit button

			$('body').on('click', '.bsf-submit-button-deregister', function () {
				var row_id = $(this).attr('data-row-id');
				var $row = $('#' + row_id);

				var errors = 0;

				$row.find('.bsf-spinner').toggleClass('bsf-spinner-show');

				var purchase_key = $row.find('input[name="purchase_key"]').val();
				var bsf_username = $(this).attr('data-bsf_username');
				var bsf_useremail = $(this).attr('data-bsf_useremail');
				var product_id = $(this).attr('data-product-id');
				var product_type = $(this).attr('data-product-type');
				var template = $(this).attr('data-template');
				var version = $(this).attr('data-version');
				var name = $(this).attr('data-product-name');

				var action = $(this).attr('data-action');

				var data = {
					action: action,
					purchase_key: purchase_key,
					bsf_username: bsf_username,
					bsf_useremail: bsf_useremail,
					type: product_type,
					id: product_id,
					product: template,
					version: version,
					product_name: name
				};

				console.log(data);

				$.post(ajaxurl, data, function (response) {
					//console.log($.parseJSON(response));
					//return false;
					console.log(response);
					//return false;
					var result = $.parseJSON(response);
					var html = '';
					if (typeof result.response.title !== 'undefined')
						html += '<h2><span class="dashicons dashicons-yes" style="margin-right: 10px;color: rgb(0, 213, 0);  font-size: 25px;"></span>' + result.response.title + '</h2>';
					if (typeof result.response.message_html !== 'undefined')
						html += '<div class="bsf-popup-message-inner-html">' + result.response.message_html + '</div>';
					$('.bsf-popup-message-inner').html(html);
					$('.bsf-popup, .bsf-popup-message').fadeIn(300);
					if (result.proceed === 'true' || result.proceed === true) {
						//setTimeout(function(){
						location.reload();
						//},2000);
					}
				});

			}); // end of de-registering licence

			$('body').on('click', '.nav-tab', function (event) {
				//event.preventDefault();
				$('.nav-tab').removeClass('nav-tab-active');
				$(this).addClass('nav-tab-active');
				var tab = $(this).attr('href');
				$('.bsf-tab').fadeOut(200);
				setTimeout(function () {
					$(tab).fadeIn(200);
				}, 200);
			}); // end of tabs functionality
		});
	})(jQuery);
</script>
