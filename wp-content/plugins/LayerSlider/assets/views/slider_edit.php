<?php

	// Prevent direct file access
	defined( 'LS_ROOT_FILE' ) || exit;

	// Attempt to avoid memory limit issues
	@ini_set( 'memory_limit', '256M' );

	// Get the IF of the slider
	$id = (int) $_GET['id'];

	// Get slider
	$sliderItem = LS_Sliders::find($id);
	$slider = $sliderItem['data'];

	// Redirect back to the slider list if the slider cannot be found
	// or it is malformed or a group item.
	//
	// Using <script> tag since headers are already sent out at this point.
	if( empty( $slider ) || ! empty( $sliderItem['flag_group'] ) ){
		die('<script>window.location.href = "'.admin_url('admin.php?page=layerslider').'";</script>');
	}

	// Product activation
	$lsActivated = LS_Config::isActivatedSite();

	// Get screen options
	$lsScreenOptions = get_option('ls-screen-options', '0');
	$lsScreenOptions = ($lsScreenOptions == 0) ? array() : $lsScreenOptions;
	$lsScreenOptions = is_array($lsScreenOptions) ? $lsScreenOptions : unserialize($lsScreenOptions);

	// Defaults: tooltips
	if( ! isset($lsScreenOptions['showTooltips'])) {
		$lsScreenOptions['showTooltips'] = 'true';
	}

	// Deafults: keyboard shortcuts
	if( ! isset($lsScreenOptions['useKeyboardShortcuts'])) {
		$lsScreenOptions['useKeyboardShortcuts'] = 'true';
	}

	// Deafults: notify osd
	if( ! isset($lsScreenOptions['useNotifyOSD'])) {
		$lsScreenOptions['useNotifyOSD'] = 'true';
	}

	// Deafults: collapse sidebar
	if( ! isset($lsScreenOptions['collapseSidebar'])) {
		$lsScreenOptions['collapseSidebar'] = 'true';
	}

	// Deafults: hover sidebar on hover
	if( ! isset($lsScreenOptions['expandSidebarOnHover'])) {
		$lsScreenOptions['expandSidebarOnHover'] = 'true';
	}

	// Get phpQuery
	if( ! defined('LS_phpQuery') ) {
		libxml_use_internal_errors(true);
		include LS_ROOT_PATH.'/helpers/phpQuery.php';
	}

	// Get defaults
	include LS_ROOT_PATH . '/config/defaults.php';
	include LS_ROOT_PATH . '/helpers/admin.ui.tools.php';

	// Run filters
	if(has_filter('layerslider_override_defaults')) {
		$newDefaults = apply_filters('layerslider_override_defaults', $lsDefaults);
		if(!empty($newDefaults) && is_array($newDefaults)) {
			$lsDefaults = $newDefaults;
			unset($newDefaults);
		}
	}

	// Show tab
	$settingsTabClass = isset($_GET['showsettings']) ? 'active' : '';
	$slidesTabClass = !isset($_GET['showsettings']) ? 'active' : '';


	// Get google fonts
	$googleFonts = get_option('ls-google-fonts', array() );

	// Get post types
	$postTypes = LS_Posts::getPostTypes();
	$postCategories = get_categories();
	$postTags = get_tags();
	$postTaxonomies = get_taxonomies(array('_builtin' => false), 'objects');


	$uploadsDir 		= wp_get_upload_dir();
	$uploadsBaseDir 	= $uploadsDir['basedir'];
	$uploadsBaseURL 	= $uploadsDir['baseurl'];

	$pixieModuleHandle 	= 'pixie-2.0.8';
	$pixieModuleDir 	= $uploadsBaseDir.'/layerslider/modules/'.$pixieModuleHandle;
	$pixieModuleReqDL 	= ! file_exists( $pixieModuleDir ) || count( glob( "$pixieModuleDir/*" ) ) === 0;

?>
<div id="ls-screen-options" class="metabox-prefs hidden">
	<div id="screen-options-wrap" class="hidden">
		<form id="ls-screen-options-form" method="post">
			<?php wp_nonce_field('ls-save-screen-options'); ?>
			<h5><?php _e('General features', 'LayerSlider') ?></h5>
			<label>
				<input type="checkbox" name="showTooltips"<?php echo $lsScreenOptions['showTooltips'] == 'true' ? ' checked="checked"' : ''?>> <?php _e('Tooltips', 'LayerSlider') ?>
			</label>
			<label>
				<input type="checkbox" name="useKeyboardShortcuts"<?php echo $lsScreenOptions['useKeyboardShortcuts'] == 'true' ? ' checked="checked"' : ''?>> <?php _e('Keyboard Shortcuts', 'LayerSlider') ?>
			</label>
			<label>
				<input type="checkbox" name="useNotifyOSD"<?php echo $lsScreenOptions['useNotifyOSD'] == 'true' ? ' checked="checked"' : ''?>> <?php _e('On Screen Notifications', 'LayerSlider') ?>
			</label>

			<br><br>
			<h5><?php _e('Sidebar features', 'LayerSlider') ?></h5>
			<label>
				<input type="checkbox" name="collapseSidebar"<?php echo $lsScreenOptions['collapseSidebar'] == 'true' ? ' checked="checked"' : ''?>> <?php _e('Collapse Sidebar While Editing', 'LayerSlider') ?>
			</label>
			<label>
				<input type="checkbox" name="expandSidebarOnHover"<?php echo $lsScreenOptions['expandSidebarOnHover'] == 'true' ? ' checked="checked"' : ''?>> <?php _e('Expand Sidebar On Hover', 'LayerSlider') ?>
			</label>
		</form>
	</div>
	<div id="screen-options-link-wrap" class="hide-if-no-js screen-meta-toggle">
		<button type="button" id="show-settings-link" class="button show-settings" aria-controls="screen-options-wrap" aria-expanded="false"><?php _e('Screen Options', 'LayerSlider') ?></button>
	</div>
</div>

<!-- Load templates -->
<?php

include LS_ROOT_PATH . '/templates/tmpl-layer-item.php';
include LS_ROOT_PATH . '/templates/tmpl-static-layer-item.php';
include LS_ROOT_PATH . '/templates/tmpl-layer.php';
include LS_ROOT_PATH . '/templates/tmpl-preview-context-menu.php';
include LS_ROOT_PATH . '/templates/tmpl-transition-window.php';
include LS_ROOT_PATH . '/templates/tmpl-popup-presets-window.php';
include LS_ROOT_PATH . '/templates/tmpl-popup-example-slider.php';
include LS_ROOT_PATH . '/templates/tmpl-post-chooser.php';
include LS_ROOT_PATH . '/templates/tmpl-insert-icons-modal.php';
include LS_ROOT_PATH . '/templates/tmpl-insert-media-modal.php';
include LS_ROOT_PATH . '/templates/tmpl-button-presets.php';
include LS_ROOT_PATH . '/templates/tmpl-import-slide.php';
include LS_ROOT_PATH . '/templates/tmpl-import-layer.php';
include LS_ROOT_PATH . '/templates/tmpl-slide-tab.php';
include LS_ROOT_PATH . '/templates/tmpl-activation.php';
include LS_ROOT_PATH . '/templates/tmpl-downloading-module.php';

?>

<!-- Load slide template -->
<script type="text/html" id="ls-slide-template">
	<?php include LS_ROOT_PATH . '/templates/tmpl-slide.php'; ?>
</script>

<!-- Slider JSON data source -->
<?php

	if( ! isset($slider['properties']['status']) ) {
		$slider['properties']['status'] = true;
	}

	// COMPAT: If old and non-fullwidth slider
	if( ! isset($slider['properties']['slideBGSize']) && ! isset($slider['properties']['new']) ) {
		if( empty( $slider['properties']['forceresponsive'] ) ) {
			$slider['properties']['slideBGSize'] = 'auto';
		}
	}

	$slider['properties']['schedule_start'] = '';
	$slider['properties']['schedule_end'] = '';


	if( ! empty( $sliderItem['schedule_start'] ) ) {
		$dateTime = new DateTime('@'.$sliderItem['schedule_start']);
		$dateTime->setTimezone( ls_wp_timezone() );

		$slider['properties']['schedule_start'] = $dateTime->format('Y-m-d\TH:i:s');
	}

	if( ! empty( $sliderItem['schedule_end'] ) ) {
		$dateTime = new DateTime('@'.$sliderItem['schedule_end']);
		$dateTime->setTimezone( ls_wp_timezone() );

		$slider['properties']['schedule_end'] = $dateTime->format('Y-m-d\TH:i:s');;
	}

	// Get yourLogo
	if( ! empty($slider['properties']['yourlogoId']) ) {
		$slider['properties']['yourlogo'] = apply_filters('ls_get_image', $slider['properties']['yourlogoId'], $slider['properties']['yourlogo']);
		$slider['properties']['yourlogoThumb'] = apply_filters('ls_get_thumbnail', $slider['properties']['yourlogoId'], $slider['properties']['yourlogo']);
	}

	if( empty($slider['properties']['new']) && empty($slider['properties']['type']) ) {
		if( !empty($slider['properties']['forceresponsive']) ) {
			$slider['properties']['type'] = 'fullwidth';

			if( strpos($slider['properties']['width'], '%') !== false ) {

				if( ! empty($slider['properties']['responsiveunder']) ) {
					$slider['properties']['width'] = $slider['properties']['responsiveunder'];

				} elseif( ! empty($slider['properties']['sublayercontainer']) ) {
					$slider['properties']['width'] = $slider['properties']['sublayercontainer'];
				}
			}

		} elseif( empty($slider['properties']['responsive']) ) {
			$slider['properties']['type'] = 'fixedsize';
		} else {
			$slider['properties']['type'] = 'responsive';
		}
	}

	if( ! empty( $slider['properties']['width'] ) ) {
		if( strpos($slider['properties']['width'], '%') !== false ) {
			$slider['properties']['width'] = 1000;
		}
	}

	if( ! empty($slider['properties']['sublayercontainer']) ) {
		unset($slider['properties']['sublayercontainer']);
	}

	if( ! empty( $slider['properties']['width'] ) ) {
		$slider['properties']['width'] = (int) $slider['properties']['width'];
	}

	if( ! empty( $slider['properties']['width'] ) ) {
		$slider['properties']['height'] = (int) $slider['properties']['height'];
	}

	if( empty( $slider['properties']['pauseonhover'] ) ) {
		$slider['properties']['pauseonhover'] = 'enabled';
	}

	if( empty($slider['properties']['sliderVersion'] ) && empty($slider['properties']['circletimer'] ) ) {
		$slider['properties']['circletimer'] = false;
	}

	// Convert old checkbox values
	foreach($slider['properties'] as $optionKey => $optionValue) {
		switch($optionValue) {
			case 'on':
				$slider['properties'][$optionKey] = true;
				break;

			case 'off':
				$slider['properties'][$optionKey] = false;
				break;
		}
	}

	foreach($slider['layers'] as $slideKey => $slideVal) {

		// Make sure to each slide has a 'properties' object
		if( ! isset( $slideVal['properties'] ) ) {
			$slideVal['properties'] = array();
		}


		// Get slide background
		if( ! empty($slideVal['properties']['backgroundId']) ) {
			$slideVal['properties']['background'] = apply_filters('ls_get_image', $slideVal['properties']['backgroundId'], $slideVal['properties']['background']);
			$slideVal['properties']['backgroundThumb'] = apply_filters('ls_get_thumbnail', $slideVal['properties']['backgroundId'], $slideVal['properties']['background']);
		}

		// Get slide thumbnail
		if( ! empty($slideVal['properties']['thumbnailId']) ) {
			$slideVal['properties']['thumbnail'] = apply_filters('ls_get_image', $slideVal['properties']['thumbnailId'], $slideVal['properties']['thumbnail']);
			$slideVal['properties']['thumbnailThumb'] = apply_filters('ls_get_thumbnail', $slideVal['properties']['thumbnailId'], $slideVal['properties']['thumbnail']);
		}


		// v6.3.0: Improve compatibility with *really* old sliders
		if( ! empty( $slideVal['sublayers'] ) && is_array( $slideVal['sublayers'] ) ) {
			$slideVal['sublayers'] = array_values( $slideVal['sublayers'] );
		}


		$slider['layers'][$slideKey] = $slideVal;

		if(!empty($slideVal['sublayers']) && is_array($slideVal['sublayers'])) {

			// v6.0: Reverse layers list
			$slideVal['sublayers'] = array_reverse($slideVal['sublayers']);

			foreach($slideVal['sublayers'] as $layerKey => $layerVal) {

				if( ! empty($layerVal['imageId']) ) {
					$layerVal['image'] = apply_filters('ls_get_image', $layerVal['imageId'], $layerVal['image']);
					$layerVal['imageThumb'] = apply_filters('ls_get_thumbnail', $layerVal['imageId'], $layerVal['image']);
				}

				if( ! empty($layerVal['posterId']) ) {
					$layerVal['poster'] = apply_filters('ls_get_image', $layerVal['posterId'], $layerVal['poster']);
					$layerVal['posterThumb'] = apply_filters('ls_get_thumbnail', $layerVal['posterId'], $layerVal['poster']);
				}

				if( ! empty($layerVal['layerBackgroundId']) ) {
					$layerVal['layerBackground'] = apply_filters('ls_get_image', $layerVal['layerBackgroundId'], $layerVal['layerBackground']);
					$layerVal['layerBackgroundThumb'] = apply_filters('ls_get_thumbnail', $layerVal['layerBackgroundId'], $layerVal['layerBackground']);
				}

				// Ensure that magic quotes will not mess with JSON data
				if(function_exists('get_magic_quotes_gpc') && @get_magic_quotes_gpc()) {
					$layerVal['styles'] = stripslashes($layerVal['styles']);
					$layerVal['transition'] = stripslashes($layerVal['transition']);
				}

				// Parse embedded JSON data
				$layerVal['styles'] = !empty($layerVal['styles']) ? (object) json_decode(stripslashes($layerVal['styles']), true) : new stdClass;
				$layerVal['transition'] = !empty($layerVal['transition']) ? (object) json_decode(stripslashes($layerVal['transition']), true) : new stdClass;
				$layerVal['html'] = !empty($layerVal['html']) ? stripslashes($layerVal['html']) : '';

				// Add 'top', 'left' and 'wordwrap' to the styles object
				if(isset($layerVal['top'])) { $layerVal['styles']->top = $layerVal['top']; unset($layerVal['top']); }
				if(isset($layerVal['left'])) { $layerVal['styles']->left = $layerVal['left']; unset($layerVal['left']); }
				if(isset($layerVal['wordwrap'])) { $layerVal['styles']->wordwrap = $layerVal['wordwrap']; unset($layerVal['wordwrap']); }

				// v6.8.5: Introduced individual background properties for layers such as size, position, etc.
				// Thus we need to specify each property with its own unique key instead of the combined 'background'
				// to avoid potentially overriding previous settings.
				if( ! empty( $layerVal['styles']->background ) ) {
					$layerVal['styles']->{'background-color'} = $layerVal['styles']->background;
					unset( $layerVal['styles']->background );
				}

				if( ! empty( $layerVal['transition']->showuntil ) ) {

					$layerVal['transition']->startatout = 'transitioninend + '.$layerVal['transition']->showuntil;
					$layerVal['transition']->startatouttiming = 'transitioninend';
					$layerVal['transition']->startatoutvalue = $layerVal['transition']->showuntil;
					unset($layerVal['transition']->showuntil);
				}

				if( ! empty( $layerVal['transition']->parallaxlevel ) ) {
					$layerVal['transition']->parallax = true;
				}

				// Custom attributes
				$layerVal['innerAttributes'] = !empty($layerVal['innerAttributes']) ?  (object) $layerVal['innerAttributes'] : new stdClass;
				$layerVal['outerAttributes'] = !empty($layerVal['outerAttributes']) ?  (object) $layerVal['outerAttributes'] : new stdClass;


				// v6.5.6: Convert old checkbox media settings to the new
				// select based options.
				if( isset( $layerVal['transition']->controls ) ) {
					if( true === $layerVal['transition']->controls ) {
						$layerVal['transition']->controls = 'auto';
					} elseif( false === $layerVal['transition']->controls ) {
						$layerVal['transition']->controls = 'disabled';
					}
				}

				$slider['layers'][$slideKey]['sublayers'][$layerKey] = $layerVal;
			}
		} else {
			$slider['layers'][$slideKey]['sublayers'] = array();
		}
	}

	if( ! empty( $slider['callbacks'] ) ) {
		foreach( $slider['callbacks'] as $key => $callback ) {
			$slider['callbacks'][$key] = stripslashes($callback);
		}
	}

	// v6.6.8: Set slider type to responsive in case of Popup
	// on a non-activated site.
	if( ! $lsActivated && ! empty( $slider['properties']['type'] ) && $slider['properties']['type'] === 'popup' ) {
		$slider['properties']['type'] = 'responsive';
	}

	// Slider version
	$slider['properties']['sliderVersion'] = LS_PLUGIN_VERSION;
?>

<!-- Get slider data from DB -->
<script type="text/javascript">

	// Slider data
	window.lsSliderData = <?php echo json_encode($slider) ?>;

	// Plugin path
	var pluginPath = '<?php echo LS_ROOT_URL ?>/static/';
	var lsTrImgPath = '<?php echo LS_ROOT_URL ?>/static/admin/img/';

	// Screen options
	var lsScreenOptions = <?php echo json_encode($lsScreenOptions) ?>;

	var pixieModuleData = <?php echo json_encode( array(
		'handle' 	=> $pixieModuleHandle,
		'baseURL' 	=> $uploadsBaseURL.'/layerslider/modules/'.$pixieModuleHandle,
		'needsDL' 	=> $pixieModuleReqDL,
		'cssFile' 	=> 'styles.min.css',
		'jsFile' 	=> 'scripts.min.js'
	) ); ?>;

</script>



<form method="post" id="ls-slider-form" novalidate="novalidate" autocomplete="off">

	<input type="hidden" name="slider_id" value="<?php echo $id ?>">
	<input type="hidden" name="action" value="ls_save_slider">
	<?php wp_nonce_field('ls-save-slider-' . $id); ?>

	<div class="wrap">

		<!-- Title -->
		<h2 id="ls-page-title">
			<?php _e('Editing slider:', 'LayerSlider') ?>
			<?php $sliderName = !empty($slider['properties']['title']) ? $slider['properties']['title'] : 'Unnamed'; ?>
			<?php echo apply_filters('ls_slider_title', $sliderName, 30) ?>
			<a href="<?php echo admin_url( 'admin.php?page=layerslider' ) ?>" class="add-new-h2">
				<?php _e('&larr; Sliders', 'LayerSlider') ?>
			</a>
		</h2>

		<!-- Version number -->
		<?php include LS_ROOT_PATH . '/templates/tmpl-beta-feedback.php'; ?>

		<div class="ls-notify-osd">
			<span class="icon"></span>
			<span class="text"></span>
		</div>

		<!-- Main menu bar -->
		<div id="ls-main-nav-bar">
			<a href="#slider-settings" data-deeplink="slider-settings" class="settings <?php echo $settingsTabClass ?>">
				<i class="dashicons dashicons-admin-tools"></i>
				<?php _e('Slider Settings', 'LayerSlider') ?>
			</a>
			<a href="#" class="layers <?php echo $slidesTabClass ?>">
				<i class="dashicons dashicons-images-alt"></i>
				<?php _e('Slides', 'LayerSlider') ?>
			</a>
			<a href="#callbacks" data-deeplink="callbacks" class="callbacks">
				<i class="dashicons dashicons-redo"></i>
				<?php _e('Event Callbacks', 'LayerSlider') ?>
			</a>
			<a href="https://layerslider.kreaturamedia.com/help/" target="_blank" class="faq right unselectable">
				<i class="dashicons dashicons-sos"></i>
				<?php _e('FAQ', 'LayerSlider') ?>
			</a>
			<a href="https://layerslider.kreaturamedia.com/documentation/" target="_blank" class="support right unselectable">
				<i class="dashicons dashicons-editor-help"></i>
				<?php _e('Documentation', 'LayerSlider') ?>
			</a>
			<span class="right help"><?php _e('Need help? Try these:', 'LayerSlider') ?></span>
			<a href="#" class="clear unselectable"></a>
		</div>

	</div>

	<!-- Post options -->
	<?php include LS_ROOT_PATH . '/templates/tmpl-post-options.php'; ?>

	<!-- Pages -->
	<div id="ls-pages">

		<!-- Slider Settings -->
		<div class="ls-page ls-settings ls-slider-settings <?php echo $settingsTabClass ?>">
			<?php include LS_ROOT_PATH . '/templates/tmpl-slider-settings.php'; ?>
		</div>

		<!-- Slides -->
		<div class="ls-page <?php echo $slidesTabClass ?>">

			<!-- Slide tabs -->
			<div id="ls-slide-tabs" class="clearfix">
				<?php
					foreach($slider['layers'] as $key => $layer) :
					$active = empty($key) ? 'active' : '';
					$name = !empty($layer['properties']['title']) ? $layer['properties']['title'] : sprintf(__('Slide #%d', 'LayerSlider'), ($key+1));

					$bgImage = !empty($layer['properties']['background']) ? $layer['properties']['background'] : null;
					$bgImageId = !empty($layer['properties']['backgroundId']) ? $layer['properties']['backgroundId'] : null;

					$thumb = !empty($layer['properties']['thumbnail']) ? $layer['properties']['thumbnail'] : null;
					$thumbId = !empty($layer['properties']['thumbnailId']) ? $layer['properties']['thumbnailId'] : null;

					$image = ! empty( $thumb ) ? apply_filters('ls_get_image', $thumbId, $thumb, true) : apply_filters('ls_get_image', $bgImageId, $bgImage, true);
					$empty = (false !== strpos( $image, 'blank.gif')) ? 'empty' : '';

					$hidden = ! empty( $layer['properties']['skip'] ) ? 'skip' : '';
				?>
				<div class="ls-slide-tab <?php echo $active ?> <?php echo $hidden ?> <?php echo $empty ?>">
					<span class="ls-slide-counter"></span>
					<span class="ls-slide-hidden dashicons dashicons-hidden"></span>
					<span class="ls-slide-actions dashicons dashicons-arrow-down-alt2"></span>
					<div class="ls-slide-preview" style="background-image: url(<?php echo $image?>)">
						<span><?php _e('No Preview', 'LayerSlider') ?></span>
					</div>
					<div class="ls-slide-name">
						<input type="text" value="<?php echo htmlspecialchars($name) ?>" placeholder="<?php _e('Type slide name here', 'LayerSlider') ?>">
					</div>
					<ul class="ls-slide-actions-sheet ls-hidden">
						<li class="ls-slide-duplicate">
							<span>
								<i class="dashicons dashicons-admin-page"></i>
								<?php _e('Duplicate', 'LayerSlider') ?>
							</span>
						</li>
						<li class="ls-slide-visibility">
							<span>
								<i class="dashicons dashicons-hidden"></i>
								<?php _e('Hide', 'LayerSlider') ?>
							</span>
							<span>
								<i class="dashicons dashicons-visibility"></i>
								<?php _e('Unhide', 'LayerSlider') ?>
							</span>
						</li>
						<li class="ls-slide-remove">
							<span>
								<i class="dashicons dashicons-trash"></i>
								<?php _e('Remove', 'LayerSlider') ?>
							</span>
						</li>
					</ul>
				</div>
				<?php endforeach; ?>

				<div id="ls-add-slide" class="unsortable ls-slide-controls">
					<div>
						<i class="dashicons dashicons-plus"></i>
						<span><?php _e('Add New', 'LayerSlider') ?></span>
					</div>
				</div>
				<div id="ls-import-slide" class="unsortable ls-slide-controls">
					<div>
						<i class="dashicons dashicons-upload"></i>
						<span><?php _e('Import', 'LayerSlider') ?></span>
					</div>
				</div>
			</div>

			<!-- Slides -->
			<div id="ls-layers" class="clearfix">
				<?php include LS_ROOT_PATH . '/templates/tmpl-slide.php'; ?>
			</div>
		</div>

		<!-- Event Callbacks -->
		<div class="ls-page ls-callback-page">

			<div class="ls-notification-info">
				<i class="dashicons dashicons-info"> </i>
				<?php echo sprintf(__('Please read our %sonline documentation%s for more information about the API.', 'LayerSlider'), '<a href="https://layerslider.kreaturamedia.com/documentation/#layerslider-api" target="_blank">', '</a>') ?>
			</div>


			<div class="ls-callback-separator"><?php _e('Init Events', 'LayerSlider') ?></div>

			<div class="ls-box ls-callback-box">
				<h3 class="header">
					sliderWillLoad
					<figure><span>|</span> <?php _e('Fires before parsing user data and rendering the UI.', 'LayerSlider') ?></figure>
				</h3>
				<div>
					<textarea name="sliderWillLoad" cols="20" rows="5" class="ls-codemirror">function( event ) {

}</textarea>
				</div>
			</div>

			<div class="ls-box ls-callback-box">
				<h3 class="header">
					sliderDidLoad
					<figure><span>|</span> <?php _e('Fires when the slider is fully initialized and its DOM nodes become accessible.', 'LayerSlider') ?></figure>
				</h3>
				<div>
					<textarea name="sliderDidLoad" cols="20" rows="5" class="ls-codemirror">function( event, slider ) {

}</textarea>
				</div>
			</div>

			<div class="ls-callback-separator"><?php _e('Resize Events', 'LayerSlider') ?></div>


			<div class="ls-box ls-callback-box side">
				<h3 class="header">
					sliderWillResize
					<figure><span>|</span> <?php _e('Fires before the slider renders resize events.', 'LayerSlider') ?></figure>
				</h3>
				<div>
					<textarea name="sliderWillResize" cols="20" rows="5" class="ls-codemirror">function( event, slider ) {

}</textarea>
				</div>
			</div>

			<div class="ls-box ls-callback-box">
				<h3 class="header">
					sliderDidResize
					<figure><span>|</span> <?php _e('Fires after the slider has rendered resize events.', 'LayerSlider') ?></figure>
				</h3>
				<div>
					<textarea name="sliderDidResize" cols="20" rows="5" class="ls-codemirror">function( event, slider ) {

}</textarea>
				</div>
			</div>

			<div class="ls-callback-separator"><?php _e('Slideshow Events', 'LayerSlider') ?></div>


			<div class="ls-box ls-callback-box">
				<h3 class="header">
					slideshowStateDidChange
					<figure><span>|</span> <?php _e('Fires upon every slideshow state change, which may not influence the playing status.', 'LayerSlider') ?></figure>
				</h3>
				<div>
					<textarea name="slideshowStateDidChange" cols="20" rows="5" class="ls-codemirror">function( event, slider ) {

}</textarea>
				</div>
			</div>

			<div class="ls-box ls-callback-box">
				<h3 class="header">
					slideshowDidPause
					<figure><span>|</span> <?php _e('Fires when the slideshow pauses from playing status.', 'LayerSlider') ?></figure>
				</h3>
				<div>
					<textarea name="slideshowDidPause" cols="20" rows="5" class="ls-codemirror">function( event, slider ) {

}</textarea>
				</div>
			</div>

			<div class="ls-box ls-callback-box side">
				<h3 class="header">
					slideshowDidResume
					<figure><span>|</span> <?php _e('Fires when the slideshow resumes from paused status.', 'LayerSlider') ?></figure>
				</h3>
				<div>
					<textarea name="slideshowDidResume" cols="20" rows="5" class="ls-codemirror">function( event, slider ) {

}</textarea>
				</div>
			</div>


			<div class="ls-callback-separator"><?php _e('Slide Change Events', 'LayerSlider') ?></div>


			<div class="ls-box ls-callback-box">
				<h3 class="header">
					slideChangeWillStart
					<figure><span>|</span> <?php _e('Signals when the slider wants to change slides, and is your last chance to divert it or intervene in any way.', 'LayerSlider') ?></figure>
				</h3>
				<div>
					<textarea name="slideChangeWillStart" cols="20" rows="5" class="ls-codemirror">function( event, slider ) {

}</textarea>
				</div>
			</div>

			<div class="ls-box ls-callback-box">
				<h3 class="header">
					slideChangeDidStart
					<figure><span>|</span> <?php _e('Fires when the slider has started a slide change.', 'LayerSlider') ?></figure>
				</h3>
				<div>
					<textarea name="slideChangeDidStart" cols="20" rows="5" class="ls-codemirror">function( event, slider ) {

}</textarea>
				</div>
			</div>

			<div class="ls-box ls-callback-box">
				<h3 class="header">
					slideChangeWillComplete
					<figure><span>|</span> <?php _e('Fires before completing a slide change.', 'LayerSlider') ?></figure>
				</h3>
				<div>
					<textarea name="slideChangeWillComplete" cols="20" rows="5" class="ls-codemirror">function( event, slider ) {

}</textarea>
				</div>
			</div>

			<div class="ls-box ls-callback-box">
				<h3 class="header">
					slideChangeDidComplete
					<figure><span>|</span> <?php _e('Fires after a slide change has completed and the slide indexes have been updated. ', 'LayerSlider') ?></figure>
				</h3>
				<div>
					<textarea name="slideChangeDidComplete" cols="20" rows="5" class="ls-codemirror">function( event, slider ) {

}</textarea>
				</div>
			</div>


			<div class="ls-callback-separator"><?php _e('Slide Timeline Events', 'LayerSlider') ?></div>

			<div class="ls-box ls-callback-box">
				<h3 class="header">
					slideTimelineDidCreate
					<figure><span>|</span> <?php _e('Fires when the current slide’s animation timeline (e.g. your layers) becomes accessible for interfacing.', 'LayerSlider') ?></figure>
				</h3>
				<div>
					<textarea name="slideTimelineDidCreate" cols="20" rows="5" class="ls-codemirror">function( event, slider ) {

}</textarea>
				</div>
			</div>


			<div class="ls-box ls-callback-box">
				<h3 class="header">
					slideTimelineDidUpdate
					<figure><span>|</span> <?php _e('Fires rapidly (at each frame) throughout the entire slide while playing, including reverse playback.', 'LayerSlider') ?></figure>
				</h3>
				<div>
					<textarea name="slideTimelineDidUpdate" cols="20" rows="5" class="ls-codemirror">function( event, timeline ) {

}</textarea>
				</div>
			</div>


			<div class="ls-box ls-callback-box">
				<h3 class="header">
					slideTimelineDidStart
					<figure><span>|</span> <?php _e('Fires when the current slide’s animation timeline (e.g. your layers) has started playing.', 'LayerSlider') ?></figure>
				</h3>
				<div>
					<textarea name="slideTimelineDidStart" cols="20" rows="5" class="ls-codemirror">function( event, slider ) {

}</textarea>
				</div>
			</div>

			<div class="ls-box ls-callback-box">
				<h3 class="header">
					slideTimelineDidComplete
					<figure><span>|</span> <?php _e('Fires when the current slide’s animation timeline (e.g. layer transitions) has completed.', 'LayerSlider') ?></figure>
				</h3>
				<div>
					<textarea name="slideTimelineDidComplete" cols="20" rows="5" class="ls-codemirror">function( event, slider ) {

}</textarea>
				</div>
			</div>

			<div class="ls-box ls-callback-box">
				<h3 class="header">
					slideTimelineDidReverseComplete
					<figure><span>|</span> <?php _e('Fires when all reversed animations have reached the beginning of the current slide.', 'LayerSlider') ?></figure>
				</h3>
				<div>
					<textarea name="slideTimelineDidReverseComplete" cols="20" rows="5" class="ls-codemirror">function( event, slider ) {

}</textarea>
				</div>
			</div>

			<div class="ls-callback-separator"><?php _e('Media Events', 'LayerSlider') ?></div>

			<div class="ls-box ls-callback-box">
				<h3 class="header">
					mediaDidStart
					<figure><span>|</span> <?php _e('A media element on the current slide has started playback.', 'LayerSlider') ?></figure>
				</h3>
				<div>
					<textarea name="mediaDidStart" cols="20" rows="5" class="ls-codemirror">function( event, slider ) {

}</textarea>
				</div>
			</div>

			<div class="ls-box ls-callback-box">
				<h3 class="header">
					mediaDidStop
					<figure><span>|</span> <?php _e('A media element on the current slide has stopped playback.', 'LayerSlider') ?></figure>
				</h3>
				<div>
					<textarea name="mediaDidStop" cols="20" rows="5" class="ls-codemirror">function( event, slider ) {

}</textarea>
				</div>
			</div>


			<div class="ls-callback-separator"><?php _e('Popup Events', 'LayerSlider') ?></div>

			<div class="ls-box ls-callback-box">
				<h3 class="header">
					popupWillOpen
					<figure><span>|</span> <?php _e('Fires when the Popup starts its opening transition and becomes visible.', 'LayerSlider') ?></figure>
				</h3>
				<div>
					<textarea name="popupWillOpen" cols="20" rows="5" class="ls-codemirror">function( event, slider ) {

}</textarea>
				</div>
			</div>

			<div class="ls-box ls-callback-box">
				<h3 class="header">
					popupDidOpen
					<figure><span>|</span> <?php _e('Fires when the Popup completed its opening transition.', 'LayerSlider') ?></figure>
				</h3>
				<div>
					<textarea name="popupDidOpen" cols="20" rows="5" class="ls-codemirror">function( event, slider ) {

}</textarea>
				</div>
			</div>

			<div class="ls-box ls-callback-box">
				<h3 class="header">
					popupWillClose
					<figure><span>|</span> <?php _e('Fires when the Popup stars its closing transition.', 'LayerSlider') ?></figure>
				</h3>
				<div>
					<textarea name="popupWillClose" cols="20" rows="5" class="ls-codemirror">function( event, slider ) {

}</textarea>
				</div>
			</div>

			<div class="ls-box ls-callback-box">
				<h3 class="header">
					popupDidClose
					<figure><span>|</span> <?php _e('Fires when the Popup completed its closing transition and became hidden.', 'LayerSlider') ?></figure>
				</h3>
				<div>
					<textarea name="popupDidClose" cols="20" rows="5" class="ls-codemirror">function( event, slider ) {

}</textarea>
				</div>
			</div>


			<div class="ls-callback-separator"><?php _e('Destroy Events', 'LayerSlider') ?></div>


			<div class="ls-box ls-callback-box">
				<h3 class="header">
					sliderDidDestroy
					<figure><span>|</span> <?php _e('Fires when the slider destructor has finished and it is safe to remove the slider from the DOM.', 'LayerSlider') ?></figure>
				</h3>
				<div>
					<textarea name="sliderDidDestroy" data-event-data="false" cols="20" rows="5" class="ls-codemirror">function( event ) {

}</textarea>
				</div>
			</div>

			<div class="ls-box ls-callback-box">
				<h3 class="header">
					sliderDidRemove
					<figure><span>|</span> <?php _e('Fires when the slider has been removed from the DOM when using the <i>destroy</i> API method.', 'LayerSlider') ?></figure>
				</h3>
				<div>
					<textarea name="sliderDidRemove" data-event-data="false" cols="20" rows="5" class="ls-codemirror">function( event ) {

}</textarea>
				</div>
			</div>

		</div>
	</div>

	<div class="ls-publish">
		<button type="submit" class="button button-primary button-hero"><?php _e('Save changes', 'LayerSlider') ?></button>
		<div class="ls-save-shortcode">

			<?php
				$revisions = LS_Revisions::count( $id );
				if( $revisions > 1 ) : ?>
				<p class="revisions"><span><i class="dashicons dashicons-backup"></i><?php echo sprintf(__('Revisions Available:', 'LayerSlider'), $revisions) ?></span><br><a href="<?php echo admin_url('admin.php?page=layerslider-addons&section=revisions&id='.$id) ?>"><?php echo sprintf(__('Browse %d Revisions', 'LayerSlider'), $revisions) ?></a></p>
			<?php endif ?>

			<p><span><?php _e('Use shortcode:', 'LayerSlider') ?></span><br><span>[layerslider id="<?php echo !empty($slider['properties']['slug']) ? $slider['properties']['slug'] : $id ?>"]</span></p>
			<p><span><?php _e('Use PHP function:', 'LayerSlider') ?></span><br><span>&lt;?php layerslider(<?php echo !empty($slider['properties']['slug']) ? "'{$slider['properties']['slug']}'" : $id ?>) ?&gt;</span></p>
		</div>
	</div>
</form>