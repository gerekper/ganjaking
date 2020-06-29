<?php
/**
 * Class Helper generate feed file
 *
 * @author  Yithemes
 * @package YITH Google Product Feed for WooCommerce
 * @version 1.0.0
 */

if ( !defined( 'YITH_WCGPF_VERSION' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'YITH_WCGPF_File_Helper' ) ) {
    /**
     * YITH_WCGPF_Helper
     *
     * @since 1.0.0
     */
    class YITH_WCGPF_File_Helper
    {

        /**
         * Single instance of the class
         *
         * @var \YITH_WCGPF_File_Helper
         * @since 1.0.0
         */
        protected static $_instance;

        /**
         * Returns single instance of the class
         *
         * @return \YITH_WCGPF_File_Helper
         * @since 1.0.0
         */
        public static function get_instance()
        {
            $self = __CLASS__ . ( class_exists( __CLASS__ . '_Premium' ) ? '_Premium' : '' );

            if ( is_null( $self::$_instance ) ) {
                $self::$_instance = new $self;
            }

            return $self::$_instance;
        }

        public $feed_type;
        public $merchant;
        public $feed_id;
        public $limit;
        public $offset;
        public $url;

        /**
         * @type array
         */
        public $allowed_merchant = array();

        public function __construct( )
        {

            add_action('wp_ajax_yith_wcgpf_generate_feed_file', array($this, 'yith_wcgpf_generate_feed_file'));
            add_action('wp_ajax_nopriv_yith_wcgpf_generate_feed_file', array($this, 'yith_wcgpf_generate_feed_file'));

        }

        function save_feed( $values,$mode,$feed='' ) {
            $upload_dir = wp_upload_dir();
            $base = $upload_dir['basedir'];
            $filename = ($title = get_the_title($values['post_id'])) ? $title : $values['post_id'];
            $filename = str_replace(' ','', $filename);
            $type = $values['feed_type'];
            # Save File
            $path = $base . "/yith-wcgpf-feeds/" . $values['merchant'] . "/" . $type;
            $file = $path . "/" . $filename . "." .$type;

            if( $feed ) {
                $save = new YITH_Google_Product_Feed_Save_Feed();
                if ($type == "txt") {
                    $save->save_feed_txt_file($path, $file, $feed,$mode);
                } else {
                    $save->save_feed_file($path, $file, $feed,$mode);
                }
            }

            $this->url = $upload_dir['baseurl'] . "/yith-wcgpf-feeds/" . $values['merchant'] . "/" . $type . "/" . $filename . "." .$type;
        }

        public function yith_wcgpf_generate_feed_file() {

            $this->offset  = intval( $_POST['offset']);
            $this->limit   = intval($_POST['limit']);
            $this->feed_id = intval($_POST['post_id']);
            $this->feed_type = sanitize_text_field($_POST['type']);

            if ( $this->limit == 0 ) {
                $this->limit = apply_filters('yith_wcgpf_limit_generate_feed',150);
            }

            $values = get_post_meta($this->feed_id, 'yith_wcgpf_save_feed', true);
            $generate_feed =  YITH_Google_Product_Feed()->generate_file;
            $products = YITH_Google_Product_Feed()->products;
            $ids_product = $products->get_products( $values,$this->limit,$this->offset );
            //$product_ids = array_slice($ids_product,$this->offset,$this->limit);
            $product_ids = $ids_product;
            
            if ( $this->offset == 0 ) {
                if ( 'xml' == $this->feed_type ) {

                    $head = $generate_feed->get_header_xml();

                } else {

                    $head = $generate_feed->get_header_txt($values);
                }
                $mode = 'wb';
                $this->save_feed( $values,$mode,$head );
            }
            if ( $ids_product && $product_ids ) {

                $feed_body = $generate_feed->create_feed($this->feed_id,$this->feed_type,$product_ids);
                $mode = 'a';
                $this->save_feed($values,$mode,$feed_body);

                $data=array(
                    "limit"=>$this->limit,
                    "offset" => $this->offset + $this->limit,
                    "products"=>count($ids_product),
                    "post_id" => $this->feed_id,
                    "type"  => $this->feed_type
                );
                wp_send_json_success($data);

            } else { //Close the file
                if ( 'xml' == $this->feed_type ) {

                    $footer = $generate_feed->get_footer_xml();
                    $mode = 'a';
                    $this->save_feed($values,$mode,$footer);
                }else {
                    $mode = 'a';
                    $this->save_feed($values,$mode);
                }

                $values['feed_file'] = $this->url;
                update_post_meta($this->feed_id,'yith_wcgpf_save_feed',$values);


                $data=array(
                    "limit"=>$this->limit,
                    "offset" => $this->offset + $this->limit,
                    "products"=>0,
                    "post_id" => 0,
                    "type"  => $this->feed_type,
                );
                wp_send_json_success($data);
            }
        }
    }
}