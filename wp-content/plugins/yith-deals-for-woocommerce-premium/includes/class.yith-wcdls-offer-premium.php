<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( !defined( 'YITH_WCDLS_VERSION' ) ) {
    exit( 'Direct access forbidden.' );
}

/**
 *
 *
 * @class      YITH_WCDLS_Offer_Premium
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
 *
 */

if ( !class_exists( 'YITH_WCDLS_Offer_Premium' ) ) {
    /**
     * Class YITH_WCDLS_Offer_Premium
     *
     * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
     */
    class YITH_WCDLS_Offer_Premium extends YITH_WCDLS_Offer
    {

        /**
         * Construct
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */
        public function __construct()
        {
            add_action('yith_wcdls_add_meta_boxes',array($this,'yith_wcdls_rule_option_content'),10);
            parent::__construct();
        }

        /*
        *
        * Rule option content
        *
        */
        public function yith_wcdls_rule_option_content($post) {
            if ( ! $post ) {
                return;
            }
            wc_get_template( apply_filters('yith_wcdls_template','wcdls-template-offer.php'),
                array( 'post' => $post ),
                '', YITH_WCDLS_TEMPLATE_PATH . 'admin/metabox/' );
        }



		/**
		 * Save metabox
		 *
		 * Save post type data
		 * @param int $post_id
		 * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
		 * @since 1.0
		 **/

		public function save_metabox( $post_id ) {
			parent::save_metabox( $post_id );

			if ( isset( $_POST['post_type'] ) && 'yith_wcdls_offer' === $_POST['post_type'] ) {

				if ( isset( $_POST['yith-wcdls-offer'] ) ) {
				    update_post_meta( $post_id, 'yith_wcdls_offer',
				        $_POST['yith-wcdls-offer'] );
				}

	            /*Offer conditions*/
	            $enable_disable = isset( $_POST['enable_disable'] )
		            ? $_POST['enable_disable'] : false;

	            if ( isset( $_POST['_yith_wcdls_for'] ) ) {
		            $my_date = $_POST['_yith_wcdls_for'];
		            update_post_meta( $post_id, '_yith_wcdls_for',
			            strtotime( $my_date ) );
	            }
	            if ( isset( $_POST['_yith_wcdls_to'] ) ) {

		            $my_date = $_POST['_yith_wcdls_to'];
		            update_post_meta( $post_id, '_yith_wcdls_to',
			            strtotime( $my_date ) );
	            }
	            $conditions     = isset( $_POST['yith-wcdls-rule'] )
		            ? $_POST['yith-wcdls-rule']['conditions'] : array();
	            $list_condition = array();

	            for ( $i = 0; $i < count( $conditions ); $i ++ ) {

		            $type_restriction
			            = isset( $conditions[ $i ]['type_restriction'] )
			            ? $conditions[ $i ]['type_restriction'] : '';

		            if ( '' != $type_restriction ) {
			            $list_condition[ $i ]['type_restriction']
				            = $type_restriction;

			            switch ( $type_restriction ) {
				            case 'price' :
					            $list_condition[ $i ]['restriction_by_price']
						            = $conditions[ $i ]['restriction_by_price'];
					            $list_condition[ $i ]['price']
						            = $conditions[ $i ]['price'];
					            break;

				            case 'geolocalization' :
					            $list_condition[ $i ]['restriction_by']
						            = $conditions[ $i ]['restriction_by'];
					            $list_condition[ $i ]['geolocalization']
						            = ( isset( $conditions[ $i ]['geolocalization'] ) )
						            ? $conditions[ $i ]['geolocalization'] : '';
					            break;
				            case 'product' :
					            $list_condition[ $i ]['restriction_by']
						            = $conditions[ $i ]['restriction_by'];
					            $list_condition[ $i ]['products_selected']
						            = ( isset( $conditions[ $i ]['products_selected'] ) )
						            ? $conditions[ $i ]['products_selected']
						            : '';

					            break;
				            case 'category' :
					            $list_condition[ $i ]['restriction_by']
						            = $conditions[ $i ]['restriction_by'];
					            $list_condition[ $i ]['categories_selected']
						            = ( isset( $conditions[ $i ]['categories_selected'] ) )
						            ? $conditions[ $i ]['categories_selected']
						            : '';
					            break;

				            case 'tag' :
					            $list_condition[ $i ]['restriction_by']
						            = $conditions[ $i ]['restriction_by'];
					            $list_condition[ $i ]['tags_selected']
						            = ( isset( $conditions[ $i ]['tags_selected'] ) )
						            ? $conditions[ $i ]['tags_selected'] : '';
					            break;

				            case 'role' :
					            $list_condition[ $i ]['restriction_by']
						            = $conditions[ $i ]['restriction_by'];
					            $list_condition[ $i ]['roles']
						            = ( isset( $conditions[ $i ]['roles'] ) )
						            ? $conditions[ $i ]['roles'] : '';

					            break;
				            case 'user' :
					            $list_condition[ $i ]['restriction_by']
						            = $conditions[ $i ]['restriction_by'];
					            $list_condition[ $i ]['users_selected']
						            = ( isset( $conditions[ $i ]['users_selected'] ) )
						            ? $conditions[ $i ]['users_selected'] : '';
					            break;

				            /*case 'date' :
								break;*/
			            }

		            }
	            }

	            /*Automatic Deal*/
	            $automatic_deal = isset( $_POST['automatic_deal'] )
		            ? $_POST['automatic_deal'] : false;

	            $conditions['conditions'] = $list_condition;

	            update_post_meta( $post_id, 'yith_wcdls_enable_disable',
		            $enable_disable );
	            update_post_meta( $post_id, 'yith_wcdls_automatic_deal',
		            $automatic_deal );

	            update_post_meta( $post_id, 'yith_wcdls_rule', $conditions );

            }

        }
    }
}