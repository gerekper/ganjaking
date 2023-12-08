<?php
/**
 * Compression Level.
 *
 * @var $name string Compression field name.
 * @var $value mixed Current compression value.
 */

use Smush\Core\Settings;

$settings            = Settings::get_instance();
$lossy_level_setting = $settings->get_lossy_level_setting();
$level_basic         = Settings::LEVEL_LOSSLESS;
$level_super         = Settings::LEVEL_SUPER_LOSSY;
$level_ultra         = Settings::LEVEL_ULTRA_LOSSY;
$level_labels        = array(
	Settings::LEVEL_LOSSLESS    => __( 'Basic', 'wp-smushit' ),
	Settings::LEVEL_SUPER_LOSSY => __( 'Super', 'wp-smushit' ),
	Settings::LEVEL_ULTRA_LOSSY => __( 'Ultra', 'wp-smushit' ),
);

$level_notices = array(
	Settings::LEVEL_LOSSLESS    => sprintf(
	/* translators: 1: opening <strong>, 2: closing </strong> */
		__( '%1$sBasic:%2$s Achieve flawless, lossless compression for pixel-perfect images. Minimal file size reduction, negligible impact on speed.', 'wp-smushit' ),
		'<strong>',
		'</strong>'
	),
	Settings::LEVEL_SUPER_LOSSY => sprintf(
	/* translators: 1: opening <strong>, 2: closing </strong> */
		__( '%1$sSuper:%2$s Harness the power of lossy compression for substantial file size reduction with excellent image clarity. Accelerate page loads for better performance.', 'wp-smushit' ),
		'<strong>',
		'</strong>'
	),
	Settings::LEVEL_ULTRA_LOSSY => sprintf(
	/* translators: 1: opening <strong>, 2: closing </strong> */
		__( '%1$sUltra:%2$s Unlock unprecedented compression levels up to 5x greater than Super, while preserving remarkable image quality. The ultimate choice for unparalleled performance.', 'wp-smushit' ),
		'<strong>',
		'</strong>'
	),
);

?>
<div class="sui-tabs sui-side-tabs wp-smush-lossy-level-tabs">
	<div role="tablist" class="sui-tabs-menu">
		<!-- Basic -->
		<button
			type="button"
			role="tab"
			id="lossy-level__basic"
			class="sui-tab-item<?php echo $level_basic === $lossy_level_setting ? ' active' : ''; ?>"
			aria-controls="lossy-level__basic-notice">
			<?php echo esc_html( $settings->get_lossy_level_label( $level_basic ) ); ?>
		</button>
		<input
			type="radio"
			class="sui-screen-reader-text"
			aria-hidden="true"
			name="<?php echo esc_attr( $name ); ?>"
			aria-labelledby="<?php echo esc_attr( $name . '-label' ); ?>"
			aria-describedby="<?php echo esc_attr( $name . '-desc' ); ?>"
			value="<?php echo (int) $level_basic; ?>"
			<?php checked( $lossy_level_setting, $level_basic, true ); ?> />

		<!-- Super -->
		<button
			type="button"
			role="tab"
			id="lossy-level__super"
			class="sui-tab-item<?php echo $level_super === $lossy_level_setting ? ' active' : ''; ?>"
			aria-controls="lossy-level__super-notice"
			tabindex="-1">
			<?php echo esc_html( $settings->get_lossy_level_label( $level_super ) ); ?>
		</button>
		<input
			type="radio"
			class="sui-screen-reader-text"
			aria-hidden="true"
			name="<?php echo esc_attr( $name ); ?>"
			aria-labelledby="<?php echo esc_attr( $name . '-label' ); ?>"
			aria-describedby="<?php echo esc_attr( $name . '-desc' ); ?>"
			value="<?php echo (int) $level_super; ?>"
			<?php checked( $lossy_level_setting, $level_super, true ); ?> />

		<!-- Ultra -->
		<?php if ( WP_Smush::is_pro() ) : ?>
			<button
				type="button"
				role="tab"
				id="lossy-level__ultra"
				class="sui-tab-item<?php echo $level_ultra === $lossy_level_setting ? ' active' : ''; ?>"
				aria-controls="lossy-level__ultra-notice"
				tabindex="-1">
				<?php echo esc_html( $settings->get_lossy_level_label( $level_ultra ) ); ?>
			</button>
			<input
				type="radio"
				class="sui-screen-reader-text"
				aria-hidden="true"
				name="<?php echo esc_attr( $name ); ?>"
				aria-labelledby="<?php echo esc_attr( $name . '-label' ); ?>"
				aria-describedby="<?php echo esc_attr( $name . '-desc' ); ?>"
				value="<?php echo (int) $level_ultra; ?>"
				<?php checked( $lossy_level_setting, $level_ultra, true ); ?> />
		<?php else :
			$utm_link = $this->get_utm_link(
				array(
					'utm_campaign' => 'smush_ultra_bulksmush_radio',
				)
			);
		?>
			<a target="_blank" href="<?php echo esc_url( $utm_link ); ?>" class="sui-tab-item wp-smush-ultra-compression-link wp-smush-upsell-ultra-compression">
				<?php esc_html_e( '🚀 Ultra - unlock 5x more compression', 'wp-smushit' ); ?>
				<span class="sui-icon-open-new-window" aria-hidden="true"></span>
			</a>
		<?php endif; ?>
	</div>
	<div class="sui-tabs-content">
		<div role="tabpanel"
			id="lossy-level__basic-notice"
			class="sui-tab-content<?php echo $level_basic === $lossy_level_setting ? ' active' : ''; ?>"
			aria-labelledby="lossy-level__basic"
			tabindex="0">
			<p>
				<i class="sui-notice-icon sui-icon-info sui-md" aria-hidden="true"></i>
				<?php
					echo wp_kses(
						$level_notices[ $level_basic ],
						array(
							'strong' => array(),
						)
					);
					?>
			</p>
		</div>
		<div role="tabpanel"
			id="lossy-level__super-notice"
			class="sui-tab-content<?php echo $level_super === $lossy_level_setting ? ' active' : ''; ?>"
			aria-labelledby="lossy-level__super"
			tabindex="0"
			hidden>
			<p>
				<i class="sui-notice-icon sui-icon-info sui-md" aria-hidden="true"></i>
				<?php
					echo wp_kses(
						$level_notices[ $level_super ],
						array(
							'strong' => array(),
						)
					);
					?>
			</p>
		</div>
		<div role="tabpanel"
			id="lossy-level__ultra-notice"
			class="sui-tab-content<?php echo $level_ultra === $lossy_level_setting ? ' active' : ''; ?>"
			aria-labelledby="lossy-level__ultra"
			tabindex="0"
			hidden>
			<p>
				<i class="sui-notice-icon sui-icon-info sui-md" aria-hidden="true"></i>
				<?php
					echo wp_kses(
						$level_notices[ $level_ultra ],
						array(
							'strong' => array(),
						)
					);
					?>
			</p>
		</div>
	</div>
</div>