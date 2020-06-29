<?php

// Prevent direct file access
defined( 'LS_ROOT_FILE' ) || exit;

$sDefs  =& $lsDefaults['slider'];
$sProps =& $slider['properties'];

?>

<!-- Slider title -->
<div class="ls-slider-titlewrap">
	<?php $sliderName = !empty($sProps['title']) ? htmlspecialchars(stripslashes($sProps['title'])) : ''; ?>
	<input type="text" name="title" value="<?php echo $sliderName ?>" id="title" autocomplete="off" placeholder="<?php _e('Type your slider name here', 'LayerSlider') ?>">
	<div class="ls-slider-slug">
		<?php _e('Slider slug', 'LayerSlider') ?>:<input type="text" name="slug" value="<?php echo !empty($sProps['slug']) ? $sProps['slug'] : '' ?>" autocomplete="off" placeholder="<?php _e('e.g. homepageslider', 'LayerSlider') ?>" data-help="<?php _e('Set a custom slider identifier to use in shortcodes instead of the database ID. Needs to be unique, and can contain only alphanumeric characters. This setting is optional.', 'LayerSlider') ?>">
	</div>
</div>

<!-- Slider settings -->
<div class="ls-box ls-settings">
	<h3 class="header medium">
		<?php _e('Slider Settings', 'LayerSlider') ?>
		<div class="ls-slider-settings-advanced">
			<?php _e('Show advanced settings', 'LayerSlider') ?> <input type="checkbox" data-toggleitems=".ls-settings-contents .ls-advanced">
		</div>
	</h3>
	<div class="inner">
		<ul class="ls-settings-sidebar">
			<li data-deeplink="publish">
				<i class="dashicons dashicons-calendar-alt"></i>
				<strong><?php _e('Publish', 'LayerSlider') ?></strong>
			</li>
			<li data-deeplink="layout" class="active">
				<i class="dashicons dashicons-editor-distractionfree"></i>
				<strong><?php _e('Layout', 'LayerSlider') ?></strong>
			</li>
			<li data-deeplink="mobile">
				<i class="dashicons dashicons-smartphone"></i>
				<strong><?php _e('Mobile', 'LayerSlider') ?></strong>
			</li>
			<li data-deeplink="slideshow">
				<i class="dashicons dashicons-editor-video"></i>
				<strong><?php _e('Slideshow', 'LayerSlider') ?></strong>
			</li>
			<li data-deeplink="appearance">
				<i class="dashicons dashicons-admin-appearance"></i>
				<strong><?php _e('Appearance', 'LayerSlider') ?></strong>
			</li>
			<li data-deeplink="navigation">
				<i class="dashicons dashicons-image-flip-horizontal"></i>
				<strong><?php _e('Navigation Area', 'LayerSlider') ?></strong>
			</li>
			<li data-deeplink="thumbnav">
				<i class="dashicons dashicons-screenoptions"></i>
				<strong><?php _e('Thumbnail Navigation', 'LayerSlider') ?></strong>
			</li>
			<li data-deeplink="media">
				<i class="dashicons dashicons-video-alt3"></i>
				<strong><?php _e('Video / Audio', 'LayerSlider') ?></strong>
			</li>

			<?php if( ! LS_Config::get('theme_bundle') || $lsActivated ) : ?>
			<li data-deeplink="popup" <?php echo ! $lsActivated ? 'class="locked" data-help="'.__('Popup requires product activation. Click on the padlock icon to learn more.', 'LayerSlider').'"' : '' ?>">
				<i class="dashicons dashicons-external"></i>
				<?php _e('Popup', 'LayerSlider') ?>
				<small><?php _e('NEW', 'LayerSlider') ?></small>

				<?php if( ! $lsActivated ) : ?>
				<a class="ls-activation-lock dashicons dashicons-lock" target="_blank" href="<?php echo admin_url('admin.php?page=layerslider-addons' ) ?>"></a>
				<?php endif; ?>
			</li>
			<?php endif ?>

			<li data-deeplink="yourlogo">
				<i class="dashicons dashicons-admin-post"></i>
				<strong><?php _e('YourLogo', 'LayerSlider') ?></strong>
			</li>
			<li data-deeplink="transition">
				<i class="dashicons dashicons-admin-settings"></i>
				<strong><?php _e('Default Options', 'LayerSlider') ?></strong>
			</li>
			<li data-deeplink="misc">
				<i class="dashicons dashicons-admin-generic"></i>
				<strong><?php _e('Misc', 'LayerSlider') ?></strong>
			</li>
		</ul>
		<div class="ls-settings-contents">
			<input type="hidden" name="sliderVersion" value="<?php echo LS_PLUGIN_VERSION ?>">
			<table>
				<!-- Publish -->
				<tbody>
					<tr><th colspan="2"><?php echo $sDefs['status']['name'] ?></th></tr>
					<tr>
						<td colspan="2" class="hero">
							<p>
								<?php lsGetCheckbox($sDefs['status'], $sProps, array('class' => 'hero ls-publish-checkbox')); ?>
								<?php echo $sDefs['status']['desc'] ?>
							</p>
						</td>
					</tr>
					<tr>
						<th class="half"><?php echo $sDefs['scheduleStart']['name'] ?></th>
						<th class="half"><?php echo $sDefs['scheduleEnd']['name'] ?></th>
					</tr>
					<tr>
						<td class="half">
							<div class="ls-datepicker-wrapper">
								<label><?php _e('Interpreted as:', 'LayerSlider') ?> <span></span></label>
								<?php lsGetInput($sDefs['scheduleStart'], $sProps, array('class' => 'ls-datepicker-input', 'data-schedule-key' => 'schedule_start')); ?>
							</div>
						</td>
						<td class="half">
							<div class="ls-datepicker-wrapper">
								<label><?php _e('Interpreted as:', 'LayerSlider') ?> <span></span></label>
								<?php lsGetInput($sDefs['scheduleEnd'], $sProps, array('class' => 'ls-datepicker-input', 'data-schedule-key' => 'schedule_end')); ?>
							</div>
						</td>
					</tr>
					<tr>
						<td colspan="2" class="hero">
							<div class="ls-schedule-desc"><?php echo $sDefs['scheduleStart']['desc'] ?></div>
						</td>
					</tr>
				</tbody>

				<!-- Layout -->
				<tbody class="active">
					<tr><th colspan="3"><?php _e('Slider type & dimensions', 'LayerSlider') ?></th></tr>
					<tr>
						<td colspan="3" class="ls-slider-dimensions">
							<div data-type="fixedsize">
								<img src="<?php echo LS_ROOT_URL.'/static/admin/img/layout-fixed.png' ?>">
								<span><?php _e('Fixed size', 'LayerSlider') ?></span>
							</div>

							<div data-type="responsive">
								<img src="<?php echo LS_ROOT_URL.'/static/admin/img/layout-responsive.png' ?>">
								<span><?php _e('Responsive', 'LayerSlider') ?></span>
							</div>

							<div data-type="fullwidth">
								<img src="<?php echo LS_ROOT_URL.'/static/admin/img/layout-full-width.png' ?>">
								<span><?php _e('Full width', 'LayerSlider') ?></span>
							</div>

							<div data-type="fullsize">
								<img src="<?php echo LS_ROOT_URL.'/static/admin/img/layout-full-screen.png' ?>">
								<span><?php _e('Full size', 'LayerSlider') ?></span>
							</div>

							<?php if( ! LS_Config::get('theme_bundle') || $lsActivated ) : ?>
							<div data-type="popup" <?php echo ! $lsActivated ? 'class="locked" data-help="'.__('Popup requires product activation. Click on the padlock icon to learn more.', 'LayerSlider').'" data-help-delay="100"' : '' ?>">
								<img src="<?php echo LS_ROOT_URL.'/static/admin/img/layout-popup.png' ?>">
								<span><?php _e('Popup', 'LayerSlider') ?></span>

								<?php if( ! $lsActivated ) : ?>
								<a class="ls-activation-lock dashicons dashicons-lock" target="_blank" href="<?php echo admin_url('admin.php?page=layerslider-addons' ) ?>"></a>
								<?php endif ?>
							</div>
							<?php endif ?>

							<?php lsGetInput($sDefs['type'], $sProps); ?>
						</td>
					</tr>
					<tr class="popup-row">
						<td colspan="3" class="ls-layout-popup-notice">
							<?php _e('Popup uses different kinds of layout settings than normal sliders. Press the button below to configure your Popup.', 'LayerSlider') ?><br>
							<a href="#popup" class="button button-hero button-primary"><?php _e('Configure Popup', 'LayerSlider') ?></a>
						</td>
					</tr>
					<?php
					lsOptionRow('input', $sDefs['width'], $sProps, array(), 'ls-popup-hide' );
					lsOptionRow('input', $sDefs['height'], $sProps, array(), 'ls-popup-hide' );
					lsOptionRow('input', $sDefs['maxWidth'], $sProps, array(), 'ls-popup-hide' );
					lsOptionRow('input', $sDefs['responsiveUnder'], $sProps, array(), 'full-width-row ls-popup-hide' );
					lsOptionRow('select', $sDefs['fullSizeMode'], $sProps, array(), 'full-size-row ls-popup-hide' );
					lsOptionRow('checkbox', $sDefs['fitScreenWidth'], $sProps, array(), 'full-width-row full-size-row ls-popup-hide' );
					lsOptionRow('checkbox', $sDefs['allowFullscreen'], $sProps, array(), 'ls-popup-hide' )
					?>

					<tr class="ls-advanced ls-hidden"><th colspan="3"><?php _e('Other settings', 'LayerSlider') ?></th></tr>
					<?php lsOptionRow('input', $sDefs['maxRatio'], $sProps ); ?>
					<tr class="ls-advanced ls-hidden">
						<td style="vertical-align: top; padding-top: 10px;">
							<div>
								<i class="dashicons dashicons-flag" data-help="<?php _e('Advanced option', 'LayerSlider') ?>"></i>
								<?php echo $sDefs['insertMethod']['name'] ?>
							</div>
						</td>
						<td>
							<?php
								lsGetSelect($sDefs['insertMethod'], $sProps);
								lsGetInput($sDefs['insertSelector'], $sProps);
							?>
						</td>
						<td class="desc"><?php echo $sDefs['insertMethod']['desc'] ?></td>
					</tr>
					<?php
					lsOptionRow('select', $sDefs['clipSlideTransition'], $sProps );
					lsOptionRow('checkbox', $sDefs['preventSliderClip'], $sProps, array(), 'full-width-row full-size-row' );
					?>
				</tbody>


				<!-- Mobile -->
				<tbody>
					<?php
					lsOptionRow('checkbox', $sDefs['hideOnMobile'], $sProps );
					lsOptionRow('input', $sDefs['hideUnder'], $sProps );
					lsOptionRow('input', $sDefs['hideOver'], $sProps );
					lsOptionRow('checkbox', $sDefs['slideOnSwipe'], $sProps );
					lsOptionRow('checkbox', $sDefs['optimizeForMobile'], $sProps );
					?>
				</tbody>

				<!-- Slideshow -->
				<tbody>
					<tr><th colspan="3"><?php _e('Slideshow behavior', 'LayerSlider') ?></th></tr>
					<tr>
						<td><?php echo $sDefs['firstSlide']['name'] ?></td>
						<td><?php lsGetInput($sDefs['firstSlide'], $sProps) ?></td>
						<td class="desc"><?php echo $sDefs['firstSlide']['desc'] ?></td>
					</tr>
					<?php
					lsOptionRow('checkbox', $sDefs['autoStart'], $sProps );
					lsOptionRow('checkbox', $sDefs['pauseLayers'], $sProps );
					lsOptionRow('checkbox', $sDefs['startInViewport'], $sProps );
					lsOptionRow('select', $sDefs['pauseOnHover'], $sProps );
					lsOptionRow('checkbox', $sDefs['hashChange'], $sProps );
					?>
					<tr><th colspan="3"><?php _e('Slideshow navigation', 'LayerSlider') ?></th></tr>
					<?php
					lsOptionRow('checkbox', $sDefs['keybNavigation'], $sProps );
					lsOptionRow('checkbox', $sDefs['touchNavigation'], $sProps );

					if( ! LS_Config::get('theme_bundle') || $lsActivated ) { ?>
					<tr><th colspan="3"><?php _e('Play By Scroll', 'LayerSlider') ?></th></tr>
					<?php
						lsOptionRow('checkbox', $sDefs['playByScroll'], $sProps );
						lsOptionRow('checkbox', $sDefs['playByScrollStart'], $sProps );
						lsOptionRow('checkbox', $sDefs['playByScrollSkipSlideBreaks'], $sProps );
						lsOptionRow('input', $sDefs['playByScrollSpeed'], $sProps );
					}
					?>
					<tr><th colspan="3"><?php _e('Cycles', 'LayerSlider') ?></th></tr>
					<?php
					lsOptionRow('input', $sDefs['loops'], $sProps );
					lsOptionRow('checkbox', $sDefs['forceLoopNumber'], $sProps );
					?>
					<tr><th colspan="3"><?php _e('Other settings', 'LayerSlider') ?></th></tr>
					<?php
					lsOptionRow('checkbox', $sDefs['twoWaySlideshow'], $sProps );
					lsOptionRow('checkbox', $sDefs['shuffle'], $sProps );
					?>
				</tbody>

				<!-- Appearance -->
				<tbody>
					<tr><th colspan="3"><?php _e('Slider appearance', 'LayerSlider') ?></th></tr>
					<tr>
						<td><?php _e('Skin', 'LayerSlider') ?></td>
						<td>
							<select name="skin">
								<?php $sProps['skin'] = empty($sProps['skin']) ? $sDefs['skin']['value'] : $sProps['skin'] ?>
								<?php $skins = LS_Sources::getSkins(); ?>
								<?php foreach($skins as $skin) : ?>
								<?php $selected = ($skin['handle'] == $sProps['skin']) ? ' selected="selected"' : '' ?>
								<option value="<?php echo $skin['handle'] ?>"<?php echo $selected ?>>
									<?php
									echo $skin['name'];
									if(!empty($skin['info']['note'])) { echo ' - ' . $skin['info']['note']; }
									?>
								</option>
								<?php endforeach; ?>
							</select>
						</td>
						<td class="desc"><?php echo $sDefs['skin']['desc'] ?></td>
					</tr>
					<?php
					lsOptionRow('input', $sDefs['sliderFadeInDuration'], $sProps );
					lsOptionRow('input', $sDefs['sliderClasses'], $sProps );
					?>
					<tr>
						<td><?php _e('Custom slider CSS', 'LayerSlider') ?></td>
						<td colspan="2"><textarea name="sliderstyle" cols="30" rows="10"><?php echo !empty($sProps['sliderstyle']) ? $sProps['sliderstyle'] : $sDefs['sliderStyle']['value'] ?></textarea></td>
					</tr>

					<tr><th colspan="3"><?php _e('Slider global background', 'LayerSlider') ?></th></tr>
					<?php
					lsOptionRow('input', $sDefs['globalBGColor'], $sProps, array('class' => 'input ls-colorpicker minicolors-input') );
					?>
					<tr>
						<td><?php _e('Background image', 'LayerSlider') ?></td>
						<td>
							<?php $bgImage = !empty($sProps['backgroundimage']) ? $sProps['backgroundimage'] : null; ?>
							<?php $bgImageId = !empty($sProps['backgroundimageId']) ? $sProps['backgroundimageId'] : null; ?>
							<input type="hidden" name="backgroundimageId" value="<?php echo !empty($sProps['backgroundimageId']) ? $sProps['backgroundimageId'] : '' ?>">
							<input type="hidden" name="backgroundimage" value="<?php echo !empty($sProps['backgroundimage']) ? $sProps['backgroundimage'] : '' ?>">
							<div class="ls-image ls-global-background ls-upload" data-l10n-set="<?php _e('Click to set', 'LayerSlider') ?>" data-l10n-change="<?php _e('Click to change', 'LayerSlider') ?>">
								<div><img src="<?php echo apply_filters('ls_get_thumbnail', $bgImageId, $bgImage) ?>" alt=""></div>
								<a href="#" class="dashicons dashicons-dismiss"></a>
							</div>
						</td>
						<td class="desc"><?php echo $sDefs['globalBGImage']['desc'] ?></td>
					</tr>
					<?php
					lsOptionRow('select', $sDefs['globalBGRepeat'], $sProps );
					lsOptionRow('select', $sDefs['globalBGAttachment'], $sProps );
					lsOptionRow('input', $sDefs['globalBGPosition'], $sProps, array('class' => 'input') );
					?>
					<tr>
						<td><?php echo $sDefs['globalBGSize']['name'] ?></td>
						<td><?php lsGetInput($sDefs['globalBGSize'], $sProps, array('class' => 'input')) ?></div>
						</td>
						<td class="desc"><?php echo $sDefs['globalBGSize']['desc'] ?></td>
					</tr>

				</tbody>

				<!-- Navigation Area -->
				<tbody>
					<tr><th colspan="3"><?php _e('Show navigation buttons', 'LayerSlider') ?></th></tr>
					<?php
					lsOptionRow('checkbox', $sDefs['navPrevNextButtons'], $sProps );
					lsOptionRow('checkbox', $sDefs['navStartStopButtons'], $sProps );
					lsOptionRow('checkbox', $sDefs['navSlideButtons'], $sProps );
					?>
					<tr><th colspan="3"><?php _e('Navigation buttons on hover', 'LayerSlider') ?></th></tr>
					<?php
					lsOptionRow('checkbox', $sDefs['hoverPrevNextButtons'], $sProps );
					lsOptionRow('checkbox', $sDefs['hoverSlideButtons'], $sProps );
					?>
					<tr><th colspan="3"><?php _e('Slideshow timers', 'LayerSlider') ?></th></tr>
					<?php
					lsOptionRow('checkbox', $sDefs['barTimer'], $sProps );
					lsOptionRow('checkbox', $sDefs['circleTimer'], $sProps );
					lsOptionRow('checkbox', $sDefs['slideBarTimer'], $sProps );
					?>
				</tbody>

				<!-- Thumbnail navigation -->
				<tbody>
					<tr><th colspan="3"><?php _e('Appearance', 'LayerSlider') ?></th></tr>
					<?php
					lsOptionRow('select', $sDefs['thumbnailNavigation'], $sProps );
					lsOptionRow('input', $sDefs['thumbnailAreaWidth'], $sProps );
					?>
					<tr><th colspan="3"><?php _e('Thumbnail dimensions', 'LayerSlider') ?></th></tr>
					<?php
					lsOptionRow('input', $sDefs['thumbnailWidth'], $sProps );
					lsOptionRow('input', $sDefs['thumbnailHeight'], $sProps );
					?>
					<tr><th colspan="3"><?php _e('Thumbnail appearance', 'LayerSlider') ?></th></tr>
					<?php
					lsOptionRow('input', $sDefs['thumbnailActiveOpacity'], $sProps );
					lsOptionRow('input', $sDefs['thumbnailInactiveOpacity'], $sProps );
					?>
				</tbody>

				<!-- Videos -->
				<tbody>
					<?php
					lsOptionRow('checkbox', $sDefs['autoPlayVideos'], $sProps );
					lsOptionRow('checkbox', $sDefs['rememberUnmuteState'], $sProps );
					lsOptionRow('select', $sDefs['autoPauseSlideshow'], $sProps );
					lsOptionRow('select', $sDefs['youtubePreviewQuality'], $sProps );
					?>
				</tbody>



				<!-- Popup -->
				<?php if( ! LS_Config::get('theme_bundle') || $lsActivated ) : ?>
				<tbody class="ls-settings-popup">
					<tr class="ls-premium">
						<td colspan="3">
							<div class="ls-description">
								<?php echo sprintf(__('Instead of embedding sliders at a fixed location on your page, you can display them  on-the-fly at certain actions as a popup. Greet new visitors on your site with a beautifully designed animated banner with newsletter subscription or other offers. Display a message when they become idle. Show them recommended content before leaving the page or when they finished reading an article. There are a lot of possibilities and all of LayerSlider’s content creation and animation capabilities are now available in a popup form as well. This includes dynamic content from your WP posts and any other feature you would use in a slider. %sClick here for more information and live examples%s', 'LayerSlider'), '<a href="https://layerslider.kreaturamedia.com/features/popups/" target="_blank">', '</a>') ?>
							</div>

							<div id="ls-popup-notifications">
								<?php if( ! LS_Config::isActivatedSite() ) : ?>
								<div class="ls-notification">
									<i class="dashicons dashicons-warning"></i>
									<?php echo sprintf(__('Popup is a premium feature. You can preview all the options here with the Live Preview button, but you need to activate your copy of LayerSlider in order to use it on your front end pages. %sPurchase a license%s or %sread the documentation%s to learn more. %sGot LayerSlider in a theme?%s', 'LayerSlider'), '<a href="'.LS_Config::get('purchase_url').'" target="_blank">', '</a>', '<a href="https://layerslider.kreaturamedia.com/documentation/#activation" target="_blank">', '</a>', '<a href="https://layerslider.kreaturamedia.com/documentation/#activation-bundles" target="_blank">', '</a>') ?>
								</div>
								<?php endif ?>

								<div class="ls-popup-layout-notification ls-notification info">
									<i class="dashicons dashicons-warning"></i>
									<?php echo sprintf(__('Currently, this slider is not set up as a Popup. You can preview all the options here with the Live Preview button, but you need to select the Popup option under the %sLayout section%s to use it on your front end pages.', 'LayerSlider'), '<a href="#layout">', '</a>') ?>
								</div>

								<div class="ls-popup-trigger-notification ls-notification info">
									<i class="dashicons dashicons-warning"></i>
									<?php _e('Your Popup will not show up until you set a trigger. Check out the Launch Popup section and choose how and when your Popup should be displayed.', 'LayerSlider') ?>
								</div>
							</div>
						</td>
					</tr>
					<tr><th colspan="3"><div><?php _e('Publish', 'LayerSlider') ?></div></th></tr>
					<tr>
						<td colspan="3" class="center ls-spacer-top ls-spacer-bottom"><?php echo sprintf(__('Check out the %sPublish%s and %sMobile%s sections to set up scheduling, target devices, etc.'), '<a href="#publish">', '</a>', '<a href="#mobile">', '</a>') ?></td>
					</tr>
					<tr>
						<td colspan="3" class="ls-popup-appearance">
							<table>
								<tr>
									<th class="ls-popup-preview"><?php _e('Preview', 'LayerSlider') ?></th>
									<th char="ls-popup-layout"><?php _e('Layout Settings', 'LayerSlider') ?></th>
								</tr>
								<tr>
									<td class="ls-popup-preview ls-spacer-top" style="padding-bottom: 30px;">
										<div class="ls-layout-illustration ls-popup-layout-preview">
											<div class="ls-layout-illustration-inner">

												<div class="ls-popup-layout-example">
													<div class="ls-popup-layout-padding">
														<div class="ls-popup-layout-inner ls-popup-right-bottom">
														</div>
													</div>
												</div>

											</div>
										</div>
										<button type="button" class="button ls-popup-preview-button">
											<i class="dashicons dashicons-visibility"></i>
											<?php _e('Live Preview', 'LayerSlider') ?>
										</button>
									</td>
									<td class="ls-popup-layout ls-spacer-top" style="padding-bottom: 30px;">
										<div>
											<button type="button" id="tmpl-popup-presets-button" class="button">
												<i class="dashicons dashicons-layout"></i>
												<?php _e('Choose Preset', 'LayerSlider') ?>
											</button>
											<?php
											lsGetInput($sDefs['popupPositionHorizontal'], $sProps, array( 'type' => 'hidden', 'class' => 'popup-prop' ));
											lsGetInput($sDefs['popupPositionVertical'], $sProps, array( 'type' => 'hidden', 'class' => 'popup-prop' ));
											?>
											<button type="button" class="button ls-popup-alignment-button" data-ls-su>
												<i class="dashicons dashicons-align-right"></i>
												<?php _e('Align Popup to...', 'LayerSlider') ?>
											</button>
											<div class="ls-su-data">
												<div class="ls-layer-alignment">
													<table class="ls-popup-position">
														<tr>
															<td data-move="top left"><i><?php _e('top left', 'LayerSlider') ?></i></td>
															<td data-move="top center"><i><?php _e('top center', 'LayerSlider') ?></i></td>
															<td data-move="top right"><i><?php _e('top right', 'LayerSlider') ?></i></td>
														</tr>
														<tr>
															<td data-move="middle left"><i><?php _e('middle left', 'LayerSlider') ?></i></td>
															<td data-move="middle center"><i><?php _e('middle center', 'LayerSlider') ?></i></td>
															<td data-move="middle right"><i><?php _e('middle right', 'LayerSlider') ?></i></td>
														</tr>
														<tr>
															<td data-move="bottom left"><i><?php _e('bottom left', 'LayerSlider') ?></i></td>
															<td data-move="bottom center"><i><?php _e('bottom center', 'LayerSlider') ?></i></td>
															<td data-move="bottom right"><i><?php _e('bottom right', 'LayerSlider') ?></i></td>
														</tr>
													</table>
												</div>
											</div>

										</div>
										<table>
											<tr>
												<td>
													<table>
														<tr>
															<td>
																<?php _e('Width', 'LayerSlider') ?>
															</td>
															<td>
																<?php
																	lsGetInput($sDefs['popupWidth'], $sProps, array( 'class' => 'mini ls-popup-width' ));
																?>
																px
															</td>
														</tr>
														<tr>
															<td>
																<?php _e('Height', 'LayerSlider') ?>
															</td>
															<td>
																<?php
																	lsGetInput($sDefs['popupHeight'], $sProps, array( 'class' => 'mini ls-popup-height' ));
																?>
																px
															</td>
														</tr>
														<tr>
															<td>
																<?php _e('Fit Screen Width', 'LayerSlider') ?>
															</td>
															<td>
																<?php
																				lsGetCheckbox($sDefs['popupFitWidth'], $sProps, array( 'class' => 'ls-popup-fit-width popup-prop' ));
																?>
															</td>
														</tr>
														<tr>
															<td>
																<?php _e('Fit Screen Height', 'LayerSlider') ?>
															</td>
															<td>
																<?php
																	lsGetCheckbox($sDefs['popupFitHeight'], $sProps, array( 'class' => 'ls-popup-fit-height popup-prop' ));
																?>
															</td>
														</tr>
													</table>
												</td>
												<td>
													<table>
														<tr>
															<td>
																<?php _e('Distance Left', 'LayerSlider') ?>
															</td>
															<td>
																<?php lsGetInput($sDefs['popupDistanceLeft'], $sProps, array( 'class' => 'mini ls-popup-distance-left popup-prop' )); ?>
																px
															</td>
														</tr>
														<tr>
															<td>
																<?php _e('Distance Right', 'LayerSlider') ?>
															</td>
															<td>
																<?php lsGetInput($sDefs['popupDistanceRight'], $sProps, array( 'class' => 'mini ls-popup-distance-right popup-prop' )); ?>
																px
															</td>
														</tr>
														<tr>
															<td>
																<?php _e('Distance Top', 'LayerSlider') ?>
															</td>
															<td>
																<?php lsGetInput($sDefs['popupDistanceTop'], $sProps, array( 'class' => 'mini ls-popup-distance-top popup-prop' )); ?>
																px
															</td>
														</tr>
														<tr>
															<td>
																<?php _e('Distance Bottom', 'LayerSlider') ?>
															</td>
															<td>
																<?php lsGetInput($sDefs['popupDistanceBottom'], $sProps, array( 'class' => 'mini ls-popup-distance-bottom popup-prop' )); ?>
																px
															</td>
														</tr>
													</table>
												</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</td>
					</tr>


					<tr><th colspan="3"><?php _e('Launch Popup', 'LayerSlider') ?></th></tr>
					<tr class="ls-popup-triggers">
						<td><?php echo $sDefs['popupShowOnTimeout']['name'] ?></td>
						<td><?php lsGetInput($sDefs['popupShowOnTimeout'], $sProps, array( 'class' => 'mini')) ?> <?php _e('seconds') ?></td>
						<td class="desc"><?php echo $sDefs['popupShowOnTimeout']['desc'] ?></td>
					</tr>
					<tr class="ls-popup-triggers">
						<td><?php echo $sDefs['popupShowOnIdle']['name'] ?></td>
						<td><?php lsGetInput($sDefs['popupShowOnIdle'], $sProps, array( 'class' => 'mini')) ?> <?php _e('seconds') ?></td>
						<td class="desc"><?php echo $sDefs['popupShowOnIdle']['desc'] ?></td>
					</tr>
					<?php
					lsOptionRow('input', $sDefs['popupShowOnScroll'], $sProps, array( 'class' => 'mini'), 'ls-popup-triggers' );
					lsOptionRow('checkbox', $sDefs['popupShowOnLeave'], $sProps, array(), 'ls-popup-triggers' );
					lsOptionRow('input', $sDefs['popupShowOnClick'], $sProps, array(), 'ls-popup-triggers' );
					?>

					<tr><th colspan="3"><?php _e('Close Popup', 'LayerSlider') ?></th></tr>
					<tr>
						<td><?php echo $sDefs['popupCloseOnTimeout']['name'] ?></td>
						<td><?php lsGetInput($sDefs['popupCloseOnTimeout'], $sProps, array( 'class' => 'mini')) ?> <?php _e('seconds') ?></td>
						<td class="desc"><?php echo $sDefs['popupCloseOnTimeout']['desc'] ?></td>
					</tr>
					<?php
					lsOptionRow('input', $sDefs['popupCloseOnScroll'], $sProps, array( 'class' => 'mini') );
					lsOptionRow('checkbox', $sDefs['popupCloseOnSliderEnd'], $sProps, array( 'class' => 'popup-prop') );
					?>

					<tr><th colspan="3"><?php _e('Repeat Control', 'LayerSlider') ?></th></tr>
					<?php lsOptionRow('checkbox', $sDefs['popupRepeat'], $sProps ); ?>
					<tr>
						<td><?php echo $sDefs['popupRepeatDays']['name'] ?></td>
						<td><?php lsGetInput($sDefs['popupRepeatDays'], $sProps, array( 'class' => 'mini')) ?> <?php _e('days') ?></td>
						<td class="desc"><?php echo $sDefs['popupRepeatDays']['desc'] ?></td>
					</tr>
					<?php lsOptionRow('checkbox', $sDefs['popupShowOnce'], $sProps ); ?>

					<tr><th colspan="3"><?php _e('Target Pages', 'LayerSlider') ?></th></tr>
					<tr class="ls-popup-include-pages">
						<td><?php _e('Include pages', 'LayerSlider') ?></td>
						<td colspan="2" class="ls-popup-target">
							<span>
								<?php lsGetCheckbox($sDefs['popupPagesAll'], $sProps, array( 'class' => 'ls-popup-include-all-pages' )); ?>
								<?php echo $sDefs['popupPagesAll']['name'] ?>
							</span>
							<span>
								<?php lsGetCheckbox($sDefs['popupPagesHome'], $sProps); ?>
								<?php echo $sDefs['popupPagesHome']['name'] ?>
							</span>
							<span>
								<?php lsGetCheckbox($sDefs['popupPagesPage'], $sProps); ?>
								<?php echo $sDefs['popupPagesPage']['name'] ?>
							</span>
							<span>
								<?php lsGetCheckbox($sDefs['popupPagesPost'], $sProps); ?>
								<?php echo $sDefs['popupPagesPost']['name'] ?>
							</span>
						</td>
					</tr>
					<tr class="ls-popup-include-custom-pages">
						<td><?php _e('Include custom pages', 'LayerSlider') ?></td>
						<td colspan="2"><?php lsGetInput($sDefs['popupPagesCustom'], $sProps, array( 'placeholder' => __('Comma separated list of page IDs, titles or slugs.') )); ?></td>
					</tr>
					<tr class="ls-popup-exclude-pages">
						<td><?php _e('Exclude pages') ?></td>
						<td colspan="2"><?php lsGetInput($sDefs['popupPagesExclude'], $sProps, array( 'placeholder' => __('Comma separated list of page IDs, titles or slugs.') )); ?></td>
					</tr>



					<tr><th colspan="3"><?php _e('Target Audience', 'LayerSlider') ?></th></tr>
					<tr>
						<td><?php _e('Show Popup for users', 'LayerSlider') ?></td>
						<td colspan="2" class="ls-popup-target">
							<span>
								<?php lsGetCheckbox($sDefs['popupRolesAdministrator'], $sProps); ?>
								<?php echo $sDefs['popupRolesAdministrator']['name'] ?>
							</span>
							<span>
								<?php lsGetCheckbox($sDefs['popupRolesEditor'], $sProps); ?>
								<?php echo $sDefs['popupRolesEditor']['name'] ?>
							</span>
							<span>
								<?php lsGetCheckbox($sDefs['popupRolesAuthor'], $sProps); ?>
								<?php echo $sDefs['popupRolesAuthor']['name'] ?>
							</span>
							<span>
								<?php lsGetCheckbox($sDefs['popupRolesContributor'], $sProps); ?>
								<?php echo $sDefs['popupRolesContributor']['name'] ?>
							</span>
							<span>
								<?php lsGetCheckbox($sDefs['popupRolesSubscriber'], $sProps); ?>
								<?php echo $sDefs['popupRolesSubscriber']['name'] ?>
							</span>
							<span>
								<?php lsGetCheckbox($sDefs['popupRolesCustomer'], $sProps); ?>
								<?php echo $sDefs['popupRolesCustomer']['name'] ?>
							</span>
							<span>
								<?php lsGetCheckbox($sDefs['popupRolesVisitor'], $sProps); ?>
								<?php echo $sDefs['popupRolesVisitor']['name'] ?>
							</span>
						</td>
					</tr>
					<?php lsOptionRow('checkbox', $sDefs['popupFirstTimeVisitor'], $sProps ); ?>



					<tr><th colspan="3"><?php _e('Modal Options', 'LayerSlider') ?></th></tr>
					<?php
					lsOptionRow('select', $sDefs['popupTransitionIn'], $sProps, array( 'class' => 'popup-prop' ) );
					lsOptionRow('input', $sDefs['popupDurationIn'], $sProps, array( 'class' => 'popup-prop') );
					lsOptionRow('input', $sDefs['popupDelayIn'], $sProps, array( 'class' => 'popup-prop') );
					lsOptionRow('select', $sDefs['popupTransitionOut'], $sProps, array( 'class' => 'popup-prop' ) );
					lsOptionRow('input', $sDefs['popupDurationOut'], $sProps, array( 'class' => 'popup-prop') );
					lsOptionRow('checkbox', $sDefs['popupStartSliderImmediately'], $sProps, array( 'class' => 'popup-prop') );
					lsOptionRow('select', $sDefs['popupResetOnClose'], $sProps, array( 'class' => 'popup-prop'));
					lsOptionRow('checkbox', $sDefs['popupShowCloseButton'], $sProps, array( 'class' => 'popup-prop') );
					lsOptionRow('input', $sDefs['popupCloseButtonStyle'], $sProps, array( 'class' => 'popup-prop') );
					?>


					<tr><th colspan="3"><?php _e('Overlay Options', 'LayerSlider') ?></th></tr>
					<?php
					lsOptionRow('checkbox', $sDefs['popupDisableOverlay'], $sProps );
					lsOptionRow('checkbox', $sDefs['popupOverlayClickToClose'], $sProps );
					lsOptionRow('input', $sDefs['popupOverlayBackground'], $sProps, array( 'class' => 'popup-prop ls-colorpicker minicolors-input' ) );
					lsOptionRow('select', $sDefs['popupOverlayTransitionIn'], $sProps, array( 'class' => 'popup-prop' ) );
					lsOptionRow('input', $sDefs['popupOverlayDurationIn'], $sProps, array( 'class' => 'popup-prop' ) );
					lsOptionRow('select', $sDefs['popupOverlayTransitionOut'], $sProps, array( 'class' => 'popup-prop' ) );
					lsOptionRow('input', $sDefs['popupOverlayDurationOut'], $sProps, array( 'class' => 'popup-prop' ) );

					?>

				</tbody>
				<?php endif ?>


				<!-- YourLogo -->
				<tbody>
					<tr>
						<td><?php echo $sDefs['yourLogoImage']['name'] ?></td>
						<td>
							<?php $sProps['yourlogo'] = !empty($sProps['yourlogo']) ? $sProps['yourlogo'] : null; ?>
							<?php $sProps['yourlogoId'] = !empty($sProps['yourlogoId']) ? $sProps['yourlogoId'] : null; ?>
							<input type="hidden" name="yourlogoId" value="<?php echo !empty($sProps['yourlogoId']) ? $sProps['yourlogoId'] : '' ?>">
							<input type="hidden" name="yourlogo" value="<?php echo !empty($sProps['yourlogo']) ? $sProps['yourlogo'] : '' ?>">
							<div class="ls-image ls-upload ls-yourlogo-upload not-set" data-l10n-set="<?php _e('Click to set', 'LayerSlider') ?>" data-l10n-change="<?php _e('Click to change', 'LayerSlider') ?>">
								<div><img src="<?php echo apply_filters('ls_get_thumbnail', $sProps['yourlogoId'], $sProps['yourlogo']) ?>" alt=""></div>
								<a href="#" class="dashicons dashicons-dismiss"></a>
							</div>
						</td>
						<td class="desc"><?php echo $sDefs['yourLogoImage']['desc'] ?></td>
					</tr>
					<tr>
						<td><?php echo $sDefs['yourLogoStyle']['name'] ?></td>
						<td colspan="2">
							<textarea name="yourlogostyle" cols="30" rows="10"><?php echo !empty($sProps['yourlogostyle']) ? $sProps['yourlogostyle'] : $sDefs['yourLogoStyle']['value'] ?></textarea>
						</td>
					</tr>
					<?php
					lsOptionRow('input', $sDefs['yourLogoLink'], $sProps );
					lsOptionRow('select', $sDefs['yourLogoTarget'], $sProps );
					?>
				</tbody>

				<!-- Transition Defaults -->
				<tbody>
					<tr><th colspan="3"><?php _e('Slide background defaults', 'LayerSlider') ?></th></tr>
					<?php
					lsOptionRow('select', $sDefs['slideBGSize'], $sProps );
					lsOptionRow('select', $sDefs['slideBGPosition'], $sProps );
					?>
					<tr><th colspan="3"><?php _e('Parallax defaults', 'LayerSlider') ?></th></tr>
					<?php
					lsOptionRow('input', $sDefs['parallaxSensitivity'], $sProps );
					lsOptionRow('select', $sDefs['parallaxCenterLayers'], $sProps );
					lsOptionRow('input', $sDefs['parallaxCenterDegree'], $sProps );
					lsOptionRow('checkbox', $sDefs['parallaxScrollReverse'], $sProps );
					?>
					<tr class="ls-advanced ls-hidden"><th colspan="3"><?php _e('Misc', 'LayerSlider') ?></th></tr>
					<?php
					lsOptionRow('input', $sDefs['forceLayersOutDuration'], $sProps );
					?>
				</tbody>

				<!-- Misc -->
				<tbody>
					<?php
					lsOptionRow('checkbox', $sDefs['relativeURLs'], $sProps );
					lsOptionRow('checkbox', $sDefs['useSrcset'], $sProps );
					lsOptionRow('checkbox', $sDefs['enhancedLazyLoad'], $sProps );
					lsOptionRow('checkbox', $sDefs['allowRestartOnResize'], $sProps );
					lsOptionRow('select', $sDefs['preferBlendMode'], $sProps );
					?>
					<tr>
						<td><?php _e('Slider preview image', 'LayerSlider') ?></td>
						<td>
							<?php $preview = !empty($slider['meta']['preview']) ? $slider['meta']['preview'] : null; ?>
							<?php $previewId = !empty($slider['meta']['previewId']) ? $slider['meta']['previewId'] : null; ?>
							<input type="hidden" name="previewId" value="<?php echo !empty($slider['meta']['previewId']) ? $slider['meta']['previewId'] : '' ?>">
							<input type="hidden" name="preview" value="<?php echo !empty($slider['meta']['preview']) ? $slider['meta']['preview'] : '' ?>">
							<div class="ls-image ls-slider-preview ls-upload" data-l10n-set="<?php _e('Click to set', 'LayerSlider') ?>" data-l10n-change="<?php _e('Click to change', 'LayerSlider') ?>">
								<div><img src="<?php echo apply_filters('ls_get_thumbnail', $previewId, $preview) ?>" alt=""></div>
								<a href="#" class="dashicons dashicons-dismiss"></a>
							</div>
						</td>
						<td class="desc"><?php _e('The preview image you can see in your list of sliders.', 'LayerSlider') ?></td>
					</tr>
				</tbody>

			</table>
		</div>
		<div class="clear"></div>
	</div>
</div>
