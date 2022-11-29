<?php
/**
 * Debug.
 *
 * @package Debug.
 */

	$author        = true;
	$author_extend = '';
if ( $author ) {
	$author_extend = '&author';
}
?>

<div class="wrap about-wrap bsf-page-wrapper ultimate-debug bend">
<div class="wrap-container">
<div class="bend-heading-section ultimate-header">
<h1><?php esc_html_e( 'Ultimate Addons Settings', 'ultimate_vc' ); ?></h1>
<h3><?php esc_html_e( 'Enable or disable the features as per your preference.', 'ultimate_vc' ); ?></h3>
<div class="bend-head-logo">
<div class="bend-product-ver">
		<?php
		esc_html_e( 'Version', 'ultimate_vc' );
		echo esc_html( ' ' . ULTIMATE_VERSION );
		?>
		</div>
</div>
	</div><!-- bend-heading section -->

	<div id="msg"></div>
	<div id="bsf-message"></div>

	<div class="bend-content-wrap">
<div class="smile-settings-wrapper">
		<h2 class="nav-tab-wrapper">
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=about-ultimate' . $author_extend ) ); ?>" data-tab="about-ultimate" class="nav-tab"> <?php echo esc_html__( 'About', 'ultimate_vc' ); ?> </a>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=ultimate-dashboard' . $author_extend ) ); ?>" data-tab="ultimate-modules" class="nav-tab"> <?php echo esc_html__( 'Elements', 'ultimate_vc' ); ?> </a>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=ultimate-smoothscroll' . $author_extend ) ); ?>" data-tab="css-settings" class="nav-tab"> <?php echo esc_html__( 'Smooth Scroll', 'ultimate_vc' ); ?> </a>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=ultimate-scripts-and-styles' . $author_extend ) ); ?>" data-tab="css-settings" class="nav-tab"> <?php echo esc_html__( 'Scripts and Styles', 'ultimate_vc' ); ?> </a>
			<?php if ( $author ) : ?>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=ultimate-debug-settings' ) ); ?>" data-tab="ultimate-debug" class="nav-tab nav-tab-active"> Debug </a>
			<?php endif; ?>
		</h2>
</div><!-- smile-settings-wrapper -->

</hr>

<div class="container ultimate-content">
			<div class="col-md-12">
				<?php if ( $author ) : ?>
					<div id="ultimate-debug" class="ult-tabs active-tab">
						<form method="post" id="ultimate_debug_settings">
							<input type="hidden" name="security" value="<?php echo esc_attr( wp_create_nonce( 'ultimate-debug-settings' ) ); ?>" />
							<input type="hidden" name="action" value="update_ultimate_debug_options" />
							<table class="form-table">
								<tbody>
									<tr>
										<th scope="row"><?php echo esc_html__( 'Theme', 'ultimate_vc' ); ?></th>
										<td>
											<?php
												$site_theme    = wp_get_theme();
												$current_theme = $site_theme->get( 'Name' );
												echo esc_html( $current_theme );
											?>
									</td>
									</tr>
									<?php
										$ultimate_video_fixer = get_option( 'ultimate_video_fixer' );
									if ( 'enable' == $ultimate_video_fixer ) {
										$ultimate_video_fixer = 'checked="checked"';
									}
									?>
									<tr valign="top">
										<th scope="row"><?php echo esc_html__( 'Video Fixer', 'ultimate_vc' ); ?></th>
										<td> <div class="onoffswitch">
										<input type="checkbox" <?php echo esc_attr( $ultimate_video_fixer ); ?> id="ultimate_video_fixer" value="enable" class="onoffswitch-checkbox" name="ultimate_video_fixer" />
											<label class="onoffswitch-label" for="ultimate_video_fixer">
												<div class="onoffswitch-inner">
													<div class="onoffswitch-active">
														<div class="onoffswitch-switch"><?php echo esc_html__( 'ON', 'ultimate_vc' ); ?></div>
													</div>
													<div class="onoffswitch-inactive">
														<div class="onoffswitch-switch"><?php echo esc_html__( 'OFF', 'ultimate_vc' ); ?></div>
													</div>
												</div>
											</label>
											</div>
										</td>
									</tr>

									<?php
										$ultimate_ajax_theme = get_option( 'ultimate_ajax_theme' );
									if ( 'enable' == $ultimate_ajax_theme ) {
										$ultimate_ajax_theme = 'checked="checked"';
									}
									?>
									<tr valign="top">
										<th scope="row"><?php echo esc_html__( 'Ajax', 'ultimate_vc' ); ?></th>
										<td> <div class="onoffswitch">
										<input type="checkbox" <?php echo esc_attr( $ultimate_ajax_theme ); ?> id="ultimate_ajax_theme" value="enable" class="onoffswitch-checkbox" name="ultimate_ajax_theme" />
											<label class="onoffswitch-label" for="ultimate_ajax_theme">
												<div class="onoffswitch-inner">
													<div class="onoffswitch-active">
														<div class="onoffswitch-switch"><?php echo esc_html__( 'ON', 'ultimate_vc' ); ?></div>
													</div>
													<div class="onoffswitch-inactive">
														<div class="onoffswitch-switch"><?php echo esc_html__( 'OFF', 'ultimate_vc' ); ?></div>
													</div>
												</div>
											</label>
											</div>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row"><?php echo esc_html__( 'Icon Fonts', 'ultimate' ); ?></th>
										<td>
											<a href="<?php echo esc_url( admin_url( 'admin.php?page=bsf-font-icon-manager&delete-bsf-fonts' ) ); ?>" target="_blank" class="button">Reset Icon Fonts</a>
										</td>
									</tr>
									<?php
										$ultimate_theme_support = get_option( 'ultimate_theme_support' );
										$theme_dependant        = '';
									if ( 'enable' == $ultimate_theme_support ) {
										$ultimate_theme_support = 'checked="checked"';
										$theme_dependant        = 'display:none;';
									}
									?>
									<tr valign="top">
										<th scope="row"><?php echo esc_html__( 'Theme Support', 'ultimate_vc' ); ?></th>
										<td> <div class="onoffswitch" id="ult-theme-support-row">
										<input type="checkbox" <?php echo esc_attr( $ultimate_theme_support ); ?> id="ultimate_theme_support" value="enable" class="onoffswitch-checkbox" name="ultimate_theme_support" />
											<label class="onoffswitch-label" for="ultimate_theme_support">
												<div class="onoffswitch-inner">
													<div class="onoffswitch-active">
														<div class="onoffswitch-switch"><?php echo esc_html__( 'ON', 'ultimate_vc' ); ?></div>
													</div>
													<div class="onoffswitch-inactive">
														<div class="onoffswitch-switch"><?php echo esc_html__( 'OFF', 'ultimate_vc' ); ?></div>
													</div>
												</div>
											</label>
											</div>
										</td>
									</tr>
									<?php
										$ultimate_rtl_support = get_option( 'ultimate_rtl_support' );
									if ( 'enable' == $ultimate_rtl_support ) {
										$ultimate_rtl_support = 'checked="checked"';
									}
									?>
									<tr valign="top">
										<th scope="row"><?php echo esc_html__( 'RTL Support', 'ultimate_vc' ); ?></th>
										<td> <div class="onoffswitch" id="ult-rtl-support-row">
										<input type="checkbox" <?php echo esc_attr( $ultimate_rtl_support ); ?> id="ultimate_rtl_support" value="enable" class="onoffswitch-checkbox" name="ultimate_rtl_support" />
											<label class="onoffswitch-label" for="ultimate_rtl_support">
												<div class="onoffswitch-inner">
													<div class="onoffswitch-active">
														<div class="onoffswitch-switch"><?php echo esc_html__( 'ON', 'ultimate_vc' ); ?></div>
													</div>
													<div class="onoffswitch-inactive">
														<div class="onoffswitch-switch"><?php echo esc_html__( 'OFF', 'ultimate_vc' ); ?></div>
													</div>
												</div>
											</label>
											</div>
										</td>
									</tr>
									<?php
										$ultimate_custom_vc_row = get_option( 'ultimate_custom_vc_row' );
									?>
									<tr valign="top" class="ult-theme-support-row-dependant" style="<?php echo esc_attr( $theme_dependant ); ?>">
										<th scope="row"><?php echo esc_html__( 'Custom VC Row Class', 'ultimate_vc' ); ?></th>
										<td>
											<div>
												<input type="text" id="ultimate_custom_vc_row" value="<?php echo esc_attr( $ultimate_custom_vc_row ); ?>" name="ultimate_custom_vc_row" />
											</div>
										</td>
									</tr>
									<!--<tr valign="top">
										<th scope="row"><?php echo esc_html__( 'Deregister Licence', 'ultimate_vc' ); ?></th>
										<td>
											<div>
												<a href="<?php echo esc_attr( admin_url( 'admin.php?page=bsf-dashboard&deregister-licence' ) ); ?>" target="_blank" class="button-primary">Deregister Licence</a>
											</div>
										</td>
									</tr>-->
									<?php
										$ultimate_modal_fixer = get_option( 'ultimate_modal_fixer' );
									if ( 'enable' == $ultimate_modal_fixer ) {
										$ultimate_modal_fixer = 'checked="checked"';
									}
									?>
									<!--<tr valign="top">
										<th scope="row"><?php echo esc_html__( 'Modal Fixer', 'ultimate_vc' ); ?></th>
										<td> <div class="onoffswitch">
										<input type="checkbox" <?php echo esc_attr( $ultimate_modal_fixer ); ?> id="ultimate_modal_fixer" value="enable" class="onoffswitch-checkbox" name="ultimate_modal_fixer" />
											<label class="onoffswitch-label" for="ultimate_modal_fixer">
												<div class="onoffswitch-inner">
													<div class="onoffswitch-active">
														<div class="onoffswitch-switch"><?php echo esc_html__( 'ON', 'ultimate_vc' ); ?></div>
													</div>
													<div class="onoffswitch-inactive">
														<div class="onoffswitch-switch"><?php echo esc_html__( 'OFF', 'ultimate_vc' ); ?></div>
													</div>
												</div>
											</label>
											</div>
										</td>
									</tr>-->
									<?php
										$bsf_dev_mode = bsf_get_option( 'dev_mode' );
									if ( 'enable' == $bsf_dev_mode ) {
										$bsf_dev_mode = 'checked="checked"';
									}
									?>
									<tr>
										<th scope="row"><?php echo esc_html__( 'Developer Mode', 'ultimate_vc' ); ?></th>
										<td> <div class="onoffswitch">
										<input type="checkbox" <?php echo esc_attr( $bsf_dev_mode ); ?> id="bsf_dev_mode" value="enable" class="onoffswitch-checkbox" name="bsf_options[dev_mode]" />
											<label class="onoffswitch-label" for="bsf_dev_mode">
												<div class="onoffswitch-inner">
													<div class="onoffswitch-active">
														<div class="onoffswitch-switch"><?php echo esc_html__( 'ON', 'ultimate_vc' ); ?></div>
													</div>
													<div class="onoffswitch-inactive">
														<div class="onoffswitch-switch"><?php echo esc_html__( 'OFF', 'ultimate_vc' ); ?></div>
													</div>
												</div>
											</label>
											</div>
										</td>
									</tr>
									<?php
										$ultimate_global_scripts = bsf_get_option( 'ultimate_global_scripts' );
									if ( 'enable' == $ultimate_global_scripts ) {
										$ultimate_global_scripts = 'checked="checked"';
									}
									?>
									<tr>
										<th scope="row"><?php echo esc_html__( 'Load scripts globally', 'ultimate_vc' ); ?></th>
										<td> <div class="onoffswitch">
										<input type="checkbox" <?php echo esc_attr( $ultimate_global_scripts ); ?> id="ultimate_global_scripts" value="enable" class="onoffswitch-checkbox" name="bsf_options[ultimate_global_scripts]" />
											<label class="onoffswitch-label" for="ultimate_global_scripts">
												<div class="onoffswitch-inner">
													<div class="onoffswitch-active">
														<div class="onoffswitch-switch"><?php echo esc_html__( 'ON', 'ultimate_vc' ); ?></div>
													</div>
													<div class="onoffswitch-inactive">
														<div class="onoffswitch-switch"><?php echo esc_html__( 'OFF', 'ultimate_vc' ); ?></div>
													</div>
												</div>
											</label>
											</div>
										</td>
									</tr>
									<?php
										$ultimate_modal_menu = bsf_get_option( 'ultimate_modal_menu' );
									if ( 'enable' == $ultimate_modal_menu ) {
										$ultimate_modal_menu = 'checked="checked"';
									}
									?>
									<tr>
										<th scope="row"><?php echo esc_html__( 'Modal on Menu', 'ultimate_vc' ); ?></th>
										<td> <div class="onoffswitch">
										<input type="checkbox" <?php echo esc_attr( $ultimate_modal_menu ); ?> id="ultimate_modal_menu" value="enable" class="onoffswitch-checkbox" name="bsf_options[ultimate_modal_menu]" />
											<label class="onoffswitch-label" for="ultimate_modal_menu">
												<div class="onoffswitch-inner">
													<div class="onoffswitch-active">
														<div class="onoffswitch-switch"><?php echo esc_html__( 'ON', 'ultimate_vc' ); ?></div>
													</div>
													<div class="onoffswitch-inactive">
														<div class="onoffswitch-switch"><?php echo esc_html__( 'OFF', 'ultimate_vc' ); ?></div>
													</div>
												</div>
											</label>
											</div>
										</td>
									</tr>
									<tr valign="top">
										<?php
											$ultimate_smooth_scroll_compatible = get_option( 'ultimate_smooth_scroll_compatible' );
										if ( 'enable' == $ultimate_smooth_scroll_compatible ) {
											$ultimate_smooth_scroll_compatible_attr = 'checked="checked"';
										} else {
											$ultimate_smooth_scroll_compatible_attr = '';
										}
										?>
										<th scope="row"><?php echo esc_html__( 'Smooth Scroll - Old library', 'ultimate_vc' ); ?></th>
										<td> <div class="onoffswitch">
										<input type="checkbox" <?php echo esc_attr( $ultimate_smooth_scroll_compatible_attr ); ?> id="ultimate_smooth_scroll_compatible" value="enable" class="onoffswitch-checkbox" name="ultimate_smooth_scroll_compatible" />
											<label class="onoffswitch-label" for="ultimate_smooth_scroll_compatible">
												<div class="onoffswitch-inner">
													<div class="onoffswitch-active">
														<div class="onoffswitch-switch"><?php echo esc_html__( 'ON', 'ultimate_vc' ); ?></div>
													</div>
													<div class="onoffswitch-inactive">
														<div class="onoffswitch-switch"><?php echo esc_html__( 'OFF', 'ultimate_vc' ); ?></div>
													</div>
												</div>
											</label>
											</div>
										</td>
									</tr>
									<tr valign="top">
										<?php
											$ultimate_animation = get_option( 'ultimate_animation' );
										if ( 'enable' == $ultimate_animation ) {
											$ultimate_animation = 'checked="checked"';
										} else {
											$ultimate_animation = '';
										}
										?>
										<th scope="row"><?php echo esc_html__( 'Animation Block on Mobile', 'ultimate_vc' ); ?></th>
										<td> <div class="onoffswitch">
										<input type="checkbox" <?php echo esc_attr( $ultimate_animation ); ?> id="ultimate_animations" value="enable" class="onoffswitch-checkbox" name="ultimate_animation" />
											<label class="onoffswitch-label" for="ultimate_animations">
												<div class="onoffswitch-inner">
													<div class="onoffswitch-active">
														<div class="onoffswitch-switch"><?php echo esc_html__( 'ON', 'ultimate_vc' ); ?></div>
													</div>
													<div class="onoffswitch-inactive">
														<div class="onoffswitch-switch"><?php echo esc_html__( 'OFF', 'ultimate_vc' ); ?></div>
													</div>
												</div>
											</label>
											</div>
										</td>
									</tr>
									<tr valign="top">
										<?php
											$bsf_ultimate_roles = ( bsf_get_option( 'ultimate_roles' ) ) ? bsf_get_option( 'ultimate_roles' ) : array();
										?>
										<th scope="row"><?php echo esc_html__( 'Enable access', 'ultimate_vc' ); ?></th>
										<td>
											<?php
												$roles = get_editable_roles();
											foreach ( $roles as $role_name => $role_info ) :
												$is_admin = ( 'administrator' == $role_name ) ? true : false;
												$checked  = ( in_array( $role_name, $bsf_ultimate_roles ) || $is_admin ) ? 'checked' : '';
												?>
													<div>
														<input <?php echo ( $is_admin ) ? 'disabled' : ''; ?> id="<?php echo esc_attr( $role_name ); ?>" type="checkbox" name="bsf_options[ultimate_roles][]" value="<?php echo esc_attr( $role_name ); ?>" <?php echo esc_attr( $checked ); ?> /><label for="<?php echo esc_attr( $role_name ); ?>"><?php echo esc_attr( $role_info['name'] ); ?></label>
													</div>
												<?php
												endforeach;
											?>
										</td>
									</tr>

									<?php

										$license_form_url = add_query_arg(
											array(
												'bsf-inline-license-form'   => '6892199',
												'license-form-method'       => 'license-key',
											),
											network_admin_url( 'plugins.php' )
										);

									?>
								</tbody>
							</table>
						</form>
						<p class="submit"><input type="submit" name="submit" id="submit-debug-settings" class="button button-primary" value="<?php echo esc_html__( 'Save Changes', 'ultimate' ); ?>"></p>
					</div>
				<?php endif; ?>
		</div> <!--col-md-12 -->
	</div> <!-- ultimate-content -->
	</div> <!-- bend-content-wrap -->
</div> <!-- .wrap-container -->
</div> <!-- .bend -->
<script type="text/javascript">
var submit_btn = jQuery("#submit-debug-settings");
submit_btn.bind('click',function(e){
	e.preventDefault();
	var data = jQuery("#ultimate_debug_settings").serialize();
	jQuery.ajax({
		url: ajaxurl,
		data: data,
		dataType: 'html',
		type: 'post',
		success: function(result){
			result = jQuery.trim(result);
			console.log(result);
			if(result == "success"){
				jQuery("#msg").html('<div class="updated"><p><?php echo esc_html__( 'Settings updated successfully!', 'ultimate_vc' ); ?></p></div>');
			} else {
				jQuery("#msg").html('<div class="error"><p><?php echo esc_html__( 'No settings were updated.', 'ultimate_vc' ); ?></p></div>');
			}
		}
	});
});
jQuery(document).ready(function(e) {

	jQuery('.onoffswitch').on('onUltimateSwitchClick',function(){
		setTimeout(function(){
			if(jQuery('#ultimate_theme_support').is(':checked'))
			{
				jQuery('.ult-theme-support-row-dependant').fadeOut(200);
			}
			else
			{
				jQuery('.ult-theme-support-row-dependant').fadeIn(200);
			}
		},300);
	});

	jQuery('.onoffswitch').click(function(){
		$switch = jQuery(this);
		setTimeout(function(){
			if($switch.find('.onoffswitch-checkbox').is(':checked'))
				$switch.find('.onoffswitch-checkbox').attr('checked',false);
			else
				$switch.find('.onoffswitch-checkbox').attr('checked',true);
			$switch.trigger('onUltimateSwitchClick');
		},300);

	});

});
</script>
<style type="text/css">
/*On Off Checkbox Switch*/
.onoffswitch {
	position: relative;
	width: 95px;
	display: inline-block;
	float: left;
	margin-right: 15px;
	-webkit-user-select:none;
	-moz-user-select:none;
	-ms-user-select: none;
}
.onoffswitch-checkbox {
	display: none !important;
}
.onoffswitch-label {
	display: block;
	overflow: hidden;
	cursor: pointer;
	border: 0px solid #999999;
	border-radius: 0px;
}
.onoffswitch-inner {
	width: 200%;
	margin-left: -100%;
	-moz-transition: margin 0.3s ease-in 0s;
	-webkit-transition: margin 0.3s ease-in 0s;
	-o-transition: margin 0.3s ease-in 0s;
	transition: margin 0.3s ease-in 0s;
}
.rtl .onoffswitch-inner{
	margin: 0;
}
.rtl .onoffswitch-checkbox:checked + .onoffswitch-label .onoffswitch-inner {
	margin-right: -100%;
	margin-left:auto;
}
.onoffswitch-inner > div {
	float: left;
	position: relative;
	width: 50%;
	height: 24px;
	padding: 0;
	line-height: 24px;
	font-size: 12px;
	color: white;
	font-weight: bold;
	-moz-box-sizing: border-box;
	-webkit-box-sizing: border-box;
	box-sizing: border-box;
}
.onoffswitch-inner .onoffswitch-active {
	padding-left: 15px;
	background-color: #CCCCCC;
	color: #FFFFFF;
}
.onoffswitch-inner .onoffswitch-inactive {
	padding-right: 15px;
	background-color: #CCCCCC;
	color: #FFFFFF;
	text-align: right;
}
.onoffswitch-switch {
	/*width: 50px;*/
	width:35px;
	margin: 0px;
	text-align: center;
	border: 0px solid #999999;
	border-radius: 0px;
	position: absolute;
	top: 0;
	bottom: 0;
}
.onoffswitch-active .onoffswitch-switch {
	background: #3F9CC7;
	left: 0;
}
.onoffswitch-inactive .onoffswitch-switch {
	background: #7D7D7D;
	right: 0;
}
.onoffswitch-active .onoffswitch-switch:before {
	content: " ";
	position: absolute;
	top: 0;
	/*left: 50px;*/
	left:35px;
	border-style: solid;
	border-color: #3F9CC7 transparent transparent #3F9CC7;
	/*border-width: 12px 8px;*/
	border-width: 15px;
}
.onoffswitch-inactive .onoffswitch-switch:before {
	content: " ";
	position: absolute;
	top: 0;
	/*right: 50px;*/
	right:35px;
	border-style: solid;
	border-color: transparent #7D7D7D #7D7D7D transparent;
	/*border-width: 12px 8px;*/
	border-width: 50px;
}
.onoffswitch-checkbox:checked + .onoffswitch-label .onoffswitch-inner {
	margin-left: 0;
}
#ultimate-settings, #ultimate-modules, .ult-tabs{ display:none; }
#ultimate-settings.active-tab, #ultimate-modules.active-tab, .ult-tabs.active-tab{ display:block; }
.ult-badge {
	padding-bottom: 10px;
	height: 170px;
	width: 150px;
	position: absolute;
	border-radius: 3px;
	top: 0;
	right: 0;
}
div#msg > .updated, div#msg > .error { display:block !important;}
div#msg {
	position: absolute;
	left: 20px;
	top: 140px;
	max-width: 30%;
}
.onoffswitch-inner:before,
.onoffswitch-inner:after {
	display:none
}
.onoffswitch-switch {
	height: initial !important;
	color: white !important;
}
</style>
