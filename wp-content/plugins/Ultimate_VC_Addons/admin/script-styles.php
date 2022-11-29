<?php
/**
 * Script styles Page
 *
 *  @package Script styles Page
 */

if ( isset( $_GET['author'] ) ) { // PHPCS:ignore:WordPress.Security.NonceVerification.Recommended
	$author = true;
} else {
	$author = false;
}
	$author_extend = '';
if ( $author ) {
	$author_extend = '&author';
}
?>

<div class="wrap about-wrap bsf-page-wrapper ultimate-smooth-scroll bend">
<div class="wrap-container">
	<div class="bend-heading-section ultimate-header">
	<h1><?php esc_html_e( 'Ultimate Addons Settings', 'ultimate_vc' ); ?></h1>
	<h3><?php esc_html_e( 'If optimized option is set to ON, a combined file for all modules in our plugin will be loaded on your website. Use this option if you use Ultimate Addons significantly on your website.', 'ultimate_vc' ); ?></h3>
	<div class="bend-head-logo">
		<div class="bend-product-ver">
			<?php
			esc_html_e( 'Version', 'ultimate_vc' );
			echo ' ' . ULTIMATE_VERSION; // PHPCS:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
		</div>
	</div>
	</div><!-- bend-heading section -->

	<div id="msg"></div>
	<div id="bsf-message"></div>

	<div class="bend-content-wrap">
	<div class="smile-settings-wrapper">
			<h2 class="nav-tab-wrapper">
				<a href="<?php echo admin_url( 'admin.php?page=about-ultimate' . $author_extend ); // PHPCS:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped ?>" data-tab="about-ultimate" class="nav-tab"> <?php echo esc_attr__( 'About', 'ultimate_vc' ); ?> </a>
				<a href="<?php echo admin_url( 'admin.php?page=ultimate-dashboard' . $author_extend ); // PHPCS:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped ?>" data-tab="ultimate-modules" class="nav-tab"> <?php echo esc_attr__( 'Elements', 'ultimate_vc' ); ?> </a>
				<a href="<?php echo admin_url( 'admin.php?page=ultimate-smoothscroll' . $author_extend ); // PHPCS:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped ?>" data-tab="css-settings" class="nav-tab"> <?php echo esc_attr__( 'Smooth Scroll', 'ultimate_vc' ); ?> </a>
				<a href="<?php echo admin_url( 'admin.php?page=ultimate-scripts-and-styles' . $author_extend ); // PHPCS:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped ?>" data-tab="css-settings" class="nav-tab nav-tab-active"> <?php echo esc_attr__( 'Scripts and Styles', 'ultimate_vc' ); ?> </a>
				<?php if ( $author ) : ?>
					<a href="<?php echo admin_url( 'admin.php?page=ultimate-debug-settings' ); // PHPCS:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped ?>" data-tab="ultimate-debug" class="nav-tab"> Debug </a>
				<?php endif; ?>
			</h2>
	</div><!-- smile-settings-wrapper -->

	</hr>

	<div class="container ultimate-content">
		<div class="col-md-12">
			<?php

			$ultimate_css = get_option( 'ultimate_css' );
			$ultimate_js  = get_option( 'ultimate_js' );

			if ( 'enable' == $ultimate_css ) {
				$ultimate_css = 'checked="checked"';
			} else {
				$ultimate_css = '';
			}

			if ( 'enable' == $ultimate_js ) {
				$ultimate_js = 'checked="checked"';
			} else {
				$ultimate_js = '';
			}

			?>
			<div id="css-settings" class="ult-tabs active-tab">
				<form method="post" id="css_settings">
					<input type="hidden" name="security" value="<?php echo esc_attr( wp_create_nonce( 'css-settings-setting' ) ); ?>" />
					<input type="hidden" name="action" value="update_css_options" />
					<table class="form-table">
						<tbody>
							<tr valign="top">
								<th scope="row"><?php echo esc_attr__( 'Combined CSS', 'ultimate_vc' ); ?></th>
								<td> <div class="onoffswitch">
								<input type="checkbox" <?php echo esc_attr( $ultimate_css ); ?> id="ultimate_css" value="enable" class="onoffswitch-checkbox" name="ultimate_css" />
									<label class="onoffswitch-label" for="ultimate_css">
										<div class="onoffswitch-inner">
											<div class="onoffswitch-active">
												<div class="onoffswitch-switch"><?php echo esc_attr__( 'ON', 'ultimate_vc' ); ?></div>
											</div>
											<div class="onoffswitch-inactive">
												<div class="onoffswitch-switch"><?php echo esc_attr__( 'OFF', 'ultimate_vc' ); ?></div>
											</div>
										</div>
									</label>
									</div>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row"><?php echo esc_attr__( 'Combined JS', 'ultimate_vc' ); ?></th>
								<td> <div class="onoffswitch">
								<input type="checkbox" <?php echo esc_attr( $ultimate_js ); ?> id="ultimate_js" value="enable" class="onoffswitch-checkbox" name="ultimate_js" />
									<label class="onoffswitch-label" for="ultimate_js">
										<div class="onoffswitch-inner">
											<div class="onoffswitch-active">
												<div class="onoffswitch-switch"><?php echo esc_attr__( 'ON', 'ultimate_vc' ); ?></div>
											</div>
											<div class="onoffswitch-inactive">
												<div class="onoffswitch-switch"><?php echo esc_attr__( 'OFF', 'ultimate_vc' ); ?></div>
											</div>
										</div>
									</label>
									</div>
								</td>
							</tr>
						</tbody>
					</table>
				</form>
				<p class="submit"><input type="submit" name="submit" id="submit-css-settings" class="button button-primary" value="<?php echo esc_attr__( 'Save Changes', 'ultimate_vc' ); ?>"></p>
			</div>
		</div> <!--col-md-12 -->
	</div> <!-- ultimate-content -->
	</div> <!-- bend-content-wrap -->
</div> <!-- .wrap-container -->
</div> <!-- .bend -->

<script type="text/javascript">

var submit_btn = jQuery("#submit-css-settings");
submit_btn.bind('click',function(e){
	e.preventDefault();
	var data = jQuery("#css_settings").serialize();
	jQuery.ajax({
		url: ajaxurl,
		data: data,
		dataType: 'html',
		type: 'post',
		success: function(result){
			console.log(result);
			result = jQuery.trim(result);
			if(result == "success"){
				jQuery("#msg").html('<div class="updated"><p><?php echo esc_attr__( 'Settings updated successfully!', 'ultimate_vc' ); ?></p></div>');
			} else {
				jQuery("#msg").html('<div class="error"><p><?php echo esc_attr__( 'No settings were updated.', 'ultimate_vc' ); ?></p></div>');
			}
		}
	});
});

jQuery(document).ready(function(e) {

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
	top: 160px;
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
