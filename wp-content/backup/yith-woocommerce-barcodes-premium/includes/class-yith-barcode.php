<?php
if ( ! defined ( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

use Endroid\QrCode\QrCode;

if ( ! class_exists ( 'YITH_Barcode' ) ) {

    /**
     *
     * @class   YITH_Barcode
     * @package Yithemes
     * @since   1.0.0
     * @author  Your Inspiration Themes
     */
    class YITH_Barcode {

        /** Define constants for post meta key */
        const YITH_YWBC_META_KEY_BARCODE_PROTOCOL = '_ywbc_barcode_protocol';
        const YITH_YWBC_META_KEY_BARCODE_VALUE = '_ywbc_barcode_value';
        const YITH_YWBC_META_KEY_BARCODE_DISPLAY_VALUE = '_ywbc_barcode_display_value';
        const YITH_YWBC_META_KEY_BARCODE_IMAGE = '_ywbc_barcode_image';
        const YITH_YWBC_META_KEY_BARCODE_FILENAME = '_ywbc_barcode_filename';

        /**
         * @var int the object(Order or Product) id related to the current barcode
         */
        public $object_id;

        /**
         * @var string barcode protocol
         */
        private $protocol;

        /**
         * @var string barcode value
         */
        private $value;

        /**
         * @var string the value being displayed
         */
        private $display_value;


        /**
         * Constructor
         *
         * Initialize plugin and registers actions and filters to be used
         *
         * @param int $object_id
         *
         * @since  1.0
         * @author Lorenzo Giuffrida
         */
        public function __construct( $object_id = 0 ) {

            if ( $object_id ) {
                $this->object_id = $object_id;

                $this->load_by_object_type ();

            }
        }

        /**
         * Load barcode attributes based on the object type, added after WC 3.0
         */
        private function load_by_object_type() {
            if ( ( $object = wc_get_product ( $this->object_id ) ) ||
                ( $object = wc_get_order ( $this->object_id ) )
            ) {
                $this->load_wc_object ( $object );
            } else {
                $this->load_cpt_object ();
            }
        }

        /**
         * Load barcode attributes for custom post type objects
         *
         */
        private function load_cpt_object() {
            $this->protocol       = get_post_meta ( $this->object_id, self::YITH_YWBC_META_KEY_BARCODE_PROTOCOL, true );
            $this->value          = get_post_meta ( $this->object_id, self::YITH_YWBC_META_KEY_BARCODE_VALUE, true );
            $this->display_value  = get_post_meta ( $this->object_id, self::YITH_YWBC_META_KEY_BARCODE_DISPLAY_VALUE, true );
            $this->image          = get_post_meta ( $this->object_id, self::YITH_YWBC_META_KEY_BARCODE_IMAGE, true );
            $this->image_filename = get_post_meta ( $this->object_id, self::YITH_YWBC_META_KEY_BARCODE_FILENAME, true );
        }

        /**
         * Load barcode attributes for WC3.0+ objects
         *
         * @param WC_Order|WC_Product $object
         */
        private function load_wc_object( $object ) {
            $this->protocol       = get_post_meta ( $object->get_id(), self::YITH_YWBC_META_KEY_BARCODE_PROTOCOL, true );
            $this->value          = get_post_meta ( $object->get_id(), self::YITH_YWBC_META_KEY_BARCODE_VALUE, true );
            $this->display_value  = get_post_meta ( $object->get_id(), self::YITH_YWBC_META_KEY_BARCODE_DISPLAY_VALUE, true );
            $this->image          = get_post_meta ( $object->get_id(), self::YITH_YWBC_META_KEY_BARCODE_IMAGE, true );
            $this->image_filename = get_post_meta ( $object->get_id(), self::YITH_YWBC_META_KEY_BARCODE_FILENAME, true );
        }

        /**
         * Retrieve the barcode by id
         *
         * @param int $id
         *
         * @return YITH_Barcode
         * @author Lorenzo Giuffrida
         * @since  1.0.0
         */
        public static function get( $id ) {
            return new YITH_Barcode( $id );
        }

        /**
         * Retrieve current formatted value
         * @return mixed|string
         */
        public function get_display_value() {
            return $this->display_value;
        }

        /**
         * Retrieve current formatted value
         * @return mixed|string
         */
        public function get_protocol() {
            return $this->protocol;
        }

        /**
         * save the barcode instance
         *
         */
        public function save() {

            if ( $this->object_id ) {

                if ( ( $object = wc_get_product ( $this->object_id ) ) ||
                    ( $object = wc_get_order ( $this->object_id ) )
                ) {
                    $this->save_wc_object ( $object );
                } else {
                    $this->save_cpt_object ();
                }
            }
        }

        /**
         * Save barcode attributes for custom post types objects
         */
        private function save_cpt_object() {
            if ( $this->object_id ) {

                update_post_meta ( $this->object_id, self::YITH_YWBC_META_KEY_BARCODE_PROTOCOL, $this->protocol );
                update_post_meta ( $this->object_id, self::YITH_YWBC_META_KEY_BARCODE_VALUE, $this->value );
                update_post_meta ( $this->object_id, self::YITH_YWBC_META_KEY_BARCODE_DISPLAY_VALUE, $this->display_value );
                update_post_meta ( $this->object_id, 'ywbc_barcode_display_value_custom_field', $this->display_value );
                update_post_meta ( $this->object_id, self::YITH_YWBC_META_KEY_BARCODE_IMAGE, $this->image );
                update_post_meta ( $this->object_id, self::YITH_YWBC_META_KEY_BARCODE_FILENAME, $this->image_filename );
            }
        }

        /**
         * Save barcode attributes for custom post types objects
         *
         * @param WC_Order|WC_Product $object
         */
        private function save_wc_object( $object ) {

            if ( $object->get_id() ) {
                update_post_meta($object->get_id(), self::YITH_YWBC_META_KEY_BARCODE_PROTOCOL, $this->protocol);
                update_post_meta($object->get_id(), self::YITH_YWBC_META_KEY_BARCODE_VALUE, $this->value);
                update_post_meta($object->get_id(), self::YITH_YWBC_META_KEY_BARCODE_DISPLAY_VALUE, $this->display_value);
                update_post_meta($object->get_id(), 'ywbc_barcode_display_value_custom_field', $this->display_value);
                update_post_meta($object->get_id(), self::YITH_YWBC_META_KEY_BARCODE_IMAGE, $this->image);
                update_post_meta($object->get_id(), self::YITH_YWBC_META_KEY_BARCODE_FILENAME, $this->image_filename);
            }
        }

        /**
         * Generate a barcode image
         *
         * @param string $protocol
         * @param string $value
         * @param string $path
         */
        public function generate( $protocol, $value, $path = '' ) {

            $this->protocol       = $protocol;
            $this->value          = $value;
            $this->image_filename = $path;

            if ( 'qrcode' == strtolower ( $this->protocol ) ) {
                $this->create_qrcode_image ();
            } else {
                if ( ( $is_ean8 = strtolower ( $this->protocol ) == 'ean8' ) ||
                    ( strtolower ( $this->protocol ) == 'ean13' )
                ) {
                    $len         = $is_ean8 ? 7 : 12;
                    $this->value = substr ( $this->value, 0, $len );
                }

                $this->create_barcode_image ();
            }
        }

        /**
         * Retrieve if the barcode exists for the current object
         * @return bool
         */
        public function exists() {

            return $this->image_filename || $this->image;
        }

        public static function get_protocols() {
            $defaults = array(
                'EAN13'   => 'EAN-13',
                'EAN8'    => 'EAN-8',
                'UPC'     => 'UPC-A',
                'STD25'   => 'STD 25',
                'INT25'   => 'INT 25',
                'CODE39'  => 'CODE 39',
                'code93'  => 'CODE 93',
                'code128' => 'CODE 128',
                'Codabar' => 'Codabar',
            );

            return $defaults;
        }

        /**
         * Check if the value is in a valid format and fix it if possible
         *
         * @param string $protocol
         * @param string $value
         *
         * @return null|string
         * @author Lorenzo Giuffrida
         * @since  1.0.0
         */
        private function formatted_value( $protocol, $value ) {

            $formatted_value = $value;

            switch ( strtolower ( $protocol ) ) {
                case 'ean8' :
                    $formatted_value = sprintf ( '%07s', $value );
                    break;
                case 'ean13' :
                    $formatted_value = apply_filters('yith_ywbc_ean13_formatted_value',sprintf ( '%012s', $value ));
                    break;
                case 'upc' :
                    $formatted_value = apply_filters('yith_ywbc_upc_formatted_value', sprintf ( '%011s', $value ), $value );
                    break;
            }

            return $formatted_value;
        }


	   public function check_digit_generator( $code, $len ) {
		    $code = substr( $code, 0, $len );
		    if ( ! preg_match( '`[0-9]{' . $len . '}`', $code ) ) {
			    return ( '' );
		    }
		    $sum = 0;
		    $odd = true;
		    for ( $i = $len - 1; $i > - 1; $i -- ) {
			    $sum += ( $odd ? 3 : 1 ) * intval( $code[ $i ] );
			    $odd = ! $odd;
		    }

		    return ( $code . ( (string) ( ( 10 - $sum % 10 ) % 10 ) ) );
	    }


	    /**
         * Create a barcode image
         *
         * @return string
         * @author Lorenzo Giuffrida
         * @since  1.0.0
         *
         */
        private function create_barcode_image() {

	        require ( YITH_YWBC_INCLUDES_DIR . 'vendor/autoload.php' );

	        $generator = new Picqer\Barcode\BarcodeGeneratorJPG();

            $formatted_value = $this->formatted_value ( $this->protocol, $this->value );

	        $this->display_value = $formatted_value;

	        switch (  $this->protocol ) {
		        case 'EAN13' :
			        $protocol_type = $generator::TYPE_EAN_13;
			        $this->display_value = $this->check_digit_generator( $formatted_value, '12' );
			        break;
		        case 'EAN8' :
			        $protocol_type = $generator::TYPE_EAN_8;
			        $this->display_value = $this->check_digit_generator( $formatted_value, '7'  );
			        break;
		        case 'UPC' :
			        $protocol_type = $generator::TYPE_UPC_A;
			        $this->display_value = apply_filters('yith_ywbc_upc_display_value', $this->check_digit_generator( $formatted_value, '11' ) , $formatted_value ) ;
			        break;
		        case 'STD25' :
			        $protocol_type = $generator::TYPE_STANDARD_2_5;
			        break;
		        case 'INT25' :
			        $protocol_type = $generator::TYPE_INTERLEAVED_2_5;
			        break;
		        case 'CODE39' :
			        $protocol_type = $generator::TYPE_CODE_39;
			        break;
		        case 'code93' :
			        $protocol_type = $generator::TYPE_CODE_93;
			        break;
		        case 'code128' :
			        $protocol_type = $generator::TYPE_CODE_128;
			        break;
		        case 'Codabar' :
			        $protocol_type = $generator::TYPE_CODABAR;
			        break;

	        }

	        $this->image = base64_encode( $generator->getBarcode($formatted_value, $protocol_type ) );

	        file_put_contents(  $this->image_filename, $generator->getBarcode($formatted_value, $protocol_type) );

        }

	    /**
	     * Create a QR code image
	     *
	     * @return string
	     * @author Lorenzo Giuffrida
	     * @since  1.0.0
	     *
	     */
	    private function create_qrcode_image() {

		    require ( YITH_YWBC_INCLUDES_DIR . 'vendor/autoload.php' );

		    $formatted_value = $this->formatted_value ( $this->protocol, $this->value );
		    $formatted_value = apply_filters('yith_ywbc_formatted_value',$formatted_value, $this->protocol, $this->value);

		    $image_filename = apply_filters('yith_ywbc_image_filename',$this->image_filename);

		    $qrCode = new QrCode( $formatted_value );

		    $qrCode->setSize(100);
		    $qrCode->writeFile( $image_filename );

		    $this->display_value = $formatted_value;
	    }


    }
}
