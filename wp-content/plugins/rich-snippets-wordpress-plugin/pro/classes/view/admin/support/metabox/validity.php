<?php

namespace wpbuddy\rich_snippets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

$response = WPBuddy_Model::request(
	'/wpbuddy/rich_snippets_manager/v1/support?field=supported_until'
);

if ( is_object( $response ) && isset( $response->supported_until ) ) {
	$supported_until = absint( $response->supported_until );
	$renew_form      = true;

	if ( current_time( 'timestamp' ) <= ( $supported_until - MONTH_IN_SECONDS ) ) {
		$icon       = 'yes';
		$str        = __( 'Cool! You are eligible for support until %1$s.', 'rich-snippets-schema' );
		$renew_form = false;
	} else if ( current_time( 'timestamp' ) <= $supported_until ) {
		$icon = 'warning';
		$str  = _x(
			'Your item support will soon end (on %1$s). However still %2$s to go!',
			'First is the exact date. Second is the human time diff.',
			'rich-snippets-schema'
		);

	} else {
		$icon = 'no';
		$str  = __( 'Oh no! You are not eligible for support. It has expired on %1$s.', 'rich-snippets-schema' );
	}

	$str = sprintf(
		$str,
		date_i18n( get_option( 'date_format', 'd.m.Y' ), $supported_until ),
		human_time_diff( current_time( 'timestamp' ), $supported_until )
	);

	?>
	<div class="wpb-rs-support-validity">
		<span class="dashicons dashicons-<?php echo $icon; ?>"></span>
		<p><?php echo $str; ?></p>
	</div>
	<?php if ( $renew_form ): ?>
		<a target="_blank" class="button"
		   href="https://codecanyon.net/checkout/from_item/3464341?support=renew_6month&ref=wpbuddy"
		   target="_blank"><?php _e( 'Renew support now', 'rich-snippets-schema' ); ?></a>
	<?php endif;
} else {
	_e( 'The plugin was not able to gather any information for your purchase code.', 'rich-snippets-schema' );
}

