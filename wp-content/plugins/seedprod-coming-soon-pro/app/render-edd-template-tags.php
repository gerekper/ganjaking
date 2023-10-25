<?php


/**
 * Render Easy Digital Downloads Template Tags.
 */

// Add [seedprod_edd] shortcode.
add_shortcode( 'seedprod_edd', 'seedprod_pro_render_edd_template_tags_shortcode' );

/**
 * Render SeedProd EDD Shortcode[seedprod_edd].
 *
 * @return void|string
 */
function seedprod_pro_render_edd_template_tags_shortcode( $atts ) {
	$a = shortcode_atts(
		array(
			'tag'  => '',
			'echo' => false,
		),
		$atts
	);

	$tag_allow_list = array(
		'the_title',
		'the_post_thumbnail',
		'the_content',
		'the_excerpt',
		'short_description',

		'download_instructions',
		'price_html',
		'downloads',
		'download_cart',
		'download_checkout',
		'purchase_link',
		'edd_login',
		'edd_register',
		'purchase_history',
		'edd_receipt',
		'download_history',
	);

	// If tag not allowed return empty string.
	if ( ! in_array( $a['tag'], $tag_allow_list ) ) {
		return;
	}

	// Check if the EDD Instance exists.
	if ( ! in_array( 'easy-digital-downloads/easy-digital-downloads.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) && ! in_array( 'easy-digital-downloads-pro/easy-digital-downloads.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		return;
	}

	global $post;

	// If the EDD_Download Object is not defined globally
	if ( ! is_a( $post, 'EDD_Download' ) ) {
		$download = edd_get_download( get_the_id() );
	}

	// Render EDD Template Tags.
	if ( ! empty( $a['tag'] ) ) {
		$values  = null;
		$values2 = null;

		if ( ! $download ) {
			return;
		}

		if ( strpos( $a['tag'], '(' ) !== false ) {
			preg_match( '#\((.*?)\)#', $a['tag'], $match );
			$a['tag'] = str_replace( $match[0], '', $a['tag'] );
			$values   = $match[1];
		}
		if ( 'the_post_thumbnail' === $a['tag'] ) {
			remove_all_filters( 'post_thumbnail_html' );
			$values2 = array( 'alt' => get_the_title() );
		}

		ob_start();
		if ( 'get_post_custom_values' === $a['tag'] ) {
			$output = @call_user_func( $a['tag'], $values, $values2 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			if ( ! empty( $output[0] ) ) {
				$output = $output[0];
			}
			echo wp_kses_post( $output );
		} elseif ( 'price_html' == $a['tag'] ) {
			$output = seedprod_pro_edd_get_price_html( $download->ID );
			echo wp_kses_post( $output );
		} elseif ( 'the_title' == $a['tag'] ) {
			$output = $download->get_name();
			echo wp_kses_post( $output );
		} elseif ( 'the_content' == $a['tag'] ) {
			$output = $download->post_content;
			echo wp_kses_post( $output );
		} elseif ( 'the_excerpt' == $a['tag'] ) {
			$output = $download->post_excerpt;
			echo wp_kses_post( $output );
		} elseif ( 'download_instructions' == $a['tag'] ) {
			$output = $download->get_notes();
			echo wp_kses_post( $output );
		} elseif ( ! empty( $a['echo'] ) ) {
			$output = @call_user_func( $a['tag'], $values, $values2 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			echo wp_kses_post( $output );
		} else {
			if ( 'none' == $values ) {
				$output = @call_user_func( $a['tag'] ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			} else {
				@call_user_func( $a['tag'], $values, $values2 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			}
		}

		$render = ob_get_clean();
		return $render;
	}
}

// Add [sp_edd_add_to_cart] shortcode.
add_shortcode( 'sp_edd_add_to_cart', 'seedprod_pro_edd_add_to_cart_shortcode' );

/**
 * Render SeedProd EDD Shortcode[sp_edd_add_to_cart].
 *
 * @return void|string
 */
function seedprod_pro_edd_add_to_cart_shortcode( $atts ) {
	// Set default shortcode args.
	$shortcode_args = shortcode_atts(
		array(
			'direct_to_checkout' => 'true',
			'show_price'         => 'true',
			'btn_txt'            => '',
			'before_icon'        => '',
			'after_icon'         => '',
		),
		$atts
	);

	// Check if the EDD Instance exists.
	if ( ! in_array( 'easy-digital-downloads/easy-digital-downloads.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) && ! in_array( 'easy-digital-downloads-pro/easy-digital-downloads.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		return;
	}

	$show_price         = ( 'true' === $shortcode_args['show_price'] ) ? 1 : 0;
	$btn_txt            = $shortcode_args['btn_txt'];
	$direct_to_checkout = $shortcode_args['direct_to_checkout'];
	$before_icon        = $shortcode_args['before_icon'];
	$after_icon         = $shortcode_args['after_icon'];

	global $post;

	// If the EDD_Download Object is not defined globally
	if ( ! is_a( $post, 'EDD_Download' ) ) {
		$download = edd_get_download( get_the_id() );
	}

	if ( ! $download ) {
		return;
	}

	// Add global button class.
	$purchase_link = do_shortcode( "[purchase_link id='$download->id' text='$btn_txt' direct='$direct_to_checkout' price='$show_price']" );
	$purchase_link = str_replace( 'edd-add-to-cart button', 'edd-add-to-cart sp-button', $purchase_link );
	$purchase_link = str_replace( 'edd_go_to_checkout button', 'edd_go_to_checkout sp-button', $purchase_link );

	// Insert before Icon.
	if ( '' !== $purchase_link && '' !== $before_icon ) {
		$doc = new DOMDocument();
		// Using LIBXML_NOERROR to prevent HTML errors since HTML5 is not supported by libxml2.
		$doc->loadHTML( $purchase_link, LIBXML_NOERROR );
		$xpath = new DOMXpath( $doc );

		// Get button text <span>.
		$button_span = $xpath->query( '//span[contains(@class, "edd-add-to-cart-label")]' )->item( 0 );

		$before_icon_html = $doc->createElement( 'i' );
		$class_attribute  = $doc->createAttribute( 'class' );

		// Value for the created attribute
		$class_attribute->value = $before_icon;

		// Don't forget to append it to the element
		$before_icon_html->appendChild( $class_attribute );
		$button_span->parentNode->insertBefore( $before_icon_html, $button_span ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

		$purchase_link = $doc->saveHTML();
	}

	// Insert after Icon.
	if ( '' !== $purchase_link && '' !== $after_icon ) {
		$doc = new DOMDocument();
		// Using LIBXML_NOERROR to prevent HTML errors since HTML5 is not supported by libxml2.
		$doc->loadHTML( $purchase_link, LIBXML_NOERROR );
		$xpath = new DOMXpath( $doc );

		// Get button text <span>.
		$button_span = $xpath->query( '//span[contains(@class, "edd-loading")]' )->item( 0 );

		$after_icon_html = $doc->createElement( 'i' );
		$class_attribute = $doc->createAttribute( 'class' );

		// Value for the created attribute
		$class_attribute->value = $after_icon;

		// Don't forget to append it to the element
		$after_icon_html->appendChild( $class_attribute );

		$button_span->parentNode->insertBefore( $after_icon_html, $button_span ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		$purchase_link = $doc->saveHTML();
	}

	return $purchase_link;
}

/**
 * Return the price html for the download.
 *
 * @param integer $download_id The download ID.
 * @return string $html The price html.
 */
function seedprod_pro_edd_get_price_html( $download_id ) {
	$html = '<div class="edd-blocks__download-price">';
	if ( edd_is_free_download( $download_id ) ) {
		$html .= sprintf(
			'<span class="edd_price" id="edd_price_%s">%s</span>',
			absint( $download_id ),
			esc_html__( 'Free', 'seedprod-pro' )
		);
	} elseif ( edd_has_variable_prices( $download_id ) ) {
		$html .= wp_kses_post( edd_price_range( $download_id ) );
	} else {
		$html .= edd_price( $download_id, true );
	}
	$html .= '</div>';

	return $html;
}


