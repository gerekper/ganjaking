<?php
/**
 * Class Generate Product Feed
 *
 * @author  Yithemes
 * @package YITH Google Product Feed for WooCommerce
 * @version 1.0.0
 */

if ( !defined( 'YITH_WCGPF_VERSION' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'YITH_WCGPF_Generate_Feed' ) ) {
    /**
     * YITH_WCGPF_Generate_Feed
     *
     * @since 1.0.0
     */
    class YITH_WCGPF_Generate_Feed
    {
        /**
         * id of feed
         *
         * @var int
         * @since 1.0.0
         */
        public $id;

        /**
         * post of feed
         *
         * @var WP_Post|bool
         * @since 1.0.0
         */
        public $post;

        public function __construct( $feed_id, $feed_type,$merchant)
        {
                $this->create_feed( $feed_id, $feed_type,$merchant);
        }

        /**
         * Create the feed
         * @author Carlos Rodriguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         * @return array
         */

        function create_feed( $feed_id, $feed_type,$merchant ) {
            
        }

        /**
         * Create the xml feed
         * @author Carlos Rodriguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         * @return array
         */
        function create_feed_xml( $values,$products ){


        }

        /**
         * Get header xml
         * @author Carlos Rodriguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         * @return $header
         */
        function get_header_xml(){
             $filename = 'product-feed-' . date( 'Ym-d_His', time() ) . '.xml';
             header ( "X-Robots-Tag: noindex, nofollow", true );
             header ( "Content-Type: application/xml" );
             header ( "Content-Disposition: attachment; filename=\"" . $filename . "\";" );
             $header = '<?xml version="1.0" encoding="' . get_option( 'blog_charset' ) . '"?' . '>
                        <rss version="2.0" xmlns:g="http://base.google.com/ns/1.0" xmlns:c="http://base.google.com/cns/1.0">
                        <channel>
                            <title><![CDATA['.get_option('blogname').']]></title>
                            <link><![CDATA['.site_url().']]></link>
                            <description><![CDATA['.get_option('blogdescription','Feed').']]></description>';
            return $header;
        }

        /**
         * Get content xml
         * @author Carlos Rodriguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         * @return $footer
         */
        function get_content_xml( $product,$values ) {}

        /**
         * Get footer xml
         * @author Carlos Rodriguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         * @return $footer
         */
        function get_footer_xml()
        {
            $footer = "</channel>
                       </rss>";
            return $footer;
        }
        

        /**
         * Add CDATA section
         * @author Carlos Rodriguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         * @return $footer
         */
        function CDATA ( $value ) {
            $cdata = '<![CDATA['.$value.']]>';

            return $cdata;
        }
        
    }
}