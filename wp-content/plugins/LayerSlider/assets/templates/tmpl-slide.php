<?php defined( 'LS_ROOT_FILE' ) || exit; ?>
<div class="ls-box ls-layer-box active">
	<input type="hidden" name="layerkey" value="0">
	<table>
		<thead class="ls-layer-options-thead">
			<tr>
				<td colspan="5">
					<i class="dashicons dashicons-welcome-write-blog"></i>
					<h4><?php _e('Slide Options', 'LayerSlider') ?></h4>
				</td>
			</tr>
		</thead>
		<tbody class="ls-slide-options">
			<input type="hidden" name="post_offset" value="-1">
			<input type="hidden" name="3d_transitions">
			<input type="hidden" name="2d_transitions">
			<input type="hidden" name="custom_3d_transitions">
			<input type="hidden" name="custom_2d_transitions">
			<tr>
				<td class="slide-image" colspan="2">
					<h3 class="subheader"><?php _e('Slide Background Image', 'LayerSlider') ?></h3>
					<div class="inner">
						<div class="float">
							<input type="hidden" name="backgroundId">
							<input type="hidden" name="background">
							<div class="ls-image ls-upload ls-bulk-upload ls-slide-image not-set" data-l10n-set="<?php _e('Click to set', 'LayerSlider') ?>" data-l10n-change="<?php _e('Click to change', 'LayerSlider') ?>" data-help="<?php echo $lsDefaults['slides']['image']['tooltip'] ?>">
								<div><img src="<?php echo LS_ROOT_URL.'/static/admin/img/blank.gif' ?>" alt=""></div>
								<a href="#" class="pixie"></a>
								<a href="#" class="dashicons dashicons-dismiss"></a>
							</div>
							<span class="indent">
								<?php _e('or', 'LayerSlider') ?> <a href="#" class="ls-url-prompt"><?php _e('enter URL', 'LayerSlider') ?></a>
								| <a href="#" class="ls-post-image"><?php _e('use post image', 'LayerSlider') ?></a>
							</span>
						</div>
						<div class="float">
							<div class="row-helper">
								<?php echo $lsDefaults['slides']['imageSize']['name'] ?>
								<?php lsGetSelect($lsDefaults['slides']['imageSize'], null, array('class' => 'slideprop')) ?>
							</div>
							<div class="row-helper">
								<?php echo $lsDefaults['slides']['imagePosition']['name'] ?>
								<?php lsGetSelect($lsDefaults['slides']['imagePosition'], null, array('class' => 'slideprop')) ?>
							</div>
							<div class="row-helper">
								<?php echo $lsDefaults['slides']['imageColor']['name'] ?>
								<?php lsGetInput($lsDefaults['slides']['imageColor'], null, array('class' => 'slideprop ls-colorpicker')) ?>
							</div>
						</div>
					</div>
				</td>
				<td class="slide-thumb">
					<h3 class="subheader"><?php _e('Slide Thumbnail', 'LayerSlider') ?></h3>
					<div class="inner">
						<input type="hidden" name="thumbnailId">
						<input type="hidden" name="thumbnail">
						<div class="ls-image ls-upload ls-slide-thumbnail not-set" data-l10n-set="<?php _e('Click to set', 'LayerSlider') ?>" data-l10n-change="<?php _e('Click to change', 'LayerSlider') ?>" data-help="<?php echo $lsDefaults['slides']['thumbnail']['tooltip'] ?>">
							<div><img src="<?php echo LS_ROOT_URL.'/static/admin/img/blank.gif' ?>" alt=""></div>
							<a href="#" class="pixie"></a>
							<a href="#" class="dashicons dashicons-dismiss"></a>
						</div>
						<span class="indent">
							<?php _e('or', 'LayerSlider') ?>
							<a href="#" class="ls-url-prompt"><?php _e('enter URL', 'LayerSlider') ?></a> |
							<a href="#" class="ls-capture-slide"><?php _e('capture slide', 'LayerSlider') ?></a>
						</span>
					</div>
				</td>
				<td class="slide-timing">
					<h3 class="subheader"><?php _e('Slide Timing', 'LayerSlider') ?></h3>
					<div class="inner">
						<div class="row-helper">
							<?php echo $lsDefaults['slides']['delay']['name'] ?>
							<?php lsGetInput($lsDefaults['slides']['delay'], null, array('class' => 'slideprop')) ?> ms<br>
						</div>
						<div class="row-helper">
							<?php echo $lsDefaults['slides']['timeshift']['name'] ?>
							<?php lsGetInput($lsDefaults['slides']['timeshift'], null, array('class' => 'slideprop')) ?> ms
						</div>
					</div>
				</td>
				<td class="slide-transition">
					<h3 class="subheader"><?php _e('Slide Transition', 'LayerSlider') ?></h3>
					<div class="inner">
						<button type="button" class="button ls-select-transitions new" data-help="<?php _e('You can select your desired slide transitions by clicking on this button.', 'LayerSlider') ?>"><?php _e('Select transitions', 'LayerSlider') ?></button> <br>
						<div class="row-helper">
							<?php lsGetInput($lsDefaults['slides']['transitionDuration'], null, array('class' => 'slideprop')) ?>
							<span>ms</span>
						</div>
					</div>
				</td>
			</tr>
			<tr class="ls-advanced ls-hidden">
				<td class="ls-slide-link" colspan="2">
					<h3 class="subheader"><?php _e('Slide Linking', 'LayerSlider') ?></h3>
					<div class="inner">
						<div class="row-helper">
							<?php lsGetInput($lsDefaults['slides']['linkUrl'], null, array('class' => 'slideprop url', 'placeholder' => $lsDefaults['slides']['linkUrl']['name'] )) ?>
							<input type="hidden" name="linkId">
							<input type="hidden" name="linkName">
							<input type="hidden" name="linkType">
							<div class="ls-insert-link ls-pointer ls-box ls-hidden">
								<ul class="inner">
									<li>
										<a href="#" class="post">
											<?php _e('Choose Post or Page', 'LayerSlider') ?>
										</a>
									</li>
									<li>
										<a href="#" class="dyn">
											<?php _e('Use Dynamic post URL', 'LayerSlider') ?>
										</a>
									</li>
								</ul>
							</div>
							<span>
								<a href="#" class="ls-insert-link-button"><?php _e('insert ...', 'LayerSlider') ?></a>
							</span>
							<a href="#" class="change">
								<i class="dashicons dashicons-editor-unlink"></i>
								<?php _e('change', 'LayerSlider') ?>
							</a>
						</div>
						<div class="row-helper">
							<?php lsGetSelect($lsDefaults['slides']['linkTarget'], null, array('class' => 'slideprop')) ?>
							<?php lsGetSelect($lsDefaults['slides']['linkPosition'], null, array('class' => 'slideprop')) ?>
						</div>
					</div>
				</td>
				<td class="post-options">
					<h3 class="subheader"></h3>
					<div class="inner">
						<button type="button" class="button ls-configure-posts"><span class="dashicons dashicons-admin-post"></span><?php _e('Configure<br>post options', 'LayerSlider') ?></button>
					</div>
				</td>
				<td class="additional-settings">
					<h3 class="subheader"><?php _e('Additional Slide Settings', 'LayerSlider') ?></h3>
					<div class="inner">
						<div class="row-helper">
							<?php echo $lsDefaults['slides']['deeplink']['name'] ?>
							<?php lsGetInput($lsDefaults['slides']['deeplink'], null, array('class' => 'slideprop')) ?>
						</div>

						<?php if( ! LS_Config::get('theme_bundle') || $lsActivated ) : ?>
						<div class="row-helper ls-global-hover">
							<div class="ls-premium">
								<?php if( ! $lsActivated ) : ?>
								<a class="ls-activation-lock dashicons dashicons-lock" target="_blank" href="<?php echo admin_url('admin.php?page=layerslider-addons' ) ?>" data-help="<?php _e('This feature requires product activation. Click on the padlock icon to learn more.', 'LayerSlider') ?>"></a>
								<?php endif ?>
							</div>
							<a href="https://layerslider.kreaturamedia.com/sliders/global-hover-example/" target="_blank">
								<?php echo $lsDefaults['slides']['globalHover']['name'] ?>
							</a>
							<?php lsGetCheckbox($lsDefaults['slides']['globalHover'], null, array('class' => 'slideprop')) ?>
						</div>
						<?php endif ?>
					</div>
				</td>
				<td class="slide-actions">
					<h3 class="subheader"></h3>
					<div class="inner">
						<div class="row-helper">
							<span>
								<?php echo $lsDefaults['slides']['overflow']['name'] ?>
							</span>
							<?php lsGetCheckbox($lsDefaults['slides']['overflow'], null, array('class' => 'slideprop large')) ?>
						</div>
					</div>
				</td>
			</tr>
			<tr class="ls-advanced ls-hidden">
				<td>
					<h3 class="subheader"><?php _e('Custom Slide Properties', 'LayerSlider') ?></h3>
					<div class="ls-custom-slide-properties">
						<table>
							<tr>
								<td>
									<input type="text" placeholder="<?php _e('Key', 'LayerSlider') ?>">
								</td>
								<td>
									<input type="text" placeholder="<?php _e('Value', 'LayerSlider') ?>">
								</td>
							</tr>
						</table>
					</div>
				</td>
				<td class="slide-ken-burns">
					<h3 class="subheader"><?php _e('Ken Burns Effect', 'LayerSlider') ?></h3>
					<div class="inner">
						<div class="row-helper">
							<?php echo $lsDefaults['slides']['kenBurnsZoom']['name'] ?>
							<?php lsGetSelect($lsDefaults['slides']['kenBurnsZoom'], null, array('class' => 'slideprop')) ?>
						</div>
						<div class="row-helper">
							<?php echo $lsDefaults['slides']['kenBurnsScale']['name'] ?>
							<?php lsGetInput($lsDefaults['slides']['kenBurnsScale'], null, array('class' => 'slideprop')) ?>
						</div>
						<div class="row-helper">
							<?php echo $lsDefaults['slides']['kenBurnsRotate']['name'] ?>
							<?php lsGetInput($lsDefaults['slides']['kenBurnsRotate'], null, array('class' => 'slideprop')) ?>
						</div>
					</div>
				</td>
				<td class="slide-parallax">
					<h3 class="subheader"></h3>
					<div class="inner">
						<div class="row-helper">
							<?php echo $lsDefaults['slides']['parallaxType']['name'] ?>
							<?php lsGetSelect($lsDefaults['slides']['parallaxType'], null, array('class' => 'slideprop')) ?>
						</div>
						<div class="row-helper">
							<?php echo $lsDefaults['slides']['parallaxEvent']['name'] ?>
							<?php lsGetSelect($lsDefaults['slides']['parallaxEvent'], null, array('class' => 'slideprop')) ?>
						</div>
						<div class="row-helper">
							<?php echo $lsDefaults['slides']['parallaxAxis']['name'] ?>
							<?php lsGetSelect($lsDefaults['slides']['parallaxAxis'], null, array('class' => 'slideprop')) ?>
						</div>
					</div>
				</td>
				<td class="slide-parallax">
					<h3 class="subheader"><?php _e('Parallax Defaults', 'LayerSlider') ?></h3>
					<div class="inner">
						<div class="row-helper">
							<?php echo $lsDefaults['slides']['parallaxTransformOrigin']['name'] ?>
							<?php lsGetInput($lsDefaults['slides']['parallaxTransformOrigin'], null, array('class' => 'slideprop')) ?>
						</div>
						<div class="row-helper">
							<?php echo $lsDefaults['slides']['parallaxDurationMove']['name'] ?>
							<?php lsGetInput($lsDefaults['slides']['parallaxDurationMove'], null, array('class' => 'slideprop')) ?>
						</div>
						<div class="row-helper">
							<?php echo $lsDefaults['slides']['parallaxDurationLeave']['name'] ?>
							<?php lsGetInput($lsDefaults['slides']['parallaxDurationLeave'], null, array('class' => 'slideprop')) ?>
						</div>
					</div>
				</td>
				<td class="slide-parallax">
					<h3 class="subheader"></h3>
					<div class="inner">
						<div class="row-helper">
							<?php echo $lsDefaults['slides']['parallaxDistance']['name'] ?>
							<?php lsGetInput($lsDefaults['slides']['parallaxDistance'], null, array('class' => 'slideprop')) ?>
						</div>
						<div class="row-helper">
							<?php echo $lsDefaults['slides']['parallaxRotate']['name'] ?>
							<?php lsGetInput($lsDefaults['slides']['parallaxRotate'], null, array('class' => 'slideprop')) ?>
						</div>
						<div class="row-helper">
							<?php echo $lsDefaults['slides']['parallaxPerspective']['name'] ?>
							<?php lsGetInput($lsDefaults['slides']['parallaxPerspective'], null, array('class' => 'slideprop')) ?>
						</div>
					</div>
				</td>
			</tr>
<!-- 			<tr class="ls-advanced ls-hidden">
				<td>
					<h3 class="subheader"><?php _e('Filters', 'LayerSlider') ?></h3>
					<div class="inner">
						<div class="row-helper">
							<?php //echo $lsDefaults['slides']['filterFrom']['name'] ?>
							<?php //lsGetInput($lsDefaults['slides']['filterFrom'], null, array('class' => 'slideprop')) ?>
						</div>
						<div class="row-helper">
							<?php //echo $lsDefaults['slides']['filterTo']['name'] ?>
							<?php //lsGetInput($lsDefaults['slides']['filterTo'], null, array('class' => 'slideprop')) ?>
						</div>
					</div>
				</td>
				<td colspan="3"></td>
			</tr>
 -->		</tbody>
	</table>

	<div class="ls-center">
		<div id="ls-more-slide-options" class="button">
			<div>
				<strong>
					<?php _e('Show More Options', 'LayerSlider') ?>
					<small><?php _e('Linking, Ken Burns, Parallax', 'LayerSlider') ?></small>
				</strong>
				<strong><?php _e('Show Less Options', 'LayerSlider') ?></strong>
			</div>
		</div>
	</div>

	<table id="ls-preview-table">
		<thead>
			<tr>
				<td>
					<i class="dashicons dashicons-editor-video ls-preview-icon"></i>
					<h4>
						<span><?php _e('Preview', 'LayerSlider') ?></span>
					</h4>
				</td>
			</tr>
		</thead>
		<tbody>
			<tr id="slider-editor-toolbar">
				<td>
					<div class="ls-editor-zoom">
						<!-- <span class="dashicons dashicons-editor-expand ls-layers-icon"></span> -->
						<div class="ls-editor-slider" ></div>
						<span class="ls-editor-slider-val">100%</span>
						|
    					<?php _e('Auto-Fit', 'LayerSlider') ?>
						<input id="zoom-fit" class="ls-checkbox checkbox small" type="checkbox" checked>
					</div>
					<div class="ls-editor-alignment">
						<button type="button" class="button" data-ls-su>
							<span class="dashicons dashicons-align-right ls-layers-icon"></span>
							<?php _e('Align Layer ...', 'LayerSlider') ?>
						</button>
						<div class="ls-su-data">
							<div id="ls-layer-alignment" class="ls-layer-alignment">

								<table class="ls-grid">
									<tr>
										<td data-move="top left">
											<i><?php _e('top left', 'LayerSlider') ?></i>
										</td>
										<td data-move="top center">
											<i><?php _e('top center', 'LayerSlider') ?></i>
										</td>
										<td data-move="top right">
											<i><?php _e('top right', 'LayerSlider') ?></i>
										</td>
									</tr>
									<tr>
										<td data-move="middle left">
											<i><?php _e('center left', 'LayerSlider') ?></i>
										</td>
										<td data-move="middle center">
											<i><?php _e('center center', 'LayerSlider') ?></i>
										</td>
										<td data-move="middle right">
											<i><?php _e('center right', 'LayerSlider') ?></i>
										</td>
									</tr>
									<tr>
										<td data-move="bottom left">
											<i><?php _e('bottom left', 'LayerSlider') ?></i>
										</td>
										<td data-move="bottom center">
											<i><?php _e('bottom center', 'LayerSlider') ?></i>
										</td>
										<td data-move="bottom right">
											<i><?php _e('bottom right', 'LayerSlider') ?></i>
										</td>
									</tr>
								</table>


								<table class="ls-edges">
									<tr>
										<td data-move="left" class="ls-align-left">
											<i><?php _e('Left Edge', 'LayerSlider') ?></i>
											<span class="dashicons dashicons-align-left"></span>
										</td>
										<td data-move="center" class="ls-align-center">
											<i><?php _e('H. Center', 'LayerSlider') ?></i>
											<span class="dashicons dashicons-align-center"></span>
										</td>
										<td data-move="right" class="ls-align-right">
											<i><?php _e('Right Edge', 'LayerSlider') ?></i>
											<span class="dashicons dashicons-align-right"></span>
										</td>
									</tr>
									<tr>
										<td data-move="top" class="ls-align-top">
											<i><?php _e('Top Edge', 'LayerSlider') ?></i>
											<span class="dashicons dashicons-align-left"></span>
										</td>
										<td data-move="middle" class="ls-align-middle">
											<i><?php _e('V. Center', 'LayerSlider') ?></i>
											<span class="dashicons dashicons-align-center"></span>
										</td>
										<td data-move="bottom" class="ls-align-bottom">
											<i><?php _e('Bottom Edge', 'LayerSlider') ?></i>
											<span class="dashicons dashicons-align-right"></span>
										</td>
									</tr>
								</table>

							</div>
						</div>
					</div>
					<div class="ls-editor-undo-redo">
						<div class="ls-editor-undo disabled">
							<button type="button" class="button-left button">
								<span class="dashicons dashicons-undo ls-layers-icon"></span>
							</button>
							<?php _e('Undo', 'LayerSlider') ?>
						</div>
						|
						<div class="ls-editor-redo disabled">
							<?php _e('Redo', 'LayerSlider') ?>
							<button type="button" class="button-right button">
								<span class="dashicons dashicons-redo ls-layers-icon"></span>
							</button>
						</div>
					</div>

<!--  					<div class="ls-editor-copy-paste">
						<button type="button" class="button"><?php _e('Copy...', 'LayerSlider') ?></button>
						<button type="button" class="button"><?php _e('Paste...', 'LayerSlider') ?></button>
					</div>
 -->
 					<div class="ls-editor-preview">
						<?php _e('Preview', 'LayerSlider') ?>
						<button type="button" class="button ls-preview-button"><?php _ex('Slide', 'noun', 'LayerSlider') ?></button><!--
					 --><button type="button" class="button ls-layer-preview-button"><?php _e('Layer', 'LayerSlider') ?></button>
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

	<div class="ls-sublayer-wrapper">
		<h4>
			<span class="dashicons dashicons-images-alt2 ls-layers-icon"></span>
			<span class="ls-layers-text"><?php _e('Layers', 'LayerSlider') ?></span>
			<a href="#" class="ls-add-sublayer">
				<span class="dashicons dashicons-plus"></span><?php _e('Add New', 'LayerSlider') ?>
			</a>
			<div class="ls-layer-types-wrapper">
				<div class="ls-box ls-layer-types">
					<div>
						<!-- <div class="subheader"><?php _e('Choose a layer type', 'LayerSlider') ?></div> -->
						<ul class="inner">
							<li data-type="img">
								<i class="dashicons dashicons-format-image"></i>
								<?php _e('Image', 'LayerSlider') ?>
							</li>
							<li data-type="icon">
								<i class="dashicons dashicons-flag"></i>
								<?php _e('Icon', 'LayerSlider') ?>
							</li>
							<li data-type="text">
								<i class="dashicons dashicons-text"></i>
								<?php _e('Text', 'LayerSlider') ?>
							</li>
							<li data-type="button">
								<i class="dashicons dashicons-marker"></i>
								<?php _e('Button', 'LayerSlider') ?>
							</li>
							<li data-type="media">
								<i class="dashicons dashicons-video-alt3"></i>
								<?php _e('Video / Audio', 'LayerSlider') ?>
							</li>
							<li data-type="html">
								<i class="dashicons dashicons-editor-code"></i>
								<?php _e('HTML', 'LayerSlider') ?>
							</li>
							<li data-type="post">
								<i class="dashicons dashicons-admin-post"></i>
								<?php _e('Dynamic Layer', 'LayerSlider') ?>
							</li>
							<li data-type="import">
								<i class="dashicons dashicons-upload"></i>
								<?php _e('Import Layer', 'LayerSlider') ?>
							</li>
						</ul>
					</div>
				</div>
			</div>
			<div class="ls-empty-layer-notification">
				<div class="ls-line"></div>
				<div class="ls-spot"></div>
				<h3><?php _e('This slide has no layers') ?></h3>
				<h5><?php printf(__('Click %sAdd New%s to add your first layer.', 'LayerSlider'), '<span><span class="dashicons dashicons-plus"></span>', '</span>') ?></h5>
			</div>
			<div class="ls-timeline-switch filters">
				<ul>
					<li class="active"><?php _e('Layer options', 'LayerSlider') ?></li>
					<li><?php _e('Timeline', 'LayerSlider') ?></li>
				</ul>
			</div>
		</h4>
		<table class="ls-layers-table">
			<tr>
				<td class="ls-layers-list">
					<div class="ls-layers-wrapper">
						<div class="subheader"><?php _e('Static layers from other slides', 'LayerSlider') ?></div>
						<ul class="ls-static-sublayers ls-sublayer-sortable"></ul>
						<div class="subheader"><?php _e('Layers on this slide', 'LayerSlider') ?></div>
						<ul class="ls-sublayers ls-sublayer-sortable"></ul>
					</div>
				</td>
				<td class="ls-layers-settings">

					<div id="ls-popout-desc">
						<table>
							<tr>
								<td>
									<i class="dashicons dashicons-external"></i>
								</td>
								<td class="ls-popout-desc-text">
									<?php _e('Nothing to see here. <br> We popped out. ;)', 'LayerSlider'); ?>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<button class="button"><?php _e('Restore editor', 'LayerSlider') ?></button>
								</td>
							</tr>
						</table>
					</div>

					<div id="ls-layers-settings-popout" data-km-ui-resize="600,950,1300">

						<div id="ls-layers-settings-popout-handler"
							data-help="<?php _e('You can grab me here and drag where you need.', 'LayerSlider') ?>"
							data-km-ui-popover-once="true"
							data-km-ui-popover-autoclose="3"
							data-km-ui-popover-distance="20"
							data-km-ui-popover-theme="red">
							<?php _e('Layer editor', 'LayerSlider') ?>
							<b id="ls-close-popout-editor">
								<i class="dashicons dashicons-external"></i>
								<?php _e('Put back', 'LayerSlider') ?>
							</b>
						</div>

						<div class="ls-multi-select-notice">
							<h5>
								<span class="dashicons dashicons-info"></span>
								<?php _e('Multiple Selection Mode', 'LayerSlider') ?>
								<sup><?php _e('BETA', 'LayerSlider') ?></sup>
							</h5>
							<span><?php _e('In Multiple Selection Mode you can override specific options on all selected layers. Each option field has been reset, only the options you change will be updated on the selected layers. This feature is currently in beta phase, use it cautiously.', 'LayerSlider') ?></span>
							<small><?php _e('Changes will be applied on all selected layers.', 'LayerSlider') ?></small>
						</div>
						<div class="ls-sublayer-pages-wrapper">
							<div class="ls-sublayer-nav">
								<a href="#" class="active"><?php _e('Content', 'LayerSlider') ?></a>
								<a href="#"><?php _e('Transitions', 'LayerSlider') ?></a>
								<a href="#"><?php _e('Link & Attributes', 'LayerSlider') ?></a>
								<a href="#" class="ls-layer-style-tab"><?php _e('Styles', 'LayerSlider') ?></a>
								<b id="ls-open-popout-editor">
									<i class="dashicons dashicons-external"></i>
									<?php _e('Popout editor', 'LayerSlider') ?>
								</b>
							</div>
							<div class="ls-sublayer-pages">
							</div>
						</div>

					</div>
				</td>
			</tr>
		</table>
		<div class="ls-preview-timeline" data-timeline-for="ls-preview-timeline"></div>
	</div>
</div>
