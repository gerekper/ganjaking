<?php
namespace Happy_Addons_Pro;

use Elementor\Controls_Manager;
use Happy_Addons_Pro\Controls\Image_Selector;

defined( 'ABSPATH' ) || die();

class Designs_Manager {

	public static function init() {
		if ( ! hapro_get_appsero()->license()->is_valid() ) {
			return;
		}
		// add_action( 'happyaddons_start_register_controls', [ __CLASS__, 'add_surprise_controls' ], 10, 3 );
		if( defined('HAPPY_ADDONS_VERSION') && HAPPY_ADDONS_VERSION >= '3.8.4' ) {
			add_action( 'happyaddons_after_register_content_controls', [ __CLASS__, 'add_surprise_controls' ], 10, 3 );
		}else{
			add_action( 'happyaddons_start_register_controls', [ __CLASS__, 'add_surprise_controls' ], 10, 3 );
		}
		add_action( 'elementor/editor/after_enqueue_scripts', [ __CLASS__, 'enqueue_editor_scripts' ] );
		add_action( 'wp_ajax_ha_make_me_surprised', [ __CLASS__, 'surprise_me' ] );
	}

	public static function surprise_me() {
		if ( ! check_ajax_referer( self::get_secret_id(), 'secret' ) ) {
			wp_send_json_error( __( 'Invalid surprise request', 'happy-addons-pro' ), 403 );
		}

		if ( empty( $_GET['widget'] ) ) {
			wp_send_json_error( __( 'Incomplete surprise request', 'happy-addons-pro' ), 404 );
		}

		if ( ! ( $surprises = self::get_surprises( substr( $_GET['widget'], 3 ) ) ) ) {
			wp_send_json_error( __( 'Surprise not found', 'happy-addons-pro' ), 404 );
		}

		// Finally you got the surprise
		wp_send_json_success( $surprises, 200 );
	}

	protected static function get_surprises( $design_name ) {
		$design = HAPPY_ADDONS_PRO_DIR_PATH . 'assets/designs/' . $design_name . '/' . $design_name . '.json';
		if ( ! is_readable( $design ) ) {
			return false;
		}
		return file_get_contents( $design );
	}

	private static function get_secret_id() {
		return 'ha_surprise_secret';
	}

	public static function enqueue_editor_scripts() {
		$data = '
        .elementor-control-_ha_design .ha-reset-design {
            position: absolute;
            -webkit-transform: translateY(-50%);
            transform: translateY(-50%);
            cursor: pointer;
			top: 2%;
			left: auto;
			right: 20px;
			padding: 3px;
			border-radius: 6px;
			box-shadow: none;
			font-size: var(--control-title-size);
			border: 0;
			background: none;
        }

        .elementor-control-_ha_design .ha-reset-design:hover {
            color: #562dd4;
        }
        ';
		wp_add_inline_style( 'hapro-editor-css', $data );

		if ( hapro_is_elementor_version( '>=', '2.8.0' ) ) {
			$src = HAPPY_ADDONS_PRO_ASSETS . 'admin/js/design-new.min.js';
		} else {
			$src = HAPPY_ADDONS_PRO_ASSETS . 'admin/js/design.min.js';
		}

		wp_enqueue_script(
			'hapro-design',
			$src,
			[ 'elementor-editor' ],
			HAPPY_ADDONS_PRO_VERSION,
			true
		);

		wp_localize_script(
			'hapro-design',
			'hapro',
			[
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'secret'  => wp_create_nonce( self::get_secret_id() ),
			]
		);
	}

	/**
	 * @param $widget
	 */
	public static function add_surprise_controls( $widget ) {
		$widget_key = substr( $widget->get_name(), 3 );
		$designs    = self::get_designs_map();

		if ( isset( $designs[ $widget_key ] ) && ! empty( $designs[ $widget_key ] ) ) {
			$widget->start_controls_section(
				'_section_ha_design',
				[
					'label' => __( 'Presets', 'happy-addons-pro' ),
					'tab'   => Controls_Manager::TAB_CONTENT,
				]
			);

			$widget->add_control(
				'_ha_design',
				[
					'label'          => __( 'Designs', 'happy-addons-pro' ),
					'label_block'    => true,
					'show_label'     => true,
					'type'           => Image_Selector::TYPE,
					'default'        => '',
					'options'        => self::get_image_list( $widget_key, $designs[ $widget_key ] ),
					'render_type'    => 'none',
					'style_transfer' => true,
				]
			);

			$widget->end_controls_section();
		}
	}

	public static function get_image_list( $widget_key, $design_count ) {
		$list      = [];
		$path      = HAPPY_ADDONS_PRO_DIR_URL . 'assets/designs/' . $widget_key;
		$extension = '.jpg';
		for ( $i = 1; $i <= $design_count; $i++ ) {
			$list[ 'design-' . $i ] = [
				'title' => ucwords( 'design ' . $i ),
				'url'   => $path . '/design-' . $i . $extension,
			];
		}
		return $list;
	}

	public static function get_designs_map() {
		return [
			'accordion'                     => 9,
			'advanced-data-table'           => 4,
			'advanced-heading'              => 14,
			'advanced-slider'               => 8,
			'advanced-tabs'                 => 13,
			'animated-text'                 => 10,
			'author-list'                   => 5,
			'bar-chart'                     => 4,
			'breadcrumbs'                   => 11,
			'business-hour'                 => 10,
			'calderaform'                   => 4,
			'card'                          => 13,
			'carousel'                      => 6,
			'cf7'                           => 4,
			'content-switcher'              => 5,
			'countdown'                     => 12,
			'data-table'                    => 7,
			'dual-button'                   => 7,
			'edd-category-grid'             => 2,
			'edd-product-carousel'          => 2,
			'edd-product-grid'              => 2,
			'edd-single-product'            => 3,
			'event-calendar'                => 4,
			'feature-list'                  => 11,
			'flip-box'                      => 9,
			'fluent-form'                   => 6,
			'fun-factor'                    => 8,
			'gradient-heading'              => 5,
			'gravityforms'                  => 3,
			'horizontal-timeline'           => 6,
			'hotspots'                      => 6,
			'hover-box'                     => 7,
			'icon-box'                      => 11,
			'image-accordion'               => 5,
			'image-compare'                 => 5,
			'image-grid'                    => 5,
			'image-hover-effect'            => 17,
			'image-stack-group'             => 11,
			'infobox'                       => 12,
			'instagram-feed'                => 9,
			'line-chart'                    => 5,
			'link-hover'                    => 15,
			'list-group'                    => 10,
			'logo-carousel'                 => 8,
			'logo-grid'                     => 6,
			'mailchimp'                     => 11,
			'member'                        => 10,
			'news-ticker'                   => 11,
			'ninjaform'                     => 3,
			'number'                        => 8,
			'pie-chart'                     => 2,
			'polar-chart'                   => 2,
			'post-carousel'                 => 10,
			'post-grid-new'                 => 9,
			'post-list'                     => 12,
			'post-tab'                      => 6,
			'post-tiles'                    => 12,
			'price-menu'                    => 10,
			'pricing-table'                 => 11,
			'product-carousel-new'          => 3,
			'product-category-carousel-new' => 3,
			'product-category-grid-new'     => 6,
			'product-grid-new'              => 4,
			'promo-box'                     => 9,
			'radar-chart'                   => 2,
			'review'                        => 9,
			'scrolling-image'               => 8,
			'single-product-new'            => 7,
			'skills'                        => 4,
			'slider'                        => 4,
			'smart-post-list'               => 10,
			'social-icons'                  => 13,
			'social-share'                  => 14,
			'step-flow'                     => 7,
			'sticky-video'                  => 4,
			'taxonomy-list'                 => 7,
			'team-carousel'                 => 12,
			'testimonial'                   => 7,
			'testimonial-carousel'          => 13,
			'timeline'                      => 13,
			'toggle'                        => 11,
			'twitter-carousel'              => 8,
			'twitter-feed'                  => 8,
			'unfold'                        => 8,
			'wc-cart'                       => 1,
			'wc-checkout'                   => 1,
			'weform'                        => 3,
			'wpform'                        => 4,
		];
	}
}

Designs_Manager::init();
