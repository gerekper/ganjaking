<?php
/**
 * Bulk compress page.
 *
 * @since 2.9.0
 * @package Smush\App\Pages
 */

namespace Smush\App\Pages;

use Smush\App\Abstract_Summary_Page;
use Smush\App\Interface_Page;
use Smush\Core\Array_Utils;
use Smush\Core\Core;
use Smush\Core\Settings;
use Smush\Core\Stats\Global_Stats;
use WP_Smush;
use Smush\Core\Backups\Backups;

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Bulk
 */
class Bulk extends Abstract_Summary_Page implements Interface_Page {
	/**
	 * Function triggered when the page is loaded before render any content.
	 */
	public function on_load() {
		parent::on_load();

		// If a free user, update the limits.
		if ( ! WP_Smush::is_pro() ) {
			// Reset transient.
			Core::check_bulk_limit( true );
			add_action( 'smush_setting_column_tag', array( $this, 'add_pro_tag' ) );
		}
		add_action( 'smush_setting_column_tag', array( $this, 'add_lossy_new_tag' ) );

		add_action( 'smush_setting_column_right_inside', array( $this, 'settings_desc' ), 10, 2 );
		add_action( 'smush_setting_column_right_inside', array( $this, 'auto_smush' ), 15, 2 );
		add_action( 'smush_setting_column_right_outside', array( $this, 'image_sizes' ), 15, 2 );
		add_action( 'smush_setting_column_right_additional', array( $this, 'resize_settings' ), 20 );
		add_action( 'smush_setting_column_right_outside', array( $this, 'full_size_options' ), 20, 2 );
		add_action( 'smush_setting_column_right_outside', array( $this, 'scale_options' ), 20, 2 );
		add_action( 'wp_smush_render_setting_row', array( $this, 'set_background_email_setting_visibility' ) );
	}

	public function enqueue_scripts( $hook ) {
		parent::enqueue_scripts( $hook );

		$this->enqueue_lib_scanner_scripts();
	}

	protected function enqueue_lib_scanner_scripts() {
		wp_enqueue_script(
			'smush-library-scanner',
			WP_SMUSH_URL . 'app/assets/js/smush-library-scanner.min.js',
			array( 'wp-i18n' ),
			WP_SMUSH_VERSION,
			true
		);

		wp_localize_script( 'smush-library-scanner', 'mediaLibraryScan', array(
			'nonce' => wp_create_nonce( 'wp_smush_media_library_scanner' ),
		) );
	}

	/**
	 * Register meta boxes.
	 */
	public function register_meta_boxes() {
		if ( ! is_network_admin() ) {
			$this->add_meta_box(
				'recheck-images-notice',
				null,
				array( $this, 'recheck_images_notice_meta_box' ),
				null,
				null,
				'main',
				array(
					'box_class'         => 'sui-box wp-smush-recheck-images-notice-box sui-hidden',
					'box_content_class' => false,
				)
			);
		}
		parent::register_meta_boxes();

		if ( ! is_network_admin() ) {
			$this->add_meta_box(
				'bulk',
				__( 'Bulk Smush', 'wp-smushit' ),
				array( $this, 'bulk_smush_metabox' ),
				null,
				null,
				'main',
				array(
					'box_class' => 'sui-box bulk-smush-wrapper',
				)
			);
		}

		$class = WP_Smush::is_pro() ? 'wp-smush-pro' : '';
		$this->add_meta_box(
			'bulk-settings',
			__( 'Settings', 'wp-smushit' ),
			array( $this, 'bulk_settings_meta_box' ),
			null,
			array( $this, 'common_meta_box_footer' ),
			'main',
			array(
				'box_class' => "sui-box smush-settings-wrapper {$class}",
			)
		);

		$this->modals['restore-images'] = array();
	}

	/**
	 * Prints Dimensions required for Resizing
	 *
	 * @param string $name Setting name.
	 */
	public function resize_settings( $name = '' ) {
		// Add only to full size settings.
		if ( 'resize' !== $name ) {
			return;
		}

		// Dimensions.
		$resize_sizes = $this->settings->get_setting(
			'wp-smush-resize_sizes',
			array(
				'width'  => '',
				'height' => '',
			)
		);

		// Get max dimensions.
		$max_sizes = WP_Smush::get_instance()->core()->get_max_image_dimensions();

		$setting_status = $this->settings->get( 'resize' );
		?>
		<div tabindex="0" class="sui-toggle-content">
			<div class="sui-border-frame<?php echo $setting_status ? '' : ' sui-hidden'; ?>" id="smush-resize-settings-wrap" style="margin-bottom: 10px;">
				<div class="sui-row">
					<div class="sui-col-md-6">
						<div class="sui-form-field">
							<label aria-labelledby="wp-smush-label-max-width" for="<?php echo 'wp-smush-' . esc_attr( $name ) . '_width'; ?>" class="sui-label">
								<?php esc_html_e( 'Max width', 'wp-smushit' ); ?>
							</label>
							<input aria-required="true" type="number" class="sui-form-control wp-smush-resize-input"
								aria-describedby="wp-smush-resize-note"
								id="<?php echo 'wp-smush-' . esc_attr( $name ) . '_width'; ?>"
								name="<?php echo 'wp-smush-' . esc_attr( $name ) . '_width'; ?>"
								value="<?php echo isset( $resize_sizes['width'] ) && ! empty( $resize_sizes['width'] ) ? absint( $resize_sizes['width'] ) : 2560; ?>">
						</div>
					</div>
					<div class="sui-col-md-6">
						<div class="sui-form-field">
							<label aria-labelledby="wp-smush-label-max-height" for="<?php echo 'wp-smush-' . esc_attr( $name ) . '_height'; ?>" class="sui-label">
								<?php esc_html_e( 'Max height', 'wp-smushit' ); ?>
							</label>
							<input aria-required="true" type="number" class="sui-form-control wp-smush-resize-input"
								aria-describedby="wp-smush-resize-note"
								id="<?php echo 'wp-smush-' . esc_attr( $name ) . '_height'; ?>"
								name="<?php echo 'wp-smush-' . esc_attr( $name ) . '_height'; ?>"
								value="<?php echo isset( $resize_sizes['height'] ) && ! empty( $resize_sizes['height'] ) ? absint( $resize_sizes['height'] ) : 2560; ?>">
						</div>
					</div>
				</div>
				<div class="sui-description" id="wp-smush-resize-note">
					<?php
					printf( /* translators: %1$s: strong tag, %2$d: max width size, %3$s: tag, %4$d: max height size, %5$s: closing strong tag  */
						esc_html__( 'Currently, your largest image size is set at %1$s%2$dpx wide %3$s %4$dpx high%5$s.', 'wp-smushit' ),
						'<strong>',
						esc_html( $max_sizes['width'] ),
						'&times;',
						esc_html( $max_sizes['height'] ),
						'</strong>'
					);
					?>
					<div class="sui-notice sui-notice-info wp-smush-update-width sui-no-margin-bottom sui-hidden">
						<div class="sui-notice-content">
							<div class="sui-notice-message">
								<i class="sui-notice-icon sui-icon-info sui-md" aria-hidden="true"></i>
								<p><?php esc_html_e( "Just to let you know, the width you've entered is less than your largest image and may result in pixelation.", 'wp-smushit' ); ?></p>
							</div>
						</div>
					</div>
					<div class="sui-notice sui-notice-info wp-smush-update-height sui-no-margin-bottom sui-hidden">
						<div class="sui-notice-content">
							<div class="sui-notice-message">
								<i class="sui-notice-icon sui-icon-info sui-md" aria-hidden="true"></i>
								<p><?php esc_html_e( 'Just to let you know, the height you’ve entered is less than your largest image and may result in pixelation.', 'wp-smushit' ); ?></p>
							</div>
						</div>
					</div>
				</div>
			</div>
			<span class="sui-description">
				<?php
				printf( /* translators: %s: link to gifgifs.com */
					esc_html__(
						'Note: Image resizing happens automatically when you upload attachments. To support retina devices, we recommend using 2x the dimensions of your image size. Animated GIFs will not be resized as they will lose their animation, please use a tool such as %s to resize then re-upload.',
						'wp-smushit'
					),
					'<a href="http://gifgifs.com/resizer/" target="_blank">http://gifgifs.com/resizer/</a>'
				);
				?>
			</span>
		</div>
		<?php
	}

	/**
	 * Show additional descriptions for settings.
	 *
	 * @param string $setting_key Setting key.
	 */
	public function settings_desc( $setting_key = '' ) {
		if ( empty( $setting_key ) || ! in_array(
			$setting_key,
			array( 'resize', 'original', 'strip_exif', 'png_to_jpg', 'background_email' ),
			true
		) ) {
			return;
		}

		if ( 'png_to_jpg' === $setting_key ) {
			?>
			<div class="sui-toggle-content">
				<div class="sui-notice sui-notice-info" style="margin-top: 10px">
					<div class="sui-notice-content">
						<div class="sui-notice-message smush-png2jpg-setting-note">
							<i class="sui-notice-icon sui-icon-info sui-md" aria-hidden="true"></i>
							<p>
								<?php
									/* translators: 1: <strong> 2: </strong> */
									printf( esc_html__( 'Note: Any PNGs with transparency will be ignored. Smush will only convert PNGs if it results in a smaller file size. The original PNG file will be deleted, and the resulting file will have a new filename and extension (JPEG). %1$sAny hard-coded URLs on your site that contain the original PNG filename will need to be updated manually.%2$s', 'wp-smushit' ), '<strong>', '</strong>' ); ?>
								<br/>
								<span>
									<?php
										/* translators: 1: <strong> 2: </strong> */
										printf( esc_html__( '%1$sBackup original images%2$s must be enabled if you wish to retain the original PNG image as a backup.', 'wp-smushit' ), '<strong>', '</strong>' );
									?>
								</span>
							</p>
						</div>
					</div>
				</div>
			</div>
			<?php
			return;
		}

		global $wp_version;

		?>
		<span class="sui-description sui-toggle-description" id="<?php echo esc_attr( $setting_key . '-desc' ); ?>">
			<?php
			switch ( $setting_key ) {
				case 'resize':
					if ( version_compare( $wp_version, '5.2.999', '>' ) ) {
						esc_html_e( 'As of WordPress 5.3, large image uploads are resized down to a specified max width and height. If you require images larger than 2560px, you can override this setting here.', 'wp-smushit' );
					} else {
						esc_html_e( 'Save a ton of space by not storing over-sized images on your server. Set a maximum height and width for all images uploaded to your site so that any unnecessarily large images are automatically resized before they are added to the media gallery. This setting does not apply to images smushed using Directory Smush feature.', 'wp-smushit' );
					}
					break;
				case 'original':
					esc_html_e( 'By default, WordPress will only compress the generated attachments when you upload images, not the original ones. Enable this feature to compress the original images.', 'wp-smushit' );
					break;
				case 'strip_exif':
					esc_html_e(
						'Note: This data adds to the size of the image. While this information might be important to photographers, it’s unnecessary for most users and safe to remove.',
						'wp-smushit'
					);
					break;
				case 'background_email':
					$bg_optimization = WP_Smush::get_instance()->core()->mod->bg_optimization;
					if ( $bg_optimization->can_use_background() ) {
						/* translators: %s: Email address. */
						$bg_email_desc = sprintf( __( 'You will receive an email at <strong>%s</strong> when the bulk smush has completed.', 'wp-smushit' ), $bg_optimization->get_mail_recipient() );
					} else {
						$bulk_upgrade_url = $this->get_utm_link(
							array(
								'utm_campaign' => 'smush_bulk_smush_BO_email_toggle',
							)
						);
						$bg_email_desc    = sprintf(
							/* translators: 1: Open link tag <a>, 2: Close link tag </a> */
							esc_html__( 'Get the email notification as part of the Background Optimization feature. You don’t have to keep the bulk smush page open when it is in progress. Be notified when Background Optimization completes. %1$sUnlock now with Pro%2$s', 'wp-smushit' ),
							'<a href="' . esc_url( $bulk_upgrade_url ) . '" class="smush-upsell-link" target="_blank">',
							'</a>'
						);
					}
					echo wp_kses_post( $bg_email_desc );
					break;
				default:
					break;
			}
			?>
		</span>
		<?php
	}

	/**
	 * Prints notice after auto compress settings.
	 *
	 * @since 3.2.1
	 *
	 * @param string $name  Setting key.
	 */
	public function auto_smush( $name = '' ) {
		// Add only to auto smush settings.
		if ( 'auto' !== $name ) {
			return;
		}
		?>
		<div class="sui-toggle-content">
			<div class="sui-notice <?php echo $this->settings->get( 'auto' ) ? '' : ' sui-hidden'; ?>" style="margin-top: 10px">
				<div class="sui-notice-content">
					<div class="sui-notice-message">
						<i class="sui-notice-icon sui-icon-info sui-md" aria-hidden="true"></i>
						<p><?php esc_html_e( 'Note: We will only automatically compress the image sizes selected above.', 'wp-smushit' ); ?></p>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Prints all the registered image sizes, to be selected/unselected for smushing.
	 *
	 * @param string $name Setting key.
	 *
	 * @return void
	 */
	public function image_sizes( $name = '' ) {
		// Add only to bulk smush settings.
		if ( 'bulk' !== $name ) {
			return;
		}

		// Additional image sizes.
		$image_sizes  = $this->settings->get_setting( 'wp-smush-image_sizes' );
		$sizes        = WP_Smush::get_instance()->core()->image_dimensions();

		$all_selected = false === $image_sizes || count( $image_sizes ) === count( $sizes );
		?>
		<?php if ( ! empty( $sizes ) ) : ?>
			<div class="sui-side-tabs sui-tabs">
				<div data-tabs="">
					<label for="all-image-sizes" class="sui-tab-item <?php echo $all_selected ? 'active' : ''; ?>">
						<input type="radio" name="wp-smush-auto-image-sizes" value="all" id="all-image-sizes" <?php checked( $all_selected ); ?>>
						<?php esc_html_e( 'All', 'wp-smushit' ); ?>
					</label>
					<label for="custom-image-sizes" class="sui-tab-item <?php echo $all_selected ? '' : 'active'; ?>">
						<input type="radio" name="wp-smush-auto-image-sizes" value="custom" id="custom-image-sizes" <?php checked( $all_selected, false ); ?>>
						<?php esc_html_e( 'Custom', 'wp-smushit' ); ?>
					</label>
				</div><!-- end data-tabs -->
				<div data-panes>
					<div class="sui-tab-boxed <?php echo $all_selected ? 'active' : ''; ?>" style="display:none"></div>
					<div class="sui-tab-boxed <?php echo $all_selected ? '' : 'active'; ?>">
						<span class="sui-label"><?php esc_html_e( 'Included image sizes', 'wp-smushit' ); ?></span>
						<?php
						foreach ( $sizes as $size_k => $size ) {
							// If image sizes array isn't set, mark all checked ( Default Values ).
							if ( false === $image_sizes ) {
								$checked = true;
							} else {
								// WPMDUDEV hosting support: cast $size_k to string to properly work with object cache.
								$checked = is_array( $image_sizes ) && in_array( (string) $size_k, $image_sizes, true );
							}
							?>
							<label class="sui-checkbox sui-checkbox-stacked sui-checkbox-sm">
								<input type="checkbox" <?php checked( $checked, true ); ?>
									id="wp-smush-size-<?php echo esc_attr( $size_k ); ?>"
									name="wp-smush-image_sizes[]"
									value="<?php echo esc_attr( $size_k ); ?>">
								<span aria-hidden="true">&nbsp;</span>
								<span>
									<?php if ( isset( $size['width'], $size['height'] ) ) : ?>
										<?php echo esc_html( $size_k . ' (' . $size['width'] . 'x' . $size['height'] . ') ' ); ?>
									<?php else : ?>
										<?php echo esc_attr( $size_k ); ?>
									<?php endif; ?>
								</span>
							</label>
							<?php
						}
						?>
					</div>
				</div>
			</div>
		<?php endif;
	}

	/**
	 * Prints Resize, Smush Original, and Backup settings.
	 *
	 * @param string $name  Name of the current setting being processed.
	 */
	public function full_size_options( $name = '' ) {
		// Continue only if original image option.
		if ( 'original' !== $name ) {
			return;
		}

		$value = $this->settings->get( 'backup' );
		?>
		<div class="sui-form-field">
			<label for="backup" class="sui-toggle">
				<input
					type="checkbox"
					value="1"
					id="backup"
					name="backup"
					aria-labelledby="backup-label"
					aria-describedby="backup-desc"
					<?php checked( $value, 1 ); ?>
				/>
				<span class="sui-toggle-slider" aria-hidden="true"></span>
				<span id="backup-label" class="sui-toggle-label">
					<?php echo esc_html( Settings::get_setting_data( 'backup', 'label' ) ); ?>
				</span>
				<span class="sui-description sui-toggle-description" id="backup-desc">
					<?php echo esc_html( Settings::get_setting_data( 'backup', 'desc' ) ); ?>
				</span>
			</label>

			<div class="sui-toggle-content <?php echo $this->settings->get( 'original' ) ? 'sui-hidden' : ''; ?>" id="backup-notice">
				<div class="sui-notice" style="margin-top: 10px">
					<div class="sui-notice-content">
						<div class="sui-notice-message">
							<i class="sui-notice-icon sui-icon-info sui-md" aria-hidden="true"></i>
							<p>
								<?php
								printf( /* translators: %1$s - <strong>, %2$s - </strong> */
									esc_html__( '%1$sCompress original images%2$s is disabled, which means that enabling %1$sBackup original images%2$s won’t yield additional benefits and will use more storage space. We recommend enabling %1$sBackup original images%2$s only if %1$sCompress original images%2$s is also enabled.', 'wp-smushit' ),
									'<strong>',
									'</strong>'
								);
								?>
							</p>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Add scale image settings.
	 *
	 * @since 3.9.1
	 *
	 * @param string $name  Name of the current setting being processed.
	 */
	public function scale_options( $name = '' ) {
		if ( 'resize' !== $name ) {
			return;
		}

		// Not available on WordPress before 5.3.
		global $wp_version;
		if ( version_compare( $wp_version, '5.3', '<' ) ) {
			return;
		}

		$value = $this->settings->get( 'no_scale' );
		?>
		<div class="sui-form-field">
			<label for="no_scale" class="sui-toggle">
				<input
					type="checkbox"
					value="1"
					id="no_scale"
					name="no_scale"
					aria-labelledby="no_scale-label"
					aria-describedby="no_scale-desc"
					<?php checked( $value, 1 ); ?>
				/>
				<span class="sui-toggle-slider" aria-hidden="true"></span>
				<span id="no_scale-label" class="sui-toggle-label">
					<?php echo esc_html( Settings::get_setting_data( 'no_scale', 'label' ) ); ?>
				</span>
				<span class="sui-description sui-toggle-description" id="no_scale-desc">
					<?php echo esc_html( Settings::get_setting_data( 'no_scale', 'desc' ) ); ?>
				</span>
			</label>
		</div>
		<?php
	}

	/**************************
	 * META BOXES
	 */

	/**
	 * Common footer meta box.
	 *
	 * @since 3.2.0
	 */
	public function common_meta_box_footer() {
		$this->view( 'meta-box-footer', array(), 'common' );
	}

	/**
	 * Bulk smush meta box.
	 *
	 * Container box to handle bulk smush actions. Show progress bars,
	 * bulk smush action buttons etc. in this box.
	 */
	public function bulk_smush_metabox() {
		$core         = WP_Smush::get_instance()->core();
		$global_stats = $core->get_global_stats();
		$array_utils  = new Array_Utils();

		$bulk_upgrade_url       = $this->get_utm_link(
			array(
				'utm_campaign' => 'smush_bulk_smush_complete_global',
			)
		);
		$in_progress_upsell_url = $this->get_utm_link(
			array(
				'utm_campaign' => 'smush_bulk_smush_progress_BO',
			)
		);
		$upsell_cdn_url 		= $this->get_utm_link(
			array(
				'utm_campaign' => 'smush_bulksmush_cdn',
			)
		);

		$bg_optimization               = WP_Smush::get_instance()->core()->mod->bg_optimization;
		$background_processing_enabled = $bg_optimization->should_use_background();
		$background_in_processing      = $background_processing_enabled && $bg_optimization->is_in_processing();

		if ( $bg_optimization->can_use_background() ) {
			$upsell_text = '';
		} else {
			$upsell_text = sprintf(
				/* translators: %s: Upsell Link */
				__( 'Want to exit the page? Background Optimization is available with Smush Pro, allowing you to leave while Smush continues to work its magic. %s', 'wp-smushit' ),
				'<a class="smush-upsell-link" target="_blank" href="' . esc_url( $in_progress_upsell_url ) . '">
					<strong>' .
					sprintf(
						/* translators: %s: Discount */
						esc_html__( 'Upgrade to Pro and get %s off', 'wp-smushit' ),
						WP_Smush::get_instance()->admin()->get_plugin_discount()
					) . '</strong>
				</a>'
			);
		}
		$in_processing_notice = sprintf(
			/* translators: %s: Upsell text */
			__( 'Bulk Smush is currently running. Please keep this page open until the process is complete. %s', 'wp-smushit' ),
			$upsell_text
		);

		$this->view(
			'bulk/meta-box',
			array(
				'core'                            => $core,
				'can_use_background'              => $bg_optimization->can_use_background(),
				'is_pro'                          => WP_Smush::is_pro(),
				'unsmushed_count'                 => (int) $array_utils->get_array_value( $global_stats, 'count_unsmushed' ),
				'resmush_count'                   => (int) $array_utils->get_array_value( $global_stats, 'count_resmush' ),
				'remaining_count'                 => (int) $array_utils->get_array_value( $global_stats, 'remaining_count' ),
				'total_count'                     => (int) $array_utils->get_array_value( $global_stats, 'count_total' ),
				'bulk_upgrade_url'                => $bulk_upgrade_url,
				'upsell_cdn_url'                  => $upsell_cdn_url,
				'background_processing_enabled'   => $background_processing_enabled,
				'background_in_processing'        => $background_in_processing,
				'background_in_processing_notice' => $bg_optimization->get_in_process_notice(),
				'in_processing_notice'            => $in_processing_notice,
			)
		);
	}

	/**
	 * Settings meta box.
	 *
	 * Free and pro version settings are shown in same section. For free users, pro settings won't be shown.
	 * To print full size smush, resize and backup in group, we hook at `smush_setting_column_right_end`.
	 */
	public function bulk_settings_meta_box() {
		$fields = $this->settings->get_bulk_fields();

		// Remove backups setting, as it's added separately.
		$key = array_search( 'backup', $fields, true );
		if ( false !== $key ) {
			unset( $fields[ $key ] );
		}

		// Remove no_scale setting, as it's added separately.
		$key = array_search( 'no_scale', $fields, true );
		if ( false !== $key ) {
			unset( $fields[ $key ] );
		}

		$backups       = new Backups();
		$backup_exists = $backups->items_with_backup_exist();

		$this->view(
			'bulk-settings/meta-box',
			array(
				'basic_features'   => Settings::$basic_features,
				'cdn_enabled'      => $this->settings->get( 'cdn' ),
				'grouped_settings' => $fields,
				'settings'         => $this->settings->get(),
				'backup_exists'    => $backup_exists,
			)
		);
	}

	/**
	 * Show a "new" tag near the super-smush option for free users.
	 *
	 * @since 3.10.0
	 * @since 3.14.0 Showing the new tag for all users after adding Ultra level.
	 *
	 * @param string $name Option name.
	 *
	 * @return void
	 */
	public function add_lossy_new_tag( $name ) {
		if ( 'lossy' !== $name ) {
			return;
		}
		?>
		<span class="sui-tag smush-sui-tag-new"><?php esc_html_e( 'New', 'wp-smushit' ); ?></span>
		<?php
	}

	public function add_pro_tag( $name ) {
		$settings = Settings::get_instance();
		if ( ! $settings->is_pro_field( $name ) || $settings->can_access_pro_field( $name ) ) {
			return;
		}
		?>
		<span class="sui-tag sui-tag-pro"><?php esc_html_e( 'Pro', 'wp-smushit' ); ?></span>
		<?php
	}

	function set_background_email_setting_visibility( $name ) {
		if ( $name !== 'background_email' ) {
			return;
		}

		$bg_optimization       = WP_Smush::get_instance()->core()->mod->bg_optimization;
		$is_background_enabled = $bg_optimization->should_use_background();

		if ( ! $is_background_enabled && $bg_optimization->can_use_background() ) {
			?>
			<style>
				.background_email-settings-row {
					display: none !important;
				}
			</style>
			<?php
		}
	}

	public function recheck_images_notice_meta_box() {
		$this->view(
			'recheck-images-notice',
			array(),
			'common'
		);
	}
}