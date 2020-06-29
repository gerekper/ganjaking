<?php

	// Prevent direct file access
	defined( 'LS_ROOT_FILE' ) || exit;


	// Get screen options
	$lsScreenOptions = get_option('ls-screen-options', '0');
	$lsScreenOptions = ($lsScreenOptions == 0) ? array() : $lsScreenOptions;
	$lsScreenOptions = is_array($lsScreenOptions) ? $lsScreenOptions : unserialize($lsScreenOptions);

	// Defaults
	if(!isset($lsScreenOptions['showTooltips'])) { $lsScreenOptions['showTooltips'] = 'true'; }
	if(!isset($lsScreenOptions['numberOfSliders'])) { $lsScreenOptions['numberOfSliders'] = '25'; }

	// Get current page
	$curPage = (!empty($_GET['paged']) && is_numeric($_GET['paged'])) ? (int) $_GET['paged'] : 1;
	// $curPage = ($curPage >= $maxPage) ? $maxPage : $curPage;

	$layout = get_user_meta( get_current_user_id(), 'ls-sliders-layout', true );


	// NOTE: LayerSlider's grouping feature works only on WordPress 4.4 or newer. Older versions are automatically forced to use the list view since the slider groups feature isn't implemented there.
	if( version_compare( get_bloginfo('version'), '4.4', '<') ) {
		$layout = 'list';
	}

	// Set filters
	$userFilters 	= false;
	$showPopupSlider= false;
	$showAllSlider 	= false;

	$urlParamFilter = 'published';
	$urlParamOrder 	= 'date_c';
	$urlParamTerm 	= '';

	$filters = array(
		'orderby' => 'date_c',
		'order' => 'DESC',
		'page' => $curPage,
		'limit' => (int) $lsScreenOptions['numberOfSliders']
	);

	if( $layout !== 'list' ) {
		$filters['groups'] = true;
	}

	if( ! empty($_GET['filter']) && $_GET['filter'] === 'all' ) {
		$userFilters = true;
		$showAllSlider = true;
		$urlParamFilter = htmlentities($_GET['filter']);
		$filters['exclude'] = array();
	}

	if( ! empty($_GET['filter']) && $_GET['filter'] === 'popup') {
		$userFilters = true;
		$showPopupSlider = true;
		$urlParamFilter = htmlentities($_GET['filter']);
		$filters['exclude'] = array();
		$filters['where'] = "flag_popup = '1'";
	}

	if( ! empty($_GET['order']) ) {
		$userFilters = true;
		$urlParamOrder = $_GET['order'];
		$filters['orderby'] = htmlentities($_GET['order']);

		if( $_GET['order'] === 'name' ) {
			$filters['order'] = 'ASC';
		}
	}

	if( ! empty($_GET['term']) ) {
		$userFilters = true;
		$urlParamTerm = htmlentities($_GET['term']);
		$filters['where'] = "name LIKE '%".esc_sql($_GET['term'])."%' OR slug LIKE '%".esc_sql($_GET['term'])."%'";
	}

	// Find sliders
	$sliders = LS_Sliders::find($filters);

	// Pager
	$maxItem = LS_Sliders::$count;
	$maxPage = ceil($maxItem / (int) $lsScreenOptions['numberOfSliders']);
	$maxPage = $maxPage ? $maxPage : 1;



	// Site activation
	$code 		= get_option('layerslider-purchase-code', '');
	$validity 	= LS_Config::isActivatedSite();
	$channel 	= get_option('layerslider-release-channel', 'stable');


	// License key
	$codeFormatted = '';
	if(!empty($code)) {
		$start = substr($code, 0, -6);
		$end = substr($code, -6);
		$codeFormatted = preg_replace("/[a-zA-Z0-9]/", '●', $start) . $end;
		$codeFormatted = str_replace('-', ' ', $codeFormatted);
	}


	// Template store data
	$lsStoreUpdate 		= get_option('ls-store-last-updated', 0);
	$lsStoreData 		= get_option('ls-store-data', false);
	$lsStoreInterval 	= ! empty($lsStoreData) ? DAY_IN_SECONDS : HOUR_IN_SECONDS;
	$lsStoreLastViewed 	= get_user_meta( get_current_user_id(), 'ls-store-last-viewed', true);

	// Update last visited date
	if( empty( $lsStoreLastViewed ) ) {
		$lsStoreLastViewed = time();
		update_user_meta(get_current_user_id(), 'ls-store-last-viewed', date('Y-m-d'));
	}

	// Update store data
	if( $lsStoreUpdate < time() - $lsStoreInterval ) {

		// Refresh update time
		update_option('ls-store-last-updated', time());
		$lsStoreUpdate = time();

		// Set update data
		$data = wp_remote_retrieve_body(wp_remote_get(sprintf('%ssliders/', LS_REPO_BASE_URL, LS_MARKETPLACE_ID)));
		$lsStoreData = ! empty($data) ? json_decode($data, true) : array();
		update_option('ls-store-data', $lsStoreData, false);
	}

	$lsStoreHasUpdate = ( ! empty($lsStoreData['last_updated']) && $lsStoreLastViewed <  $lsStoreData['last_updated'] );

	$importSliderCount = ! empty( $_GET['sliderCount'] ) ? (int)$_GET['sliderCount'] : 0;

	// Notification messages
	$notifications = array(

		'updateStore' => __('Successfully updated the Template Store library.', 'LayerSlider'),

		'removeSelectError' => __('No sliders were selected to remove.', 'LayerSlider'),
		'removeSuccess' => __('The selected sliders were removed.', 'LayerSlider'),

		'duplicateSuccess' => __('The selected sliders were duplicated.', 'LayerSlider'),

		'deleteSelectError' => __('No sliders were selected.', 'LayerSlider'),
		'deleteSuccess' => __('The selected sliders were permanently deleted.', 'LayerSlider'),
		'groupSuccess' => __('A new group has been created from the selected items.', 'LayerSlider'),
		'groupSelectError' => __('You need to select at least 2 sliders to group them.', 'LayerSlider'),
		'mergeSelectError' => __('You need to select at least 2 sliders to merge them.', 'LayerSlider'),
		'mergeSuccess' => __('The selected items were merged together as a new slider.', 'LayerSlider'),
		'restoreSelectError' => __('No sliders were selected.', 'LayerSlider'),
		'restoreSuccess' => __('The selected sliders were restored.', 'LayerSlider'),

		'exportNotFound' => __('No sliders were found to export.', 'LayerSlider'),
		'exportSelectError' => __('No sliders were selected to export.', 'LayerSlider'),
		'exportZipError' => __('The PHP ZipArchive extension is required to import .zip files.', 'LayerSlider'),

		'importSelectError' => __('Choose a file to import sliders.', 'LayerSlider'),
		'importFailed' => __('The import file seems to be invalid or corrupted.', 'LayerSlider'),
		'importSuccess' => sprintf( _n( '%d slider has been successfully imported.', '%d sliders have been successfully imported.', $importSliderCount, 'LayerSlider' ), $importSliderCount ),

		'generalUpdated' => __('Your settings has been updated.', 'LayerSlider')
	);
?>

<script type="text/javascript">
	window.lsSiteActivation = <?php echo ! empty( $validity ) ? 'true' : 'false' ?>;
</script>

<div id="ls-guides" class="metabox-prefs hidden">
	<div id="ls-guides-wrap" class="hidden">
		<h5><?php _e('Interactive guides coming soon!', 'LayerSlider') ?></h5>
		<p><?php _e('Interactive step-by-step tutorial guides will shortly arrive to help you get started using LayerSlider.', 'LayerSlider') ?></p>
	</div>
	<div id="show-guides-link-wrap" class="hide-if-no-js screen-meta-toggle">
		<button type="button" id="show-guides-link" class="button show-settings" aria-controls="ls-guides-wrap" aria-expanded="false"><?php _e('Guides', 'LayerSlider') ?></button>
	</div>
</div>

<div id="ls-screen-options" class="metabox-prefs hidden">
	<div id="screen-options-wrap" class="hidden">
		<form id="ls-screen-options-form" method="post" novalidate>
			<?php wp_nonce_field('ls-save-screen-options'); ?>
			<h5><?php _e('Show on screen', 'LayerSlider') ?></h5>
			<label><input type="checkbox" name="showTooltips"<?php echo $lsScreenOptions['showTooltips'] == 'true' ? ' checked="checked"' : ''?>> <?php _e('Tooltips', 'LayerSlider') ?></label><br><br>

			<?php _e('Show me', 'LayerSlider') ?> <input type="number" name="numberOfSliders" min="8" step="4" value="<?php echo (int) $lsScreenOptions['numberOfSliders'] ?>"> <?php _e('sliders per page', 'LayerSlider') ?>
			<button class="button"><?php _e('Apply', 'LayerSlider') ?></button>
		</form>
	</div>
	<div id="screen-options-link-wrap" class="hide-if-no-js screen-meta-toggle">
		<button type="button" id="show-settings-link" class="button show-settings" aria-controls="screen-options-wrap" aria-expanded="false"><?php _e('Screen Options', 'LayerSlider') ?></button>
	</div>
</div>


<!-- WP hack to place notification at the top of page -->
<div class="wrap ls-wrap ls-wp-hack">
	<h2></h2>

	<!-- Error messages -->
	<?php if(isset($_GET['message'])) : ?>
	<div class="ls-notification large <?php echo isset($_GET['error']) ? 'error' : 'updated' ?>">
		<div><?php echo $notifications[ $_GET['message'] ] ?></div>
	</div>
	<?php endif; ?>
	<!-- End of error messages -->
</div>

<div class="wrap ls-wrap" id="ls-list-page">
	<h2><?php _e('Your Sliders', 'LayerSlider') ?></h2>

	<!-- Beta version -->
	<?php include LS_ROOT_PATH . '/templates/tmpl-beta-feedback.php'; ?>

	<!-- Add slider template -->
	<?php include LS_ROOT_PATH . '/templates/tmpl-add-slider-list.php'; ?>
	<?php include LS_ROOT_PATH . '/templates/tmpl-add-slider-grid.php'; ?>
	<?php include LS_ROOT_PATH . '/templates/tmpl-slider-group-item.php'; ?>
	<?php include LS_ROOT_PATH . '/templates/tmpl-slider-group-placeholder.php'; ?>
	<?php include LS_ROOT_PATH . '/templates/tmpl-slider-group-remove-area.php'; ?>

	<!-- Import sample sliders template -->
	<?php include LS_ROOT_PATH . '/templates/tmpl-import-templates.php'; ?>

	<!-- Importing template -->
	<?php include LS_ROOT_PATH . '/templates/tmpl-importing.php'; ?>

	<!-- Import sample sliders template -->
	<?php include LS_ROOT_PATH . '/templates/tmpl-upload-sliders.php'; ?>
	<?php include LS_ROOT_PATH . '/templates/tmpl-quick-import.php'; ?>


	<!-- Activation required -->
	<?php include LS_ROOT_PATH . '/templates/tmpl-activation.php'; ?>
	<?php include LS_ROOT_PATH . '/templates/tmpl-activation-unavailable.php'; ?>
	<?php include LS_ROOT_PATH . '/templates/tmpl-purchase-ww-popups.php'; ?>


	<!-- Embed slider template -->
	<?php include LS_ROOT_PATH . '/templates/tmpl-embed-slider.php'; ?>



	<!-- Slider Filters -->
	<form method="get" id="ls-slider-filters">
		<input type="hidden" name="page" value="layerslider">

		<?php
		// NOTE: LayerSlider's grouping feature works only on WordPress 4.4 or newer. Older versions are automatically forced to use the list view since the slider groups feature isn't implemented there.
		if( version_compare( get_bloginfo('version'), '4.4', '>=') ) : ?>
		<div class="layout">
			<a href="<?php echo admin_url('admin.php?page=layerslider&action=layout&type=list') ?>" data-help="<?php _e('List View', 'LayerSlider') ?>" class="dashicons dashicons-list-view"></a>
			<a href="<?php echo admin_url('admin.php?page=layerslider&action=layout&type=grid') ?>" data-help="<?php _e('Grid View', 'LayerSlider') ?>" class="dashicons dashicons-grid-view"></a>
		</div>
		<?php endif ?>

		<div class="filter">
			<?php _e('Show', 'LayerSlider') ?>
			<select name="filter">
				<option value="published"><?php _e('published', 'LayerSlider') ?></option>
				<option value="popup" <?php echo $showPopupSlider ? 'selected' : '' ?>><?php _e('popup') ?></option>
				<option value="all" <?php echo $showAllSlider ? 'selected' : '' ?>><?php _e('all', 'LayerSlider') ?></option>
			</select>
			<?php _e('sliders', 'LayerSlider') ?>
		</div>
		<div class="sort">
			<?php _e('Sort by', 'LayerSlider') ?>
			<select name="order">
				<option value="name" <?php echo ($filters['orderby'] === 'name') ? 'selected' : '' ?>><?php _e('name', 'LayerSlider') ?></option>
				<option value="date_c" <?php echo ($filters['orderby'] === 'date_c') ? 'selected' : '' ?>><?php _e('date created', 'LayerSlider') ?></option>
				<option value="date_m" <?php echo ($filters['orderby'] === 'date_m') ? 'selected' : '' ?>><?php _e('date modified', 'LayerSlider') ?></option>
				<option value="schedule_start" <?php echo ($filters['orderby'] === 'schedule_start') ? 'selected' : '' ?>><?php _e('date scheduled', 'LayerSlider') ?></option>
			</select>
		</div>

		<div class="right">
			<input type="search" name="term" placeholder="<?php _e('Filter by name', 'LayerSlider') ?>" value="<?php echo ! empty($_GET['term']) ? htmlentities($_GET['term']) : '' ?>">
			<button class="button"><?php _e('Search', 'LayerSlider') ?></button>
		</div>
	</form>

	<form method="post" class="ls-slider-list-form">
		<input type="hidden" name="ls-bulk-action" value="1">
		<?php wp_nonce_field('bulk-action'); ?>

		<div>

			<!-- List View -->
			<?php if( $layout === 'list' ) : ?>
			<div class="ls-sliders-list">

				<a class="button import-templates <?php echo $lsStoreHasUpdate ? 'has-updates' : '' ?>" href="#" id="ls-import-samples-button">
					<i class="import dashicons dashicons-star-filled"></i>
					<span><?php _e('Template Store', 'LayerSlider') ?></span>
				</a>

				<a class="button" href="#" id="ls-import-button">
					<i class="import dashicons dashicons-upload"></i>
					<span><?php _e('Import Sliders', 'LayerSlider') ?></span>
				</a>

				<a class="button" href="#" id="ls-add-slider-button">
					<i class="add dashicons dashicons-plus"></i>
					<span><?php _e('Add New Slider', 'LayerSlider') ?></span>
				</a>

				<?php if( ! empty($sliders) ) : ?>
				<div class="ls-box">
					<table>
						<thead class="header">
							<tr>
								<td></td>
								<td><?php _e('ID', 'LayerSlider') ?></td>
								<td class="preview-td"><?php _e('Slider preview', 'LayerSlider') ?></td>
								<td><?php _e('Name', 'LayerSlider') ?></td>
								<td class="center"><?php _e('Shortcode', 'LayerSlider') ?></td>
								<td><?php _e('Slides', 'LayerSlider') ?></td>
								<td><?php _e('Created', 'LayerSlider') ?></td>
								<td><?php _e('Modified', 'LayerSlider') ?></td>
								<td></td>
							</tr>
						</thead>
						<tbody>
							<?php foreach($sliders as $key => $item) :
								$class = ($item['flag_deleted'] == '1') ? ' dimmed' : '';
								$preview = apply_filters('ls_preview_for_slider', $item );
							?>
							<tr class="slider-item<?php echo $class ?>" data-id="<?php echo $item['id'] ?>" data-slug="<?php echo htmlentities($item['slug']) ?>">
								<td><input type="checkbox" name="sliders[]" value="<?php echo $item['id'] ?>"></td>
								<td><span><?php echo $item['id'] ?></span></td>
								<td class="preview-td">
									<a class="preview" style="background-image: url(<?php echo  ! empty( $preview ) ? $preview : LS_ROOT_URL . '/static/admin/img/blank.gif' ?>);" href="<?php echo admin_url('admin.php?page=layerslider&action=edit&id='.$item['id'] ) ?>">

									</a>
								</td>
								<td class="name">
									<a href="<?php echo admin_url('admin.php?page=layerslider&action=edit&id='.$item['id'] ) ?>">
										<?php echo apply_filters('ls_slider_title', stripslashes($item['name']), 40) ?>
									</a>
								</td>
								<td class="center"><input type="text" class="ls-shortcode" value="[layerslider id=&quot;<?php echo !empty($item['slug']) ? $item['slug'] : $item['id'] ?>&quot;]" readonly></td>
								<td><span><?php echo isset($item['data']['layers']) ? count($item['data']['layers']) : 0 ?></span></td>
								<td><span><?php echo ls_date('d/m/y', $item['date_c']) ?></span></td>
								<td><span><?php echo human_time_diff($item['date_m']) ?> <?php _e('ago', 'LayerSlider') ?></span></td>
								<td class="center">
									<?php if(!$item['flag_deleted']) : ?>
									<span class="slider-actions-button dashicons dashicons-arrow-down-alt2"
										data-id="<?php echo $item['id'] ?>"
										data-slug="<?php echo htmlentities($item['slug']) ?>"
										data-export-url="<?php echo wp_nonce_url( admin_url( 'admin.php?page=layerslider&action=export&id='.$item['id'] ), 'export-sliders') ?>"
										data-export-html-url="<?php echo wp_nonce_url( admin_url( 'admin.php?page=layerslider&action=export-html&id='.$item['id'] ), 'export-sliders') ?>"
										data-duplicate-url="<?php echo wp_nonce_url( admin_url( 'admin.php?page=layerslider&action=duplicate&id='.$item['id'] ), 'duplicate_'.$item['id'] ) ?>"
										data-revisions-url="<?php echo admin_url( 'admin.php?page=layerslider-addons&section=revisions&id='.$item['id'] ) ?>"
										data-remove-url="<?php echo wp_nonce_url( admin_url( 'admin.php?page=layerslider&action=remove&id='.$item['id'] ), 'remove_'.$item['id'] ) ?>">
									</span>
									<?php else : ?>
									<a href="<?php echo wp_nonce_url( admin_url('admin.php?page=layerslider&action=restore&id='.$item['id'] ), 'restore_'.$item['id']) ?>">
										<span class="dashicons dashicons-backup" data-help="<?php _e('Restore removed slider', 'LayerSlider') ?>"></span>
									</a>
									<?php endif; ?>
								</td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>

					<!-- Slider actions template -->
					<div id="ls-slider-actions-template" class="ls-pointer ls-box ls-hidden">
						<span class="ls-mce-arrow"></span>
						<ul class="inner">
							<li>
								<a href="#" class="embed">
									<i class="dashicons dashicons-plus"></i>
									<?php _e('Embed Slider', 'LayerSlider') ?>
								</a>
							</li>
							<li>
								<a href="#">
									<i class="dashicons dashicons-share-alt2"></i>
									<?php _e('Export', 'LayerSlider') ?>
								</a>
							</li>
							<li>
								<a href="#" class="ls-html-export">
									<i class="dashicons dashicons-media-code"></i>
									<?php _e('Export as HTML', 'LayerSlider') ?>
								</a>
							</li>
							<li>
								<a href="#">
									<i class="dashicons dashicons-admin-page"></i>
									<?php _e('Duplicate', 'LayerSlider') ?>
								</a>
							</li>
							<li>
								<a href="#">
									<i class="dashicons dashicons-backup"></i>
									<?php _e('Revisions', 'LayerSlider') ?>
								</a>
							</li>
							<li>
								<a href="#" class="remove">
									<i class="dashicons dashicons-trash"></i>
									<?php _e('Remove', 'LayerSlider') ?>
								</a>
							</li>
						</ul>
					</div>
					<!-- End of Slider actions template -->
				</div>
				<?php endif ?>
			</div>
			<?php else : ?>

			<!-- Slider List -->
			<div class="ls-sliders-grid clearfix">

				<div class="ls-grid-buttons ls-center clearfix">
					<div class="slider-item hero import-templates <?php echo $lsStoreHasUpdate ? 'has-updates' : '' ?>">
						<div class="slider-item-wrapper">
							<a href="#" id="ls-import-samples-button" class="preview import-templates <?php echo $lsStoreHasUpdate ? 'has-updates' : '' ?>">
								<i class="import dashicons dashicons-star-filled"></i>
								<span><?php _e('Template Store', 'LayerSlider') ?></span>
							</a>
						</div>
					</div>
					<div class="slider-item hero import-sliders">
						<div class="slider-item-wrapper" data-drop-text="<?= _e('Drop file to import', 'LayerSlider') ?>" data-import-text="<?= _e('Uploading, please wait ...', 'LayerSlider') ?>">
							<a href="#" id="ls-import-button" class="preview">
								<i class="import dashicons dashicons-upload"></i>
								<span><?php _e('Import Sliders', 'LayerSlider') ?></span>
							</a>
						</div>
					</div>
					<div class="slider-item hero">
						<div class="slider-item-wrapper">
							<a href="#" id="ls-add-slider-button" class="preview">
								<i class="add dashicons dashicons-plus"></i>
								<span><?php _e('Add New Slider', 'LayerSlider') ?></span>
							</a>
						</div>
					</div>
				</div>



				<?php
				if( ! empty($sliders) ) {
					foreach($sliders as $key => $item) {
						$class = ($item['flag_deleted'] == '1') ? 'dimmed' : '';
						$preview = apply_filters('ls_preview_for_slider', $item );

						if( ! empty( $item['flag_group'] ) ) {
							$groupItems = $item['items'];

							if( empty( $groupItems ) ) { continue; }
							?>
							<div class="slider-item group-item" data-id="<?php echo $item['id'] ?>">
								<div class="slider-item-wrapper">
									<div class="items">
										<?php
											if( ! empty( $item['items'] ) ) {
											foreach( $groupItems as $groupKey => $groupItem )  {
											$groupPreview = apply_filters('ls_preview_for_slider', $groupItem ); ?>
												<div class="item <?php echo ($groupItem['flag_deleted'] == '1') ? 'dimmed' : '' ?>">
													<div class="preview" style="background-image: url(<?php echo  ! empty( $groupPreview ) ? $groupPreview : LS_ROOT_URL . '/static/admin/img/blank.gif' ?>);">
														<?php if( empty( $groupPreview ) ) : ?>
														<div class="no-preview">
															<?php _e('No Preview', 'LayerSlider') ?>
														</div>
														<?php endif ?>
													</div>
												</div>
											<?php } } ?>
									</div>
								</div>
								<div class="info">
									<div class="name">
										<?php echo apply_filters('ls_slider_title', stripslashes($item['name']), 40) ?>
									</div>
								</div>
							</div>
							<div class="ls-hidden">
								<div class="clearfix">
									<?php
										if( ! empty( $item['items'] ) ) {
											foreach( $groupItems as $groupKey => $item ) {
												$class = ($item['flag_deleted'] == '1') ? 'dimmed' : '';
												$preview = apply_filters('ls_preview_for_slider', $item );

												include LS_ROOT_PATH.'/templates/tmpl-slider-grid-item.php';
											}
										}
									?>
								</div>
							</div>
							<?php

						} else {
							include LS_ROOT_PATH.'/templates/tmpl-slider-grid-item.php';
						}
					}
				}
				?>

			</div>
			<?php endif ?>


			<!-- No Slider Notification -->
			<?php if( empty($sliders ) ) : ?>
			<div id="ls-no-sliders">
				<div class="ls-notification-info">
					<i class="dashicons dashicons-info"></i>
					<?php if( $userFilters ) : ?>
					<span><?php echo sprintf(__('No sliders found with the current filters set. %sClick here%s to reset filters.', 'LayerSlider'), '<a href="'.admin_url('admin.php?page=layerslider').'">', '</a>') ?></span>
					<?php else : ?>
					<span><?php echo sprintf(__('Add a new slider or check out the %sTemplate Store%s to get started using LayerSlider.', 'LayerSlider'), '<a href="#" class="ls-open-template-store"><i class="dashicons dashicons-star-filled"></i>', '</a>') ?></span>
					<?php endif ?>
				</div>
			</div>
			<?php endif ?>
		</div>



		<?php if( ! empty($sliders ) ) : ?>
		<div>
			<div class="ls-bulk-actions">
				<select name="action">
					<option value="0"><?php _e('Bulk Actions', 'LayerSlider') ?></option>
					<option value="export"><?php _e('Export selected', 'LayerSlider') ?></option>
					<option value="remove"><?php _e('Remove selected', 'LayerSlider') ?></option>
					<option value="delete"><?php _e('Delete permanently', 'LayerSlider') ?></option>
					<?php if( $showAllSlider ) : ?>
					<option value="restore"><?php _e('Restore selected', 'LayerSlider') ?></option>
					<?php endif; ?>
					<option value="group"><?php _e('Create group from selected', 'LayerSlider') ?></option>
					<option value="merge"><?php _e('Merge selected as new', 'LayerSlider') ?></option>
				</select>
				<button class="button"><?php _e('Apply', 'LayerSlider') ?></button>
			</div>
			<div class="ls-pagination bottom">
				<div class="tablenav-pages">
					<span class="displaying-num"><?php echo sprintf(_n('%d slider', '%d sliders', $maxItem, 'LayerSlider'), $maxItem) ?></span>
					<span class="pagination-links">
						<a class="button first-page<?php echo ($curPage <= 1) ? ' disabled' : ''; ?>" title="<?php _e('Go to the first page', 'LayerSlider') ?>" href="<?php echo admin_url( 'admin.php?page=layerslider&filter='.$urlParamFilter.'&term='.$urlParamTerm.'&order='.$urlParamOrder ) ?>">«</a>
						<a class="button prev-page <?php echo ($curPage <= 1) ? ' disabled' : ''; ?>" title="<?php _e('Go to the previous page', 'LayerSlider') ?>" href="<?php echo admin_url( 'admin.php?page=layerslider&paged='.($curPage-1).'&filter='.$urlParamFilter.'&term='.$urlParamTerm.'&order='.$urlParamOrder ) ?> ">‹</a>

						<span class="total-pages"><?php echo sprintf(__('%1$d of %2$d', 'LayerSlider'), $curPage, $maxPage) ?> </span>

						<a class="button next-page <?php echo ($curPage >= $maxPage) ? ' disabled' : ''; ?>" title="<?php _e('Go to the next page', 'LayerSlider') ?>" href="<?php echo admin_url( 'admin.php?page=layerslider&paged='.($curPage+1).'&filter='.$urlParamFilter.'&term='.$urlParamTerm.'&order='.$urlParamOrder ) ?>">›</a>
						<a class="button last-page <?php echo ($curPage >= $maxPage) ? ' disabled' : ''; ?>" title="<?php _e('Go to the last page', 'LayerSlider') ?>" href="<?php echo admin_url( 'admin.php?page=layerslider&paged='.$maxPage.'&filter='.$urlParamFilter.'&term='.$urlParamTerm.'&order='.$urlParamOrder ) ?>">»</a>
					</span>
				</div>
			</div>
		</div>
		<?php endif ?>
	</form>



	<div class="columns clearfix">

		<!-- Product Activation -->
		<div class="half">
			<div class="ls-box ls-product-banner ls-auto-update <?php echo  $validity ? 'active' : '' ?>">
				<div class="header medium">
					<h2><?php _e('Product Activation', 'LayerSlider') ?></h2>
					<figure class="status <?php echo $validity ? 'activated' : 'not-activated' ?>">

						<span>
							<i class="dashicons dashicons-warning"></i>
							<?php _e('Not Activated', 'LayerSlider') ?>
						</span>

						<span>
							<i class="dashicons dashicons-yes"></i>
							<?php _e('Activated', 'LayerSlider') ?>
						</span>

					</figure>
				</div>
				<div class="inner guide">
					<p>
						<?php if( ! $validity ) : ?>
						<?php _e('Unlock all these features by activating your site.', 'LayerSlider') ?>
						<a target="_blank" href="https://layerslider.kreaturamedia.com/documentation/#activation"><?php _e('Click here to learn more', 'LayerSlider') ?></a>
						<?php else : ?>
						<?php _e('You have successfully activated your site to receive all these features:', 'LayerSlider') ?>
						<?php endif ?>
					</p>
					<ul>
						<li>
							<i class="dashicons dashicons-update"></i>
							<strong><?php _e('Automatic Updates', 'LayerSlider') ?></strong>
							<small><?php _e('Always receive the latest LayerSlider version.', 'LayerSlider') ?></small>
						</li>
						<li>
							<i class="dashicons dashicons-editor-help"></i>
							<strong><?php _e('Product Support', 'LayerSlider') ?></strong>
							<small><?php _e('Direct help from our Support Team.', 'LayerSlider') ?></small>
						</li>
						<li>
							<i class="dashicons dashicons-star-filled"></i>
							<strong><?php _e('Exclusive Features', 'LayerSlider') ?></strong>
							<small><?php _e('Unlock exclusive and early-access features.', 'LayerSlider') ?></small>
						</li>
						<li>
							<i class="dashicons dashicons-store"></i>
							<strong><?php _e('Premium Slider Templates', 'LayerSlider') ?></strong>
							<small><?php _e('Access more templates to get started with projects.', 'LayerSlider') ?></small>
						</li>
					</ul>

					<button class="button-activation button button-primary button-hero"><?php _e('Activate Now', 'LayerSlider') ?></button>
				</div>
				<form method="post" class="inner">
					<input type="hidden" name="action" value="layerslider_authorize_site">

					<div class="main-controls">
						<span class="enter"><?php _e('Enter your license key:', 'LayerSlider') ?></span>
						<a target="_blank" class="button button-small where-button" href="https://layerslider.kreaturamedia.com/documentation/#activation-purchase-code"><?php _e('Where’s my license key?', 'LayerSlider') ?></a>

						<div class="key">
							<input type="text" name="purchase_code" value="<?php echo $codeFormatted ?>" placeholder="e.g. bc8e2b24-3f8c-4b21-8b4b-90d57a38e3c7">
						</div>
						<p>
							<?php if( ! LS_Config::get('autoupdate') ) {
								echo sprintf(
									__('In case you’ve received LayerSlider with a theme, you will need a license key, which is payable. Product activation is optional, it’s for additional features only. For more information, read %sour documentation%s.', 'LayerSlider'), '<a href="https://layerslider.kreaturamedia.com/documentation/#activation" target="_blank">', '</a>');
							} else {
								echo sprintf(__('If you experience any issue or need further information, please read our %sactivation guide%s.', 'LayerSlider'), '<a href="https://layerslider.kreaturamedia.com/documentation/#activation" target="_blank">', '</a>');
							} ?>
						</p>

						<button class="button button-primary button-hero button-save"><?php _e('Activate Now', 'LayerSlider') ?></button>
						<a target="_blank" class="button button-hero purchase-button" href="<?php echo LS_Config::get('purchase_url') ?>"><?php _e('Purchase license', 'LayerSlider') ?></a>
					</div>
					<div class="sub-options">

						<button class="button button-secondary button-save"><?php _e('Update', 'LayerSlider') ?></button>
						<div class="channel">
							<?php _e('Release channel:', 'LayerSlider') ?>
							<label><input type="radio" name="channel" value="stable" <?php echo ($channel === 'stable') ? 'checked="checked"' : ''?>> <?php _e('Stable', 'LayerSlider') ?></label>
							<label data-help="<?php _e('Although pre-release versions meant to work properly, they might contain unknown issues, and are not recommended for sites in production.', 'LayerSlider') ?>">
								<input type="radio" name="channel" value="beta" <?php echo ($channel === 'beta') ? 'checked="checked"' : ''?>> <?php _e('Beta', 'LayerSlider') ?>
							</label>
						</div>

						<p class="note">
							<?php _e('Thank you for purchasing LayerSlider! Your site is activated to receive automatic updates and to access all premium content & features.', 'LayerSlider') ?>
						</p>

						<div class="controls">
							<a href="update-core.php"><?php _e('Check for updates', 'LayerSlider') ?></a>
							<a href="#" class="ls-deauthorize"><?php _e('Deactivate this site', 'LayerSlider') ?></a>
							<span></span>
						</div>
					</div>
				</form>
			</div>
		</div>

		<!-- Product Support  -->
		<div class="half">
			<div class="ls-box ls-product-banner ls-product-support">
				<div class="header medium">
					<h2><?php _e('Product Support', 'LayerSlider') ?></h2>
				</div>
				<div class="inner">
					<ul>
						<li>
							<i class="dashicons dashicons-book"></i>
							<strong><?php _e('Read the documentation', 'LayerSlider') ?></strong>
							<small><?php _e('Get started with using LayerSlider.', 'LayerSlider') ?></small>
						</li>
						<li>
							<i class="dashicons dashicons-sos"></i>
							<strong><?php _e('Browse the FAQs', 'LayerSlider') ?></strong>
							<small><?php _e('Find answers for common questions.', 'LayerSlider') ?></small>
						</li>
						<li>
							<i class="dashicons <?php echo $validity ? 'dashicons-groups' : 'dashicons-lock' ?>"></i>
							<strong><?php _e('Direct Support', 'LayerSlider') ?></strong>
							<small><?php _e('Get in touch with our Support Team.', 'LayerSlider') ?></small>

							<?php if( ! $validity ) : ?>
							<a class="unlock button button-small">
								<?php _e('Unlock Now', 'LayerSlider') ?>
							</a>
							<?php endif ?>
						</li>
					</ul>
					<a href="https://kreatura.ticksy.com/" target="_blank" class="button button-normal"><?php _e('Visit our Support Center', 'LayerSlider') ?></a>
				</div>
			</div>
		</div>
	</div>


	<div class="columns clearfix">

		<!-- Kreatura Newsletter -->
		<div class="half">
			<div class="ls-box ls-product-banner ls-newsletter">
				<div class="header medium">
					<h2><?php _e('LayerSlider Newsletter from Kreatura', 'LayerSlider') ?></h2>
				</div>
				<div class="inner">
					<ul>
						<li>
							<i class="dashicons dashicons-megaphone"></i>
							<strong><?php _e('Stay Updated', 'LayerSlider') ?></strong>
							<small><?php _e('News about the latest features and other product info.', 'LayerSlider') ?></small>
						</li>
						<li>
							<i class="dashicons dashicons-heart"></i>
							<strong><?php _e('Sneak Peek on Product Updates', 'LayerSlider') ?></strong>
							<small><?php _e('Access to all the cool new features before anyone else.', 'LayerSlider') ?></small>
						</li>
						<li>
							<i class="dashicons dashicons-smiley"></i>
							<strong><?php _e('Provide Feedback', 'LayerSlider') ?></strong>
							<small><?php _e('Participate in various programs and help us improving LayerSlider.', 'LayerSlider') ?></small>
						</li>
					</ul>
					<form method="post" action="https://kreaturamedia.com/newsletter/" target="_blank">
						<div class="email"><input type="text" name="email" placeholder="<?php _e('Enter your email address', 'LayerSlider') ?>"></div>
						<button class="button"><?php _e('Subscribe', 'LayerSlider') ?></button>
					</form>
				</div>
			</div>
		</div>


		<!-- Social Media -->
		<div class="half">
			<div class="ls-box ls-product-banner ls-social-media-box">
				<div class="header medium">
					<h2><?php _e('Stay in Touch with Kreatura', 'LayerSlider') ?></h2>
				</div>
				<div class="inner">
					<div><?php _e('Follow us on Social Media and get notified about the latest product updates, sales, deals, and participate in giveaways and other programs.', 'LayerSlider') ?></div>
					<div class="ls-social-media-buttons">

						<a class="ls-facebook button" href="https://www.facebook.com/kreaturamedia/" target="_blank">
							<span class="dashicons dashicons-facebook-alt"></span> Facebook
						</a>

						<a class="ls-twitter button" href="https://twitter.com/kreaturamedia" target="_blank">
							<span class="dashicons dashicons-twitter"></span> Twitter
						</a>

						<a class="ls-youtube button" href="https://www.youtube.com/user/kreaturamedia" target="_blank">
							<span class="dashicons dashicons-video-alt3"></span> YouTube
						</a>
					</div>

				</div>
			</div>
		</div>
	</div>


	<div class="ls-box ls-news">
		<div class="header medium">
			<h2><?php _e('LayerSlider News', 'LayerSlider') ?></h2>
			<div class="ls-version"><?php echo sprintf(__('You have version %s installed', 'LayerSlider'), LS_PLUGIN_VERSION) ?></div>
		</div>
		<div>
			<?php $updateVersion = get_option( 'ls-latest-version', LS_PLUGIN_VERSION ); ?>
			<iframe src="https://news.kreaturamedia.com/layerslider/?ver=<?php echo $updateVersion ?>"></iframe>
		</div>
	</div>
</div>

<!-- Help menu WP Pointer -->
<?php
if(get_user_meta(get_current_user_id(), 'layerslider_help_wp_pointer', true) != '1') {
add_user_meta(get_current_user_id(), 'layerslider_help_wp_pointer', '1'); ?>
<script type="text/javascript">

	// Help
	jQuery(document).ready(function() {
		jQuery('#contextual-help-link-wrap').pointer({
			pointerClass : 'ls-help-pointer',
			pointerWidth : 320,
			content: '<h3><?php _e('The documentation is here', 'LayerSlider') ?></h3><div class="inner"><?php _e('Open this help menu to quickly access to our online documentation.', 'LayerSlider') ?></div>',
			position: {
				edge : 'top',
				align : 'right'
			}
		}).pointer('open');
	});
</script>
<?php } ?>
<script type="text/javascript">
	var lsScreenOptions = <?php echo json_encode($lsScreenOptions) ?>;
	var pluginPath = '<?php echo LS_ROOT_URL ?>/static/';
</script>
