<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Tp_Woo_Swatches_Meta.
 *
 * @package theplus
 */

if ( ! class_exists( 'Tp_Term_Meta' ) ){
	
    class Tp_Term_Meta {
        private $taxonomy;
        private $post_type;
        private $fields = array();
        
        public function __construct( $taxonomy, $post_type, $fields = array() ) {
           
            $this->taxonomy  = $taxonomy;
            $this->post_type = $post_type;
            $this->fields    = $fields;
            // Add form

            add_action( 'delete_term', array( $this, 'delete_term' ), 5, 4 );
            add_action( "{$this->taxonomy}_add_form_fields", array( $this, 'tp_add_field' ) );
            add_action( "{$this->taxonomy}_edit_form_fields", array( $this, 'tp_edit_field' ), 10 );
            add_action( "created_term", array( $this, 'save' ), 10, 3 );
			add_action( "edit_term", array( $this, 'save' ), 10, 3 );
            
            // Add columns
            add_filter( "manage_edit-{$this->taxonomy}_columns", array( $this, 'taxonomy_columns' ) );
            add_filter( "manage_{$this->taxonomy}_custom_column", array( $this, 'taxonomy_column' ), 10, 3 );
        }

        //Add Attributes Value
        function tp_add_field() {  
            $this->tp_generate_fields();
        }
        
        //Custom field
        function tp_generate_fields( $term = false ) {
            $screen = get_current_screen();
            
            if ( ( $screen->post_type == $this->post_type ) and ( $screen->taxonomy == $this->taxonomy ) ) {
                self::generate_form_fields( $this->fields, $term );
            }
        }
        function generate_form_fields( $fields, $term ) {
				
            $fields = apply_filters( 'tp_term_meta_fields', $fields, $term );
            
            if ( empty( $fields ) ) {
                return;
            }
            
            foreach ( $fields as $field ) {
                
                $field = apply_filters( 'tp_term_meta_field', $field, $term );
                $field[ 'id' ] = esc_html( $field[ 'id' ] );
                
                if ( ! $term ) {
                    $field[ 'value' ] = isset( $field[ 'default' ] ) ? $field[ 'default' ] : '';
                } else {
                    $field[ 'value' ] = get_term_meta( $term->term_id, $field[ 'id' ], true );
                }
                
                
                $field[ 'size' ]        = isset( $field[ 'size' ] ) ? $field[ 'size' ] : '40';
                $field[ 'required' ]    = ( isset( $field[ 'required' ] ) and $field[ 'required' ] == true ) ? ' aria-required="true"' : '';
                $field[ 'placeholder' ] = ( isset( $field[ 'placeholder' ] ) ) ? ' placeholder="' . $field[ 'placeholder' ] . '" data-placeholder="' . $field[ 'placeholder' ] . '"' : '';
                $field[ 'desc' ]        = ( isset( $field[ 'desc' ] ) ) ? $field[ 'desc' ] : '';
                
                $field[ 'dependency' ]  = ( isset( $field[ 'dependency' ] ) ) ? $field[ 'dependency' ] : array();

                self::field_start( $field, $term );
                switch ( $field[ 'type' ] ) {
                    case 'color':
                        ob_start();
                        ?>
                        <input name="<?php echo $field[ 'id' ] ?>" id="<?php echo $field[ 'id' ] ?>" type="text" class="tp-color-picker" value="<?php echo $field[ 'value' ] ?>" data-default-color="#ffffff" data-alpha-enabled="true" size="<?php echo $field[ 'size' ] ?>" <?php echo $field[ 'required' ] . $field[ 'placeholder' ] ?>>
                        <?php
                        echo ob_get_clean();
                        break;
                    case 'image':
                        ob_start();
                        ?>
                        <div class="tp-meta-image-field-wrapper">
                            <div class="image-preview">
                                <img data-placeholder="<?php echo THEPLUS_ASSETS_URL.'images/placeholder-grid.jpg' ?>" src="<?php echo esc_url( self::tp_get_img_src( $field[ 'value' ] ) ); ?>" width="60px" height="60px"/>
                            </div>
                            <div class="button-wrapper">
                                <input type="hidden" id="<?php echo $field[ 'id' ] ?>" name="<?php echo $field[ 'id' ] ?>" value="<?php echo esc_attr( $field[ 'value' ] ) ?>"/>
                                <button type="button" class="tp_upload_image_button button button-primary button-small"><?php esc_html_e( 'Upload / Add image', 'theplus' ); ?></button>
                                <button type="button" style="<?php echo( empty( $field[ 'value' ] ) ? 'display:none' : '' ) ?>" class="tp_remove_image_button button button-danger button-small"><?php esc_html_e( 'Remove image', 'theplus' ); ?></button>
                            </div>
                        </div>
                        <?php
                        echo ob_get_clean();
                        break;
					case 'button':
						ob_start();
						?>
							<input name="<?php echo $field[ 'id' ] ?>" id="<?php echo $field[ 'id' ] ?>" type="text" class="tp-woo-text" value="<?php echo $field[ 'value' ] ?>" />
						<?php
						echo ob_get_clean();
						break;
                    default:
                        //do_action( 'tp_term_meta_field', $field, $term );
                        //break;
                    
                }
                self::field_end( $field, $term );
                
            }
        }

        //Edit Attributes Value
        function tp_edit_field($term) {
            $this->tp_generate_fields( $term );
        }

        //Save Attributes Value
        function save( $term_id, $tt_id = '', $taxonomy = '' ) {
				
            if ( $taxonomy == $this->taxonomy ) {
                foreach ( $this->fields as $field ) {
                    foreach ( $_POST as $post_key => $post_value ) {
                        if ( $field[ 'id' ] == $post_key ) {
                            switch ( $field[ 'type' ] ) {
                                case 'color':
                                    $post_value = esc_html( $post_value );
                                    break;
                                case 'image':
                                    $post_value = absint( $post_value );
                                    break;
                                default:
                                    do_action( 'tp_save_term_meta', $term_id, $field, $post_value, $taxonomy );
                                    break;
                            }
                            update_term_meta( $term_id, $field[ 'id' ], $post_value );
                        }
                    }
                }
                do_action( 'tp_after_term_meta_saved', $term_id, $taxonomy );
            }
        }
        
         //Woocommerce Custom Table Column
        function taxonomy_columns( $columns ) {
            $new_columns = array();
            
            if ( isset( $columns[ 'cb' ] ) ) {
                $new_columns[ 'cb' ] = $columns[ 'cb' ];
            }
            
            $new_columns[ 'tp-meta-preview' ] = '';
            
            if ( isset( $columns[ 'cb' ] ) ) {
                unset( $columns[ 'cb' ] );
            }
            return array_merge( $new_columns, $columns );
        }
        
        //Woocommerce Custom Table Column
        function taxonomy_column( $columns, $column, $term_id ) {
            
            $attribute = $this->tp_get_wc_attribute_taxonomy( $this->taxonomy );
            $fields =Tp_Woo_Swatches_Term_Meta::tp_taxonomy_meta_fields( $attribute->attribute_type );
            if(!empty($attribute) && !empty($fields)){
                $this->tp_attr_preview( $term_id, $attribute, $fields );
            }
            
        }

        function tp_get_wc_attribute_taxonomy($attribute_name){            
                global $wpdb;

                $attribute_name = str_replace( 'pa_', '', wc_sanitize_taxonomy_name( $attribute_name ) );
				
				$attribute_taxonomy = $wpdb->get_row( $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "woocommerce_attribute_taxonomies WHERE attribute_name = %s", $attribute_name) );

            return apply_filters( 'tp_get_wc_attribute_taxonomy', $attribute_taxonomy, $attribute_name );
        }

        //Field Start Function
        function field_start( $field, $term ) {
            
            ob_start();
            if ( ! $term ) {
                ?>
                <div class="form-field <?php echo esc_attr( $field[ 'id' ] ) ?> <?php echo empty( $field[ 'required' ] ) ? '' : 'form-required' ?>">
                <?php if ( $field[ 'type' ] !== 'checkbox' ) { ?>
                    <label for="<?php echo esc_attr( $field[ 'id' ] ) ?>"><?php echo $field[ 'label' ] ?></label>
                    <?php
                }
            } else {
                ?>
                <tr class="form-field  <?php echo esc_attr( $field[ 'id' ] ) ?> <?php echo empty( $field[ 'required' ] ) ? '' : 'form-required' ?>">
                <th scope="row">
                    <label for="<?php echo esc_attr( $field[ 'id' ] ) ?>"><?php echo $field[ 'label' ] ?></label>
                </th>
                <td>
                <?php
            }
            echo ob_get_clean();
        }

        //Field End Function
        function field_end( $field, $term ) {
				
            ob_start();
            if ( ! $term ) {
                ?>
                <p><?php echo $field[ 'desc' ] ?></p>
                </div>
                <?php
            } else {
                ?>
                <p class="description"><?php echo $field[ 'desc' ] ?></p></td>
                </tr>
                <?php
            }
            echo ob_get_clean();
        }

        function tp_get_img_src( $thumbnail_id = false ) {
            if ( ! empty( $thumbnail_id ) ) {
                $image = wp_get_attachment_thumb_url( $thumbnail_id );
            } else {
                $image = THEPLUS_ASSETS_URL.'images/placeholder-grid.jpg';
            }
            
            return $image;
        }

        //Woocommerce Attr Preview
        function tp_attr_preview($term_id, $attribute, $fields){

            $key   = $fields[0]['id'];
            if($key == 'product_attribute_color'){
                $value = sanitize_hex_color( get_term_meta( $term_id, $key, true ) );
                printf( '<div class="tp-color-preview" style="background-color:%s;"></div>', esc_attr( $value ) );
            }else if($key == 'product_attribute_image'){
                $attachment_id = absint( get_term_meta( $term_id, $key, true ) );
                $image         = wp_get_attachment_image_src( $attachment_id, 'thumbnail' );
                if ( is_array( $image ) ) {
                    printf( '<img src="%s" alt="" width="%d" height="%d" class="wvs-preview wvs-image-preview" />', esc_url( $image[0] ), 40 , 40 );
                }
            }
            
        }

        //Delete Terms
        function delete_term( $term_id, $tt_id, $taxonomy, $deleted_term ) {
            global $wpdb;
            
            $term_id = absint( $term_id );
            if ( $term_id and $taxonomy == $this->taxonomy ) {
                $wpdb->delete( $wpdb->termmeta, array( 'term_id' => $term_id ), array( '%d' ) );
            }
        }
    }   
}