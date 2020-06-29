<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( defined( 'WP_CLI' ) && WP_CLI && ! class_exists( 'YITH_Custom_Thankyou_Page_CLI' ) ) {
    /**
     * CLI support class
     *
     * @class       YITH_Custom_Thankyou_Page_CLI
     * @package     YITH Custom ThankYou Page for Woocommerce
     * @since       1.1.6
     * @author      Armando Liccardo
     *
     */
    class YITH_Custom_Thankyou_Page_CLI {

        public $priorities = array();

        public function __construct() {
            /* priority available options */
            $this->priorities = apply_filters('yctpw_cli_priorities', array( 'general', 'category', 'product', 'payment' ) );
        }

        /**
         * Set General Thank you Page or URL
         *
         * two arguments needed, 'page' or 'url' followed by 'page id' or 'url'
         *
         * @param array
         * @since 1.1.6
         * @author Armando Liccardo
         * @return void
         * @example set_general_page page 80
         */
        public function set_general_page( $args ) {
            if ( count( $args ) < 2 ) {
                $this->print_message( array('no valid arguments provided'), 'error');
            } else {

                /* check if first argument is not empty */
                if ( isset( $args[0]) ) {
                    /* get the type of thank you page */
                    $type = ( $args[0] == 'page') ? 'ctpw_page' : 'ctpw_url';
                    update_option('yith_ctpw_general_page_or_url', $type);
                    $page_title = '';
                    /* save the option as page or url */
                     if( $type == 'ctpw_page' ) {
                         $avoid_pages = apply_filters('yctpw_avoid_pages', array(get_option( 'woocommerce_checkout_page_id' ), get_option( 'woocommerce_cart_page_id' )) );

                         /* check if the provided id is a page and if it is not a forbidden one too */
                         if( $this->yctpw_check_if_valid_id_page($args[1]) ) {
                             update_option('yith_ctpw_general_page', $args[1]);
                             $args[1] = get_the_title( $args[1]) . ' (id: ' . $args[1] . ')';
                             $this->print_message( array('Set General Thank you to be ' . $args[0] . ' with this value ' . $args[1]), 'success' );
                         } else {
                             $forbidden_pages = '';
                             foreach ( $avoid_pages as $p ) {
                                 $forbidden_pages = $forbidden_pages . '(id: '. $p .') ' . get_the_title( $p ) . ' | ';
                             }
                             $this->print_message( array('It seems the Id you provided is not a page or it is a forbidden page. Frobidden pages are: ' . $forbidden_pages), 'error' );
                         }

                     } else {
                         /* ctpw_url*/
                         update_option('yith_ctpw_general_page_url', $args[1]);
                         $this->print_message( array('Set General Thank you to be ' . $args[0] . ' with this value "' . $args[1] . '"'), 'success' );
                     }


                } else {
                    $this->print_message( array('No valid, or not enough, arguments provided'), 'error' );
                }

            }
        }

        /**
         * Change General Priority
         *
         * @param array
         * @since 1.1.6
         * @author Armando Liccardo
         * @return void
         */
        public function set_priority( $p ) {

            if ( !isset($p[0]) ) { $p[0] = 'general'; }



            if ( trim( $p[0] ) != '' && in_array( $p[0], $this->priorities ) ) {
                update_option('yith_ctpw_priority', $p[0]); /* update option */
                $this->print_message( array('Priority Changed to ' . $p[0]), 'success' );
            } else {
                $this->print_message( array('No valid priority provided. Available values are:general, category, product, payment'), 'error' );
            }


        }

        /**
         * Set a custom thank you page for Product by Product id
         *
         * you will need to provide a product id (or product variation id), the kind of custom page (url or page), and the url or page id
         *
         * @param array
         * @since 1.1.6
         * @author Armando Liccardo
         * @return void
         */
        public function set_product_thankyou_page( $args ) {
            if ( count( $args ) < 3 ) {
                $this->print_message( array('No, or too few arguments provided'), 'error' );
            } else {

                $product_id = $args[0];
                /* check if provided id is a product post type */
                if ( get_post_type( $product_id ) == 'product' || get_post_type( $product_id )== 'product_variation' ) {

                    $type = ( $args[1] == 'page' ) ? 'ctpw_page' : 'ctpw_url';
                    update_post_meta( $product_id, 'yith_ctpw_product_thankyou_page_url', $type );

                    if ( $type == 'ctpw_url' ) {
                        update_post_meta( $product_id, 'yith_ctpw_product_thankyou_url', $args[2] );
                    } else {
                        if (  get_post_type( $product_id ) == 'product' ) {
                            /* update value for product */
                            update_post_meta($product_id, 'yith_product_thankyou_page', $args[2]);
                        } else {
                            /* update value for product variation */
                            update_post_meta($product_id, 'yith_product_thankyou_page_variation', $args[2]);
                        }
                    }

                    $this->print_message( array('Custom Thank you Page set for product with id ' . $product_id ), 'success' );

                } else {
                    $this->print_message( array('It seems the product id you provided is wrong or it is not a Product.'), 'error' );
                }
            }
        }

        /**
         * set a thank you page for a specific product category
         *
         * @param array
         * @since 1.1.6
         * @author Armando Liccardo
         * @return void
         */
        public function set_category_thankyou( $args ) {
            if ( count( $args ) < 3 ) {
                $this->print_message( array('No, or too few arguments provided'), 'error');
            } else {

                /* check if the first arg is a product category id */
                $cat_id = $args[0];


                $cat = get_term_by('id', $cat_id,'product_cat');
                if ( ! $cat ) { /* no valid product category id */
                    $this->print_message( array('The ID provided as Product Category seems it is not valid'), 'error');
                } else {
                    /* valid product category id */
                    $type = ( $args[1] == 'page' ) ? 'ctpw_page' : 'ctpw_url';

                    update_term_meta( $cat_id, 'yith_ctpw_or_url_product_cat_thankyou_page', $type );

                    if ( $type == 'ctpw_url' ) {
                        update_term_meta( $cat_id, 'yith_ctpw_url_product_cat_thankyou_page', $args[2] );
                    } else {
                        if( $this->yctpw_check_if_valid_id_page( $args[2]) ) {
                            update_term_meta( $cat_id, 'yith_ctpw_product_cat_thankyou_page', $args[2] );
                        } else {
                           $this->print_message( array('The ID provided as Thank you Page is not for a valid id page'), 'error');
                        }

                    }

                    $this->print_message( array('Custom Thank you Page set for Category (' . $cat_id . ') ' . $cat->name), 'succes');


                }

            }


        }

        /**
         * Show all the applied settings
         *
         * @since 1.1.6
         * @author Armando Liccardo
         * @return void
         */
        public function list_all_custom_tp_applied() {

            /* general settings */
            $generalpagetype = get_option('yith_ctpw_general_page_or_url');
            $generalpagetype = ( $generalpagetype == 'ctpw_page') ? 'page' : 'url';
            if ( $generalpagetype == 'page' ) {
                $generalpagevalue = get_option( 'yith_ctpw_general_page');
                $generalpagevalue = '(' . $generalpagevalue . ') ' . get_the_title( $generalpagevalue );
            } else {
                $generalpagevalue = get_option( 'yith_ctpw_general_page_url');
            }

            $this->print_message( array('General Page is set to be a "' . $generalpagetype . '" - ' . $generalpagevalue ) );

            /* get product categories Custom Thank you pages set up */
            $get_product_cats = array(
                'taxonomy'     => 'product_cat',
                'orderby'      => 'name',
                'hide_empty'   => '0',
            );
            $cats = get_categories( $get_product_cats );
            $cats_applied = array();

            foreach ( $cats as $c ) {
              $opt = get_term_meta( $c->term_id, 'yith_ctpw_or_url_product_cat_thankyou_page',true);
              if ( !empty( $opt ) ) {
                  if ( $opt == 'ctpw_page') {
                      $page = get_term_meta( $c->term_id, 'yith_ctpw_product_cat_thankyou_page',true);
                      $page = '(' . $page . ') ' . get_the_title( $page );
                      $cats_applied[] = array(
                          'Category' => '(' . $c->term_id . ')' . $c->name,
                          'Type' => 'page',
                          'Value' => $page );
                  } else {
                      $url = get_term_meta( $c->term_id, 'yith_ctpw_url_product_cat_thankyou_page',true);
                      $cats_applied[] = array(
                          'Category' => '(' . $c->term_id . ')' . $c->name,
                          'Type' => 'url',
                          'Value' => $url );
                  }

              }
            }

            /* print products infos */
            $fields = array('Category', 'Type' , 'Value');
            $this->print_message( $cats_applied, 'table', $fields );

            /* get products Custom Thank you pages set up */
            $ps = wc_get_products(
                array(
                    'limit' => -1
                )
            );

            $ps_applied = array(); /* this will store all the products with a custom thankyou page set up */

            if ( count($ps) > 0 ) {
                foreach ( $ps as $p ) {
                    $pid = $p->get_id();
                    $pname = $p->get_name();
                    $ptype = $p->get_type();

                    $type = get_post_meta( $pid, 'yith_ctpw_product_thankyou_page_url', true);
                    if ( !empty($type) ) {
                        if ( $type == 'ctpw_page') {
                            $t = 'page';
                            $v = get_post_meta( $pid, 'yith_product_thankyou_page', true);
                            $v = '(' . $v . ') ' . get_the_title( $v );
                        } else {
                            $t = 'url';
                            $v = get_post_meta( $pid, 'yith_ctpw_product_thankyou_url', true);
                        }

                        $v = trim( $v );

                        if ( !empty($v) && $v != '(0)') {
                            $ps_applied[] = array(
                                'Product' => '(' . $pid . ')' . $pname,
                                'Type' => $t,
                                'Value' => $v);
                        }

                    }

                    /* check in product variations */
                    if( $ptype == 'variable' ) {
                        $vars = $p->get_available_variations();
                        foreach ($vars as $key => $value) {
                            $vv = '';
                            $v_type = get_post_meta( $value['variation_id'], 'yith_ctpw_product_thankyou_page_url', true);
                            if ( $v_type == 'ctpw_page' ) {
                                $vt = 'page';
                                $vv = get_post_meta( $value['variation_id'], 'yith_product_thankyou_page_variation', true);
                                $vv = '(' . $vv . ') ' . get_the_title( $vv );
                            } else {
                                $vt = 'url';
                                $vv = get_post_meta( $value['variation_id'], 'yith_ctpw_product_thankyou_url', true);
                            }

                            $vv = trim( $vv );

                            if ( !empty($vv) && $vv != '(0)') {
                                $ps_applied[] =  array(
                                    'Product' => 'Variation id: ' . $value['variation_id'] . ' of product ' . $pname,
                                    'Type' => $vt,
                                    'Value' => $vv );
                            }

                        }
                    }


                }

                /* print products infos */
                $fields = array('Product', 'Type' , 'Value');
                $this->print_message( $ps_applied, 'table', $fields );

            }

        }

        public function help( $args ) {

            $functions = array('help','set_general_page', 'set_priority', 'set_product_thankyou_page', 'set_category_thankyou', 'list_all_custom_tp_applied');

             $help_table = array(
                'help' => array(
                    'Description' => 'Show CLI commands help. Use help [function_name] for specific function help',
                    'Params' => 'none, or function name',
                    'Example' => 'wp yctpw help set_general_page'
                ),
                'set_general_page' => array(
                    'Description' => 'Set the general ThankYou page as Page or Url',
                    'Params' => 'provide the type as "page" or "url", then provide the page id or url',
                    'Example' => 'wp yctpw set_general_page page 80'
                ),
                'set_priority' => array(
                    'Description' => 'Set the main priority',
                    'Params' => 'priority option that can be one of this values: ' . implode(', ', $this->priorities),
                    'Example' => 'wp yctpw set_priority general'
                ),
                'list_all_custom_tp_applied' => array(
                    'Description' => 'Show all the applied custom thank you pages/url',
                    'Params' => 'no parameters needed',
                    'Example' => 'wp yctpw list_all_custom_tp_applied'
                ),
                 'set_product_thankyou_page' => array(
                     'Description' => 'Set a custom thank you page/url for a specific product',
                     'Params' => 'you will need to provide a product id (or product variation id), the kind of custom page (url or page), and the url or page id',
                     'Example' => 'wp yctpw set_product_thankyou_page 80 page 52'
                 ),
                 'set_category_thankyou' => array(
                     'Description' => 'Set a custom thank you page/url for a specific product category',
                     'Params' => 'you will need to provide a product category id, the kind of custom page (url or page), and the url or page id',
                     'Example' => 'wp yctpw set_category_thankyou 152 url https://www.mywebsite.com/my_custom_thankyou_page_url'
                 ),

             );

            /* no specific param provided so show the general help */
            if ( !isset($args) || count( $args ) == 0 ) {

                $infos = array();
                foreach ($help_table as $f => $f_table) {
                    $infos[] = array(
                        'Function' => $f,
                        'Description' => $f_table['Description']

                    );
                }

                $fields = array('Function', 'Description');
                $this->print_message( $infos, 'table', $fields );

            } else {
                /* specific function help if it exists or message error */
                if ( in_array( $args[0], $functions ) ) {

                    $message = array(
                        'Function: ' . $args[0],
                        'Description: ' . $help_table[$args[0]]['Description'],
                        'Parameters: ' . $help_table[$args[0]]['Params'],
                        'Example: ' . $help_table[$args[0]]['Example'],
                    );

                    $this->print_message( $message );


                } else {

                    $this->print_message( array('The function for which you are asking help does not exists!'),'error' );
                }

            }
        }


        /* Private Methods */

        /**
         * Print the message in console
         *
         * @param array message/s
         * @param string type of message: line, error, success
         * @param array if type is table we need the table fields header
         * @since 1.1.6
         * @author Armando Liccardo
         * @return void
         */
        private function print_message( $message = array(), $type = 'line', $fields = array() ) {
            if ( count( $message ) == '0' || ! is_array( $message ) ) {
                return;
            }

            $available_types = array('line', 'error', 'table');

            $type = strtolower( $type );

            if ( !in_array($type, $available_types) ) { /* set a default $type of message */
                $type = 'line';
            }

            WP_CLI::line( ' ' );

            if ( $type == 'line' ) {    /* CLI line command */
                foreach ( $message as $m ) {
                    WP_CLI::line( $m );
                }

            } elseif ( $type == 'error' ) { /* CLI error command */
                foreach ( $message as $m ) {
                    WP_CLI::line( "ERROR: " . $m );
                }
            } elseif ( $type == 'success' ) {
                foreach ( $message as $m ) {
                    WP_CLI::success( $m );
                }

            } elseif ( $type == 'table' && is_array($fields) && count( $fields ) > 0 ) {
                WP_CLI\Utils\format_items('table', $message, $fields);
            }

            WP_CLI::line( ' ' );
        }

        /**
         * Check if provided ID is a valid page
         *
         * you will need to provide a product category id, the kind of custom thank you page (url or page), and the url or page id
         *
         * @param int
         * @since 1.1.6
         * @author Armando Liccardo
         * @return bool
         */
        private function yctpw_check_if_valid_id_page( $id ) {
            $avoid_pages = apply_filters('yctpw_avoid_pages', array(get_option( 'woocommerce_checkout_page_id' ), get_option( 'woocommerce_cart_page_id' )) );

            /* check if the provided id is a page and if it is not a forbidden one too */
            if( get_post_type($id) == 'page' && !in_array( $id, $avoid_pages) ) {
                return true;
            } else {
                return false;
            }

        }


    }


    /* add yctpw command to wp cli */

    WP_CLI::add_command( 'yctpw', 'YITH_Custom_Thankyou_Page_CLI' );


}