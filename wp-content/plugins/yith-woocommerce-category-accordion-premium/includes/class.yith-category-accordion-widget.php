<?php // phpcs:ignore WordPress.NamingConventions
/**
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH\CategoryAccordion
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YITH_Category_Accordion_Widget' ) ) {
	/**
	 * YITH_Category_Accordion_Widget
	 */
	class YITH_Category_Accordion_Widget extends WP_Widget {
		/**
		 * __construct function
		 *
		 * @author YITH <plugins@yithemes.com>
		 * @since 1.0.0
		 */
		public function __construct() {
			parent::__construct(
				'yith_wc_category_accordion',
				__( 'YITH WooCommerce Category Accordion', 'yith-woocommerce-category-accordion' ),
				array( 'description' => __( 'Show your categories in an accordion!', 'yith-woocommerce-category-accordion' ) )
			);
            wp_enqueue_style( 'yith-plugin-fw-fields' );
            wp_enqueue_script( 'yith-plugin-fw-fields' );
        }


		/**
		 * Widget
		 *
		 * @param mixed $args args.
		 * @param mixed $instance instance.
		 *
		 * @return void
		 */
		public function widget( $args, $instance ) {

            $how_show = empty( $instance['how_show'] ) ? 'wc' : $instance['how_show'];

			$string_build_shortcode = 'how_show="' . $how_show . '" ';

			switch ( $how_show ) {
				case 'wc':

					$show_sub_cat   = $instance['show_wc_subcat'];
					$show_wc_img    = isset( $instance['show_wc_img'] ) ? $instance['show_wc_img'] : 'no';
					$exclude_specific_cat    = isset( $instance['exclude_specific_cat'] ) ? $instance['exclude_specific_cat'] : 'no';
					$exclude_cat    = $instance['exclude_wc_cat'];
					$show_count     = $instance['show_count'];

					if ( is_array( $exclude_cat ) ) {
						$exclude_cat = implode( ',', $exclude_cat );
					}

					$string_build_shortcode .= 'show_sub_cat="' . $show_sub_cat . '" exclude_cat="' . $exclude_cat . '" show_count="' . $show_count . '" show_wc_img="' . $show_wc_img . '" exclude_specific_cat="' . $exclude_specific_cat . '" ';
					break;
				case 'wp':
					$show_sub_cat   = $instance['show_wp_subcat'];
					$exclude_cat    = $instance['exclude_wp_cat'];
                    $exclude_wp_cat_select    = $instance['exclude_wp_cat_select'];
                    $show_last_post = $instance['show_post'];
					$post_limit     = $instance['post_limit'];
					$show_count     = $instance['show_count'];
					if ( is_array( $exclude_cat ) ) {
						$exclude_cat = implode( ',', $exclude_cat );
					}
					$string_build_shortcode .= 'exclude_wp_cat_select="' . $exclude_wp_cat_select . '" show_sub_cat="' . $show_sub_cat . '" exclude_cat="' . $exclude_cat . '" show_last_post="' . $show_last_post . '" post_limit="' . $post_limit . '" show_count="' . $show_count . '" ';
					break;
				case 'menu':
					$menu_ids = implode( ',', $instance['include_menu'] );

					$string_build_shortcode .= 'menu_ids="' . $menu_ids . '" ';

					break;

				case 'tag':
					$menu_wc_name           = $instance['name_wc_tag'];
					$menu_wp_name           = $instance['name_wp_tag'];
					$string_build_shortcode .= 'tag_wc="' . $instance['tag_wc'] . '" tag_wp="' . $instance['tag_wp'] . '"  name_wc_tag="' . $menu_wc_name . '" name_wp_tag="' . $menu_wp_name . '" ';
					break;

			}

			/*General params*/
			$title        = $instance['title'];
			$title        = apply_filters( 'widget_title', $title, $instance, $this->id_base );
			$hide_pages_posts    = isset( $instance['hide_pages_posts'] ) ? $instance['hide_pages_posts'] : 'no';
			$exclude_page = $instance['exclude_page'];
			$exclude_post = $instance['exclude_post'];
			$highlight    = $instance['highlight_curr_cat'];
			$style        = $instance['acc_style'];
			$orderby      = $instance['orderby'];
			$order        = $instance['order'];

			$exclude_page           = is_array( $exclude_page ) ? implode( ',', $exclude_page ) : $exclude_page;
			$exclude_post           = is_array( $exclude_post ) ? implode( ',', $exclude_post ) : $exclude_post;
			$string_build_shortcode .= 'hide_pages_posts="' . $hide_pages_posts . '" exclude_page="' . $exclude_page . '" exclude_post="' . $exclude_post . '" highlight="' . $highlight . '" orderby="' . $orderby . '" order="' . $order . '" acc_style="' . $style . '" ';
			ob_start();
			echo $args['before_widget']; //phpcs:ignore WordPress.Security.EscapeOutput
			echo do_shortcode( '[yith_wcca_category_accordion title="' . $title . '" ' . $string_build_shortcode . ']' );
			echo $args['after_widget']; //phpcs:ignore WordPress.Security.EscapeOutput
			$content = ob_get_clean();
			echo $content; //phpcs:ignore WordPress.Security.EscapeOutput

		}

		/**
		 * Form
		 *
		 * @param mixed $instance instance.
		 *
		 * @return void
		 */
		public function form( $instance ) {

			$is_default = empty( $instance );

			$default = array(
				'title'              => isset( $instance['title'] ) ? $instance['title'] : '',
				'show_wc_subcat'     => isset( $instance['show_wc_subcat'] ) ? $instance['show_wc_subcat'] : 'no',
                'exclude_specific_cat'     => isset( $instance['exclude_specific_cat'] ) ? $instance['exclude_specific_cat'] : 'no',
				'show_wc_img'        => isset( $instance['show_wc_img'] ) ? $instance['show_wc_img'] : 'no',
				'show_wp_subcat'     => isset( $instance['show_wp_subcat'] ) ? $instance['show_wp_subcat'] : 'no',
				'show_post'          => isset( $instance['show_post'] ) ? $instance['show_post'] : 'no',
				'highlight_curr_cat' => isset( $instance['highlight_curr_cat'] ) ? $instance['highlight_curr_cat'] : 'yes',
				'acc_style'          => isset( $instance['acc_style'] ) ? $instance['acc_style'] : 'style_1',
				'exclude_wc_cat'     => isset( $instance['exclude_wc_cat'] ) ? $instance['exclude_wc_cat'] : '',
				'exclude_wp_cat'     => isset( $instance['exclude_wp_cat'] ) ? $instance['exclude_wp_cat'] : '',
				'exclude_wp_cat_select'     => isset( $instance['exclude_wp_cat_select'] ) ? $instance['exclude_wp_cat_select'] : '',
				'hide_pages_posts'   => isset( $instance['hide_pages_posts'] ) ? $instance['hide_pages_posts'] : 'no',
				'exclude_page'       => isset( $instance['exclude_page'] ) ? $instance['exclude_page'] : '',
				'exclude_post'       => isset( $instance['exclude_post'] ) ? $instance['exclude_post'] : 'no',
				'how_show'           => isset( $instance['how_show'] ) ? $instance['how_show'] : '',
				'include_menu'       => isset( $instance['include_menu'] ) ? $instance['include_menu'] : array(),
				'show_count'         => isset( $instance['show_count'] ) ? $instance['show_count'] : 'no',
				'orderby'            => isset( $instance['orderby'] ) ? $instance['orderby'] : 'id',
				'order'              => isset( $instance['order'] ) ? $instance['order'] : 'asc',
				'tag_wc'             => isset( $instance['tag_wc'] ) ? $instance['tag_wc'] : 'yes',
				'tag_wp'             => isset( $instance['tag_wp'] ) ? $instance['tag_wp'] : 'yes',
				'post_limit'         => isset( $instance['post_limit'] ) ? $instance['post_limit'] : '-1',
				'name_wc_tag'        => isset( $instance['name_wc_tag'] ) ? $instance['name_wc_tag'] : __( 'WooCommerce TAGS', 'yith-woocommerce-category-accordion' ),
				'name_wp_tag'        => isset( $instance['name_wp_tag'] ) ? $instance['name_wp_tag'] : __( 'WordPress TAGS', 'yith-woocommerce-category-accordion' ),

			);

			$instance = wp_parse_args( $instance, $default );
			?>
            <style>
                #ywcca_widget_content {
                    background: #fff;
                }
                #ywcca_widget_content p.ywcca_name_tag_wc {
                    display:block;
                }
                #ywcca_widget_content p.ywcca_name_tag_wp {
                    display:block;
                }

                #ywcca_widget_content p.ywcca_select_field {
                    display:block;
                }

                #ywcca_widget_content p {
                    display: flex;
                    flex-direction: column;
                    margin: 0;
                    padding: 10px;
                    box-sizing: content-box;
                }

                #ywcca_widget_content .title_shortcode {
                    height: 39px;
                    margin-bottom: 30px;
                    margin-left: 10px;
                }

                #ywcca_widget_content label {
                    font-weight: bold;
                    margin-right: 10px;

                }

                #ywcca_widget_content input[type="checkbox"] {

                    width: 20px;
                    height: 20px;
                    float: left;
                }
                #ywcca_widget_content select {
                    height: 40px;
                }

                .select2-container {
                    display: block !important;
                    width: 100% !important;
                }

                .ywcca_order {
                    margin-top: 5px !important;
                }

                .ywcc_show_count_field {
                    display: flex;
                    align-items: center;
                    margin-left: 10px;
                }

                .ywcca_wc_field .yith-plugin-ui{
                    display: flex !important;
                    align-items: center;
                    margin-left: 9px;
                }
                .ywcca_highlight {
                    display: flex;
                    padding: 10px;
                }

                .ywcca_wp_sub_field {
                    display: flex;
                    padding: 10px;
                }

                .ywcca_wp_post_field {
                    display: flex;
                    padding: 10px;
                }
                p.ywcca_wp_post_limit {
                    height: 40px;
                }
                .ywcca_choose_tag_wc {
                    display: flex;
                    padding: 10px;
                }
                .ywcca_choose_tag_wp {
                    display: flex;
                    padding: 10px;
                }

                .yith-plugin-ui-show-wc-subcat{
                    margin-bottom: 10px;
                }

                .yith-plugin-ui-show-wc-subcat label {
                    margin-left: 10px;
                }

                .yith-plugin-ui-show-wc-img label {
                    margin-left: 10px;
                }

                .yith-plugin-ui-highlight-curr-cat label {
                    margin-left: 10px;
                }
                .ywcc_show_count_field label {
                    margin-left: 10px;
                }

                p.ywcca_exclude_page {
                    margin-bottom: -15px !important;
                }

                #ywcca_widget_content p.ywcca_menu_multiselect select {
                    height: 80px;
                }

                .ywcca_show_wp_subcat label {
                    margin-left: 10px;
                }
                .ywcca_show_post label {
                    margin-left: 10px;
                }
                .yith-plugin-ui-exclude-specific-cat label {
                    margin-left: 10px;
                    margin-bottom: 5px;
                }
                .yith-plugin-ui.yith-plugin-ui-exclude-specific-cat {
                    margin-bottom: 5px;
                }

                .ywcca_wc_exclude {
                    margin-left: 9px;
                    margin-bottom: 10px;
                    margin-top: 10px;
                }

                .ywcca_choose_tag_wc label {
                    margin-left: 10px;
                }

                .ywcca_choose_tag_wp label {
                    margin-left: 10px;
                }

                .yith-plugin-ui.title_shortcode {
                    width: 97%;
                }

                #ywcca_widget_content input[type=text] {
                    width: 98%;
                    height: 40px;
                    padding-left: 8px;
                }

                #ywcca_widget_content select {
                    width: 98%;
                    padding-left: 8px;
                }

                span.select2-selection.select2-selection--multiple {
                    width: 96% !important;
                }

                .yith-plugin-ui.yith-plugin-ui-hide-pages-post {
                    display: flex;
                    margin-left: 10px;
                    margin-top: 10px;
                }

                .yith-plugin-ui.yith-plugin-ui-hide-pages-post label {
                    margin-left: 10px;
                }

                .ywcca_exclude_page {
                    margin-left: 10px;
                }
                .ywcca_exclude_post {
                    margin-left: 10px;
                }
                .yith-plugin-ui-exclude-wp-cat-select {
                    display: flex;
                    margin-top: 40px !important;
                    margin-left: 10px;
                }

                .yith-plugin-ui-exclude-wp-cat-select label {
                    margin-left: 10px;
                }

                .ywcca_wp_exclude {
                    margin-left: 10px;
                }

                .ywcca_name_tag_wc {
                    margin-left: 10px;
                }
                .ywcca_name_tag_wp {
                    margin-left: 10px;
                }

            </style>

            <div id="ywcca_widget_content">
                <div class="yith-plugin-ui title_shortcode">
                <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title', 'yith-woocommerce-category-accordion' ); ?></label>
                    <?php
                    $title_field = array(
                        'id' => $this->get_field_id( 'title' ),
                        'type' => 'text',
                        'std' =>  $instance['title'],
                        'value' => $instance['title'],
                        'val' => $instance['title'],
                        'placeholder' => 'Insert a title',
                        'name' => $this->get_field_name( 'title' ),
                    );

                    yith_plugin_fw_get_field( $title_field, true );
                    ?>
                </div>

                <p class="ywcca_select_field">
                    <label for="<?php echo esc_attr( $this->get_field_id( 'how_show' ) ); ?>"><?php esc_html_e( 'Show in Accordion', 'yith-woocommerce-category-accordion' ); ?></label>
                    <select id="<?php echo esc_attr( $this->get_field_id( 'how_show' ) ); ?>"
                            name="<?php echo esc_attr( $this->get_field_name( 'how_show' ) ); ?>"
                            class="ywcca_select_howshow widefat">
                        <option value="" <?php selected( '', $instance['how_show'] ); ?>><?php esc_html_e( 'Select an option', 'yith-woocommerce-category-accordion' ); ?></option>
                        <option value="wc" <?php selected( 'wc', $instance['how_show'] ); ?> ><?php esc_html_e( 'WooCommerce Category', 'yith-woocommerce-category-accordion' ); ?></option>
                        <option value="wp" <?php selected( 'wp', $instance['how_show'] ); ?> ><?php esc_html_e( 'WordPress Category', 'yith-woocommerce-category-accordion' ); ?></option>
                        <option value="tag" <?php selected( 'tag', $instance['how_show'] ); ?> ><?php esc_html_e( 'Tags', 'yith-woocommerce-category-accordion' ); ?></option>
                        <option value="menu" <?php selected( 'menu', $instance['how_show'] ); ?> ><?php esc_html_e( 'Menu', 'yith-woocommerce-category-accordion' ); ?></option>
                    </select>
                </p>
                <div class="ywcca_wc_field ywcca_specific_categories"
                     style="display:<?php echo 'wc' === $instance['how_show'] ? 'block' : 'none'; ?>;">

                    <div class="yith-plugin-ui yith-plugin-ui-exclude-specific-cat">
                        <?php
                        $onoff_wc_field = array(
                            'id' => $this->get_field_id( 'exclude_specific_cat' ),
                            'type' => 'onoff',
                            'default' => 'yes',
                            'std' =>  $instance['exclude_specific_cat'],
                            'value' => $instance['exclude_specific_cat'],
                            'val' => $instance['exclude_specific_cat'],
                            //'class' => 'my-class-for-my-component',
                            'name' => $this->get_field_name( 'exclude_specific_cat' ),
                        );

                        yith_plugin_fw_get_field( $onoff_wc_field, true );
                        ?>
                        <label for="<?php echo esc_attr( $this->get_field_id( 'exclude_specific_cat' ) ); ?>"><?php esc_html_e( 'Exclude specific categories', 'yith-woocommerce-category-accordion' ); ?></label>
                    </div>
                    <div class="ywcca_wc_exclude">
                        <label for="<?php echo esc_attr( $this->get_field_id( 'exclude_wc_cat' ) ); ?>"><?php esc_html_e( 'Exclude:', 'yith-woocommerce-category-accordion' ); ?></label>
						<?php

						$category_ids = $instance['exclude_wc_cat'];

						if ( ! is_array( $category_ids ) ) {
							$category_ids = explode( ',', $category_ids );
						}

						$json_ids = array();

						foreach ( $category_ids as $category_id ) {

							$cat_name = get_term_by( 'id', $category_id, 'product_cat' );
							if ( ! empty( $cat_name ) ) {
								$json_ids[ $category_id ] = '#' . $cat_name->term_id . '-' . $cat_name->name;
							}
						}

						$args = array(
							'id'               => $this->get_field_id( 'exclude_wc_cat' ),
							'class'            => 'wc-product-search' . ( $is_default ? ' enhanced' : '' ),
							'name'             => $this->get_field_name( 'exclude_wc_cat' ),
							'data-multiple'    => true,
							'data-action'      => 'yith_category_accordion_json_search_wc_categories',
							'data-placeholder' => __( 'Select categories', 'yith-woocommerce-category-accordion' ),
							'data-selected'    => $json_ids,
							'value'            => implode( ',', array_keys( $json_ids ) ),
						);

						yit_add_select2_fields( $args );
						?>
                    </div>

                    <div class="yith-plugin-ui yith-plugin-ui-show-wc-subcat">
                        <?php
                        $onoff_wc_field = array(
                            'id' => $this->get_field_id( 'show_wc_subcat' ),
                            'type' => 'onoff',
                            'default' => 'yes',
                            'std' =>  $instance['show_wc_subcat'],
                            'value' => $instance['show_wc_subcat'],
                            'val' => $instance['show_wc_subcat'],
                            //'class' => 'my-class-for-my-component',
                            'name' => $this->get_field_name( 'show_wc_subcat' ),
                        );

                        yith_plugin_fw_get_field( $onoff_wc_field, true );
                        ?>
                        <label for="<?php echo esc_attr( $this->get_field_id( 'show_wc_subcat' ) ); ?>"><?php esc_html_e( 'Show Subcategories', 'yith-woocommerce-category-accordion' ); ?></label>
                    </div>
                    <div class="yith-plugin-ui yith-plugin-ui-show-wc-img">
                        <?php
                        $onoff_wc_field = array(
                            'id' => $this->get_field_id( 'show_wc_img' ),
                            'type' => 'onoff',
                            'default' => 'yes',
                            'std' =>  $instance['show_wc_img'],
                            'value' => $instance['show_wc_img'],
                            'val' => $instance['show_wc_img'],
                            //'class' => 'my-class-for-my-component',
                            'name' => $this->get_field_name( 'show_wc_img' ),
                        );

                        yith_plugin_fw_get_field( $onoff_wc_field, true );
                        ?>
                        <label for="<?php echo esc_attr( $this->get_field_id( 'show_wc_img' ) ); ?>"><?php esc_html_e( 'Show category image', 'yith-woocommerce-category-accordion' ); ?></label>
                    </div>

                </div>
                <div class="ywcca_wp_field"
                     style="display:<?php echo 'wp' === $instance['how_show'] ? 'block' : 'none'; ?>;">
                    <div class="ywcca_wp_sub_field yith-plugin-ui ywcca_show_wp_subcat">
                            <?php
                            $onoff_wc_field = array(
                                'id' => $this->get_field_id( 'show_wp_subcat' ),
                                'type' => 'onoff',
                                'default' => 'yes',
                                'std' =>  $instance['show_wp_subcat'],
                                'value' => $instance['show_wp_subcat'],
                                'val' => $instance['show_wp_subcat'],
                                //'class' => 'my-class-for-my-component',
                                'name' => $this->get_field_name( 'show_wp_subcat' ),
                            );
                            yith_plugin_fw_get_field( $onoff_wc_field, true );
                            ?>
                            <label for="<?php echo esc_attr( $this->get_field_id( 'show_wp_subcat' ) ); ?>"><?php esc_html_e( 'Show Subcategories', 'yith-woocommerce-category-accordion' ); ?></label>
                    </div>
                    <div class="ywcca_wp_post_field yith-plugin-ui ywcca_show_post">
                            <?php
                            $onoff_wc_field = array(
                                'id' => $this->get_field_id( 'show_post' ),
                                'type' => 'onoff',
                                'default' => 'yes',
                                'std' =>  $instance['show_post'],
                                'value' => $instance['show_post'],
                                'val' => $instance['show_post'],
                                //'class' => 'my-class-for-my-component',
                                'name' => $this->get_field_name( 'show_post' ),
                            );
                            yith_plugin_fw_get_field( $onoff_wc_field, true );
                            ?>
                            <label for="<?php echo esc_attr( $this->get_field_id( 'show_post' ) ); ?>"><?php esc_html_e( 'Show Last Post', 'yith-woocommerce-category-accordion' ); ?></label>
                    </div>
                    <div class="ywcca_wp_post_limit" style="width:97%; height: 40px; margin-left: 10px; margin-bottom: 20px;">
                        <label for="<?php echo esc_attr( $this->get_field_id( 'post_limit' ) ); ?>"><?php esc_html_e( 'Number Post (-1 for all post )', 'yith-woocommerce-category-accordion' ); ?></label>
                        <input type="text" id="<?php echo esc_attr( $this->get_field_id( 'post_limit' ) ); ?>"
                               name="<?php echo esc_attr( $this->get_field_name( 'post_limit' ) ); ?>"
                               value="<?php echo esc_attr( $instance['post_limit'] ); ?>">
                    </div>
                    <div class="yith-plugin-ui yith-plugin-ui-exclude-wp-cat-select">
                        <?php
                        $onoff_wc_field = array(
                            'id' => $this->get_field_id( 'exclude_wp_cat_select' ),
                            'type' => 'onoff',
                            'default' => 'yes',
                            'std' =>  $instance['exclude_wp_cat_select'],
                            'value' => $instance['exclude_wp_cat_select'],
                            'val' => $instance['exclude_wp_cat_select'],
                            //'class' => 'my-class-for-my-component',
                            'name' => $this->get_field_name( 'exclude_wp_cat_select' ),
                        );

                        yith_plugin_fw_get_field( $onoff_wc_field, true );
                        ?>
                        <label for="<?php echo esc_attr( $this->get_field_id( 'exclude_wp_cat_select' ) ); ?>"><?php esc_html_e( 'Exclude specific categories', 'yith-woocommerce-category-accordion' ); ?></label>
                    </div>
                    <div class="ywcca_wp_exclude" style="display:<?php echo 'no' == $instance['exclude_specific_cat'] ? 'none' : 'block'; ?>;">
                        <label for="<?php echo esc_attr( $this->get_field_id( 'exclude_wp_cat' ) ); ?>"><?php esc_html_e( 'Exclude Categories', 'yith-woocommerce-category-accordion' ); ?></label>
						<?php
						$category_ids = $instance['exclude_wp_cat'];

						if ( ! is_array( $category_ids ) ) {
							$category_ids = explode( ',', $category_ids );
						}
						$json_ids = array();

						foreach ( $category_ids as $category_id ) {

							$cat_name = get_term_by( 'id', $category_id, 'category' );
							if ( ! empty( $cat_name ) ) {
								$json_ids[ $category_id ] = '#' . $cat_name->term_id . '-' . $cat_name->name;
							}
						}

						$args = array(
							'id'               => $this->get_field_id( 'exclude_wp_cat' ),
							'class'            => 'wc-product-search' . ( $is_default ? ' enhanced' : '' ),
							'name'             => $this->get_field_name( 'exclude_wp_cat' ),
							'data-multiple'    => true,
							'data-action'      => 'yith_json_search_wp_categories',
							'data-placeholder' => __( 'Select categories', 'yith-woocommerce-category-accordion' ),
							'data-selected'    => $json_ids,
							'value'            => implode( ',', array_keys( $json_ids ) ),
						);

						yit_add_select2_fields( $args );

						?>

                    </div>
                </div>
                <div class="ywcca_menu_field"
                     style="display:<?php echo 'menu' === $instance['how_show'] ? 'block' : 'none'; ?>;">
					<?php
					$menu_option = yith_get_navmenu();

					?>
                    <p class="ywcca_menu_multiselect">
                        <label for="<?php echo esc_attr( $this->get_field_id( 'include_menu' ) ); ?>"><?php esc_html_e( 'Add menu in accordion', 'yith-woocommerce-category-accordion' ); ?></label>
                        <select id="<?php echo esc_attr( $this->get_field_id( 'include_menu' ) ); ?>"
                                name="<?php echo esc_attr( $this->get_field_name( 'include_menu' ) ); ?>[]"
                                multiple="multiple">
							<?php
							foreach ( $menu_option as $key => $val ) {
								?>

                                <option value="<?php echo esc_attr( $key ); ?>" <?php selected( in_array( $key, $instance['include_menu'], true ) ); ?>><?php echo esc_attr( $val ); ?></option>
								<?php
							}
							?>
                        </select>
                    </p>

                </div>

                <div class="ywcca_tags_field"
                     style="display:<?php echo 'tag' === $instance['how_show'] ? 'block' : 'none'; ?>;">
                    <div class="ywcca_choose_tag_wc yith-plugin-ui">
                            <?php
                            $onoff_wc_tag_field = array(
                                'id' => $this->get_field_id( 'tag_wc' ),
                                'type' => 'onoff',
                                'default' => 'yes',
                                'std' =>  $instance['tag_wc'],
                                'value' => $instance['tag_wc'],
                                'val' => $instance['tag_wc'],
                                //'class' => 'my-class-for-my-component',
                                'name' => $this->get_field_name( 'tag_wc' ),
                            );
                            yith_plugin_fw_get_field( $onoff_wc_tag_field, true );
                            ?>
                            <label for="<?php echo esc_attr( $this->get_field_id( 'tag_wc' ) ); ?>"><?php esc_html_e( 'Show WooCommerce Tags', 'yith-woocommerce-category-accordion' ); ?></label>
                    </div>
                    <div class="ywcca_name_tag_wc">
                        <label for="<?php echo esc_attr( $this->get_field_id( 'name_wc_tag' ) ); ?>"><?php esc_html_e( 'WooCommerce Tag Label', 'yith-woocommerce-category-accordion' ); ?></label>
                        <input type="text" id="<?php echo esc_attr( $this->get_field_id( 'name_wc_tag' ) ); ?>"
                               name="<?php echo esc_attr( $this->get_field_name( 'name_wc_tag' ) ); ?>"
                               value="<?php echo esc_attr( $instance['name_wc_tag'] ); ?>">
                    </div>

                    <div class="ywcca_choose_tag_wp yith-plugin-ui">
                            <?php
                            $onoff_wp_tag_field = array(
                                'id' => $this->get_field_id( 'tag_wp' ),
                                'type' => 'onoff',
                                'default' => 'yes',
                                'std' =>  $instance['tag_wp'],
                                'value' => $instance['tag_wp'],
                                'val' => $instance['tag_wp'],
                                //'class' => 'my-class-for-my-component',
                                'name' => $this->get_field_name( 'tag_wp' ),
                            );
                            yith_plugin_fw_get_field( $onoff_wp_tag_field, true );
                            ?>
                            <label for="<?php echo esc_attr( $this->get_field_id( 'tag_wp' ) ); ?>"><?php esc_html_e( 'Show WordPress Tags', 'yith-woocommerce-category-accordion' ); ?></label>
                    </div>
                    <div class="ywcca_name_tag_wp">
                        <label for="<?php echo esc_attr( $this->get_field_id( 'name_wp_tag' ) ); ?>"><?php esc_html_e( 'WordPress Tag Label', 'yith-woocommerce-category-accordion' ); ?></label>
                        <input type="text" id="<?php echo esc_attr( $this->get_field_id( 'name_wp_tag' ) ); ?>"
                               name="<?php echo esc_attr( $this->get_field_name( 'name_wp_tag' ) ); ?>"
                               value="<?php echo esc_attr( $instance['name_wp_tag'] ); ?>">
                    </div>
                </div>

                    <div class="ywcca_highlight yith-plugin-ui yith-plugin-ui-highlight-curr-cat">
                        <?php
                        $onoff_highlight = array(
                            'id' => $this->get_field_id( 'highlight_curr_cat' ),
                            'type' => 'onoff',
                            'default' => 'yes',
                            'std' =>  $instance['highlight_curr_cat'],
                            'value' => $instance['highlight_curr_cat'],
                            'val' => $instance['highlight_curr_cat'],
                            //'class' => 'my-class-for-my-component',
                            'name' => $this->get_field_name( 'highlight_curr_cat' ),
                        );

                        yith_plugin_fw_get_field( $onoff_highlight, true );
                        ?>
                        <label for="<?php echo esc_attr( $this->get_field_id( 'highlight_curr_cat' ) ); ?>"><?php esc_html_e( 'Highlight the current category', 'yith-woocommerce-category-accordion' ); ?></label>
                </div>
                <div class="ywcc_show_count_field yith-plugin-ui"
                     style="display:<?php echo 'wc' === $instance['how_show'] || 'wp' === $instance['how_show'] ? 'flex' : 'none'; ?>;">
                            <?php
                            $onoff_show_count = array(
                                'id' => $this->get_field_id( 'show_count' ),
                                'type' => 'onoff',
                                'default' => 'yes',
                                'std' =>  $instance['show_count'],
                                'value' => $instance['show_count'],
                                'val' => $instance['show_count'],
                                //'class' => 'my-class-for-my-component',
                                'name' => $this->get_field_name( 'show_count' ),
                            );

                            yith_plugin_fw_get_field( $onoff_show_count, true );
                            ?>
                     <label for="<?php echo esc_attr( $this->get_field_id( 'show_count' ) ); ?>"><?php esc_html_e( 'Show Count', 'yith-woocommerce-category-accordion' ); ?></label>
                </div>
                <p class="ywcca_select_style">
                    <label for="<?php echo esc_attr( $this->get_field_id( 'acc_style' ) ); ?>"><?php esc_html_e( 'Accordion style:', 'yith-woocommerce-category-accordion' ); ?></label>
                    <select id="<?php echo esc_attr( $this->get_field_id( 'acc_style' ) ); ?>"
                            name="<?php echo esc_attr( $this->get_field_name( 'acc_style' ) ); ?>">
						<?php
						$args = array(
							'numberposts' => - 1,
							'post_type'   => 'yith_cacc'
						);

						$category_styles_posts = get_posts( $args );

						if ( ! empty( $category_styles_posts ) ) {
							foreach ( $category_styles_posts as $style ) {
								?>
                                <option <?php echo 'value="' . $style->ID . '"';
								selected( $style->ID, $instance['acc_style'] ); ?>><?php esc_html_e( $style->post_title, 'yith-woocommerce-category-accordion' ); ?></option>

								<?php
							}
						}
						?>
                    </select>
                </p>
                <p class="ywcca_orderby"
                   style="display:<?php echo 'menu' === $instance['how_show'] ? 'none' : 'block'; ?>;">
                    <label for="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>"><?php esc_html_e( 'Order By', 'yith-woocommerce-category-accordion' ); ?></label>
                    <select class="ywcca_type_order" id="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>"
                            name="<?php echo esc_attr( $this->get_field_name( 'orderby' ) ); ?>">
                        <option value="name" <?php selected( 'name', $instance['orderby'] ); ?>><?php esc_html_e( 'Name', 'yith-woocommerce-category-accordion' ); ?></option>
                        <option value="count" <?php selected( 'count', $instance['orderby'] ); ?>><?php esc_html_e( 'Count', 'yith-woocommerce-category-accordion' ); ?></option>
                        <option value="id" <?php selected( 'id', $instance['orderby'] ); ?>><?php esc_html_e( 'ID', 'yith-woocommerce-category-accordion' ); ?></option>
                        <?php
                        if ( 'wc' === $instance['how_show'] ) :
                            ;
                            ?>
                            <option value="menu_order" <?php selected( 'menu_order', $instance['orderby'] ); ?>><?php esc_html_e( 'WooCommerce Order', 'yith-woocommerce-category-accordion' ); ?></option>
                        <?php endif; ?>
                    </select>
                    <select class="ywcca_order" id="<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>"
                            name="<?php echo esc_attr( $this->get_field_name( 'order' ) ); ?>">
                        <option value="asc" <?php selected( 'asc', $instance['order'] ); ?>><?php esc_html_e( 'ASC', 'yith-woocommerce-category-accordion' ); ?></option>
                        <option value="desc" <?php selected( 'desc', $instance['order'] ); ?>><?php esc_html_e( 'DESC', 'yith-woocommerce-category-accordion' ); ?></option>

                    </select>
                </p>
                <div class="yith-plugin-ui yith-plugin-ui-hide-pages-post">
                    <?php
                    $onoff_wc_field = array(
                        'id' => $this->get_field_id( 'hide_pages_posts' ),
                        'type' => 'onoff',
                        'default' => 'yes',
                        'std' =>  $instance['hide_pages_posts'],
                        'value' => $instance['hide_pages_posts'],
                        'val' => $instance['hide_pages_posts'],
                        //'class' => 'my-class-for-my-component',
                        'name' => $this->get_field_name( 'hide_pages_posts' ),
                    );

                    yith_plugin_fw_get_field( $onoff_wc_field, true );
                    ?>
                    <label for="<?php echo esc_attr( $this->get_field_id( 'hide_pages_posts' ) ); ?>"><?php esc_html_e( 'Hide in specific pages/posts', 'yith-woocommerce-category-accordion' ); ?></label>
                </div>
                <div class="ywcca_exclude_page">
                    <label for="<?php echo esc_attr( $this->get_field_id( 'exclude_page' ) ); ?>"><?php esc_html_e( 'Hide Accordion in  pages', 'yith-woocommerce-category-accordion' ); ?></label>
					<?php
					$post_ids = $instance['exclude_page'];

					if ( ! is_array( $post_ids ) ) {
						$post_ids = explode( ',', $post_ids );
					}
					$json_ids = array();

					foreach ( $post_ids as $post_id ) {

						$post_name = get_post( $post_id );
						if ( ! empty( $post_name ) ) {
							$json_ids[ $post_id ] = $post_name->post_title;
						}
					}
					$args = array(
						'id'               => $this->get_field_id( 'exclude_page' ),
						'class'            => 'wc-product-search' . ( $is_default ? ' enhanced' : '' ),
						'name'             => $this->get_field_name( 'exclude_page' ),
						'data-multiple'    => true,
						'data-action'      => 'yith_json_search_wp_pages',
						'data-placeholder' => __( 'Select page', 'yith-woocommerce-category-accordion' ),
						'data-selected'    => $json_ids,
						'value'            => implode( ',', array_keys( $json_ids ) ),
					);

					yit_add_select2_fields( $args );
					?>

                </div>
                <div class="ywcca_exclude_post">
                    <label for="<?php echo esc_attr( $this->get_field_id( 'exclude_post' ) ); ?>"><?php esc_html_e( 'Hide Accordion in  posts', 'yith-woocommerce-category-accordion' ); ?></label>
					<?php
					$post_ids = $instance['exclude_post'];
					if ( ! is_array( $post_ids ) ) {
						$post_ids = explode( ',', $post_ids );
					}
					$json_ids = array();

					foreach ( $post_ids as $post_id ) {

						$post_name = get_post( $post_id );
						if ( ! empty( $post_name ) ) {
							$json_ids[ $post_id ] = $post_name->post_title;
						}
					}
					$args = array(
						'id'               => $this->get_field_id( 'exclude_post' ),
						'class'            => 'wc-product-search' . ( $is_default ? ' enhanced' : '' ),
						'name'             => $this->get_field_name( 'exclude_post' ),
						'data-multiple'    => true,
						'data-action'      => 'yith_json_search_wp_posts',
						'data-placeholder' => __( 'Select post', 'yith-woocommerce-category-accordion' ),
						'data-selected'    => $json_ids,
						'value'            => implode( ',', array_keys( $json_ids ) ),
					);

					yit_add_select2_fields( $args );
					?>

                </div>
            </div>

			<?php
		}


		/**
		 * Update
		 *
		 * @param array $new_instance new_instance.
		 * @param array $old_instance old_instance.
		 *
		 * @return $instance
		 */
		public function update( $new_instance, $old_instance ) {

			$instance = array();

			$instance['title']              = $new_instance['title'] ?? '';
			$instance['show_wc_subcat']     = $new_instance['show_wc_subcat'] ?? 'no';
			$instance['exclude_specific_cat']     = $new_instance['exclude_specific_cat'] ?? 'no';
			$instance['show_wc_img']        = $new_instance['show_wc_img'] ?? 'no';
			$instance['show_wp_subcat']     = $new_instance['show_wp_subcat'] ?? 'no';
			$instance['show_post']          = $new_instance['show_post'] ?? 'no';
			$instance['highlight_curr_cat'] = $new_instance['highlight_curr_cat'] ?? 'no';
			$instance['acc_style']          = $new_instance['acc_style'] ?? '';
			$instance['exclude_wc_cat']     = isset( $new_instance['exclude_wc_cat'] ) ? esc_sql( $new_instance['exclude_wc_cat'] ) : '';
			$instance['exclude_wp_cat']     = isset( $new_instance['exclude_wp_cat'] ) ? esc_sql( $new_instance['exclude_wp_cat'] ) : '';
			$instance['exclude_wp_cat_select']     = isset( $new_instance['exclude_wp_cat_select'] ) ? esc_sql( $new_instance['exclude_wp_cat_select'] ) : '';
			$instance['hide_pages_posts']   = isset($new_instance['hide_pages_posts']) ? $new_instance['hide_pages_posts'] : 'no';
			$instance['exclude_page']       = $new_instance['exclude_page'] ?? '';
			$instance['exclude_post']       = $new_instance['exclude_post'] ?? 'no';
			$instance['how_show']           = $new_instance['how_show'] ?? '';
			$instance['include_menu']       = $new_instance['include_menu'] ?? array();
			$instance['show_count']         = $new_instance['show_count'] ?? 'no';
			$instance['orderby']            = $new_instance['orderby'] ?? 'id';
			$instance['order']              = $new_instance['order'] ?? 'asc';
			$instance['tag_wc']             = $new_instance['tag_wc'] ?? 'no';
			$instance['tag_wp']             = $new_instance['tag_wp'] ?? 'no';
			$instance['post_limit']         = $new_instance['post_limit'] ?? '-1';
			$instance['name_wc_tag']        = $new_instance['name_wc_tag'] ?? __( 'WooCommerce TAGS', 'yith-woocommerce-category-accordion' );
			$instance['name_wp_tag']        = $new_instance['name_wp_tag'] ?? __( 'WordPress TAGS', 'yith-woocommerce-category-accordion' );

			return $instance;
		}
	}
}
