<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Tp_Woo_Swatches_Meta.
 *
 * @package theplus
 */

if ( ! class_exists( 'Tp_Woo_Swatches_Term_Meta' ) ){
	
    class Tp_Woo_Swatches_Term_Meta {
		
		private static $_instance;
		
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}
	
        public function __construct() {
			add_action( 'admin_enqueue_scripts', [ $this,'theplus_elementor_admin_woo'] );
			add_action( 'admin_init', [ $this,'tp_add_product_taxonomy_meta'] );
			add_action( 'woocommerce_product_option_terms',  [ $this,'tp_product_option_terms'], 20, 3 );
        }
		public function theplus_elementor_admin_woo() { 
			wp_enqueue_media();
			wp_enqueue_script( 'wp-color-picker', THEPLUS_ASSETS_URL . 'js/extra/wp-color-picker-alpha.min.js',array() , THEPLUS_VERSION, true );
			
		}
	/**
	* @since 4.1.8
	* Woocommerce Custom Meta field
	*/

	public static function tp_taxonomy_meta_fields( $field_id = false ) {

		$fields = array();

		$fields['color'] = array(
			array(
				'label' => esc_html__( 'Color', 'theplus' ), // <label>
				'desc'  => esc_html__( 'Choose a color', 'theplus' ), // description
				'id'    => 'product_attribute_color', // name of field
				'type'  => 'color'
			)
		);

		$fields['image'] = array(
			array(
				'label' => esc_html__( 'Image', 'theplus' ), // <label>
				'desc'  => esc_html__( 'Choose an Image', 'theplus' ), // description
				'id'    => 'product_attribute_image', // name of field
				'type'  => 'image'
			)
		);
		
		$fields['button'] = array(
			array(
				'label' => esc_html__( 'Button', 'theplus' ), // <label>
				'desc'  => esc_html__( 'Add Button Text', 'theplus' ), // description
				'id'    => 'product_attribute_button', // name of field
				'type'  => 'button'
			)
		);
		
		if ( $field_id ) {
			return isset( $fields[ $field_id ] ) ? $fields[ $field_id ] : array();
		}

		return $fields;

	}
	/**
	* @since 4.1.8
	* Woocommerce Custom Term Meta
	*/

	public function tp_add_product_taxonomy_meta(){

		$fields = $this->tp_taxonomy_meta_fields();
		$meta_added_for =  array_keys( $fields );


		if ( function_exists( 'wc_get_attribute_taxonomies' ) ){
			$attribute_taxonomies = wc_get_attribute_taxonomies();
			
			if ( $attribute_taxonomies ){
				foreach( $attribute_taxonomies as $tax ) {
					$product_attr  = wc_attribute_taxonomy_name( $tax->attribute_name );
					$product_attr_type = $tax->attribute_type;					
					if ( in_array( $product_attr_type, $meta_added_for ) ) {
						$this->tp_term_meta( $product_attr, 'product', $fields[ $product_attr_type ] );
						do_action( 'wc_attribute_taxonomy_meta_added', $product_attr, $product_attr_type );
					}
				}
			}
		}
	}	
	
	/**
	* @since 4.1.8
	* Woocommerce Call Meta Class
	*/

	public function tp_term_meta( $taxonomy, $post_type, $fields ){
		return new \Tp_Term_Meta( $taxonomy, $post_type, $fields );
	}
	
	/**
	* @since 4.1.8
	* Woocommerce Call Meta Class
	*/
	public function tp_product_option_terms($attribute_taxonomy, $i, $attribute){
		if ( in_array( $attribute_taxonomy->attribute_type, array_keys( $this->tp_taxonomy_meta_fields() ) ) ) {
			?>
				<select multiple="multiple" data-placeholder="<?php esc_attr_e( 'Select terms', 'theplus' ); ?>" class="multiselect attribute_values wc-enhanced-select" name="attribute_values[<?php echo esc_attr( $i ); ?>][]">
				<?php
				$args      = array(
					'orderby'    => 'name',
					'hide_empty' => 0,
				);
				$all_terms = get_terms( $attribute->get_taxonomy(), apply_filters( 'woocommerce_product_attribute_terms', $args ) );
				if ( $all_terms ) {
					foreach ( $all_terms as $term ) {
						$options = $attribute->get_options();
						$options = ! empty( $options ) ? $options : array();
						echo '<option value="' . esc_attr( $term->term_id ) . '"' . wc_selected( $term->term_id, $options ) . '>' . esc_attr( apply_filters( 'woocommerce_product_attribute_term_name', $term->name, $term ) ) . '</option>';
					}
				}
				?>
			</select>
			<button class="button plus select_all_attributes"><?php esc_html_e( 'Select all', 'theplus' ); ?></button>
			<button class="button minus select_no_attributes"><?php esc_html_e( 'None', 'theplus' ); ?></button>

			<?php
		}
	}
    }
}
new Tp_Woo_Swatches_Term_Meta();