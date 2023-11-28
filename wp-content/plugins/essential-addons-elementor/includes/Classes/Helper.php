<?php

namespace Essential_Addons_Elementor\Pro\Classes;

use Elementor\Plugin;

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

class Helper extends \Essential_Addons_Elementor\Classes\Helper
{
	use \Essential_Addons_Elementor\Pro\Traits\Dynamic_Filterable_Gallery;

	const EAEL_PRO_ALLOWED_HTML_TAGS = [
		'article',
		'aside',
		'div',
		'footer',
		'h1',
		'h2',
		'h3',
		'h4',
		'h5',
		'h6',
		'header',
		'main',
		'nav',
		'p',
		'section',
		'span',
	];
    /**
     * Get all product tags
     *
     * @return array
     */
    public static function get_woo_product_tags()
    {
        if (!apply_filters('eael/is_plugin_active', 'woocommerce/woocommerce.php')) {
            return [];
        }

        $options = [];
        $tags = get_terms('product_tag', array('hide_empty' => true));
        if (!empty($tags) && !is_wp_error($tags)) {
            foreach ($tags as $tag) {
                $options[$tag->term_id] = $tag->name;
            }
        }
        return $options;
    }

    /**
     * Get all product attributes
     *
     * @return array
     */
    public static function get_woo_product_atts()
    {
	    if ( ! apply_filters( 'eael/is_plugin_active', 'woocommerce/woocommerce.php' ) || ! function_exists( 'wc_get_attribute_taxonomies' ) ) {
		    return [];
	    }

        $options = [];
        $taxonomies = wc_get_attribute_taxonomies();

        foreach ($taxonomies as $tax) {
            $terms = get_terms('pa_' . $tax->attribute_name);

            if (!empty($terms)) {
                foreach ($terms as $term) {
                    $options[$term->term_id] = $tax->attribute_label . ': ' . $term->name;
                }
            }
        }

        return $options;
    }

    /**
     * Get all registered menus.
     *
     * @return array of menus.
     */
    public static function get_menus()
    {
        $menus = wp_get_nav_menus();
        $options = [];

        if (empty($menus)) {
            return $options;
        }

        foreach ($menus as $menu) {
            $options[$menu->term_id] = $menu->name;
        }

        return $options;
    }

    public static function user_roles()
    {
        global $wp_roles;

        $all = $wp_roles->roles;
        $all_roles = array();

        if (!empty($all)) {
            foreach ($all as $key => $value) {
                $all_roles[$key] = $all[$key]['name'];
            }
        }

        return $all_roles;
    }

    public static function get_page_template_options($type = '')
    {
        $page_templates = self::get_elementor_templates($type);

        $options[-1] = __('Select', 'essential-addons-elementor');

        if (count($page_templates)) {
            foreach ($page_templates as $id => $name) {
                $options[$id] = $name;
            }
        } else {
            $options['no_template'] = __('No saved templates found!', 'essential-addons-elementor');
        }

        return $options;
    }

    // Get all WordPress registered widgets
    public static function get_registered_sidebars()
    {
        global $wp_registered_sidebars;
        $options = [];

        if (!$wp_registered_sidebars) {
            $options[''] = __('No sidebars were found', 'essential-addons-elementor');
        } else {
            $options['---'] = __('Choose Sidebar', 'essential-addons-elementor');

            foreach ($wp_registered_sidebars as $sidebar_id => $sidebar) {
                $options[$sidebar_id] = $sidebar['name'];
            }
        }
        return $options;
    }

    // Get Mailchimp list
	public static function mailchimp_lists( $element = 'mailchimp', $type_double_optin = false ) {
		$lists   = $lists_double_optin = [];
		$api_key = get_option( 'eael_save_mailchimp_api' );

		if ( $element === 'login-register-form' ) {
			$api_key = get_option( 'eael_lr_mailchimp_api_key' );
		}

		if ( empty( $api_key ) ) {
			return $lists;
		}

        $pattern = '/^[0-9a-z]{32}(-us)(0?[1-9]|[1-9][0-9])?$/';
		if ( ! preg_match( $pattern, $api_key ) ) {
			return $lists;
		}

		$response = wp_safe_remote_get( 'https://' . substr( $api_key,
				strpos( $api_key, '-' ) + 1 ) . '.api.mailchimp.com/3.0/lists/?fields=lists.id,lists.name,lists.double_optin&count=1000', [
			'headers' => [
				'Content-Type'  => 'application/json',
				'Authorization' => 'Basic ' . base64_encode( 'user:' . $api_key ),
			],
		] );

		if ( ! is_wp_error( $response ) ) {
			$response = json_decode( wp_remote_retrieve_body( $response ) );

			if ( ! empty( $response ) && ! empty( $response->lists ) ) {
				$lists[''] = __( 'Select One', 'essential-addons-for-elementor-lite' );

				for ( $i = 0; $i < count( $response->lists ); $i ++ ) {
					$lists[ $response->lists[ $i ]->id ]              = $response->lists[ $i ]->name;
					$lists[ $response->lists[ $i ]->id ]              = $response->lists[ $i ]->name;
					$lists_double_optin[ $response->lists[ $i ]->id ] = $response->lists[ $i ]->double_optin;
				}
			}
		}

		return $type_double_optin ? $lists_double_optin : $lists;
	}

    public static function list_db_tables()
    {
        global $wpdb;

        $result = [];
        $tables = $wpdb->get_results('show tables', ARRAY_N);

        if ($tables) {
            $tables = wp_list_pluck($tables, 0);

            foreach ($tables as $table) {
                $result[$table] = $table;
            }
        }

        return $result;
    }

	public static function list_tablepress_tables() {
		if ( empty( \TablePress::$model_table ) ) {
			return [];
		}

		$result = [];
		$tables = \TablePress::$model_table->load_all( true );

		if ( $tables ) {
			foreach ( $tables as $table ) {
				$table                  = \TablePress::$model_table->load( $table, false, false );
				$result[ $table['id'] ] = $table['name'];
			}
		}

		return $result;
	}

	/**
	 * eael_pro_validate_html_tag
	 * @param $tag
	 * @return mixed|string
	 */
	public static function eael_pro_validate_html_tag( $tag ){
		return in_array( strtolower( $tag ), self::EAEL_PRO_ALLOWED_HTML_TAGS ) ? $tag : 'div';
	}

    /**
     * Get all dropdown options of elementor breakpoints.
     *
     * @return array of breakpoint options.
     */
    public static function get_breakpoint_dropdown_options(){
        $breakpoints = Plugin::$instance->breakpoints->get_active_breakpoints();

        $dropdown_options = [];
        $excluded_breakpoints = [
            'laptop',
            'widescreen',
        ];

        foreach ( $breakpoints as $breakpoint_key => $breakpoint_instance ) {
            // Do not include laptop and widscreen in the options since this feature is for mobile devices.
            if ( in_array( $breakpoint_key, $excluded_breakpoints, true ) ) {
                continue;
            }

            $dropdown_options[ $breakpoint_key ] = sprintf(
                /* translators: 1: Breakpoint label, 2: `>` character, 3: Breakpoint value */
                esc_html__( '%1$s (%2$s %3$dpx)', 'essential-addons-elementor' ),
                $breakpoint_instance->get_label(),
                '>',
                $breakpoint_instance->get_value()
            );
        }

        $dropdown_options['desktop']    = esc_html__( 'Desktop (> 2400px)', 'essential-addons-elementor' );
        $dropdown_options['none']       = esc_html__( 'None', 'essential-addons-elementor' );
        
        return $dropdown_options;
    }
}
