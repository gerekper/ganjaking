<?php
/**
 * General Settings options
 *
 * @package YITH\Booking\Options
 */

defined( 'YITH_WCBK' ) || exit(); // Exit if accessed directly.

$categories = yith_wcbk()->wp->get_terms(
	array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => true,
		'fields'     => 'id=>name',
	)
);

$options = array(
	'general-settings'                            => array(
		'title' => __( 'Settings', 'yith-booking-for-woocommerce' ),
		'type'  => 'title',
		'desc'  => '',
	),
	'theme-action'                                => array(
		'name'             => __( 'Suggested Themes', 'yith-booking-for-woocommerce' ),
		'type'             => 'yith-field',
		'yith-type'        => 'html',
		'yith-display-row' => true,
		'html'             => '',
	),
	'booking-categories-to-show'                  => array(
		'id'               => 'yith-wcbk-booking-categories-to-show',
		'name'             => __( 'Set which categories to show in Search Forms', 'yith-booking-for-woocommerce' ),
		'type'             => 'yith-field',
		'yith-type'        => 'radio',
		'desc'             => implode(
			'<br />',
			array(
				__( 'Choose the categories of the bookable products that will be visible in the Search Form.', 'yith-booking-for-woocommerce' ),
				__( 'You can set all categories (if all categories are assigned to bookable products) or choose specific ones.', 'yith-booking-for-woocommerce' ),
			)
		),
		'default'          => 'all',
		'options'          => array(
			'all'      => __( 'All product categories', 'yith-booking-for-woocommerce' ),
			'specific' => __( 'Choose specific product categories', 'yith-booking-for-woocommerce' ),
		),
		'yith-wcbk-module' => 'search-forms',
	),
	'booking-categories'                          => array(
		'id'               => 'yith-wcbk-booking-categories',
		'name'             => __( 'Booking Categories', 'yith-booking-for-woocommerce' ),
		'type'             => 'yith-field',
		'yith-type'        => 'select-buttons',
		'multiple'         => true,
		'options'          => $categories,
		'add_all_label'    => _x( 'Add all', 'Categories', 'yith-booking-for-woocommerce' ),
		'remove_all_label' => _x( 'Remove all', 'Categories', 'yith-booking-for-woocommerce' ),
		'deps'             => array(
			'id'    => 'yith-wcbk-booking-categories-to-show',
			'value' => 'specific',
			'type'  => 'hide',
		),
		'yith-wcbk-module' => 'search-forms',
	),
	'reject-pending-confirmation-booking-enabled' => array(
		'id'        => 'yith-wcbk-reject-pending-confirmation-booking-enabled',
		'name'      => __( 'Reject a "pending confirmation" booking after a specific time', 'yith-booking-for-woocommerce' ),
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'no',
	),
	'reject-pending-confirmation-booking-after'   => array(
		'id'                   => 'yith-wcbk-reject-pending-confirmation-bookings-after',
		'name'                 => __( 'Reject a "pending confirmation" booking after', 'yith-booking-for-woocommerce' ),
		'type'                 => 'yith-field',
		'yith-type'            => 'number',
		'class'                => 'yith-wcbk-number-field-mini',
		'yith-wcbk-after-html' => __( 'day(s)', 'yith-booking-for-woocommerce' ),
		'default'              => 1,
		'min'                  => 1,
		'deps'                 => array(
			'id'    => 'yith-wcbk-reject-pending-confirmation-booking-enabled',
			'value' => 'yes',
			'type'  => 'hide',
		),
	),
	'complete-paid-bookings-enabled'              => array(
		'id'        => 'yith-wcbk-complete-paid-bookings-enabled',
		'name'      => __( 'Set paid bookings to "completed"', 'yith-booking-for-woocommerce' ),
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'no',
	),
	'complete-paid-bookings-after'                => array(
		'id'                   => 'yith-wcbk-complete-paid-bookings-after',
		'name'                 => __( 'Set paid bookings to "completed" after', 'yith-booking-for-woocommerce' ),
		'type'                 => 'yith-field',
		'yith-type'            => 'number',
		'class'                => 'yith-wcbk-number-field-mini',
		'yith-wcbk-after-html' => __( 'day(s)', 'yith-booking-for-woocommerce' ),
		'default'              => 1,
		'min'                  => 0,
		'deps'                 => array(
			'id'    => 'yith-wcbk-complete-paid-bookings-enabled',
			'value' => 'yes',
			'type'  => 'hide',
		),
	),
	'google-maps-api-key'                         => array(
		'id'               => 'yith-wcbk-google-maps-api-key',
		'name'             => __( 'Google Maps API Key', 'yith-booking-for-woocommerce' ),
		'type'             => 'yith-field',
		'yith-type'        => 'text',
		'desc'             =>
			__( 'Enter the Google Maps API Key.', 'yith-booking-for-woocommerce' ) .
			' ' .
			'<a href="https://docs.yithemes.com/yith-woocommerce-booking/premium-version-settings/google-map-api-key/" target="_blank">' .
			__( 'Learn how to use the Google Maps API Key >', 'yith-booking-for-woocommerce' ) .
			'</a>',
		'default'          => '',
		'yith-wcbk-module' => 'google-maps',
	),
	'google-maps-geocode-api-key'                 => array(
		'id'               => 'yith-wcbk-google-maps-geocode-api-key',
		'name'             => __( 'Google Maps Geocode API Key', 'yith-booking-for-woocommerce' ),
		'type'             => 'yith-field',
		'yith-type'        => 'text',
		'desc'             =>
			__( 'Enter the Google Maps API Key for Geocode.', 'yith-booking-for-woocommerce' ) .
			' ' .
			'<a href="https://docs.yithemes.com/yith-woocommerce-booking/premium-version-settings/google-map-api-key/" target="_blank">' .
			__( 'Learn how to use the Google Maps Geocode API Key >', 'yith-booking-for-woocommerce' ) .
			'</a>',
		'default'          => '',
		'yith-wcbk-module' => 'google-maps',
	),
	'general-settings-end'                        => array(
		'type' => 'sectionend',
	),
);

// Theme options.
$theme_html = '';
if ( current_user_can( 'switch_themes' ) && current_user_can( 'edit_theme_options' ) ) {
	$themes = array(
		'yith-proteo' => array(
			'name' => 'YITH Proteo',
			'slug' => 'yith-proteo',
		),
	);

	foreach ( $themes as $theme ) {
		$is_installed = yith_wcbk()->theme->is_installed( $theme['slug'] );
		$is_active    = $is_installed && yith_wcbk()->theme->is_active( $theme['slug'] );
		$the_theme    = yith_wcbk()->theme->get_theme( $theme['slug'] );
		$is_allowed   = $the_theme && $the_theme->is_allowed();
		$actions      = '';
		$message      = '';

		if ( ! $is_installed ) {
			if ( current_user_can( 'install_themes' ) ) {
				$actions = sprintf(
					'<a href="#" class="theme__action theme-install" data-name="%s" data-slug="%s">%s</a>',
					$theme['name'],
					$theme['slug'],
					_x( 'Install', 'Theme', 'yith-booking-for-woocommerce' )
				);

			}
		} elseif ( ! $is_allowed ) {
			if ( current_user_can( 'install_themes' ) ) {
				$actions = sprintf(
					'<a href="#" class="theme__action theme-network-enable" data-name="%s" data-slug="%s">%s</a>',
					$theme['name'],
					$theme['slug'],
					_x( 'Network enable', 'Theme', 'yith-booking-for-woocommerce' )
				);
			} else {
				$actions = '<span class="theme__status--cannot-activate yith-icon yith-icon-warning-triangle"></span>';
				$message = sprintf(
				// translators: %s is the theme name.
					__( '%s theme is installed, but you cannot activate it. If you are running a Multi Site installation, please contact the Multi-Site administrator to enable it in <em>Network Admin > Themes</em>.', 'yith-booking-for-woocommerce' ),
					'<strong>' . esc_html( $theme['name'] ) . '</strong>'
				);
			}
		} elseif ( ! $is_active ) {
			$activate_url = YITH_WCBK()->theme->get_theme_activation_url( $theme['slug'] );
			$actions      = sprintf(
				'<a href="%s" class="theme__action activate" data-name="%s" data-slug="%s">%s</a>',
				$activate_url,
				$theme['name'],
				$theme['slug'],
				_x( 'Activate', 'Theme', 'yith-booking-for-woocommerce' )
			);
		} else {
			$actions = '<span class="theme__status--active yith-icon yith-icon-check-circle"></span>';
		}

		if ( $actions ) {
			$theme_html .= '<li class="theme" data-slug="' . $theme['slug'] . '">';
			$theme_html .= '<div class="theme__name-and-actions">';
			$theme_html .= '<span class="theme__name">' . esc_html( $theme['name'] ) . '</span>';
			$theme_html .= '<span class="theme__spacer"></span>';
			$theme_html .= '<span class="theme__actions">' . $actions . '</span>';
			$theme_html .= '</div>';
			$theme_html .= '<div class="theme__messages">';
			if ( $message ) {
				$theme_html .= '<div class="theme__message">' . wp_kses_post( $message ) . '</div>';
			}
			$theme_html .= '</div>';
			$theme_html .= '</li>';
		}
	}

	if ( $theme_html ) {
		$theme_html = '<ul class="yith-wcbk-suggested-themes">' . $theme_html . '</ul>';
	}
}

if ( $theme_html ) {
	$options['theme-action']['html'] = "<div class='yith-wcbk-settings-theme-actions__wrapper'>$theme_html</div>";
} else {
	unset( $options['theme-action'] );
}

$options = yith_wcbk_filter_options( $options );

$tab_options = array(
	'settings-general-settings' => $options,
);

// phpcs:enable
return apply_filters( 'yith_wcbk_panel_general_settings_options', $tab_options );
