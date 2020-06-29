<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'YITH_WCARS_VERSION' ) ) {
    exit( 'Direct access forbidden.' );
}

/**
 *
 *
 * @class      YITH_Advanced_Refund_System_Frontend_Premium
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Carlos Mora <carlos.eugenio@yourinspiration.it>
 *
 */

if ( ! class_exists( 'YITH_Advanced_Refund_System_Frontend_Premium' ) ) {
    /**
     * Class YITH_Advanced_Refund_System_Frontend_Premium
     *
     * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
     */
    class YITH_Advanced_Refund_System_Frontend_Premium extends YITH_Advanced_Refund_System_Frontend {

        // WC_Product object if it is not set by theme in 'woocommerce_stock_html' filter.
        public $_product_from_availability;

        /**
         * Construct
         *
         * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
         * @since 1.0.0
         */
        public function __construct() {
            parent::__construct();
            add_action( 'woocommerce_order_item_meta_end', array( $this, 'refund_single_product_button' ), 21, 3 );
            add_action( 'ywcars_request_form_before_reason', array( $this, 'add_qty_field_to_form' ), 10, 2 );
	        add_action( 'ywcars_request_form_after_reason', array( $this, 'add_attachment_field_to_form' ), 10, 2 );
	        if ( version_compare( WC()->version, '3.0.0', '<' ) ) {
		        add_filter( 'woocommerce_stock_html', array( $this, 'non_refundable_message_legacy' ), 10, 3 );
	        } else {
		        add_filter( 'woocommerce_get_stock_html', array( $this, 'non_refundable_message' ), 10, 2 );
	        }
            add_filter( 'woocommerce_get_availability', array( $this, 'get_product_from_availability' ), 10, 2 );
        }

	    public function refund_whole_order_button( $order ) {
		    if ( ! apply_filters( 'ywcars_is_account_page', is_account_page() ) ) {
			    return;
		    }
		    if ( ! $order ) {
			    return;
		    }
		    if ( YITH_Advanced_Refund_System_Request_Manager::order_has_wc_refunds( $order ) ) {
		        return;
            }
		    if ( ! $this->order_has_minimum_order_amount( $order ) ) {
			    return;
		    }
		    if ( ! $this->order_has_enough_ndays( $order ) ) {
			    return;
		    }

		    $refundable = get_option( 'yith_wcars_allow_refunds' );
		    if ( 'yes' != $refundable ) {
			    return;
		    }
		    $requests = yit_get_prop( $order, '_ywcars_requests', true );
		    $text = apply_filters( 'ywcars_refund_entire_order_text', esc_html__( 'Refund my entire order', 'yith-advanced-refund-system-for-woocommerce' ), $order );
		    if ( ! $requests ) {
			    $show_refund_button = true;
			    foreach ( $order->get_items() as $item ) {
				    $product_id = empty( $item['variation_id'] ) ? $item['product_id'] : $item['variation_id'];
				    if ( ! $this->product_can_show_refundable_button( $order, $product_id ) ) {
					    $show_refund_button = false;
					    break;
				    }
			    }
			    if ( $show_refund_button ) {
				    $params = array(
					    'ajax'       => 'true',
					    'action'     => 'ywcars_open_request_window',
					    'order_id'   => $order->get_id(),
					    'target'     => 'whole_order',
					    'line_total' => $order->get_total()
				    );
				    $link = add_query_arg( $params, admin_url( 'admin-ajax.php', apply_filters( 'ywcars_ajax_url_scheme_frontend', '' ) ) );
				    ?>
                    <div class="ywcars_button_refund_container ywcars_whole_order" style="margin-bottom: 10px;">
                        <a class="button ywcars_button_refund" data-rel="prettyPhoto"
                           href="<?php echo $link; ?>"><?php echo $text; ?></a>
                    </div>
				    <?php
			    } else {
				    ?>
                    <div class="ywcars_button_refund_container ywcars_whole_order" style="margin-bottom: 10px;">
                        <button disabled title="<?php esc_html_e( 'Refund on the entire order is not allowed', 'yith-advanced-refund-system-for-woocommerce' ); ?>"
                                class="button ywcars_button_refund"><?php echo $text; ?></button>
                    </div>
				    <?php
                }
		    } else {
			    ?>
                <div class="ywcars_button_refund_container ywcars_whole_order" style="margin-bottom: 10px;">
                    <button disabled title="<?php esc_html_e( 'Refund on the entire order is not allowed', 'yith-advanced-refund-system-for-woocommerce' ); ?>"
                            class="button ywcars_button_refund"><?php echo $text; ?></button>
                </div>
			    <?php
            }
	    }

        public function refund_single_product_button( $item_id, $item, $order ) {
	        if ( ! apply_filters( 'ywcars_is_account_page', is_account_page() ) ) {
                return;
            }
	        if ( ! $item_id || ! $item || ! $order ) {
		        return;
	        }
	        $product_id = empty( $item['variation_id'] ) ? $item['product_id'] : $item['variation_id'];
	        $text = apply_filters( 'ywcars_ask_for_refund_text', esc_html__( 'Ask for a refund', 'yith-advanced-refund-system-for-woocommerce' ), $product_id, $item, $order );
	        if ( $this->product_can_show_refundable_button( $order, $product_id ) ) {
		        $max_quantity = $this->item_qty_available( $item, $order );
		        if ( $max_quantity ) {
			        $params = array(
				        'ajax'       => 'true',
				        'action'     => 'ywcars_open_request_window',
				        'order_id'   => $order->get_id(),
				        'item_id'    => $item_id,
				        'target'     => $product_id,
				        'qty'        => $max_quantity,
				        'qty_total'  => $item['qty'],
				        'line_total' => $item['line_total']
			        );
			        $link = add_query_arg( $params, admin_url( 'admin-ajax.php', apply_filters( 'ywcars_ajax_url_scheme_frontend', '' ) ) );
			        ?>
                    <div class="ywcars_button_refund_container">
                        <a class="button ywcars_button_refund" data-rel="prettyPhoto"
                           href="<?php echo $link; ?>"><?php echo $text; ?></a>
                    </div>
			        <?php
		        } else {
			        ?>
                    <div class="ywcars_button_refund_container">
                        <button disabled title="<?php esc_html_e( 'No further refunds can be granted on this item', 'yith-advanced-refund-system-for-woocommerce' ); ?>"
                           class="button ywcars_button_refund" ><?php echo $text; ?></button>
                    </div>
			        <?php
                }
	        } else {
		        ?>
                <div class="ywcars_button_refund_container">
                    <button disabled title="<?php esc_html_e( 'No refund shall be granted on this product', 'yith-advanced-refund-system-for-woocommerce' ); ?>"
                       class="button ywcars_button_refund"><?php echo $text; ?></button>
                </div>
		        <?php
            }
        }

	    public function order_has_minimum_order_amount( $order ) {
		    $order_total = $order->get_total();
		    $minimum_order_amount = get_option( 'yith_wcars_minimum_order_amount' );
		    if ( $order_total >= $minimum_order_amount ) {
			    return true;
		    }
		    return false;
	    }


        public function product_has_enough_ndays( $product, $order ) {
	        $order_date = yit_get_prop( $order, '_paid_date', true ) ? yit_get_prop( $order, '_paid_date', true ) : yit_get_prop( $order, '_completed_date', true );
	        $order_date = apply_filters( 'yith_wcars_order_date', $order_date, $order, $product );

            $ndays_global_value = get_option( 'yith_wcars_ndays_refund' );
            $product_ndays_type = yit_get_prop( $product, '_ywcars_ndays_refund_type', true );
            $ndays_type         = empty( $product_ndays_type ) ? 'global' : $product_ndays_type;
            $product_ndays      = yit_get_prop( $product, '_ywcars_ndays_refund', true );
            $ndays              = empty( $product_ndays ) ? $ndays_global_value : $product_ndays;

            if ( 'parent' == $ndays_type ) {
	            $parent = version_compare( WC()->version, '3.0.0', '<' ) ? $product->get_parent() : $product->get_parent_id();
	            $parent = wc_get_product( $parent );
                $ndays = yit_get_prop( $parent, '_ywcars_ndays_refund', true );
            }
            if ( 'global' == $ndays_type ) {
                $ndays = $ndays_global_value;
            }

	        if ( ! $ndays && $order_date ) {
		        return true;
	        }
            if ( $order_date && ( ( $ndays * DAY_IN_SECONDS ) + strtotime( $order_date ) ) > time() ) {
                return true;
            }
            return false;
        }


        public function product_id_is_refundable( $id ) {
            $product = wc_get_product( $id );
            $refundable_global_option = get_option('yith_wcars_allow_refunds');
            $product_refundable = yit_get_prop( $product, '_ywcars_refundable', true );
            $refundable = empty( $product_refundable )
                ? $refundable_global_option
                : $product_refundable;
            if ( 'parent' == $refundable ) {
                $parent = version_compare( WC()->version, '3.0.0', '<' ) ? $product->get_parent() : $product->get_parent_id();
                $parent = wc_get_product( $parent );
                $refundable = yit_get_prop( $parent, '_ywcars_refundable', true );
            }
            if ( 'global' == $refundable ) {
                $refundable = $refundable_global_option;
            }

            return $refundable;

        }


        public function product_can_show_refundable_button( $order, $product_id ) {
            if ( ! $order ) {
                return false;
            }
	        if ( YITH_Advanced_Refund_System_Request_Manager::order_has_wc_refunds( $order ) ) {
		        return false;
	        }
            if ( ! $this->order_has_minimum_order_amount( $order ) ) {
                return false;
            }

            $product = wc_get_product( $product_id );
            if ( ! $this->product_has_enough_ndays( $product, $order ) ) {
                return false;
            }

            $refundable = $this->product_id_is_refundable( $product );
            if ( 'yes' != $refundable ) {
                return false;
            }
            $requests = yit_get_prop( $order, '_ywcars_requests', true );
            if ( $requests ) {
                foreach ( $requests as $request_id ) {
                    $request = new YITH_Refund_Request( $request_id );
                    if ( ! ( $request instanceof YITH_Refund_Request && $request->exists() ) ) {
                        continue;
                    }
                    if ( $request->whole_order ) {
                        return false;
                    }
                }
            }

            return true;
        }

        public function item_qty_available( $item, $order ) {
            if ( ! $item || ! $order ) {
                return false;
            }

            $requests = yit_get_prop( $order, '_ywcars_requests', true );
            $qty_available = $item['qty'];

            if ( $requests ) {
                foreach ( $requests as $request_id ) {
                    $request = new YITH_Refund_Request( $request_id );

                    if ( $request->exists() && ( $request->product_id == $item['product_id'] || $request->product_id == $item['variation_id'] ) ) {
                        $qty_available = (int) $qty_available - (int) $request->qty;
                    }
                }
            }
            return $qty_available;
        }

        public function add_qty_field_to_form( $order, $is_whole_order ) {
            if ( $is_whole_order ) {
                return;
            }
            ! empty( $_GET['qty'] ) ? $qty = $_GET['qty'] : $qty = '';
	        ! empty( $_GET['qty_total'] ) ? $qty_total = $_GET['qty_total'] : $qty_total = '';
            ! empty( $_GET['item_id'] ) ? $item_id = $_GET['item_id'] : $item_id = '';
            ?>
            <div class="ywcars_block">
                <label><?php echo apply_filters( 'ywcars_qty_block_text', esc_html__( 'Please, select the amount of items:', 'yith-advanced-refund-system-for-woocommerce' ), $order, $is_whole_order ); ?>
                    <input type="hidden" name="ywcars_form_item_id" value="<?php echo $item_id; ?>">
                    <input type="number" name="ywcars_form_qty" class="ywcars_form_qty" min="1" max="<?php echo $qty; ?>" step="1">
                    <input type="hidden" name="ywcars_form_max_qty" value="<?php echo $qty; ?>">
                    <input type="hidden" name="ywcars_form_qty_total" value="<?php echo $qty_total; ?>">
                </label>
            </div>
            <?php
        }

	    public function add_attachment_field_to_form( $order, $is_whole_order ) {
            ?>
            <div class="ywcars_block">
                <div><label for="ywcars_form_attachment"><?php
					    esc_html_e( 'Attach a file (optional)', 'yith-advanced-refund-system-for-woocommerce' );
					    ?></label></div>
                <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo get_option( 'yith_wcars_max_file_size', YITH_WCARS_ONE_KILOBYTE_IN_BYTES ) * YITH_WCARS_ONE_KILOBYTE_IN_BYTES; ?>" />
                <input type="file" id="ywcars_form_attachment" name="ywcars_form_attachment[]" multiple <?php echo 'yes' == get_option( 'yith_wcars_enable_only_images', 'no' ) ? 'accept="image/*"' : ''; ?>>
            </div>
            <?php
	    }

        public function non_refundable_message_legacy( $availability_html, $availability, $product = false ) {
	        if ( ! $product ) {
		        $product = $this->_product_from_availability;
	        }
	        return $this->non_refundable_message( $availability_html, $product );
        }

        public function non_refundable_message( $availability_html, $product ) {
            $id = $this->get_wpml_product_id( $product );
            $product = wc_get_product( $id );
            $refundable = $this->product_id_is_refundable( $id );

            if ( 'no' == $refundable ) {
                $message_type = yit_get_prop( $product, '_ywcars_message_type', true );
                $message = yit_get_prop( $product, '_ywcars_message', true );
                $color = get_option( 'yith_wcars_message_color', '#000000' );
                if ( 'parent' == $message_type ) {
	                $parent = version_compare( WC()->version, '3.0.0', '<' ) ? $product->get_parent() : $product->get_parent_id();
	                $parent = wc_get_product( $parent );
                    $message_type = yit_get_prop( $parent, '_ywcars_message_type', true );
                    $message = yit_get_prop( $parent, '_ywcars_message', true );
                }
                if ( 'global' == $message_type ) {
                    $message = get_option( 'yith_wcars_message' );
                }

                return $availability_html . '<div style="font-weight: bold; color: ' . $color . '">' . $message . '</div>';
            }
            return $availability_html;
        }

        public function get_product_from_availability( $availability, $product ) {
            $this->_product_from_availability = $product;
            return $availability;
        }

        public function get_wpml_product_id( $product ) {
            global $sitepress;
            return $sitepress ? yit_wpml_object_id( $product->get_id(), 'product', true, $sitepress->get_default_language() ) : $product->get_id();
        }



    }
}