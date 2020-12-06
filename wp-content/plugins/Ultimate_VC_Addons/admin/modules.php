<?php
/**
 * Elements Page
 *
 *  @package Elements Page
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

<?php
	$ultimate_row = get_option( 'ultimate_row' );
if ( 'enable' == $ultimate_row ) {
	$checked_row = 'checked="checked"';
} else {
	$checked_row = '';
}

	$ultimate_modules = get_option( 'ultimate_modules' );
	$modules          = array(
		'Ultimate_Animation'            => __( 'Animation Block', 'ultimate_vc' ),
		'Ultimate_Buttons'              => __( 'Advanced Buttons', 'ultimate_vc' ),
		'Ultimate_CountDown'            => __( 'Count Down Timer', 'ultimate_vc' ),
		'Ultimate_Flip_Box'             => __( 'Flip Boxes', 'ultimate_vc' ),
		'Ultimate_Google_Maps'          => __( 'Google Maps', 'ultimate_vc' ),
		'Ultimate_Google_Trends'        => __( 'Google Trends', 'ultimate_vc' ),
		'Ultimate_Headings'             => __( 'Headings', 'ultimate_vc' ),
		'Ultimate_Icon_Timeline'        => __( 'Timeline', 'ultimate_vc' ),
		'Ultimate_Info_Box'             => __( 'Info Boxes', 'ultimate_vc' ),
		'Ultimate_Info_Circle'          => __( 'Info Circle', 'ultimate_vc' ),
		'Ultimate_Info_List'            => __( 'Info List', 'ultimate_vc' ),
		'Ultimate_Info_Tables'          => __( 'Info Tables', 'ultimate_vc' ),
		'Ultimate_Interactive_Banners'  => __( 'Interactive Banners', 'ultimate_vc' ),
		'Ultimate_Interactive_Banner_2' => __( 'Interactive Banners - 2', 'ultimate_vc' ),
		'Ultimate_Modals'               => __( 'Modal Popups', 'ultimate_vc' ),
		'Ultimate_Pricing_Tables'       => __( 'Price Box', 'ultimate_vc' ),
		'Ultimate_Spacer'               => __( 'Spacer / Gap', 'ultimate_vc' ),
		'Ultimate_Stats_Counter'        => __( 'Counter', 'ultimate_vc' ),
		'Ultimate_Swatch_Book'          => __( 'Swatch Book', 'ultimate_vc' ),
		'Ultimate_Icons'                => __( 'Icons', 'ultimate_vc' ),
		'Ultimate_List_Icon'            => __( 'List Icons', 'ultimate_vc' ),
		'Ultimate_Carousel'             => __( 'Advanced Carousel', 'ultimate_vc' ),
		'Ultimate_Fancy_Text'           => __( 'Fancy Text', 'ultimate_vc' ),
		'Ultimate_Hightlight_Box'       => __( 'Highlight Box', 'ultimate_vc' ),
		'Ultimate_Info_Banner'          => __( 'Info Banner', 'ultimate_vc' ),
		'Ultimate_iHover'               => __( 'iHover', 'ultimate_vc' ),
		'Ultimate_Hotspot'              => __( 'Hotspot', 'ultimate_vc' ),
		'Ultimate_Video_Banner'         => __( 'Video Banner', 'ultimate_vc' ),
		'WooComposer'                   => __( 'WooComposer', 'ultimate_vc' ),
		'Ultimate_Dual_Button'          => __( 'Dual Button', 'ultimate_vc' ),
		'Ultimate_link'                 => __( 'Creative Link', 'ultimate_vc' ),
		'Ultimate_Image_Separator'      => __( 'Image Separator', 'ultimate_vc' ),
		'Ultimate_Content_Box'          => __( 'Content Box', 'ultimate_vc' ),
		'Ultimate_Expandable_section'   => __( 'Expandable Section', 'ultimate_vc' ),
		'Ultimate_Tab'                  => __( 'Advanced Tabs', 'ultimate_vc' ),
		'Ultimate_Team'                 => __( 'Ultimate Teams', 'ultimate_vc' ),
		'Ultimate_Sticky_Section'       => __( 'Sticky Section', 'ultimate_vc' ),
		'Ultimate_Range_Slider'         => __( 'Range Slider', 'ultimate_vc' ),
		'Ultimate_Videos'               => __( 'Video', 'ultimate_vc' ),
		'Ultimate_Ribbons'              => __( 'Ribbon', 'ultimate_vc' ),
		'Ultimate_Dual_colors'          => __( 'Dual Color Heading', 'ultimate_vc' ),
	);
	?>

<div class="wrap about-wrap bsf-page-wrapper ultimate-modules bend">
<div class="wrap-container">
	<div class="bend-heading-section ultimate-header">
	<h1><?php esc_html_e( 'Ultimate Addons Settings', 'ultimate_vc' ); ?></h1>
	<h3><?php esc_html_e( 'Ultimate Addons is designed in a very modular fashion so that most the features would be independent of each other. For any reason, should you wish to disable some features, you can do it very easily below.', 'ultimate_vc' ); ?></h3>
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
			<a href="<?php echo admin_url( 'admin.php?page=about-ultimate' . $author_extend ); // PHPCS:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped ?>" data-tab="about-ultimate" class="nav-tab"> <?php echo esc_html__( 'About', 'ultimate_vc' ); ?> </a>
			<a href="<?php echo admin_url( 'admin.php?page=ultimate-dashboard' . $author_extend ); // PHPCS:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped ?>" data-tab="ultimate-modules" class="nav-tab nav-tab-active"> <?php echo esc_html__( 'Elements', 'ultimate_vc' ); ?> </a>
			<a href="<?php echo admin_url( 'admin.php?page=ultimate-smoothscroll' . $author_extend ); // PHPCS:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped ?>" data-tab="css-settings" class="nav-tab"> <?php echo esc_html__( 'Smooth Scroll', 'ultimate_vc' ); ?> </a>
			<a href="<?php echo admin_url( 'admin.php?page=ultimate-scripts-and-styles' . $author_extend ); // PHPCS:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped ?>" data-tab="css-settings" class="nav-tab"> <?php echo esc_html__( 'Scripts and Styles', 'ultimate_vc' ); ?> </a>
			<?php if ( $author ) : ?>
				<a href="<?php echo admin_url( 'admin.php?page=ultimate-debug-settings' ); // PHPCS:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped ?>" data-tab="ultimate-debug" class="nav-tab"> Debug </a>
			<?php endif; ?>
		</h2>
	</div><!-- smile-settings-wrapper -->

	</hr>

	<div class="container ultimate-content">
		<div class="col-md-12">
			<div id="ultimate-modules" class="ult-tabs active-tab">
				<br/>
				<div>
					<input type="checkbox" id="ult-all-modules-toggle" data-all="<?php echo count( $modules ); ?>" value="checkall" /> <label for="ult-all-modules-toggle"><?php echo esc_html__( 'Enable/Disable All', 'ultimate_vc' ); ?></label>
				</div>
				<form method="post" id="ultimate_modules">
					<input type="hidden" name="security" value="<?php echo esc_attr( wp_create_nonce( 'ultimate-modules-setting' ) ); ?>" />
					<input type="hidden" name="action" value="update_ultimate_modules" />
					<table class="form-table">
						<tbody>
							<?php
								$i             = 1;
								$checked_items = 0;
							foreach ( $modules as $module => $label ) {
								$checked = '';
								if ( is_array( $ultimate_modules ) && ! empty( $ultimate_modules ) ) {
									if ( in_array( strtolower( $module ), $ultimate_modules ) ) {
										$checked = 'checked="checked"';
										$checked_items++;
									} else {
										$checked = '';
									}
								}
								if ( ( $i % 2 ) == 1 ) {
									?>
									<tr valign="top" style="border-bottom: 1px solid #ddd;">
									<?php } ?>
										<th scope="row"><?php echo esc_attr( $label ); ?></th>
										<td>
										<div class="onoffswitch">
											<input type="checkbox" <?php echo esc_attr( $checked ); ?> class="onoffswitch-checkbox" value="<?php echo esc_attr( strtolower( $module ) ); ?>" id="<?php echo esc_attr( strtolower( $module ) ); ?>" name="ultimate_modules[]" />

											<label class="onoffswitch-label" for="<?php echo esc_attr( strtolower( $module ) ); ?>">
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
									<?php if ( ( $i % 2 ) == 1 ) { ?>
									<!-- </tr> -->
									<?php } ?>
							<?php $i++; } ?>
							<tr valign="top" style="border-bottom: 1px solid #ddd;">
								<th scope="row"><?php echo esc_html__( 'Row backgrounds', 'ultimate_vc' ); ?></th>
								<td> <div class="onoffswitch">
								<input type="checkbox" <?php echo esc_attr( $checked_row ); ?> id="ultimate_row" value="enable" class="onoffswitch-checkbox" name="ultimate_row" />
									<label class="onoffswitch-label" for="ultimate_row">
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
								<th></th><td></td>
							</tr>
						</tbody>
					</table>
				</form>
				<p class="submit"><input type="submit" name="submit" id="submit-modules" class="button button-primary" value="<?php echo esc_html__( 'Save Changes', 'ultimate_vc' ); ?>"></p>
			</div> <!-- #ultimate-modules -->
		</div> <!--col-md-12 -->
	</div> <!-- ultimate-content -->
	</div> <!-- bend-content-wrap -->
</div> <!-- .wrap-container -->
</div> <!-- .bend -->

<script type="text/javascript">
var submit_btn = jQuery("#submit-modules");
submit_btn.bind('click',function(e){
	e.preventDefault();
	var data = jQuery("#ultimate_modules").serialize();
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
				jQuery('html,body').animate({ scrollTop: 0 }, 300);
			} else {
				jQuery("#msg").html('<div class="error"><p><?php echo esc_html__( 'No settings were updated.', 'ultimate_vc' ); ?></p></div>');
				jQuery('html,body').animate({ scrollTop: 0 }, 300);
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

	var checked_items = <?php echo esc_attr( $checked_items ); ?>;
	var all_modules = parseInt(jQuery('#ult-all-modules-toggle').data('all'));
	if(checked_items === all_modules) {
		jQuery('#ult-all-modules-toggle').attr('checked',true);
	}

	jQuery('#ult-all-modules-toggle').click(function(){
		var is_check = (jQuery(this).is(':checked')) ? true : false;
		jQuery('.onoffswitch').find('.onoffswitch-checkbox').attr('checked',is_check);
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
