<?php

class TP_Woo_Variation_Swatches_Front {
	
	protected static $instance = null;
	
	public static function instance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
	
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		
		add_filter( 'woocommerce_dropdown_variation_attribute_options_html', array( $this, 'tp_html_swatches' ), 150, 2 );
		add_filter( 'tp_woo_html_swatches', array( $this, 'tp_swatch_html' ), 5, 4 );
	}
	
	public function enqueue_scripts() {
		if(class_exists('woocommerce')){
			if(is_product()){
				wp_enqueue_style('plus-woo-swatches-front-css', THEPLUS_URL .'/assets/css/main/woo-swatches/woo-swatches-front.css',false,THEPLUS_VERSION);
				wp_enqueue_script('plus-woo-swatches-front-js',THEPLUS_URL . '/assets/js/main/woo-swatches/woo-swatches-front.js',false,THEPLUS_VERSION);
			}
		}
	}
	
	public function tp_html_swatches( $html, $args ) {
		
		global $wpdb;
		$swatch_types = tp_product_attr();
		$attr = substr( $args['attribute'], 3 );
		$attr = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "woocommerce_attribute_taxonomies WHERE attribute_name = %s", $attr ) );
		
		if ( empty( $attr ) ) {
			return $html;
		}

		if ( ! array_key_exists( $attr->attribute_type, $swatch_types ) ) {
			return $html;
		}

		$options   = $args['options'];
		$product   = $args['product'];
		$attribute = $args['attribute'];
		$class     = "variation-selector variation-select-{$attr->attribute_type}";
		$swatches  = '';
		
		$args['tooltip'] = wc_string_to_bool( get_option( 'tpwoo_enable_tooltip', 'yes' ) );
		
		if ( empty( $options ) && ! empty( $product ) && ! empty( $attribute ) ) {
			$attributes = $product->get_variation_attributes();
			$options    = $attributes[$attribute];
		}

		if ( array_key_exists( $attr->attribute_type, $swatch_types ) ) {
			if ( ! empty( $options ) && $product && taxonomy_exists( $attribute ) ) {
				
				$terms = wc_get_product_terms( $product->get_id(), $attribute, array( 'fields' => 'all' ) );

				foreach ( $terms as $term ) {
					if ( in_array( $term->slug, $options ) ) {
						$swatches .= apply_filters( 'tp_woo_html_swatches', '', $term, $attr->attribute_type, $args );
					}
				}
			}

			if ( ! empty( $swatches ) ) {
				$class    .= ' tp-woo-hidden';
				$swatches = '<ul class="tp-woo-swatches" data-attribute_name="attribute_' . esc_attr( $attribute ) . '">' . $swatches . '</ul>';
				$html     = '<div class="' . esc_attr( $class ) . '">' . $html . '</div>' . $swatches;
			}
		}

		return $html;
	}
	
	public function tp_swatch_html( $html, $term, $type, $args ) {
		$selected = sanitize_title( $args['selected'] ) == $term->slug ? 'selected' : '';
		$name     = esc_html( apply_filters( 'woocommerce_variation_option_name', $term->name ) );
		
		$tooltip  = '';		
		if ( ! empty( $args['tooltip'] ) ) {
			$tooltip = '<span class="tp-swatches-tooltip">' . ( $term->description ? $term->description : $name ) . '</span>';
		}
		
		if(!empty($type)){
			if($type=='color'){
				$color = get_term_meta( $term->term_id, 'product_attribute_color', true );				
				list( $r, $g, $b ) = sscanf( $color, "#%02x%02x%02x" );
				$html = sprintf(
					'<li class="tp-swatches tp-swatches-color tp-swatches-%s %s" style="background-color:%s;color:%s;" data-value="%s">%s%s</li>',
					esc_attr( $term->slug ),
					$selected,
					esc_attr( $color ),
					"rgba($r,$g,$b,0.5)",
					esc_attr( $term->slug ),
					$name,
					$tooltip
				);
			}else if($type=='image'){
				$image = get_term_meta( $term->term_id, 'product_attribute_image', true );
				$image = $image ? wp_get_attachment_image_src( $image ) : '';
				$image = $image ? $image[0] : WC()->plugin_url() . '/assets/images/placeholder.png';
				$html  = sprintf(
					'<li class="tp-swatches tp-swatches-image tp-swatches-%s %s" data-value="%s"><img src="%s" alt="%s">%s%s</li>',
					esc_attr( $term->slug ),
					$selected,
					esc_attr( $term->slug ),
					esc_url( $image ),
					esc_attr( $name ),
					$name,
					$tooltip
				);
			}else if($type=='button'){
				$button = get_term_meta( $term->term_id, 'product_attribute_button', true );
				$button = $button ? $button : $name;
				$html  = sprintf(
					'<li class="tp-swatches tp-swatches-button tp-swatches-%s %s" data-value="%s">%s%s</li>',
					esc_attr( $term->slug ),
					$selected,
					esc_attr( $term->slug ),
					esc_html( $button ),
					$tooltip
				);
			}
		}		

		return $html;
	}
}