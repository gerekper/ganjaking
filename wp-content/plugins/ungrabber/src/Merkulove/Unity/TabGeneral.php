<?php
/**
 * UnGrabber
 * A most effective way to protect your online content from being copied or grabbed
 * Exclusively on https://1.envato.market/ungrabber
 *
 * @encoding        UTF-8
 * @version         3.0.2
 * @copyright       (C) 2018 - 2021 Merkulove ( https://merkulov.design/ ). All rights reserved.
 * @license         Commercial Software
 * @contributors    Dmitry Merkulov (dmitry@merkulov.design)
 * @support         help@merkulov.design
 **/

namespace Merkulove\Ungrabber\Unity;

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit;
}

/**
 * SINGLETON: Class used to implement any tab with settings.
 *
 * @since 1.0.0
 *
 **/
final class TabGeneral extends Tab {

	/**
	 * The one true TabGeneral.
	 *
     * @since 1.0.0
     * @access private
	 * @var TabGeneral
	 **/
	private static $instance;

    /**
     * Render form with all settings fields.
     *
     * @since 1.0.0
     * @access public
     *
     * @param string $tab_slug - Slug of current tab.
     *
     * @return void
     **/
    public function do_settings( $tab_slug ) {

        /** No status tab, nothing to do. */
        if ( ! $this->is_enabled( $tab_slug ) ) { return; }

        /** Render title. */
        $this->render_title( $tab_slug );

        /** Render fields. */
        $this->do_settings_base( $tab_slug );

    }

    /**
     * Generate General Tab.
     *
     * @since 1.0.0
     * @access public
     *
     * @param string $tab_slug - Slug of current tab.
     *
     * @return void
     **/
	public function add_settings( $tab_slug ) {

        /** Custom General Tab. */
        $this->add_settings_base( $tab_slug );

        $group = 'Ungrabber' . $tab_slug . 'OptionsGroup';
        $section = 'mdp_ungrabber_' . $tab_slug . '_page_status_section';

        /** Exit if no fields to process. */
        if ( empty( Plugin::get_tabs()[$tab_slug]['fields'] ) ) { return; }

        $fields = Plugin::get_tabs()[$tab_slug]['fields'];

        /** Create settings for each field. */
        foreach ( $fields as $key => $field ) {

            /** Prepare field label. */
            $label = $field['show_label'] ? $field['label'] : '';

            /** Hide label for header fields. */
            if ( 'header' === $field['type'] ) { $label = ''; }

            /** Create field. */
            add_settings_field( $key, $label, [ $this, 'create_field' ], $group, $section, [ 'key' => $key, 'type' => $field[ 'type' ], 'tab_slug' => $tab_slug ] );

        }

	}

    /**
     * Render Settings field.
     *
     * @param array $args - Array of params for render: field key and type.
     *
     * @since  1.0.0
     * @access public
     *
     * @return void
     **/
	public function create_field( $args = [] ) {

        /** Do we have custom handler for this field type? */
        $handler = $this->get_field_handler( $args );
        if ( is_array( $handler ) && is_callable( $handler ) ) {

            /** Call custom render for field. */
            $handler( $args[ 'key' ], $args[ 'tab_slug' ] );
            return;

        }

        /** In field haven't custom render check maybe we have standard handler for this field type? */
        if ( ! is_callable( [ $this, 'render_' . $args[ 'type' ] ] ) ) {
            ?><div class="mdc-system-warn"><?php esc_html_e( 'Handler for this field type not found.' ); ?></div><?php
            return;
        }

        /** Call render field by type. */
        $this->{'render_' . $args[ 'type' ]}( $args['key'], $args['tab_slug'] );

	}

    /**
     * Return custom handler for field or false.
     *
     * @param array $args - Array of params for render: field key and type.
     *
     * @since  1.0.0
     * @access public
     *
     * @return array|false
     **/
	private function get_field_handler( $args ) {

	    /** Get field. */
        $tabs = Plugin::get_tabs();
        $tab = $tabs[ $args[ 'tab_slug' ] ];
        $fields = $tab[ 'fields' ];
        $field = $fields[ $args[ 'key' ] ];

        if ( ! empty( $field[ 'render' ] ) ) {
            return $field[ 'render' ];
        }

	    return false;

    }

    /**
     * Render Divider field.
     *
     * @param string $key - Field key.
     * @param string $tab_slug - Tab slug to which the field belongs.
     *
     * @since  1.0.0
     * @access public
     *
     * @return void
     **/
    public function render_divider( $key, $tab_slug ) {
	    ?><hr class="mdc-filed-divider"><?php
    }

    /**
     * Render WP Editor field.
     *
     * @param string $key - Field key.
     * @param string $tab_slug - Tab slug to which the field belongs.
     *
     * @since  1.0.0
     * @access public
     *
     * @return void
     **/
    public function render_editor( $key, $tab_slug ) {

        /** Prepare general field params. */
        list( $field, $label, $description, $attr ) = $this->prepare_general_params( $tab_slug, $key );

        /** Render WP Editor. */
        wp_editor( Settings::get_instance()->options[$key], $attr['id'], [ 'textarea_rows' => $attr['textarea_rows'], 'textarea_name' => $attr['name'] ] );

        if ( $description ) {
            ?>
            <div class="mdc-text-field-helper-line">
                <div class="mdc-text-field-helper-text mdc-text-field-helper-text--persistent"><?php echo wp_kses_post( $description ); ?></div>
            </div>
            <?php
        }

    }

    /**
     * Render icon field.
     *
     * @param string $key - Field key.
     * @param string $tab_slug - Tab slug to which the field belongs.
     *
     * @since  1.0.0
     * @access public
     *
     * @return void
     **/
    public function render_icon( $key, $tab_slug ) {

        /** Prepare general field params. */
        list( $field, $label, $description, $attr ) = $this->prepare_general_params( $tab_slug, $key );

        if ( empty( $field[ 'placeholder' ] ) ) {
            $label = '';
        }

        /** Render Icon. */
        UI::get_instance()->render_icon(
            Settings::get_instance()->options[$key],
            $label,
            $description,
            $attr,
            $field['meta']
        );

    }

    /**
     * Render Colorpicker field.
     *
     * @param string $key - Field key.
     * @param string $tab_slug - Tab slug to which the field belongs.
     *
     * @since  1.0.0
     * @access public
     *
     * @return void
     **/
    public function render_colorpicker( $key, $tab_slug ) {

        /** Prepare general field params. */
        list( $field, $label, $description, $attr ) = $this->prepare_general_params( $tab_slug, $key );

        /** Render colorpicker. */
        UI::get_instance()->render_colorpicker(
            Settings::get_instance()->options[$key],
            $label,
            $description,
            $attr
        );

    }

    /**
     * Render Textarea field.
     *
     * @param string $key - Field key.
     * @param string $tab_slug - Tab slug to which the field belongs.
     *
     * @since  1.0.0
     * @access public
     *
     * @return void
     **/
    public function render_textarea( $key, $tab_slug ) {

        /** Prepare general field params. */
        list( $field, $label, $description, $attr ) = $this->prepare_general_params( $tab_slug, $key );

        /** Render Textarea. */
        UI::get_instance()->render_textarea(
            Settings::get_instance()->options[$key],
            $label,
            $description,
            $attr
        );

    }

    /**
     * Render Button field.
     *
     * @param string $key - Field key.
     * @param string $tab_slug - Tab slug to which the field belongs.
     *
     * @since  1.0.0
     * @access public
     *
     * @return void
     **/
    public function render_button( $key, $tab_slug ) {

        /** Prepare general field params. */
        list( $field, $label, $description, $attr ) = $this->prepare_general_params( $tab_slug, $key );

        UI::get_instance()->render_button(
            $label,
            $description,
            $field['icon'],
            $attr
        );

    }

    /**
     * Render Header field.
     *
     * @param string $key - Field key.
     * @param string $tab_slug - Tab slug to which the field belongs.
     *
     * @since  1.0.0
     * @access public
     *
     * @return void
     **/
    public function render_header( $key, $tab_slug ) {

        /** Prepare general field params. */
        list( $field, $label, $description, $attr ) = $this->prepare_general_params( $tab_slug, $key );

        /** Render Header. */
        UI::get_instance()->render_header(
            $label,
            $description,
            'h5'
        );

    }

    /**
     * Render Slider field.
     *
     * @param string $key - Field key.
     * @param string $tab_slug - Tab slug to which the field belongs.
     *
     * @since  1.0.0
     * @access public
     *
     * @return void
     **/
    public function render_slider( $key, $tab_slug ) {

        /** Prepare general field params. */
        list( $field, $label, $description, $attr ) = $this->prepare_general_params( $tab_slug, $key );

        /** Render slider. */
        UI::get_instance()->render_slider(
            Settings::get_instance()->options[$key],
            $field['min'],
            $field['max'],
            $field['step'],
            $label,
            $description,
            $attr,
            $field['discrete']
        );

    }

    /**
     * Render Switcher field.
     *
     * @param string $key - Field key.
     * @param string $tab_slug - Tab slug to which the field belongs.
     *
     * @since  1.0.0
     * @access public
     *
     * @return void
     **/
    public function render_switcher( $key, $tab_slug ) {

        /** Prepare general field params. */
        list( $field, $label, $description, $attr ) = $this->prepare_general_params( $tab_slug, $key );

        /** Render switcher. */
        UI::get_instance()->render_switcher(
            Settings::get_instance()->options[$key],
            $label,
            $description,
            $attr
        );

    }

    /**
     * Render Select field.
     *
     * @param string $key - Field key.
     * @param string $tab_slug - Tab slug to which the field belongs.
     *
     * @since  1.0.0
     * @access public
     *
     * @return void
     **/
    public function render_select( $key, $tab_slug ) {

        /** Prepare general field params. */
        list( $field, $label, $description, $attr ) = $this->prepare_general_params( $tab_slug, $key );

        /** Render select. */
        UI::get_instance()->render_select(
            $field['options'],
            Settings::get_instance()->options[$key], // Selected option.
            $label,
            $description,
            $attr
        );

    }

	/**
	 * Render Select field with helper image.
	 *
	 * @param string $key - Field key.
	 * @param string $tab_slug - Tab slug to which the field belongs.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return void
	 **/
	public function render_select_img( $key, $tab_slug ) {

		/** Prepare general field params. */
		list( $field, $label, $description, $attr ) = $this->prepare_general_params( $tab_slug, $key );

		/** Render select. */
		UI::get_instance()->render_select_img(
			$field['options'],
			Settings::get_instance()->options[$key], // Selected option.
			$label,
			$description,
			$attr
		);

	}

    /**
     * Render Text field.
     *
     * @param string $key - Field key.
     * @param string $tab_slug - Tab slug to which the field belongs.
     *
     * @since  1.0.0
     * @access public
     *
     * @return void
     **/
	public function render_text( $key, $tab_slug ) {

	    /** Prepare general field params. */
        list( $field, $label, $description, $attr ) = $this->prepare_general_params( $tab_slug, $key );

        /** Render input. */
        UI::get_instance()->render_input(
            Settings::get_instance()->options[$key],
            $label,
            $description,
            $attr
        );

    }

    /**
     * Prepare general field params.
     *
     * @param $tab_slug
     * @param $key
     *
     * @return array
     **/
    public function prepare_general_params( $tab_slug, $key ) {

        /** Get field settings. */
        $field = Plugin::get_tabs()[ $tab_slug ][ 'fields' ][ $key ];

        /** Prepare label, description and attributes. */
        $label = $field[ 'show_label' ] ? $field[ 'label' ] : '';
        if ( ! empty( $field[ 'placeholder' ] ) ) {
            $label = $field[ 'placeholder' ];
        }
        $description = ! empty( $field[ 'show_description' ] ) ? $field[ 'description' ] : '';

        $attr = $this->prepare_attr( $key, $tab_slug, $field );

        return [ $field, $label, $description, $attr ];
    }

    /**
     * Prepare array with attributes.
     *
     * @param string $key - Field key.
     * @param string $tab_slug - Tab slug to which the field belongs.
     * @param array $field
     *
     * @since  1.0.0
     * @access private
     *
     * @return array
     **/
    private function prepare_attr( $key, $tab_slug, $field ) {

        $name = 'mdp_ungrabber_' . $tab_slug . '_settings';

        $attr = [
            'name'      => $name . '[' . $key . ']',
            'id'        => $name . '_' . $key,
        ];

        if ( ! empty( $field['attr'] ) ) {
            $attr = array_merge( $attr, $field['attr'] );
        }

        return $attr;

    }

    /**
     * Render Chosen field.
     *
     * @param string $key - Field key.
     * @param string $tab_slug - Tab slug to which the field belongs.
     *
     * @since  1.0.0
     * @access public
     *
     * @return void
     **/
    public function render_chosen( $key, $tab_slug ) {

        /** Prepare general field params. */
        list( $field, $label, $description, $attr ) = $this->prepare_general_params( $tab_slug, $key );

        /** Render select. */
        UI::get_instance()->render_chosen(
            $field['options'],
            Settings::get_instance()->options[$key], // Selected options.
            $description,
            $attr
        );

    }

	/**
	 * Render Custom Post Types with chosen.
	 *
	 * @param string $key - Field key.
	 * @param string $tab_slug - Tab slug to which the field belongs.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return void
	 **/
	public function render_cpt( $key, $tab_slug ) {

		/** Prepare general field params. */
		list( $field, $label, $description, $attr ) = $this->prepare_general_params( $tab_slug, $key );

		/** Render select. */
		UI::get_instance()->render_chosen(
			$this->get_cpt_list(),
			Settings::get_instance()->options[$key], // Selected options.
			$description,
			$attr
		);

	}

	/**
	 * Render Layout field.
	 *
	 * @param string $key - Field key.
	 * @param string $tab_slug - Tab slug to which the field belongs.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return void
	 **/
	public function render_layout( $key, $tab_slug ) {

		/** Prepare general field params. */
		list( $field, $label, $description, $attr ) = $this->prepare_general_params( $tab_slug, $key );

		/** Render layouts. */
		UI::get_instance()->render_layouts(
			$field['options'],
			Settings::get_instance()->options[$key], // Selected option.
			$label,
			$description,
			$attr
		);

	}

	/**
	 * Return list of Custom Post Types.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return array
	 **/
	private function get_cpt_list() {

		/** All available post types. */
		return Helper::get_instance()->get_cpt( [ 'exclude' => [ 'attachment', 'elementor_library' ] ] );

	}

	/**
	 * Main TabGeneral Instance.
	 * Insures that only one instance of TabGeneral exists in memory at any one time.
	 *
	 * @static
	 * @return TabGeneral
	 **/
	public static function get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {

			self::$instance = new self;

		}

		return self::$instance;

	}

}
