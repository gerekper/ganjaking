<?php
if( 'full' === $membership_type ){
	/**
	 * Dashboard template: Tools Functions
	 *
	 * Manage support tickets, grant support-staff access and view System
	 * configuration.
	 *
	 * Following variables are passed into the template:
	 * - $urls
	 * - $whitelabel_settings
	 * - $analytics_enabled
	 * - $analytics_role
	 *
	 * @since   4.0.0
	 * @package WPMUDEV_Dashboard
	 */

	// Render the page header section.
	$page_title = __( 'Tools', 'wpmudev' );
	$page_slug  = 'tools';
	$this->render_sui_header( $page_title, $page_slug );

	/** @var WPMUDEV_Dashboard_Sui $this */
	/** @var WPMUDEV_Dashboard_Sui_Page_Urls $urls */
	/** @var bool $analytics_enabled */
	/** @var string $analytics_role */
	/** @var array $whitelabel_settings */
	/** @var array $analytics_metrics */

	?>

	<?php if ( isset( $_GET['success-action'] ) ): // wpcs csrf ok. ?>
		<?php switch ( $_GET['success-action'] ) { // wpcs csrf ok.
			case 'analytics-setup':
				?>
				<div class="sui-notice-top sui-notice-success sui-can-dismiss">
					<div class="sui-notice-content">
						<p><?php esc_html_e( 'Analytics configuration has been saved.', 'wpmudev' ); ?></p>
					</div>
					<span class="sui-notice-dismiss">
						<a role="button" aria-label="Dismiss" class="sui-icon-check"></a>
					</span>
				</div>
				<?php
				break;
			case 'whitelabel-setup':
				?>
				<div class="sui-notice-top sui-notice-success sui-can-dismiss">
					<div class="sui-notice-content">
						<p><?php esc_html_e( 'Whitelabel configuration has been saved.', 'wpmudev' ); ?></p>
					</div>
					<span class="sui-notice-dismiss">
						<a role="button" aria-label="Dismiss" class="sui-icon-check"></a>
					</span>
				</div>
				<?php
				break;
			default:
				break;
		} ?>
	<?php endif; ?>

	<?php if ( isset( $_GET['failed-action'] ) ): // wpcs csrf ok. ?>
		<?php switch ( $_GET['failed-action'] ) { // wpcs csrf ok.
			case 'analytics-setup':
				?>
				<div class="sui-notice-top sui-notice-error sui-can-dismiss">
					<div class="sui-notice-content">
						<p><?php esc_html_e( 'Failed save analytics configuration.', 'wpmudev' ); ?></p>
					</div>
					<span class="sui-notice-dismiss">
						<a role="button" aria-label="Dismiss" class="sui-icon-check"></a>
					</span>
				</div>
				<?php
				break;
			default:
				break;
		} ?>
	<?php endif; ?>

	<div class="sui-row-with-sidenav">

		<div class="sui-sidenav">

			<ul class="sui-vertical-tabs sui-sidenav-hide-md">

				<li class="sui-vertical-tab">
					<a href="<?php echo esc_url( $urls->tools_url ) . '#analytics'; ?>">
						<?php esc_html_e( 'Analytics', 'wpmudev' ); ?>
					</a>

				</li>

				<li class="sui-vertical-tab">
					<a href="<?php echo esc_url( $urls->tools_url ) . '#whitelabel'; ?>">
						<?php esc_html_e( 'Whitelabel', 'wpmudev' ); ?>
					</a>
				</li>

			</ul>

			<div class="sui-sidenav-hide-lg">

				<select class="sui-mobile-nav" style="display: none;">
					<option value="#analytics" selected="selected"><?php esc_html_e( 'Analytics', 'wpmudev' ); ?></option>
					<option value="#whitelabel"><?php esc_html_e( 'Whitelabel', 'wpmudev' ); ?></option>
				</select>

			</div>

		</div>

		<div class="sui-box js-sidenav-content" id="analytics" style="display: none;">

			<form method="POST" action="<?php echo esc_url( $urls->tools_url ) . '#analytics'; ?>">

				<input type="hidden" name="action" value="analytics-setup"/>

				<?php wp_nonce_field( 'analytics-setup', 'hash' ); ?>

				<div class="sui-box-header">
					<h2 class="sui-box-title"><?php esc_html_e( 'Analytics', 'wpmudev' ); ?></h2>
				</div>

				<?php if ( $analytics_enabled && is_wpmudev_member() ) : ?>

					<?php
					$role_names = wp_roles()->get_names();
					$role_name  = isset( $role_names[ $analytics_role ] ) ? $role_names[ $analytics_role ] : 'Administrator';
					?>

					<div class="sui-box-body">

						<p><?php esc_html_e( "Add basic analytics tracking that doesn't require any third party integration, and display the data in the WordPress Admin Dashboard area.", 'wpmudev' ); ?></p>

						<div class="sui-notice sui-notice-info" style="margin-bottom:0;">

							<p class="sui-notice-content">
								<?php printf(
									esc_html__( 'Analytics are now being tracked and the widget is being displayed to %s and above in their Dashboard area', 'wpmudev' ),
									esc_html( $role_name )
								); ?>
							</p>
						</div>
						<span class="sui-description" style="margin: 10px 0 30px 0;"><?php esc_html_e( 'Note: IP addresses are anonymized when stored and meet GDPR recommendations.', 'wpmudev' ); ?></span>
						<div class="sui-box-settings-row">

							<div class="sui-box-settings-col-1">

								<span class="sui-settings-label"><?php esc_html_e( 'User Role', 'wpmudev' ); ?></span>

								<span class="sui-description"><?php esc_html_e( 'Choose which minimum user roles you want to make the analytics widget available to.', 'wpmudev' ); ?></span>

							</div>

							<div class="sui-box-settings-col-2">

								<div class="sui-form-field sui-input-md">

									<select name="analytics_role">

										<?php $roles = wp_roles()->roles;

										foreach ( $roles as $key => $site_role ) {
											// core roles define level_X caps, that's what we'll use to check permissions.
											if ( ! isset( $site_role['capabilities']['level_0'] ) ) {
												continue;
											} ?>
											<option <?php selected( $analytics_role, $key ); ?> value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $site_role['name'] ); ?></option>
										<?php } ?>

									</select>

								</div>

							</div>

						</div>

						<div class="sui-box-settings-row">

							<div class="sui-box-settings-col-1">

								<span class="sui-settings-label"><?php esc_html_e( 'Metric Types', 'wpmudev' ); ?></span>

								<span class="sui-description"><?php esc_html_e( 'Select the types of analytics the selected User Roles will see in their WordPress Admin area.', 'wpmudev' ); ?></span>

							</div>

							<div class="sui-box-settings-col-2">

								<div class="sui-form-field sui-input-md">

									<label for="analytics_metrics-pageviews" class="sui-checkbox sui-checkbox-stacked">
										<input type="checkbox"
											id="analytics_metrics-pageviews"
											name="analytics_metrics[]"
											value="pageviews"
											<?php checked( in_array( 'pageviews', $analytics_metrics, true ) ); ?>>
										<span aria-hidden="true"></span>
										<span><?php esc_html_e( 'Page views', 'wpmudev' ); ?></span>
									</label>
									<label for="analytics_metrics-unique_pageviews" class="sui-checkbox sui-checkbox-stacked">
										<input type="checkbox"
											id="analytics_metrics-unique_pageviews"
											name="analytics_metrics[]"
											value="unique_pageviews"
											<?php checked( in_array( 'unique_pageviews', $analytics_metrics, true ) ); ?>>
										<span aria-hidden="true"></span>
										<span><?php esc_html_e( 'Unique page views', 'wpmudev' ); ?></span>
									</label>
									<label for="analytics_metrics-page_time" class="sui-checkbox sui-checkbox-stacked">
										<input type="checkbox"
											id="analytics_metrics-page_time"
											name="analytics_metrics[]"
											value="page_time"
											<?php checked( in_array( 'page_time', $analytics_metrics, true ) ); ?>>
										<span aria-hidden="true"></span>
										<span><?php esc_html_e( 'Avg time on page', 'wpmudev' ); ?></span>
									</label>
									<label for="analytics_metrics-bounce_rate" class="sui-checkbox sui-checkbox-stacked">
										<input type="checkbox"
											id="analytics_metrics-bounce_rate"
											name="analytics_metrics[]"
											value="bounce_rate"
											<?php checked( in_array( 'bounce_rate', $analytics_metrics, true ) ); ?>>
										<span aria-hidden="true"></span>
										<span><?php esc_html_e( 'Bounce rate', 'wpmudev' ); ?></span>
									</label>
									<label for="analytics_metrics-exit_rate" class="sui-checkbox sui-checkbox-stacked">
										<input type="checkbox"
											id="analytics_metrics-exit_rate"
											name="analytics_metrics[]"
											value="exit_rate"
											<?php checked( in_array( 'exit_rate', $analytics_metrics, true ) ); ?>>
										<span aria-hidden="true"></span>
										<span><?php esc_html_e( 'Exit rate', 'wpmudev' ); ?></span>
									</label>
									<label for="analytics_metrics-gen_time" class="sui-checkbox sui-checkbox-stacked">
										<input type="checkbox"
											id="analytics_metrics-gen_time"
											name="analytics_metrics[]"
											value="gen_time"
											<?php checked( in_array( 'gen_time', $analytics_metrics, true ) ); ?>>
										<span aria-hidden="true"></span>
										<span><?php esc_html_e( 'Avg generation time', 'wpmudev' ); ?></span>
									</label>

								</div>

							</div>

						</div>

					</div>

					<div class="sui-box-footer">

						<button type="submit"
								name="status"
								value="deactivate"
								class="sui-button sui-button-ghost">

							<span class="sui-loading-text">
								<i class="sui-icon-power-on-off" aria-hidden="true"></i>
								<?php esc_html_e( 'Deactivate', 'wpmudev' ); ?>
							</span>

							<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>

						</button>

						<div class="sui-actions-right">

							<button type="submit" class="sui-button sui-button-blue" name="status" value="settings">

								<span class="sui-loading-text">
									<i class="sui-icon-save" aria-hidden="true"></i>
									<?php esc_html_e( 'SAVE CHANGES', 'wpmudev' ); ?>
								</span>

								<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>

							</button>

						</div>

					</div>

				<?php else : ?>

					<div class="sui-message sui-message-lg">

						<img src="<?php echo esc_url( WPMUDEV_Dashboard::$site->plugin_url . 'assets/images/devman-analytics.png' ); ?>"
							srcset="<?php echo esc_url( WPMUDEV_Dashboard::$site->plugin_url . 'assets/images/devman-analytics.png' ); ?> 1x, <?php echo esc_url( WPMUDEV_Dashboard::$site->plugin_url . 'assets/images/devman-analytics@2x.png' ); ?> 2x"
							alt="Analytics"
							class="sui-image"
							aria-hidden="true" />

						<p><?php esc_html_e( "Add basic analytics tracking that doesn't require any third party integration, and display the data in the WordPress Admin Dashboard area.", 'wpmudev' ); ?></p>

						<button type="submit"
								name="status"
								value="activate"
								class="sui-button sui-button-blue"
							<?php echo( ! is_wpmudev_member() ? 'disabled="disabled"' : '' ); ?>>

							<span class="sui-loading-text"><?php esc_html_e( 'Activate', 'wpmudev' ); ?></span>

							<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>

						</button>

					</div>

				<?php endif; ?>

			</form>

		</div>

		<div class="sui-box js-sidenav-content" id="whitelabel" style="display: none;">

			<div class="sui-box-header">
				<h2 class="sui-box-title"><?php esc_html_e( 'Whitelabel', 'wpmudev' ); ?></h2>
			</div>

			<form method="POST" action="<?php echo esc_url( $urls->tools_url ) . '#whitelabel'; ?>">

				<input type="hidden" name="action" value="whitelabel-setup"/>

				<?php wp_nonce_field( 'whitelabel-setup', 'hash' ); ?>

				<?php if ( $whitelabel_settings['enabled'] && is_wpmudev_member() ) : ?>

					<div class="sui-box-body">

						<p><?php esc_html_e( 'Remove WPMU DEV branding from all our plugins and replace it with your own branding for your clients.', 'wpmudev' ); ?></p>

						<?php
						// SETTING: WPMU DEV Branding ?>
						<div class="sui-box-settings-row">

							<div class="sui-box-settings-col-1">
								<span class="sui-settings-label"><?php esc_html_e( 'WPMU DEV branding', 'wpmudev' ); ?></span>
								<span class="sui-description"><?php esc_html_e( 'Remove Super Hero images from our plugins entirely, and upload your own logo for the dashboard section of each plugin.', 'wpmudev' ); ?></span>
							</div>

							<div class="sui-box-settings-col-2">

								<div class="sui-side-tabs js-tabs-checkbox"
									data-checkbox="branding_enabled">

									<div class="sui-tabs-menu">

										<label for="wpmudev-branding-default"
											class="sui-tab-item<?php echo esc_attr( $whitelabel_settings['branding_enabled'] ? '' : ' active' ); ?>">
											<input type="radio"
												name="branding_enabled"
												value="0"
												id="wpmudev-branding-default"
												data-checked="false"/>
											<?php esc_html_e( 'Default', 'wpmudev' ); ?>
										</label>

										<label for="wpmudev-branding-custom"
											class="sui-tab-item<?php echo esc_attr( $whitelabel_settings['branding_enabled'] ? ' active' : '' ); ?>">
											<input type="radio"
												name="branding_enabled"
												value="1"
												id="wpmudev-branding-custom"
												data-checked="true"
												data-tab-menu="wpmudev-branding-upload"
												<?php checked( $whitelabel_settings['branding_enabled'] ); ?> />
											<?php esc_html_e( 'Custom', 'wpmudev' ); ?>
										</label>

									</div>

									<div class="sui-tabs-content">

										<div id="wpmudev-branding-upload"
											class="sui-tab-content sui-tab-boxed<?php echo esc_attr( $whitelabel_settings['branding_enabled'] ? ' active' : '' ); ?>"
											data-tab-content="wpmudev-branding-upload">

											<div class="sui-form-field">

												<label class="sui-label"><?php esc_html_e( 'Upload Logo (optional)', 'wpmudev' ); ?></label>

												<div id="branding_upload"
													class="sui-upload <?php echo esc_attr( $whitelabel_settings['branding_image'] ? 'sui-has_file' : '' ); ?>">

													<div class="sui-hidden">
														<input type="url"
															name="branding_image"
															id="branding_image"
															readonly="readonly"
															value="<?php echo esc_attr( $whitelabel_settings['branding_image'] ); ?>">
													</div>

													<div class="sui-upload-image" aria-hidden="true">
														<div class="sui-image-mask"></div>
														<div role="button"
															class="sui-image-preview wp-browse-media"
															data-frame-title="<?php esc_html_e( 'Select or Upload Media for Branding Logo', 'wpmudev' ); ?>"
															data-button-text="<?php esc_html_e( 'Use this as Branding Logo', 'wpmudev' ); ?>"
															data-input-id="branding_image"
															data-preview-id="branding_image_preview"
															data-upload-wrapper-id="branding_upload"
															data-text-id="branding_image_text"
															id="branding_image_preview"
															style="background-image: url('<?php echo esc_url( $whitelabel_settings['branding_image'] ); ?>');">
														</div>
													</div>

													<button class="sui-upload-button wp-browse-media"
															data-frame-title="<?php esc_html_e( 'Select or Upload Media for Branding Logo', 'wpmudev' ); ?>"
															data-button-text="<?php esc_html_e( 'Use this as Branding Logo', 'wpmudev' ); ?>"
															data-input-id="branding_image"
															data-preview-id="branding_image_preview"
															data-upload-wrapper-id="branding_upload"
															data-text-id="branding_image_text"
													>
														<i class="sui-icon-upload-cloud" aria-hidden="true"></i> <?php esc_html_e( 'Upload image', 'wpmudev' ); ?>
													</button>

													<div class="sui-upload-file">

														<span id="branding_image_text"><?php echo esc_url( $whitelabel_settings['branding_image'] ); ?></span>

														<button class="js-clear-image" aria-label="<?php esc_attr_e( 'Remove', 'wpmudev' ); ?>">
															<i class="sui-icon-close" aria-hidden="true"></i>
														</button>

													</div>

												</div>

												<span class="sui-description"><?php esc_html_e( 'Maximum height and width of logo should be 192px and 172px respectively. This Logo will appear only in the dashboard section of each WPMU DEV plugin you have installed that supports this feature.', 'wpmudev' ); ?></span>

											</div>
											<?php if( is_multisite() ): ?>
												<div class="sui-form-field">
													<label class="sui-toggle">
														<input
															type="checkbox"
															name="branding_enabled_subsite"
															value="1"
															id="branding_enabled_subsite"
															<?php checked( $whitelabel_settings['branding_enabled_subsite'] ); ?>
														/>
														<span class="sui-toggle-slider"></span>
													</label>
													<label for="branding_enabled_subsite" class="sui-toggle-label"><?php esc_html_e( 'Allow Subsite Admins to override', 'wpmudev' ); ?></label>
													<span class="sui-description"><?php esc_html_e( 'By default, subsites will inherit the main branding set here. With this setting enabled, we will use the logo set in the Customizer Menu as the branding across plugins.', 'wpmudev' ); ?></span>
												</div>
											<?php endif; ?>
										</div>

									</div>

								</div>

							</div>

						</div>

						<?php
						// SETTING: Footer Text ?>
						<div class="sui-box-settings-row">

							<div class="sui-box-settings-col-1">
								<span class="sui-settings-label"><?php esc_html_e( 'Footer Text', 'wpmudev' ); ?></span>
								<span class="sui-description"><?php esc_html_e( 'Remove or replace the default WPMU DEV footer text from all plugin screens.', 'wpmudev' ); ?></span>
							</div>

							<div class="sui-box-settings-col-2">

								<div class="sui-side-tabs js-tabs-checkbox" data-checkbox="footer_enabled">

									<div class="sui-tabs-menu">

										<label for="wpmudev-footer-default"
											class="sui-tab-item<?php echo esc_attr( $whitelabel_settings['footer_enabled'] ? '' : ' active' ); ?>">
											<input type="radio"
												name="footer_enabled"
												value="0"
												id="wpmudev-footer-default"
												data-checked="false"/>
											<?php esc_html_e( 'Default', 'wpmudev' ); ?>
										</label>

										<label for="wpmudev-footer-custom"
											class="sui-tab-item<?php echo esc_attr( $whitelabel_settings['footer_enabled'] ? ' active' : '' ); ?>">
											<input type="radio"
												name="footer_enabled"
												value="1"
												id="wpmudev-footer-custom"
												data-checked="true"
												data-tab-menu="wpmudev-footer-upload"
												<?php checked( $whitelabel_settings['footer_enabled'] ); ?> />
											<?php esc_html_e( 'Custom', 'wpmudev' ); ?>
										</label>

									</div>

									<div class="sui-tabs-content">

										<div id="wpmudev-branding-footer"
											class="sui-tab-content sui-tab-boxed<?php echo esc_attr( $whitelabel_settings['footer_enabled'] ? ' active' : '' ); ?>"
											data-tab-content="wpmudev-footer-upload">

											<div class="sui-form-field">

												<label class="sui-label" for="footer_text"><?php esc_html_e( 'Footer text', 'wpmudev' ); ?></label>

												<input type="text"
													name="footer_text"
													value="<?php echo esc_attr( $whitelabel_settings['footer_text'] ); ?>"
													placeholder="<?php esc_html_e( 'Your brand name', 'wpmudev' ); ?>"
													id="footer_text"
													class="sui-form-control"/>

												<span class="sui-description"><?php esc_html_e( 'Leave the field empty to hide the footer completely.', 'wpmudev' ); ?></span>

											</div>

										</div>

									</div>

								</div>

							</div>

						</div>

						<?php
						// SETTING: Documentation Links ?>
						<div class="sui-box-settings-row">

							<div class="sui-box-settings-col-1">
								<span class="sui-settings-label"><?php esc_html_e( 'Documentation Links', 'wpmudev' ); ?></span>
								<span class="sui-description"><?php esc_html_e( 'Remove the Documentations button from the top of WPMU DEV plugin settings screens.', 'wpmudev' ); ?></span>
							</div>

							<div class="sui-box-settings-col-2">

								<div class="sui-side-tabs js-tabs-checkbox"
									data-checkbox="doc_links_enabled">

									<div class="sui-tabs-menu">

										<label for="wpmudev-documentation-links-show"
											class="sui-tab-item<?php echo esc_attr( $whitelabel_settings['doc_links_enabled'] ? '' : ' active' ); ?>">
											<input type="radio"
												name="doc_links_enabled"
												value="0"
												id="wpmudev-documentation-links-show"
												data-checked="false"/>
											<?php esc_html_e( 'Show', 'wpmudev' ); ?>
										</label>

										<label for="wpmudev-documentation-links-hide"
											class="sui-tab-item<?php echo esc_attr( $whitelabel_settings['doc_links_enabled'] ? ' active' : '' ); ?>">
											<input type="radio"
												name="doc_links_enabled"
												value="1"
												id="wpmudev-documentation-links-hide"
												data-checked="true"
												<?php checked( $whitelabel_settings['doc_links_enabled'] ); ?> />
											<?php esc_html_e( 'Hide', 'wpmudev' ); ?>
										</label>

									</div>

								</div>

							</div>

						</div>

					</div>

				<?php else: ?>

					<div class="sui-message sui-message-lg">

						<img src="<?php echo esc_url( WPMUDEV_Dashboard::$site->plugin_url . 'assets/images/devman-whitelabel.png' ); ?>"
							srcset="<?php echo esc_url( WPMUDEV_Dashboard::$site->plugin_url . 'assets/images/devman-whitelabel.png' ); ?> 1x, <?php echo esc_url( WPMUDEV_Dashboard::$site->plugin_url . 'assets/images/devman-whitelabel@2x.png' ); ?> 2x"
							alt="dev-man"
							class="sui-image"
							aria-hidden="true" />

						<p><?php esc_html_e( 'Remove WPMU DEV branding from all our plugins and replace it with your own branding for your clients.', 'wpmudev' ); ?></p>

						<button type="submit"
								name="status"
								value="activate"
								class="sui-button sui-button-blue"
							<?php echo( ! is_wpmudev_member() ? 'disabled="disabled"' : '' ); ?>>

							<span class="sui-loading-text"><?php esc_html_e( 'Activate', 'wpmudev' ); ?></span>

							<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>

						</button>

					</div>

				<?php endif; ?>



				<?php if ( $whitelabel_settings['enabled'] && is_wpmudev_member() ) : ?>

					<div class="sui-box-footer">

						<button type="submit"
								name="status"
								value="deactivate"
								class="sui-button sui-button-ghost">

							<span class="sui-loading-text">
								<i class="sui-icon-power-on-off" aria-hidden="true"></i>
								<?php esc_html_e( 'Deactivate', 'wpmudev' ); ?>
							</span>

							<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>

						</button>

						<div class="sui-actions-right">

							<button type="submit"
									name="status"
									value="settings"
									class="sui-button sui-button-blue">

								<span class="sui-loading-text">
									<i class="sui-icon-save" aria-hidden="true"></i>
									<?php esc_html_e( 'Save Changes', 'wpmudev' ); ?>
								</span>

								<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>

							</button>

						</div>

					</div>

				<?php endif; ?>

			</form>

		</div>

	</div>

	<?php $this->load_sui_template( 'element-last-refresh', array(), true ); ?>
	<?php $this->load_sui_template( 'footer', array(), true );
} else {
	$this->render_upgrade_box( $membership_type );
}
