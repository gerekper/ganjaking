<?php

if ( ! defined( 'ABSPATH' ) )
    exit; // Exit if accessed directly

class WC_MS_Notes {

    private $wcms;

    public function __construct( WC_Ship_Multiple $wcms ) {
        $this->wcms = $wcms;

        add_filter( 'wc_ms_multiple_shipping_checkout_locale', array(__CLASS__, 'add_datepicker_variables' ) );
        add_action( 'wp_footer', array( __CLASS__, 'checkout_scripts' ) );
        add_action( 'wc_ms_shipping_package_block', array( __CLASS__, 'render_note_form'), 10, 2 );

        add_action( 'woocommerce_checkout_update_order_meta', array( __CLASS__, 'store_order_notes'), 20, 2 );

        // Modify the packages, shipping methods and addresses in the session
        add_filter( 'wc_ms_checkout_session_packages', array( __CLASS__, 'apply_notes_to_packages' ), 30 );

        add_action( 'wc_ms_order_package_block_before_address', array( __CLASS__, 'render_notes'), 11, 3 );
        add_action( 'wc_ms_order_package_block_before_address', array( __CLASS__, 'render_dates'), 11, 3 );
    }

    /**
     * Add datepicker settings to the WCMS JS array
     * @param array $wcms_js
     * @return array
     */
    public static function add_datepicker_variables( $wcms_js ) {
        global $wcms;
        $settings = $wcms->gateway_settings;

        $show_datepicker = ( ! empty( $settings['checkout_datepicker'] ) && $settings['checkout_datepicker'] == 'yes' ) ? true : false;

        // set to enable all days by default
        $wcms_js['datepicker_valid_days'] = array(0,1,2,3,4,5,6);

        if ( $show_datepicker && ! empty( $settings['checkout_valid_days'] ) ) {
            $wcms_js['datepicker_valid_days'] = array_map('absint', $settings['checkout_valid_days'] );
        }

        // set excluded dates
        $wcms_js['datepicker_excluded_dates'] = array();
        if ( $show_datepicker && ! empty( $settings['checkout_exclude_dates'] ) ) {
            $wcms_js['datepicker_excluded_dates'] = $settings['checkout_exclude_dates'];
        }

        return $wcms_js;
    }

    /**
     * Store the notes in the checkout form immediately after they are entered
     */
    public static function checkout_scripts() {

        if ( !is_checkout() ) {
            return;
        }

        ?>
        <script>
            jQuery(document).ready(function($) {

                $("form.checkout").on("keypress", "textarea.ms_shipping_note", function(e) {
                    var val     = $(this).val(),
                        length  = val.length,
                        limit   = $(this).data("limit"),
                        remain = parseInt(limit - length);

                    if ( limit > 0 ) {
                        if (remain <= 0 && e.which !== 0 && e.charCode !== 0) {
                            $(this).val((val).substring(0, length - 1))
                        }
                    }

                });

                if ( supports_html5_storage() ) {

                    var apply_notes = function() {
                        $("textarea.ms_shipping_note").each(function() {
							var hash = $( this ).data( 'hash' );
							var note_hash = localStorage["ms_note_" + hash ];
							$(this).val(note_hash);
                        });
                    };

                    $("div.woocommerce").on("change", "textarea.ms_shipping_note", function() {
                        var note  = $(this).val();

						var hash = $( this ).data( 'hash' );
						localStorage["ms_note_"+ hash] = note;
                    });

                    $("body").on("updated_checkout", function() {
                        apply_notes();
                    })

                    $("form.checkout").on("submit", function() {
                        $("textarea.ms_shipping_note").each(function() {
							var hash = $( this ).data( 'hash' );
							localStorage.removeItem("ms_note_" + hash);
                        });
                    });
                }
            });
        </script>
        <?php
    }

    /**
     * Show the note checkbox on the shipping packages blocks
     */
    public static function render_note_form( $loop, $package ) {
        global $wcms;

		$package_hash = md5( json_encode( $package ) );

        if ( !isset( $wcms->gateway_settings['checkout_notes'] ) ) {
            $wcms->gateway_settings['checkout_notes'] = 'yes';
        }

        $show_notes = ( !empty($wcms->gateway_settings['checkout_notes']) && $wcms->gateway_settings['checkout_notes'] == 'yes' ) ? true : false;
        $show_datepicker = ( !empty($wcms->gateway_settings['checkout_datepicker']) && $wcms->gateway_settings['checkout_datepicker'] == 'yes' ) ? true : false;

        if ( $show_datepicker ):
            $value    = '';
            $postdata = array();
			// No need to verify nonce. It's already verified.
            if ( !empty( $_POST['post_data'] ) ) { // phpcs:ignore
                parse_str( $_POST['post_data'], $postdata ); //phpcs:ignore
				$postdata = wc_clean( $postdata );
            }

            if ( isset( $postdata['shipping_date'] ) && isset( $postdata['shipping_date'][ $loop ]) ) {
                $value = $postdata['shipping_date'][ $loop ];
            }
        ?>
        <div class="datepicker-form">
            <p>
                <label>
                    <?php esc_html_e( 'Shipping Date', 'wc_shipping_multiple_address' ); ?>
                </label>
                <input type="text" class="datepicker ms_shipping_date" name="shipping_date[<?php echo esc_attr( $loop ); ?>]" data-index="<?php echo esc_attr( $loop ); ?>" value="<?php echo esc_attr( $value ); ?>" />
            </p>
        </div>
        <?php
        endif;

        if ( $show_notes ):
            $limit = !empty( $wcms->gateway_settings['checkout_notes_limit'] ) ? absint( $wcms->gateway_settings['checkout_notes_limit'] ) : 0;
        ?>
        <div class="note-form">
            <p>
                <label>
                    <?php esc_html_e( 'Note', 'wc_shipping_multiple_address' ); ?>
                </label>
                <textarea name="shipping_note[<?php echo esc_attr( $loop ); ?>]" rows="2" cols="30" class="ms_shipping_note" data-index="<?php echo esc_attr( $loop ); ?>" data-hash="<?php echo esc_attr( $package_hash ); ?>" data-limit="<?php echo esc_attr( $limit ); ?>"></textarea>
                <?php if ( ! empty( $limit ) ): ?>
                    <small><em><?php printf( esc_html__( 'Character Limit: %d', 'wc_shipping_multiple_address' ), esc_html( $limit ) ); ?></em></small>
                <?php endif; ?>
            </p>
        </div>
        <?php
        endif;

    }

    /**
     * Modify the 'wcms_packages' session data to attach notes from POST
     */
    public static function apply_notes_to_packages( $packages ) {
		// No need to verify. It's already been verified.
		$post_note = isset( $_POST['shipping_note'] ) ? wc_clean( $_POST['shipping_note'] ) : array(); //phpcs:ignore
		$post_date = isset( $_POST['shipping_date'] ) ? wc_clean( $_POST['shipping_date'] ) : array(); //phpcs:ignore

        if ( !empty( $post_note ) && is_array( $post_note ) ) {
            foreach ( $post_note as $idx => $value ) {

                if ( !isset( $packages[ $idx ] ) ) {
                    continue;
                }

                $packages[ $idx ]['note'] = esc_html( $value );

            }
        }

        if ( ! empty( $post_date ) && is_array( $post_date ) ) {
            foreach ( $post_date as $idx => $value ) {

                if ( !isset( $packages[ $idx ] ) ) {
                    continue;
                }

                $ts = strtotime( $value );

                if ( $ts ) {
                    $packages[ $idx ]['date'] = date( get_option('date_format'), $ts );
                } else {
                    $packages[ $idx ]['date'] = esc_html( $value );
                }

            }
        }

        return $packages;

    }

    public static function store_order_notes( $order_id ) {
        $order = wc_get_order( $order_id );

        if ( ! $order ) {
            return;
        }

		// No need to verify nonce. It's already verified.
        $packages  = $order->get_meta( '_wcms_packages' );
		$post_note = isset( $_POST['shipping_note'] ) ? wc_clean( $_POST['shipping_note'] ) : array(); //phpcs:ignore
		$post_date = isset( $_POST['shipping_date'] ) ? wc_clean( $_POST['shipping_date'] ) : array(); //phpcs:ignore

        if ( ! empty( $post_note ) && is_array( $post_note ) ) {
            foreach ( $post_note as $idx => $value ) {
                if ( ! array_key_exists( $idx, $packages ) ) {
                    continue;
                }

                $order->update_meta_data( '_note_' . $idx, $value );
            }
        }

        if ( ! empty( $post_date ) && is_array( $post_date ) ) {
            foreach ( $post_date as $idx => $value ) {
                if ( ! array_key_exists( $idx, $packages ) ) {
                    continue;
				}

                $order->update_meta_data( '_date_' . $idx, $value );
            }
        }
        
        $order->save();		
    }

    public static function render_notes( $order, $package, $package_index ) {
		$allowed_html = array(
			'a'      => array(
				'href'  => array(),
				'title' => array(),
			),
			'br'     => array(),
			'em'     => array(),
			'strong' => array(),
		);

        if ( isset( $package['note'] ) && !empty( $package['note'] ) ) {
        ?>
            <ul class="order_notes">
                <li class="note">
                    <div class="note_content">
                        <?php echo wp_kses( nl2br( $package['note'] ), $allowed_html ); ?>
                    </div>
                </li>
            </ul>
        <?php
        }

        return;
    }

    public static function render_dates( $order, $package, $package_index ) {

        if ( isset( $package['date'] ) && !empty( $package['date'] ) ) {
            ?>
            <ul class="order_notes">
                <li class="note">
                    <div class="note_content">
                        <?php printf( esc_html__('Shipping Date: %s', 'wc_shipping_multiple_address'), esc_html( $package['date'] ) ); ?>
                    </div>
                </li>
            </ul>
        <?php
        }

        return;
    }

}
