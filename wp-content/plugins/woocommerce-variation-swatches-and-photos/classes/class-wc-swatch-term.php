<?php

class WC_Swatch_Term {

	public $attribute_meta_key;
	public $term_id;
	public $term;
	public $term_label;
	public $term_slug;
	public $taxonomy_slug;
	public $selected;
	public $type;
	public $color;
	public $thumbnail_src;
	public $thumbnail_id;
	public $thumbnail_alt = 'thumbnail';
	public $size;
	public $width = 32;
	public $height = 32;
	public $description;

	public $_debug_log = array();



	public function __construct( $attribute_configuration, $term_id, $taxonomy, $selected = false, $size = 'swatches_image_size' ) {

		$this->attribute_meta_key = 'swatches_id';
		$this->term_id            = $term_id;
		$this->term               = get_term( $term_id, $taxonomy );
		$this->term_label         = $this->term->name;
		$this->term_slug          = $this->term->slug;

		$this->taxonomy_slug = $taxonomy;
		$this->selected      = $selected;
		$this->size          = $size;

		$this->on_init();
	}

	public function on_init() {
		$this->init_size( $this->size );

		$type               = get_term_meta( $this->term_id, $this->meta_key() . '_type', true );
		$color              = get_term_meta( $this->term_id, $this->meta_key() . '_color', true );
		$this->thumbnail_id = get_term_meta( $this->term_id, $this->meta_key() . '_photo', true );
		$this->description  = term_description( $this->term_id, $this->taxonomy_slug );

		$this->type          = $type;
		$this->thumbnail_src = apply_filters( 'woocommerce_placeholder_img_src', WC()->plugin_url() . '/assets/images/placeholder.png' );
		$this->color         = '#FFFFFF';

		if ( $type == 'photo' ) {
			if ( $this->thumbnail_id ) {
				$imgsrc = wp_get_attachment_image_src( $this->thumbnail_id, $this->size );
				if ( $imgsrc && is_array( $imgsrc ) ) {
					$this->thumbnail_src = current( $imgsrc );
					$this->thumbnail_alt = trim( strip_tags( get_post_meta( $this->thumbnail_id, '_wp_attachment_image_alt', true ) ) );
				} else {
					$this->thumbnail_src = apply_filters( 'woocommerce_placeholder_img_src', WC()->plugin_url() . '/assets/images/placeholder.png' );
				}
			} else {
				$this->thumbnail_src = apply_filters( 'woocommerce_placeholder_img_src', WC()->plugin_url() . '/assets/images/placeholder.png' );
			}
		} elseif ( $type == 'color' ) {
			$this->color = $color;
		}
	}

	public function init_size( $size ) {
		$sizes = $this->get_image_sizes();
		$this->size = $size;
		$the_size   = isset( $sizes[ $size ] ) ? $sizes[ $size ] : $sizes['swatches_image_size'];
		if ( isset( $the_size['width'] ) && ! empty( $the_size['width'] ) && isset( $the_size['height'] ) && ! empty( $the_size['height'] ) ) {

			$this->width  = $the_size['width'];
			$this->height = $the_size['height'];


		} else {

			$image_size = get_option( 'swatches_image_size', array(
				'width'  => 32,
				'height' => 32
			) );

			$loaded_size = array();
			$loaded_size['width']  = isset( $image_size['width'] ) && ! empty( $image_size['width'] ) ? $image_size['width'] : 32;
			$loaded_size['height'] = isset( $image_size['height'] ) && ! empty( $image_size['height'] ) ? $image_size['height'] : 32;

			$image_size = apply_filters( 'woocommerce_get_image_size_swatches_image_size', $loaded_size );


			$this->width  = apply_filters( 'woocommerce_swatches_size_width_default', $image_size['width'] );
			$this->height = apply_filters( 'woocommerce_swatches_size_height_default', $image_size['height'] );

		}
	}

	public function get_output( $placeholder = true, $placeholder_src = 'default' ) {

		$picker = '';

		$href         = apply_filters( 'woocommerce_swatches_get_swatch_href', '#', $this );
		$anchor_class = apply_filters( 'woocommerce_swatches_get_swatch_anchor_css_class', 'swatch-anchor', $this );
		$image_class  = apply_filters( 'woocommerce_swatches_get_swatch_image_css_class', 'swatch-img', $this );
		$image_alt    = apply_filters( 'woocommerce_swatches_get_swatch_image_alt', $this->thumbnail_alt, $this );

		if ( $this->type == 'photo' || $this->type == 'image' ) {
			$picker .= '<a href="' . $href . '" style="width:' . $this->width . 'px;height:' . $this->height . 'px;" title="' . esc_attr( $this->term_label ) . '" class="' . $anchor_class . '">';
			$picker .= '<img src="' . apply_filters( 'woocommerce_swatches_get_swatch_image', $this->thumbnail_src, $this->term_slug, $this->taxonomy_slug, $this ) . '" alt="' . $image_alt . '" class="wp-post-image swatch-photo' . $this->meta_key() . ' ' . $image_class . '" width="' . $this->width . '" height="' . $this->height . '"/>';
			$picker .= '</a>';
		} elseif ( $this->type == 'color' ) {
			$picker .= '<a href="' . $href . '" style="text-indent:-9999px;width:' . $this->width . 'px;height:' . $this->height . 'px;background-color:' . apply_filters( 'woocommerce_swatches_get_swatch_color', $this->color, $this->term_slug, $this->taxonomy_slug, $this ) . ';" title="' . $this->term_label . '" class="' . $anchor_class . '">' . $this->term_label . '</a>';
		} elseif ( $placeholder ) {
			if ( $placeholder_src == 'default' ) {
				$src = apply_filters( 'woocommerce_placeholder_img_src', WC()->plugin_url() . '/assets/images/placeholder.png' );
			} else {
				$src = $placeholder_src;
			}

			$picker .= '<a href="' . $href . '" style="width:' . $this->width . 'px;height:' . $this->height . 'px;" title="' . esc_attr( $this->term_label ) . '"  class="' . $anchor_class . '">';
			$picker .= '<img src="' . $src . '" alt="' . $image_alt . '" class="wp-post-image swatch-photo' . $this->meta_key() . ' ' . $image_class . '" width="' . $this->width . '" height="' . $this->height . '"/>';
			$picker .= '</a>';
		} else {
			return '';
		}

		$out = '<div class="select-option swatch-wrapper' . ( $this->selected ? ' selected' : '' ) . '" data-attribute="' . esc_attr( $this->taxonomy_slug ) . '" data-value="' . esc_attr( $this->term_slug ) . '">';
		$out .= apply_filters( 'woocommerce_swatches_picker_html', $picker, $this );
		$out .= '</div>';

		return $out;
	}

	public function get_type() {
		return $this->type;
	}

	public function get_color() {
		return $this->color;
	}

	public function get_image_src() {
		return $this->thumbnail_src;
	}

	public function get_image_id() {
		return $this->thumbnail_id;
	}

	public function get_width() {
		return $this->width;
	}

	public function get_height() {
		return $this->height;
	}

	public function meta_key() {
		return $this->taxonomy_slug . '_' . $this->attribute_meta_key;
	}

	protected function get_image_sizes() {
		global $_wp_additional_image_sizes;

		$sizes = array();

		foreach ( get_intermediate_image_sizes() as $_size ) {
			if ( in_array( $_size, array('thumbnail', 'medium', 'medium_large', 'large') ) ) {
				$sizes[ $_size ]['width']  = get_option( "{$_size}_size_w" );
				$sizes[ $_size ]['height'] = get_option( "{$_size}_size_h" );
				$sizes[ $_size ]['crop']   = (bool) get_option( "{$_size}_crop" );
			} elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
				$sizes[ $_size ] = array(
					'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
					'height' => $_wp_additional_image_sizes[ $_size ]['height'],
					'crop'   => $_wp_additional_image_sizes[ $_size ]['crop'],
				);
			}
		}

		return $sizes;
	}

}
