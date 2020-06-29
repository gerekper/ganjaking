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
 * @class      YITH_Advanced_Refund_System_My_Account_Premium
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Carlos Mora <carlos.eugenio@yourinspiration.it>
 *
 */

if ( ! class_exists( 'YITH_Advanced_Refund_System_My_Account_Premium' ) ) {
	/**
	 * Class YITH_Advanced_Refund_System_My_Account_Premium
	 *
	 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
	 */
	class YITH_Advanced_Refund_System_My_Account_Premium extends YITH_Advanced_Refund_System_My_Account {

		/**
		 * Construct
		 *
		 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
		 * @since 1.0.0
		 */
		public function __construct() {
		    parent::__construct();
		    add_action( 'ywcars_view_request_after_submit', array( $this, 'add_attachment_field_to_view_request' ) );
		}

		public function add_attachment_field_to_view_request() {
		    ?>
            <div>
                <label for="ywcars_form_attachment"><?php
					_e( 'Attach files (optional)', 'yith-advanced-refund-system-for-woocommerce' );
					?></label>
            </div>
            <input type="hidden" name="MAX_FILE_SIZE"
                   value="<?php echo get_option( 'yith_wcars_max_file_size', YITH_WCARS_ONE_KILOBYTE_IN_BYTES ) * YITH_WCARS_ONE_KILOBYTE_IN_BYTES; ?>" />
            <input type="file" id="ywcars_form_attachment" name="ywcars_form_attachment[]"
                   multiple <?php echo 'yes' == get_option( 'yith_wcars_enable_only_images', 'no' ) ? 'accept="image/*"' : ''; ?>>
            <?php
        }

	}
}