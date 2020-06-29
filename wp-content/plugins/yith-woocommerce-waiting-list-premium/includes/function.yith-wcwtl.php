<?php
/**
 * General Function
 *
 * @author  YITH
 * @package YITH WooCommerce Waiting List
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCWTL' ) ) {
	exit;
} // Exit if accessed directly

if ( ! function_exists( 'yith_waitlist_get' ) ) {
	/**
	 * Get waiting list for product id
	 *
	 * @since  1.0.0
	 * @author Francesco Licandro <francesco.licandro@yithemes.com>
	 * @param object|integer $product
	 * @return array
	 */
	function yith_waitlist_get( $product ) {
		if ( ! is_object( $product ) ) {
			$product = wc_get_product( $product );
		}

		$list = $product ? $product->get_meta( '_yith_wcwtl_users_list', true ) : array();
		return is_array( $list ) ? $list : array();
	}
}

if ( ! function_exists( 'yith_waitlist_save' ) ) {
	/**
	 * Save waiting list for product id
	 *
	 * @since  1.0.0
	 * @author Francesco Licandro <francesco.licandro@yithemes.com>
	 * @param array          $list
	 * @param object|integer $product
	 * @return void
	 */
	function yith_waitlist_save( $product, $list ) {
		if ( ! is_object( $product ) ) {
			$product = wc_get_product( $product );
		}

		if ( $product ) {
			$product->update_meta_data( '_yith_wcwtl_users_list', $list );
			$product->save();
		}
	}
}

if ( ! function_exists( 'yith_waitlist_user_is_register' ) ) {
	/**
	 * Check if user is already register for a waiting list
	 *
	 * @since  1.0.0
	 * @author Francesco Licandro <francesco.licandro@yithemes.com>
	 * @param array  $list
	 * @param string $user
	 * @return bool
	 */
	function yith_waitlist_user_is_register( $user, $list ) {
		return is_array( $list ) && in_array( $user, $list );
	}
}

if ( ! function_exists( 'yith_waitlist_register_user' ) ) {
	/**
	 * Register user to waiting list
	 *
	 * @since  1.0.0
	 * @author Francesco Licandro <francesco.licandro@yithemes.com>
	 * @param object|int $product
	 * @param string     $user User email
	 * @return bool
	 */
	function yith_waitlist_register_user( $user, $product ) {

		if ( ! is_object( $product ) ) {
			$product = wc_get_product( $product );
		}

		$list = yith_waitlist_get( $product );

		if ( ! is_email( $user ) || yith_waitlist_user_is_register( $user, $list ) ) {
			return false;
		}

		// add product to user meta
		yith_waitlist_save_user_meta( $product, $user );

		$list[] = $user;
		// save it in product meta
		yith_waitlist_save( $product, $list );

		return true;
	}
}

if ( ! function_exists( 'yith_waitlist_register_users_bulk' ) ) {
	/**
	 * Register an array of users to waiting list
	 *
	 * @since  1.6.0
	 * @author Francesco Licandro <francesco.licandro@yithemes.com>
	 * @param object|int $product
	 * @param array      $users An array of users email
	 * @return bool
	 */
	function yith_waitlist_register_users_bulk( $users, $product ) {

		if ( ! is_object( $product ) ) {
			$product = wc_get_product( $product );
		}

		$list = yith_waitlist_get( $product );

		foreach ( $users as $user ) {
			if ( ! is_email( $user ) || yith_waitlist_user_is_register( $user, $list ) ) {
				continue;
			}
			// add product to user meta
			yith_waitlist_save_user_meta( $product, $user );
			$list[] = $user;
		}

		// save it in product meta
		yith_waitlist_save( $product, $list );

		return true;
	}
}

if ( ! function_exists( 'yith_waitlist_unregister_user' ) ) {
	/**
	 * Unregister user from waiting list
	 *
	 * @since  1.0.0
	 * @author Francesco Licandro <francesco.licandro@yithemes.com>
	 * @param object|integer $product Product id
	 * @param string         $user    User email
	 * @return bool
	 */
	function yith_waitlist_unregister_user( $user, $product ) {

		$list = yith_waitlist_get( $product );

		if ( yith_waitlist_user_is_register( $user, $list ) ) {
			// remove product from user meta
			yith_waitlist_remove_user_meta( $product, $user );

			$list = array_diff( $list, array( $user ) );

			// save it in product meta
			yith_waitlist_save( $product, $list );
			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'yith_waitlist_get_registered_users' ) ) {
	/**
	 * Get registered users for product waitlist
	 *
	 * @since  1.0.0
	 * @author Francesco Licandro <francesco.licandro@yithemes.com>
	 * @param object|integer $product
	 * @return mixed
	 */
	function yith_waitlist_get_registered_users( $product ) {

		$list  = yith_waitlist_get( $product );
		$users = array();

		if ( is_array( $list ) ) {
			foreach ( $list as $key => $email ) {
				$users[] = $email;
			}
		}

		return $users;
	}
}

if ( ! function_exists( 'yith_waitlist_empty' ) ) {
	/**
	 * Empty waitlist by product id
	 *
	 * @since  1.0.0
	 * @author Francesco Licandro <francesco.licandro@yithemes.com>
	 * @param object|integer $product
	 * @return void
	 */
	function yith_waitlist_empty( $product ) {
		if ( ! is_object( $product ) ) {
			$product = wc_get_product( $product );
		}

		if ( $product ) {
			// first of all get all users and update their meta
			$users = yith_waitlist_get_registered_users( $product );
			foreach ( $users as $user ) {
				yith_waitlist_remove_user_meta( $product, $user );
			}
			// now empty waiting list
			delete_post_meta( $product->get_id(), '_yith_wcwtl_users_list' );

		}
	}
}

if ( ! function_exists( 'yith_waitlist_is_excluded' ) ) {
	/**
	 * Check if product is in excluded list
	 *
	 * @since  1.0.0
	 * @author Francesco Licandro <francesco.licandro@yithems.com>
	 * @param object|integer $product
	 * @return bool
	 */
	function yith_waitlist_is_excluded( $product ) {

		global $sitepress;

		if ( ! is_object( $product ) ) {
			$product = wc_get_product( $product );
		}

		if ( ! $product ) {
			return true;
		}

		if ( function_exists( 'wpml_object_id_filter' ) && ! is_null( $sitepress ) ) {
			$product_type = $product->is_type( 'variation' ) ? 'product_variation' : 'product';
			$product_id   = wpml_object_id_filter( $product->get_id(), $product_type, true, $sitepress->get_default_language() );
			( $product->get_id() != $product_id ) && $product = wc_get_product( $product_id );
		}

		// check inverted logic
		$inverted    = get_option( 'yith-wcwtl-exclusion-inverted', 'no' ) == 'yes';
		$is_excluded = $product->get_meta( '_yith_wcwtl_exclude_list', true );

		return $inverted ? ! $is_excluded : $is_excluded;
	}
}

if ( ! function_exists( 'yith_count_users_on_waitlist' ) ) {
	/**
	 * Count users on waitlist
	 *
	 * @since  1.0.0
	 * @author Francesco Licandro <francesco.licandro@yithems.com>
	 * @param object|integer $product
	 * @return bool
	 */
	function yith_count_users_on_waitlist( $product ) {
		if ( ! is_object( $product ) ) {
			$product = wc_get_product( $product );
		}

		if ( ! $product ) {
			return false;
		}

		$user = $product->get_meta( '_yith_wcwtl_users_list', true );
		return $user ? count( $user ) : 0;
	}
}

/***************
 * USER FUNCTION
 **************/

if ( ! function_exists( 'yith_get_user_waitlists' ) ) {
	/**
	 * Get meta for user subscribed waiting lists
	 *
	 * @since  1.0.0
	 * @author Francesco Licandro <francesco.licandro@yithems.com>
	 * @param int $id User id
	 * @return mixed
	 */
	function yith_get_user_waitlists( $id ) {
		return get_user_meta( $id, '_yith_wcwtl_products_list', true );
	}
}

if ( ! function_exists( 'yith_get_user_wailists' ) ) {
	/**
	 * @param $id
	 * @return mixed
	 * @deprecated use yith_get_user_waitlists instead
	 */
	function yith_get_user_wailists( $id ) {
		return yith_get_user_waitlists( $id );
	}
}

if ( ! function_exists( 'yith_waitlist_user_meta' ) ) {
	/**
	 * Save new waiting list in user meta
	 *
	 * @since  1.0.0
	 * @author Francesco Licandro <francesco.licandro@yithemes.com>
	 * @param string     $email User email
	 * @param object|int $product
	 */
	function yith_waitlist_save_user_meta( $product, $email ) {
		if ( ! is_object( $product ) ) {
			$product = wc_get_product( $product );
		}

		$user = get_user_by( 'email', $email );

		if ( ! $user || ! $product ) {
			return;
		}

		$products = yith_get_user_waitlists( $user->ID );
		! is_array( $products ) && $products = array();
		$products[] = $product->get_id();

		update_user_meta( $user->ID, '_yith_wcwtl_products_list', array_unique( $products ) );
	}
}

if ( ! function_exists( 'yith_waitlist_remove_user_meta' ) ) {
	/**
	 * Remove waiting list from user meta
	 *
	 * @since  1.0.0
	 * @author Francesco Licandro <francesco.licandro@yithemes.com>
	 * @param string     $email   User email
	 * @param object|int $product Product Id
	 */
	function yith_waitlist_remove_user_meta( $product, $email ) {

		$product = ( $product instanceof WC_Product ) ? $product->get_id() : absint( $product );

		$user = get_user_by( 'email', $email );

		if ( ! $user ) {
			return;
		}

		$products = yith_get_user_waitlists( $user->ID );
		$products = array_diff( $products, array( $product ) );

		update_user_meta( $user->ID, '_yith_wcwtl_products_list', $products );
	}
}

if ( ! function_exists( 'yith_waitlist_mandrill_mail' ) ) {
	/**
	 * Send mail using Mandrill Service
	 *
	 * @access public
	 * @since  1.0.0
	 * @author Francesco Licandro <francesco.licandro@yithemes.com>
	 * @param string $subject
	 * @param string $message
	 * @param string $headers
	 * @param string $attachments
	 * @param object $email
	 *
	 * @param string $to
	 * @return bool | void
	 */
	function yith_waitlist_mandrill_mail( $to, $subject, $message, $headers, $attachments, $email ) {

		// Retrieve Mandrill API KEY
		$api_key = get_option( 'yith-wcwtl-mandrill-api-key' );

		if ( empty( $api_key ) ) {
			return false;
		}

		/**
		 * Filter the wp_mail() arguments.
		 *
		 * @since 2.2.0
		 *
		 * @param array $args A compacted array of wp_mail() arguments, including the "to" email,
		 *                    subject, message, headers, and attachments values.
		 */
		$atts = apply_filters( 'wp_mail', compact( 'to', 'subject', 'message', 'headers', 'attachments' ) );

		if ( isset( $atts['to'] ) ) {
			$to = $atts['to'];
		}

		if ( isset( $atts['subject'] ) ) {
			$subject = $atts['subject'];
		}

		if ( isset( $atts['message'] ) ) {
			$message = $atts['message'];
		}

		if ( isset( $atts['headers'] ) ) {
			$headers = $atts['headers'];
		}

		if ( isset( $atts['attachments'] ) ) {
			$attachments = $atts['attachments'];
		}

		if ( ! is_array( $attachments ) ) {
			$attachments = explode( "\n", str_replace( "\r\n", "\n", $attachments ) );
		}

		// include lib
		if ( file_exists( YITH_WCWTL_DIR . 'vendor/Mandrill/Mandrill.php' ) ) {
			include_once YITH_WCWTL_DIR . 'vendor/Mandrill/Mandrill.php';
		} else {
			return false;
		}

		// Headers
		if ( empty( $headers ) ) {
			$headers = array();
		} else {
			if ( ! is_array( $headers ) ) {
				// Explode the headers out, so this function can take both
				// string headers and an array of headers.
				$tempheaders = explode( "\n", str_replace( "\r\n", "\n", $headers ) );
			} else {
				$tempheaders = $headers;
			}

			$headers = array();
			$cc      = array();
			$bcc     = array();

			// If it's actually got contents
			if ( ! empty( $tempheaders ) ) {
				// Iterate through the raw headers
				foreach ( (array) $tempheaders as $header ) {
					if ( strpos( $header, ':' ) === false ) {
						if ( false !== stripos( $header, 'boundary=' ) ) {
							$parts    = preg_split( '/boundary=/i', trim( $header ) );
							$boundary = trim( str_replace( array( "'", '"' ), '', $parts[1] ) );
						}
						continue;
					}
					// Explode them out
					list( $name, $content ) = explode( ':', trim( $header ), 2 );

					// Cleanup crew
					$name    = trim( $name );
					$content = trim( $content );

					switch ( strtolower( $name ) ) {
						// Mainly for legacy -- process a From: header if it's there
						case 'from':
							if ( strpos( $content, '<' ) !== false ) {
								// So... making my life hard again?
								$from_name = substr( $content, 0, strpos( $content, '<' ) - 1 );
								$from_name = str_replace( '"', '', $from_name );
								$from_name = trim( $from_name );

								$from_email = substr( $content, strpos( $content, '<' ) + 1 );
								$from_email = str_replace( '>', '', $from_email );
								$from_email = trim( $from_email );
							} else {
								$from_email = trim( $content );
							}
							break;
						default:
							// Add it to our grand headers array
							$headers[ trim( $name ) ] = trim( $content );
							break;
					}
				}
			}
		}

		// From email and name
		// If we don't have a name from the input headers
		if ( ! isset( $from_name ) )
			$from_name = $email->get_from_name();

		// If we don't have an email from the input headers
		if ( ! isset( $from_email ) ) {
			$from_email = $email->get_from_address();
		}

		// Set destination addresses
		if ( ! is_array( $to ) ) {
			$to = explode( ',', $to );
			$to = array_filter( $to ); // remove empty
		}

		$recipients = array();

		foreach ( (array) $to as $recipient ) {
			try {
				// Break $recipient into name and address parts if in the format "Foo <bar@baz.com>"
				$recipient_name = '';
				if ( preg_match( '/(.*)<(.+)>/', $recipient, $matches ) ) {
					if ( count( $matches ) == 3 ) {
						$recipient_name = $matches[1];
						$recipient      = $matches[2];
					}
				}
				$recipients[] = array(
					'email' => $recipient,
					'name'  => $recipient_name,
					'type'  => 'to',
				);
			} catch ( phpmailerException $e ) {
				continue;
			}
		}

		$files_to_attach = array();

		if ( ! empty( $attachments ) ) {
			foreach ( $attachments as $attachment ) {
				try {
					$new_attachment = yith_waitlist_get_attachment_struct( $attachment );

					if ( $new_attachment == false ) {
						continue;
					}

					$files_to_attach[] = $new_attachment;
				} catch ( Exception $e ) {
					continue;
				}
			}
		}

		try {
			$mandrill = new Mandrill( $api_key );
			$message  = apply_filters( 'yith_waitlist_mandrill_send_mail_message', array(
				'html'        => apply_filters( 'woocommerce_mail_content', $email->style_inline( $message ) ),
				'subject'     => $subject,
				'from_email'  => apply_filters( 'wp_mail_from', $from_email ),
				'from_name'   => apply_filters( 'wp_mail_from_name', $from_name ),
				'to'          => $recipients,
				'headers'     => $headers,
				'attachments' => $files_to_attach,
			) );

			$async   = apply_filters( 'yith_waitlist_mandrill_send_mail_async', false );
			$ip_pool = apply_filters( 'yith_waitlist_mandrill_send_mail_ip_pool', null );
			$send_at = apply_filters( 'yith_waitlist_mandrill_send_mail_send_at', null );

			$results = $mandrill->messages->send( $message, $async, $ip_pool, $send_at );
			$return  = true;

			if ( ! empty( $results ) ) {
				foreach ( $results as $result ) {
					if ( ! isset( $result['status'] ) || in_array( $result['status'], array( 'rejected', 'invalid' ) ) ) {
						$return = false;
					}
				}
			}

			return $return;
		} catch ( Mandrill_Error $e ) {
			return false;
		}
	}
}

if ( ! function_exists( 'yith_waitlist_get_attachment_struct' ) ) {
	/**
	 * Using file path, build an attachment struct, to use in Mandrill send request
	 *
	 * @since  1.0.0
	 * @param $path string File absolute path
	 *
	 * @static
	 * @return bool|array
	 * [
	 *     type => mime type of the file
	 *     name => file name with extension
	 *     content => file complete content, divided in chunks
	 * ]
	 * @throws Exception When some error occurs with file handling
	 */
	function yith_waitlist_get_attachment_struct( $path ) {

		$struct = array();

		try {
			if ( ! @is_file( $path ) ) throw new Exception( $path . ' is not a valid file.' );

			$filename = basename( $path );

			$file_buffer = file_get_contents( $path );
			$file_buffer = chunk_split( base64_encode( $file_buffer ), 76, "\n" );

			$mime_type = '';
			if ( function_exists( 'finfo_open' ) && function_exists( 'finfo_file' ) ) {
				$finfo     = finfo_open( FILEINFO_MIME_TYPE );
				$mime_type = finfo_file( $finfo, $path );
			} elseif ( function_exists( 'mime_content_type' ) ) {
				$mime_type = mime_content_type( $path );
			}

			if ( ! empty( $mime_type ) ) {
				$struct['type'] = $mime_type;
			}

			$struct['name']    = $filename;
			$struct['content'] = $file_buffer;

		} catch ( Exception $e ) {
			return false;
		}

		return $struct;
	}
}

if ( ! function_exists( 'yith_waitlist_textarea_editor_html' ) ) {
	/**
	 * Print textarea editor html for email options
	 *
	 * @access public
	 * @since  1.0.0
	 * @author Francesco Licandro
	 * @param array  $data
	 * @param object $email
	 * @param string $key
	 * @return string
	 */
	function yith_waitlist_textarea_editor_html( $key, $data, $email ) {

		$field = $email->get_field_key( $key );

		$defaults = array(
			'title'             => '',
			'disabled'          => false,
			'class'             => '',
			'css'               => '',
			'placeholder'       => '',
			'type'              => 'text',
			'desc_tip'          => false,
			'description'       => '',
			'custom_attributes' => array(),
		);

		$data = wp_parse_args( $data, $defaults );

		$editor_args = array(
			'wpautop'       => true, // use wpautop?
			'media_buttons' => true, // show insert/upload button(s)
			'textarea_name' => esc_attr( $field ), // set the textarea name to something different, square brackets [] can be used here
			'textarea_rows' => 20, // rows="..."
			'tabindex'      => '',
			'editor_css'    => '', // intended for extra styles for both visual and HTML editors buttons, needs to include the <style> tags, can use "scoped".
			'editor_class'  => '', // add extra class(es) to the editor textarea
			'teeny'         => false, // output the minimal editor config used in Press This
			'dfw'           => false, // replace the default fullscreen with DFW (needs specific DOM elements and css)
			'tinymce'       => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
			'quicktags'     => true // load Quicktags, can be used to pass settings directly to Quicktags using an array()
		);

		ob_start();
		?>

		<tr valign="top">
			<th scope="row" class="select_categories">
				<label for="<?php echo esc_attr( $field ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
				<?php echo wp_kses_post( $email->get_tooltip_html( $data ) ); ?>
			</th>
			<td class="forminp">
				<fieldset>
					<div id="<?php echo esc_attr( $field ); ?>-container">
						<div
							class="editor"><?php wp_editor( $email->get_option( $key ), esc_attr( $field ), $editor_args ); ?></div>
						<?php echo wp_kses_post( $email->get_description_html( $data ) ); ?>
					</div>
				</fieldset>
			</td>
		</tr>

		<?php

		return ob_get_clean();
	}
}

if ( ! function_exists( 'yith_waitlist_is_wc26' ) ) {
	/**
	 * Check if WooCommerce version is 2.6
	 *
	 * @author     Francesco Licandro
	 * @deprecated This function is deprecated
	 */
	function yith_waitlist_is_wc26() {
		return version_compare( WC()->version, '2.6', '>=' );
	}
}

if ( ! function_exists( 'yith_waitlist_get_custom_style' ) ) {
	/**
	 * Get custom style from panel options
	 *
	 * @since  1.1.3
	 * @author Francesco Licandro
	 * @return string
	 */
	function yith_waitlist_get_custom_style() {

		// get size font
		$size = yith_wcwtl_get_proteo_default( 'yith-wcwtl-general-font-size', '15' );
		$size = ( $size < 1 || $size > 99 ) ? 15 : intval( $size );

		$add_background     = yith_wcwtl_get_proteo_default( 'yith-wcwtl-button-add-background', '#a46497' );
		$add_color          = yith_wcwtl_get_proteo_default( 'yith-wcwtl-button-add-text-color', '#ffffff' );
		$add_background_h   = yith_wcwtl_get_proteo_default( 'yith-wcwtl-button-add-background-hover', '#935386' );
		$add_color_h        = yith_wcwtl_get_proteo_default( 'yith-wcwtl-button-add-text-color-hover', '#ffffff' );
		$leave_background   = yith_wcwtl_get_proteo_default( 'yith-wcwtl-button-leave-background', '#a46497' );
		$leave_color        = yith_wcwtl_get_proteo_default( 'yith-wcwtl-button-leave-text-color', '#ffffff' );
		$leave_background_h = yith_wcwtl_get_proteo_default( 'yith-wcwtl-button-leave-background-hover', '#935386' );
		$leave_color_h      = yith_wcwtl_get_proteo_default( 'yith-wcwtl-button-leave-text-color-hover', '#ffffff' );
		$font_color         = yith_wcwtl_get_proteo_default( 'yith-wcwtl-general-font-color', '#333333' );

		$css = "#yith-wcwtl-output .button.alt{background:{$add_background};color:{$add_color};}
			#yith-wcwtl-output .button.alt:hover{background:{$add_background_h};color:{$add_color_h};}
			#yith-wcwtl-output .button.button-leave.alt{background:{$leave_background};color:{$leave_color};}
			#yith-wcwtl-output .button.button-leave.alt:hover{background:{$leave_background_h};color:{$leave_color_h};}
			#yith-wcwtl-output p, #yith-wcwtl-output label{font-size:{$size}px;color:{$font_color};}";

		return apply_filters( 'yith_waitlist_custom_style', $css );
	}
}

if ( ! function_exists( 'yith_waitlist_is_double_optin_enabled' ) ) {
	/**
	 * Check if double option email is enabled
	 *
	 * @since  1.5.0
	 * @author Francesco Licandro
	 * @return boolean
	 */
	function yith_waitlist_is_double_optin_enabled() {
		// first of all check the default option and check if is enabled for logged in
		if ( get_option( 'yith-wcwtl-enable-double-optin', 'yes' ) != 'yes'
			|| ( is_user_logged_in() && get_option( 'yith-wcwtl-enable-double-optin-logged', 'yes' ) != 'yes' ) ) {
			return false;
		}
		$emails = YITH_WCWTL()->get_emails();
		if ( ! in_array( 'YITH_WCWTL_Mail_Subscribe_Optin', $emails ) ) {
			return false;
		}

		$mailer = WC()->mailer();
		$email  = $mailer->emails['YITH_WCWTL_Mail_Subscribe_Optin'];

		return $email->is_enabled();
	}
}

if ( ! function_exists( 'yith_waitlist_policy_checkbox' ) ) {
	/**
	 * Add policy checkbox html for subscription form
	 *
	 * @since  1.5.0
	 * @author Francesco Licandro
	 * @return string
	 */
	function yith_waitlist_policy_checkbox() {
		// first check option
		if ( get_option( 'yith-wcwtl-enable-privacy-checkbox', 'yes' ) != 'yes' ) {
			return '';
		}
		$text = get_option( 'yith-wcwtl-privacy-checkbox-text', '' );
		$text = function_exists( 'wc_replace_policy_page_link_placeholders' ) ? wc_replace_policy_page_link_placeholders( $text ) : $text;

		$html = '<label for="yith-wcwtl-policy-check">';
		$html .= '<input type="checkbox" name="yith-wcwtl-policy-check" id="yith-wcwtl-policy-check" value="yes">';
		$html .= '<span>' . wp_kses_post( $text ) . '</span></label>';

		return $html;
	}
}

if ( ! function_exists( 'yith_wcwtl_get_proteo_default' ) ) {
	/**
	 * Filter option default value if Proteo theme is active
	 *
	 * @since  1.5.1
	 * @author Francesco Licandro
	 * @param string  $key
	 * @param mixed   $default
	 * @param boolean $force_default
	 * @return string
	 */
	function yith_wcwtl_get_proteo_default( $key, $default = '', $force_default = false ) {

		// get value from DB if requested and return if not empty
		! $force_default && $value = get_option( $key, $default );

		if ( ! empty( $value ) ) {
			return $value;
		}

		if ( ! defined( 'YITH_PROTEO_VERSION' ) ) {
			return $default;
		}


		switch ( $key ) {
			case 'yith-wcwtl-general-font-size':
				$default = get_theme_mod( 'yith_proteo_base_font_size', 16 );
				break;
			case 'yith-wcwtl-general-font-color':
				$default = get_theme_mod( 'yith_proteo_base_font_color', '#404040' );
				break;
			case 'yith-wcwtl-button-add-background':
			case 'yith-wcwtl-button-leave-background':
				$default = get_theme_mod( 'yith_proteo_button_style_1_bg_color', '#448a85' );
				break;
			case 'yith-wcwtl-button-add-background-hover':
			case 'yith-wcwtl-button-leave-background-hover':
				$default = get_theme_mod( 'yith_proteo_button_style_1_bg_color_hover', yith_proteo_adjust_brightness( get_theme_mod( 'yith_proteo_main_color_shade', '#448a85' ), 0.2 ) );
				break;
			case 'yith-wcwtl-button-add-text-color':
			case 'yith-wcwtl-button-leave-text-color':
				$default = get_theme_mod( 'yith_proteo_button_style_1_text_color', '#ffffff' );
				break;
			case 'yith-wcwtl-button-add-text-color-hover':
			case 'yith-wcwtl-button-leave-text-color-hover':
				$default = get_theme_mod( 'yith_proteo_button_style_1_text_color_hover', '#ffffff' );
				break;

		}

		return $default;
	}
}