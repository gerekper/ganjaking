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
 * @class      YITH_Advanced_Refund_System_Admin_Premium
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Carlos Mora <carlos.eugenio@yourinspiration.it>
 *
 */

if ( ! class_exists( 'YITH_Advanced_Refund_System_Admin_Premium' ) ) {
    /**
     * Class YITH_Advanced_Refund_System_Admin_Premium
     *
     * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
     */
    class YITH_Advanced_Refund_System_Admin_Premium extends YITH_Advanced_Refund_System_Admin {
        /**
         * Construct
         *
         * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
         * @since 1.0.0
         */
        public function __construct() {
	        $this->show_premium_landing = false;

	        add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
	        add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );

            parent::__construct();
            add_filter( 'woocommerce_product_data_tabs', array( $this, 'refunds_tab' ), 5 );
            add_action( 'woocommerce_product_data_panels', array( $this, 'refunds_tab_content' ) );
            add_action( 'woocommerce_process_product_meta', array( $this, 'update_product' ) );
            add_action( 'woocommerce_product_after_variable_attributes', array( $this, 'variations_content' ), 10, 3 );
            add_action( 'woocommerce_save_product_variation', array( $this, 'save_variations' ), 10, 2 );
            add_action( 'ywcars_admin_before_submit', array( $this, 'add_attachment_field_on_cpt' ) );
            add_action( 'ywcars_before_items_table_end', array( $this, 'show_go_to_coupon_link' ) );
            add_action( 'ywcars_after_action_buttons', array( $this, 'add_offer_coupon_button' ), 10, 2 );
	        add_filter( 'add_menu_classes', array( $this, 'new_refund_requests_count' ) );
	        add_filter( 'yith_wcars_settings_options', array( $this, 'add_premium_plugin_options' ) );
        }

	    /**
	     * Register plugins for activation tab
	     *
	     * @return void
	     * @since    2.0.0
	     * @author   Andrea Grillo <andrea.grillo@yithemes.com>
	     */
	    public function register_plugin_for_activation() {

		    if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
			    require_once YITH_WCARS_PATH . '/plugin-fw/licence/lib/yit-licence.php';
			    require_once YITH_WCARS_PATH . '/plugin-fw/lib/yit-plugin-licence.php';
		    }

		    YIT_Plugin_Licence()->register( YITH_WCARS_INIT, YITH_WCARS_SECRETKEY, YITH_WCARS_SLUG );
	    }

	    /**
	     * Register plugins for update tab
	     *
	     * @return void
	     * @since    2.0.0
	     * @author   Andrea Grillo <andrea.grillo@yithemes.com>
	     */
	    public function register_plugin_for_updates() {
		    if ( ! class_exists( 'YIT_Upgrade' ) ) {
			    require_once( YITH_WCARS_PATH . '/plugin-fw/lib/yit-upgrade.php' );
		    }
		    YIT_Upgrade()->register( YITH_WCARS_SLUG, YITH_WCARS_INIT );
	    }

	    public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_WCARS_INIT' ) {
		    $new_row_meta_args = parent::plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file );

		    if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ){
			    $new_row_meta_args['is_premium'] = true;
		    }

		    return $new_row_meta_args;
	    }

	    public function action_links( $links ) {
		    $links = yith_add_action_links( $links, $this->_panel_page, true );
		    return $links;
	    }

        public function refunds_tab( $product_data_tabs ) {
            $new_tab = array(
                'ywcars_refunds' => array(
                    'label'  => esc_html__( 'Refunds', 'yith-advanced-refund-system-for-woocommerce' ),
                    'target' => 'ywcars_refunds_product_data',
                    'class'  => array( 'hide_if_grouped', 'hide_if_external' ),
                )
            );
            return array_merge( $product_data_tabs, $new_tab );
        }

        public function refunds_tab_content() {
            global $thepostid;

            $product = wc_get_product( $thepostid );
            $refundable = yit_get_prop( $product, '_ywcars_refundable', true );
            $refundable_global_option = get_option( 'yith_wcars_allow_refunds', 'yes' );

            ?><div id="ywcars_refunds_product_data" class="panel woocommerce_options_panel hidden">
            <div class="options_group">
                <fieldset class="form-field _ywpo_price_adjustment_field">
                    <legend><?php
                        esc_html_e( 'Set the refundable status', 'yith-advanced-refund-system-for-woocommerce' );
                        ?></legend>
                    <ul class="wc-radios">
                        <li>
                            <label>
                                <input name="<?php echo esc_attr( '_ywcars_refundable' ); ?>"
                                       value="<?php echo esc_attr( 'global' ); ?>" type="radio"
                                       class="<?php echo esc_attr( 'ywcars_refundable_radio' ); ?>"
                                    <?php echo checked( empty( $refundable )
                                        ? 'global'
                                        : $refundable, esc_attr( 'global' ), false ); ?>
                                />
                                <span><?php
                                    esc_html_e( 'Use the global value', 'yith-advanced-refund-system-for-woocommerce' );
                                    echo ' (';
                                    esc_html_e( 'Currently:', 'yith-advanced-refund-system-for-woocommerce' );
	                                echo ' ';
                                    if ( 'yes' == $refundable_global_option ) {
                                        esc_html_e( 'Yes', 'yith-advanced-refund-system-for-woocommerce' );
                                    } else {
                                        esc_html_e( 'No', 'yith-advanced-refund-system-for-woocommerce' );
                                    }
                                    echo ')';
                                    ?></span>
                            </label>
                        </li>
                        <li>
                            <label>
                                <input name="<?php echo esc_attr( '_ywcars_refundable' ); ?>"
                                       value="<?php echo esc_attr( 'yes' ); ?>" type="radio"
                                       class="<?php echo esc_attr( 'ywcars_refundable_radio' ); ?>"
                                    <?php echo checked( $refundable, esc_attr( 'yes' ), false ); ?>
                                />
                                <?php esc_html_e( 'Set the product as refundable', 'yith-advanced-refund-system-for-woocommerce' ); ?>
                            </label>
                        </li>
                        <li>
                            <label>
                                <input name="<?php echo esc_attr( '_ywcars_refundable' ); ?>"
                                       value="<?php echo esc_attr( 'no' ); ?>" type="radio"
                                       class="<?php echo esc_attr( 'ywcars_refundable_radio' ); ?>"
                                    <?php echo checked( $refundable, esc_attr( 'no' ), false ); ?>
                                />
                                <?php esc_html_e( 'Set the product as non-refundable', 'yith-advanced-refund-system-for-woocommerce' ); ?>
                            </label>
                        </li>
                    </ul>
                    <?php echo wc_help_tip( esc_html__( 'Choose whether refunds are allowed for this product or not.',
                        'yith-advanced-refund-system-for-woocommerce' ) ); ?>
                </fieldset>
            </div><?php

            $ndays_refund_type = yit_get_prop( $product, '_ywcars_ndays_refund_type', true );
            $ndays_global_option = get_option( 'yith_wcars_ndays_refund', 30 );
            ?><div class="options_group">
                <fieldset class="form-field _ywpo_price_adjustment_field">
                    <legend><?php
                        esc_html_e( 'Number of days for refund', 'yith-advanced-refund-system-for-woocommerce' );
                        ?></legend>
                    <ul class="wc-radios">
                        <li>
                            <label>
                                <input name="<?php echo esc_attr( '_ywcars_ndays_refund_type' ); ?>"
                                       value="<?php echo esc_attr( 'global' ); ?>" type="radio"
                                       class="<?php echo esc_attr( 'ywcars_ndays_radio' ); ?>"
                                    <?php echo checked( empty( $ndays_refund_type )
                                        ? 'global'
                                        : $ndays_refund_type, esc_attr( 'global' ), false ); ?>
                                />
                                <span><?php
                                    esc_html_e( 'Use the global value', 'yith-advanced-refund-system-for-woocommerce' );
                                    echo ' (';
                                    esc_html_e( 'Currently:', 'yith-advanced-refund-system-for-woocommerce' );
                                    echo ' ' . $ndays_global_option;
                                    echo ')';
                                    ?></span>
                            </label>
                        </li>
                        <li>
                            <label>
                                <input name="<?php echo esc_attr( '_ywcars_ndays_refund_type' ); ?>"
                                       value="<?php echo esc_attr( 'custom' ); ?>" type="radio"
                                       class="<?php echo esc_attr( 'ywcars_ndays_radio' ); ?>"
                                    <?php echo checked( $ndays_refund_type, esc_attr( 'custom' ), false ); ?>
                                />
                                <?php esc_html_e( 'Set a custom number of days', 'yith-advanced-refund-system-for-woocommerce' ); ?>
                            </label>
                        </li>
                    </ul>
                    <?php echo wc_help_tip( esc_html__( 'Enter the maximum time (in days) to allow refunds. Set 0 if refunds for this product are always
                    available',
                        'yith-advanced-refund-system-for-woocommerce' ) ); ?>
                </fieldset><?php
                woocommerce_wp_text_input( array(
                    'id'                => '_ywcars_ndays_refund',
                    'label'             => esc_html__( 'Custom number of days', 'yith-advanced-refund-system-for-woocommerce' ),
                    'desc_tip'          => true,
                    'description'       => esc_html__( '0 means that refunds for this product are always available',
                        'yith-advanced-refund-system-for-woocommerce' ),
                    'type'              => 'number',
                    'custom_attributes' => array(
                        'step' => '1',
                        'min'  => '0'
                    ),
                ) );
                ?></div><?php

            $message_type = yit_get_prop( $product, '_ywcars_message_type', true );
            $message_general = get_option( 'yith_wcars_message' );
            ?><div class="options_group">
                <fieldset class="form-field _ywpo_price_adjustment_field">
                    <legend><?php
                        esc_html_e( 'Non-refundable message', 'yith-advanced-refund-system-for-woocommerce' );
                        ?></legend>
                    <ul class="wc-radios">
                        <li>
                            <label>
                                <input name="<?php echo esc_attr( '_ywcars_message_type' ); ?>"
                                       value="<?php echo esc_attr( 'global' ); ?>" type="radio"
                                       class="<?php echo esc_attr( 'ywcars_message_radio' ); ?>"
                                    <?php echo checked( empty( $message_type )
                                        ? 'global'
                                        : $message_type, esc_attr( 'global' ), false ); ?>
                                />
                                <span title="<?php
                                echo $message_general ?
                                    sprintf( esc_html__( 'Current message: \'%s\'', 'yith-advanced-refund-system-for-woocommerce' ), $message_general ) : ''; ?>"><?php
                                    esc_html_e( 'Use the global value', 'yith-advanced-refund-system-for-woocommerce' );
                                    ?></span>
                            </label>
                        </li>
                        <li>
                            <label>
                                <input name="<?php echo esc_attr( '_ywcars_message_type' ); ?>"
                                       value="<?php echo esc_attr( 'custom' ); ?>" type="radio"
                                       class="<?php echo esc_attr( 'ywcars_message_radio' ); ?>"
                                    <?php echo checked( $message_type, esc_attr( 'custom' ), false ); ?>
                                />
                                <?php esc_html_e( 'Use a custom message', 'yith-advanced-refund-system-for-woocommerce' ); ?>
                            </label>
                        </li>
                    </ul>
                    <?php echo wc_help_tip( esc_html__( 'Select a message to show on non-refundable products',
                        'yith-advanced-refund-system-for-woocommerce' ) ); ?>
                </fieldset><?php

                woocommerce_wp_text_input( array(
                    'id'                => '_ywcars_message',
                    'label'             => esc_html__( 'Custom message', 'yith-advanced-refund-system-for-woocommerce' ),
                    'desc_tip'          => true,
                    'description'       => esc_html__( 'Set a custom message that will be shown on the product page.',
                        'yith-advanced-refund-system-for-woocommerce' ),
                    'type'              => 'text'
                ) );

                ?></div>
            </div><?php
        }


        public function update_product( $post_id ) {
            if ( $post_id ) {
                $product = wc_get_product( $post_id );
                $refundable = ! empty( $_POST['_ywcars_refundable'] ) ? $_POST['_ywcars_refundable'] : 'global';
                $ndays_type = ! empty( $_POST['_ywcars_ndays_refund_type'] ) ? $_POST['_ywcars_ndays_refund_type'] : 'global';
                $ndays = ! empty( $_POST['_ywcars_ndays_refund'] ) ? $_POST['_ywcars_ndays_refund'] : 0;
                $message_type = ! empty( $_POST['_ywcars_message_type'] ) ? $_POST['_ywcars_message_type'] : 'global';
                $message = ! empty( $_POST['_ywcars_message'] ) ? $_POST['_ywcars_message'] : '';


                yit_save_prop( $product, '_ywcars_refundable', $refundable );
	            yit_save_prop( $product, '_ywcars_ndays_refund_type', $ndays_type );
	            yit_save_prop( $product, '_ywcars_ndays_refund', $ndays );
	            yit_save_prop( $product, '_ywcars_message_type', $message_type );
	            yit_save_prop( $product, '_ywcars_message', $message );

            }
        }



        public function variations_content( $loop, $variation_data, $variation ) {
            $product_variation = wc_get_product( $variation->ID );
            $refundable = yit_get_prop( $product_variation, '_ywcars_refundable', true );
            $refundable_global_option = get_option( 'yith_wcars_allow_refunds', 'yes' );
            ?>

            <div class="ywcars_refunds_product_variations_data">
                <fieldset class="form-row form-row-full">
                    <p><?php esc_html_e( 'Set the refundable status', 'yith-advanced-refund-system-for-woocommerce' );
				        echo wc_help_tip( esc_html__( 'Choose whether the product allows refunds or not.',
					        'yith-advanced-refund-system-for-woocommerce' ) );
				        ?></p>
                    <p class="form-row form-row-full options">
                        <label>
                            <input name="<?php echo esc_attr( '_ywcars_refundable[' . $loop . ']' ); ?>"
                                   value="<?php echo esc_attr( 'global' ); ?>" type="radio"
                                   class="<?php echo esc_attr( 'ywcars_refundable_radio' ); ?>"
						        <?php echo checked( empty( $refundable )
							        ? 'global'
							        : $refundable, esc_attr( 'global' ), false ); ?>
                            />
                            <span><?php
						        esc_html_e( 'Global value', 'yith-advanced-refund-system-for-woocommerce' );
						        echo ' (';
						        esc_html_e( 'Currently:', 'yith-advanced-refund-system-for-woocommerce' );
						        if ( 'yes' == $refundable_global_option ) {
							        echo ' ' . esc_html__( 'Yes', 'yith-advanced-refund-system-for-woocommerce' );
						        } else {
							        echo ' ' . esc_html__( 'No', 'yith-advanced-refund-system-for-woocommerce' );
						        }
						        echo ')';
						        ?></span>
                        </label>
                        <label>
                            <input name="<?php echo esc_attr( '_ywcars_refundable[' . $loop . ']' ); ?>"
                                   value="<?php echo esc_attr( 'parent' ); ?>" type="radio"
                                   class="<?php echo esc_attr( 'ywcars_refundable_radio' ); ?>"
						        <?php echo checked( $refundable, esc_attr( 'parent' ), false ); ?>
                            />
					        <?php esc_html_e( 'Parent value', 'yith-advanced-refund-system-for-woocommerce' ); ?>
                        </label>
                        <label>
                            <input name="<?php echo esc_attr( '_ywcars_refundable[' . $loop . ']' ); ?>"
                                   value="<?php echo esc_attr( 'yes' ); ?>" type="radio"
                                   class="<?php echo esc_attr( 'ywcars_refundable_radio' ); ?>"
						        <?php echo checked( $refundable, esc_attr( 'yes' ), false ); ?>
                            />
					        <?php esc_html_e( 'Refundable', 'yith-advanced-refund-system-for-woocommerce' ); ?>
                        </label>
                        <label>
                            <input name="<?php echo esc_attr( '_ywcars_refundable[' . $loop . ']' ); ?>"
                                   value="<?php echo esc_attr( 'no' ); ?>" type="radio"
                                   class="<?php echo esc_attr( 'ywcars_refundable_radio' ); ?>"
						        <?php echo checked( $refundable, esc_attr( 'no' ), false ); ?>
                            />
					        <?php esc_html_e( 'Non-refundable', 'yith-advanced-refund-system-for-woocommerce' ); ?>
                        </label>
                    </p>
                </fieldset>
		        <?php
		        $ndays_global_option = get_option( 'yith_wcars_ndays_refund', 30 );
		        $ndays_refund_type = yit_get_prop( $product_variation, '_ywcars_ndays_refund_type', true );
		        $ndays = yit_get_prop( $product_variation, '_ywcars_ndays_refund', true );
		        ?>
                <fieldset class="form-row form-row-full">
                    <p><?php esc_html_e( 'Number of days for refund', 'yith-advanced-refund-system-for-woocommerce' );
				        echo wc_help_tip( esc_html__( 'Enter the maximum time (in days) to allow refunds. 0 means that refunds for this product are always
				        available',
					        'yith-advanced-refund-system-for-woocommerce' ) );
				        ?></p>
                    <p class="form-row form-row-full options">
                        <label>
                            <input name="<?php echo esc_attr( '_ywcars_ndays_refund_type[' . $loop . ']' ); ?>"
                                   value="<?php echo esc_attr( 'global' ); ?>" type="radio"
                                   class="<?php echo esc_attr( 'ywcars_ndays_radio' ); ?>"
						        <?php echo checked( empty( $ndays_refund_type )
							        ? 'global'
							        : $ndays_refund_type, esc_attr( 'global' ), false ); ?>
                            />
                            <span><?php
						        esc_html_e( 'Global value', 'yith-advanced-refund-system-for-woocommerce' );
						        echo ' (';
						        esc_html_e( 'Currently:', 'yith-advanced-refund-system-for-woocommerce' );
						        echo ' ' . $ndays_global_option;
						        echo ')';
						        ?></span>
                        </label>
                        <label>
                            <input name="<?php echo esc_attr( '_ywcars_ndays_refund_type[' . $loop . ']' ); ?>"
                                   value="<?php echo esc_attr( 'parent' ); ?>" type="radio"
                                   class="<?php echo esc_attr( 'ywcars_ndays_radio' ); ?>"
						        <?php echo checked( $ndays_refund_type, esc_attr( 'parent' ), false ); ?>
                            />
					        <?php esc_html_e( 'Parent value', 'yith-advanced-refund-system-for-woocommerce' ); ?>
                        </label>
                        <label>
                            <input name="<?php echo esc_attr( '_ywcars_ndays_refund_type[' . $loop . ']' ); ?>"
                                   value="<?php echo esc_attr( 'custom' ); ?>" type="radio"
                                   class="<?php echo esc_attr( 'ywcars_ndays_radio' ); ?>"
						        <?php echo checked( $ndays_refund_type, esc_attr( 'custom' ), false ); ?>
                            />
					        <?php esc_html_e( 'Set custom number of days', 'yith-advanced-refund-system-for-woocommerce' ); ?>
                        </label>
                    </p>
	                <?php
	                if ( 'custom' == $ndays_refund_type ) {
		                $display_custom_ndays_style = 'block';
	                } else {
		                $display_custom_ndays_style = 'none';
	                }
	                ?>
                    <p class="form-row form-row-first _ywcars_ndays_refund_variation_field" style="display: <?php echo $display_custom_ndays_style; ?>">
                        <label><?php esc_html_e( 'Custom number of days', 'yith-advanced-refund-system-for-woocommerce' ); ?> <?php echo wc_help_tip( esc_html__( '0 means that refunds for this product are always available', 'yith-advanced-refund-system-for-woocommerce' ) ); ?></label>
                        <input type="number" size="3" name="_ywcars_ndays_refund[<?php echo $loop; ?>]" value="<?php echo esc_attr( $ndays ); ?>" step="1" min="0" />
                    </p>
                </fieldset>
		        <?php
		        $message_type = yit_get_prop( $product_variation, '_ywcars_message_type', true );
		        $message = yit_get_prop( $product_variation, '_ywcars_message', true );
		        $message_general = get_option( 'yith_wcars_message' );
		        ?>
                <fieldset class="form-row form-row-full">
                    <p><?php esc_html_e( 'Non-refundable message', 'yith-advanced-refund-system-for-woocommerce' );
				        echo wc_help_tip( esc_html__( 'Select which message to show on non-refundable products',
					        'yith-advanced-refund-system-for-woocommerce' ) );
				        ?></p>
                    <p class="form-row form-row-full options">
                        <label>
                            <input name="<?php echo esc_attr( '_ywcars_message_type[' . $loop . ']' ); ?>"
                                   value="<?php echo esc_attr( 'global' ); ?>" type="radio"
                                   class="<?php echo esc_attr( 'ywcars_message_radio' ); ?>"
						        <?php echo checked( empty( $message_type )
							        ? 'global'
							        : $message_type, esc_attr( 'global' ), false ); ?>
                            />
                            <span title="<?php
                            echo $message_general ?
	                            sprintf( esc_html__( 'Current message: \'%s\'', 'yith-advanced-refund-system-for-woocommerce' ), $message_general ) : ''; ?>"><?php
						        esc_html_e( 'Global value', 'yith-advanced-refund-system-for-woocommerce' );
						        ?></span>
                        </label>
                        <label>
                            <input name="<?php echo esc_attr( '_ywcars_message_type[' . $loop . ']' ); ?>"
                                   value="<?php echo esc_attr( 'parent' ); ?>" type="radio"
                                   class="<?php echo esc_attr( 'ywcars_message_radio' ); ?>"
						        <?php echo checked( $message_type, esc_attr( 'parent' ), false ); ?>
                            />
					        <?php esc_html_e( 'Parent value', 'yith-advanced-refund-system-for-woocommerce' ); ?>
                        </label>
                        <label>
                            <input name="<?php echo esc_attr( '_ywcars_message_type[' . $loop . ']' ); ?>"
                                   value="<?php echo esc_attr( 'custom' ); ?>" type="radio"
                                   class="<?php echo esc_attr( 'ywcars_message_radio' ); ?>"
						        <?php echo checked( $message_type, esc_attr( 'custom' ), false ); ?>
                            />
					        <?php esc_html_e( 'Use a custom message', 'yith-advanced-refund-system-for-woocommerce' ); ?>
                        </label>
                    </p>
                    <?php
                    if ( 'custom' == $message_type ) {
                        $display_custom_message_style = 'block';
                    } else {
	                    $display_custom_message_style = 'none';
                    }
                    ?>
                    <p class="form-row form-row-first _ywcars_message_variation_field" style="display: <?php echo $display_custom_message_style; ?>">
                        <label><?php esc_html_e( 'Custom message', 'yith-advanced-refund-system-for-woocommerce' ); ?> <?php echo wc_help_tip( esc_html__( 'Set a custom message that will be shown on the product page.', 'yith-advanced-refund-system-for-woocommerce' ) ); ?></label>
                        <input type="text" name="_ywcars_message[<?php echo $loop; ?>]" value="<?php echo esc_attr( $message ); ?>" />
                    </p>
                </fieldset>
            </div>

            <?php
        }

        public function save_variations( $post_id, $_i ) {

            if ( $post_id ) {
                $product = wc_get_product( $post_id );
                $refundable = ! empty( $_POST['_ywcars_refundable'][$_i] ) ? $_POST['_ywcars_refundable'][$_i] : 'global';
                $ndays_type = ! empty( $_POST['_ywcars_ndays_refund_type'][$_i] ) ? $_POST['_ywcars_ndays_refund_type'][$_i] : 'global';
                $ndays = ! empty( $_POST['_ywcars_ndays_refund'][$_i] ) ? $_POST['_ywcars_ndays_refund'][$_i] : 0;
                $message_type = ! empty( $_POST['_ywcars_message_type'][$_i] ) ? $_POST['_ywcars_message_type'][$_i] : 'global';
                $message = ! empty( $_POST['_ywcars_message'][$_i] ) ? $_POST['_ywcars_message'][$_i] : '';

                yit_save_prop( $product, '_ywcars_refundable', $refundable );
	            yit_save_prop( $product, '_ywcars_ndays_refund_type', $ndays_type );
	            yit_save_prop( $product, '_ywcars_ndays_refund', $ndays );
	            yit_save_prop( $product, '_ywcars_message_type', $message_type );
	            yit_save_prop( $product, '_ywcars_message', $message );

            }
        }

        public function add_attachment_field_on_cpt() {
            ?>
            <div>
                <label for="ywcars_form_attachment"><?php
			        esc_html_e( 'Attach files (optional)', 'yith-advanced-refund-system-for-woocommerce' );
			        ?></label>
            </div>
            <input type="hidden" name="MAX_FILE_SIZE"
                   value="<?php echo get_option( 'yith_wcars_max_file_size', YITH_WCARS_ONE_KILOBYTE_IN_BYTES ) * YITH_WCARS_ONE_KILOBYTE_IN_BYTES; ?>" />
            <input type="file" id="ywcars_form_attachment" name="ywcars_form_attachment[]"
                   multiple <?php echo 'yes' == get_option( 'yith_wcars_enable_only_images', 'no' ) ? 'accept="image/*"' : ''; ?>>
	        <?php
        }

	    public function show_go_to_coupon_link( $request ) {
		    if ( 'ywcars-coupon' == $request->status && $request->coupon_id ) {
			    $coupon_url = get_edit_post_link( $request->coupon_id );
			    $coupon_link = '<a href="' . esc_attr( $coupon_url  ) . '">#' . $request->coupon_id . '</a>';
			    ?>
                <div><?php printf( esc_html__( 'Go to coupon: %s', 'yith-advanced-refund-system-for-woocommerce' ), $coupon_link ); ?></div>
			    <?php
		    }
	    }

	    public function add_offer_coupon_button( $request, $refund_amount ) {
            ?>
            <button class="ywcars_request_action_button button button-secondary" id="ywcars_offer_coupon_button">
                <span><?php printf( esc_html__( 'Offer a coupon for %s', 'yith-advanced-refund-system-for-woocommerce' ), $refund_amount ); ?></span>
            </button>
            <?php
        }

	    function new_refund_requests_count( $menu ) {
		    $type = YITH_WCARS_CUSTOM_POST_TYPE;
		    $status = 'ywcars-new';
		    $num_posts = wp_count_posts( $type, 'readable' );
		    $pending_count = 0;
		    if ( ! empty( $num_posts->$status ) )
			    $pending_count = $num_posts->$status;

		    // build string to match in $menu array
		    if ( $type == 'post' ) {
			    $menu_str = 'edit.php';
		    } else {
			    $menu_str = 'edit.php?post_type=' . $type;
		    }

		    // loop through $menu items, find match, add indicator
		    foreach( $menu as $menu_key => $menu_data ) {
			    if( $menu_str != $menu_data[2] )
				    continue;
			    $menu[$menu_key][0] .= " <span class='update-plugins count-$pending_count'><span class='plugin-count'>" . number_format_i18n($pending_count) . '</span></span>';
		    }
		    return $menu;
	    }

        public function add_premium_plugin_options( $settings ) {
            $premium_settings = array(

	            'refunds_start' => array(
		            'type' => 'sectionstart',
		            'id'   => 'yith_wcars_refunds_start'
	            ),

	            'refunds_title' => array(
		            'title' => esc_html__( 'Refunds', 'yith-advanced-refund-system-for-woocommerce' ),
		            'type'  => 'title',
		            'desc'  => '',
		            'id'    => 'yith_wcars_refunds_title'
	            ),

	            'refunds_automatic_refunds' => array(
		            'title'   => esc_html__( 'Accept all refund requests automatically', 'yith-advanced-refund-system-for-woocommerce' ),
		            'type'    => 'checkbox',
		            'desc'    => esc_html__( 'By activating this option, incoming refund requests will be accepted automatically.',
                        'yith-advanced-refund-system-for-woocommerce' ),
		            'id'      => 'yith_wcars_automatic_refunds',
		            'default' => 'no'
	            ),

	            'refunds_restock_items' => array(
		            'title'   => esc_html__( 'Restock refunded items on automatic refunds?', 'yith-advanced-refund-system-for-woocommerce' ),
		            'type'    => 'checkbox',
		            'desc'    => esc_html__( 'By activating this option, automatic refunds will restock the refunded items (bulk refunds included).',
                        'yith-advanced-refund-system-for-woocommerce' ),
		            'id'      => 'yith_wcars_restock_items',
		            'default' => 'no'
	            ),

	            'refunds_minimum_order_amount' => array(
		            'title'             => esc_html__( 'Minimum order amount', 'yith-advanced-refund-system-for-woocommerce' ),
		            'type'              => 'number',
		            'desc'              => esc_html__( 'Minimum amount of the order required to accept customers\'s requests.', 'yith-advanced-refund-system-for-woocommerce' ),
		            'id'                => 'yith_wcars_minimum_order_amount',
		            'custom_attributes' => array(
			            'step' => '1',
			            'min'  => '0'
		            ),
		            'default' => '0'
	            ),

	            'refunds_enable_message' => array(
		            'title'   => esc_html__( 'Enable message to show on non-refundable products', 'yith-advanced-refund-system-for-woocommerce' ),
		            'type'    => 'checkbox',
		            'desc'    => esc_html__( 'You can display a message which shows users that the product is refundable.',
			            'yith-advanced-refund-system-for-woocommerce' ),
		            'id'      => 'yith_wcars_enable_message',
		            'default' => 'yes'
	            ),

	            'refunds_message' => array(
		            'title'   => esc_html_x( 'Message', 'Admin option: custom message for non-refundable products', 'yith-advanced-refund-system-for-woocommerce' ),
		            'type'    => 'textarea',
		            'desc'    => esc_html__( 'Custom message for alert users when a product is not refundable.', 'yith-advanced-refund-system-for-woocommerce' ),
		            'id'      => 'yith_wcars_message',
		            'css'     => 'width:50%; height: 65px;',
		            'default' => esc_html__( 'Sorry, this product is not refundable.', 'yith-advanced-refund-system-for-woocommerce' )
	            ),

	            'refunds_end' => array(
		            'type' => 'sectionend',
		            'id'   => 'yith_wcars_refunds_end'
	            ),




	            'file_uploads_start' => array(
		            'type' => 'sectionstart',
		            'id'   => 'yith_wcars_settings_file_uploads_start'
	            ),

	            'file_uploads_title' => array(
		            'title' => esc_html__( 'File uploads', 'yith-advanced-refund-system-for-woocommerce' ),
		            'type'  => 'title',
		            'desc'  => '',
		            'id'    => 'yith_wcars_settings_file_uploads_title'
	            ),

	            'file_uploads_max_file_size' => array(
		            'title'             => esc_html__( 'Maximum file size allowed', 'yith-advanced-refund-system-for-woocommerce' ),
		            'type'              => 'number',
		            'desc'              => esc_html__( 'In Kilobytes. 0 means no limit.', 'yith-advanced-refund-system-for-woocommerce' ),
		            'id'                => 'yith_wcars_max_file_size',
		            'custom_attributes' => array(
			            'min'  => '0'
		            ),
		            'default'           => 2048
	            ),

	            'file_uploads_enable' => array(
		            'title'   => esc_html__( 'Only images', 'yith-advanced-refund-system-for-woocommerce' ),
		            'type'    => 'checkbox',
		            'desc'    => esc_html__( 'Allow uploading images only', 'yith-advanced-refund-system-for-woocommerce' ),
		            'id'      => 'yith_wcars_enable_only_images',
		            'default' => 'no'
	            ),

	            'file_uploads_end' => array(
		            'type' => 'sectionend',
		            'id'   => 'yith_wcars_settings_file_uploads_end'
	            ),


	            'coupon_settings_start' => array(
		            'type' => 'sectionstart',
		            'id'   => 'yith_wcars_coupon_code_start'
	            ),

	            'coupon_settings_title' => array(
		            'title' => esc_html__( 'Coupon settings', 'yith-advanced-refund-system-for-woocommerce' ),
		            'type'  => 'title',
		            'desc'  => '',
		            'id'    => 'yith_wcars_coupon_code_title'
	            ),

	            'coupon_settings_code' => array(
		            'title'   => esc_html__( 'Code', 'yith-advanced-refund-system-for-woocommerce' ),
		            'type'    => 'text',
		            'desc'    => sprintf( esc_html__( 'Pattern for automatically generated coupon codes when a coupon is offered in place of a refund. You
		            can use the following placeholders: %s, %s, %s or %s.', 'yith-advanced-refund-system-for-woocommerce' ), '{request_id}',
                        '{customer_email}', '{coupon_amount}', '{order_number}' ),
		            'id'      => 'yith_wcars_coupon_code',
		            'default' => ''
	            ),

	            'coupon_settings_expiry_date' => array(
		            'title'   => esc_html__( 'Expiry date', 'yith-advanced-refund-system-for-woocommerce' ),
		            'type'    => 'number',
		            'desc'    => esc_html__( 'Number of days through which the coupon will be valid. 0 means the coupon will never expire.',
                        'yith-advanced-refund-system-for-woocommerce' ),
		            'id'      => 'yith_wcars_expiry_date',
		            'default' => 0
	            ),

	            'coupon_settings_end' => array(
		            'type' => 'sectionend',
		            'id'   => 'yith_wcars_coupon_code_end'
	            ),
            );

            return array_merge( $settings, $premium_settings );
        }

    }
}