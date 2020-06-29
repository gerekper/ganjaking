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

	wp_enqueue_script('layerslider-admin');

	$revisions = LS_Revisions::snapshots( $id );
	$revisionsCount = count($revisions);

	foreach( $revisions as $key => $revision ) {

		$revisions[$key]->avatar 	= function_exists( 'get_avatar_url' ) ? get_avatar_url( $revisions[$key]->author ) : '';
		$revisions[$key]->nickname 	= get_userdata( $revisions[$key]->author )->user_nicename;
		$revisions[$key]->time_diff =  sprintf(__(' %s ago', 'LayerSlider'), human_time_diff( $revision->date_c ) );
		$revisions[$key]->created 	= ls_date('M j @ H:i', $revision->date_c);

		$slider = json_decode( $revision->data , true);


		// Get yourLogo
		if( ! empty($slider['properties']['yourlogoId']) ) {
			$slider['properties']['yourlogo'] = apply_filters('ls_get_image', $slider['properties']['yourlogoId'], $slider['properties']['yourlogo']);
			$slider['properties']['yourlogoThumb'] = apply_filters('ls_get_thumbnail', $slider['properties']['yourlogoId'], $slider['properties']['yourlogo']);
		}


		if( ! empty( $slider['properties']['width'] ) ) {
			if( strpos($slider['properties']['width'], '%') !== false ) {
				$slider['properties']['width'] = 1000;
			}
		}

		if( ! empty( $slider['properties']['width'] ) ) {
			$slider['properties']['width'] = (int) $slider['properties']['width'];
		}

		if( ! empty( $slider['properties']['width'] ) ) {
			$slider['properties']['height'] = (int) $slider['properties']['height'];
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

					// Custom attributes
					$layerVal['innerAttributes'] = !empty($layerVal['innerAttributes']) ?  (object) $layerVal['innerAttributes'] : new stdClass;
					$layerVal['outerAttributes'] = !empty($layerVal['outerAttributes']) ?  (object) $layerVal['outerAttributes'] : new stdClass;

					$slider['layers'][$slideKey]['sublayers'][$layerKey] = $layerVal;
				}
			} else {
				$slider['layers'][$slideKey]['sublayers'] = array();
			}
		}

		if( ! empty( $slider['callbacks'] ) ) {
			foreach( $slider['callbacks'] as $CBkey => $callback ) {
				$slider['callbacks'][$CBkey] = stripslashes($callback);
			}
		}

		$revisions[$key]->data = $slider;
	}
?>

<!-- Get slider data from DB -->
<script type="text/javascript">

	// Revisions
	window.lsRevisions = <?php echo json_encode($revisions); ?>;

	// Slider data
	window.lsSliderData = <?php echo json_encode($slider) ?>;

	window.lsPostsJSON = [];

	// Plugin path
	var pluginPath = '<?php echo LS_ROOT_URL ?>/static/';
	var lsTrImgPath = '<?php echo LS_ROOT_URL ?>/static/admin/img/';

</script>

<?php include LS_ROOT_PATH . '/templates/tmpl-slide-tab.php'; ?>

<div id="ls-revisions">

	<div class="wrap">
		<h2>
			<?php _e('Revisions for Slider:', 'LayerSlider') ?>
			<?php $sliderName = !empty($slider['properties']['title']) ? $slider['properties']['title'] : 'Unnamed'; ?>
			<?php echo apply_filters('ls_slider_title', $sliderName, 30) ?>
			<a href="<?php echo admin_url('admin.php?page=layerslider&action=edit&id='.$id) ?>" class="add-new-h2"><?php _e('&larr; Back to Slider', 'LayerSlider') ?></a>
		</h2>
		<form method="post" id="ls-revisions-form">
			<?php wp_nonce_field('ls-revert-slider-' . $id); ?>
			<input type="hidden" name="ls-revert-slider" value="1">
			<input type="hidden" name="slider-id" value="<?php echo $id ?>">
			<input type="hidden" id="revision-id" name="revision-id" value="<?php echo $revision->id ?>">
			<span class="ls-revisions-oldest"><?php echo ls_date('M j, Y', $revisions[0]->date_c) ?></span>
			<span class="ls-revisions-now"><?php _e('Now') ?></span>

			<div id="ls-revisions-selected">
				<table>
					<tr>
						<td class="avatar" rowspan="2">
							<img src="<?php echo $revision->avatar ?>">
						</td>
						<td>
							<?php echo sprintf(__('Selected Revision by %s', 'LayerSlider'), '<strong class="author">'.$revision->nickname.'</strong>')  ?>

						</td>
					</tr>
					<tr>
						<td>
							<span class="time-diff"><?php echo $revision->time_diff ?></span>
							( <span class="date"><?php echo $revision->created ?></span> )
						</td>
					</tr>
				</table>
				<button class="button button-primary button-hero"><?php _e('Revert to This Revision', 'LayerSlider') ?></button>
			</div>

			<input type="range" id="ls-revisions-range" min="1" max="<?php echo $revisionsCount ?>" value="<?php echo $revisionsCount ?>" name="revision" list="ls-revisions-timeline">
			<datalist id="ls-revisions-timeline">
				<?php for($c = 1; $c <= $revisionsCount; $c++) : ?>
				<option><?php echo $c ?></option>
				<?php endfor ?>
			</datalist>
		</form>

		<div class="ls-notification-info">
			<i class="dashicons dashicons-info"></i>
			<?php _e('Reverting a slider to an earlier version adds another snapshot to Revisions, which can also be reverted if you change your mind and would rather return to the original copy.', 'LayerSlider') ?>
			<?php _e('Slider Revisions also saves the undo/redo controls. Even if there is no perfect snapshot, you will be able to undo the changes in-between to find what you are looking for.', 'LayerSlider') ?>
		</div>

		<h2 class="ls-revisions-h2"><?php _e('Preview for Selected Revision', 'LayerSlider') ?></h2>
		<div id="ls-slider-form">

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


				?>
				<div class="ls-slide-tab <?php echo $active ?>">
					<span class="ls-slide-counter"></span>
					<div class="ls-slide-preview" style="background-image: url(<?php echo $image?>)"></div>
					<div class="ls-slide-name">
						<input type="text" disabled value="<?php echo htmlspecialchars($name) ?>">
					</div>
				</div>
				<?php endforeach; ?>
			</div>

			<!-- Slides -->
			<div id="ls-layers" class="clearfix">
				<div class="ls-box ls-layer-box active">
					<table id="ls-preview-table">
						<tbody>
							<tr id="slider-editor-toolbar">
								<td >
									<div class="ls-editor-zoom">
										<!-- <span class="dashicons dashicons-editor-expand ls-layers-icon"></span> -->
										<div class="ls-editor-slider" ></div>
										<span class="ls-editor-slider-val">100%</span>
										|
				    					<?php _e('Auto-Fit', 'LayerSlider') ?>
										<input id="zoom-fit" class="ls-checkbox checkbox small" type="checkbox" checked>
									</div>


				 					<div class="ls-editor-preview">
										<button type="button" class="button ls-preview-button"><?php _e('Preview Slide', 'LayerSlider') ?></button>
									</div>


									<div class="ls-editor-layouts">
										<button data-type="desktop" class="button dashicons dashicons-desktop playing" data-help="<?php _e('Show layers that are visible on desktop.', 'LayerSlider') ?>"></button><!--
									--><button data-type="tablet" class="button dashicons dashicons-tablet" data-help="<?php _e('Show layers that are visible on tablets.', 'LayerSlider') ?>"></button><!--
									--><button data-type="phone"  class="button dashicons dashicons-smartphone" data-help="<?php _e('Show layers that are visible on mobile phones.', 'LayerSlider') ?>"></button>
									</div>
								</td>
							</tr>
						</tbody>
					</table>
					<div class="ls-preview-td">
						<div class="ls-preview-wrapper ls-preview-size" data-dragover="<?php _e('Drop image(s) here', 'LayerSlider') ?>">
							<div class="ls-preview ls-preview-size">
								<div id="ls-preview-layers" class="draggable ls-layer ls-preview-transform">
									<div id="ls-static-preview" class="disabled"></div>
								</div>
							</div>
							<div class="ls-real-time-preview ls-preview-size"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>