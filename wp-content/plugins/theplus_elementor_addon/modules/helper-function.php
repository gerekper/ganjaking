<?php
namespace TheplusAddons\Widgets;
use TheplusAddons\Theplus_Element_Load;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
// Get Elementor Template 
function theplus_get_templates() {
	// if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
		$templates = Theplus_Element_Load::elementor()->templates_manager->get_source( 'local' )->get_items();
		$types = [];

		if (empty($templates)) {
			$options = [ '0' => esc_html__( 'You Haven’t Saved Templates Yet.', 'theplus' ) ];
		}else{
			$options = [ '0' => esc_html__( 'Select Template', 'theplus' ) ];
			
			foreach ( $templates as $template ) {
				$options[ $template['template_id'] ] = $template['title'] . ' (' . $template['type'] . ')';
				$types[ $template['template_id'] ] = $template['type'];
			}
		}

		return $options;
	// }
}

/*get template*/

/*Widget Error Html*/
function theplus_get_widgetError($Title, $Massage) {
	$sanitized_title = sanitize_text_field($Title);
    $sanitized_massage = sanitize_text_field($Massage);

	$HTML = "<div class='tp-widget-error-notice'>
				<div class='tp-widget-error-thumb'><svg width='56' height='56' viewBox='0 0 56 56' fill='none' xmlns='http://www.w3.org/2000/svg'><path d='M28 0L52.2487 14V42L28 56L3.75129 42V14L28 0Z' fill='#DD4646'/><path d='M7.71539 16.2887L28 4.57735L48.2846 16.2887V39.7113L28 51.4226L7.71539 39.7113V16.2887Z' stroke='white'/><path fill-rule='evenodd' clip-rule='evenodd' d='M27.1016 15C25.9578 15 25.047 15.9575 25.1041 17.0999L25.9516 34.0499C25.9783 34.5822 26.4175 35 26.9504 35H29.0479C29.5807 35 30.02 34.5822 30.0466 34.0499L30.8941 17.0999C30.9513 15.9575 30.0405 15 28.8967 15H27.1016ZM26.9991 38C26.4468 38 25.9991 38.4477 25.9991 39V41C25.9991 41.5523 26.4468 42 26.9991 42H28.9991C29.5514 42 29.9991 41.5523 29.9991 41V39C29.9991 38.4477 29.5514 38 28.9991 38H26.9991Z' fill='white'/></svg></div>
				<div class='tp-widget-error-content'><span>{$sanitized_title}</span><Span>{$sanitized_massage}</Span></div>
			</div>";

	return $HTML;
}

//woo out of stock
function tp_out_of_stock() {
  global $post;
  $id = $post->ID;
  $status = get_post_meta($id, '_stock_status',true);
  
  if ($status == 'outofstock') {
  	return true;
  } else {
  	return false;
  }
}

/*registration user role*/
function tp_wp_lr_user_role() {
	global $wp_roles;

	if ( ! class_exists( 'WP_Roles' ) ) {
		return;
	}

	if ( ! isset( $wp_roles ) ) {
		$wp_roles = new WP_Roles(); 
	}

	$roles     = isset( $wp_roles->roles ) ? $wp_roles->roles : array();
	$all_roles = array();

	foreach ( $roles as $role_key => $role ) {
		$all_roles[ $role_key ] = $role['name'];
	}

	return apply_filters( 'user_registration_user_default_roles', $all_roles );
}
/*registration user role*/

function the_plus_get_term_options() {

$args = [];
$args['post_type'] = ['page'];
$args['posts_per_page'] = -1;

$query = get_posts($args);

// Initate an empty array
$output = [];

 if ( $query ) {
	$output['']='Select Template';
    foreach ( $query as $post ){        
        $output[ $post->ID ] = $post->post_title;
    }
}

    return $output;
}
/*get template*/

//woo single next previous
function theplus_remove_wpautop( $content, $autop = false ) {

	if ( $autop ) {
		$content = wpautop( preg_replace( '/<\/?p\>/', "\n", $content ) . "\n" );
	}

	return do_shortcode( shortcode_unautop( $content ) );
}

/*get woo attribute*/
function tp_products_attributes_list() {

	$attributes = wc_get_attribute_taxonomies();

	if ( empty( $attributes ) || ! is_array( $attributes ) ) {
		return array();
	}

	return wp_list_pluck( $attributes, 'attribute_label', 'attribute_name' );

}

//Get Page Ids
function theplus_get_pages_list() {
   $page_ids=get_all_page_ids();
    if ( empty( $page_ids ) ) {
        $options = [ '0' => esc_html__( 'Not Page List', 'theplus' ) ];
    } else {
        $options = [ '0' => esc_html__( 'Select Page', 'theplus' ) ];
        
        foreach ( $page_ids as $page ) {
            $options[ $page ] = get_the_title($page);
        }
    }

    return $options;
}
/*-custom post type-*/
function theplus_get_post_type() {
	$args = array(
		'public'   => true,
		'show_ui' => true
	);	 
	$post_types = get_post_types( $args, 'objects' );
	
	foreach ( $post_types  as $post_type ) {
		$exclude = array( 'attachment', 'elementor_library' );
		if( TRUE === in_array( $post_type->name, $exclude ) )
		  continue;
	  
		$options[$post_type->name] = $post_type->label;	   
	}
	return $options;
}
/*-custom post type-*/

/*-custom post taxonomies-*/
function theplus_get_post_taxonomies() {
	$args = array(
		'public'   => true,
    	'show_ui' => true
	);
	$output = 'names';	//	or objects
	$operator = 'and';	//	'and' or 'or'
	$options[''] = 'Select Attributes';

	$taxonomies = get_taxonomies( $args, $output, $operator );
	if ( $taxonomies ) {		
		foreach ( $taxonomies  as $taxonomy ) {
			$options[$taxonomy] = $taxonomy;				
		}

		if( function_exists('wc_get_attribute_taxonomies') ){
			$attributes =  wc_get_attribute_taxonomies();
			if($attributes){
				$options['product_attr'] = 'Product Attributes';			
			
			}
		}

		return $options;
	}
}
/*-custom post taxonomies-*/

/*-woocommerce_taxonomies-*/
function theplus_get_woocommerce_taxonomies() {
	$args = array(
		'public'   => true,
		'show_ui' => true
	);
	$output = 'names';		// or objects
	$operator = 'and';		// 'and' or 'or'
	$attr_list = array();
	$attr_list[''] = 'Select Attributes';

	if( function_exists('wc_get_attribute_taxonomies') ){
		$attributes =  wc_get_attribute_taxonomies();
		if ( $attributes ) {		
			foreach ( $attributes  as $attr ) {
				$attr_list['pa_'.$attr->attribute_name] = $attr->attribute_label;
			}	
		}
	}
	return $attr_list;
}
/*-woocommerce_taxonomies-*/

/*contact form 7*/
function theplus_get_contact_form_post() {
	$ContactForms = [];
	$cf7 = get_posts('post_type="wpcf7_contact_form"&numberposts=-1');

	if ( !empty($cf7) ) {
		$ContactForms['none'] = esc_html__('No Forms Selected', 'theplus');

		foreach ($cf7 as $cform) {
			$GetId = !empty($cform->ID) ? $cform->ID : '';
			$GetTitle = !empty($cform->post_title) ? $cform->post_title : '';

			if( !empty($GetId) ){
				$ContactForms[$GetId] = $GetTitle;
			}
		}
	} else {
		$ContactForms['none'] = esc_html__('No contact forms found', 'theplus');
	}

	return $ContactForms;
}
/*contact form 7*/
/*caldera forms*/
if ( !function_exists('theplus_caldera_forms') ) {
    function theplus_caldera_forms() {
       if ( class_exists( 'Caldera_Forms' ) ) {
        $caldera_forms = \Caldera_Forms_Forms::get_forms( true, true );
        $form_options  = ['0' => esc_html__( 'Select Form', 'theplus' )];
        $form          = [];
        if ( ! empty( $caldera_forms ) && ! is_wp_error( $caldera_forms ) ) {
            foreach ( $caldera_forms as $form ) {
                if ( isset($form['ID']) and isset($form['name'])) {
                    $form_options[$form['ID']] = $form['name'];
                }   
            }
        }
    } else {
        $form_options = ['0' => esc_html__( 'Form Not Found!', 'theplus' ) ];
    }
	return $form_options;
    }
}
/*caldera forms*/
/*-everest form-*/
function theplus_get_everest_form_post() {
	$EverestForm = [];
	$ev_form = get_posts('post_type="everest_form"&numberposts=-1');

	if ( !empty($ev_form) ) {
		$EverestForm['none'] = esc_html__('No Forms Selected', 'theplus');

		foreach ($ev_form as $evform) {
			$GetId = !empty($evform->ID) ? $evform->ID : '';
			$GetTitle = !empty($evform->post_title) ? $evform->post_title : '';
		
			if( !empty($GetId) ){
				$EverestForm[$GetId] = $GetTitle;
			}
		}
	} else {
		$EverestForm[0] = esc_html__('No everest forms found', 'theplus');
	}

	return $EverestForm;
}
/*-everest form-*/
/*-gravity form-*/
function theplus_gravity_form() {
    if ( class_exists( 'GFCommon' ) ) {
     	$gravity_forms = \RGFormsModel::get_forms( null, 'title' );
        $g_form_options = ['0' => esc_html__( 'Select Form', 'theplus' )];
        if ( ! empty( $gravity_forms ) && ! is_wp_error( $gravity_forms ) ) {
            foreach ( $gravity_forms as $form ) {   
                $g_form_options[ $form->id ] = $form->title;
            }
        }
    } else {
        $g_form_options = ['0' => esc_html__( 'No gravity forms found', 'theplus' ) ];
	}

    return $g_form_options;
}
/*-gravity form-*/

/*-gravity form using download monitor-*/
function theplus_gravity_form_using_dm() {
	$gf_dm = array();
	$gf_dm_form = get_posts('post_type="dlm_download"&numberposts=-1');
	$gf_dm = ['0' => esc_html__( 'Select Form', 'theplus' )];
	if ($gf_dm_form) {
		foreach ($gf_dm_form as $gfdmform) {
			$gf_dm[$gfdmform->ID] = $gfdmform->post_title;
		}
	} else {
		$gf_dm[0] = esc_html__('No forms found', 'theplus');
	}
	return $gf_dm;
}
/*-gravity form using download monitor-*/

/*-ninja form-*/
if ( !function_exists('theplus_get_ninja_form_post') ) {
    function theplus_get_ninja_form_post() {
        $options = array();
        if ( class_exists( 'Ninja_Forms' ) ) {
            $contact_forms = Ninja_Forms()->form()->get_forms();

            if ( ! empty( $contact_forms ) && ! is_wp_error( $contact_forms ) ) {

                $options[0] = esc_html__( 'Select Ninja Form', 'theplus' );

                foreach ( $contact_forms as $form ) {   
                    $options[ $form->get_id() ] = $form->get_setting( 'title' );
                }
            }
        } else {
            $options[0] = esc_html__( 'Create a Form First', 'theplus' );
        }
        return $options;
    }
}
/*-ninja form-*/
/*wpforms*/
if ( !function_exists('theplus_wpforms_forms') ) {
    function theplus_wpforms_forms() {
        $options = array();
        if ( class_exists( '\WPForms\WPForms' ) ) {

            $args = array(
                'post_type'         => 'wpforms',
                'posts_per_page'    => -1
            );

            $contact_forms = get_posts( $args );

            if ( ! empty( $contact_forms ) && ! is_wp_error( $contact_forms ) ) {
                $options[0] = esc_html__( 'Select a WPForm', 'theplus' );
                foreach ( $contact_forms as $post ) {   
                    $options[ $post->ID ] = $post->post_title;
                }
            }
        } else {
            $options[0] = esc_html__( 'Create a Form First', 'theplus' );
        }

        return $options;
    }
}
/*wpforms*/
//Navigation Get Menu
function theplus_navigation_menulist() {
    $menus = wp_get_nav_menus();
    $items = ['0' => esc_html__( 'Select Menu', 'theplus' ) ];
    foreach ( $menus as $menu ) {
        $items[ $menu->slug ] = $menu->name;
    }

    return $items;
}
class Theplus_Navigation_NavWalker extends \Walker_Nav_Menu {

	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat( "\t", $depth );
		$dropdown_menu = "\n$indent<ul role=\"menu\" class=\" dropdown-menu\">\n";
		$dropdown_menu = apply_filters( 'theplus_nav_menu_start_lvl', $dropdown_menu, $indent, $args );
		$output .= $dropdown_menu;
	}

	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
	
		if ( strcasecmp( $item->attr_title, 'divider' ) == 0 && $depth === 1 ) {
			$output .= $indent . '<li role="presentation" class="divider">';
		} else if ( strcasecmp( $item->title, 'divider') == 0 && $depth === 1 ) {
			$output .= $indent . '<li role="presentation" class="divider">';
		} else if ( strcasecmp( $item->attr_title, 'dropdown-header') == 0 && $depth === 1 ) {
			$output .= $indent . '<li role="presentation" class="dropdown-header">' . esc_attr( $item->title );
		} else if ( strcasecmp($item->attr_title, 'disabled' ) == 0 ) {
			$output .= $indent . '<li role="presentation" class="disabled"><a href="#">' . esc_attr( $item->title ) . '</a>';
		} else {

			$class_names = $value = '';

			$classes = empty( $item->classes ) ? array() : (array) $item->classes;
			$classes[] = 'animate-dropdown menu-item-' . $item->ID;

			$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args , $depth) );

			if ( $args->has_children ) {
				if ( $args->theme_location == 'departments-menu' && $depth === 0 ) {
					$class_names .= ' depth-'.$depth.' dropdown-submenu';
				} elseif ( $depth === 0 ) {
					$class_names .= ' depth-'.$depth.' dropdown';
				} else {
					$class_names .= ' depth-'.$depth.' dropdown-submenu';
				}
			}
			

			if ( in_array( 'current-menu-item', $classes ) )
				$class_names .= ' active';

			$plus_data_attr = '';
			$tp_megamenu_type = get_post_meta( $item->ID, 'menu-item-tp-megamenu-type', true );
			$tp_menu_alignment = get_post_meta( $item->ID, 'menu-item-tp-menu-alignment', true );
			if( !empty( $tp_megamenu_type ) && $tp_megamenu_type == 'default' ) {
				$tp_dropdown_width = get_post_meta( $item->ID, 'menu-item-tp-dropdown-width', true );
				if( !empty( $tp_dropdown_width ) ) {
					$class_names .= ' plus-dropdown-default';
					$plus_data_attr .= ' data-dropdown-width="'.esc_attr($tp_dropdown_width).'px"';
				}
			}else if( !empty( $tp_megamenu_type ) && $tp_megamenu_type != 'default' ) {
				$class_names .= ' plus-dropdown-'.esc_attr($tp_megamenu_type);
			}
			if( !empty( $tp_megamenu_type ) && $tp_megamenu_type == 'default' ) {
				$class_names .= ' plus-dropdown-menu-'.$tp_menu_alignment;
			}
			$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

			$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
			$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

			$output .= $indent . '<li' . $id . $value . $class_names .' '.$plus_data_attr.'>';

			$atts = array();
			$atts['title']  = ! empty( $item->title )	? $item->title	: '';
			$atts['target'] = ! empty( $item->target )	? $item->target	: '';
			$atts['rel']    = ! empty( $item->xfn )		? $item->xfn	: '';

			// If item has_children add atts to a.
			if ( $args->has_children && $depth === 0 ) {
				$atts['href']   		= $item->url ;				
				//$atts['data-toggle'] = 'dropdown';
				$atts['class']			= 'dropdown-toggle';
				$atts['aria-haspopup']	= 'true';
			} else {
				$atts['href'] = ! empty( $item->url ) ? $item->url : '';
			}

			$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

			$attributes = '';
			foreach ( $atts as $attr => $value ) {
				if ( ! empty( $value ) ) {
					$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
					$attributes .= ' ' . $attr . '="' . $value . '"';
				}
			}
			
			$icon_class_type = get_post_meta( $item->ID, 'menu-item-tp-menu-icon-type', true );
			if(!empty($icon_class_type) && $icon_class_type == 'icon_class' ){
				$icon_class = get_post_meta( $item->ID, 'menu-item-tp-icon-class', true );
				$icon = empty( $icon_class ) ? '' : '<i class="plus-nav-icon-menu ' . esc_attr( $icon_class ) . '"></i>';
			}else if(!empty($icon_class_type) && $icon_class_type == 'icon_image' ){
				$attachment_id = get_post_meta( $item->ID, 'tp-menu-icon-img', true );
				$icon_thumb = wp_get_attachment_image_src( $attachment_id, 'full' );
				$icon = empty( $icon_thumb[0] ) ? '' : '<img class="plus-nav-icon-menu icon-img" src="' . esc_attr( $icon_thumb[0] ) . '" />';
			}else{
				$icon ='';
			}
			
			$tp_text_label = get_post_meta( $item->ID, 'menu-item-tp-text-label', true );
			if(!empty($tp_text_label)){
				$tp_text_label_color = get_post_meta( $item->ID, 'menu-item-tp-label-color', true );
				$tp_text_label_bgcolor = get_post_meta( $item->ID, 'menu-item-tp-label-bg-color', true );
				$label_style = ($tp_text_label_color) ?  'color:'.esc_attr($tp_text_label_color).';' : '';
				$label_style .= ($tp_text_label_bgcolor) ?  'background-color:'.esc_attr($tp_text_label_bgcolor).';' : '';
				
				$label_style = ($label_style) ? 'style="'.$label_style.'"' : '';
				$text_label = '<span class="plus-nav-label-text" '.$label_style.'>'.esc_html($tp_text_label).'</span>';
			}else{
				$text_label ='';
			}
			
			
			
			$item_output = $args->before;
			
			if( 'plus-mega-menu' == $item->object ){
				
				$page_data = get_post($item->object_id);
				if (!empty($page_data) && isset($page_data->post_status) && strcmp($page_data->post_status,'publish')===0) {
					
					$elementor_instance = \Elementor\Plugin::instance();
					$content = $elementor_instance->frontend->get_builder_content_for_display( $item->object_id );
					$item_output .= '<div class="plus-megamenu-content">' . $content . '</div>';					
					
				}
			} else {
				if ( ! empty( $item->attr_title ) && !ctype_space($item->attr_title)) {					
					$item_output .= '<a'. $attributes .'><span class="' . esc_attr( $item->attr_title ) . '"></span>';
				} else {
					$item_output .= '<a'. $attributes .' data-text="' . esc_attr( $item->title ) . '">';
				}
				
				$item_output .= $icon;
				$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
				$item_output .= $text_label;
				
				if($args->has_children && 0 === $depth ){
				$item_output .='</a>';
				}else if($args->has_children && 1 <= $depth ){
					$item_output .='</a>';
				}else{
					$item_output .='</a>';
				}
				$item_output .= (!empty($item->description)) ? '<span class="tp-navigation-description">'.$item->description.'</span>' : '';
				$item_output .= $args->after;
			}
			$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
		}
	}

	public function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {
        if ( ! $element )
            return;
			
        $id_field = $this->db_fields['id'];

        if ( is_object( $args[0] ) )
           $args[0]->has_children = ! empty( $children_elements[ $element->$id_field ] );

        parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
    }

	public static function fallback( $args ) {
		if ( current_user_can( 'manage_options' ) ) {

			extract( $args );

			$fb_output = null;

			if ( $container ) {
				$fb_output = '<' . $container;

				if ( $container_id )
					$fb_output .= ' id="' . $container_id . '"';

				if ( $container_class )
					$fb_output .= ' class="' . $container_class . '"';

				$fb_output .= '>';
			}

			$fb_output .= '<ul';

			if ( $menu_id )
				$fb_output .= ' id="' . $menu_id . '"';

			if ( $menu_class )
				$fb_output .= ' class="' . $menu_class . '"';

			$fb_output .= '>';
			$fb_output .= '<li><a href="' . admin_url( 'nav-menus.php' ) . '">' . esc_html__( 'Add a menu', 'theplus' ) . '</a></li>';
			$fb_output .= '</ul>';

			if ( $container )
				$fb_output .= '</' . $container . '>';

			echo wp_kses_post( $fb_output );
		}
	}
	
}

// Get list of user role for protected content widget start
if( !function_exists('theplus_user_roles')) {
    function theplus_user_roles(){
        global $wp_roles;
        $all = $wp_roles->roles;
        $all_roles = array();
        if(!empty($all)){
            foreach($all as $key => $value){
                $all_roles[$key] = $all[$key]['name'];
            }
        }
        return $all_roles;
    }
}
// Get list of user role for protected content widget end
function theplus_pc_form( $settings,$widget_id ) {
    echo '<div class="theplus-password-pc-fields">';
		echo '<form class="theplus-pc-form" method="post">';		
			if(!empty($settings['form_input_text'])){
				$input_ph_text=$settings['form_input_text'];
			}else{
				$input_ph_text="Enter Password";
			}
			
			if(!empty($settings['form_button_text'])){
				$input_btn_text=$settings['form_button_text'];
			}else{
				$input_btn_text="Submit";
			}
			echo '<input type="password" name="protection_password'.esc_attr($widget_id).'" class="theplus-pc-password" placeholder="'.esc_attr($input_ph_text).'">';
			echo '<input type="submit" value="'.esc_attr($input_btn_text).'" class="theplus-pc-submit">';
		echo '</form>';
		if(!empty($settings['error_message_text'])){
			$error_msg=$settings['error_message_text'];
		}else{
			$error_msg= "Wrong Password,Please Try Again...!";
		}
		if(isset($_POST['protection_password'.$widget_id]) && sanitize_text_field($_POST['protection_password'.$widget_id]) && !isset($_SESSION['protection_password'.$widget_id])) {
			echo '<p class="theplus-pc-error-msg">'.$error_msg.'</p>';
		}
    echo '</div>';
}

/*table csv file*/
function theplus_fetch_csv( $file, $sorting ) {
	
     $column = $char_skip = '';
    $csv_rows = file( $file );
    if ( is_array( $csv_rows ) ) {
        $count = count( $csv_rows );
        for ( $i = 0; $i < $count; $i++ ) {
            $rows = $csv_rows[$i];
            $rows = trim( $rows );
            $first_character = true;
            $number_column = 0;
            $length = strlen( $rows );
            for ( $j = 0; $j < $length; $j++ ) {
                if ( $char_skip != true ) {
                    $display = true;
                    if ( $first_character == true ) {
                        if ( $rows[$j] == '"' ) {
                            $combine_char = '";';
                            $display = false;
                        }
                        else
                            $combine_char = ';';
                        $first_character = false;
                    }
                    if ( $rows[$j] == '"' ) {
                        $next_char = $rows[$j + 1];
                        if ( $next_char == '"' ) $char_skip = true;
                        elseif ( $next_char == ';' ) {
                            if ( $combine_char == '";' ) {
                                $first_character = true;
                                $display = false;
                                $char_skip = true;
                            }
                        }
                    }
                    if ( $display == true ) {
                        if ( $rows[$j] == ';' ) {
                            if ( $combine_char == ';' ) {
                                $first_character = true;
                                $display = false;
                            }
                        }
                    }
                    if ( $display == true ){ $column .= $rows[$j]; }
                    if ( $j == ( $length - 1 ) ){ $first_character = true; }
                    if ( $first_character == true ) {
                        $values[$i][$number_column] = $column;
                        $column = '';
                        $number_column++;
                    }
                }
                else
                    $char_skip = false;
            }
        }
    }
	
    $return = '<thead><tr class="plus-table-row">';
	
    foreach ( $values[0] as $value ){
		
		$return .= '<th class="sort-this plus-table-col">';
		$return .= $value;
		if ( $sorting === 'yes') {
			$return .= '<span class="plus-sort-icon"></span>';
		}
		$return .= '</th>';
	}
    $return .= '</tr></thead><tbody>';
    array_shift( $values );
    foreach ( $values as $rows ) {
        $return .= '<tr class="plus-table-row">';
        foreach ( $rows as $col ) {
			
           // $return .= '<td class="plus-table-col">' . htmlentities($col, ENT_QUOTES, "ISO-8859-1"). '</td>';
		   $return .= '<td class="plus-table-col">' . htmlentities($col). '</td>';
        }
        $return .= '</tr>';
    }
    $return .= '</tbody>';
	
    return $return;
}
/*table csv file*/

function theplus_get_style_list($max=4,$none='') {
	$options=array();
	if($none=='yes'){
		$options[ 'none' ] = 'None';
	}
	for( $i=1;$i<=$max;$i++) {
		$options[ 'style-'.$i ] = 'Style '.$i;
	}
    return $options;
}

function theplus_get_style_list_custom($max=4,$none='') {
	$options=array();
	if($none=='yes'){
		$options[ 'none' ] = 'None';
	}
	for( $i=1;$i<=$max;$i++) {
		$options[ 'style-'.$i ] = 'Style '.$i;
	}
	$options[ 'custom' ] = esc_html__('Custom Skin','theplus');
	
    return $options;
}
function theplus_get_style_list_custom_pro($max=3,$none='') {
	$options=array();
	if($none=='yes'){
		$options[ 'none' ] = 'None';
	}
	for( $i=1;$i<=$max;$i++) {
		$options[ 'style-'.$i ] = 'Style '.$i;
	}
	$options[ 'custom' ] = esc_html__('Custom Skin','theplus');
	
    return $options;
}

function theplus_get_numbers($tabs='') {
	$options = array();

	if ( !empty($tabs) && $tabs == "tabs" ){
		$options[ 'all-open' ] = 'All Close';
	}else{
		$options[ 'all-open' ] = 'All Open';
	}

	for( $i=0; $i<=20; $i++ ) {
		if( $tabs == "tabs" && $i == 0 ){
			continue;
		}

		$options[ $i ] = $i;
	}

    return $options;
}

function theplus_get_active_slide()
{
	$options=array();	
	$options[ '0' ] = 'All Equal';
	for( $i=1;$i<=20;$i++) {
        $options[ $i ] = $i;
    }
    return $options;
}
function theplus_get_gradient_styles()
{
    return array(
		'linear' => esc_html__('Linear', 'theplus'),
		'radial' => esc_html__('Radial', 'theplus'),
    );
}
function theplus_get_border_style()
{
    return array(
        'solid' => esc_html__( 'Solid', 'theplus' ),
		'dashed' => esc_html__( 'Dashed', 'theplus' ),
		'dotted' => esc_html__( 'Dotted', 'theplus' ),
		'groove' => esc_html__( 'Groove',  'theplus' ),
		'inset' => esc_html__( 'Inset','theplus' ),
		'outset' => esc_html__( 'Outset','theplus' ),
		'ridge' => esc_html__( 'Ridge', 'theplus' ),
    );
}
function theplus_get_border_style_with_none()
{
    return array(
        '' => esc_html__( 'none', 'theplus' ),
        'solid' => esc_html__( 'Solid', 'theplus' ),
		'dashed' => esc_html__( 'Dashed', 'theplus' ),
		'dotted' => esc_html__( 'Dotted', 'theplus' ),
		'groove' => esc_html__( 'Groove',  'theplus' ),
		'inset' => esc_html__( 'Inset','theplus' ),
		'outset' => esc_html__( 'Outset','theplus' ),
		'ridge' => esc_html__( 'Ridge', 'theplus' ),
    );
}
function theplus_get_list_layout_style()
{
    return array(
        'grid'  => esc_html__( 'Grid', 'theplus' ),
		'masonry' => esc_html__( 'Masonry', 'theplus' ),
		'metro' => esc_html__( 'Metro', 'theplus' ),
		'carousel' => esc_html__( 'Carousel', 'theplus' ),
    );
}
function theplus_get_columns_list()
{
    return array(
        '2' => esc_html__( 'Column 6', 'theplus' ),
		'3' => esc_html__( 'Column 4', 'theplus' ),
		'4' => esc_html__( 'Column 3', 'theplus' ),
		'5' => esc_html__( 'Column 5', 'theplus' ),
		'6' => esc_html__( 'Column 2', 'theplus' ),
		'12'  => esc_html__( 'Column 1', 'theplus' ),
	);
}

function theplus_get_columns_list_desk() {

	return array(
		'2' => esc_html__( 'Column 6', 'theplus' ),
        '5' => esc_html__( 'Column 5', 'theplus' ),
		'3' => esc_html__( 'Column 4', 'theplus' ),
		'4' => esc_html__( 'Column 3', 'theplus' ),
		'6' => esc_html__( 'Column 2', 'theplus' ),
		'12'  => esc_html__( 'Column 1', 'theplus' ),
	);
}

function theplus_get_categories() {

	$categories = get_categories();

	if ( empty( $categories ) || ! is_array( $categories ) ) {
		return array();
	}
	return wp_list_pluck( $categories, 'name', 'term_id' );
	
}
function theplus_get_tags() {

	$tags = get_tags();
	
	if ( empty( $tags ) || ! is_array( $tags ) ) {
		return array();
	}
	return wp_list_pluck( $tags, 'name', 'term_id' );
	
}
function theplus_get_testimonial_categories() {
	
		$testimonial=theplus_testimonial_post_category();
		if($testimonial!=''){
			$categories = get_categories(array('taxonomy' => $testimonial,'hide_empty' => 0));
			
			if ( empty( $categories ) || ! is_array( $categories ) ) {
				return array();
			}	
		}
	return wp_list_pluck( $categories, 'name', 'term_id' );
}
function theplus_get_client_categories() {
	
		$clients=theplus_client_post_category();
		if($clients!=''){
			$categories = get_categories(array('taxonomy' => $clients,'hide_empty' => 0));
			
			if ( empty( $categories ) || ! is_array( $categories ) ) {
				return array();
			}	
		}
	return wp_list_pluck( $categories, 'name', 'term_id' );
}
function theplus_get_team_member_categories() {
	
		$teams=theplus_team_member_post_category();
		if($teams!=''){
			$categories = get_categories(array('taxonomy' => $teams,'hide_empty' => 0));
			
			if ( empty( $categories ) || ! is_array( $categories ) ) {
				return array();
			}	
		}
	return wp_list_pluck( $categories, 'name', 'term_id' );
}
function theplus_get_woo_product_categories() {
	
		$teams='product_cat';
		if($teams!=''){
			$categories = get_categories(array('taxonomy' => $teams,'hide_empty' => 0));
			
			if ( empty( $categories ) || ! is_array( $categories ) ) {
				return array();
			}
		}
	return wp_list_pluck( $categories, 'name', 'term_id' );
}
function theplus_orderby_arr() {
	return array(
		'none'          => esc_html__( 'None', 'theplus' ),
		'ID'            => esc_html__( 'ID', 'theplus' ),
		'author'        => esc_html__( 'Author', 'theplus' ),
		'title'         => esc_html__( 'Title', 'theplus' ),
		'name'          => esc_html__( 'Name (slug)', 'theplus' ),
		'date'          => esc_html__( 'Date', 'theplus' ),
		'modified'      => esc_html__( 'Modified', 'theplus' ),
		'rand'          => esc_html__( 'Random', 'theplus' ),
		'comment_count' => esc_html__( 'Comment Count', 'theplus' ),
		'menu_order' => esc_html__( 'Default Menu Order', 'theplus' ),
	);
}
function theplus_order_arr() {

	return array(
		'DESC' => esc_html__( 'Descending', 'theplus' ),
		'ASC'  => esc_html__( 'Ascending', 'theplus' ),
	);

}
function theplus_woo_product_display() {

	return array(
		'all' => esc_html__( 'All', 'theplus' ),
		'recent'  => esc_html__( 'Recent', 'theplus' ),
		'featured'  => esc_html__( 'Featured', 'theplus' ),
		'on_sale'  => esc_html__( 'On sale', 'theplus' ),
		'top_rated'  => esc_html__( 'Top rated', 'theplus' ),
		'top_sales'  => esc_html__( 'Top sales', 'theplus' ),
		'instock'  => esc_html__( 'In Stock', 'theplus' ),
		'outofstock'  => esc_html__( 'Out of Stock', 'theplus' ),		
		//'instock_backorder'  => esc_html__( 'In Stock & Back Order', 'theplus' ),
	);

}
function theplus_post_loading_option() {

	return array(
		'none' => esc_html__( 'Select Options', 'theplus' ),
		'pagination'  => esc_html__( 'Pagination', 'theplus' ),
		'load_more'  => esc_html__( 'Load More', 'theplus' ),
		'lazy_load'  => esc_html__( 'Lazy Load', 'theplus' ),
	);

}
function theplus_metro_style_layout($columns='1',$metro_column='3',$metro_style='style-1'){
	$i=($columns!='') ? $columns : 1;
	if(!empty($metro_column)){
		//style-3
		if($metro_column=='3' && $metro_style=='style-1'){
			$i=($i<=10) ? $i : ($i%10);			
		}
		if($metro_column=='3' && $metro_style=='style-2'){
			$i=($i<=9) ? $i : ($i%9);			
		}
		if($metro_column=='3' && $metro_style=='style-3'){
			$i=($i<=15) ? $i : ($i%15);			
		}
		if($metro_column=='3' && $metro_style=='style-4'){
			$i=($i<=8) ? $i : ($i%8);			
		}
		//style-4
		if($metro_column=='4' && $metro_style=='style-1'){
			$i=($i<=12) ? $i : ($i%12);			
		}
		if($metro_column=='4' && $metro_style=='style-2'){
			$i=($i<=14) ? $i : ($i%14);			
		}
		if($metro_column=='4' && $metro_style=='style-3'){
			$i=($i<=12) ? $i : ($i%12);			
		}
		//style-5
		if($metro_column=='5' && $metro_style=='style-1'){
			$i=($i<=18) ? $i : ($i%18);			
		}
		//style-6
		if($metro_column=='6' && $metro_style=='style-1'){
			$i=($i<=16) ? $i : ($i%16);			
		}
	}
	return $i;
}
function theplus_get_layout_list_class($layout=''){
	$layout_class='';
	
	$layout_class=' list-isotope ';
	if($layout=='grid'){
		$layout_class=' list-isotope ';
	}else if($layout=='masonry'){
		$layout_class=' list-isotope ';
	}else if($layout=='metro'){
		$layout_class=' list-isotope-metro ';
	}else if($layout=='carousel'){
		$layout_class=' list-carousel-slick';
	}
	
	return $layout_class;
}

function theplus_get_layout_list_attr($layout=''){
	$layout_attr='';
	if($layout=='grid'){
		$layout_attr .=' data-layout-type="fitRows" ';
	}else if($layout=='masonry'){
		$layout_attr .=' data-layout-type="masonry" ';
	}else if($layout=='metro'){
		$layout_attr .=' data-layout-type="metro" ';		
	}
	return $layout_attr;
}

function theplus_messy_columns($columns='',$layout='',$class_css='',$desktop='',$tablet='',$mobile='',$desktop_column='',$tablet_column='',$mobile_column=''){
	$css_messy=$d_column=$t_column=$m_column='';
	if($layout=='grid' || $layout=='masonry'){
		$d_column=intval(12/$desktop_column);
		$t_column=intval(12/$tablet_column);
		$m_column=intval(12/$mobile_column);
	}
	
	if($layout=='grid'){
		$d_nth_child=$d_column.'n+'.$columns;
		$t_nth_child=$t_column.'n+'.$columns;
		$m_nth_child=$m_column.'n+'.$columns;
	}else if($layout=='masonry'){
		$d_nth_child=$columns;
		$t_nth_child=$columns;
		$m_nth_child=$columns;
	}else if($layout=='carousel'){
		$d_nth_child=$desktop_column.'n+'.$columns;
		$t_nth_child=$tablet_column.'n+'.$columns;
		$m_nth_child=$mobile_column.'n+'.$columns;
	}
	
	if($class_css!='' && $desktop!='' && ($desktop!='0px' && $desktop!='px' )){
		$css_messy .='@media (min-width:992px){.'.$class_css.' .post-inner-loop .grid-item:not(.slick-cloned):nth-child('.$d_nth_child.'){margin-top:'.$desktop.'}}';
	}
	
	if($class_css!='' && $tablet!='' && ($tablet!='0px' && $tablet!='px')){
		$css_messy .='@media (max-width:991px) and (min-width: 768px){.'.$class_css.' .post-inner-loop .grid-item:not(.slick-cloned):nth-child('.$t_nth_child.'){margin-top:'.$tablet.'}}';
	}
	
	if($class_css!='' && $mobile!='' && ($mobile!='0px' && $mobile!='px')){
		$css_messy .='@media (max-width: 767px){.'.$class_css.' .post-inner-loop .grid-item:not(.slick-cloned):nth-child('.$m_nth_child.'){margin-top:'.$mobile.'}}';
	}
	
	return $css_messy;
}

function theplus_get_position_options()
{
    return array(
        'center center' => esc_html__( 'Center Center', 'theplus' ),
		'center left' => esc_html__( 'Center Left', 'theplus' ),
		'center right' => esc_html__( 'Center Right', 'theplus' ),
		'top center' => esc_html__( 'Top Center',  'theplus' ),
		'top left' => esc_html__( 'Top Left','theplus' ),
		'top right' => esc_html__( 'Top Right','theplus' ),
		'bottom center' => esc_html__( 'Bottom Center', 'theplus' ),
		'bottom left' => esc_html__( 'Bottom Left','theplus' ),
		'bottom right' => esc_html__( 'Bottom Right','theplus' ),
    );
}
function theplus_get_image_position_options()
{
    return array(
        '' => esc_html__( 'Default','theplus' ),
		'top left' => esc_html__( 'Top Left','theplus' ),
		'top center' => esc_html__( 'Top Center','theplus' ),
		'top right' => esc_html__( 'Top Right','theplus' ),
		'center left' => esc_html__( 'Center Left','theplus' ),
		'center center' => esc_html__( 'Center Center','theplus' ),
		'center right' => esc_html__( 'Center Right', 'theplus' ),
		'bottom left' => esc_html__( 'Bottom Left', 'theplus' ),
		'bottom center' => esc_html__( 'Bottom Center','theplus' ),
		'bottom right' => esc_html__( 'Bottom Right','theplus' ),
    );
}
function theplus_get_image_attachment_options()
{
    return array(
        '' => esc_html__( 'Default', 'theplus' ),
		'scroll' => esc_html__( 'Scroll', 'theplus' ),
		'fixed' => esc_html__( 'Fixed', 'theplus' ),
    );
}
function theplus_get_content_hover_effect_options()
{
    return array(
		''   => esc_html__( 'Select Hover Effect', 'theplus' ),
		'grow'  => esc_html__( 'Grow', 'theplus' ),
		'push' => esc_html__( 'Push', 'theplus' ),
		'bounce-in' => esc_html__( 'Bounce In', 'theplus' ),
		'float' => esc_html__( 'Float', 'theplus' ),
		'wobble_horizontal' => esc_html__( 'Wobble Horizontal', 'theplus' ),
		'wobble_vertical' => esc_html__( 'Wobble Vertical', 'theplus' ),
		'float_shadow' => esc_html__( 'Float Shadow', 'theplus' ),
		'grow_shadow' => esc_html__( 'Grow Shadow	', 'theplus' ),
		'shadow_radial' => esc_html__( 'Shadow Radial', 'theplus' ),
    );
}
function theplus_get_image_reapeat_options()
{
    return array(
        '' => esc_html__( 'Default', 'theplus' ),
		'no-repeat' => esc_html__( 'No-repeat', 'theplus' ),
		'repeat' => esc_html__( 'Repeat', 'theplus' ),
		'repeat-x' => esc_html__( 'Repeat-x','theplus' ),
		'repeat-y' => esc_html__( 'Repeat-y','theplus' ),
    );
}
function theplus_get_image_size_options()
{
    return array(
        '' => esc_html__( 'Default', 'theplus' ),
		'auto' => esc_html__( 'Auto', 'theplus' ),
		'cover' => esc_html__( 'Cover', 'theplus' ),
		'contain' => esc_html__( 'Contain', 'theplus' ),
    );
}
function theplus_get_animation_options()
{
    return array(
        'no-animation' => esc_html__( 'No-animation', 'theplus' ),
		'transition.fadeIn' => esc_html__( 'FadeIn', 'theplus' ),
		'transition.flipXIn' => esc_html__( 'FlipXIn', 'theplus' ),
		'transition.flipYIn' => esc_html__( 'FlipYIn', 'theplus' ),
		'transition.flipBounceXIn' => esc_html__( 'FlipBounceXIn', 'theplus' ),
		'transition.flipBounceYIn' => esc_html__( 'FlipBounceYIn', 'theplus' ),
		'transition.swoopIn' => esc_html__( 'SwoopIn', 'theplus' ),
		'transition.whirlIn' => esc_html__( 'WhirlIn', 'theplus' ),
		'transition.shrinkIn' => esc_html__( 'ShrinkIn', 'theplus' ),
		'transition.expandIn' => esc_html__( 'ExpandIn', 'theplus' ),
		'transition.bounceIn' => esc_html__( 'BounceIn', 'theplus' ),
		'transition.bounceUpIn' => esc_html__( 'BounceUpIn', 'theplus' ),
		'transition.bounceDownIn' => esc_html__( 'BounceDownIn', 'theplus' ),
		'transition.bounceLeftIn' => esc_html__( 'BounceLeftIn', 'theplus' ),
		'transition.bounceRightIn' => esc_html__( 'BounceRightIn', 'theplus' ),
		'transition.slideUpIn' => esc_html__( 'SlideUpIn', 'theplus' ),
		'transition.slideDownIn' => esc_html__( 'SlideDownIn', 'theplus' ),
		'transition.slideLeftIn' => esc_html__( 'SlideLeftIn', 'theplus' ),
		'transition.slideRightIn' => esc_html__( 'SlideRightIn', 'theplus' ),
		'transition.slideUpBigIn' => esc_html__( 'SlideUpBigIn', 'theplus' ),
		'transition.slideDownBigIn' => esc_html__( 'SlideDownBigIn', 'theplus' ),
		'transition.slideLeftBigIn' => esc_html__( 'SlideLeftBigIn', 'theplus' ),
		'transition.slideRightBigIn' => esc_html__( 'SlideRightBigIn', 'theplus' ),
		'transition.perspectiveUpIn' => esc_html__( 'PerspectiveUpIn', 'theplus' ),
		'transition.perspectiveDownIn' => esc_html__( 'PerspectiveDownIn', 'theplus' ),
		'transition.perspectiveLeftIn' => esc_html__( 'PerspectiveLeftIn', 'theplus' ),
		'transition.perspectiveRightIn' => esc_html__( 'PerspectiveRightIn', 'theplus' ),
    );
	
}
function theplus_get_out_animation_options()
{
    return array(
        'no-animation' => esc_html__( 'No-animation', 'theplus' ),
		'transition.fadeOut' => esc_html__( 'FadeOut', 'theplus' ),
		'transition.flipXOut' => esc_html__( 'FlipXOut', 'theplus' ),
		'transition.flipYOut' => esc_html__( 'FlipYOut', 'theplus' ),
		'transition.flipBounceXOut' => esc_html__( 'FlipBounceXOut', 'theplus' ),
		'transition.flipBounceYOut' => esc_html__( 'FlipBounceYOut', 'theplus' ),
		'transition.swoopOut' => esc_html__( 'SwoopOut', 'theplus' ),
		'transition.whirlOut' => esc_html__( 'WhirlOut', 'theplus' ),
		'transition.shrinkOut' => esc_html__( 'ShrinkOut', 'theplus' ),
		'transition.expandOut' => esc_html__( 'ExpandOut', 'theplus' ),
		'transition.bounceOut' => esc_html__( 'BounceOut', 'theplus' ),
		'transition.bounceUpOut' => esc_html__( 'BounceUpOut', 'theplus' ),
		'transition.bounceDownOut' => esc_html__( 'BounceDownOut', 'theplus' ),
		'transition.bounceLeftOut' => esc_html__( 'BounceLeftOut', 'theplus' ),
		'transition.bounceRightOut' => esc_html__( 'BounceRightOut', 'theplus' ),
		'transition.slideUpOut' => esc_html__( 'SlideUpOut', 'theplus' ),
		'transition.slideDownOut' => esc_html__( 'SlideDownOut', 'theplus' ),
		'transition.slideLeftOut' => esc_html__( 'SlideLeftOut', 'theplus' ),
		'transition.slideRightOut' => esc_html__( 'SlideRightOut', 'theplus' ),
		'transition.slideUpBigOut' => esc_html__( 'SlideUpBigOut', 'theplus' ),
		'transition.slideDownBigOut' => esc_html__( 'SlideDownBigOut', 'theplus' ),
		'transition.slideLeftBigOut' => esc_html__( 'SlideLeftBigOut', 'theplus' ),
		'transition.slideRightBigOut' => esc_html__( 'SlideRightBigOut', 'theplus' ),
		'transition.perspectiveUpOut' => esc_html__( 'PerspectiveUpOut', 'theplus' ),
		'transition.perspectiveDownOut' => esc_html__( 'PerspectiveDownOut', 'theplus' ),
		'transition.perspectiveLeftOut' => esc_html__( 'PerspectiveLeftOut', 'theplus' ),
		'transition.perspectiveRightOut' => esc_html__( 'PerspectiveRightOut', 'theplus' ),
    );
	
}
function theplus_anime_animation_easing(){
	 return array(
		'linear'  => esc_html__( 'Linear', 'theplus' ),
		'easeOutQuad'  => esc_html__( 'Ease-Out Quad', 'theplus' ),
		'easeInQuad'  => esc_html__( 'Ease-In Quad', 'theplus' ),
		'easeInOutQuad'  => esc_html__( 'Ease-InOut Quad', 'theplus' ),
		
		'easeOutCubic'  => esc_html__( 'Ease-Out Cubic', 'theplus' ),					
		'easeInCubic'  => esc_html__( 'Ease-In Cubic', 'theplus' ),
		'easeInOutCubic'  => esc_html__( 'Ease-InOut Cubic', 'theplus' ),
		
		'easeOutQuart'  => esc_html__( 'Ease-Out Quart', 'theplus' ),
		'easeInQuart'  => esc_html__( 'Ease-In Quart', 'theplus' ),
		'easeInOutQuart'  => esc_html__( 'Ease-InOut Quart', 'theplus' ),
		
		'easeOutQuint'  => esc_html__( 'ease-Out Quint', 'theplus' ),					
		'easeInQuint'  => esc_html__( 'ease-In Quint', 'theplus' ),
		'easeInOutQuint'  => esc_html__( 'ease-InOut Quint', 'theplus' ),
		
		'easeOutSine'  => esc_html__( 'Ease-Out Sine', 'theplus' ),
		'easeInSine'  => esc_html__( 'Ease-In Sine', 'theplus' ),
		'easeInOutSine'  => esc_html__( 'Ease-InOut Sine', 'theplus' ),
		
		'easeOutExpo'  => esc_html__( 'Ease-Out Expo', 'theplus' ),
		'easeInExpo'  => esc_html__( 'Ease-In Expo', 'theplus' ),
		'easeInOutExpo'  => esc_html__( 'Ease-InOut Expo', 'theplus' ),
		
		'easeOutElastic'  => esc_html__( 'Ease-Out Elastic', 'theplus' ),
		'easeInElastic'  => esc_html__( 'Ease-In Elastic', 'theplus' ),
		'easeInOutElastic'  => esc_html__( 'Ease-InOut Elastic', 'theplus' ),
		
		'easeOutCirc'  => esc_html__( 'Ease-Out Circ', 'theplus' ),
		'easeInCirc'  => esc_html__( 'Ease-In Circ', 'theplus' ),
		'easeInOutCirc'  => esc_html__( 'Ease-InOut Circ', 'theplus' ),
		
		'easeOutBack'  => esc_html__( 'Ease-Out Back', 'theplus' ),
		'easeInBack'  => esc_html__( 'Ease-In Back', 'theplus' ),
		'easeInOutBack'  => esc_html__( 'Ease-InOut Back', 'theplus' ),
		
		'easeOutBounce'  => esc_html__( 'Ease-Out Bounce', 'theplus' ),
		'easeInBounce'  => esc_html__( 'Ease-In Bounce', 'theplus' ),
		'easeInOutBounce'  => esc_html__( 'Ease-InOut Bounce', 'theplus' ),
	);
}
function theplus_get_animation_easing()
{
    return array(
        '' => esc_html__( 'Default', 'theplus' ),
		'swing' => esc_html__( 'Swing', 'theplus' ),
		'easeInSine' => esc_html__( 'EaseInSine', 'theplus' ),
		'easeOutSine' => esc_html__( 'EaseOutSine', 'theplus' ),
		'easeInOutSine' => esc_html__( 'EaseInOutSine', 'theplus' ),
		'easeInQuad' => esc_html__( 'EaseInQuad', 'theplus' ),
		'easeOutQuad' => esc_html__( 'EaseOutQuad', 'theplus' ),
		'easeInOutQuad' => esc_html__( 'EaseInOutQuad', 'theplus' ),
		'easeInCubic' => esc_html__( 'EaseInCubic', 'theplus' ),
		'easeOutCubic' => esc_html__( 'EaseOutCubic', 'theplus' ),
		'easeInOutCubic' => esc_html__( 'EaseInOutCubic', 'theplus' ),
		'easeInQuart' => esc_html__( 'EaseInQuart', 'theplus' ),
		'easeOutQuart' => esc_html__( 'EaseOutQuart', 'theplus' ),
		'easeInOutQuart' => esc_html__( 'EaseInOutQuart', 'theplus' ),
		'easeInQuint' => esc_html__( 'EaseInQuint', 'theplus' ),
		'easeOutQuint' => esc_html__( 'EaseOutQuint', 'theplus' ),
		'easeInOutQuint' => esc_html__( 'EaseInOutQuint', 'theplus' ),
		'easeInExpo' => esc_html__( 'EaseInExpo', 'theplus' ),
		'easeOutExpo' => esc_html__( 'EaseOutExpo', 'theplus' ),
		'easeInOutExpo' => esc_html__( 'EaseInOutExpo', 'theplus' ),
		'easeInCirc' => esc_html__( 'EaseInCirc', 'theplus' ),
		'easeOutCirc' => esc_html__( 'EaseOutCirc', 'theplus' ),
		'easeInOutCirc' => esc_html__( 'EaseInOutCirc', 'theplus' ),
		'easeInBack' => esc_html__( 'EaseInBack', 'theplus' ),
		'easeOutBack' => esc_html__( 'EaseOutBack', 'theplus' ),
		'easeInOutBack' => esc_html__( 'EaseInOutBack', 'theplus' ),
		'easeInElastic' => esc_html__( 'EaseInElastic', 'theplus' ),
		'easeOutElastic' => esc_html__( 'EaseOutElastic', 'theplus' ),
		'easeInOutElastic' => esc_html__( 'EaseInOutElastic', 'theplus' ),
		'easeInBounce' => esc_html__( 'EaseInBounce', 'theplus' ),
		'easeOutBounce' => esc_html__( 'EaseOutBounce', 'theplus' ),
		'easeInOutBounce' => esc_html__( 'EaseInOutBounce', 'theplus' ),
    );
	
}

function theplus_get_tags_options($href=''){
    $html_tag = array(
        'h1' => esc_html__( 'H1', 'theplus' ),
		'h2' => esc_html__( 'H2', 'theplus' ),
		'h3' => esc_html__( 'H3', 'theplus' ),
		'h4' => esc_html__( 'H4', 'theplus' ),
		'h5' => esc_html__( 'H5', 'theplus' ),
		'h6' => esc_html__( 'H6', 'theplus' ),
		'div' => esc_html__( 'div', 'theplus' ),
		'p' => esc_html__( 'p', 'theplus' ),
		'span' => esc_html__( 'span', 'theplus' ),
    );

	if(!empty($href)){
		$html_tag['a'] = esc_html__( 'a', 'theplus' );
	}
	
	return $html_tag;
}

function theplus_date_format_list(){
	$date_format_list = array(		
		'F j, Y' => date( 'F j, Y' ),
		'F j, Y g:i a' => date('F j, Y g:i a'),		
		'F, Y' => date( 'F, Y' ),
		'g:i a' => date( 'g:i a' ),
		'g:i:s a' => date( 'g:i:s a' ),
		'l, F jS, Y' => date( 'l, F jS, Y' ),
		'M j, Y @ G:i' => date( 'M j, Y @ G:i' ),
		'Y/m/d' => date( 'Y/m/d' ),
		'Y/m/d \a\t g:i A' => date( 'Y/m/d \a\t g:i A' ),
		'Y/m/d \a\t g:ia' => date( 'Y/m/d \a\t g:ia' ),
		'Y/m/d g:i:s A' => date( 'Y/m/d g:i:s A' ),		
		'Y-m-d' => date( 'Y-m-d' ),
		'Y-m-d \a\t g:i A' => date( 'Y-m-d \a\t g:i A' ),
		'Y-m-d \a\t g:ia' => date( 'Y-m-d \a\t g:ia' ),
		'Y-m-d g:i:s A' => date( 'Y-m-d g:i:s A' ),		
		'custom' => __( 'Custom', 'theplus' ),
		'default' => __( 'Default', 'theplus' ),
	);
	return $date_format_list;
}

function theplus_svg_icons_list()
{
	return array(
		"app.svg"  => esc_html__("1. App", 'theplus'),
        "arrow.svg" => esc_html__("2. Arrow", 'theplus'),
        "art.svg" => esc_html__("3. Art", 'theplus'),
        "banknote.svg" => esc_html__("4. Banknote", 'theplus'),
        "building.svg" => esc_html__("5. Building", 'theplus'),
         "bulb-idea.svg" => esc_html__("6. Bulb-idea", 'theplus'),
         "calendar.svg" => esc_html__("7. Calendar", 'theplus'),
         "call.svg" => esc_html__("8. Call", 'theplus'),
         "camera.svg" => esc_html__("9. Camera", 'theplus'),
         "cart.svg" => esc_html__("10. Cart", 'theplus'),
         "cd.svg" => esc_html__("11. Cd", 'theplus'),
         "clip.svg" => esc_html__("12. Clip", 'theplus'),
         "clock.svg" => esc_html__("13. Clock", 'theplus'),
         "cloud.svg" => esc_html__("14. Cloud", 'theplus'),
         "comment.svg" => esc_html__("15. Comment", 'theplus'),
         "content-board.svg" => esc_html__("16. Content-board", 'theplus'),
         "cup.svg" => esc_html__("17. Cup", 'theplus'),
         "diamond.svg" => esc_html__("18. Diamond", 'theplus'),
         "earth.svg" => esc_html__("19. Earth", 'theplus'),
         "eye.svg" => esc_html__("20. Eye", 'theplus'),
         "finger.svg" => esc_html__("21. Finger", 'theplus'),
         "fingerprint.svg" => esc_html__("22. Fingerprint", 'theplus'),
         "food.svg" => esc_html__("23. Food", 'theplus'),
         "foundation.svg" => esc_html__("24. Foundation", 'theplus'),
         "gear.svg" => esc_html__("25. Gear", 'theplus'),
         "graphics-design.svg" => esc_html__("26. Graphics-design", 'theplus'),
         "handshake.svg" => esc_html__("27. Handshakeandshake", 'theplus'),
         "hard-disk.svg" => esc_html__("28. Hard-disk", 'theplus'),
         "heart.svg" => esc_html__("29. Heart", 'theplus'),
         "hook.svg" => esc_html__("30. Hook", 'theplus'),
         "image.svg" => esc_html__("31. Image", 'theplus'),
         "key.svg" => esc_html__("32. Key", 'theplus'),
         "laptop.svg" => esc_html__("33. Laptop", 'theplus'),
         "layers.svg" => esc_html__("34. Layers", 'theplus'),
         "list.svg" => esc_html__("35. List", 'theplus'),
         "location.svg" => esc_html__("36. Location", 'theplus'),
         "loudspeaker.svg" => esc_html__("37. Loudspeaker", 'theplus'),
         "mail.svg" => esc_html__("38. Mail", 'theplus'),
         "map.svg" => esc_html__("39. Map", 'theplus'),
         "mic.svg" => esc_html__("40. Mic", 'theplus'),
         "mind.svg" => esc_html__("41. Mind", 'theplus'),
         "mobile.svg" => esc_html__("42. Mobile", 'theplus'),
         "mobile-comment.svg" => esc_html__("43. Mobile-comment", 'theplus'),
         "music.svg" => esc_html__("44. Music", 'theplus'),
         "news.svg" => esc_html__("45. News", 'theplus'),
         "note.svg" => esc_html__("46. Note", 'theplus'),
         "offer.svg" => esc_html__("47. Offer", 'theplus'),
         "paperplane.svg" => esc_html__("48. Paperplane", 'theplus'),
         "pendrive.svg" => esc_html__("49. Pendrive", 'theplus'),
         "person.svg" => esc_html__("50. Person", 'theplus'),
         "photography.svg" => esc_html__("51. Photography", 'theplus'),
         "posisvg.svg" => esc_html__("52. Posisvg", 'theplus'),
         "recycle.svg" => esc_html__("53. Recycle", 'theplus'),
         "ruler.svg" => esc_html__("54. Ruler", 'theplus'),
         "satelite.svg" => esc_html__("55. Satelite", 'theplus'),
         "search.svg" => esc_html__("56. Search", 'theplus'),
         "secure.svg" => esc_html__("57. Secure", 'theplus'),
         "server.svg" => esc_html__("58. Server", 'theplus'),
         "setting.svg" => esc_html__("59. Setting", 'theplus'),
         "share.svg" => esc_html__("60. Share", 'theplus'),
         "smiley.svg" => esc_html__("61. Smiley", 'theplus'),
         "sound.svg" => esc_html__("62. Sound", 'theplus'),
         "stack.svg" => esc_html__("63. Stack", 'theplus'),
         "star.svg" => esc_html__("64. Star", 'theplus'),
         "study.svg" => esc_html__("65. Study", 'theplus'),
         "suitcase.svg" => esc_html__("66. Suitcase", 'theplus'),
         "tag.svg" => esc_html__("67. Tag", 'theplus'),
         "tempsvg.svg"=> esc_html__("68. Tempsvg", 'theplus'),
         "thumbsup.svg" => esc_html__("69. Thumbsup", 'theplus'),
        "tick.svg" => esc_html__("70. Tick", 'theplus'),
        "trash.svg" => esc_html__("71. Trash", 'theplus'),
        "truck.svg" => esc_html__("72. Truck", 'theplus'),
        "tv.svg" => esc_html__("73. Tv", 'theplus'),
        "user.svg" => esc_html__("74. User", 'theplus'),
        "video.svg" => esc_html__("75. Video", 'theplus'),
        "video-production.svg" => esc_html__("76. Video-production", 'theplus'),
        "wallet.svg" => esc_html__("77. Wallet", 'theplus')
		);
}

function theplus_svg_type(){
	return array(
		"delayed" => esc_html__("Delayed", 'theplus'),
        "sync" => esc_html__("Sync", 'theplus'),
        "oneByOne" => esc_html__("One-By-One", 'theplus'),
        "scenario-sync" => esc_html__("Scenario-Sync", 'theplus')
	);
}

function theplus_carousel_desktop_columns(){
	return array(
		"1" => esc_html__("Column 1", 'theplus'),
        "2" => esc_html__("Column 2", 'theplus'),
        "3" => esc_html__("Column 3", 'theplus'),
        "4" => esc_html__("Column 4", 'theplus'),
        "5" => esc_html__("Column 5", 'theplus'),
        "6" => esc_html__("Column 6", 'theplus'),
        "7" => esc_html__("Column 7", 'theplus'),
        "8" => esc_html__("Column 8", 'theplus'),
        "9" => esc_html__("Column 9", 'theplus'),
        "10" => esc_html__("Column 10", 'theplus'),
        "11" => esc_html__("Column 11", 'theplus'),
        "12" => esc_html__("Column 12", 'theplus')
	);
}
function theplus_carousel_tablet_columns(){
	return array(
		"1" => esc_html__("Column 1", 'theplus'),
        "2" => esc_html__("Column 2", 'theplus'),
        "3" => esc_html__("Column 3", 'theplus'),
        "4" => esc_html__("Column 4", 'theplus'),
        "5" => esc_html__("Column 5", 'theplus'),
        "6" => esc_html__("Column 6", 'theplus'),
        "7" => esc_html__("Column 7", 'theplus'),
        "8" => esc_html__("Column 8", 'theplus'),
	);
}
function theplus_carousel_mobile_columns(){
	return array(
		"1" => esc_html__("Column 1", 'theplus'),
        "2" => esc_html__("Column 2", 'theplus'),
        "3" => esc_html__("Column 3", 'theplus'),
        "4" => esc_html__("Column 4", 'theplus'),
        "5" => esc_html__("Column 5", 'theplus'),
        "6" => esc_html__("Column 6", 'theplus'),
	);
}
function theplus_carousel_center_effects(){
	return array(
		'none' => esc_html__( 'none', 'theplus' ),
		'scale' => esc_html__( 'Scale', 'theplus' ),
		'shadow' => esc_html__( 'Shadow', 'theplus' ),	
	);
}

function theplus_acf_repeater_field_data(){
	$field_data = [];

	$document = \Elementor\Plugin::$instance->documents->get_current();
	if(!isset($document) || is_null($document)){
		$field_data['is_repeater_field'] = false;
		return $field_data;
	}
	$doc_post_data = $document->get_post();

	if($doc_post_data->post_type == 'revision'){
		$doc_post_data = get_post($doc_post_data->post_parent);
	}

	$render_mode_type = get_post_meta($doc_post_data->ID, 'tp_render_mode_type', true);

	if($GLOBALS['post']->ID == $doc_post_data->ID && $render_mode_type == 'acf_repeater'){

		$field_data['is_repeater_field'] = true;
		$field_data['field'] = get_post_meta($doc_post_data->ID, 'tp_acf_field_name', true);

	}elseif($doc_post_data->post_type == 'elementor_library' && $render_mode_type == 'acf_repeater'){
		$field_data['is_repeater_field'] = true;
	}else{
		$field_data['is_repeater_field'] = false;
	}
	return $field_data;

}

/*Woo Thank You Page*/
if( class_exists('woocommerce') && !function_exists('theplus_get_order_id') ){
	function theplus_get_order_id(){
		global $wpdb;
		$order_status = array_keys(wc_get_order_statuses());
		$order_status = implode( "','", $order_status );	
		
		$output = $wpdb->get_col( "
			SELECT MAX(ID) FROM {$wpdb->prefix}posts
			WHERE post_type LIKE 'shop_order'
			AND post_status IN ('$order_status')
			" );
			return reset($output);
	}
}
/*Woo Thank You Page*/

function theplus_icons_mind(){
	return array(
		'' => esc_html__( 'Select Icon', 'theplus' ),
		'iconsmind-A-Z' => esc_html__( 'A-Z', 'theplus' ),
		'iconsmind-Aa' => esc_html__( 'Aa', 'theplus' ),
		'iconsmind-Add-Bag' => esc_html__( 'Add Bag', 'theplus' ),
		'iconsmind-Add-Basket' => esc_html__( 'Add Basket', 'theplus' ),
		'iconsmind-Add-Cart' => esc_html__( 'Add Cart', 'theplus' ),
		'iconsmind-Add-File' => esc_html__( 'Add File', 'theplus' ),
		'iconsmind-Add-SpaceAfterParagraph' => esc_html__( 'Add SpaceAfterParagraph', 'theplus' ),
		'iconsmind-Add-SpaceBeforeParagraph' => esc_html__( 'Add SpaceBeforeParagraph', 'theplus' ),
		'iconsmind-Add-User' => esc_html__( 'Add User', 'theplus' ),
		'iconsmind-Add-UserStar' => esc_html__( 'Add UserStar', 'theplus' ),
		'iconsmind-Add-Window' => esc_html__( 'Add Window', 'theplus' ),
		'iconsmind-Add' => esc_html__( 'Add', 'theplus' ),
		'iconsmind-Address-Book' => esc_html__( 'Address Book', 'theplus' ),
		'iconsmind-Address-Book2' => esc_html__( 'Address Book2', 'theplus' ),
		'iconsmind-Administrator' => esc_html__( 'Administrator', 'theplus' ),
		'iconsmind-Aerobics-2' => esc_html__( 'Aerobics 2', 'theplus' ),
		'iconsmind-Aerobics-3' => esc_html__( 'Aerobics 3', 'theplus' ),
		'iconsmind-Aerobics' => esc_html__( 'Aerobics', 'theplus' ),
		'iconsmind-Affiliate' => esc_html__( 'Affiliate', 'theplus' ),
		'iconsmind-Aim' => esc_html__( 'Aim', 'theplus' ),
		'iconsmind-Air-Balloon' => esc_html__( 'Air-Balloon', 'theplus' ),
		'iconsmind-Airbrush' => esc_html__( 'Airbrush', 'theplus' ),
		'iconsmind-Airship' => esc_html__( 'Airship', 'theplus' ),
		'iconsmind-Alarm-Clock' => esc_html__( 'Alarm Clock', 'theplus' ),
		'iconsmind-Alarm-Clock2' => esc_html__( 'Alarm Clock2', 'theplus' ),
		'iconsmind-Alarm' => esc_html__( 'Alarm', 'theplus' ),
		'iconsmind-Alien-2' => esc_html__( 'Alien 2', 'theplus' ),
		'iconsmind-Alien' => esc_html__( 'Alien', 'theplus' ),
		'iconsmind-Aligator' => esc_html__( 'Aligator', 'theplus' ),
		'iconsmind-Align-Center' => esc_html__( 'Align Center', 'theplus' ),
		'iconsmind-Align-JustifyAll' => esc_html__( 'Align JustifyAll', 'theplus' ),
		'iconsmind-Align-JustifyCenter' => esc_html__( 'Align JustifyCenter', 'theplus' ),
		'iconsmind-Align-JustifyLeft' => esc_html__( 'Align JustifyLeft', 'theplus' ),
		'iconsmind-Align-JustifyRight' => esc_html__( 'Align JustifyRight', 'theplus' ),
		'iconsmind-Align-Left' => esc_html__( 'Align Left', 'theplus' ),
		'iconsmind-Align-Right' => esc_html__( 'Align Right', 'theplus' ),
		'iconsmind-Alpha' => esc_html__( 'Alpha', 'theplus' ),
		'iconsmind-Ambulance' => esc_html__( 'Ambulance', 'theplus' ),
		'iconsmind-AMX' => esc_html__( 'AMX', 'theplus' ),
		'iconsmind-Anchor-2' => esc_html__( 'Anchor 2', 'theplus' ),
		'iconsmind-Anchor' => esc_html__( 'Anchor', 'theplus' ),
		'iconsmind-Android-Store' => esc_html__( 'Android Store', 'theplus' ),
		'iconsmind-Android' => esc_html__( 'Android', 'theplus' ),
		'iconsmind-Angel-Smiley' => esc_html__( 'Angel Smiley', 'theplus' ),
		'iconsmind-Angel' => esc_html__( 'Angel', 'theplus' ),
		'iconsmind-Angry' => esc_html__( 'Angry', 'theplus' ),
		'iconsmind-Apple-Bite' => esc_html__( 'Apple Bite', 'theplus' ),
		'iconsmind-Apple-Store' => esc_html__( 'Apple Store', 'theplus' ),
		'iconsmind-Apple' => esc_html__( 'Apple', 'theplus' ),
		'iconsmind-Approved-Window' => esc_html__( 'Approved Window', 'theplus' ),
		'iconsmind-Aquarius-2' => esc_html__( 'Aquarius 2', 'theplus' ),
		'iconsmind-Aquarius' => esc_html__( 'Aquarius', 'theplus' ),
		'iconsmind-Archery-2' => esc_html__( 'Archery 2', 'theplus' ),
		'iconsmind-Archery' => esc_html__( 'Archery', 'theplus' ),
		'iconsmind-Argentina' => esc_html__( 'Argentina', 'theplus' ),
		'iconsmind-Aries-2' => esc_html__( 'Aries 2', 'theplus' ),
		'iconsmind-Aries' => esc_html__( 'Aries', 'theplus' ),
		'iconsmind-Army-Key' => esc_html__( 'Army Key', 'theplus' ),
		'iconsmind-Arrow-Around' => esc_html__( 'Arrow Around', 'theplus' ),
		'iconsmind-Arrow-Back3' => esc_html__( 'Arrow Back3', 'theplus' ),
		'iconsmind-Arrow-Back' => esc_html__( 'Arrow Back', 'theplus' ),
		'iconsmind-Arrow-Back2' => esc_html__( 'Arrow Back2', 'theplus' ),
		'iconsmind-Arrow-Barrier' => esc_html__( 'Arrow Barrier', 'theplus' ),
		'iconsmind-Arrow-Circle' => esc_html__( 'Arrow Circle', 'theplus' ),
		'iconsmind-Arrow-Cross' => esc_html__( 'Arrow Cross', 'theplus' ),
		'iconsmind-Arrow-Down' => esc_html__( 'Arrow Down', 'theplus' ),
		'iconsmind-Arrow-Down2' => esc_html__( 'Arrow Down2', 'theplus' ),
		'iconsmind-Arrow-Down3' => esc_html__( 'Arrow Down3', 'theplus' ),
		'iconsmind-Arrow-DowninCircle' => esc_html__( 'Arrow DowninCircle', 'theplus' ),
		'iconsmind-Arrow-Fork' => esc_html__( 'Arrow Fork', 'theplus' ),
		'iconsmind-Arrow-Forward' => esc_html__( 'Arrow Forward', 'theplus' ),
		'iconsmind-Arrow-Forward2' => esc_html__( 'Arrow Forward2', 'theplus' ),
		'iconsmind-Arrow-From' => esc_html__( 'Arrow From', 'theplus' ),
		'iconsmind-Arrow-Inside' => esc_html__( 'Arrow Inside', 'theplus' ),
		'iconsmind-Arrow-Inside45' => esc_html__( 'Arrow Inside45', 'theplus' ),
		'iconsmind-Arrow-InsideGap' => esc_html__( 'Arrow InsideGap', 'theplus' ),
		'iconsmind-Arrow-InsideGap45' => esc_html__( 'Arrow InsideGap45', 'theplus' ),
		'iconsmind-Arrow-Into' => esc_html__( 'Arrow Into', 'theplus' ),
		'iconsmind-Arrow-Join' => esc_html__( 'Arrow Join', 'theplus' ),
		'iconsmind-Arrow-Junction' => esc_html__( 'Arrow Junction', 'theplus' ),
		'iconsmind-Arrow-Left' => esc_html__( 'Arrow Left', 'theplus' ),
		'iconsmind-Arrow-Left2' => esc_html__( 'Arrow Left2', 'theplus' ),
		'iconsmind-Arrow-LeftinCircle' => esc_html__( 'Arrow LeftinCircle', 'theplus' ),
		'iconsmind-Arrow-Loop' => esc_html__( 'Arrow Loop', 'theplus' ),
		'iconsmind-Arrow-Merge' => esc_html__( 'Arrow Merge', 'theplus' ),
		'iconsmind-Arrow-Mix' => esc_html__( 'Arrow Mix', 'theplus' ),
		'iconsmind-Arrow-Next' => esc_html__( 'Arrow Next', 'theplus' ),
		'iconsmind-Arrow-OutLeft' => esc_html__( 'Arrow OutLeft', 'theplus' ),
		'iconsmind-Arrow-OutRight' => esc_html__( 'Arrow OutRight', 'theplus' ),
		'iconsmind-Arrow-Outside' => esc_html__( 'Arrow Outside', 'theplus' ),
		'iconsmind-Arrow-Outside45' => esc_html__( 'Arrow Outside45', 'theplus' ),
		'iconsmind-Arrow-OutsideGap' => esc_html__( 'Arrow OutsideGap', 'theplus' ),
		'iconsmind-Arrow-OutsideGap45' => esc_html__( 'Arrow OutsideGap45', 'theplus' ),
		'iconsmind-Arrow-Over' => esc_html__( 'Arrow Over', 'theplus' ),
		'iconsmind-Arrow-Refresh' => esc_html__( 'Arrow Refresh', 'theplus' ),
		'iconsmind-Arrow-Refresh2' => esc_html__( 'Arrow Refresh2', 'theplus' ),
		'iconsmind-Arrow-Right' => esc_html__( 'Arrow Right', 'theplus' ),
		'iconsmind-Arrow-Right2' => esc_html__( 'Arrow Right2', 'theplus' ),
		'iconsmind-Arrow-RightinCircle' => esc_html__( 'Arrow RightinCircle', 'theplus' ),
		'iconsmind-Arrow-Shuffle' => esc_html__( 'Arrow Shuffle', 'theplus' ),
		'iconsmind-Arrow-Squiggly' => esc_html__( 'Arrow Squiggly', 'theplus' ),
		'iconsmind-Arrow-Through' => esc_html__( 'Arrow Through', 'theplus' ),
		'iconsmind-Arrow-To' => esc_html__( 'Arrow To', 'theplus' ),
		'iconsmind-Arrow-TurnLeft' => esc_html__( 'Arrow TurnLeft', 'theplus' ),
		'iconsmind-Arrow-TurnRight' => esc_html__( 'Arrow TurnRight', 'theplus' ),
		'iconsmind-Arrow-Up' => esc_html__( 'Arrow Up', 'theplus' ),
		'iconsmind--Arrow-Up2' => esc_html__( '-Arrow Up2', 'theplus' ),
		'iconsmind-Arrow-Up3' => esc_html__( 'Arrow Up3', 'theplus' ),
		'iconsmind-Arrow-UpinCircle' => esc_html__( 'Arrow UpinCircle', 'theplus' ),
		'iconsmind-Arrow-XLeft' => esc_html__( 'Arrow XLeft', 'theplus' ),
		'iconsmind-Arrow-XRight' => esc_html__( 'Arrow XRight', 'theplus' ),
		'iconsmind-Ask' => esc_html__( 'Ask', 'theplus' ),
		'iconsmind-Assistant' => esc_html__( 'Assistant', 'theplus' ),
		'iconsmind-Astronaut' => esc_html__( 'Astronaut', 'theplus' ),
		'iconsmind-At-Sign' => esc_html__( 'At Sign', 'theplus' ),
		'iconsmind-ATM' => esc_html__( 'ATM', 'theplus' ),
		'iconsmind-Atom' => esc_html__( 'Atom', 'theplus' ),
		'iconsmind-Audio' => esc_html__( 'Audio', 'theplus' ),
		'iconsmind-Auto-Flash' => esc_html__( 'Auto Flash', 'theplus' ),
		'iconsmind-Autumn' => esc_html__( 'Autumn', 'theplus' ),
		'iconsmind-Baby-Clothes' => esc_html__( 'Baby Clothes', 'theplus' ),
		'iconsmind-Baby-Clothes2' => esc_html__( 'Baby Clothes2', 'theplus' ),
		'iconsmind-Baby-Cry' => esc_html__( 'Baby Cry', 'theplus' ),
		'iconsmind-Baby' => esc_html__( 'Baby', 'theplus' ),
		'iconsmind-Back2' => esc_html__( 'Back2', 'theplus' ),
		'iconsmind-Back-Media' => esc_html__( 'Back Media', 'theplus' ),
		'iconsmind-Back-Music' => esc_html__( 'Back Music', 'theplus' ),
		'iconsmind-Back' => esc_html__( 'Back', 'theplus' ),
		'iconsmind-Background' => esc_html__( 'Background', 'theplus' ),
		'iconsmind-Bacteria' => esc_html__( 'Bacteria', 'theplus' ),
		'iconsmind-Bag-Coins' => esc_html__( 'Bag Coins', 'theplus' ),
		'iconsmind-Bag-Items' => esc_html__( 'Bag Items', 'theplus' ),
		'iconsmind-Bag-Quantity' => esc_html__( 'Bag Quantity', 'theplus' ),
		'iconsmind-Bag' => esc_html__( 'Bag', 'theplus' ),
		'iconsmind-Bakelite' => esc_html__( 'Bakelite', 'theplus' ),
		'iconsmind-Ballet-Shoes' => esc_html__( 'Ballet Shoes', 'theplus' ),
		'iconsmind-Balloon' => esc_html__( 'Balloon', 'theplus' ),
		'iconsmind-Banana' => esc_html__( 'Banana', 'theplus' ),
		'iconsmind-Band-Aid' => esc_html__( 'Band-Aid', 'theplus' ),
		'iconsmind-Bank' => esc_html__( 'Bank', 'theplus' ),
		'iconsmind-Bar-Chart' => esc_html__( 'Bar Chart', 'theplus' ),
		'iconsmind-Bar-Chart2' => esc_html__( 'Bar Chart2', 'theplus' ),
		'iconsmind-Bar-Chart3' => esc_html__( 'Bar Chart3', 'theplus' ),
		'iconsmind-Bar-Chart4' => esc_html__( 'Bar Chart4', 'theplus' ),
		'iconsmind-Bar-Chart5' => esc_html__( 'Bar Chart5', 'theplus' ),
		'iconsmind-Bar-Code' => esc_html__( 'Bar Code', 'theplus' ),
		'iconsmind-Barricade-2' => esc_html__( 'Barricade 2', 'theplus' ),
		'iconsmind-Barricade' => esc_html__( 'Barricade', 'theplus' ),
		'iconsmind-Baseball' => esc_html__( 'Baseball', 'theplus' ),
		'iconsmind-Basket-Ball' => esc_html__( 'Basket Ball', 'theplus' ),
		'iconsmind-Basket-Coins' => esc_html__( 'Basket Coins', 'theplus' ),
		'iconsmind-Basket-Items' => esc_html__( 'Basket Items', 'theplus' ),
		'iconsmind-Basket-Quantity' => esc_html__( 'Basket Quantity', 'theplus' ),
		'iconsmind-Bat-2' => esc_html__( 'Bat 2', 'theplus' ),
		'iconsmind-Bat' => esc_html__( 'Bat', 'theplus' ),
		'iconsmind-Bathrobe' => esc_html__( 'Bathrobe', 'theplus' ),
		'iconsmind-Batman-Mask' => esc_html__( 'Batman Mask', 'theplus' ),
		'iconsmind-Battery-0' => esc_html__( 'Battery 0', 'theplus' ),
		'iconsmind-Battery-25' => esc_html__( 'Battery 25', 'theplus' ),
		'iconsmind-Battery-50' => esc_html__( 'Battery 50', 'theplus' ),
		'iconsmind-Battery-75' => esc_html__( 'Battery 75', 'theplus' ),
		'iconsmind-Battery-100' => esc_html__( 'Battery 100', 'theplus' ),
		'iconsmind-Battery-Charge' => esc_html__( 'Battery Charge', 'theplus' ),
		'iconsmind-Bear' => esc_html__( 'Bear', 'theplus' ),
		'iconsmind-Beard-2' => esc_html__( 'Beard 2', 'theplus' ),
		'iconsmind-Beard-3' => esc_html__( 'Beard 3', 'theplus' ),
		'iconsmind-Beard' => esc_html__( 'Beard', 'theplus' ),
		'iconsmind-Bebo' => esc_html__( 'Bebo', 'theplus' ),
		'iconsmind-Bee' => esc_html__( 'Bee', 'theplus' ),
		'iconsmind-Beer-Glass' => esc_html__( 'Beer Glass', 'theplus' ),
		'iconsmind-Beer' => esc_html__( 'Beer', 'theplus' ),
		'iconsmind-Bell-2' => esc_html__( 'Bell 2', 'theplus' ),
		'iconsmind-Bell' => esc_html__( 'Bell', 'theplus' ),
		'iconsmind-Belt-2' => esc_html__( 'Belt 2', 'theplus' ),
		'iconsmind-Belt-3' => esc_html__( 'Belt 3', 'theplus' ),
		'iconsmind-Belt' => esc_html__( 'Belt', 'theplus' ),
		'iconsmind-Berlin-Tower' => esc_html__( 'Berlin Tower', 'theplus' ),
		'iconsmind-Beta' => esc_html__( 'Beta', 'theplus' ),
		'iconsmind-Betvibes' => esc_html__( 'Betvibes', 'theplus' ),
		'iconsmind-Bicycle-2' => esc_html__( 'Bicycle 2', 'theplus' ),
		'iconsmind-Bicycle-3' => esc_html__( 'Bicycle 3', 'theplus' ),
		'iconsmind-Bicycle' => esc_html__( 'Bicycle', 'theplus' ),
		'iconsmind-Big-Bang' => esc_html__( 'Big Bang', 'theplus' ),
		'iconsmind-Big-Data' => esc_html__( 'Big Data', 'theplus' ),
		'iconsmind-Bike-Helmet' => esc_html__( 'Bike Helmet', 'theplus' ),
		'iconsmind-Bikini' => esc_html__( 'Bikini', 'theplus' ),
		'iconsmind-Bilk-Bottle2' => esc_html__( 'Bilk Bottle2', 'theplus' ),
		'iconsmind-Billing' => esc_html__( 'Billing', 'theplus' ),
		'iconsmind-Bing' => esc_html__( 'Bing', 'theplus' ),
		'iconsmind-Binocular' => esc_html__( 'Binocular', 'theplus' ),
		'iconsmind-Bio-Hazard' => esc_html__( 'Bio Hazard', 'theplus' ),
		'iconsmind-Biotech' => esc_html__( 'Biotech', 'theplus' ),
		'iconsmind-Bird-DeliveringLetter' => esc_html__( 'Bird DeliveringLetter', 'theplus' ),
		'iconsmind-Bird' => esc_html__( 'Bird', 'theplus' ),
		'iconsmind-Birthday-Cake' => esc_html__( 'Birthday Cake', 'theplus' ),
		'iconsmind-Bisexual' => esc_html__( 'Bisexual', 'theplus' ),
		'iconsmind-Bishop' => esc_html__( 'Bishop', 'theplus' ),
		'iconsmind-Bitcoin' => esc_html__( 'Bitcoin', 'theplus' ),
		'iconsmind-Black-Cat' => esc_html__( 'Black Cat', 'theplus' ),
		'iconsmind-Blackboard' => esc_html__( 'Blackboard', 'theplus' ),
		'iconsmind-Blinklist' => esc_html__( 'Blinklist', 'theplus' ),
		'iconsmind-Block-Cloud' => esc_html__( 'Block Cloud', 'theplus' ),
		'iconsmind-Block-Window' => esc_html__( 'Block-Window', 'theplus' ),
		'iconsmind-Blogger' => esc_html__( 'Blogger', 'theplus' ),
		'iconsmind-Blood' => esc_html__( 'Blood', 'theplus' ),
		'iconsmind-Blouse' => esc_html__( 'Blouse', 'theplus' ),
		'iconsmind-Blueprint' => esc_html__( 'Blueprint', 'theplus' ),
		'iconsmind-Board' => esc_html__( 'Board', 'theplus' ),
		'iconsmind-Bodybuilding' => esc_html__( 'Bodybuilding', 'theplus' ),
		'iconsmind-Bold-Text' => esc_html__( 'Bold-Text', 'theplus' ),
		'iconsmind-Bone' => esc_html__( 'Bone', 'theplus' ),
		'iconsmind-Bones' => esc_html__( 'Bones', 'theplus' ),
		'iconsmind-Book' => esc_html__( 'Book', 'theplus' ),
		'iconsmind-Bookmark' => esc_html__( 'Bookmark', 'theplus' ),
		'iconsmind-Books-2' => esc_html__( 'Books 2', 'theplus' ),
		'iconsmind-Books' => esc_html__( 'Books', 'theplus' ),
		'iconsmind-Boom' => esc_html__( 'Boom', 'theplus' ),
		'iconsmind-Boot-2' => esc_html__( 'Boot 2', 'theplus' ),
		'iconsmind-Boot' => esc_html__( 'Boot', 'theplus' ),
		'iconsmind-Bottom-ToTop' => esc_html__( 'Bottom ToTop', 'theplus' ),
		'iconsmind-Bow-2' => esc_html__( 'Bow 2', 'theplus' ),
		'iconsmind-Bow-3' => esc_html__( 'Bow 3', 'theplus' ),
		'iconsmind-Bow-4' => esc_html__( 'Bow 4', 'theplus' ),
		'iconsmind-Bow-5' => esc_html__( 'Bow 5', 'theplus' ),
		'iconsmind-Bow-6' => esc_html__( 'Bow 6', 'theplus' ),
		'iconsmind-Bow' => esc_html__( 'Bow', 'theplus' ),
		'iconsmind-Bowling-2' => esc_html__( 'Bowling 2', 'theplus' ),
		'iconsmind-Bowling' => esc_html__( 'Bowling', 'theplus' ),
		'iconsmind-Box2' => esc_html__( 'Box2', 'theplus' ),
		'iconsmind-Box-Close' => esc_html__( 'Box Close', 'theplus' ),
		'iconsmind-Box-Full' => esc_html__( 'Box Full', 'theplus' ),
		'iconsmind-Box-Open' => esc_html__( 'Box Open', 'theplus' ),
		'iconsmind-Box-withFolders' => esc_html__( 'Box withFolders', 'theplus' ),
		'iconsmind-Box' => esc_html__( 'Box', 'theplus' ),
		'iconsmind-Boy' => esc_html__( 'Boy', 'theplus' ),
		'iconsmind-Bra' => esc_html__( 'Bra', 'theplus' ),
		'iconsmind-Brain-2' => esc_html__( 'Brain 2', 'theplus' ),
		'iconsmind-Brain-3' => esc_html__( 'Brain 3', 'theplus' ),
		'iconsmind-Brain' => esc_html__( 'Brain', 'theplus' ),
		'iconsmind-Brazil' => esc_html__( 'Brazil', 'theplus' ),
		'iconsmind-Bread-2' => esc_html__( 'Bread 2', 'theplus' ),
		'iconsmind-Bread' => esc_html__( 'Bread', 'theplus' ),
		'iconsmind-Bridge' => esc_html__( 'Bridge', 'theplus' ),
		'iconsmind-Brightkite' => esc_html__( 'Brightkite', 'theplus' ),
		'iconsmind-Broke-Link2' => esc_html__( 'Broke Link2', 'theplus' ),
		'iconsmind-Broken-Link' => esc_html__( 'Broken Link', 'theplus' ),
		'iconsmind-Broom' => esc_html__( 'Broom', 'theplus' ),
		'iconsmind-Brush' => esc_html__( 'Brush', 'theplus' ),
		'iconsmind-Bucket' => esc_html__( 'Bucket', 'theplus' ),
		'iconsmind-Bug' => esc_html__( 'Bug', 'theplus' ),
		'iconsmind-Building' => esc_html__( 'Building', 'theplus' ),
		'iconsmind-Bulleted-List' => esc_html__( 'Bulleted List', 'theplus' ),
		'iconsmind-Bus-2' => esc_html__( 'Bus 2', 'theplus' ),
		'iconsmind-Bus' => esc_html__( 'Bus', 'theplus' ),
		'iconsmind-Business-Man' => esc_html__( 'Business Man', 'theplus' ),
		'iconsmind-Business-ManWoman' => esc_html__( 'Business ManWoman', 'theplus' ),
		'iconsmind-Business-Mens' => esc_html__( 'Business Mens', 'theplus' ),
		'iconsmind-Business-Woman' => esc_html__( 'Business Woman', 'theplus' ),
		'iconsmind-Butterfly' => esc_html__( 'Butterfly', 'theplus' ),
		'iconsmind-Button' => esc_html__( 'Button', 'theplus' ),
		'iconsmind-Cable-Car' => esc_html__( 'Cable Car', 'theplus' ),
		'iconsmind-Cake' => esc_html__( 'Cake', 'theplus' ),
		'iconsmind-Calculator-2' => esc_html__( 'Calculator 2', 'theplus' ),
		'iconsmind-Calculator-3' => esc_html__( 'Calculator 3', 'theplus' ),
		'iconsmind-Calculator' => esc_html__( 'Calculator', 'theplus' ),
		'iconsmind-Calendar-2' => esc_html__( 'Calendar 2', 'theplus' ),
		'iconsmind-Calendar-3' => esc_html__( 'Calendar 3', 'theplus' ),
		'iconsmind-Calendar-4' => esc_html__( 'Calendar 4', 'theplus' ),
		'iconsmind-Calendar-Clock' => esc_html__( 'Calendar Clock', 'theplus' ),
		'iconsmind-Calendar' => esc_html__( 'Calendar', 'theplus' ),
		'iconsmind-Camel' => esc_html__( 'Camel', 'theplus' ),
		'iconsmind-Camera-2' => esc_html__( 'Camera 2', 'theplus' ),
		'iconsmind-Camera-3' => esc_html__( 'Camera 3', 'theplus' ),
		'iconsmind-Camera-4' => esc_html__( 'Camera 4', 'theplus' ),
		'iconsmind-Camera-5' => esc_html__( 'Camera 5', 'theplus' ),
		'iconsmind-Camera-Back' => esc_html__( 'Camera Back', 'theplus' ),
		'iconsmind-Camera' => esc_html__( 'Camera', 'theplus' ),
		'iconsmind-Can-2' => esc_html__( 'Can 2', 'theplus' ),
		'iconsmind-Can' => esc_html__( 'Can', 'theplus' ),
		'iconsmind-Canada' => esc_html__( 'Canada', 'theplus' ),
		'iconsmind-Cancer-2' => esc_html__( 'Cancer 2', 'theplus' ),
		'iconsmind-Cancer-3' => esc_html__( 'Cancer 3', 'theplus' ),
		'iconsmind-Cancer' => esc_html__( 'Cancer', 'theplus' ),
		'iconsmind-Candle' => esc_html__( 'Candle', 'theplus' ),
		'iconsmind-Candy-Cane' => esc_html__( 'Candy Cane', 'theplus' ),
		'iconsmind-Candy' => esc_html__( 'Candy', 'theplus' ),
		'iconsmind-Cannon' => esc_html__( 'Cannon', 'theplus' ),
		'iconsmind-Cap-2' => esc_html__( 'Cap 2', 'theplus' ),
		'iconsmind-Cap-3' => esc_html__( 'Cap 3', 'theplus' ),
		'iconsmind-Cap-Smiley' => esc_html__( 'Cap Smiley', 'theplus' ),
		'iconsmind-Cap' => esc_html__( 'Cap', 'theplus' ),
		'iconsmind-Capricorn-2' => esc_html__( 'Capricorn 2', 'theplus' ),
		'iconsmind-Capricorn' => esc_html__( 'Capricorn', 'theplus' ),
		'iconsmind-Car-2' => esc_html__( 'Car 2', 'theplus' ),
		'iconsmind-Car-3' => esc_html__( 'Car 3', 'theplus' ),
		'iconsmind-Car-Coins' => esc_html__( 'Car Coins', 'theplus' ),
		'iconsmind-Car-Items' => esc_html__( 'Car Items', 'theplus' ),
		'iconsmind-Car-Wheel' => esc_html__( 'Car Wheel', 'theplus' ),
		'iconsmind-Car' => esc_html__( 'Car', 'theplus' ),
		'iconsmind-Cardigan' => esc_html__( 'Cardigan', 'theplus' ),
		'iconsmind-Cardiovascular' => esc_html__( 'Cardiovascular', 'theplus' ),
		'iconsmind-Cart-Quantity' => esc_html__( 'Cart Quantity', 'theplus' ),
		'iconsmind-Casette-Tape' => esc_html__( 'Casette Tape', 'theplus' ),
		'iconsmind-Cash-Register' => esc_html__( 'Cash Register', 'theplus' ),
		'iconsmind-Cash-register2' => esc_html__( 'Cash register2', 'theplus' ),
		'iconsmind-Castle' => esc_html__( 'Castle', 'theplus' ),
		'iconsmind-Cat' => esc_html__( 'Cat', 'theplus' ),
		'iconsmind-Cathedral' => esc_html__( 'Cathedral', 'theplus' ),
		'iconsmind-Cauldron' => esc_html__( 'Cauldron', 'theplus' ),
		'iconsmind-CD-2' => esc_html__( 'CD-2', 'theplus' ),
		'iconsmind-CD-Cover' => esc_html__( 'CD-Cover', 'theplus' ),
		'iconsmind-CD' => esc_html__( 'CD', 'theplus' ),
		'iconsmind-Cello' => esc_html__( 'Cello', 'theplus' ),
		'iconsmind-Celsius' => esc_html__( 'Celsius', 'theplus' ),
		'iconsmind-Chacked-Flag' => esc_html__( 'Chacked Flag', 'theplus' ),
		'iconsmind-Chair' => esc_html__( 'Chair', 'theplus' ),
		'iconsmind-Charger' => esc_html__( 'Charger', 'theplus' ),
		'iconsmind-Check-2' => esc_html__( 'Check 2', 'theplus' ),
		'iconsmind-Check' => esc_html__( 'Check', 'theplus' ),
		'iconsmind-Checked-User' => esc_html__( 'Checked-User', 'theplus' ),
		'iconsmind-Checkmate' => esc_html__( 'Checkmate', 'theplus' ),
		'iconsmind-Checkout-Bag' => esc_html__( 'Checkout Bag', 'theplus' ),
		'iconsmind-Checkout-Basket' => esc_html__( 'Checkout Basket', 'theplus' ),
		'iconsmind-Checkout' => esc_html__( 'Checkout', 'theplus' ),
		'iconsmind-Cheese' => esc_html__( 'Cheese', 'theplus' ),
		'iconsmind-Cheetah' => esc_html__( 'Cheetah', 'theplus' ),
		'iconsmind-Chef-Hat' => esc_html__( 'Chef Hat', 'theplus' ),
		'iconsmind-Chef-Hat2' => esc_html__( 'Chef Hat2', 'theplus' ),
		'iconsmind-Chef' => esc_html__( 'Chef', 'theplus' ),
		'iconsmind-Chemical-2' => esc_html__( 'Chemical 2', 'theplus' ),
		'iconsmind-Chemical-3' => esc_html__( 'Chemical 3', 'theplus' ),
		'iconsmind-Chemical 4' => esc_html__( 'Chemical 4', 'theplus' ),
		'iconsmind-Chemical-5' => esc_html__( 'Chemical 5', 'theplus' ),
		'iconsmind-Chemical' => esc_html__( 'Chemical', 'theplus' ),
		'iconsmind-Chess-Board' => esc_html__( 'Chess Board', 'theplus' ),
		'iconsmind-Chess' => esc_html__( 'Chess', 'theplus' ),
		'iconsmind-Chicken' => esc_html__( 'Chicken', 'theplus' ),
		'iconsmind-Chile' => esc_html__( 'Chile', 'theplus' ),
		'iconsmind-Chimney' => esc_html__( 'Chimney', 'theplus' ),
		'iconsmind-China' => esc_html__( 'China', 'theplus' ),
		'iconsmind-Chinese-Temple' => esc_html__( 'Chinese Temple', 'theplus' ),
		'iconsmind-Chip' => esc_html__( 'Chip', 'theplus' ),
		'iconsmind-Chopsticks-2' => esc_html__( 'Chopsticks 2', 'theplus' ),
		'iconsmind-Chopsticks' => esc_html__( 'Chopsticks', 'theplus' ),
		'iconsmind-Christmas-Ball' => esc_html__( 'Christmas Ball', 'theplus' ),
		'iconsmind-Christmas-Bell' => esc_html__( 'Christmas Bell', 'theplus' ),
		'iconsmind-Christmas-Candle' => esc_html__( 'Christmas Candle', 'theplus' ),
		'iconsmind-Christmas-Hat' => esc_html__( 'Christmas Hat', 'theplus' ),
		'iconsmind-Christmas-Sleigh' => esc_html__( 'Christmas Sleigh', 'theplus' ),
		'iconsmind-Christmas-Snowman' => esc_html__( 'Christmas Snowman', 'theplus' ),
		'iconsmind-Christmas-Sock' => esc_html__( 'Christmas Sock', 'theplus' ),
		'iconsmind-Christmas-Tree' => esc_html__( 'Christmas Tree', 'theplus' ),
		'iconsmind-Christmas' => esc_html__( 'Christmas', 'theplus' ),
		'iconsmind-Chrome' => esc_html__( 'Chrome', 'theplus' ),
		'iconsmind-Chrysler-Building' => esc_html__( 'Chrysler Building', 'theplus' ),
		'iconsmind-Cinema' => esc_html__( 'Cinema', 'theplus' ),
		'iconsmind-Circular-Point' => esc_html__( 'Circular Point', 'theplus' ),
		'iconsmind-City-Hall' => esc_html__( 'City Hall', 'theplus' ),
		'iconsmind-Clamp' => esc_html__( 'Clamp', 'theplus' ),
		'iconsmind-Clapperboard-Close' => esc_html__( 'Clapperboard Close', 'theplus' ),
		'iconsmind-Clapperboard-Open' => esc_html__( 'Clapperboard Open', 'theplus' ),
		'iconsmind-Claps' => esc_html__( 'Claps', 'theplus' ),
		'iconsmind-Clef' => esc_html__( 'Clef', 'theplus' ),
		'iconsmind-Clinic' => esc_html__( 'Clinic', 'theplus' ),
		'iconsmind-Clock-2' => esc_html__( 'Clock 2', 'theplus' ),
		'iconsmind-Clock-3' => esc_html__( 'Clock 3', 'theplus' ),
		'iconsmind-Clock-4' => esc_html__( 'Clock 4', 'theplus' ),
		'iconsmind-Clock-Back' => esc_html__( 'Clock Back', 'theplus' ),
		'iconsmind-Clock-Forward' => esc_html__( 'Clock Forward', 'theplus' ),
		'iconsmind-Clock' => esc_html__( 'Clock', 'theplus' ),
		'iconsmind-Close-Window' => esc_html__( 'Close Window', 'theplus' ),
		'iconsmind-Close' => esc_html__( 'Close', 'theplus' ),
		'iconsmind-Clothing-Store' => esc_html__( 'Clothing Store', 'theplus' ),
		'iconsmind-Cloud--' => esc_html__( 'Cloud', 'theplus' ),
		'iconsmind-Cloud-' => esc_html__( 'Cloud', 'theplus' ),
		'iconsmind-Cloud-Camera' => esc_html__( 'Cloud Camera', 'theplus' ),
		'iconsmind-Cloud-Computer' => esc_html__( 'Cloud Computer', 'theplus' ),
		'iconsmind-Cloud-Email' => esc_html__( 'Cloud Email', 'theplus' ),
		'iconsmind-Cloud-Hail' => esc_html__( 'Cloud Hail', 'theplus' ),
		'iconsmind-Cloud-Laptop' => esc_html__( 'Cloud Laptop', 'theplus' ),
		'iconsmind-Cloud-Lock' => esc_html__( 'Cloud Lock', 'theplus' ),
		'iconsmind-Cloud-Moon' => esc_html__( 'Cloud Moon', 'theplus' ),
		'iconsmind-Cloud-Music' => esc_html__( 'Cloud Music', 'theplus' ),
		'iconsmind-Cloud-Picture' => esc_html__( 'Cloud-Picture', 'theplus' ),
		'iconsmind-Cloud-Rain' => esc_html__( 'Cloud Rain', 'theplus' ),
		'iconsmind-Cloud-Remove' => esc_html__( 'Cloud Remove', 'theplus' ),
		'iconsmind-Cloud-Secure' => esc_html__( 'Cloud Secure', 'theplus' ),
		'iconsmind-Cloud-Settings' => esc_html__( 'Cloud Settings', 'theplus' ),
		'iconsmind-Cloud-Smartphone' => esc_html__( 'Cloud Smartphone', 'theplus' ),
		'iconsmind-Cloud-Snow' => esc_html__( 'Cloud Snow', 'theplus' ),
		'iconsmind-Cloud-Sun' => esc_html__( 'Cloud Sun', 'theplus' ),
		'iconsmind-Cloud-Tablet' => esc_html__( 'Cloud Tablet', 'theplus' ),
		'iconsmind-Cloud-Video' => esc_html__( 'Cloud Video', 'theplus' ),
		'iconsmind-Cloud-Weather' => esc_html__( 'Cloud Weather', 'theplus' ),
		'iconsmind-Cloud' => esc_html__( 'Cloud', 'theplus' ),
		'iconsmind-Clouds-Weather' => esc_html__( 'Clouds Weather', 'theplus' ),
		'iconsmind-Clouds' => esc_html__( 'Clouds', 'theplus' ),
		'iconsmind-Clown' => esc_html__( 'Clown', 'theplus' ),
		'iconsmind-CMYK' => esc_html__( 'CMYK', 'theplus' ),
		'iconsmind-Coat' => esc_html__( 'Coat', 'theplus' ),
		'iconsmind-Cocktail' => esc_html__( 'Cocktail', 'theplus' ),
		'iconsmind-Coconut' => esc_html__( 'Coconut', 'theplus' ),
		'iconsmind-Code-Window' => esc_html__( 'Code Window', 'theplus' ),
		'iconsmind-Coding' => esc_html__( 'Coding', 'theplus' ),
		'iconsmind-Coffee-2' => esc_html__( 'Coffee 2', 'theplus' ),
		'iconsmind-Coffee-Bean' => esc_html__( 'Coffee Bean', 'theplus' ),
		'iconsmind-Coffee-Machine' => esc_html__( 'Coffee Machine', 'theplus' ),
		'iconsmind-Coffee-toGo' => esc_html__( 'Coffee toGo', 'theplus' ),
		'iconsmind-Coffee' => esc_html__( 'Coffee', 'theplus' ),
		'iconsmind-Coffin' => esc_html__( 'Coffin', 'theplus' ),
		'iconsmind-Coin' => esc_html__( 'Coin', 'theplus' ),
		'iconsmind-Coins-2' => esc_html__( 'Coins 2', 'theplus' ),
		'iconsmind-Coins-3' => esc_html__( 'Coins 3', 'theplus' ),
		'iconsmind-Coins' => esc_html__( 'Coins', 'theplus' ),
		'iconsmind-Colombia' => esc_html__( 'Colombia', 'theplus' ),
		'iconsmind-Colosseum' => esc_html__( 'Colosseum', 'theplus' ),
		'iconsmind-Column-2' => esc_html__( 'Column 2', 'theplus' ),
		'iconsmind-Column-3' => esc_html__( 'Column 3', 'theplus' ),
		'iconsmind-Column' => esc_html__( 'Column', 'theplus' ),
		'iconsmind-Comb-2' => esc_html__( 'Comb 2', 'theplus' ),
		'iconsmind-Comb' => esc_html__( 'Comb', 'theplus' ),
		'iconsmind-Communication-Tower' => esc_html__( 'Communication Tower', 'theplus' ),
		'iconsmind-Communication-Tower2' => esc_html__( 'Communication Tower2', 'theplus' ),
		'iconsmind-Compass-2' => esc_html__( 'Compass 2', 'theplus' ),
		'iconsmind-Compass-3' => esc_html__( 'Compass 3', 'theplus' ),
		'iconsmind-Compass-4' => esc_html__( 'Compass 4', 'theplus' ),
		'iconsmind-Compass-Rose' => esc_html__( 'Compass Rose', 'theplus' ),
		'iconsmind-Compass' => esc_html__( 'Compass', 'theplus' ),
		'iconsmind-Computer-2' => esc_html__( 'Computer 2', 'theplus' ),
		'iconsmind-Computer-3' => esc_html__( 'Computer 3', 'theplus' ),
		'iconsmind-Computer-Secure' => esc_html__( 'Computer Secure', 'theplus' ),
		'iconsmind-Computer' => esc_html__( 'Computer', 'theplus' ),
		'iconsmind-Conference' => esc_html__( 'Conference', 'theplus' ),
		'iconsmind-Confused' => esc_html__( 'Confused', 'theplus' ),
		'iconsmind-Conservation' => esc_html__( 'Conservation', 'theplus' ),
		'iconsmind-Consulting' => esc_html__( 'Consulting', 'theplus' ),
		'iconsmind-Contrast' => esc_html__( 'Contrast', 'theplus' ),
		'iconsmind-Control-2' => esc_html__( 'Control 2', 'theplus' ),
		'iconsmind-Control' => esc_html__( 'Control', 'theplus' ),
		'iconsmind-Cookie-Man' => esc_html__( 'Cookie Man', 'theplus' ),
		'iconsmind-Cookies' => esc_html__( 'Cookies', 'theplus' ),
		'iconsmind-Cool-Guy' => esc_html__( 'Cool Guy', 'theplus' ),
		'iconsmind-Cool' => esc_html__( 'Cool', 'theplus' ),
		'iconsmind-Copyright' => esc_html__( 'Copyright', 'theplus' ),
		'iconsmind-Costume' => esc_html__( 'Costume', 'theplus' ),
		'iconsmind-Couple-Sign' => esc_html__( 'Couple-Sign', 'theplus' ),
		'iconsmind-Cow' => esc_html__( 'Cow', 'theplus' ),
		'iconsmind-CPU' => esc_html__( 'CPU', 'theplus' ),
		'iconsmind-Crane' => esc_html__( 'Crane', 'theplus' ),
		'iconsmind-Cranium' => esc_html__( 'Cranium', 'theplus' ),
		'iconsmind-Credit-Card' => esc_html__( 'Credit Card', 'theplus' ),
		'iconsmind-Credit-Card2' => esc_html__( 'Credit Card2', 'theplus' ),
		'iconsmind-Credit-Card3' => esc_html__( 'Credit-Card3', 'theplus' ),
		'iconsmind-Cricket' => esc_html__( 'Cricket', 'theplus' ),
		'iconsmind-Criminal' => esc_html__( 'Criminal', 'theplus' ),
		'iconsmind-Croissant' => esc_html__( 'Croissant', 'theplus' ),
		'iconsmind-Crop-2' => esc_html__( 'Crop 2', 'theplus' ),
		'iconsmind-Crop-3' => esc_html__( 'Crop 3', 'theplus' ),
		'iconsmind-Crown-2' => esc_html__( 'Crown 2', 'theplus' ),
		'iconsmind-Crown' => esc_html__( 'Crown', 'theplus' ),
		'iconsmind-Crying' => esc_html__( 'Crying', 'theplus' ),
		'iconsmind-Cube-Molecule' => esc_html__( 'Cube Molecule', 'theplus' ),
		'iconsmind-Cube-Molecule2' => esc_html__( 'Cube Molecule2', 'theplus' ),
		'iconsmind-Cupcake' => esc_html__( 'Cupcake', 'theplus' ),
		'iconsmind-Cursor-Click' => esc_html__( 'Cursor Click', 'theplus' ),
		'iconsmind-Cursor-Click2' => esc_html__( 'Cursor Click2', 'theplus' ),
		'iconsmind-Cursor-Move' => esc_html__( 'Cursor Move', 'theplus' ),
		'iconsmind-Cursor-Move2' => esc_html__( 'Cursor Move2', 'theplus' ),
		'iconsmind-Cursor-Select' => esc_html__( 'Cursor Select', 'theplus' ),
		'iconsmind-Cursor' => esc_html__( 'Cursor', 'theplus' ),
		'iconsmind-D-Eyeglasses' => esc_html__( 'D-Eyeglasses', 'theplus' ),
		'iconsmind-D-Eyeglasses2' => esc_html__( 'D Eyeglasses2', 'theplus' ),
		'iconsmind-Dam' => esc_html__( 'Dam', 'theplus' ),
		'iconsmind-Danemark' => esc_html__( 'Danemark', 'theplus' ),
		'iconsmind-Danger-2' => esc_html__( 'Danger 2', 'theplus' ),
		'iconsmind-Danger' => esc_html__( 'Danger', 'theplus' ),
		'iconsmind-Dashboard' => esc_html__( 'Dashboard', 'theplus' ),
		'iconsmind-Data-Backup' => esc_html__( 'Data Backup', 'theplus' ),
		'iconsmind-Data-Block' => esc_html__( 'Data Block', 'theplus' ),
		'iconsmind-Data-Center' => esc_html__( 'Data Center', 'theplus' ),
		'iconsmind-Data-Clock' => esc_html__( 'Data Clock', 'theplus' ),
		'iconsmind-Data-Cloud' => esc_html__( 'Data Cloud', 'theplus' ),
		'iconsmind-Data-Compress' => esc_html__( 'Data Compress', 'theplus' ),
		'iconsmind-Data-Copy' => esc_html__( 'Data Copy', 'theplus' ),
		'iconsmind-Data-Download' => esc_html__( 'Data Download', 'theplus' ),
		'iconsmind-Data-Financial' => esc_html__( 'Data Financial', 'theplus' ),
		'iconsmind-Data-Key' => esc_html__( 'Data Key', 'theplus' ),
		'iconsmind-Data-Lock' => esc_html__( 'Data Lock', 'theplus' ),
		'iconsmind-Data-Network' => esc_html__( 'Data Network', 'theplus' ),
		'iconsmind-Data-Password' => esc_html__( 'Data Password', 'theplus' ),
		'iconsmind-Data-Power' => esc_html__( 'Data Power', 'theplus' ),
		'iconsmind-Data-Refresh' => esc_html__( 'Data Refresh', 'theplus' ),
		'iconsmind-Data-Save' => esc_html__( 'Data Save', 'theplus' ),
		'iconsmind-Data-Search' => esc_html__( 'Data Search', 'theplus' ),
		'iconsmind-Data-Security' => esc_html__( 'Data Security', 'theplus' ),
		'iconsmind-Data-Settings' => esc_html__( 'Data Settings', 'theplus' ),
		'iconsmind-Data-Sharing' => esc_html__( 'Data Sharing', 'theplus' ),
		'iconsmind-Data-Shield' => esc_html__( 'Data Shield', 'theplus' ),
		'iconsmind-Data-Signal' => esc_html__( 'Data Signal', 'theplus' ),
		'iconsmind-Data-Storage' => esc_html__( 'Data Storage', 'theplus' ),
		'iconsmind-Data-Stream' => esc_html__( 'Data Stream', 'theplus' ),
		'iconsmind-Data-Transfer' => esc_html__( 'Data Transfer', 'theplus' ),
		'iconsmind-Data-Unlock' => esc_html__( 'Data Unlock', 'theplus' ),
		'iconsmind-Data-Upload' => esc_html__( 'Data Upload', 'theplus' ),
		'iconsmind-Data-Yes' => esc_html__( 'Data-Yes', 'theplus' ),
		'iconsmind-Data' => esc_html__( 'Data', 'theplus' ),
		'iconsmind-David-Star' => esc_html__( 'David Star', 'theplus' ),
		'iconsmind-Daylight' => esc_html__( 'Daylight', 'theplus' ),
		'iconsmind-Death' => esc_html__( 'Death', 'theplus' ),
		'iconsmind-Debian' => esc_html__( 'Debian', 'theplus' ),
		'iconsmind-Dec' => esc_html__( 'Dec', 'theplus' ),
		'iconsmind-Decrase-Inedit' => esc_html__( 'Decrase Inedit', 'theplus' ),
		'iconsmind-Deer-2' => esc_html__( 'Deer 2', 'theplus' ),
		'iconsmind-Deer' => esc_html__( 'Deer', 'theplus' ),
		'iconsmind-Delete-File' => esc_html__( 'Delete File', 'theplus' ),
		'iconsmind-Delete-Window' => esc_html__( 'Delete Window', 'theplus' ),
		'iconsmind-Delicious' => esc_html__( 'Delicious', 'theplus' ),
		'iconsmind-Depression' => esc_html__( 'Depression', 'theplus' ),
		'iconsmind-Deviantart' => esc_html__( 'Deviantart', 'theplus' ),
		'iconsmind-Device-SyncwithCloud' => esc_html__( 'Device SyncwithCloud', 'theplus' ),
		'iconsmind-Diamond' => esc_html__( 'Diamond', 'theplus' ),
		'iconsmind-Dice-2' => esc_html__( 'Dice 2', 'theplus' ),
		'iconsmind-Dice' => esc_html__( 'Dice', 'theplus' ),
		'iconsmind-Digg' => esc_html__( 'Digg', 'theplus' ),
		'iconsmind-Digital-Drawing' => esc_html__( 'Digital Drawing', 'theplus' ),
		'iconsmind-Diigo' => esc_html__( 'Diigo', 'theplus' ),
		'iconsmind-Dinosaur' => esc_html__( 'Dinosaur', 'theplus' ),
		'iconsmind-Diploma-2' => esc_html__( 'Diploma 2', 'theplus' ),
		'iconsmind-Diploma' => esc_html__( 'Diploma', 'theplus' ),
		'iconsmind-Direction-East' => esc_html__( 'Direction East', 'theplus' ),
		'iconsmind-Direction-North' => esc_html__( 'Direction North', 'theplus' ),
		'iconsmind-Direction-South' => esc_html__( 'Direction South', 'theplus' ),
		'iconsmind-Direction-West' => esc_html__( 'Direction West', 'theplus' ),
		'iconsmind-Director' => esc_html__( 'Director', 'theplus' ),
		'iconsmind-Disk' => esc_html__( 'Disk', 'theplus' ),
		'iconsmind-Dj' => esc_html__( 'Dj', 'theplus' ),
		'iconsmind-DNA-2' => esc_html__( 'DNA 2', 'theplus' ),
		'iconsmind-DNA-Helix' => esc_html__( 'DNA Helix', 'theplus' ),
		'iconsmind-DNA' => esc_html__( 'DNA', 'theplus' ),
		'iconsmind-Doctor' => esc_html__( 'Doctor', 'theplus' ),
		'iconsmind-Dog' => esc_html__( 'Dog', 'theplus' ),
		'iconsmind-Dollar-Sign' => esc_html__( 'Dollar Sign', 'theplus' ),
		'iconsmind-Dollar-Sign2' => esc_html__( 'Dollar Sign2', 'theplus' ),
		'iconsmind-Dollar' => esc_html__( 'Dollar', 'theplus' ),
		'iconsmind-Dolphin' => esc_html__( 'Dolphin', 'theplus' ),
		'iconsmind-Domino' => esc_html__( 'Domino', 'theplus' ),
		'iconsmind-Door-Hanger' => esc_html__( 'Door Hanger', 'theplus' ),
		'iconsmind-Door' => esc_html__( 'Door', 'theplus' ),
		'iconsmind-Doplr' => esc_html__( 'Doplr', 'theplus' ),
		'iconsmind-Double-Circle' => esc_html__( 'Double Circle', 'theplus' ),
		'iconsmind-Double-Tap' => esc_html__( 'Double Tap', 'theplus' ),
		'iconsmind-Doughnut' => esc_html__( 'Doughnut', 'theplus' ),
		'iconsmind-Dove' => esc_html__( 'Dove', 'theplus' ),
		'iconsmind-Down-2' => esc_html__( 'Down 2', 'theplus' ),
		'iconsmind-Down-3' => esc_html__( 'Down 3', 'theplus' ),
		'iconsmind-Down-4' => esc_html__( 'Down 4', 'theplus' ),
		'iconsmind-Down' => esc_html__( 'Down', 'theplus' ),
		'iconsmind-Download-2' => esc_html__( 'Download 2', 'theplus' ),
		'iconsmind-Download-fromCloud' => esc_html__( 'Download fromCloud', 'theplus' ),
		'iconsmind-Download-Window' => esc_html__( 'Download Window', 'theplus' ),
		'iconsmind-Download' => esc_html__( 'Download', 'theplus' ),
		'iconsmind-Downward' => esc_html__( 'Downward', 'theplus' ),
		'iconsmind-Drag-Down' => esc_html__( 'Drag Down', 'theplus' ),
		'iconsmind-Drag-Left' => esc_html__( 'Drag Left', 'theplus' ),
		'iconsmind-Drag-Right' => esc_html__( 'Drag Right', 'theplus' ),
		'iconsmind-Drag-Up' => esc_html__( 'Drag Up', 'theplus' ),
		'iconsmind-Drag' => esc_html__( 'Drag', 'theplus' ),
		'iconsmind-Dress' => esc_html__( 'Dress', 'theplus' ),
		'iconsmind-Drill-2' => esc_html__( 'Drill 2', 'theplus' ),
		'iconsmind-Drill' => esc_html__( 'Drill', 'theplus' ),
		'iconsmind-Drop' => esc_html__( 'Drop', 'theplus' ),
		'iconsmind-Dropbox' => esc_html__( 'Dropbox', 'theplus' ),
		'iconsmind-Drum' => esc_html__( 'Drum', 'theplus' ),
		'iconsmind-Dry' => esc_html__( 'Dry', 'theplus' ),
		'iconsmind-Duck' => esc_html__( 'Duck', 'theplus' ),
		'iconsmind-Dumbbell' => esc_html__( 'Dumbbell', 'theplus' ),
		'iconsmind-Duplicate-Layer' => esc_html__( 'Duplicate Layer', 'theplus' ),
		'iconsmind-Duplicate-Window' => esc_html__( 'Duplicate Window', 'theplus' ),
		'iconsmind-DVD' => esc_html__( 'DVD', 'theplus' ),
		'iconsmind-Eagle' => esc_html__( 'Eagle', 'theplus' ),
		'iconsmind-Ear' => esc_html__( 'Ear', 'theplus' ),
		'iconsmind-Earphones-2' => esc_html__( 'Earphones 2', 'theplus' ),
		'iconsmind-Earphones' => esc_html__( 'Earphones', 'theplus' ),
		'iconsmind-Eci-Icon' => esc_html__( 'Eci Icon', 'theplus' ),
		'iconsmind-Edit-Map' => esc_html__( 'Edit Map', 'theplus' ),
		'iconsmind-Edit' => esc_html__( 'Edit', 'theplus' ),
		'iconsmind-Eggs' => esc_html__( 'Eggs', 'theplus' ),
		'iconsmind-Egypt' => esc_html__( 'Egypt', 'theplus' ),
		'iconsmind-Eifel-Tower' => esc_html__( 'Eifel Tower', 'theplus' ),
		'iconsmind-eject-2' => esc_html__( 'eject 2', 'theplus' ),
		'iconsmind-Eject' => esc_html__( 'Eject', 'theplus' ),
		'iconsmind-El-Castillo' => esc_html__( 'El Castillo', 'theplus' ),
		'iconsmind-Elbow' => esc_html__( 'Elbow', 'theplus' ),
		'iconsmind-Electric-Guitar' => esc_html__( 'Electric Guitar', 'theplus' ),
		'iconsmind-Electricity' => esc_html__( 'Electricity', 'theplus' ),
		'iconsmind-Elephant' => esc_html__( 'Elephant', 'theplus' ),
		'iconsmind-Email' => esc_html__( 'Email', 'theplus' ),
		'iconsmind-Embassy' => esc_html__( 'Embassy', 'theplus' ),
		'iconsmind-Empire-StateBuilding' => esc_html__( 'Empire StateBuilding', 'theplus' ),
		'iconsmind-Empty-Box' => esc_html__( 'Empty Box', 'theplus' ),
		'iconsmind-End2' => esc_html__( 'End2', 'theplus' ),
		'iconsmind-End-2' => esc_html__( 'End 2', 'theplus' ),
		'iconsmind-End' => esc_html__( 'End', 'theplus' ),
		'iconsmind-Endways' => esc_html__( 'Endways', 'theplus' ),
		'iconsmind-Engineering' => esc_html__( 'Engineering', 'theplus' ),
		'iconsmind-Envelope-2' => esc_html__( 'Envelope 2', 'theplus' ),
		'iconsmind-Envelope' => esc_html__( 'Envelope', 'theplus' ),
		'iconsmind-Environmental-2' => esc_html__( 'Environmental 2', 'theplus' ),
		'iconsmind-Environmental-3' => esc_html__( 'Environmental 3', 'theplus' ),
		'iconsmind-Environmental' => esc_html__( 'Environmental', 'theplus' ),
		'iconsmind-Equalizer' => esc_html__( 'Equalizer', 'theplus' ),
		'iconsmind-Eraser-2' => esc_html__( 'Eraser 2', 'theplus' ),
		'iconsmind-Eraser-3' => esc_html__( 'Eraser 3', 'theplus' ),
		'iconsmind-Eraser' => esc_html__( 'Eraser', 'theplus' ),
		'iconsmind-Error-404Window' => esc_html__( 'Error 404Window', 'theplus' ),
		'iconsmind-Euro-Sign' => esc_html__( 'Euro Sign', 'theplus' ),
		'iconsmind-Euro-Sign2' => esc_html__( 'EuroS ign2', 'theplus' ),
		'iconsmind-Euro' => esc_html__( 'Euro', 'theplus' ),
		'iconsmind-Evernote' => esc_html__( 'Evernote', 'theplus' ),
		'iconsmind-Evil' => esc_html__( 'Evil', 'theplus' ),
		'iconsmind-Explode' => esc_html__( 'Explode', 'theplus' ),
		'iconsmind-Eye-2' => esc_html__( 'Eye 2', 'theplus' ),
		'iconsmind-Eye-Blind' => esc_html__( 'Eye Blind', 'theplus' ),
		'iconsmind-Eye-Invisible' => esc_html__( 'Eye Invisible', 'theplus' ),
		'iconsmind-Eye-Scan' => esc_html__( 'Eye Scan', 'theplus' ),
		'iconsmind-Eye-Visible' => esc_html__( 'Eye Visible', 'theplus' ),
		'iconsmind-Eye' => esc_html__( 'Eye', 'theplus' ),
		'iconsmind-Eyebrow-2' => esc_html__( 'Eyebrow 2', 'theplus' ),
		'iconsmind-Eyebrow-3' => esc_html__( 'Eyebrow 3', 'theplus' ),
		'iconsmind-Eyebrow' => esc_html__( 'Eyebrow', 'theplus' ),
		'iconsmind-Eyeglasses-Smiley' => esc_html__( 'Eyeglasses Smiley', 'theplus' ),
		'iconsmind-Eyeglasses Smiley2' => esc_html__( 'Eyeglasses Smiley2', 'theplus' ),
		'iconsmind-Face-Style' => esc_html__( 'Face Style', 'theplus' ),
		'iconsmind-Face-Style2' => esc_html__( 'Face Style2', 'theplus' ),
		'iconsmind-Face-Style3' => esc_html__( 'Face Style3', 'theplus' ),
		'iconsmind-Face-Style4' => esc_html__( 'Face Style4', 'theplus' ),
		'iconsmind-Face-Style5' => esc_html__( 'Face Style5', 'theplus' ),
		'iconsmind-Face-Style6' => esc_html__( 'Face Style6', 'theplus' ),
		'iconsmind-Facebook-2' => esc_html__( 'Facebook 2', 'theplus' ),
		'iconsmind-Facebook' => esc_html__( 'Facebook', 'theplus' ),
		'iconsmind-Factory-2' => esc_html__( 'Factory 2', 'theplus' ),
		'iconsmind-Factory' => esc_html__( 'Factory', 'theplus' ),
		'iconsmind-Fahrenheit' => esc_html__( 'Fahrenheit', 'theplus' ),
		'iconsmind-Family-Sign' => esc_html__( 'Family Sign', 'theplus' ),
		'iconsmind-Fan' => esc_html__( 'Fan', 'theplus' ),
		'iconsmind-Farmer' => esc_html__( 'Farmer', 'theplus' ),
		'iconsmind-Fashion' => esc_html__( 'Fashion', 'theplus' ),
		'iconsmind-Favorite-Window' => esc_html__( 'Favorite Window', 'theplus' ),
		'iconsmind-Fax' => esc_html__( 'Fax', 'theplus' ),
		'iconsmind-Feather' => esc_html__( 'Feather', 'theplus' ),
		'iconsmind-Feedburner' => esc_html__( 'Feedburner', 'theplus' ),
		'iconsmind-Female-2' => esc_html__( 'Female-2', 'theplus' ),
		'iconsmind-Female-Sign' => esc_html__( 'Female-Sign', 'theplus' ),
		'iconsmind-Female' => esc_html__( 'Female', 'theplus' ),
		'iconsmind-File-Block' => esc_html__( 'File Block', 'theplus' ),
		'iconsmind-File-Bookmark' => esc_html__( 'File Bookmark', 'theplus' ),
		'iconsmind-File-Chart' => esc_html__( 'File Chart', 'theplus' ),
		'iconsmind-File-Clipboard' => esc_html__( 'File Clipboard', 'theplus' ),
		'iconsmind-File-ClipboardFileText' => esc_html__( 'File ClipboardFileText', 'theplus' ),
		'iconsmind-File-ClipboardTextImage' => esc_html__( 'File ClipboardTextImage', 'theplus' ),
		'iconsmind-File-Cloud' => esc_html__( 'File Cloud', 'theplus' ),
		'iconsmind-File-Copy' => esc_html__( 'File Copy', 'theplus' ),
		'iconsmind-File-Copy2' => esc_html__( 'File Copy2', 'theplus' ),
		'iconsmind-File-CSV' => esc_html__( 'File CSV', 'theplus' ),
		'iconsmind-File-Download' => esc_html__( 'File Download', 'theplus' ),
		'iconsmind-File-Edit' => esc_html__( 'File Edit', 'theplus' ),
		'iconsmind-File-Excel' => esc_html__( 'File Excel', 'theplus' ),
		'iconsmind-File-Favorite' => esc_html__( 'File Favorite', 'theplus' ),
		'iconsmind-File-Fire' => esc_html__( 'File Fire', 'theplus' ),
		'iconsmind-File-Graph' => esc_html__( 'File Graph', 'theplus' ),
		'iconsmind-File-Hide' => esc_html__( 'File Hide', 'theplus' ),
		'iconsmind-File-Horizontal' => esc_html__( 'File Horizontal', 'theplus' ),
		'iconsmind-File-HorizontalText' => esc_html__( 'File HorizontalText', 'theplus' ),
		'iconsmind-File-HTML' => esc_html__( 'File HTML', 'theplus' ),
		'iconsmind-File-JPG' => esc_html__( 'File JPG', 'theplus' ),
		'iconsmind-File-Link' => esc_html__( 'File Link', 'theplus' ),
		'iconsmind-File-Loading' => esc_html__( 'File Loading', 'theplus' ),
		'iconsmind-File-Lock' => esc_html__( 'File Lock', 'theplus' ),
		'iconsmind-File-Love' => esc_html__( 'File Love', 'theplus' ),
		'iconsmind-File-Music' => esc_html__( 'File Music', 'theplus' ),
		'iconsmind-File-Network' => esc_html__( 'File Network', 'theplus' ),
		'iconsmind-File-Pictures' => esc_html__( 'File Pictures', 'theplus' ),
		'iconsmind-File-Pie' => esc_html__( 'File Pie', 'theplus' ),
		'iconsmind-File-Presentation' => esc_html__( 'File Presentation', 'theplus' ),
		'iconsmind-File-Refresh' => esc_html__( 'File Refresh', 'theplus' ),
		'iconsmind-File-Search' => esc_html__( 'File Search', 'theplus' ),
		'iconsmind-File-Settings' => esc_html__( 'File Settings', 'theplus' ),
		'iconsmind-File-Share' => esc_html__( 'File Share', 'theplus' ),
		'iconsmind-File-TextImage' => esc_html__( 'File TextImage', 'theplus' ),
		'iconsmind-File-Trash' => esc_html__( 'File Trash', 'theplus' ),
		'iconsmind-File-TXT' => esc_html__( 'File TXT', 'theplus' ),
		'iconsmind-File-Upload' => esc_html__( 'File Upload', 'theplus' ),
		'iconsmind-File-Video' => esc_html__( 'File Video', 'theplus' ),
		'iconsmind-File-Word' => esc_html__( 'File Word', 'theplus' ),
		'iconsmind-File-Zip' => esc_html__( 'File Zip', 'theplus' ),
		'iconsmind-File' => esc_html__( 'File', 'theplus' ),
		'iconsmind-Files' => esc_html__( 'Files', 'theplus' ),
		'iconsmind-Film-Board' => esc_html__( 'Film Board', 'theplus' ),
		'iconsmind-Film-Cartridge' => esc_html__( 'Film Cartridge', 'theplus' ),
		'iconsmind-Film-Strip' => esc_html__( 'Film Strip', 'theplus' ),
		'iconsmind-Film-Video' => esc_html__( 'Film-Video', 'theplus' ),
		'iconsmind-Film' => esc_html__( 'Film', 'theplus' ),
		'iconsmind-Filter-2' => esc_html__( 'Filter 2', 'theplus' ),
		'iconsmind-Filter' => esc_html__( 'Filter', 'theplus' ),
		'iconsmind-Financial' => esc_html__( 'Financial', 'theplus' ),
		'iconsmind-Find-User' => esc_html__( 'Find User', 'theplus' ),
		'iconsmind-Finger-DragFourSides' => esc_html__( 'Finger DragFourSides', 'theplus' ),
		'iconsmind-Finger-DragTwoSides' => esc_html__( 'Finger DragTwoSides', 'theplus' ),
		'iconsmind-Finger-Print' => esc_html__( 'Finger Print', 'theplus' ),
		'iconsmind-Finger' => esc_html__( 'Finger', 'theplus' ),
		'iconsmind-Fingerprint-2' => esc_html__( 'Fingerprint 2', 'theplus' ),
		'iconsmind-Fingerprint' => esc_html__( 'Fingerprint', 'theplus' ),
		'iconsmind-Fire-Flame' => esc_html__( 'Fire-Flame', 'theplus' ),
		'iconsmind-Fire-Flame2' => esc_html__( 'Fire Flame2', 'theplus' ),
		'iconsmind-Fire-Hydrant' => esc_html__( 'Fire Hydrant', 'theplus' ),
		'iconsmind-Fire-Staion' => esc_html__( 'Fire Staion', 'theplus' ),
		'iconsmind-Firefox' => esc_html__( 'Firefox', 'theplus' ),
		'iconsmind-Firewall' => esc_html__( 'Firewall', 'theplus' ),
		'iconsmind-First-Aid' => esc_html__( 'First Aid', 'theplus' ),
		'iconsmind-First' => esc_html__( 'First', 'theplus' ),
		'iconsmind-Fish-Food' => esc_html__( 'Fish Food', 'theplus' ),
		'iconsmind-Fish' => esc_html__( 'Fish', 'theplus' ),
		'iconsmind-Fit-To' => esc_html__( 'Fit To', 'theplus' ),
		'iconsmind-Fit-To2' => esc_html__( 'Fit To2', 'theplus' ),
		'iconsmind-Five-Fingers' => esc_html__( 'Five Fingers', 'theplus' ),
		'iconsmind-Five-FingersDrag' => esc_html__( 'Five FingersDrag', 'theplus' ),
		'iconsmind-Five-FingersDrag2' => esc_html__( 'Five FingersDrag2', 'theplus' ),
		'iconsmind-Five-FingersTouch' => esc_html__( 'Five FingersTouch', 'theplus' ),
		'iconsmind-Flag-2' => esc_html__( 'Flag 2', 'theplus' ),
		'iconsmind-Flag-3' => esc_html__( 'Flag 3', 'theplus' ),
		'iconsmind-Flag-4' => esc_html__( 'Flag 4', 'theplus' ),
		'iconsmind-Flag-5' => esc_html__( 'Flag 5', 'theplus' ),
		'iconsmind-Flag-6' => esc_html__( 'Flag 6', 'theplus' ),
		'iconsmind-Flag' => esc_html__( 'Flag', 'theplus' ),
		'iconsmind-Flamingo' => esc_html__( 'Flamingo', 'theplus' ),
		'iconsmind-Flash-2' => esc_html__( 'Flash 2', 'theplus' ),
		'iconsmind-Flash-Video' => esc_html__( 'Flash Video', 'theplus' ),
		'iconsmind-Flash' => esc_html__( 'Flash', 'theplus' ),
		'iconsmind-Flashlight' => esc_html__( 'Flashlight', 'theplus' ),
		'iconsmind-Flask-2' => esc_html__( 'Flask 2', 'theplus' ),
		'iconsmind-Flask' => esc_html__( 'Flask', 'theplus' ),
		'iconsmind-Flick' => esc_html__( 'Flick', 'theplus' ),
		'iconsmind-Flickr' => esc_html__( 'Flickr', 'theplus' ),
		'iconsmind-Flowerpot' => esc_html__( 'Flowerpot', 'theplus' ),
		'iconsmind-Fluorescent' => esc_html__( 'Fluorescent', 'theplus' ),
		'iconsmind-Fog-Day' => esc_html__( 'Fog Day', 'theplus' ),
		'iconsmind-Fog-Night' => esc_html__( 'Fog Night', 'theplus' ),
		'iconsmind-Folder-Add' => esc_html__( 'Folder Add', 'theplus' ),
		'iconsmind-Folder-Archive' => esc_html__( 'Folder Archive', 'theplus' ),
		'iconsmind-Folder-Binder' => esc_html__( 'Folder Binder', 'theplus' ),
		'iconsmind-Folder-Binder2' => esc_html__( 'Folder Binder2', 'theplus' ),
		'iconsmind-Folder-Block' => esc_html__( 'Folder Block', 'theplus' ),
		'iconsmind-Folder-Bookmark' => esc_html__( 'Folder Bookmark', 'theplus' ),
		'iconsmind-Folder-Close' => esc_html__( 'Folder Close', 'theplus' ),
		'iconsmind-Folder-Cloud' => esc_html__( 'Folder Cloud', 'theplus' ),
		'iconsmind-Folder-Delete' => esc_html__( 'Folder Delete', 'theplus' ),
		'iconsmind-Folder-Download' => esc_html__( 'Folder Download', 'theplus' ),
		'iconsmind-Folder-Edit' => esc_html__( 'Folder Edit', 'theplus' ),
		'iconsmind-Folder-Favorite' => esc_html__( 'Folder Favorite', 'theplus' ),
		'iconsmind-Folder-Fire' => esc_html__( 'Folder Fire', 'theplus' ),
		'iconsmind-Folder-Hide' => esc_html__( 'Folder Hide', 'theplus' ),
		'iconsmind-Folder-Link' => esc_html__( 'Folder Link', 'theplus' ),
		'iconsmind-Folder-Loading' => esc_html__( 'Folder Loading', 'theplus' ),
		'iconsmind-Folder-Lock' => esc_html__( 'Folder Lock', 'theplus' ),
		'iconsmind-Folder-Love' => esc_html__( 'Folder Love', 'theplus' ),
		'iconsmind-Folder-Music' => esc_html__( 'Folder Music', 'theplus' ),
		'iconsmind-Folder-Network' => esc_html__( 'Folder Network', 'theplus' ),
		'iconsmind-Folder-Open' => esc_html__( 'Folder Open', 'theplus' ),
		'iconsmind-Folder-Open2' => esc_html__( 'Folder Open2', 'theplus' ),
		'iconsmind-Folder-Organizing' => esc_html__( 'Folder Organizing', 'theplus' ),
		'iconsmind-Folder-Pictures' => esc_html__( 'Folder Pictures', 'theplus' ),
		'iconsmind-Folder-Refresh' => esc_html__( 'Folder Refresh', 'theplus' ),
		'iconsmind-Folder-Remove' => esc_html__( 'Folder Remove', 'theplus' ),
		'iconsmind-Folder-Search' => esc_html__( 'Folder Search', 'theplus' ),
		'iconsmind-Folder-Settings' => esc_html__( 'Folder Settings', 'theplus' ),
		'iconsmind-Folder-Share' => esc_html__( 'Folder Share', 'theplus' ),
		'iconsmind-Folder-Trash' => esc_html__( 'Folder Trash', 'theplus' ),
		'iconsmind-Folder-Upload' => esc_html__( 'Folder Upload', 'theplus' ),
		'iconsmind-Folder-Video' => esc_html__( 'Folder Video', 'theplus' ),
		'iconsmind-Folder-WithDocument' => esc_html__( 'Folder WithDocument', 'theplus' ),
		'iconsmind-Folder-Zip' => esc_html__( 'Folder Zip', 'theplus' ),
		'iconsmind-Folder' => esc_html__( 'Folder', 'theplus' ),
		'iconsmind-Folders' => esc_html__( 'Folders', 'theplus' ),
		'iconsmind-Font-Color' => esc_html__( 'Font Color', 'theplus' ),
		'iconsmind-Font-Name' => esc_html__( 'Font Name', 'theplus' ),
		'iconsmind-Font-Size' => esc_html__( 'Font Size', 'theplus' ),
		'iconsmind-Font-Style' => esc_html__( 'Font Style', 'theplus' ),
		'iconsmind-Font-StyleSubscript' => esc_html__( 'Font StyleSubscript', 'theplus' ),
		'iconsmind-Font-StyleSuperscript' => esc_html__( 'Font StyleSuperscript', 'theplus' ),
		'iconsmind-Font-Window' => esc_html__( 'Font Window', 'theplus' ),
		'iconsmind-Foot-2' => esc_html__( 'Foot 2', 'theplus' ),
		'iconsmind-Foot' => esc_html__( 'Foot', 'theplus' ),
		'iconsmind-Football-2' => esc_html__( 'Football 2', 'theplus' ),
		'iconsmind-Football' => esc_html__( 'Football', 'theplus' ),
		'iconsmind-Footprint-2' => esc_html__( 'Footprint 2', 'theplus' ),
		'iconsmind-Footprint-3' => esc_html__( 'Footprint 3', 'theplus' ),
		'iconsmind-Footprint' => esc_html__( 'Footprint', 'theplus' ),
		'iconsmind-Forest' => esc_html__( 'Forest', 'theplus' ),
		'iconsmind-Fork' => esc_html__( 'Fork', 'theplus' ),
		'iconsmind-Formspring' => esc_html__( 'Formspring', 'theplus' ),
		'iconsmind-Formula' => esc_html__( 'Formula', 'theplus' ),
		'iconsmind-Forsquare' => esc_html__( 'Forsquare', 'theplus' ),
		'iconsmind-Forward' => esc_html__( 'Forward', 'theplus' ),
		'iconsmind-Fountain-Pen' => esc_html__( 'Fountain Pen', 'theplus' ),
		'iconsmind-Four-Fingers' => esc_html__( 'Four Fingers', 'theplus' ),
		'iconsmind-Four-FingersDrag' => esc_html__( 'Four FingersDrag', 'theplus' ),
		'iconsmind-Four-FingersDrag2' => esc_html__( 'Four FingersDrag2', 'theplus' ),
		'iconsmind-Four-FingersTouch' => esc_html__( 'Four FingersTouch', 'theplus' ),
		'iconsmind-Fox' => esc_html__( 'Fox', 'theplus' ),
		'iconsmind-Frankenstein' => esc_html__( 'Frankenstein', 'theplus' ),
		'iconsmind-French-Fries' => esc_html__( 'French Fries', 'theplus' ),
		'iconsmind-Friendfeed' => esc_html__( 'Friendfeed', 'theplus' ),
		'iconsmind-Friendster' => esc_html__( 'Friendster', 'theplus' ),
		'iconsmind-Frog' => esc_html__( 'Frog', 'theplus' ),
		'iconsmind-Fruits' => esc_html__( 'Fruits', 'theplus' ),
		'iconsmind-Fuel' => esc_html__( 'Fuel', 'theplus' ),
		'iconsmind-Full-Bag' => esc_html__( 'Full Bag', 'theplus' ),
		'iconsmind-Full-Basket' => esc_html__( 'Full Basket', 'theplus' ),
		'iconsmind-Full-Cart' => esc_html__( 'Full Cart', 'theplus' ),
		'iconsmind-Full-Moon' => esc_html__( 'Full Moon', 'theplus' ),
		'iconsmind-Full-Screen' => esc_html__( 'Full Screen', 'theplus' ),
		'iconsmind-Full-Screen2' => esc_html__( 'Full Screen2', 'theplus' ),
		'iconsmind-Full-View' => esc_html__( 'Full View', 'theplus' ),
		'iconsmind-Full-View2' => esc_html__( 'Full View2', 'theplus' ),
		'iconsmind-Full-ViewWindow' => esc_html__( 'Full ViewWindow', 'theplus' ),
		'iconsmind-Function' => esc_html__( 'Function', 'theplus' ),
		'iconsmind-Funky' => esc_html__( 'Funky', 'theplus' ),
		'iconsmind-Funny-Bicycle' => esc_html__( 'Funny Bicycle', 'theplus' ),
		'iconsmind-Furl' => esc_html__( 'Furl', 'theplus' ),
		'iconsmind-Gamepad-2' => esc_html__( 'Gamepad 2', 'theplus' ),
		'iconsmind-Gamepad' => esc_html__( 'Gamepad', 'theplus' ),
		'iconsmind-Gas-Pump' => esc_html__( 'Gas Pump', 'theplus' ),
		'iconsmind-Gaugage-2' => esc_html__( 'Gaugage 2', 'theplus' ),
		'iconsmind-Gaugage' => esc_html__( 'Gaugage', 'theplus' ),
		'iconsmind-Gay' => esc_html__( 'Gay', 'theplus' ),
		'iconsmind-Gear-2' => esc_html__( 'Gear 2', 'theplus' ),
		'iconsmind-Gear' => esc_html__( 'Gear', 'theplus' ),
		'iconsmind-Gears-2' => esc_html__( 'Gears 2', 'theplus' ),
		'iconsmind-Gears' => esc_html__( 'Gears', 'theplus' ),
		'iconsmind-Geek-2' => esc_html__( 'Geek 2', 'theplus' ),
		'iconsmind-Geek' => esc_html__( 'Geek', 'theplus' ),
		'iconsmind-Gemini-2' => esc_html__( 'Gemini-2', 'theplus' ),
		'iconsmind-Gemini' => esc_html__( 'Gemini', 'theplus' ),
		'iconsmind-Genius' => esc_html__( 'Genius', 'theplus' ),
		'iconsmind-Gentleman' => esc_html__( 'Gentleman', 'theplus' ),
		'iconsmind-Geo--' => esc_html__( 'Geo', 'theplus' ),
		'iconsmind-Geo-' => esc_html__( 'Geo', 'theplus' ),
		'iconsmind-Geo-Close' => esc_html__( 'Geo Close', 'theplus' ),
		'iconsmind-Geo-Love' => esc_html__( 'Geo Love', 'theplus' ),
		'iconsmind-Geo-Number' => esc_html__( 'Geo Number', 'theplus' ),
		'iconsmind-Geo-Star' => esc_html__( 'Geo Star', 'theplus' ),
		'iconsmind-Geo' => esc_html__( 'Geo', 'theplus' ),
		'iconsmind-Geo2--' => esc_html__( 'Geo2', 'theplus' ),
		'iconsmind-Geo2-' => esc_html__( 'Geo2', 'theplus' ),
		'iconsmind-Geo2-Close' => esc_html__( 'Geo2 Close', 'theplus' ),
		'iconsmind-Geo2-Love' => esc_html__( 'Geo2 Love', 'theplus' ),
		'iconsmind-Geo2-Number' => esc_html__( 'Geo2 Number', 'theplus' ),
		'iconsmind-Geo2-Star' => esc_html__( 'Geo2 Star', 'theplus' ),
		'iconsmind-Geo2' => esc_html__( 'Geo2', 'theplus' ),
		'iconsmind-Geo3--' => esc_html__( 'Geo3', 'theplus' ),
		'iconsmind-Geo3--' => esc_html__( 'Geo3', 'theplus' ),
		'iconsmind-Geo3-' => esc_html__( 'Geo3', 'theplus' ),
		'iconsmind-Geo3-Close' => esc_html__( 'Geo3 Close', 'theplus' ),
		'iconsmind-Geo3 Love' => esc_html__( 'Geo3 Love', 'theplus' ),
		'iconsmind-Geo3-Number' => esc_html__( 'Geo3 Number', 'theplus' ),
		'iconsmind-Geo3-Star' => esc_html__( 'Geo3 Star', 'theplus' ),
		'iconsmind-Geo3' => esc_html__( 'Geo3', 'theplus' ),
		'iconsmind-Gey' => esc_html__( 'Gey', 'theplus' ),
		'iconsmind-Gift-Box' => esc_html__( 'Gift Box', 'theplus' ),
		'iconsmind-Giraffe' => esc_html__( 'Giraffe', 'theplus' ),
		'iconsmind-Girl' => esc_html__( 'Girl', 'theplus' ),
		'iconsmind-Glass-Water' => esc_html__( 'Glass Water', 'theplus' ),
		'iconsmind-Glasses-2' => esc_html__( 'Glasses 2', 'theplus' ),
		'iconsmind-Glasses-3' => esc_html__( 'Glasses 3', 'theplus' ),
		'iconsmind-Glasses' => esc_html__( 'Glasses', 'theplus' ),
		'iconsmind-Global-Position' => esc_html__( 'Global Position', 'theplus' ),
		'iconsmind-Globe-2' => esc_html__( 'Globe 2', 'theplus' ),
		'iconsmind-Globe' => esc_html__( 'Globe', 'theplus' ),
		'iconsmind-Gloves' => esc_html__( 'Gloves', 'theplus' ),
		'iconsmind-Go-Bottom' => esc_html__( 'Go Bottom', 'theplus' ),
		'iconsmind-Go-Top' => esc_html__( 'Go Top', 'theplus' ),
		'iconsmind-Goggles' => esc_html__( 'Goggles', 'theplus' ),
		'iconsmind-Golf-2' => esc_html__( 'Golf 2', 'theplus' ),
		'iconsmind-Golf' => esc_html__( 'Golf', 'theplus' ),
		'iconsmind-Google-Buzz' => esc_html__( 'Google Buzz', 'theplus' ),
		'iconsmind-Google-Drive' => esc_html__( 'Google Drive', 'theplus' ),
		'iconsmind-Google-Play' => esc_html__( 'Google Play', 'theplus' ),
		'iconsmind-Google-Plus' => esc_html__( 'Google Plus', 'theplus' ),
		'iconsmind-Google' => esc_html__( 'Google', 'theplus' ),
		'iconsmind-Gopro' => esc_html__( 'Gopro', 'theplus' ),
		'iconsmind-Gorilla' => esc_html__( 'Gorilla', 'theplus' ),
		'iconsmind-Gowalla' => esc_html__( 'Gowalla', 'theplus' ),
		'iconsmind-Grave' => esc_html__( 'Grave', 'theplus' ),
		'iconsmind-Graveyard' => esc_html__( 'Graveyard', 'theplus' ),
		'iconsmind-Greece' => esc_html__( 'Greece', 'theplus' ),
		'iconsmind-Green-Energy' => esc_html__( 'Green Energy', 'theplus' ),
		'iconsmind-Green-House' => esc_html__( 'Green House', 'theplus' ),
		'iconsmind-Guitar' => esc_html__( 'Guitar', 'theplus' ),
		'iconsmind-Gun-2' => esc_html__( 'Gun 2', 'theplus' ),
		'iconsmind-Gun-3' => esc_html__( 'Gun 3', 'theplus' ),
		'iconsmind-Gun' => esc_html__( 'Gun', 'theplus' ),
		'iconsmind-Gymnastics' => esc_html__( 'Gymnastics', 'theplus' ),
		'iconsmind-Hair-2' => esc_html__( 'Hair 2', 'theplus' ),
		'iconsmind-Hair-3' => esc_html__( 'Hair 3', 'theplus' ),
		'iconsmind-Hair-4' => esc_html__( 'Hair 4', 'theplus' ),
		'iconsmind-Hair' => esc_html__( 'Hair', 'theplus' ),
		'iconsmind-Half-Moon' => esc_html__( 'Half Moon', 'theplus' ),
		'iconsmind-Halloween-HalfMoon' => esc_html__( 'Halloween HalfMoon', 'theplus' ),
		'iconsmind-Halloween-Moon' => esc_html__( 'Halloween Moon', 'theplus' ),
		'iconsmind-Hamburger' => esc_html__( 'Hamburger', 'theplus' ),
		'iconsmind-Hammer' => esc_html__( 'Hammer', 'theplus' ),
		'iconsmind-Hand-Touch' => esc_html__( 'Hand Touch', 'theplus' ),
		'iconsmind-Hand-Touch2' => esc_html__( 'Hand Touch2', 'theplus' ),
		'iconsmind-Hand-TouchSmartphone' => esc_html__( 'Hand TouchSmartphone', 'theplus' ),
		'iconsmind-Hand' => esc_html__( 'Hand', 'theplus' ),
		'iconsmind-Hands' => esc_html__( 'Hands', 'theplus' ),
		'iconsmind-Handshake' => esc_html__( 'Handshake', 'theplus' ),
		'iconsmind-Hanger' => esc_html__( 'Hanger', 'theplus' ),
		'iconsmind-Happy' => esc_html__( 'Happy', 'theplus' ),
		'iconsmind-Hat-2' => esc_html__( 'Hat 2', 'theplus' ),
		'iconsmind-Hat' => esc_html__( 'Hat', 'theplus' ),
		'iconsmind-Haunted-House' => esc_html__( 'Haunted House', 'theplus' ),
		'iconsmind-HD-Video' => esc_html__( 'HD Video', 'theplus' ),
		'iconsmind-HD' => esc_html__( 'HD', 'theplus' ),
		'iconsmind-HDD' => esc_html__( 'HDD', 'theplus' ),
		'iconsmind-Headphone' => esc_html__( 'Headphone', 'theplus' ),
		'iconsmind-Headphones' => esc_html__( 'Headphones', 'theplus' ),
		'iconsmind-Headset' => esc_html__( 'Headset', 'theplus' ),
		'iconsmind-Heart-2' => esc_html__( 'Heart-2', 'theplus' ),
		'iconsmind-Heart' => esc_html__( 'Heart', 'theplus' ),
		'iconsmind-Heels-2' => esc_html__( 'Heels 2', 'theplus' ),
		'iconsmind-Heels' => esc_html__( 'Heels', 'theplus' ),
		'iconsmind-Height-Window' => esc_html__( 'Height Window', 'theplus' ),
		'iconsmind-Helicopter-2' => esc_html__( 'Helicopter 2', 'theplus' ),
		'iconsmind-Helicopter' => esc_html__( 'Helicopter', 'theplus' ),
		'iconsmind-Helix-2' => esc_html__( 'Helix 2', 'theplus' ),
		'iconsmind-Hello' => esc_html__( 'Hello', 'theplus' ),
		'iconsmind-Helmet-2' => esc_html__( 'Helmet 2', 'theplus' ),
		'iconsmind-Helmet-3' => esc_html__( 'Helmet 3', 'theplus' ),
		'iconsmind-Helmet' => esc_html__( 'Helmet', 'theplus' ),
		'iconsmind-Hipo' => esc_html__( 'Hipo', 'theplus' ),
		'iconsmind-Hipster-Glasses' => esc_html__( 'Hipster Glasses', 'theplus' ),
		'iconsmind-Hipster-Glasses2' => esc_html__( 'Hipster Glasses2', 'theplus' ),
		'iconsmind-Hipster-Glasses3' => esc_html__( 'Hipster Glasses3', 'theplus' ),
		'iconsmind-Hipster-Headphones' => esc_html__( 'Hipster Headphones', 'theplus' ),
		'iconsmind-Hipster-Men' => esc_html__( 'Hipster Men', 'theplus' ),
		'iconsmind-Hipster-Men2' => esc_html__( 'Hipster Men2', 'theplus' ),
		'iconsmind-Hipster-Men3' => esc_html__( 'Hipster Men3', 'theplus' ),
		'iconsmind-Hipster-Sunglasses' => esc_html__( 'Hipster Sunglasses', 'theplus' ),
		'iconsmind-Hipster-Sunglasses2' => esc_html__( 'Hipster Sunglasses2', 'theplus' ),
		'iconsmind-Hipster-Sunglasses3' => esc_html__( 'Hipster Sunglasses3', 'theplus' ),
		'iconsmind-Hokey' => esc_html__( 'Hokey', 'theplus' ),
		'iconsmind-Holly' => esc_html__( 'Holly', 'theplus' ),
		'iconsmind-Home-2' => esc_html__( 'Home 2', 'theplus' ),
		'iconsmind-Home-3' => esc_html__( 'Home 3', 'theplus' ),
		'iconsmind-Home-4' => esc_html__( 'Home 4', 'theplus' ),
		'iconsmind-Home-5' => esc_html__( 'Home 5', 'theplus' ),
		'iconsmind-Home-Window' => esc_html__( 'Home Window', 'theplus' ),
		'iconsmind-Home' => esc_html__( 'Home', 'theplus' ),
		'iconsmind-Homosexual' => esc_html__( 'Homosexual', 'theplus' ),
		'iconsmind-Honey' => esc_html__( 'Honey', 'theplus' ),
		'iconsmind-Hong-Kong' => esc_html__( 'Hong-Kong', 'theplus' ),
		'iconsmind-Hoodie' => esc_html__( 'Hoodie', 'theplus' ),
		'iconsmind-Horror' => esc_html__( 'Horror', 'theplus' ),
		'iconsmind-Horse' => esc_html__( 'Horse', 'theplus' ),
		'iconsmind-Hospital-2' => esc_html__( 'Hospital 2', 'theplus' ),
		'iconsmind-Hospital' => esc_html__( 'Hospital', 'theplus' ),
		'iconsmind-Host' => esc_html__( 'Host', 'theplus' ),
		'iconsmind-Hot-Dog' => esc_html__( 'Hot-Dog', 'theplus' ),
		'iconsmind-Hotel' => esc_html__( 'Hotel', 'theplus' ),
		'iconsmind-Hour' => esc_html__( 'Hour', 'theplus' ),
		'iconsmind-Hub' => esc_html__( 'Hub', 'theplus' ),
		'iconsmind-Humor' => esc_html__( 'Humor', 'theplus' ),
		'iconsmind-Hurt' => esc_html__( 'Hurt', 'theplus' ),
		'iconsmind-Ice-Cream' => esc_html__( 'Ice Cream', 'theplus' ),
		'iconsmind-ICQ' => esc_html__( 'ICQ', 'theplus' ),
		'iconsmind-ID-2' => esc_html__( 'ID 2', 'theplus' ),
		'iconsmind-ID-3' => esc_html__( 'ID 3', 'theplus' ),
		'iconsmind-ID-Card' => esc_html__( 'ID Card', 'theplus' ),
		'iconsmind-Idea-2' => esc_html__( 'Idea 2', 'theplus' ),
		'iconsmind-Idea-3' => esc_html__( 'Idea 3', 'theplus' ),
		'iconsmind-Idea-4' => esc_html__( 'Idea 4', 'theplus' ),
		'iconsmind-Idea-5' => esc_html__( 'Idea-5', 'theplus' ),
		'iconsmind-Idea' => esc_html__( 'Idea', 'theplus' ),
		'iconsmind-Identification-Badge' => esc_html__( 'Identification Badge', 'theplus' ),
		'iconsmind-ImDB' => esc_html__( 'ImDB', 'theplus' ),
		'iconsmind-Inbox-Empty' => esc_html__( 'Inbox Empty', 'theplus' ),
		'iconsmind-Inbox-Forward' => esc_html__( 'Inbox Forward', 'theplus' ),
		'iconsmind-Inbox-Full' => esc_html__( 'Inbox Full', 'theplus' ),
		'iconsmind-Inbox-Into' => esc_html__( 'Inbox Into', 'theplus' ),
		'iconsmind-Inbox-Out' => esc_html__( 'Inbox-Out', 'theplus' ),
		'iconsmind-Inbox-Reply' => esc_html__( 'Inbox-Reply', 'theplus' ),
		'iconsmind-Inbox' => esc_html__( 'Inbox', 'theplus' ),
		'iconsmind-Increase-Inedit' => esc_html__( 'Increase-Inedit', 'theplus' ),
		'iconsmind-Indent-FirstLine' => esc_html__( 'Indent-FirstLine', 'theplus' ),
		'iconsmind-Indent-LeftMargin' => esc_html__( 'Indent-LeftMargin', 'theplus' ),
		'iconsmind-Indent-RightMargin' => esc_html__( 'Indent-RightMargin', 'theplus' ),
		'iconsmind-India' => esc_html__( 'India', 'theplus' ),
		'iconsmind-Info-Window' => esc_html__( 'Info-Window', 'theplus' ),
		'iconsmind-Information' => esc_html__( 'Information', 'theplus' ),
		'iconsmind-Inifity' => esc_html__( 'Inifity', 'theplus' ),
		'iconsmind-Instagram' => esc_html__( 'Instagram', 'theplus' ),
		'iconsmind-Internet-2' => esc_html__( 'Internet-2', 'theplus' ),
		'iconsmind-Internet-Explorer' => esc_html__( 'Internet Explorer', 'theplus' ),
		'iconsmind-Internet-Smiley' => esc_html__( 'Internet Smiley', 'theplus' ),
		'iconsmind-Internet' => esc_html__( 'Internet', 'theplus' ),
		'iconsmind-iOS-Apple' => esc_html__( 'iOS Apple', 'theplus' ),
		'iconsmind-Israel' => esc_html__( 'Israel', 'theplus' ),
		'iconsmind-Italic-Text' => esc_html__( 'Italic Text', 'theplus' ),
		'iconsmind-Jacket-2' => esc_html__( 'Jacket 2', 'theplus' ),
		'iconsmind-Jacket' => esc_html__( 'Jacket', 'theplus' ),
		'iconsmind-Jamaica' => esc_html__( 'Jamaica', 'theplus' ),
		'iconsmind-Japan' => esc_html__( 'Japan', 'theplus' ),
		'iconsmind-Japanese-Gate' => esc_html__( 'Japanese Gate', 'theplus' ),
		'iconsmind-Jeans' => esc_html__( 'Jeans', 'theplus' ),
		'iconsmind-Jeep-2' => esc_html__( 'Jeep 2', 'theplus' ),
		'iconsmind-Jeep' => esc_html__( 'Jeep', 'theplus' ),
		'iconsmind-Jet' => esc_html__( 'Jet', 'theplus' ),
		'iconsmind-Joystick' => esc_html__( 'Joystick', 'theplus' ),
		'iconsmind-Juice' => esc_html__( 'Juice', 'theplus' ),
		'iconsmind-Jump-Rope' => esc_html__( 'Jump-Rope', 'theplus' ),
		'iconsmind-Kangoroo' => esc_html__( 'Kangoroo', 'theplus' ),
		'iconsmind-Kenya' => esc_html__( 'Kenya', 'theplus' ),
		'iconsmind-Key-2' => esc_html__( 'Key 2', 'theplus' ),
		'iconsmind-Key-3' => esc_html__( 'Key 3', 'theplus' ),
		'iconsmind-Key-Lock' => esc_html__( 'Key Lock', 'theplus' ),
		'iconsmind-Key' => esc_html__( 'Key', 'theplus' ),
		'iconsmind-Keyboard' => esc_html__( 'Keyboard', 'theplus' ),
		'iconsmind-Keyboard3' => esc_html__( 'Keyboard3', 'theplus' ),
		'iconsmind-Keypad' => esc_html__( 'Keypad', 'theplus' ),
		'iconsmind-King-2' => esc_html__( 'King 2', 'theplus' ),
		'iconsmind-King' => esc_html__( 'King', 'theplus' ),
		'iconsmind-Kiss' => esc_html__( 'Kiss', 'theplus' ),
		'iconsmind-Knee' => esc_html__( 'Knee', 'theplus' ),
		'iconsmind-Knife-2' => esc_html__( 'Knife-2', 'theplus' ),
		'iconsmind-Knife' => esc_html__( 'Knife', 'theplus' ),
		'iconsmind-Knight' => esc_html__( 'Knight', 'theplus' ),
		'iconsmind-Koala' => esc_html__( 'Koala', 'theplus' ),
		'iconsmind-Korea' => esc_html__( 'Korea', 'theplus' ),
		'iconsmind-Lamp' => esc_html__( 'Lamp', 'theplus' ),
		'iconsmind-Landscape-2' => esc_html__( 'Landscape 2', 'theplus' ),
		'iconsmind-Landscape' => esc_html__( 'Landscape', 'theplus' ),
		'iconsmind-Lantern' => esc_html__( 'Lantern', 'theplus' ),
		'iconsmind-Laptop-2' => esc_html__( 'Laptop-2', 'theplus' ),
		'iconsmind-Laptop-3' => esc_html__( 'Laptop-3', 'theplus' ),
		'iconsmind-Laptop-Phone' => esc_html__( 'Laptop-Phone', 'theplus' ),
		'iconsmind-Laptop-Secure' => esc_html__( 'Laptop-Secure', 'theplus' ),
		'iconsmind-Laptop-Tablet' => esc_html__( 'Laptop-Tablet', 'theplus' ),
		'iconsmind-Laptop' => esc_html__( 'Laptop', 'theplus' ),
		'iconsmind-Laser' => esc_html__( 'Laser', 'theplus' ),
		'iconsmind-Last-FM' => esc_html__( 'Last-FM', 'theplus' ),
		'iconsmind-Last' => esc_html__( 'Last', 'theplus' ),
		'iconsmind-Laughing' => esc_html__( 'Laughing', 'theplus' ),
		'iconsmind-Layer-1635' => esc_html__( 'Layer-1635', 'theplus' ),
		'iconsmind-Layer-1646' => esc_html__( 'Layer-1646', 'theplus' ),
		'iconsmind-Layer-Backward' => esc_html__( 'Layer-Backward', 'theplus' ),
		'iconsmind-Layer-Forward' => esc_html__( 'Layer-Forward', 'theplus' ),
		'iconsmind-Leafs-2' => esc_html__( 'Leafs-2', 'theplus' ),
		'iconsmind-Leafs' => esc_html__( 'Leafs', 'theplus' ),
		'iconsmind-Leaning-Tower' => esc_html__( 'Leaning-Tower', 'theplus' ),
		'iconsmind-Left--Right' => esc_html__( 'Left--Right', 'theplus' ),
		'iconsmind-Left--Right3' => esc_html__( 'Left--Right3', 'theplus' ),
		'iconsmind-Left-2' => esc_html__( 'Left-2', 'theplus' ),
		'iconsmind-Left-3' => esc_html__( 'Left-3', 'theplus' ),
		'iconsmind-Left-4' => esc_html__( 'Left-4', 'theplus' ),
		'iconsmind-Left-ToRight' => esc_html__( 'Left-ToRight', 'theplus' ),
		'iconsmind-Left' => esc_html__( 'Left', 'theplus' ),
		'iconsmind-Leg-2' => esc_html__( 'Leg-2', 'theplus' ),
		'iconsmind-Leg' => esc_html__( 'Leg', 'theplus' ),
		'iconsmind-Lego' => esc_html__( 'Lego', 'theplus' ),
		'iconsmind-Lemon' => esc_html__( 'Lemon', 'theplus' ),
		'iconsmind-Len-2' => esc_html__( 'Len-2', 'theplus' ),
		'iconsmind-Len-3' => esc_html__( 'Len-3', 'theplus' ),
		'iconsmind-Len' => esc_html__( 'Len', 'theplus' ),
		'iconsmind-Leo-2' => esc_html__( 'Leo-2', 'theplus' ),
		'iconsmind-Leo' => esc_html__( 'Leo', 'theplus' ),
		'iconsmind-Leopard' => esc_html__( 'Leopard', 'theplus' ),
		'iconsmind-Lesbian' => esc_html__( 'Lesbian', 'theplus' ),
		'iconsmind-Lesbians' => esc_html__( 'Lesbians', 'theplus' ),
		'iconsmind-Letter-Close' => esc_html__( 'Letter Close', 'theplus' ),
		'iconsmind-Letter-Open' => esc_html__( 'LetterOpen', 'theplus' ),
		'iconsmind-Letter-Sent' => esc_html__( 'Letter Sent', 'theplus' ),
		'iconsmind-Libra-2' => esc_html__( 'Libra 2', 'theplus' ),
		'iconsmind-Libra' => esc_html__( 'Libra', 'theplus' ),
		'iconsmind-Library-2' => esc_html__( 'Library 2', 'theplus' ),
		'iconsmind-Library' => esc_html__( 'Library', 'theplus' ),
		'iconsmind-Life-Jacket' => esc_html__( 'Life Jacket', 'theplus' ),
		'iconsmind-Life-Safer' => esc_html__( 'Life Safer', 'theplus' ),
		'iconsmind-Light-Bulb' => esc_html__( 'Light Bulb', 'theplus' ),
		'iconsmind-Light-Bulb2' => esc_html__( 'Light Bulb2', 'theplus' ),
		'iconsmind-Light-BulbLeaf' => esc_html__( 'Light BulbLeaf', 'theplus' ),
		'iconsmind-Lighthouse' => esc_html__( 'Lighthouse', 'theplus' ),
		'iconsmind-Like-2' => esc_html__( 'Like-2', 'theplus' ),
		'iconsmind-Like' => esc_html__( 'Like', 'theplus' ),
		'iconsmind-Line-Chart' => esc_html__( 'Line Chart', 'theplus' ),
		'iconsmind-Line-Chart2' => esc_html__( 'Line Chart2', 'theplus' ),
		'iconsmind-Line-Chart3' => esc_html__( 'Line Chart3', 'theplus' ),
		'iconsmind-Line-Chart4' => esc_html__( 'Line Chart4', 'theplus' ),
		'iconsmind-Line-Spacing' => esc_html__( 'Line Spacing', 'theplus' ),
		'iconsmind-Line-SpacingText' => esc_html__( 'Line SpacingText', 'theplus' ),
		'iconsmind-Link-2' => esc_html__( 'Link-2', 'theplus' ),
		'iconsmind-Link' => esc_html__( 'Link', 'theplus' ),
		'iconsmind-Linkedin-2' => esc_html__( 'Linkedin 2', 'theplus' ),
		'iconsmind-Linkedin' => esc_html__( 'Linkedin', 'theplus' ),
		'iconsmind-Linux' => esc_html__( 'Linux', 'theplus' ),
		'iconsmind-Lion' => esc_html__( 'Lion', 'theplus' ),
		'iconsmind-Livejournal' => esc_html__( 'Livejournal', 'theplus' ),
		'iconsmind-Loading-2' => esc_html__( 'Loading 2', 'theplus' ),
		'iconsmind-Loading-3' => esc_html__( 'Loading 3', 'theplus' ),
		'iconsmind-Loading-Window' => esc_html__( 'Loading Window', 'theplus' ),
		'iconsmind-Loading' => esc_html__( 'Loading', 'theplus' ),
		'iconsmind-Location-2' => esc_html__( 'Location 2', 'theplus' ),
		'iconsmind-Location' => esc_html__( 'Location', 'theplus' ),
		'iconsmind-Lock-2' => esc_html__( 'Lock 2', 'theplus' ),
		'iconsmind-Lock-3' => esc_html__( 'Lock 3', 'theplus' ),
		'iconsmind-Lock-User' => esc_html__( 'Lock User', 'theplus' ),
		'iconsmind-Lock-Window' => esc_html__( 'Lock Window', 'theplus' ),
		'iconsmind-Lock' => esc_html__( 'Lock', 'theplus' ),
		'iconsmind-Lollipop-2' => esc_html__( 'Lollipop 2', 'theplus' ),
		'iconsmind-Lollipop-3' => esc_html__( 'Lollipop 3', 'theplus' ),
		'iconsmind-Lollipop' => esc_html__( 'Lollipop', 'theplus' ),
		'iconsmind-Loop' => esc_html__( 'Loop', 'theplus' ),
		'iconsmind-Loud' => esc_html__( 'Loud', 'theplus' ),
		'iconsmind-Loudspeaker' => esc_html__( 'Loudspeaker', 'theplus' ),
		'iconsmind-Love-2' => esc_html__( 'Love 2', 'theplus' ),
		'iconsmind-Love-User' => esc_html__( 'Love User', 'theplus' ),
		'iconsmind-Love-Window' => esc_html__( 'Love Window', 'theplus' ),
		'iconsmind-Love' => esc_html__( 'Love', 'theplus' ),
		'iconsmind-Lowercase-Text' => esc_html__( 'Lowercase Text', 'theplus' ),
		'iconsmind-Luggafe-Front' => esc_html__( 'Luggafe Front', 'theplus' ),
		'iconsmind-Luggage-2' => esc_html__( 'Luggage 2', 'theplus' ),
		'iconsmind-Macro' => esc_html__( 'Macro', 'theplus' ),
		'iconsmind-Magic-Wand' => esc_html__( 'Magic Wand', 'theplus' ),
		'iconsmind-Magnet' => esc_html__( 'Magnet', 'theplus' ),
		'iconsmind-Magnifi-Glass' => esc_html__( 'Magnifi-Glass', 'theplus' ),
		'iconsmind-Magnifi-Glass' => esc_html__( 'Magnifi-Glass', 'theplus' ),
		'iconsmind-Magnifi-Glass2' => esc_html__( 'Magnifi-Glass2', 'theplus' ),
		'iconsmind-Mail-2' => esc_html__( 'Mail-2', 'theplus' ),
		'iconsmind-Mail-3' => esc_html__( 'Mail-3', 'theplus' ),
		'iconsmind-Mail-Add' => esc_html__( 'Mail-Add', 'theplus' ),
		'iconsmind-Mail-Attachement' => esc_html__( 'Mail-Attachement', 'theplus' ),
		'iconsmind-Mail-Block' => esc_html__( 'Mail-Block', 'theplus' ),
		'iconsmind-Mail-Delete' => esc_html__( 'Mail-Delete', 'theplus' ),
		'iconsmind-Mail-Favorite' => esc_html__( 'Mail-Favorite', 'theplus' ),
		'iconsmind-Mail-Forward' => esc_html__( 'Mail-Forward', 'theplus' ),
		'iconsmind-Mail-Gallery' => esc_html__( 'Mail-Gallery', 'theplus' ),
		'iconsmind-Mail-Inbox' => esc_html__( 'Mail-Inbox', 'theplus' ),
		'iconsmind-Mail-Link' => esc_html__( 'Mail-Link', 'theplus' ),
		'iconsmind-Mail-Lock' => esc_html__( 'Mail-Lock', 'theplus' ),
		'iconsmind-Mail-Love' => esc_html__( 'Mail-Love', 'theplus' ),
		'iconsmind-Mail-Money' => esc_html__( 'Mail-Money', 'theplus' ),
		'iconsmind-Mail-Open' => esc_html__( 'Mail-Open', 'theplus' ),
		'iconsmind-Mail-Outbox' => esc_html__( 'Mail-Outbox', 'theplus' ),
		'iconsmind-Mail-Password' => esc_html__( 'Mail-Password', 'theplus' ),
		'iconsmind-Mail-Photo' => esc_html__( 'Mail-Photo', 'theplus' ),
		'iconsmind-Mail-Read' => esc_html__( 'Mail-Read', 'theplus' ),
		'iconsmind-Mail-Removex' => esc_html__( 'Mail-Removex', 'theplus' ),
		'iconsmind-Mail-Reply' => esc_html__( 'Mail-Reply', 'theplus' ),
		'iconsmind-Mail-ReplyAll' => esc_html__( 'Mail-ReplyAll', 'theplus' ),
		'iconsmind-Mail-Search' => esc_html__( 'Mail-Search', 'theplus' ),
		'iconsmind-Mail-Send' => esc_html__( 'Mail-Send', 'theplus' ),
		'iconsmind-Mail-Settings' => esc_html__( 'Mail-Settings', 'theplus' ),
		'iconsmind-Mail-Unread' => esc_html__( 'Mail-Unread', 'theplus' ),
		'iconsmind-Mail-Video' => esc_html__( 'Mail-Video', 'theplus' ),
		'iconsmind-Mail-withAtSign' => esc_html__( 'Mail-withAtSign', 'theplus' ),
		'iconsmind-Mail-WithCursors' => esc_html__( 'Mail-WithCursors', 'theplus' ),
		'iconsmind-Mail' => esc_html__( 'Mail', 'theplus' ),
		'iconsmind-Mailbox-Empty' => esc_html__( 'Mailbox-Empty', 'theplus' ),
		'iconsmind-Mailbox-Full' => esc_html__( 'Mailbox-Full', 'theplus' ),
		'iconsmind-Male-2' => esc_html__( 'Male-2', 'theplus' ),
		'iconsmind-Male-Sign' => esc_html__( 'Male-Sign', 'theplus' ),
		'iconsmind-Male' => esc_html__( 'Male', 'theplus' ),
		'iconsmind-MaleFemale' => esc_html__( 'MaleFemale', 'theplus' ),
		'iconsmind-Man-Sign' => esc_html__( 'Man-Sign', 'theplus' ),
		'iconsmind-Management' => esc_html__( 'Management', 'theplus' ),
		'iconsmind-Mans-Underwear' => esc_html__( 'Mans-Underwear', 'theplus' ),
		'iconsmind-Mans-Underwear2' => esc_html__( 'Mans-Underwear2', 'theplus' ),
		'iconsmind-Map-Marker' => esc_html__( 'Map-Marker', 'theplus' ),
		'iconsmind-Map-Marker2' => esc_html__( 'Map-Marker2', 'theplus' ),
		'iconsmind-Map-Marker3' => esc_html__( 'Map-Marker3', 'theplus' ),
		'iconsmind-Map' => esc_html__( 'Map', 'theplus' ),
		'iconsmind-Map-Marker3' => esc_html__( 'Map-Marker3', 'theplus' ),
		'iconsmind-Map' => esc_html__( 'Map', 'theplus' ),
		'iconsmind-Map2' => esc_html__( 'Map2', 'theplus' ),
		'iconsmind-Marker-2' => esc_html__( 'Marker-2', 'theplus' ),
		'iconsmind-Marker-3' => esc_html__( 'Marker-3', 'theplus' ),
		'iconsmind-Marker' => esc_html__( 'Marker', 'theplus' ),
		'iconsmind-Martini-Glass' => esc_html__( 'Martini-Glass', 'theplus' ),
		'iconsmind-Mask' => esc_html__( 'Mask', 'theplus' ),
		'iconsmind-Master-Card' => esc_html__( 'Master-Card', 'theplus' ),
		'iconsmind-Maximize-Window' => esc_html__( 'Maximize-Window', 'theplus' ),
		'iconsmind-Maximize' => esc_html__( 'Maximize', 'theplus' ),
		'iconsmind-Medal-2' => esc_html__( 'Medal-2', 'theplus' ),
		'iconsmind-Medal-3' => esc_html__( 'Medal-3', 'theplus' ),
		'iconsmind-Medal' => esc_html__( 'Medal', 'theplus' ),
		'iconsmind-Medical-Sign' => esc_html__( 'Medical-Sign', 'theplus' ),
		'iconsmind-Medicine-2' => esc_html__( 'Medicine-2', 'theplus' ),
		'iconsmind-Medicine-3' => esc_html__( 'Medicine-3', 'theplus' ),
		'iconsmind-Medicine' => esc_html__( 'Medicine', 'theplus' ),
		'iconsmind-Megaphone' => esc_html__( 'Megaphone', 'theplus' ),
		'iconsmind-Memory-Card' => esc_html__( 'Memory-Card', 'theplus' ),
		'iconsmind-Memory-Card2' => esc_html__( 'Memory-Card2', 'theplus' ),
		'iconsmind-Memory-Card3' => esc_html__( 'Memory-Card3', 'theplus' ),
		'iconsmind-Men' => esc_html__( 'Men', 'theplus' ),
		'iconsmind-Menorah' => esc_html__( 'Menorah', 'theplus' ),
		'iconsmind-Mens' => esc_html__( 'Mens', 'theplus' ),
		'iconsmind-Metacafe' => esc_html__( 'Metacafe', 'theplus' ),
		'iconsmind-Mexico' => esc_html__( 'Mexico', 'theplus' ),
		'iconsmind-Mic' => esc_html__( 'Mic', 'theplus' ),
		'iconsmind-Microphone-2' => esc_html__( 'Microphone-2', 'theplus' ),
		'iconsmind-Microphone-3' => esc_html__( 'Microphone-3', 'theplus' ),
		'iconsmind-Microphone-4' => esc_html__( 'Microphone-4', 'theplus' ),
		'iconsmind-Microphone-5' => esc_html__( 'Microphone-5', 'theplus' ),
		'iconsmind-Microphone-6' => esc_html__( 'Microphone-6', 'theplus' ),
		'iconsmind-Microphone-7' => esc_html__( 'Microphone-7', 'theplus' ),
		'iconsmind-Microphone' => esc_html__( 'Microphone', 'theplus' ),
		'iconsmind-Microscope' => esc_html__( 'Microscope', 'theplus' ),
		'iconsmind-Milk-Bottle' => esc_html__( 'Milk-Bottle', 'theplus' ),
		'iconsmind-Mine' => esc_html__( 'Mine', 'theplus' ),
		'iconsmind-Minimize-Maximize-Close-Window' => esc_html__( 'Minimize-Maximize-Close-Window', 'theplus' ),
		'iconsmind-Minimize-Window' => esc_html__( 'Minimize-Window', 'theplus' ),
		'iconsmind-Minimize' => esc_html__( 'Minimize', 'theplus' ),
		'iconsmind-Mirror' => esc_html__( 'Mirror', 'theplus' ),
		'iconsmind-Mixer' => esc_html__( 'Mixer', 'theplus' ),
		'iconsmind-Mixx' => esc_html__( 'Mixx', 'theplus' ),
		'iconsmind-Money-2' => esc_html__( 'Money 2', 'theplus' ),
		'iconsmind-Money-Bag' => esc_html__( 'Money Bag', 'theplus' ),
		'iconsmind-Money-Smiley' => esc_html__( 'Money Smiley', 'theplus' ),
		'iconsmind-Money' => esc_html__( 'Money', 'theplus' ),
		'iconsmind-Monitor-2' => esc_html__( 'Monitor 2', 'theplus' ),
		'iconsmind-Monitor-3' => esc_html__( 'Monitor 3', 'theplus' ),
		'iconsmind-Monitor-4' => esc_html__( 'Monitor 4', 'theplus' ),
		'iconsmind-Monitor-5' => esc_html__( 'Monitor 5', 'theplus' ),
		'iconsmind-Monitor-Analytics' => esc_html__( 'Monitor Analytics', 'theplus' ),
		'iconsmind-Monitor-Laptop' => esc_html__( 'Monitor Laptop', 'theplus' ),
		'iconsmind-Monitor-phone' => esc_html__( 'Monitor phone', 'theplus' ),
		'iconsmind-Monitor-Tablet' => esc_html__( 'Monitor Tablet', 'theplus' ),
		'iconsmind-Monitor-Vertical' => esc_html__( 'Monitor Vertical', 'theplus' ),
		'iconsmind-Monitor' => esc_html__( 'Monitor', 'theplus' ),
		'iconsmind-Monitoring' => esc_html__( 'Monitoring', 'theplus' ),
		'iconsmind-Monkey' => esc_html__( 'Monkey', 'theplus' ),
		'iconsmind-Monster' => esc_html__( 'Monster', 'theplus' ),
		'iconsmind-Morocco' => esc_html__( 'Morocco', 'theplus' ),
		'iconsmind-Motorcycle' => esc_html__( 'Motorcycle', 'theplus' ),
		'iconsmind-Mouse-2' => esc_html__( 'Mouse 2', 'theplus' ),
		'iconsmind-Mouse-3' => esc_html__( 'Mouse 3', 'theplus' ),
		'iconsmind-Mouse-4' => esc_html__( 'Mouse 4', 'theplus' ),
		'iconsmind-Mouse-Pointer' => esc_html__( 'Mouse Pointer', 'theplus' ),
		'iconsmind-Mouse' => esc_html__( 'Mouse', 'theplus' ),
		'iconsmind-Moustache-Smiley' => esc_html__( 'Moustache Smiley', 'theplus' ),
		'iconsmind-Movie-Ticket' => esc_html__( 'Movie Ticket', 'theplus' ),
		'iconsmind-Movie' => esc_html__( 'Movie', 'theplus' ),
		'iconsmind-Mp3-File' => esc_html__( 'Mp3 File', 'theplus' ),
		'iconsmind-Museum' => esc_html__( 'Museum', 'theplus' ),
		'iconsmind-Mushroom' => esc_html__( 'Mushroom', 'theplus' ),
		'iconsmind-Music-Note' => esc_html__( 'Music Note', 'theplus' ),
		'iconsmind-Music-Note2' => esc_html__( 'Music Note2', 'theplus' ),
		'iconsmind-Music-Note3' => esc_html__( 'Music Note3', 'theplus' ),
		'iconsmind-Music-Note4' => esc_html__( 'Music Note4', 'theplus' ),
		'iconsmind-Music-Player' => esc_html__( 'Music Player', 'theplus' ),
		'iconsmind-Mustache-2' => esc_html__( 'Mustache 2', 'theplus' ),
		'iconsmind-Mustache-3' => esc_html__( 'Mustache 3', 'theplus' ),
		'iconsmind-Mustache-4' => esc_html__( 'Mustache 4', 'theplus' ),
		'iconsmind-Mustache-5' => esc_html__( 'Mustache 5', 'theplus' ),
		'iconsmind-Mustache-6' => esc_html__( 'Mustache 6', 'theplus' ),
		'iconsmind-Mustache-7' => esc_html__( 'Mustache 7', 'theplus' ),
		'iconsmind-Mustache-8' => esc_html__( 'Mustache 8', 'theplus' ),
		'iconsmind-Mustache' => esc_html__( 'Mustache', 'theplus' ),
		'iconsmind-Mute' => esc_html__( 'Mute', 'theplus' ),
		'iconsmind-Myspace' => esc_html__( 'Myspace', 'theplus' ),
		'iconsmind-Navigat-Start' => esc_html__( 'Navigat Start', 'theplus' ),
		'iconsmind-Navigate-End' => esc_html__( 'Navigate End', 'theplus' ),
		'iconsmind-Navigation-LeftWindow' => esc_html__( 'Navigation LeftWindow', 'theplus' ),
		'iconsmind-Navigation-RightWindow' => esc_html__( 'Navigation RightWindow', 'theplus' ),
		'iconsmind-Nepal' => esc_html__( 'Nepal', 'theplus' ),
		'iconsmind-Netscape' => esc_html__( 'Netscape', 'theplus' ),
		'iconsmind-Network-Window' => esc_html__( 'Network-Window', 'theplus' ),
		'iconsmind-Network' => esc_html__( 'Network', 'theplus' ),
		'iconsmind-Neutron' => esc_html__( 'Neutron', 'theplus' ),
		'iconsmind-New-Mail' => esc_html__( 'New Mail', 'theplus' ),
		'iconsmind-New-Tab' => esc_html__( 'New-Tab', 'theplus' ),
		'iconsmind-Newspaper-2' => esc_html__( 'Newspaper 2', 'theplus' ),
		'iconsmind-Newspaper' => esc_html__( 'Newspaper', 'theplus' ),
		'iconsmind-Newsvine' => esc_html__( 'Newsvine', 'theplus' ),
		'iconsmind-Next2' => esc_html__( 'Next2', 'theplus' ),
		'iconsmind-Next-3' => esc_html__( 'Next 3', 'theplus' ),
		'iconsmind-Next-Music' => esc_html__( 'Next Music', 'theplus' ),
		'iconsmind-Next' => esc_html__( 'Next', 'theplus' ),
		'iconsmind-No-Battery' => esc_html__( 'No Battery', 'theplus' ),
		'iconsmind-No-Drop' => esc_html__( 'No Drop', 'theplus' ),
		'iconsmind-No-Flash' => esc_html__( 'No Flash', 'theplus' ),
		'iconsmind-No-Smoking' => esc_html__( 'No Smoking', 'theplus' ),
		'iconsmind-Noose' => esc_html__( 'Noose', 'theplus' ),
		'iconsmind-Normal-Text' => esc_html__( 'Normal Text', 'theplus' ),
		'iconsmind-Note' => esc_html__( 'Note', 'theplus' ),
		'iconsmind-Notepad-2' => esc_html__( 'Notepad 2', 'theplus' ),
		'iconsmind-Notepad' => esc_html__( 'Notepad', 'theplus' ),
		'iconsmind-Nuclear' => esc_html__( 'Nuclear', 'theplus' ),
		'iconsmind-Numbering-List' => esc_html__( 'Numbering List', 'theplus' ),
		'iconsmind-Nurse' => esc_html__( 'Nurse', 'theplus' ),
		'iconsmind-Office-Lamp ' => esc_html__( 'Office Lamp', 'theplus' ),
		'iconsmind-Office' => esc_html__( 'Office', 'theplus' ),
		'iconsmind-Oil' => esc_html__( 'Oil', 'theplus' ),
		'iconsmind-Old-Camera' => esc_html__( 'Old Camera', 'theplus' ),
		'iconsmind-Old-Cassette' => esc_html__( 'Old Cassette', 'theplus' ),
		'iconsmind-Old-Clock' => esc_html__( 'Old Clock', 'theplus' ),
		'iconsmind-Old-Radio' => esc_html__( 'Old Radio', 'theplus' ),
		'iconsmind-Old-Sticky' => esc_html__( 'Old Sticky', 'theplus' ),
		'iconsmind-Old-Sticky2' => esc_html__( 'Old Sticky2', 'theplus' ),
		'iconsmind-Old-Telephone' => esc_html__( 'Old Telephone', 'theplus' ),
		'iconsmind-Old-TV' => esc_html__( 'Old TV', 'theplus' ),
		'iconsmind-On-Air' => esc_html__( 'On Air', 'theplus' ),
		'iconsmind-On-Off-2' => esc_html__( 'On Off-2', 'theplus' ),
		'iconsmind-On-Off-3' => esc_html__( 'On Off-3', 'theplus' ),
		'iconsmind-On-off' => esc_html__( 'On off', 'theplus' ),
		'iconsmind-One-Finger' => esc_html__( 'One Finger', 'theplus' ),
		'iconsmind-One-FingerTouch' => esc_html__( 'One FingerTouch', 'theplus' ),
		'iconsmind-One-Window' => esc_html__( 'One Window', 'theplus' ),
		'iconsmind-Open-Banana' => esc_html__( 'Open Banana', 'theplus' ),
		'iconsmind-Open-Book' => esc_html__( 'Open Book', 'theplus' ),
		'iconsmind-Opera-House' => esc_html__( 'Opera House', 'theplus' ),
		'iconsmind-Opera' => esc_html__( 'Opera', 'theplus' ),
		'iconsmind-Optimization' => esc_html__( 'Optimization', 'theplus' ),
		'iconsmind-Orientation-2' => esc_html__( 'Orientation 2', 'theplus' ),
		'iconsmind-Orientation-3' => esc_html__( 'Orientation 3', 'theplus' ),
		'iconsmind-Orientation' => esc_html__( 'Orientation', 'theplus' ),
		'iconsmind-Orkut' => esc_html__( 'Orkut', 'theplus' ),
		'iconsmind-Ornament' => esc_html__( 'Ornament', 'theplus' ),
		'iconsmind-Over-Time' => esc_html__( 'Over Time', 'theplus' ),
		'iconsmind-Over-Time2' => esc_html__( 'Over Time2', 'theplus' ),
		'iconsmind-Owl' => esc_html__( 'Owl', 'theplus' ),
		'iconsmind-Pac-Man' => esc_html__( 'Pac Man', 'theplus' ),
		'iconsmind-Paint-Brush' => esc_html__( 'Paint Brush', 'theplus' ),
		'iconsmind-Paint-Bucket' => esc_html__( 'Paint Bucket', 'theplus' ),
		'iconsmind-Paintbrush' => esc_html__( 'Paintbrush', 'theplus' ),
		'iconsmind-Palette' => esc_html__( 'Palette', 'theplus' ),
		'iconsmind-Palm-Tree' => esc_html__( 'Palm Tree', 'theplus' ),
		'iconsmind-Panda' => esc_html__( 'Panda', 'theplus' ),
		'iconsmind-Panorama' => esc_html__( 'Panorama', 'theplus' ),
		'iconsmind-Pantheon' => esc_html__( 'Pantheon', 'theplus' ),
		'iconsmind-Pantone' => esc_html__( 'Pantone', 'theplus' ),
		'iconsmind-Pants' => esc_html__( 'Pants', 'theplus' ),
		'iconsmind-Paper-Plane' => esc_html__( 'Paper-Plane', 'theplus' ),
		'iconsmind-Paper' => esc_html__( 'Paper', 'theplus' ),
		'iconsmind-Parasailing' => esc_html__( 'Parasailing', 'theplus' ),
		'iconsmind-Parrot' => esc_html__( 'Parrot', 'theplus' ),
		'iconsmind-Password-2shopping' => esc_html__( 'Password-2shopping', 'theplus' ),
		'iconsmind-Password-Field' => esc_html__( 'Password-Field', 'theplus' ),
		'iconsmind-Password-shopping' => esc_html__( 'Password-shopping', 'theplus' ),
		'iconsmind-Password' => esc_html__( 'Password', 'theplus' ),
		'iconsmind-pause-2' => esc_html__( 'pause-2', 'theplus' ),
		'iconsmind-Pause' => esc_html__( 'Pause', 'theplus' ),
		'iconsmind-Paw' => esc_html__( 'Paw', 'theplus' ),
		'iconsmind-Pawn' => esc_html__( 'Pawn', 'theplus' ),
		'iconsmind-Paypal' => esc_html__( 'Paypal', 'theplus' ),
		'iconsmind-Pen-2' => esc_html__( 'Pen-2', 'theplus' ),
		'iconsmind-Pen-3' => esc_html__( 'Pen-3', 'theplus' ),
		'iconsmind-Pen-4' => esc_html__( 'Pen-4', 'theplus' ),
		'iconsmind-Pen-5' => esc_html__( 'Pen-5', 'theplus' ),
		'iconsmind-Pen-6' => esc_html__( 'Pen-6', 'theplus' ),
		'iconsmind-Pen' => esc_html__( 'Pen', 'theplus' ),
		'iconsmind-Pencil-Ruler' => esc_html__( 'Pencil-Ruler', 'theplus' ),
		'iconsmind-Pencil' => esc_html__( 'Pencil', 'theplus' ),
		'iconsmind-Penguin' => esc_html__( 'Penguin', 'theplus' ),
		'iconsmind-Pentagon' => esc_html__( 'Pentagon', 'theplus' ),
		'iconsmind-People-onCloud' => esc_html__( 'People-onCloud', 'theplus' ),
		'iconsmind-Pepper-withFire' => esc_html__( 'Pepper-withFire', 'theplus' ),
		'iconsmind-Pepper' => esc_html__( 'Pepper', 'theplus' ),
		'iconsmind-Petrol' => esc_html__( 'Petrol', 'theplus' ),
		'iconsmind-Petronas-Tower' => esc_html__( 'Petronas-Tower', 'theplus' ),
		'iconsmind-Philipines' => esc_html__( 'Philipines', 'theplus' ),
		'iconsmind-Phone-2' => esc_html__( 'Phone-2', 'theplus' ),
		'iconsmind-Phone-3' => esc_html__( 'Phone-3', 'theplus' ),
		'iconsmind-Phone-3G' => esc_html__( 'Phone-3G', 'theplus' ),
		'iconsmind-Phone-4G' => esc_html__( 'Phone-4G', 'theplus' ),
		'iconsmind-Phone-Simcard' => esc_html__( 'Phone-Simcard', 'theplus' ),
		'iconsmind-Phone-SMS' => esc_html__( 'Phone-SMS', 'theplus' ),
		'iconsmind-Phone-Wifi' => esc_html__( 'Phone-Wifi', 'theplus' ),
		'iconsmind-Phone' => esc_html__( 'Phone', 'theplus' ),
		'iconsmind-Photo-2' => esc_html__( 'Photo-2', 'theplus' ),
		'iconsmind-Photo-3' => esc_html__( 'Photo-3', 'theplus' ),
		'iconsmind-Photo-Album' => esc_html__( 'Photo-Album', 'theplus' ),
		'iconsmind-Photo-Album2' => esc_html__( 'Photo-Album2', 'theplus' ),
		'iconsmind-Photo-Album3' => esc_html__( 'Photo-Album3', 'theplus' ),
		'iconsmind-Photo' => esc_html__( 'Photo', 'theplus' ),
		'iconsmind-Photos' => esc_html__( 'Photos', 'theplus' ),
		'iconsmind-Physics' => esc_html__( 'Physics', 'theplus' ),
		'iconsmind-Pi' => esc_html__( 'Pi', 'theplus' ),
		'iconsmind-Piano' => esc_html__( 'Piano', 'theplus' ),
		'iconsmind-Picasa' => esc_html__( 'Picasa', 'theplus' ),
		'iconsmind-Pie-Chart' => esc_html__( 'Pie-Chart', 'theplus' ),
		'iconsmind-Pie-Chart2' => esc_html__( 'Pie-Chart2', 'theplus' ),
		'iconsmind-Pie-Chart3' => esc_html__( 'Pie-Chart3', 'theplus' ),
		'iconsmind-Pilates-2' => esc_html__( 'Pilates-2', 'theplus' ),
		'iconsmind-Pilates-3' => esc_html__( 'Pilates-3', 'theplus' ),
		'iconsmind-Pilates' => esc_html__( 'Pilates', 'theplus' ),
		'iconsmind-Pilot' => esc_html__( 'Pilot', 'theplus' ),
		'iconsmind-Pinch' => esc_html__( 'Pinch', 'theplus' ),
		'iconsmind-Ping-Pong' => esc_html__( 'Ping-Pong', 'theplus' ),
		'iconsmind-Pinterest' => esc_html__( 'Pinterest', 'theplus' ),
		'iconsmind-Pipe' => esc_html__( 'Pipe', 'theplus' ),
		'iconsmind-Pipette' => esc_html__( 'Pipette', 'theplus' ),
		'iconsmind-Piramids' => esc_html__( 'Piramids', 'theplus' ),
		'iconsmind-Pisces-2' => esc_html__( 'Pisces-2', 'theplus' ),
		'iconsmind-Pisces' => esc_html__( 'Pisces', 'theplus' ),
		'iconsmind-Pizza-Slice' => esc_html__( 'Pizza-Slice', 'theplus' ),
		'iconsmind-Pizza' => esc_html__( 'Pizza', 'theplus' ),
		'iconsmind-Plane-2' => esc_html__( 'Plane-2', 'theplus' ),
		'iconsmind-Plane' => esc_html__( 'Plane', 'theplus' ),
		'iconsmind-Plant' => esc_html__( 'Plant', 'theplus' ),
		'iconsmind-Plasmid' => esc_html__( 'Plasmid', 'theplus' ),
		'iconsmind-Plaster' => esc_html__( 'Plaster', 'theplus' ),
		'iconsmind-Plastic-CupPhone' => esc_html__( 'Plastic-CupPhone', 'theplus' ),
		'iconsmind-Plastic-CupPhone2' => esc_html__( 'Plastic-CupPhone2', 'theplus' ),
		'iconsmind-Plate' => esc_html__( 'Plate', 'theplus' ),
		'iconsmind-Plates' => esc_html__( 'Plates', 'theplus' ),
		'iconsmind-Plaxo' => esc_html__( 'Plaxo', 'theplus' ),
		'iconsmind-Play-Music' => esc_html__( 'Play-Music', 'theplus' ),
		'iconsmind-Plug-In' => esc_html__( 'Plug-In', 'theplus' ),
		'iconsmind-Plug-In2' => esc_html__( 'Plug-In2', 'theplus' ),
		'iconsmind-Plurk' => esc_html__( 'Plurk', 'theplus' ),
		'iconsmind-Pointer' => esc_html__( 'Pointer', 'theplus' ),
		'iconsmind-Poland' => esc_html__( 'Poland', 'theplus' ),
		'iconsmind-Police-Man' => esc_html__( 'Police-Man', 'theplus' ),
		'iconsmind-Police-Station' => esc_html__( 'Police-Station', 'theplus' ),
		'iconsmind-Police-Woman' => esc_html__( 'Police-Woman', 'theplus' ),
		'iconsmind-Police' => esc_html__( 'Police', 'theplus' ),
		'iconsmind-Polo-Shirt' => esc_html__( 'Polo-Shirt', 'theplus' ),
		'iconsmind-Portrait' => esc_html__( 'Portrait', 'theplus' ),
		'iconsmind-Portugal' => esc_html__( 'Portugal', 'theplus' ),
		'iconsmind-Post-Mail' => esc_html__( 'Post-Mail', 'theplus' ),
		'iconsmind-Post-Mail2' => esc_html__( 'Post-Mail2', 'theplus' ),
		'iconsmind-Post-Office' => esc_html__( 'Post-Office', 'theplus' ),
		'iconsmind-Post-Sign' => esc_html__( 'Post-Sign', 'theplus' ),
		'iconsmind-Post-Sign2ways' => esc_html__( 'Post-Sign2ways', 'theplus' ),
		'iconsmind-Posterous' => esc_html__( 'Posterous', 'theplus' ),
		'iconsmind-Pound-Sign' => esc_html__( 'Pound-Sign', 'theplus' ),
		'iconsmind-Pound-Sign2' => esc_html__( 'Pound-Sign2', 'theplus' ),
		'iconsmind-Pound' => esc_html__( 'Pound', 'theplus' ),
		'iconsmind-Power-2' => esc_html__( 'Power-2', 'theplus' ),
		'iconsmind-Power-3' => esc_html__( 'Power-3', 'theplus' ),
		'iconsmind-Power-Cable' => esc_html__( 'Power-Cable', 'theplus' ),
		'iconsmind-Power-Station' => esc_html__( 'Power-Station', 'theplus' ),
		'iconsmind-Power' => esc_html__( 'Power', 'theplus' ),
		'iconsmind-Prater' => esc_html__( 'Prater', 'theplus' ),
		'iconsmind-Present' => esc_html__( 'Present', 'theplus' ),
		'iconsmind-Presents' => esc_html__( 'Presents', 'theplus' ),
		'iconsmind-Press' => esc_html__( 'Press', 'theplus' ),
		'iconsmind-Preview' => esc_html__( 'Preview', 'theplus' ),
		'iconsmind-Previous' => esc_html__( 'Previous', 'theplus' ),
		'iconsmind-Pricing' => esc_html__( 'Pricing', 'theplus' ),
		'iconsmind-Printer' => esc_html__( 'Printer', 'theplus' ),
		'iconsmind-Professor' => esc_html__( 'Professor', 'theplus' ),
		'iconsmind-Profile' => esc_html__( 'Profile', 'theplus' ),
		'iconsmind-Project' => esc_html__( 'Project', 'theplus' ),
		'iconsmind-Projector-2' => esc_html__( 'Projector-2', 'theplus' ),
		'iconsmind-Projector' => esc_html__( 'Projector', 'theplus' ),
		'iconsmind-Pulse' => esc_html__( 'Pulse', 'theplus' ),
		'iconsmind-Pumpkin' => esc_html__( 'Pumpkin', 'theplus' ),
		'iconsmind-Punk' => esc_html__( 'Punk', 'theplus' ),
		'iconsmind-Punker' => esc_html__( 'Punker', 'theplus' ),
		'iconsmind-Puzzle' => esc_html__( 'Puzzle', 'theplus' ),
		'iconsmind-QIK' => esc_html__( 'QIK', 'theplus' ),
		'iconsmind-QR-Code' => esc_html__( 'QR-Code', 'theplus' ),
		'iconsmind-Queen-2' => esc_html__( 'Queen-2', 'theplus' ),
		'iconsmind-Queen' => esc_html__( 'Queen', 'theplus' ),
		'iconsmind-Quill-2' => esc_html__( 'Quill-2', 'theplus' ),
		'iconsmind-Quill-3' => esc_html__( 'Quill-3', 'theplus' ),
		'iconsmind-Quill' => esc_html__( 'Quill', 'theplus' ),
		'iconsmind-Quotes-2' => esc_html__( 'Quotes-2', 'theplus' ),
		'iconsmind-Quotes' => esc_html__( 'Quotes', 'theplus' ),
		'iconsmind-Radio' => esc_html__( 'Radio', 'theplus' ),
		'iconsmind-Radioactive' => esc_html__( 'Radioactive', 'theplus' ),
		'iconsmind-Rafting' => esc_html__( 'Rafting', 'theplus' ),
		'iconsmind-Rain-Drop' => esc_html__( 'Rain-Drop', 'theplus' ),
		'iconsmind-Rainbow-2' => esc_html__( 'Rainbow-2', 'theplus' ),
		'iconsmind-Rainbow' => esc_html__( 'Rainbow', 'theplus' ),
		'iconsmind-Ram' => esc_html__( 'Ram', 'theplus' ),
		'iconsmind-Razzor-Blade' => esc_html__( 'Razzor-Blade', 'theplus' ),
		'iconsmind-Receipt-2' => esc_html__( 'Receipt-2', 'theplus' ),
		'iconsmind-Receipt-3' => esc_html__( 'Receipt-3', 'theplus' ),
		'iconsmind-Receipt-4' => esc_html__( 'Receipt-4', 'theplus' ),
		'iconsmind-Receipt' => esc_html__( 'Receipt', 'theplus' ),
		'iconsmind-Record2' => esc_html__( 'Record2', 'theplus' ),
		'iconsmind-Record-3' => esc_html__( 'Record-3', 'theplus' ),
		'iconsmind-Record-Music' => esc_html__( 'Record-Music', 'theplus' ),
		'iconsmind-Record' => esc_html__( 'Record', 'theplus' ),
		'iconsmind-Recycling-2' => esc_html__( 'Recycling-2', 'theplus' ),
		'iconsmind-Recycling' => esc_html__( 'Recycling', 'theplus' ),
		'iconsmind-Reddit' => esc_html__( 'Reddit', 'theplus' ),
		'iconsmind-Redirect' => esc_html__( 'Redirect', 'theplus' ),
		'iconsmind-Redo' => esc_html__( 'Redo', 'theplus' ),
		'iconsmind-Reel' => esc_html__( 'Reel', 'theplus' ),
		'iconsmind-Refinery' => esc_html__( 'Refinery', 'theplus' ),
		'iconsmind-Refresh-Window' => esc_html__( 'Refresh-Window', 'theplus' ),
		'iconsmind-Refresh' => esc_html__( 'Refresh', 'theplus' ),
		'iconsmind-Reload-2' => esc_html__( 'Reload-2', 'theplus' ),
		'iconsmind-Reload-3' => esc_html__( 'Reload-3', 'theplus' ),
		'iconsmind-Reload' => esc_html__( 'Reload', 'theplus' ),
		'iconsmind-Remote-Controll' => esc_html__( 'Remote-Controll', 'theplus' ),
		'iconsmind-Remote-Controll2' => esc_html__( 'Remote-Controll2', 'theplus' ),
		'iconsmind-Remove-Bag' => esc_html__( 'Remove-Bag', 'theplus' ),
		'iconsmind-Remove-Basket' => esc_html__( 'Remove-Basket', 'theplus' ),
		'iconsmind-Remove-Cart' => esc_html__( 'Remove-Cart', 'theplus' ),
		'iconsmind-Remove-File' => esc_html__( 'Remove-File', 'theplus' ),
		'iconsmind-Remove-User' => esc_html__( 'Remove-User', 'theplus' ),
		'iconsmind-Remove-Window' => esc_html__( 'Remove-Window', 'theplus' ),
		'iconsmind-Remove' => esc_html__( 'Remove', 'theplus' ),
		'iconsmind-Rename' => esc_html__( 'Rename', 'theplus' ),
		'iconsmind-Repair' => esc_html__( 'Repair', 'theplus' ),
		'iconsmind-Repeat-2' => esc_html__( 'Repeat-2', 'theplus' ),
		'iconsmind-Repeat-3' => esc_html__( 'Repeat-3', 'theplus' ),
		'iconsmind-Repeat-4' => esc_html__( 'Repeat-4', 'theplus' ),
		'iconsmind-Repeat-5' => esc_html__( 'Repeat-5', 'theplus' ),
		'iconsmind-Repeat-6' => esc_html__( 'Repeat-6', 'theplus' ),
		'iconsmind-Repeat-7' => esc_html__( 'Repeat-7', 'theplus' ),
		'iconsmind-Repeat' => esc_html__( 'Repeat', 'theplus' ),
		'iconsmind-Reset' => esc_html__( 'Reset', 'theplus' ),
		'iconsmind-Resize' => esc_html__( 'Resize', 'theplus' ),
		'iconsmind-Restore-Window' => esc_html__( 'Restore-Window', 'theplus' ),
		'iconsmind-Retouching' => esc_html__( 'Retouching', 'theplus' ),
		'iconsmind-Retro-Camera' => esc_html__( 'Retro-Camera', 'theplus' ),
		'iconsmind-Retro' => esc_html__( 'Retro', 'theplus' ),
		'iconsmind-Retweet' => esc_html__( 'Retweet', 'theplus' ),
		'iconsmind-Reverbnation' => esc_html__( 'Reverbnation', 'theplus' ),
		'iconsmind-Rewind' => esc_html__( 'Rewind', 'theplus' ),
		'iconsmind-RGB' => esc_html__( 'RGB', 'theplus' ),
		'iconsmind-Ribbon-2' => esc_html__( 'Ribbon-2', 'theplus' ),
		'iconsmind-Ribbon-3' => esc_html__( 'Ribbon-3', 'theplus' ),
		'iconsmind-Ribbon' => esc_html__( 'Ribbon', 'theplus' ),
		'iconsmind-Right-2' => esc_html__( 'Right-2', 'theplus' ),
		'iconsmind-Right-3' => esc_html__( 'Right-3', 'theplus' ),
		'iconsmind-Right-4' => esc_html__( 'Right-4', 'theplus' ),
		'iconsmind-Right-ToLeft' => esc_html__( 'Right-ToLeft', 'theplus' ),
		'iconsmind-Right' => esc_html__( 'Right', 'theplus' ),
		'iconsmind-Road-2' => esc_html__( 'Road-2', 'theplus' ),
		'iconsmind-Road-3' => esc_html__( 'Road-3', 'theplus' ),
		'iconsmind-Road' => esc_html__( 'Road', 'theplus' ),
		'iconsmind-Robot-2' => esc_html__( 'Robot-2', 'theplus' ),
		'iconsmind-Robot' => esc_html__( 'Robot', 'theplus' ),
		'iconsmind-Rock-andRoll' => esc_html__( 'Rock-andRoll', 'theplus' ),
		'iconsmind-Rocket' => esc_html__( 'Rocket', 'theplus' ),
		'iconsmind-Roller' => esc_html__( 'Roller', 'theplus' ),
		'iconsmind-Roof' => esc_html__( 'Roof', 'theplus' ),
		'iconsmind-Rook' => esc_html__( 'Rook', 'theplus' ),
		'iconsmind-Rotate-Gesture' => esc_html__( 'Rotate-Gesture', 'theplus' ),
		'iconsmind-Rotate-Gesture2' => esc_html__( 'Rotate-Gesture2', 'theplus' ),
		'iconsmind-Rotate-Gesture3' => esc_html__( 'Rotate-Gesture3', 'theplus' ),
		'iconsmind-Rotation-390' => esc_html__( 'Rotation-390', 'theplus' ),
		'iconsmind-Rotation' => esc_html__( 'Rotation', 'theplus' ),
		'iconsmind-Router-2' => esc_html__( 'Router-2', 'theplus' ),
		'iconsmind-Router' => esc_html__( 'Router', 'theplus' ),
		'iconsmind-RSS' => esc_html__( 'RSS', 'theplus' ),
		'iconsmind-Ruler-2' => esc_html__( 'Ruler-2', 'theplus' ),
		'iconsmind-Ruler' => esc_html__( 'Ruler', 'theplus' ),
		'iconsmind-Running-Shoes' => esc_html__( 'Running-Shoes', 'theplus' ),
		'iconsmind-Running' => esc_html__( 'Running', 'theplus' ),
		'iconsmind-Safari' => esc_html__( 'Safari', 'theplus' ),
		'iconsmind-Safe-Box' => esc_html__( 'Safe-Box', 'theplus' ),
		'iconsmind-Safe-Box2' => esc_html__( 'Safe-Box2', 'theplus' ),
		'iconsmind-Safety-PinClose' => esc_html__( 'Safety-PinClose', 'theplus' ),
		'iconsmind-Safety-PinOpen' => esc_html__( 'Safety-PinOpen', 'theplus' ),
		'iconsmind-Sagittarus-2' => esc_html__( 'Sagittarus-2', 'theplus' ),
		'iconsmind-Sagittarus' => esc_html__( 'Sagittarus', 'theplus' ),
		'iconsmind-Sailing-Ship' => esc_html__( 'Sailing-Ship', 'theplus' ),
		'iconsmind-Sand-watch' => esc_html__( 'Sand-watch', 'theplus' ),
		'iconsmind-Sand-watch2' => esc_html__( 'Sand-watch2', 'theplus' ),
		'iconsmind-Santa-Claus' => esc_html__( 'Santa-Claus', 'theplus' ),
		'iconsmind-Santa-Claus2' => esc_html__( 'Santa-Claus2', 'theplus' ),
		'iconsmind-Santa-onSled' => esc_html__( 'Santa-onSled', 'theplus' ),
		'iconsmind-Satelite-2' => esc_html__( 'Satelite-2', 'theplus' ),
		'iconsmind-Satelite' => esc_html__( 'Satelite', 'theplus' ),
		'iconsmind-Save-Window' => esc_html__( 'Save-Window', 'theplus' ),
		'iconsmind-Save' => esc_html__( 'Save', 'theplus' ),
		'iconsmind-Saw' => esc_html__( 'Saw', 'theplus' ),
		'iconsmind-Saxophone' => esc_html__( 'Saxophone', 'theplus' ),
		'iconsmind-Scale' => esc_html__( 'Scale', 'theplus' ),
		'iconsmind-Scarf' => esc_html__( 'Scarf', 'theplus' ),
		'iconsmind-Scissor' => esc_html__( 'Scissor', 'theplus' ),
		'iconsmind-Scooter-Front' => esc_html__( 'Scooter-Front', 'theplus' ),
		'iconsmind-Scooter' => esc_html__( 'Scooter', 'theplus' ),
		'iconsmind-Scorpio-2' => esc_html__( 'Scorpio-2', 'theplus' ),
		'iconsmind-Scorpio' => esc_html__( 'Scorpio', 'theplus' ),
		'iconsmind-Scotland' => esc_html__( 'Scotland', 'theplus' ),
		'iconsmind-Screwdriver' => esc_html__( 'Screwdriver', 'theplus' ),
		'iconsmind-Scroll-Fast' => esc_html__( 'Scroll-Fast', 'theplus' ),
		'iconsmind-Scroll' => esc_html__( 'Scroll', 'theplus' ),
		'iconsmind-Scroller-2' => esc_html__( 'Scroller-2', 'theplus' ),
		'iconsmind-Scroller' => esc_html__( 'Scroller', 'theplus' ),
		'iconsmind-Sea-Dog' => esc_html__( 'Sea-Dog', 'theplus' ),
		'iconsmind-Search-onCloud' => esc_html__( 'Search-onCloud', 'theplus' ),
		'iconsmind-Search-People' => esc_html__( 'Search-People', 'theplus' ),
		'iconsmind-secound' => esc_html__( 'secound', 'theplus' ),
		'iconsmind-secound2' => esc_html__( 'secound2', 'theplus' ),
		'iconsmind-Security-Block' => esc_html__( 'Security-Block', 'theplus' ),
		'iconsmind-Security-Bug' => esc_html__( 'Security-Bug', 'theplus' ),
		'iconsmind-Security-Camera' => esc_html__( 'Security-Camera', 'theplus' ),
		'iconsmind-Security-Check' => esc_html__( 'Security-Check', 'theplus' ),
		'iconsmind-Security-Settings' => esc_html__( 'Security-Settings', 'theplus' ),
		'iconsmind-Security-Smiley' => esc_html__( 'Security-Smiley', 'theplus' ),
		'iconsmind-Securiy-Remove' => esc_html__( 'Securiy-Remove', 'theplus' ),
		'iconsmind-Seed' => esc_html__( 'Seed', 'theplus' ),
		'iconsmind-Selfie' => esc_html__( 'Selfie', 'theplus' ),
		'iconsmind-Serbia' => esc_html__( 'Serbia', 'theplus' ),
		'iconsmind-Server-2' => esc_html__( 'Server-2', 'theplus' ),
		'iconsmind-Server' => esc_html__( 'Server', 'theplus' ),
		'iconsmind-Servers' => esc_html__( 'Servers', 'theplus' ),
		'iconsmind-Settings-Window' => esc_html__( 'Settings-Window', 'theplus' ),
		'iconsmind-Sewing-Machine' => esc_html__( 'Sewing-Machine', 'theplus' ),
		'iconsmind-Sexual' => esc_html__( 'Sexual', 'theplus' ),
		'iconsmind-Share-onCloud' => esc_html__( 'Share-onCloud', 'theplus' ),
		'iconsmind-Share-Window' => esc_html__( 'Share-Window', 'theplus' ),
		'iconsmind-Share' => esc_html__( 'Share', 'theplus' ),
		'iconsmind-Sharethis' => esc_html__( 'Sharethis', 'theplus' ),
		'iconsmind-Shark' => esc_html__( 'Shark', 'theplus' ),
		'iconsmind-Sheep' => esc_html__( 'Sheep', 'theplus' ),
		'iconsmind-Sheriff-Badge' => esc_html__( 'Sheriff-Badge', 'theplus' ),
		'iconsmind-Shield' => esc_html__( 'Shield', 'theplus' ),
		'iconsmind-Ship-2' => esc_html__( 'Ship-2', 'theplus' ),
		'iconsmind-Ship' => esc_html__( 'Ship', 'theplus' ),
		'iconsmind-Shirt' => esc_html__( 'Shirt', 'theplus' ),
		'iconsmind-Shoes-2' => esc_html__( 'Shoes-2', 'theplus' ),
		'iconsmind-Shoes-3' => esc_html__( 'Shoes-3', 'theplus' ),
		'iconsmind-Shoes' => esc_html__( 'Shoes', 'theplus' ),
		'iconsmind-Shop-2' => esc_html__( 'Shop-2', 'theplus' ),
		'iconsmind-Shop-3' => esc_html__( 'Shop-3', 'theplus' ),
		'iconsmind-Shop-4' => esc_html__( 'Shop-4', 'theplus' ),
		'iconsmind-Shop' => esc_html__( 'Shop', 'theplus' ),
		'iconsmind-Shopping-Bag' => esc_html__( 'Shopping-Bag', 'theplus' ),
		'iconsmind-Shopping-Basket' => esc_html__( 'Shopping-Basket', 'theplus' ),
		'iconsmind-Shopping-Cart' => esc_html__( 'Shopping-Cart', 'theplus' ),
		'iconsmind-Short-Pants' => esc_html__( 'Short-Pants', 'theplus' ),
		'iconsmind-Shoutwire' => esc_html__( 'Shoutwire', 'theplus' ),
		'iconsmind-Shovel' => esc_html__( 'Shovel', 'theplus' ),
		'iconsmind-Shuffle-2' => esc_html__( 'Shuffle-2', 'theplus' ),
		'iconsmind-Shuffle-3' => esc_html__( 'Shuffle-3', 'theplus' ),
		'iconsmind-Shuffle-4' => esc_html__( 'Shuffle-4', 'theplus' ),
		'iconsmind-Shuffle' => esc_html__( 'Shuffle', 'theplus' ),
		'iconsmind-Shutter' => esc_html__( 'Shutter', 'theplus' ),
		'iconsmind-Sidebar-Window' => esc_html__( 'Sidebar-Window', 'theplus' ),
		'iconsmind-Signal' => esc_html__( 'Signal', 'theplus' ),
		'iconsmind-Singapore' => esc_html__( 'Singapore', 'theplus' ),
		'iconsmind-Skate-Shoes' => esc_html__( 'Skate-Shoes', 'theplus' ),
		'iconsmind-Skateboard-2' => esc_html__( 'Skateboard-2', 'theplus' ),
		'iconsmind-Skateboard' => esc_html__( 'Skateboard', 'theplus' ),
		'iconsmind-Skeleton' => esc_html__( 'Skeleton', 'theplus' ),
		'iconsmind-Ski' => esc_html__( 'Ski', 'theplus' ),
		'iconsmind-Skirt' => esc_html__( 'Skirt', 'theplus' ),
		'iconsmind-Skrill' => esc_html__( 'Skrill', 'theplus' ),
		'iconsmind-Skull' => esc_html__( 'Skull', 'theplus' ),
		'iconsmind-Skydiving' => esc_html__( 'Skydiving', 'theplus' ),
		'iconsmind-Skype' => esc_html__( 'Skype', 'theplus' ),
		'iconsmind-Sled-withGifts' => esc_html__( 'Sled-withGifts', 'theplus' ),
		'iconsmind-Sled' => esc_html__( 'Sled', 'theplus' ),
		'iconsmind-Sleeping' => esc_html__( 'Sleeping', 'theplus' ),
		'iconsmind-Sleet' => esc_html__( 'Sleet', 'theplus' ),
		'iconsmind-Slippers' => esc_html__( 'Slippers', 'theplus' ),
		'iconsmind-Smart' => esc_html__( 'Smart', 'theplus' ),
		'iconsmind-Smartphone-2' => esc_html__( 'Smartphone-2', 'theplus' ),
		'iconsmind-Smartphone-3' => esc_html__( 'Smartphone-3', 'theplus' ),
		'iconsmind-Smartphone-4' => esc_html__( 'Smartphone-4', 'theplus' ),
		'iconsmind-Smartphone-Secure' => esc_html__( 'Smartphone-Secure', 'theplus' ),
		'iconsmind-Smartphone' => esc_html__( 'Smartphone', 'theplus' ),
		'iconsmind-Smile' => esc_html__( 'Smile', 'theplus' ),
		'iconsmind-Smoking-Area' => esc_html__( 'Smoking-Area', 'theplus' ),
		'iconsmind-Smoking-Pipe' => esc_html__( 'Smoking-Pipe', 'theplus' ),
		'iconsmind-Snake' => esc_html__( 'Snake', 'theplus' ),
		'iconsmind-Snorkel' => esc_html__( 'Snorkel', 'theplus' ),
		'iconsmind-Snow-2' => esc_html__( 'Snow-2', 'theplus' ),
		'iconsmind-Snow-Dome' => esc_html__( 'Snow-Dome', 'theplus' ),
		'iconsmind-Snow-Storm' => esc_html__( 'Snow-Storm', 'theplus' ),
		'iconsmind-Snow' => esc_html__( 'Snow', 'theplus' ),
		'iconsmind-Snowflake-2' => esc_html__( 'Snowflake-2', 'theplus' ),
		'iconsmind-Snowflake-3' => esc_html__( 'Snowflake-3', 'theplus' ),
		'iconsmind-Snowflake-4' => esc_html__( 'Snowflake-4', 'theplus' ),
		'iconsmind-Snowflake' => esc_html__( 'Snowflake', 'theplus' ),
		'iconsmind-Snowman' => esc_html__( 'Snowman', 'theplus' ),
		'iconsmind-Soccer-Ball' => esc_html__( 'Soccer-Ball', 'theplus' ),
		'iconsmind-Soccer-Shoes' => esc_html__( 'Soccer-Shoes', 'theplus' ),
		'iconsmind-Socks' => esc_html__( 'Socks', 'theplus' ),
		'iconsmind-Solar' => esc_html__( 'Solar', 'theplus' ),
		'iconsmind-Sound-Wave' => esc_html__( 'Sound-Wave', 'theplus' ),
		'iconsmind-Sound' => esc_html__( 'Sound', 'theplus' ),
		'iconsmind-Soundcloud' => esc_html__( 'Soundcloud', 'theplus' ),
		'iconsmind-Soup' => esc_html__( 'Soup', 'theplus' ),
		'iconsmind-South-Africa' => esc_html__( 'South-Africa', 'theplus' ),
		'iconsmind-Space-Needle' => esc_html__( 'Space-Needle', 'theplus' ),
		'iconsmind-Spain' => esc_html__( 'Spain', 'theplus' ),
		'iconsmind-Spam-Mail' => esc_html__( 'Spam-Mail', 'theplus' ),
		'iconsmind-Speach-Bubble' => esc_html__( 'Speach-Bubble', 'theplus' ),
		'iconsmind-Speach-Bubble2' => esc_html__( 'Speach-Bubble2', 'theplus' ),
		'iconsmind-Speach-Bubble3' => esc_html__( 'Speach-Bubble3', 'theplus' ),
		'iconsmind-Speach-Bubble4' => esc_html__( 'Speach-Bubble4', 'theplus' ),
		'iconsmind-Speach-Bubble5' => esc_html__( 'Speach-Bubble5', 'theplus' ),
		'iconsmind-Speach-Bubble6' => esc_html__( 'Speach-Bubble6', 'theplus' ),
		'iconsmind-Speach-Bubble7' => esc_html__( 'Speach-Bubble7', 'theplus' ),
		'iconsmind-Speach-Bubble8' => esc_html__( 'Speach-Bubble8', 'theplus' ),
		'iconsmind-Speach-Bubble9' => esc_html__( 'Speach-Bubble9', 'theplus' ),
		'iconsmind-Speach-Bubble10' => esc_html__( 'Speach-Bubble10', 'theplus' ),
		'iconsmind-Speach-Bubble11' => esc_html__( 'Speach-Bubble11', 'theplus' ),
		'iconsmind-Speach-Bubble12' => esc_html__( 'Speach-Bubble12', 'theplus' ),
		'iconsmind-Speach-Bubble13' => esc_html__( 'Speach-Bubble13', 'theplus' ),
		'iconsmind-Speach-BubbleAsking' => esc_html__( 'Speach-BubbleAsking', 'theplus' ),
		'iconsmind-Speach-BubbleComic' => esc_html__( 'Speach-BubbleComic', 'theplus' ),
		'iconsmind-Speach-BubbleComic2' => esc_html__( 'Speach-BubbleComic2', 'theplus' ),
		'iconsmind-Speach-BubbleComic3' => esc_html__( 'Speach-BubbleComic3', 'theplus' ),
		'iconsmind-Speach-BubbleComic4' => esc_html__( 'Speach-BubbleComic4', 'theplus' ),
		'iconsmind-Speach-BubbleDialog' => esc_html__( 'Speach-BubbleDialog', 'theplus' ),
		'iconsmind-Speach-Bubbles' => esc_html__( 'Speach-Bubbles', 'theplus' ),
		'iconsmind-Speak-2' => esc_html__( 'Speak-2', 'theplus' ),
		'iconsmind-Speak' => esc_html__( 'Speak', 'theplus' ),
		'iconsmind-Speaker-2' => esc_html__( 'Speaker-2', 'theplus' ),
		'iconsmind-Speaker' => esc_html__( 'Speaker', 'theplus' ),
		'iconsmind-Spell-Check' => esc_html__( 'Spell-Check', 'theplus' ),
		'iconsmind-Spell-CheckABC' => esc_html__( 'Spell-CheckABC', 'theplus' ),
		'iconsmind-Spermium' => esc_html__( 'Spermium', 'theplus' ),
		'iconsmind-Spider' => esc_html__( 'Spider', 'theplus' ),
		'iconsmind-Spiderweb' => esc_html__( 'Spiderweb', 'theplus' ),
		'iconsmind-Split-FourSquareWindow' => esc_html__( 'Split-FourSquareWindow', 'theplus' ),
		'iconsmind-Split-Horizontal' => esc_html__( 'Split-Horizontal', 'theplus' ),
		'iconsmind-Split-Horizontal2Window' => esc_html__( 'Split-Horizontal2Window', 'theplus' ),
		'iconsmind-Split-Vertical' => esc_html__( 'Split-Vertical', 'theplus' ),
		'iconsmind-Split-Vertical2' => esc_html__( 'Split-Vertical2', 'theplus' ),
		'iconsmind-Split-Window' => esc_html__( 'Split-Window', 'theplus' ),
		'iconsmind-Spoder' => esc_html__( 'Spoder', 'theplus' ),
		'iconsmind-Spoon' => esc_html__( 'Spoon', 'theplus' ),
		'iconsmind-Sport-Mode' => esc_html__( 'Sport-Mode', 'theplus' ),
		'iconsmind-Sports-Clothings1' => esc_html__( 'Sports-Clothings1', 'theplus' ),
		'iconsmind-Sports-Clothings2' => esc_html__( 'Sports-Clothings2', 'theplus' ),
		'iconsmind-Sports-Shirt' => esc_html__( 'Sports-Shirt', 'theplus' ),
		'iconsmind-Spot' => esc_html__( 'Spot', 'theplus' ),
		'iconsmind-Spray' => esc_html__( 'Spray', 'theplus' ),
		'iconsmind-Spread' => esc_html__( 'Spread', 'theplus' ),
		'iconsmind-Spring' => esc_html__( 'Spring', 'theplus' ),
		'iconsmind-Spurl' => esc_html__( 'Spurl', 'theplus' ),
		'iconsmind-Spy' => esc_html__( 'Spy', 'theplus' ),
		'iconsmind-Squirrel' => esc_html__( 'Squirrel', 'theplus' ),
		'iconsmind-SSL' => esc_html__( 'SSL', 'theplus' ),
		'iconsmind-St-BasilsCathedral' => esc_html__( 'St-BasilsCathedral', 'theplus' ),
		'iconsmind-St-PaulsCathedral' => esc_html__( 'St-PaulsCathedral', 'theplus' ),
		'iconsmind-Stamp-2' => esc_html__( 'Stamp-2', 'theplus' ),
		'iconsmind-Stamp' => esc_html__( 'Stamp', 'theplus' ),
		'iconsmind-Stapler' => esc_html__( 'Stapler', 'theplus' ),
		'iconsmind-Star-Track' => esc_html__( 'Star-Track', 'theplus' ),
		'iconsmind-Star' => esc_html__( 'Star', 'theplus' ),
		'iconsmind-Starfish' => esc_html__( 'Starfish', 'theplus' ),
		'iconsmind-Start2' => esc_html__( 'Start2', 'theplus' ),
		'iconsmind-Start-3' => esc_html__( 'Start-3', 'theplus' ),
		'iconsmind-Start-ways' => esc_html__( 'Start-ways', 'theplus' ),
		'iconsmind-Start' => esc_html__( 'Start', 'theplus' ),
		'iconsmind-Statistic' => esc_html__( 'Statistic', 'theplus' ),
		'iconsmind-Stethoscope' => esc_html__( 'Stethoscope', 'theplus' ),
		'iconsmind-stop--2' => esc_html__( 'stop--2', 'theplus' ),
		'iconsmind-Stop-Music' => esc_html__( 'Stop-Music', 'theplus' ),
		'iconsmind-Stop' => esc_html__( 'Stop', 'theplus' ),
		'iconsmind-Stopwatch-2' => esc_html__( 'Stopwatch-2', 'theplus' ),
		'iconsmind-Stopwatch' => esc_html__( 'Stopwatch', 'theplus' ),
		'iconsmind-Storm' => esc_html__( 'Storm', 'theplus' ),
		'iconsmind-Street-View' => esc_html__( 'Street-View', 'theplus' ),
		'iconsmind-Street-View2' => esc_html__( 'Street-View2', 'theplus' ),
		'iconsmind-Strikethrough-Text' => esc_html__( 'Strikethrough-Text', 'theplus' ),
		'iconsmind-Stroller' => esc_html__( 'Stroller', 'theplus' ),
		'iconsmind-Structure' => esc_html__( 'Structure', 'theplus' ),
		'iconsmind-Student-Female' => esc_html__( 'Student-Female', 'theplus' ),
		'iconsmind-Student-Hat' => esc_html__( 'Student-Hat', 'theplus' ),
		'iconsmind-Student-Hat2' => esc_html__( 'Student-Hat2', 'theplus' ),
		'iconsmind-Student-Male' => esc_html__( 'Student-Male', 'theplus' ),
		'iconsmind-Student-MaleFemale' => esc_html__( 'Student-MaleFemale', 'theplus' ),
		'iconsmind-Students' => esc_html__( 'Students', 'theplus' ),
		'iconsmind-Studio-Flash' => esc_html__( 'Studio-Flash', 'theplus' ),
		'iconsmind-Studio-Lightbox' => esc_html__( 'Studio-Lightbox', 'theplus' ),
		'iconsmind-Stumbleupon' => esc_html__( 'Stumbleupon', 'theplus' ),
		'iconsmind-Suit' => esc_html__( 'Suit', 'theplus' ),
		'iconsmind-Suitcase' => esc_html__( 'Suitcase', 'theplus' ),
		'iconsmind-Sum-2' => esc_html__( 'Sum-2', 'theplus' ),
		'iconsmind-Sum' => esc_html__( 'Sum', 'theplus' ),
		'iconsmind-Summer' => esc_html__( 'Summer', 'theplus' ),
		'iconsmind-Sun-CloudyRain' => esc_html__( 'Sun-CloudyRain', 'theplus' ),
		'iconsmind-Sun' => esc_html__( 'Sun', 'theplus' ),
		'iconsmind-Sunglasses-2' => esc_html__( 'Sunglasses-2', 'theplus' ),
		'iconsmind-Sunglasses-3' => esc_html__( 'Sunglasses-3', 'theplus' ),
		'iconsmind-Sunglasses-Smiley' => esc_html__( 'Sunglasses-Smiley', 'theplus' ),
		'iconsmind-Sunglasses-Smiley2' => esc_html__( 'Sunglasses-Smiley2', 'theplus' ),
		'iconsmind-Sunglasses-W' => esc_html__( 'Sunglasses-W', 'theplus' ),
		'iconsmind-Sunglasses-W2' => esc_html__( 'Sunglasses-W2', 'theplus' ),
		'iconsmind-Sunglasses-W3' => esc_html__( 'Sunglasses-W3', 'theplus' ),
		'iconsmind-Sunglasses' => esc_html__( 'Sunglasses', 'theplus' ),
		'iconsmind-Sunrise' => esc_html__( 'Sunrise', 'theplus' ),
		'iconsmind-Sunset' => esc_html__( 'Sunset', 'theplus' ),
		'iconsmind-Superman' => esc_html__( 'Superman', 'theplus' ),
		'iconsmind-Support' => esc_html__( 'Support', 'theplus' ),
		'iconsmind-Surprise' => esc_html__( 'Surprise', 'theplus' ),
		'iconsmind-Sushi' => esc_html__( 'Sushi', 'theplus' ),
		'iconsmind-Sweden' => esc_html__( 'Sweden', 'theplus' ),
		'iconsmind-Swimming-Short' => esc_html__( 'Swimming-Short', 'theplus' ),
		'iconsmind-Swimming' => esc_html__( 'Swimming', 'theplus' ),
		'iconsmind-Swimmwear' => esc_html__( 'Swimmwear', 'theplus' ),
		'iconsmind-Switch' => esc_html__( 'Switch', 'theplus' ),
		'iconsmind-Switzerland' => esc_html__( 'Switzerland', 'theplus' ),
		'iconsmind-Sync-Cloud' => esc_html__( 'Sync-Cloud', 'theplus' ),
		'iconsmind-Sync' => esc_html__( 'Sync', 'theplus' ),
		'iconsmind-Synchronize-2' => esc_html__( 'Synchronize-2', 'theplus' ),
		'iconsmind-Synchronize' => esc_html__( 'Synchronize', 'theplus' ),
		'iconsmind-T-Shirt' => esc_html__( 'T-Shirt', 'theplus' ),
		'iconsmind-Tablet-2' => esc_html__( 'Tablet-2', 'theplus' ),
		'iconsmind-Tablet-3' => esc_html__( 'Tablet-3', 'theplus' ),
		'iconsmind-Tablet-Orientation' => esc_html__( 'Tablet-Orientation', 'theplus' ),
		'iconsmind-Tablet-Phone' => esc_html__( 'Tablet-Phone', 'theplus' ),
		'iconsmind-Tablet-Secure' => esc_html__( 'Tablet-Secure', 'theplus' ),
		'iconsmind-Tablet-Vertical' => esc_html__( 'Tablet-Vertical', 'theplus' ),
		'iconsmind-Tablet' => esc_html__( 'Tablet', 'theplus' ),
		'iconsmind-Tactic' => esc_html__( 'Tactic', 'theplus' ),
		'iconsmind-Tag-2' => esc_html__( 'Tag-2', 'theplus' ),
		'iconsmind-Tag-3' => esc_html__( 'Tag-3', 'theplus' ),
		'iconsmind-Tag-4' => esc_html__( 'Tag-4', 'theplus' ),
		'iconsmind-Tag-5' => esc_html__( 'Tag-5', 'theplus' ),
		'iconsmind-Tag' => esc_html__( 'Tag', 'theplus' ),
		'iconsmind-Taj-Mahal' => esc_html__( 'Taj-Mahal', 'theplus' ),
		'iconsmind-Talk-Man' => esc_html__( 'Talk-Man', 'theplus' ),
		'iconsmind-Tap' => esc_html__( 'Tap', 'theplus' ),
		'iconsmind-Target-Market' => esc_html__( 'Target-Market', 'theplus' ),
		'iconsmind-Target' => esc_html__( 'Target', 'theplus' ),
		'iconsmind-Taurus-2' => esc_html__( 'Taurus-2', 'theplus' ),
		'iconsmind-Taurus' => esc_html__( 'Taurus', 'theplus' ),
		'iconsmind-Taxi-2' => esc_html__( 'Taxi-2', 'theplus' ),
		'iconsmind-Taxi-Sign' => esc_html__( 'Taxi-Sign', 'theplus' ),
		'iconsmind-Taxi' => esc_html__( 'Taxi', 'theplus' ),
		'iconsmind-Teacher' => esc_html__( 'Teacher', 'theplus' ),
		'iconsmind-Teapot' => esc_html__( 'Teapot', 'theplus' ),
		'iconsmind-Technorati' => esc_html__( 'Technorati', 'theplus' ),
		'iconsmind-Teddy-Bear' => esc_html__( 'Teddy-Bear', 'theplus' ),
		'iconsmind-Tee-Mug' => esc_html__( 'Tee-Mug', 'theplus' ),
		'iconsmind-Telephone-2' => esc_html__( 'Telephone-2', 'theplus' ),
		'iconsmind-Telephone' => esc_html__( 'Telephone', 'theplus' ),
		'iconsmind-Telescope' => esc_html__( 'Telescope', 'theplus' ),
		'iconsmind-Temperature-2' => esc_html__( 'Temperature-2', 'theplus' ),
		'iconsmind-Temperature-3' => esc_html__( 'Temperature-3', 'theplus' ),
		'iconsmind-Temperature' => esc_html__( 'Temperature', 'theplus' ),
		'iconsmind-Temple' => esc_html__( 'Temple', 'theplus' ),
		'iconsmind-Tennis-Ball' => esc_html__( 'Tennis-Ball', 'theplus' ),
		'iconsmind-Tennis' => esc_html__( 'Tennis', 'theplus' ),
		'iconsmind-Tent' => esc_html__( 'Tent', 'theplus' ),
		'iconsmind-Test-Tube' => esc_html__( 'Test-Tube', 'theplus' ),
		'iconsmind-Test-Tube2' => esc_html__( 'Test-Tube2', 'theplus' ),
		'iconsmind-Testimonal' => esc_html__( 'Testimonal', 'theplus' ),
		'iconsmind-Text-Box' => esc_html__( 'Text-Box', 'theplus' ),
		'iconsmind-Text-Effect' => esc_html__( 'Text-Effect', 'theplus' ),
		'iconsmind-Text-HighlightColor' => esc_html__( 'Text-HighlightColor', 'theplus' ),
		'iconsmind-Text-Paragraph' => esc_html__( 'Text-Paragraph', 'theplus' ),
		'iconsmind-Thailand' => esc_html__( 'Thailand', 'theplus' ),
		'iconsmind-The-WhiteHouse' => esc_html__( 'The-WhiteHouse', 'theplus' ),
		'iconsmind-This-SideUp' => esc_html__( 'This-SideUp', 'theplus' ),
		'iconsmind-Thread' => esc_html__( 'Thread', 'theplus' ),
		'iconsmind-Three-ArrowFork' => esc_html__( 'Three-ArrowFork', 'theplus' ),
		'iconsmind-Three-Fingers' => esc_html__( 'Three-Fingers', 'theplus' ),
		'iconsmind-Three-FingersDrag' => esc_html__( 'Three-FingersDrag', 'theplus' ),
		'iconsmind-Three-FingersDrag2' => esc_html__( 'Three-FingersDrag2', 'theplus' ),
		'iconsmind-Three-FingersTouch' => esc_html__( 'Three-FingersTouch', 'theplus' ),
		'iconsmind-Thumb' => esc_html__( 'Thumb', 'theplus' ),
		'iconsmind-Thumbs-DownSmiley' => esc_html__( 'Thumbs-DownSmiley', 'theplus' ),
		'iconsmind-Thumbs-UpSmiley' => esc_html__( 'Thumbs-UpSmiley', 'theplus' ),
		'iconsmind-Thunder' => esc_html__( 'Thunder', 'theplus' ),
		'iconsmind-Thunderstorm' => esc_html__( 'Thunderstorm', 'theplus' ),
		'iconsmind-Ticket' => esc_html__( 'Ticket', 'theplus' ),
		'iconsmind-Tie-2' => esc_html__( 'Tie-2', 'theplus' ),
		'iconsmind-Tie-3' => esc_html__( 'Tie-3', 'theplus' ),
		'iconsmind-Tie-4' => esc_html__( 'Tie-4', 'theplus' ),
		'iconsmind-Tie' => esc_html__( 'Tie', 'theplus' ),
		'iconsmind-Tiger' => esc_html__( 'Tiger', 'theplus' ),
		'iconsmind-Time-Backup' => esc_html__( 'Time-Backup', 'theplus' ),
		'iconsmind-Time-Bomb' => esc_html__( 'Time-Bomb', 'theplus' ),
		'iconsmind-Time-Clock' => esc_html__( 'Time-Clock', 'theplus' ),
		'iconsmind-Time-Fire' => esc_html__( 'Time-Fire', 'theplus' ),
		'iconsmind-Time-Machine' => esc_html__( 'Time-Machine', 'theplus' ),
		'iconsmind-Time-Window' => esc_html__( 'Time-Window', 'theplus' ),
		'iconsmind-Timer-2' => esc_html__( 'Timer-2', 'theplus' ),
		'iconsmind-Timer' => esc_html__( 'Timer', 'theplus' ),
		'iconsmind-To-Bottom' => esc_html__( 'To-Bottom', 'theplus' ),
		'iconsmind-To-Bottom2' => esc_html__( 'To-Bottom2', 'theplus' ),
		'iconsmind-To-Left' => esc_html__( 'To-Left', 'theplus' ),
		'iconsmind-To-Right' => esc_html__( 'To-Right', 'theplus' ),
		'iconsmind-To-Top' => esc_html__( 'To-Top', 'theplus' ),
		'iconsmind-To-Top2' => esc_html__( 'To-Top2', 'theplus' ),
		'iconsmind-Token' => esc_html__( 'Token', 'theplus' ),
		'iconsmind-Tomato' => esc_html__( 'Tomato', 'theplus' ),
		'iconsmind-Tongue' => esc_html__( 'Tongue', 'theplus' ),
		'iconsmind-Tooth-2' => esc_html__( 'Tooth-2', 'theplus' ),
		'iconsmind-Tooth' => esc_html__( 'Tooth', 'theplus' ),
		'iconsmind-Top-ToBottom' => esc_html__( 'Top-ToBottom', 'theplus' ),
		'iconsmind-Touch-Window' => esc_html__( 'Touch-Window', 'theplus' ),
		'iconsmind-Tourch' => esc_html__( 'Tourch', 'theplus' ),
		'iconsmind-Tower-2' => esc_html__( 'Tower-2', 'theplus' ),
		'iconsmind-Tower-Bridge' => esc_html__( 'Tower-Bridge', 'theplus' ),
		'iconsmind-Tower' => esc_html__( 'Tower', 'theplus' ),
		'iconsmind-Trace' => esc_html__( 'Trace', 'theplus' ),
		'iconsmind-Tractor' => esc_html__( 'Tractor', 'theplus' ),
		'iconsmind-traffic-Light' => esc_html__( 'traffic-Light', 'theplus' ),
		'iconsmind-Traffic-Light2' => esc_html__( 'Traffic-Light2', 'theplus' ),
		'iconsmind-Train-2' => esc_html__( 'Train-2', 'theplus' ),
		'iconsmind-Train' => esc_html__( 'Train', 'theplus' ),
		'iconsmind-Tram' => esc_html__( 'Tram', 'theplus' ),
		'iconsmind-Transform-2' => esc_html__( 'Transform-2', 'theplus' ),
		'iconsmind-Transform-3' => esc_html__( 'Transform-3', 'theplus' ),
		'iconsmind-Transform-4' => esc_html__( 'Transform-4', 'theplus' ),
		'iconsmind-Transform' => esc_html__( 'Transform', 'theplus' ),
		'iconsmind-Trash-withMen' => esc_html__( 'Trash-withMen', 'theplus' ),
		'iconsmind-Tree-2' => esc_html__( 'Tree-2', 'theplus' ),
		'iconsmind-Tree-3' => esc_html__( 'Tree-3', 'theplus' ),
		'iconsmind-Tree-4' => esc_html__( 'Tree-4', 'theplus' ),
		'iconsmind-Tree-5' => esc_html__( 'Tree-5', 'theplus' ),
		'iconsmind-Tree' => esc_html__( 'Tree', 'theplus' ),
		'iconsmind-Trekking' => esc_html__( 'Trekking', 'theplus' ),
		'iconsmind-Triangle-ArrowDown' => esc_html__( 'Triangle-ArrowDown', 'theplus' ),
		'iconsmind-Triangle-ArrowLeft' => esc_html__( 'Triangle-ArrowLeft', 'theplus' ),
		'iconsmind-Triangle-ArrowRight' => esc_html__( 'Triangle-ArrowRight', 'theplus' ),
		'iconsmind-Triangle-ArrowUp' => esc_html__( 'Triangle-ArrowUp', 'theplus' ),
		'iconsmind-Tripod-2' => esc_html__( 'Tripod-2', 'theplus' ),
		'iconsmind-Tripod-andVideo' => esc_html__( 'Tripod-andVideo', 'theplus' ),
		'iconsmind-Tripod-withCamera' => esc_html__( 'Tripod-withCamera', 'theplus' ),
		'iconsmind-Tripod-withGopro' => esc_html__( 'Tripod-withGopro', 'theplus' ),
		'iconsmind-Trophy-2' => esc_html__( 'Trophy-2', 'theplus' ),
		'iconsmind-Trophy' => esc_html__( 'Trophy', 'theplus' ),
		'iconsmind-Truck' => esc_html__( 'Truck', 'theplus' ),
		'iconsmind-Trumpet' => esc_html__( 'Trumpet', 'theplus' ),
		'iconsmind-Tumblr' => esc_html__( 'Tumblr', 'theplus' ),
		'iconsmind-Turkey' => esc_html__( 'Turkey', 'theplus' ),
		'iconsmind-Turn-Down' => esc_html__( 'Turn-Down', 'theplus' ),
		'iconsmind-Turn-Down2' => esc_html__( 'Turn-Down2', 'theplus' ),
		'iconsmind-Turn-DownFromLeft' => esc_html__( 'Turn-DownFromLeft', 'theplus' ),
		'iconsmind-Turn-DownFromRight' => esc_html__( 'Turn-DownFromRight', 'theplus' ),
		'iconsmind-Turn-Left' => esc_html__( 'Turn-Left', 'theplus' ),
		'iconsmind-Turn-Left3' => esc_html__( 'Turn-Left3', 'theplus' ),
		'iconsmind-Turn-Right' => esc_html__( 'Turn-Right', 'theplus' ),
		'iconsmind-Turn-Right3' => esc_html__( 'Turn-Right3', 'theplus' ),
		'iconsmind-Turn-Up' => esc_html__( 'Turn-Up', 'theplus' ),
		'iconsmind-Turn-Up2' => esc_html__( 'Turn-Up2', 'theplus' ),
		'iconsmind-Turtle' => esc_html__( 'Turtle', 'theplus' ),
		'iconsmind-Tuxedo' => esc_html__( 'Tuxedo', 'theplus' ),
		'iconsmind-TV' => esc_html__( 'TV', 'theplus' ),
		'iconsmind-Twister' => esc_html__( 'Twister', 'theplus' ),
		'iconsmind-Twitter-2' => esc_html__( 'Twitter-2', 'theplus' ),
		'iconsmind-Twitter' => esc_html__( 'Twitter', 'theplus' ),
		'iconsmind-Two-Fingers' => esc_html__( 'Two-Fingers', 'theplus' ),
		'iconsmind-Two-FingersDrag' => esc_html__( 'Two-FingersDrag', 'theplus' ),
		'iconsmind-Two-FingersDrag2' => esc_html__( 'Two-FingersDrag2', 'theplus' ),
		'iconsmind-Two-FingersScroll' => esc_html__( 'Two-FingersScroll', 'theplus' ),
		'iconsmind-Two-FingersTouch' => esc_html__( 'Two-FingersTouch', 'theplus' ),
		'iconsmind-Two-Windows' => esc_html__( 'Two-Windows', 'theplus' ),
		'iconsmind-Type-Pass' => esc_html__( 'Type-Pass', 'theplus' ),
		'iconsmind-Ukraine' => esc_html__( 'Ukraine', 'theplus' ),
		'iconsmind-Umbrela' => esc_html__( 'Umbrela', 'theplus' ),
		'iconsmind-Umbrella-2' => esc_html__( 'Umbrella-2', 'theplus' ),
		'iconsmind-Umbrella-3' => esc_html__( 'Umbrella-3', 'theplus' ),
		'iconsmind-Under-LineText' => esc_html__( 'Under-LineText', 'theplus' ),
		'iconsmind-Undo' => esc_html__( 'Undo', 'theplus' ),
		'iconsmind-United-Kingdom' => esc_html__( 'United-Kingdom', 'theplus' ),
		'iconsmind-United-States' => esc_html__( 'United-States', 'theplus' ),
		'iconsmind-University-2' => esc_html__( 'University-2', 'theplus' ),
		'iconsmind-University' => esc_html__( 'University', 'theplus' ),
		'iconsmind-Unlike-2' => esc_html__( 'Unlike-2', 'theplus' ),
		'iconsmind-Unlike' => esc_html__( 'Unlike', 'theplus' ),
		'iconsmind-Unlock-2' => esc_html__( 'Unlock-2', 'theplus' ),
		'iconsmind-Unlock-3' => esc_html__( 'Unlock-3', 'theplus' ),
		'iconsmind-Unlock' => esc_html__( 'Unlock', 'theplus' ),
		'iconsmind-Up--Down' => esc_html__( 'Up--Down', 'theplus' ),
		'iconsmind-Up--Down3' => esc_html__( 'Up--Down3', 'theplus' ),
		'iconsmind-Up-2' => esc_html__( 'Up-2', 'theplus' ),
		'iconsmind-Up-3' => esc_html__( 'Up-3', 'theplus' ),
		'iconsmind-Up-4' => esc_html__( 'Up-4', 'theplus' ),
		'iconsmind-Up' => esc_html__( 'Up', 'theplus' ),
		'iconsmind-Upgrade' => esc_html__( 'Upgrade', 'theplus' ),
		'iconsmind-Upload-2' => esc_html__( 'Upload-2', 'theplus' ),
		'iconsmind-Upload-toCloud' => esc_html__( 'Upload-toCloud', 'theplus' ),
		'iconsmind-Upload-Window' => esc_html__( 'Upload-Window', 'theplus' ),
		'iconsmind-Upload' => esc_html__( 'Upload', 'theplus' ),
		'iconsmind-Uppercase-Text' => esc_html__( 'Uppercase-Text', 'theplus' ),
		'iconsmind-Upward' => esc_html__( 'Upward', 'theplus' ),
		'iconsmind-URL-Window' => esc_html__( 'URL-Window', 'theplus' ),
		'iconsmind-Usb-2' => esc_html__( 'Usb-2', 'theplus' ),
		'iconsmind-Usb-Cable' => esc_html__( 'Usb-Cable', 'theplus' ),
		'iconsmind-Usb' => esc_html__( 'Usb', 'theplus' ),
		'iconsmind-User' => esc_html__( 'User', 'theplus' ),
		'iconsmind-Ustream' => esc_html__( 'Ustream', 'theplus' ),
		'iconsmind-Vase' => esc_html__( 'Vase', 'theplus' ),
		'iconsmind-Vector-2' => esc_html__( 'Vector-2', 'theplus' ),
		'iconsmind-Vector-3' => esc_html__( 'Vector-3', 'theplus' ),
		'iconsmind-Vector-4' => esc_html__( 'Vector-4', 'theplus' ),
		'iconsmind-Vector-5' => esc_html__( 'Vector-5', 'theplus' ),
		'iconsmind-Vector' => esc_html__( 'Vector', 'theplus' ),
		'iconsmind-Venn-Diagram' => esc_html__( 'Venn-Diagram', 'theplus' ),
		'iconsmind-Vest-2' => esc_html__( 'Vest-2', 'theplus' ),
		'iconsmind-Vest' => esc_html__( 'Vest', 'theplus' ),
		'iconsmind-Viddler' => esc_html__( 'Viddler', 'theplus' ),
		'iconsmind-Video-2' => esc_html__( 'Video-2', 'theplus' ),
		'iconsmind-Video-3' => esc_html__( 'Video-3', 'theplus' ),
		'iconsmind-Video-4' => esc_html__( 'Video-4', 'theplus' ),
		'iconsmind-Video-5' => esc_html__( 'Video-5', 'theplus' ),
		'iconsmind-Video-6' => esc_html__( 'Video-6', 'theplus' ),
		'iconsmind-Video-GameController' => esc_html__( 'Video-GameController', 'theplus' ),
		'iconsmind-Video-Len' => esc_html__( 'Video-Len', 'theplus' ),
		'iconsmind-Video-Len2' => esc_html__( 'Video-Len2', 'theplus' ),
		'iconsmind-Video-Photographer' => esc_html__( 'Video-Photographer', 'theplus' ),
		'iconsmind-Video-Tripod' => esc_html__( 'Video-Tripod', 'theplus' ),
		'iconsmind-Video' => esc_html__( 'Video', 'theplus' ),
		'iconsmind-Vietnam' => esc_html__( 'Vietnam', 'theplus' ),
		'iconsmind-View-Height' => esc_html__( 'View-Height', 'theplus' ),
		'iconsmind-View-Width' => esc_html__( 'View-Width', 'theplus' ),
		'iconsmind-Vimeo' => esc_html__( 'Vimeo', 'theplus' ),
		'iconsmind-Virgo-2' => esc_html__( 'Virgo-2', 'theplus' ),
		'iconsmind-Virgo' => esc_html__( 'Virgo', 'theplus' ),
		'iconsmind-Virus-2' => esc_html__( 'Virus-2', 'theplus' ),
		'iconsmind-Virus-3' => esc_html__( 'Virus-3', 'theplus' ),
		'iconsmind-Virus' => esc_html__( 'Virus', 'theplus' ),
		'iconsmind-Visa' => esc_html__( 'Visa', 'theplus' ),
		'iconsmind-Voice' => esc_html__( 'Voice', 'theplus' ),
		'iconsmind-Voicemail' => esc_html__( 'Voicemail', 'theplus' ),
		'iconsmind-Volleyball' => esc_html__( 'Volleyball', 'theplus' ),
		'iconsmind-Volume-Down' => esc_html__( 'Volume-Down', 'theplus' ),
		'iconsmind-Volume-Up' => esc_html__( 'Volume-Up', 'theplus' ),
		'iconsmind-VPN' => esc_html__( 'VPN', 'theplus' ),
		'iconsmind-Wacom-Tablet' => esc_html__( 'Wacom-Tablet', 'theplus' ),
		'iconsmind-Waiter' => esc_html__( 'Waiter', 'theplus' ),
		'iconsmind-Walkie-Talkie' => esc_html__( 'Walkie-Talkie', 'theplus' ),
		'iconsmind-Wallet-2' => esc_html__( 'Wallet-2', 'theplus' ),
		'iconsmind-Wallet-3' => esc_html__( 'Wallet-3', 'theplus' ),
		'iconsmind-Wallet' => esc_html__( 'Wallet', 'theplus' ),
		'iconsmind-Warehouse' => esc_html__( 'Warehouse', 'theplus' ),
		'iconsmind-Warning-Window' => esc_html__( 'Warning-Window', 'theplus' ),
		'iconsmind-Watch-2' => esc_html__( 'Watch-2', 'theplus' ),
		'iconsmind-Watch-3' => esc_html__( 'Watch-3', 'theplus' ),
		'iconsmind-Watch' => esc_html__( 'Watch', 'theplus' ),
		'iconsmind-Wave-2' => esc_html__( 'Wave-2', 'theplus' ),
		'iconsmind-Wave' => esc_html__( 'Wave', 'theplus' ),
		'iconsmind-Webcam' => esc_html__( 'Webcam', 'theplus' ),
		'iconsmind-weight-Lift' => esc_html__( 'weight-Lift', 'theplus' ),
		'iconsmind-Wheelbarrow' => esc_html__( 'Wheelbarrow', 'theplus' ),
		'iconsmind-Wheelchair' => esc_html__( 'Wheelchair', 'theplus' ),
		'iconsmind-Width-Window' => esc_html__( 'Width-Window', 'theplus' ),
		'iconsmind-Wifi-2' => esc_html__( 'Wifi-2', 'theplus' ),
		'iconsmind-Wifi-Keyboard' => esc_html__( 'Wifi-Keyboard', 'theplus' ),
		'iconsmind-Wifi' => esc_html__( 'Wifi', 'theplus' ),
		'iconsmind-Wind-Turbine' => esc_html__( 'Wind-Turbine', 'theplus' ),
		'iconsmind-Windmill' => esc_html__( 'Windmill', 'theplus' ),
		'iconsmind-Window-2' => esc_html__( 'Window-2', 'theplus' ),
		'iconsmind-Window' => esc_html__( 'Window', 'theplus' ),
		'iconsmind-Windows-2' => esc_html__( 'Windows-2', 'theplus' ),
		'iconsmind-Windows-Microsoft' => esc_html__( 'Windows-Microsoft', 'theplus' ),
		'iconsmind-Windows' => esc_html__( 'Windows', 'theplus' ),
		'iconsmind-Windsock' => esc_html__( 'Windsock', 'theplus' ),
		'iconsmind-Windy' => esc_html__( 'Windy', 'theplus' ),
		'iconsmind-Wine-Bottle' => esc_html__( 'Wine-Bottle', 'theplus' ),
		'iconsmind-Wine-Glass' => esc_html__( 'Wine-Glass', 'theplus' ),
		'iconsmind-Wink' => esc_html__( 'Wink', 'theplus' ),
		'iconsmind-Winter-2' => esc_html__( 'Winter-2', 'theplus' ),
		'iconsmind-Winter' => esc_html__( 'Winter', 'theplus' ),
		'iconsmind-Wireless' => esc_html__( 'Wireless', 'theplus' ),
		'iconsmind-Witch-Hat' => esc_html__( 'Witch-Hat', 'theplus' ),
		'iconsmind-Witch' => esc_html__( 'Witch', 'theplus' ),
		'iconsmind-Wizard' => esc_html__( 'Wizard', 'theplus' ),
		'iconsmind-Wolf' => esc_html__( 'Wolf', 'theplus' ),
		'iconsmind-Woman-Sign' => esc_html__( 'Woman-Sign', 'theplus' ),
		'iconsmind-WomanMan' => esc_html__( 'WomanMan', 'theplus' ),
		'iconsmind-Womans-Underwear' => esc_html__( 'Womans-Underwear', 'theplus' ),
		'iconsmind-Womans-Underwear2' => esc_html__( 'Womans-Underwear2', 'theplus' ),
		'iconsmind-Women' => esc_html__( 'Women', 'theplus' ),
		'iconsmind-Wonder-Woman' => esc_html__( 'Wonder-Woman', 'theplus' ),
		'iconsmind-Wordpress' => esc_html__( 'Wordpress', 'theplus' ),
		'iconsmind-Worker-Clothes' => esc_html__( 'Worker-Clothes', 'theplus' ),
		'iconsmind-Worker' => esc_html__( 'Worker', 'theplus' ),
		'iconsmind-Wrap-Text' => esc_html__( 'Wrap-Text', 'theplus' ),
		'iconsmind-Wreath' => esc_html__( 'Wreath', 'theplus' ),
		'iconsmind-Wrench' => esc_html__( 'Wrench', 'theplus' ),
		'iconsmind-X-Box' => esc_html__( 'X-Box', 'theplus' ),
		'iconsmind-X-ray' => esc_html__( 'X-ray', 'theplus' ),
		'iconsmind-Xanga' => esc_html__( 'Xanga', 'theplus' ),
		'iconsmind-Xing' => esc_html__( 'Xing', 'theplus' ),
		'iconsmind-Yacht' => esc_html__( 'Yacht', 'theplus' ),
		'iconsmind-Yahoo-Buzz' => esc_html__( 'Yahoo-Buzz', 'theplus' ),
		'iconsmind-Yahoo' => esc_html__( 'Yahoo', 'theplus' ),
		'iconsmind-Yelp' => esc_html__( 'Yelp', 'theplus' ),
		'iconsmind-Yes' => esc_html__( 'Yes', 'theplus' ),
		'iconsmind-Ying-Yang' => esc_html__( 'Ying-Yang', 'theplus' ),
		'iconsmind-Youtube' => esc_html__( 'Youtube', 'theplus' ),
		'iconsmind-Z-A' => esc_html__( 'Z-A', 'theplus' ),
		'iconsmind-Zebra' => esc_html__( 'Zebra', 'theplus' ),
		'iconsmind-Zombie' => esc_html__( 'Zombie', 'theplus' ),
		'iconsmind-Zoom-Gesture' => esc_html__( 'Zoom-Gesture', 'theplus' ),
		'iconsmind-Zootool' => esc_html__( 'Zootool', 'theplus' ),
	);
}
/*Breadcrumbs Bar*/
function theplus_breadcrumbs($icontype='',$sep_icontype='',$icons='',$home_titles='',$sep_icons='',$active_page_text_default='',$breadcrumbs_last_sec_tri_normal='',$breadcrumbs_on_off_home='',$breadcrumbs_on_off_parent='',$breadcrumbs_on_off_current='',$letter_limit_parent='',$letter_limit_current='') {
		
		if($home_titles != ''){
			$text['home'] =$home_titles;
		}else {
			$text['home']     = ''; 
		}
		$text['category'] = esc_html__('Archive by "%s"', 'theplus'); 
		$text['category1'] = esc_html__('%s', 'theplus'); 
		$text['search']   = esc_html__('Search Results for "%s"', 'theplus');
		$text['tag']      = esc_html__('Posts Tagged "%s"', 'theplus');
		$text['author']   = esc_html__('Articles Posted by %s', 'theplus');
		$text['404']      = esc_html__('Error 404', 'theplus');
		$showCurrent = 1; 
		$showOnHome  = 1; 
		$delimiter   = ' <span class="del"></span> '; 
		
		if($breadcrumbs_on_off_current == 'on-off-current'){
			if($breadcrumbs_last_sec_tri_normal != ''){
				
				if($active_page_text_default != ''){
					 $before      = '<span class="current_active normal"><div class="current_tab_sec">';
				}else {
					 $before      = '<span class="current normal"><div class="current_tab_sec">'; 
				}
			}else {
				if($active_page_text_default != ''){
					 $before      = '<span class="current_active"><div class="current_tab_sec">';
				}else {
					 $before      = '<span class="current"><div class="current_tab_sec">'; 
				}
			}
		}else{
			if($breadcrumbs_last_sec_tri_normal != ''){
				
				if($active_page_text_default != ''){
					 $before      = '<span class="current_active normal on-off-current"><div class="current_tab_sec">';
				}else {
					 $before      = '<span class="current normal on-off-current"><div class="current_tab_sec">'; 
				}
			}else {
				if($active_page_text_default != ''){
					 $before      = '<span class="current_active on-off-current"><div class="current_tab_sec">';
				}else {
					 $before      = '<span class="current on-off-current"><div class="current_tab_sec">'; 
				}
			}			
		}
	   
		$after       = '</div></span>';
		
		$icons_content ='';
		if($icontype=='icon' && $icons != ''){
			$icons_content = '<i class=" '.esc_attr($icons).' bread-home-icon" ></i>';
		}
		
		if($icontype=='image' && $icons != ''){
			$icons_content = '<img class="bread-home-img" src="'.esc_attr($icons).'" />';
		}
		
		$icons_sep_content ='';
		if($sep_icontype=='sep_icon' && $sep_icons != ''){
				$icons_sep_content = '<i class=" '.esc_attr($sep_icons).' bread-sep-icon" ></i>';
		}
		
		if($sep_icontype=='sep_image' && $sep_icons != ''){
			$icons_sep_content = '<img class="bread-sep-icon" src="'.esc_attr($sep_icons).'" />';		
		}
		
		global $post;
		$homeLink = home_url() . '/';
		$linkBefore = '<span>';
		$linkAfter = '</span>';
		if($icons_content != '' || $icons_sep_content != '' ||  $text['home'] != ''){
			if($breadcrumbs_on_off_home != '' && $breadcrumbs_on_off_home='yes'){
				$home_link = '<span class="bc_home"><a class="home_bread_tab" href="%1$s">'.$icons_content.'%2$s'.$icons_sep_content.'</a>' . $linkAfter;
			}else {
				$home_link = '';
			}
			 $home_delimiter   = ' <span class="del"></span> ';
		}else {
			$home_link = $home_delimiter = '';
		}
	   if($breadcrumbs_on_off_parent != '' && $breadcrumbs_on_off_parent='yes'){			
			$link = '<span class="bc_parent"><a class="parent_sub_bread_tab" href="%1$s">%2$s'.$icons_sep_content.'</a>' . $linkAfter;
	   }else {			
			$link = '';
	   }
		
		
		if (is_home() || is_front_page()) {
			if ($showOnHome == 1) $crumbs_output = '<nav id="breadcrumbs"><a href="' . esc_url(home_url()) . '">'.$icons_content . esc_html($text['home']) . '</a></nav>';
		} else {
			$crumbs_output ='<nav id="breadcrumbs">' . sprintf($home_link, $homeLink, $text['home']) . $home_delimiter;
			if ( is_category() ) {
				$thisCat = get_category(get_query_var('cat'), false);
				if ($thisCat->parent != 0) {
					$cats = get_category_parents($thisCat->parent, TRUE, $delimiter);
					$cats = str_replace('<a', $linkBefore . '<a', $cats);
					$cats = str_replace('</a>', $icons_sep_content.'</a>' . $linkAfter, $cats);
					$crumbs_output .= $cats;
				}
				
				if ($thisCat->parent != 0){
					$crumbs_output .= $before . sprintf($text['category1'], single_cat_title('', false)) . $after;
				}else{
					$crumbs_output .= $before . sprintf($text['category'], single_cat_title('', false)) . $after;
				}
				
			} elseif ( is_search() ) {
				$crumbs_output .= $before . sprintf($text['search'], get_search_query()) . $after;
			}
			elseif (is_singular('topic') ){
				$post_type = get_post_type_object(get_post_type());
				printf($link, $homeLink . '/forums/', $post_type->labels->singular_name);
			}
			/* in forum, add link to support forum page template */
			elseif (is_singular('forum')){
				$post_type = get_post_type_object(get_post_type());
				printf($link, $homeLink . '/forums/', $post_type->labels->singular_name);
			}
			elseif (is_tax('topic-tag')){
				$post_type = get_post_type_object(get_post_type());
				printf($link, $homeLink . '/forums/', $post_type->labels->singular_name);
			}
			elseif ( is_day() ) {
				$crumbs_output .= sprintf($link, get_year_link(get_the_time('Y')), get_the_time('Y')) . $delimiter;
				$crumbs_output .= sprintf($link, get_month_link(get_the_time('Y'),get_the_time('m')), get_the_time('F')) . $delimiter;
				$crumbs_output .= $before . esc_html(get_the_time('d')) . $after;
			} elseif ( is_month() ) {
				$crumbs_output .= sprintf($link, get_year_link(get_the_time('Y')), get_the_time('Y')) . $delimiter;
				$crumbs_output .= $before . esc_html(get_the_time('F')) . $after;
			} elseif ( is_year() ) {
				$crumbs_output .= $before . esc_html(get_the_time('Y')) . $after;
			} elseif ( is_single() && !is_attachment() ) {
				if ( 'product' === get_post_type( $post ) ) {
					
					$terms_cate = wc_get_product_terms(
						$post->ID,
						'product_cat',
						apply_filters(
							'woocommerce_breadcrumb_product_terms_args',
							array(
								'orderby' => 'parent',
								'order'   => 'DESC',
							)
						)
					);

					if ( $terms_cate ) {
						$first_term = apply_filters( 'woocommerce_breadcrumb_main_term', $terms_cate[0], $terms_cate );
						$ancestors = get_ancestors( $first_term->term_id, 'product_cat' );
						$ancestors = array_reverse( $ancestors );

						foreach ( $ancestors as $ancestor ) {
							$ancestor = get_term( $ancestor, 'product_cat' );

							if ( ! is_wp_error( $ancestor ) && $ancestor ) {
								$crumbs_output .= sprintf($link, get_term_link( $ancestor ), $ancestor->name);
							}
						}
						if($breadcrumbs_on_off_current == 'on-off-current'){
							$crumbs_output .= sprintf($link, get_term_link( $first_term ), $first_term->name);
						}else{
							$crumbs_output .= $linkBefore . '<a href="'.esc_url(get_term_link( $first_term )). '">'.esc_html($first_term->name).'</a>' . $linkAfter;
						}
					}
					
					if($letter_limit_current != '0'){
						if ($showCurrent == 1) $crumbs_output .= $delimiter . $before .substr(get_the_title(),0,$letter_limit_current). $after;
					}else{
						if ($showCurrent == 1) $crumbs_output .= $delimiter . $before .get_the_title(). $after;
					}
				}else if ( get_post_type() != 'post' ) {
					$post_type = get_post_type_object(get_post_type());
					$slug = $post_type->rewrite;
					//$crumbs_output .= $linkBefore . '<a href="'.esc_url($homeLink). '?post_type=' . esc_attr($slug["slug"]) . '">'.esc_html($post_type->labels->singular_name).'</a>' . $linkAfter;
					
					$crumbs_output .= $linkBefore . '<a href="'.esc_url($homeLink). '?post_type=' . esc_attr(!empty($slug["slug"]) ? $slug["slug"] : '') . '">'.esc_html($post_type->labels->singular_name).'</a>' . $linkAfter;
					if($letter_limit_current != '0'){
						if ($showCurrent == 1) $crumbs_output .= $delimiter . $before .substr(get_the_title(),0,$letter_limit_current). $after;
					}else{
						if ($showCurrent == 1) $crumbs_output .= $delimiter . $before .get_the_title(). $after;
					}
				} else {
					$cat = get_the_category();
					if(isset($cat[0])) {
						$cat =  $cat[0];
						$cats = get_category_parents($cat, TRUE, $delimiter);
						if ($showCurrent == 0) $cats = preg_replace("#^(.+)$delimiter$#", "$1", $cats);
						$cats = str_replace('<a', $linkBefore . '<a', $cats);
						$cats = str_replace('</a>', $icons_sep_content.'</a>' . $linkAfter, $cats);						
						if($breadcrumbs_on_off_parent != '' && $breadcrumbs_on_off_parent='yes'){
							$crumbs_output .= $cats;
						}else{
							$crumbs_output .='';
						}						
						
						if($letter_limit_current != '0'){
							if ($showCurrent == 1) $crumbs_output .= $before . substr(get_the_title(),0,$letter_limit_current) . $after;
						}else{
							if ($showCurrent == 1) $crumbs_output .= $before . get_the_title() . $after;
						}
					}
				}
			} elseif ( class_exists('WooCommerce') && is_product_category() ){
				
				$current_term = $GLOBALS['wp_query']->get_queried_object();
				
				$permalinks   = wc_get_permalink_structure();
				$shop_page_id = wc_get_page_id( 'shop' );
				$shop_page    = get_post( $shop_page_id );

				// If permalinks contain the shop page in the URI prepend the breadcrumb with shop.
				if ( $shop_page_id && $shop_page && isset( $permalinks['product_base'] ) && strstr( $permalinks['product_base'], '/' . $shop_page->post_name ) && intval( get_option( 'page_on_front' ) ) !== $shop_page_id ) {
					$crumbs_output .= sprintf($link, get_permalink( $shop_page ), get_the_title( $shop_page ));
				}

				if($breadcrumbs_on_off_parent != '' && $breadcrumbs_on_off_parent='yes') {

					$ancestors = get_ancestors( $current_term->term_id, 'product_cat' );
					$ancestors = array_reverse( $ancestors );

					$link = '<span class="bc_parent"><a class="parent_sub_bread_tab" href="%1$s">%2$s'.$icons_sep_content.'</a>' . $linkAfter;

					foreach ( $ancestors as $ancestor ) {
						$ancestor = get_term( $ancestor,'product_cat' );

						
						if ( ! is_wp_error( $ancestor ) && $ancestor ) {
							$crumbs_output .= sprintf($link, get_term_link( $ancestor ), $ancestor->name);
						}
					}
					
				}

				if($current_term && $breadcrumbs_on_off_current == 'on-off-current'){
					$crumbs_output .= '<span class="current_active normal"><div class="current_tab_sec">'. esc_html($current_term->name) . '</div></span>';
				}
				
			} elseif ( class_exists('WooCommerce') && is_product_tag() ){
				
				$current_term = $GLOBALS['wp_query']->get_queried_object();
				
				$shop_page_id = wc_get_page_id( 'shop' );
				$shop_page    = get_post( $shop_page_id );

				// If permalinks contain the shop page in the URI prepend the breadcrumb with shop.
				if ( $shop_page_id && $shop_page && isset( $permalinks['product_base'] ) && strstr( $permalinks['product_base'], '/' . $shop_page->post_name ) && intval( get_option( 'page_on_front' ) ) !== $shop_page_id ) {
					$crumbs_output .= sprintf($link, get_permalink( $shop_page ), get_the_title( $shop_page ));
				}

				if($current_term && $breadcrumbs_on_off_current == 'on-off-current'){
					$crumbs_output .= '<span class="current_active normal"><div class="current_tab_sec">'. esc_html($current_term->name) . '</div></span>';
				}
				
			} elseif ( class_exists('WooCommerce') && is_shop()){
				
				if ( intval( get_option( 'page_on_front' ) ) === wc_get_page_id( 'shop' ) ) {					
					return;
				}
		
				$_name = wc_get_page_id( 'shop' ) ? get_the_title( wc_get_page_id( 'shop' ) ) : '';
				
				if ( ! $_name ) {
					$product_post_type = get_post_type_object( 'product' );
					$_name             = $product_post_type->labels->name;
				}
				
				//$this->add_crumb( $_name, get_post_type_archive_link( 'product' ) );
				if($breadcrumbs_on_off_current == 'on-off-current'){
					$crumbs_output .= '<span class="current_active normal "><div class="current_tab_sec">'. esc_html($_name  ) . '</div></span>';
				}
			} elseif ( !is_single() && !is_page() && get_post_type() != 'post' && !is_404() ) {
				$post_type = get_post_type_object(get_post_type());
				$crumbs_output .= $before . esc_html($post_type->labels->singular_name) . $after;
			} elseif ( is_attachment() ) {
				$parent = get_post($post->post_parent);
				$cat = get_the_category($parent->ID);
				if($cat) {
					$cat = $cat[0];
					$cats = get_category_parents($cat, TRUE, $delimiter);
					$cats = str_replace('<a', $linkBefore . '<a', $cats);
					$cats = str_replace('</a>', $icons_sep_content.'</a>' . $linkAfter, $cats);
					$crumbs_output .= $cats;
					printf($link, get_permalink($parent), $parent->post_title);
					if ($showCurrent == 1) $crumbs_output .= $delimiter . $before . esc_html(get_the_title()) . $after;
				}
			} elseif ( is_page() && !$post->post_parent ) {
				if ($showCurrent == 1) $crumbs_output .= $before . esc_html(get_the_title()) . $after;
			} elseif ( is_page() && $post->post_parent ) {
				$parent_id  = $post->post_parent;
				$breadcrumbs = array();
				while ($parent_id) {
					$page = get_page($parent_id);
					$breadcrumbs[] = sprintf($link, get_permalink($page->ID), get_the_title($page->ID));
					$parent_id  = $page->post_parent;
				}
				$breadcrumbs = array_reverse($breadcrumbs);
				for ($i = 0; $i < count($breadcrumbs); $i++) {
					$crumbs_output .= $breadcrumbs[$i];
					if ($i != count($breadcrumbs)-1) $crumbs_output .= $delimiter;
				}
				if ($showCurrent == 1) $crumbs_output .= $delimiter . $before . esc_html(get_the_title()) . $after;
			} elseif ( is_tag() ) {
				$crumbs_output .= $before . sprintf($text['tag'], single_tag_title('', false)) . $after;
			} elseif ( is_author() ) {
				global $author;
				$userdata = get_userdata($author);
				$crumbs_output .= $before . sprintf($text['author'], $userdata->display_name) . $after;
			} elseif ( is_404() ) {
				$crumbs_output .= $before . $text['404'] . $after;
			}
			if ( get_query_var('paged') ) {
				if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) $crumbs_output .= ' (';
				$crumbs_output .= '<span class="del"></span>'.esc_html__('Page', 'theplus') . ' ' . get_query_var('paged');
				if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) $crumbs_output .= ')';
			}
			$crumbs_output .= '</nav>';
		}
	return $crumbs_output;
}
/*Breadcrumbs Bar*/