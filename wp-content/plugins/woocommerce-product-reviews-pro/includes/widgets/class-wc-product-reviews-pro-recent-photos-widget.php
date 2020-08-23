<?php
/**
 * WooCommerce Product Reviews Pro
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Product Reviews Pro to newer
 * versions in the future. If you wish to customize WooCommerce Social Login for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-product-reviews-pro/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2015-2020, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * Recent Photos widget
 *
 * @since 1.5.0
 */
class WC_Product_Reviews_Pro_Recent_Photos_Widget extends WC_Widget {


	/**
	 * Constructor
	 *
	 * @since 1.5.0
	 */
	public function __construct() {

		$this->widget_cssclass    = 'woocommerce widget_recent_photos widget_product_reviews_pro';
		$this->widget_description = __( 'Display a list of most recent photo contributions from Product Reviews Pro on your site.', 'woocommerce-product-reviews-pro' );
		$this->widget_id          = 'wc_product_reviews_pro_recent_photos';
		$this->widget_name        = __( 'WooCommerce Product Reviews Pro: Photos', 'woocommerce-product-reviews-pro' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => __( 'Recent Photos', 'woocommerce-product-reviews-pro' ),
				'label' => __( 'Title', 'woocommerce-product-reviews-pro' )
			),
			'number' => array(
				'type'  => 'number',
				'step'  => 1,
				'min'   => 1,
				'max'   => '',
				'std'   => 10,
				'label' => __( 'Number of photo contributions to show', 'woocommerce-product-reviews-pro' )
			)
		);

		parent::__construct();
	}


	/**
	 * Render the Product Reviews Pro Recent Photos widget
	 *
	 * @since 1.5.0
	 * @see \WP_Widget::widget()
	 * @param array $args widget arguments
	 * @param array $instance saved values from database
	 */
	public function widget( $args, $instance ) {
		global $comments, $comment;

		// try to get a cached version of results first
		if ( $this->get_cached_widget( $args ) ) {
			return;
		}

		$number = ! empty( $instance['number'] ) ? absint( $instance['number'] ) : $this->settings['number']['std'];

		/**
		 * Filters the arguments for widget comment args
		 *
		 * @since 1.5.0
		 * @param array $args the get_comments() arguments
		 * @param array $instance the widget instance of saved values
		 * @return array the updated comment arguments
		 */
		$comment_args = apply_filters( 'wc_product_reviews_pro_widget_query_args', array(
			'number'      => $number,
			'status'      => 'approve',
			'post_status' => 'publish',
			'post_type'   => 'product',
			'type'        => 'photo',
		), $instance );

		$comments = get_comments( $comment_args );

		/**
		 * Filters the length of contribution title or content excerpts
		 *
		 * @since 1.5.0
		 * @param int $contribution_excerpt_length contribution excerpt length
		 * @param string $type the contribution type
		 * @return int the updated contribution excerpt length
		 */
		$contribution_excerpt_length = (int) apply_filters( 'wc_product_reviews_pro_widget_contribution_length', 10, $comment_args['type'] );

		ob_start();

		if ( $comments ) {

			// get the widget configuration
			$title = $instance['title'];

			echo $args['before_widget'];

			if ( $title ) {
				echo $args['before_title'] . $title . $args['after_title'];
			}

			echo '<ul class="product_list_widget">';

			foreach ( (array) $comments as $comment ) {

				$_product = wc_get_product( $comment->comment_post_ID );

				$contribution = wc_product_reviews_pro_get_contribution( $comment );

				/**
				 * Filters the contribution title, which is used automatically as the widget content
				 *
				 * @since 1.6.4
				 * @param string $contribution_title the title for the contribution, shown for each contribution in the widget
				 * @param \WC_Contribution $contribution the contribution (comment) object
				 */
				$contribution_title = apply_filters( 'wc_product_reviews_pro_widget_contribution_title', $contribution->get_title(), $contribution );

				echo '<li><a href="' . esc_url( get_comment_link( $comment->comment_ID ) ) . '">';

				echo '<img src="' . esc_url( $contribution->get_attachment_url() ) . '" class="attachment-shop_thumbnail size-shop_thumbnail wp-post-image" alt="photo contribution for ' . esc_attr( $_product->get_title() ) . '" />';

				echo wp_kses_post( $_product->get_title() ) . '</a>';

				if ( $contribution_title )  {
					echo '<span class="contribution-content">' . wp_kses_post( wc_product_reviews_pro_trim_contribution( $contribution_title, $contribution_excerpt_length ) ) . ' </span>';
				} else {
					echo '<span class="contribution-content">' . wp_kses_post( wc_product_reviews_pro_trim_contribution( $contribution->get_content(), $contribution_excerpt_length ) ) . ' </span>';
				}

				printf( '<span class="reviewer contribution-author">' . _x( 'by %1$s', 'by comment author', 'woocommerce-product-reviews-pro' ) . '</span>', get_comment_author() );

				echo '</li>';
			}

			echo '</ul>';

			echo $args['after_widget'];
		}

		$content = ob_get_clean();

		echo $content;

		// save a cached version of the review results
		$this->cache_widget( $args, $content );
	}


} // end \WC_Product_Reviews_Pro_Recent_Photos_Widget class
