<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/**
 * Displays the product donation table in YITh_WooCommerce_Donations plugin admin tab
 *
 * @class   YWCDS_Product_Donation_Table
 * @package Yithemes
 * @since   1.0.0
 * @author  Your Inspiration Themes
 *
 */
class YWCDS_Product_Donation_Table {

    /**
     * Outputs the exclusions table template with insert form in plugin options panel
     *
     * @since   1.0.0
     * @author  Alberto Ruggiero
     * @return  string
     */

    /**
     * Single instance of the class
     *
     * @var \YWCDS_Product_Donation_Table
     * @since 1.0.0
     */
    protected static $instance;

    public function __construct(){
        add_filter( 'set-screen-option', array( $this, 'set_options' ), 10, 3 );
        add_action( 'current_screen', array( $this, 'add_options' ) );

    }
    /**
     * Returns single instance of the class
     *
     * @return \YWCDS_Product_Donation_Table
     * @since 1.0.0
     */
    public static function get_instance() {

        if ( is_null( self::$instance ) ) {

            self::$instance = new self( $_REQUEST );

        }

        return self::$instance;
    }

    public function output() {

        global $wpdb;

        $table = new YITH_Custom_Table( array(
            'singular'  => __('product', 'yith-donations-for-woocommerce'),
            'plural'    => __('products', 'yith-donations-for-woocommerce')
        ) );

        $table->options = array(
            'select_table'      => $wpdb->prefix . 'posts a INNER JOIN ' . $wpdb->prefix . 'postmeta b ON a.ID = b.post_id',
            'select_columns'    => array(
                'a.ID',
                'a.post_title',
                'MAX(CASE WHEN b.meta_key = "_ywcds_donation_associate" THEN b.meta_value ELSE NULL END) AS donation_associate',
                'MAX(CASE WHEN b.meta_key = "_ywcds_donation_obligatory" THEN b.meta_value ELSE NULL END) AS obligatory'
            ),
            'select_where'      => 'a.post_type = "product" AND ( b.meta_key = "_ywcds_donation_associate" OR b.meta_key = "_ywcds_donation_obligatory" ) AND b.meta_value = "yes"',
            'select_group'      => 'a.ID',
            'select_order'      => 'a.post_title',
            'select_order_dir' => 'DESC',
            'per_page_option'  => 'items_per_page',
            'count_table'       => '( SELECT COUNT(*) FROM ' . $wpdb->prefix . 'posts a INNER JOIN ' . $wpdb->prefix . 'postmeta b ON a.ID = b.post_id  WHERE a.post_type = "product" AND (b.meta_key = "_ywcds_donation_associate" OR b.meta_key = "_ywcds_donation_obligatory") AND b.meta_value="yes" GROUP BY a.ID ) AS count_table',
            'count_where'       => '',
            'key_column'        => 'ID',
            'view_columns'      => array(
                'cb'            => '<input type="checkbox" />',
                'product'       => __('Product', 'yith-donations-for-woocommerce'),
                'donation_associate'   => __('View Donation', 'yith-donations-for-woocommerce'),
                'obligatory'         => __('Compulsory Donation', 'yith-donations-for-woocommerce')
            ),
            'hidden_columns'    => array(),
            'sortable_columns'  => array(
                'product'    => array( 'post_title', true )
            ),
            'custom_columns'    => array(
                'column_product'        => function ( $item, $me ) {

                    $edit_query_args    = array(
                        'page'      => $_GET['page'],
                        'tab'       => $_GET['tab'],
                        'action'    => 'edit',
                        'id'        => $item['ID']
                    );
                    $edit_url           = esc_url( add_query_arg( $edit_query_args, admin_url( 'admin.php' ) ) );

                    $delete_query_args  = array(
                        'page'      => $_GET['page'],
                        'tab'       => $_GET['tab'],
                        'action'    => 'delete',
                        'id'        => $item['ID']
                    );
                    $delete_url         = esc_url( add_query_arg( $delete_query_args, admin_url( 'admin.php' ) ) );

                    $product_query_args = array(
                        'post'      => $item['ID'],
                        'action'    => 'edit'
                    );
                    $product_url        = esc_url( add_query_arg( $product_query_args, admin_url( 'post.php' ) ) );

                    $actions            = array(
                        'edit'      => '<a href="' . $edit_url . '">' . __( 'Edit product list', 'yith-donations-for-woocommerce' ) . '</a>',
                        'delete'    => '<a href="' . $delete_url . '">' . __( 'Remove from product list', 'yith-donations-for-woocommerce' ) . '</a>',
                    );

                    return sprintf( '<strong><a class="tips" target="_blank" href="%s" data-tip="%s">#%d %s </a></strong> %s', $product_url, __( 'Edit product','yith-donations-for-woocommerce' ), $item['ID'], $item['post_title'], $me->row_actions( $actions ) );
                },
                'column_donation_associate'    => function ( $item, $me ) {

                     if ( $item['donation_associate'] == 'yes' ){
                         $class = 'show';
                         $tip   = __( 'Yes', 'yith-donations-for-woocommerce');
                     } else {
                         $class = 'hide';
                         $tip   = __( 'No', 'yith-donations-for-woocommerce');
                     }

                     return sprintf( '<mark class="%s tips" data-tip="%s">%s</mark>', $class, $tip, $tip );

                 },
                'column_obligatory'          => function ( $item, $me ) {

                     if ( $item['obligatory'] == 'yes' ){
                         $class = 'show';
                         $tip   = __( 'Yes', 'yith-donations-for-woocommerce');
                     } else {
                         $class = 'hide';
                         $tip   = __( 'No', 'yith-donations-for-woocommerce');
                     }

                     return sprintf( '<mark class="%s tips" data-tip="%s">%s</mark>', $class, $tip, $tip );

                 }
            ),
            'bulk_actions'      => array(
                'actions'   => array(
                    'delete'    => __( 'Remove from list','yith-donations-for-woocommerce' )
                ),
                'functions' => array(
                    'function_delete'    => function () {
                        global $wpdb;

                        $ids = isset( $_GET['id'] ) ? $_GET['id'] : array();
                        if ( is_array( $ids ) ) $ids = implode( ',', $ids );

                        if ( !empty( $ids ) ) {
                            $wpdb->query( "UPDATE {$wpdb->prefix}postmeta
                                           SET meta_value='no'
                                           WHERE ( meta_key = '_ywcds_donation_associate' OR meta_key = '_ywcds_donation_obligatory' ) AND post_id IN ( $ids )"
                            );
                        }
                    }
                )
            ),
        );

        $table->prepare_items();

        $message    = '';
        $notice     = '';

        $default = array(
            'ID'            => 0,
            'post_title'    => '',
            'donation_associate'   => '',
            'obligatory'         => ''
        );

        $list_query_args = array(
            'page'  => $_GET['page'],
            'tab'   => $_GET['tab']
        );

        $list_url = esc_url( add_query_arg( $list_query_args, admin_url( 'admin.php' ) ) );

        if ( 'delete' === $table->current_action() ) {
            $message = sprintf( _n( '%s product removed successfully', '%s products removed successfully', count( $_GET['id'] ), 'yith-donations-for-woocommerce' ), count( $_GET['id'] ) );
        }

        if ( ! empty( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], basename( __FILE__ ) ) ) {

            $item_valid = self::validate_fields( $_POST );

            if ( $item_valid !== true ){

                $notice = $item_valid;

            } else {

                $donation_associate  = isset( $_POST['donation_associate'] ) ? 'yes' : 'no';
                $obligatory = isset( $_POST['obligatory'] ) ? 'yes' : 'no';
                $products_id = isset( $_POST['products'] ) ? $_POST['products']  : array();

                if( !is_array( $products_id ) ){
                    $products_id = explode( ',', $products_id );
                }

                foreach($products_id as $product_id ){
                    $product = wc_get_product( $product_id );

                    $product_meta_data = array(
                            '_ywcds_donation_associate' => $donation_associate,
                            '_ywcds_donation_obligatory' => $obligatory
                    );

                    foreach( $product_meta_data as $meta_key => $meta_value ) {
	                    $product->update_meta_data( $meta_key, $meta_value );
                    }
                    $product->save();
                }
                if ( ! empty( $_POST['insert'] ) ) {

                    $message = sprintf( _n( '%s product added successfully', '%s products added successfully', count( $_POST['products'] ), 'yith-donations-for-woocommerce' ), count( $_POST['products'] ) );

                } elseif ( ! empty( $_POST['update'] ) ) {

                    $message = __( 'Product updated successfully' , 'yith-donations-for-woocommerce' );

                }
	            update_option( 'ywcds_sychronized_product', false );

            }

        }

        $item = $default;

        if ( isset( $_GET['id'] ) ) {

            $ids = is_array( $_GET['id'] ) ? implode(',', $_GET['id'] ) : $_GET['id'];
            $select_table   = $table->options['select_table'];
            $select_columns = implode( ',', $table->options['select_columns'] );
            $item           = $wpdb->get_row( $wpdb->prepare( "SELECT $select_columns FROM $select_table WHERE a.id = %d", $ids ), ARRAY_A );

        }

        ?>
        <div class="wrap">
            <div class="icon32 icon32-posts-post" id="icon-edit"><br/></div>
            <h2><?php _e('Product list', 'yith-donations-for-woocommerce');

                if ( empty( $_GET[ 'action' ] ) || ( 'insert' !== $_GET[ 'action' ] && 'edit' !== $_GET[ 'action' ] ) ) : ?>
                    <?php $query_args = array(
                        'page'      => $_GET['page'],
                        'tab'       => $_GET['tab'],
                        'action'    => 'insert'
                    );
                    $add_form_url = esc_url( add_query_arg( $query_args, admin_url( 'admin.php' ) ) );


                    ?>
                   <a class="add-new-h2" href="<?php echo $add_form_url; ?>"><?php _e( 'Add Products', 'yith-donations-for-woocommerce' ); ?></a>
                <?php endif; ?>
            </h2>
        <?php if ( ! empty( $notice ) ) : ?>
            <div id="notice" class="error below-h2"><p><?php echo $notice; ?></p></div>
        <?php endif;

        if ( ! empty( $message ) ) : ?>
            <div id="message" class="updated below-h2"><p><?php echo $message; ?></p></div>
        <?php endif;

        if ( ! empty( $_GET['action'] ) && ( 'insert' == $_GET['action'] ||  'edit' == $_GET['action'] ) ) : ?>

            <form id="form" method="POST">
                <input type="hidden" name="nonce" value="<?php echo wp_create_nonce( basename(__FILE__) ); ?>"/>
                <table class="form-table">
                    <tbody>
                        <tr valign="top" class="titledesc">
                            <th scope="row">
                                <label for="product"><?php _e( 'Products to associate', 'yith-donations-for-woocommerce' ); ?></label>
                            </th>
                            <td class="forminp yith-choosen">

                                <?php if ( 'insert' == $_GET['action'] ) :

	                                $select_field = array(
		                                'id'      => 'ywd_products',
		                                'name'    => 'products',
		                                'multiple' => true,
		                                'type'    => 'ajax-products',
                                        'placheolder' => __( 'Select for product', 'yith-donations-for-woocommerce' )

	                                );
	                                yith_plugin_fw_get_field( $select_field, true );
                                    ?>
                                <?php else :?>

                                    <input id="product" name="products" type="hidden" value="<?php echo esc_attr( $item['ID'] ); ?>"/>
                                    <?php printf( '<b>#%d %s</b>', esc_attr( $item['ID'] ), esc_attr( $item['post_title'] ) ); ?>

                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr valign="top" class="titledesc">

                                <input id="donation_associate" name="donation_associate" type="hidden" value="true"/>

                        </tr>
                        <tr valign="top" class="titledesc">
                            <th scope="row">
                                <label for="obligatory"><?php _e( 'Set donation as compulsory', 'yith-donations-for-woocommerce' ); ?></label>
                            </th>
                            <td class="forminp forminp-checkbox">
                                <input id="obligatory" name="obligatory" type="checkbox" <?php echo ( esc_attr( $item['obligatory'] ) == 'yes' ) ? 'checked="checked"' : ''; ?> />
                            </td>
                        </tr>
                    </tbody>
                </table>
                <?php if ( 'insert' == $_GET['action'] ) :?>

                    <input type="submit" value="<?php _e( 'Add product', 'yith-donations-for-woocommerce' ); ?>" id="insert" class="button-primary" name="insert">

                <?php else :?>

                    <input type="submit" value="<?php _e( 'Update product', 'yith-donations-for-woocommerce' ); ?>" id="update" class="button-primary" name="update">

                <?php endif; ?>
                <a class="button-secondary" href="<?php echo $list_url; ?>"><?php _e( 'Return to product list', 'yith-donations-for-woocommerce' ); ?></a>
            </form>
        <?php else : ?>
            <form id="custom-table" method="GET" action="<?php echo $list_url; ?>">
                <input type="hidden" name="page" value="<?php echo $_GET['page']; ?>"/>
                <input type="hidden" name="tab" value="<?php echo $_GET['tab']; ?>"/>
                <?php $table->display(); ?>
            </form>
        <?php endif; ?>
        </div>
        <?php

    }

    /**
     * Validate input fields
     *
     * @since   1.0.0
     * @author  Alberto Ruggiero
     * @param   $item array POST data array
     * @return  bool|string
     */
    public  function validate_fields( $item ) {
        $messages = array();

        if ( empty( $item['products'] ) ) $messages[] = __( 'Select at least one product', 'yith-donations-for-woocommerce' );
            if ( empty( $messages ) ) return true;
        return implode( '<br />', $messages );
    }

    /**
     * Add screen options for list table template
     *
     * @since   1.0.0
     * @return  void
     * @author  Alberto Ruggiero
     */
    public function add_options() {


        if ( 'yith-plugins_page_yith_wc_donations' == get_current_screen()->id && ( isset( $_GET['tab'] ) && $_GET['tab'] == 'product-donation' ) && ( !isset( $_GET['action'] ) || ( $_GET['action'] != 'edit' && $_GET['action'] != 'insert' ) ) ) {

            $option = 'per_page';

            $args = array(
                'label'   => __( 'Orders', 'yith-donations-for-woocommerce' ),
                'default' => 10,
                'option'  => 'items_per_page'
            );

            add_screen_option( $option, $args );

        }

    }

    /**
     * Set screen options for list table template
     *
     * @since   1.0.0
     *
     * @param   $status
     * @param   $option
     * @param   $value
     *
     * @return  mixed
     * @author  Alberto Ruggiero
     */
    public function set_options( $status, $option, $value ) {

        return ( 'items_per_page' == $option ) ? $value : $status;

    }

}
/**
 * Unique access to instance of YWCDS_Order_Donation_Table class
 *
 * @return \YWCDS_Order_Donation_Table
 */
function YWCDS_Product_Donation_Table() {

    return YWCDS_Product_Donation_Table::get_instance();

}

new YWCDS_Product_Donation_Table();
