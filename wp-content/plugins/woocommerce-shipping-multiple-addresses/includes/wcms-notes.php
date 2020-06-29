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
                            var index = $(this).data("index");
                            var note  = localStorage["ms_note_"+ index];

                            $(this).val(note);
                        });
                    };

                    $("div.woocommerce").on("change", "textarea.ms_shipping_note", function() {
                        var index = $(this).data("index");
                        var note  = $(this).val();

                        localStorage["ms_note_"+ index] = note;
                    });

                    $("body").bind("updated_checkout", function() {
                        apply_notes();
                    })

                    $("form.checkout").on("submit", function() {
                        $("textarea.ms_shipping_note").each(function() {
                            var index = $(this).data("index");
                            localStorage.removeItem("ms_note_"+index);
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

        if ( !isset( $wcms->gateway_settings['checkout_notes'] ) ) {
            $wcms->gateway_settings['checkout_notes'] = 'yes';
        }

        $show_notes = ( !empty($wcms->gateway_settings['checkout_notes']) && $wcms->gateway_settings['checkout_notes'] == 'yes' ) ? true : false;
        $show_datepicker = ( !empty($wcms->gateway_settings['checkout_datepicker']) && $wcms->gateway_settings['checkout_datepicker'] == 'yes' ) ? true : false;

        if ( $show_datepicker ):
            $value    = '';
            $postdata = array();

            if ( !empty( $_POST['post_data'] ) ) {
                parse_str( $_POST['post_data'], $postdata );
            }

            if ( isset( $postdata['shipping_date'] ) && isset( $postdata['shipping_date'][ $loop ]) ) {
                $value = $postdata['shipping_date'][ $loop ];
            }
        ?>
        <div class="datepicker-form">
            <p>
                <label>
                    <?php _e( 'Shipping Date', 'wc_shipping_multiple_address' ); ?>
                </label>
                <input type="text" class="datepicker ms_shipping_date" name="shipping_date[<?php echo $loop; ?>]" data-index="<?php echo $loop; ?>" value="<?php echo esc_attr( $value ); ?>" />
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
                    <?php _e( 'Note', 'wc_shipping_multiple_address' ); ?>
                </label>
                <textarea name="shipping_note[<?php echo $loop; ?>]" rows="2" cols="30" class="ms_shipping_note" data-index="<?php echo $loop; ?>" data-limit="<?php echo $limit; ?>"></textarea>
                <?php if ( !empty( $limit ) ): ?>
                    <small><em><?php printf( __('Character Limit: %d', 'wc_shipping_multiple_address'), $limit ); ?></em></small>
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

        if ( !empty($_POST['shipping_note']) ) {
            foreach ( $_POST['shipping_note'] as $idx => $value ) {

                if ( !isset( $packages[ $idx ] ) ) {
                    continue;
                }

                $packages[ $idx ]['note'] = esc_html( $value );

            }
        }

        if ( !empty($_POST['shipping_date']) ) {
            foreach ( $_POST['shipping_date'] as $idx => $value ) {

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
        $packages = get_post_meta( $order_id, '_wcms_packages', true );

        if ( !empty($_POST['shipping_note']) ) {


            foreach ( $_POST['shipping_note'] as $idx => $value ) {

                if (! array_key_exists( $idx, $packages ) )
                    continue;

                update_post_meta( $order_id, '_note_'. $idx, $value );

            }
        }

        if ( !empty($_POST['shipping_date']) ) {


            foreach ( $_POST['shipping_date'] as $idx => $value ) {

                if (! array_key_exists( $idx, $packages ) )
                    continue;

                update_post_meta( $order_id, '_date_'. $idx, $value );

            }
        }

    }

    public static function render_notes( $order, $package, $package_index ) {

        if ( isset( $package['note'] ) && !empty( $package['note'] ) ) {
        ?>
            <ul class="order_notes">
                <li class="note">
                    <div class="note_content">
                        <?php echo esc_html( $package['note'] ); ?>
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
                        <?php printf( __('Shipping Date: %s', 'wc_shipping_multiple_address'), esc_html( $package['date'] ) ); ?>
                    </div>
                </li>
            </ul>
        <?php
        }

        return;
    }

}
