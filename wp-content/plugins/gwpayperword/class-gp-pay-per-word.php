<?php 

class GP_Pay_Per_Word extends GWPerk {
	
    public $version = GP_PAY_PER_WORD_VERSION;
    private static $applicable_field_types = array( 'textarea', 'post_content' );

    function init() {
        $this->enqueue_field_settings();
        $this->add_tooltip( "{$this->slug}_enable", '<h6>' . __( 'Pay Per Word', 'gravityperks' ) . '</h6>' . __( 'Enable this option to convert this product field into a pay-per-word product.', 'gravityperks' ) );
        $this->add_tooltip( "{$this->slug}_word_field", '<h6>' . __( 'Word Field', 'gravityperks' ) . '</h6>' . __( 'Select which field should be used to count the number of words.', 'gravityperks' ) );
        $this->add_tooltip( "{$this->slug}_price_per_word", '<h6>' . __( 'Price Per Word', 'gravityperks' ) . '</h6>' . __( 'Specify the price per word.', 'gravityperks' ) );
        $this->add_tooltip( "{$this->slug}_enable_base_price", '<h6>' . __( 'Enable Base Price', 'gravityperks' ) . '</h6>' . __( 'Specify a set price for this first "x" number of words will cost.', 'gravityperks' ) );
        add_action( 'gform_enqueue_scripts', array( &$this, 'enqueue_form_scripts' ) );
        add_filter( 'gform_register_init_scripts', array( &$this, 'register_init_scripts' ), 11 );
        add_filter( 'gform_validation', array( &$this, 'validate' ) );
        add_filter( 'gform_pre_submission', array( &$this, 'pre_submission' ) );
    }

    function field_settings_ui() {
        $form = GWPerks::$form;

        ?>

        <style type="text/css">

            .gwp-option {
                margin: 0 0 10px;
            }

            #gws_field_tab .gwp-option label {
                margin: 0 !important;
            }

            .gws-child-settings {
                border-left: 2px solid #eee;
                padding: 15px;
                margin-left: 5px;
            }

        </style>

        <li class="<?php echo $this->slug; ?>_setting gwp_field_setting field_setting" style="display:none;">

            <input type="checkbox" id="<?php echo $this->key( 'enable' ); ?>" value="1"
                   onclick="gperk.toggleSettings('<?php echo $this->slug; ?>_enable', '<?php echo $this->slug; ?>_settings');"/>
            <label for="<?php echo $this->key( 'enable' ); ?>" class="inline">
                <?php _e( "Enable Pay Per Word", "gravityperks" ); ?>
                <?php gform_tooltip( "{$this->slug}_enable" ) ?>
            </label>

            <div id="<?php echo $this->key( 'settings' ); ?>" class="gws-child-settings">

                <div class="gwp-option">
                    <label for="<?php echo $this->slug; ?>_word_field">
                        <?php _e( "Word Field", "gravityperks" ); ?>
                        <?php gform_tooltip( "{$this->slug}_word_field" ) ?>
                    </label>
                    <select id="<?php echo $this->slug; ?>_word_field"
                            onchange="SetFieldProperty('<?php echo $this->slug; ?>_word_field', jQuery(this).val());">
                        <option value=""><?php _e( 'Select a Field', 'gravityperks' ); ?></option>
                        <?php
                        $applicable_fields = GFCommon::get_fields_by_type( $form, self::$applicable_field_types );
                        foreach ( $applicable_fields as $field ) { ?>
                            <option value="<?php echo $field['id']; ?>"><?php echo $field['label']; ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="gwp-option">
                    <label for="<?php echo $this->slug; ?>_price_per_word">
                        <?php _e( "Price Per Word", "gravityperks" ); ?>
                        <?php gform_tooltip( "{$this->slug}_price_per_word" ) ?>
                    </label>
                    <input type="text" id="<?php echo $this->slug; ?>_price_per_word"
                           onblur="gperk.SetFieldPropertyPrice('<?php echo $this->slug; ?>_price_per_word', this.value, this);"/>
                </div>

                <div class="gwp-option">
                    <input type="checkbox" value="1" id="<?php echo $this->slug; ?>_enable_base_price"
                           onclick="gperk.toggleSettings('<?php echo $this->slug; ?>_enable_base_price', '<?php echo $this->slug; ?>_base_price_settings');"/>
                    <label for="<?php echo $this->slug; ?>_enable_base_price" class="inline">
                        <?php _e( "Enable Base Price", "gravityperks" ); ?>
                        <?php gform_tooltip( "{$this->slug}_enable_base_price" ) ?>
                    </label>
                </div>

                <div id="<?php echo $this->key( 'base_price_settings' ); ?>" class="gwp-option gws-child-settings"
                     style="display:none;">

                    <div style="float:left;margin-right:10px;">
                        <label for="<?php echo $this->slug; ?>_base_price">
                            <?php _e( "Base Price", "gravityperks" ); ?>
                            <?php gform_tooltip( "{$this->slug}_base_price" ) ?>
                        </label>
                        <input type="text" id="<?php echo $this->slug; ?>_base_price"
                               onblur="gperk.SetFieldPropertyPrice('<?php echo $this->slug; ?>_base_price', this.value, this);"/>
                    </div>

                    <div style="float:left;">
                        <label for="<?php echo $this->slug; ?>_base_word_count">
                            <?php _e( "Base Word Count", "gravityperks" ); ?>
                            <?php gform_tooltip( "{$this->slug}_base_word_count" ) ?>
                        </label>
                        <input type="text" id="<?php echo $this->slug; ?>_base_word_count"
                               onblur="SetFieldProperty('<?php echo $this->slug; ?>_base_word_count', gformCleanNumber(this.value));"/>
                    </div>

                    <div class="clear"></div>

                    <p class="description"><?php _e( 'Specify how much the the first "x" number of words will cost.', 'gravityperks' ); ?></p>

                </div>

            </div>

        </li>

    <?php
    }

    function field_settings_js() {
        ?>

        <script type="text/javascript">

            var gwppw = {
                applicableFieldTypes: <?php echo json_encode(self::$applicable_field_types); ?>
            };
            jQuery( function ( $ ) {
                fieldSettings['product'] += ", .<?php echo $this->slug; ?>_setting";
                $( document ).bind( 'gform_load_field_settings', function ( event, field ) {

                    // this perk only applies to single products
                    if( field.inputType != 'singleproduct' ) {
                        jQuery( '.<?php echo $this->slug; ?>_setting' ).hide();
                        return;
                    } else {
                        jQuery( '.<?php echo $this->slug; ?>_setting' ).show();
                    }
                    jQuery( '#<?php echo $this->slug; ?>_word_field' ).val( field['<?php echo $this->slug; ?>_word_field'] );
                    jQuery( '#<?php echo $this->slug; ?>_price_per_word' ).val( gformFormatMoney( field['<?php echo $this->slug; ?>_price_per_word'] ) );
                    // populate base price settings
                    gperk.toggleSettings( '<?php echo $this->slug; ?>_enable_base_price', '<?php echo $this->slug; ?>_base_price_settings', field['<?php echo $this->slug; ?>_enable_base_price'] );
                    if( field['<?php echo $this->slug; ?>_enable_base_price'] ) {
                        jQuery( '#<?php echo $this->slug; ?>_base_price' ).val( gformFormatMoney( field['<?php echo $this->slug; ?>_base_price'] ) );
                        jQuery( '#<?php echo $this->slug; ?>_base_word_count' ).val( field['<?php echo $this->slug; ?>_base_word_count'] );
                    }
                    // run this on field load
                    gperk.toggleSettings( '<?php echo $this->slug; ?>_enable', '<?php echo $this->slug; ?>_settings', field['<?php echo $this->slug; ?>_enable'] );
                } );
            } );
            gperk.SetFieldPropertyPrice = function ( property, value, elem ) {
                value = gformToNumber( value );
                money = gformFormatMoney( value, true );
                SetFieldProperty( property, value );
                jQuery( elem ).val( money );
            }

        </script>

    <?php
    }

    public function enqueue_form_scripts( $form ) {
        foreach ( $form['fields'] as $field ) {
            if ( rgar( $field, $this->slug . '_enable' ) ) {
                wp_enqueue_script( 'payperword', $this->get_base_url() . '/scripts/payperword.js', array( 'jquery', 'gform_gravityforms' ) );

                return;
            }
        }
    }

    public function register_init_scripts( $form ) {
        $ppw_fields = array();
        foreach ( $form['fields'] as $field ) {
            if ( rgar( $field, $this->key( 'enable' ) ) ) {
                $options                    = array();
                $options['price_field']     = $field['id'];
                $options['word_field']      = $this->field_prop( $field, 'word_field' );
                $options['price_per_word']  = $this->field_prop( $field, 'price_per_word' );
                $options['base_price']      = $this->field_prop( $field, 'enable_base_price' ) ? $this->field_prop( $field, 'base_price' ) : '';
                $options['base_word_count'] = $this->field_prop( $field, 'enable_base_price' ) ? $this->field_prop( $field, 'base_word_count' ) : '';

	            $word_field = GFFormsModel::get_field( $form, $options['word_field'] );
	            $options['useRichTextEditor'] = (bool) ( $word_field ? $word_field->useRichTextEditor : false );

	            $ppw_fields[ $field['id'] ] = $options;
            }
        }
        if ( empty( $ppw_fields ) ) {
            return;
        }
        $script = "new GWPayPerWord({$form['id']}, " . json_encode( $ppw_fields ) . ");";
        GFFormDisplay::add_init_script( $form['id'], $this->slug, GFFormDisplay::ON_PAGE_RENDER, $script );
    }

    /**
     * Let's override the default validation failure that will come from our dynamic pricing but first, we make sure
     * that the quantity is valid. The actual price is never validated since the price submitted is not the price
     * used for the product.
     *
     * @param mixed $validation_result
     */
    public function validate( $validation_result ) {
        $form                 = $validation_result['form'];
        $has_validation_error = false;
        foreach ( $form['fields'] as &$field ) {
            if ( RGFormsModel::get_input_type( $field ) != 'singleproduct' || ! $this->field_prop( $field, 'enable' ) ) {
                if ( $field["failed_validation"] ) {
                    $has_validation_error = true;
                }
                continue;
            }
            $values   = RGFormsModel::get_field_value( $field );
            $quantity = rgar( $values, "{$field["id"]}.3" );
            if ( $field["isRequired"] && rgblank( $quantity ) && ! rgar( $field, "disableQuantity" ) ) {
                $field["failed_validation"]  = true;
                $field["validation_message"] = rgempty( "errorMessage", $field ) ? __( "This field is required.", "gravityforms" ) : rgar( $field, "errorMessage" );
            } else if ( ! empty( $quantity ) && ( ! is_numeric( $quantity ) || intval( $quantity ) != floatval( $quantity ) ) ) {
                $field["failed_validation"]  = true;
                $field["validation_message"] = __( "Please enter a valid quantity", "gravityforms" );
            } else {
                $field["failed_validation"] = false;
            }
            if ( $field["failed_validation"] ) {
                $has_validation_error = true;
            }
        }
        $validation_result['form']     = $form;
        $validation_result['is_valid'] = ! $has_validation_error;

        return $validation_result;
    }

    /**
     * We use the pre submission hook to override the submitted price with the price calculated via PHP.
     *
     * @param mixed $form
     */
    public function pre_submission( $form ) {
        foreach ( $form['fields'] as &$field ) {
            if ( RGFormsModel::get_input_type( $field ) != 'singleproduct' || ! rgar( $field, $this->slug . '_enable' ) ) {
                continue;
            }
            $price = $this->calculate_price( $field, $form );
            $_POST["input_{$field['id']}_2"] = $price;
        }
    }

    public function calculate_price( $price_field, $form ) {

        $word_field = RGFormsModel::get_field( $form, rgar( $price_field, "{$this->slug}_word_field" ) );

	    if( $word_field->useRichTextEditor ) {
		    $words      = rgpost( sprintf( 'gpppw_plain_text_%d', $word_field->id ) );
		    $word_count = count( array_filter( preg_split( '/[ \n\r]+/', trim( $words ) ) ) );
	    } else {
		    $word_count = count( array_filter( preg_split( '/[ \n\r]+/', trim( RGFormsModel::get_field_value( $word_field ) ) ) ) );
	    }

        $price          = 0;
        $price_per_word = rgar( $price_field, "{$this->slug}_price_per_word" );
        $base_price     = $this->field_prop( $price_field, 'enable_base_price' ) ? rgar( $price_field, "{$this->slug}_base_price" ) : 0;
        $base_count     = $this->field_prop( $price_field, 'enable_base_price' ) ? rgar( $price_field, "{$this->slug}_base_word_count" ) : 0;

        if ( RGFormsModel::is_field_hidden( $form, $word_field, array() ) ) {
            return $price;
        } else if ( $word_count > $base_count ) {
            $extra_words_count = $word_count - $base_count;
            $price             = $base_price;
            $price += $extra_words_count * $price_per_word;
        } else {
            $price = $base_price;
        }
        $price = apply_filters( 'gwppw_price', $price, $price_field, $word_field, $word_count );

        return $price;
    }

    public function documentation() {
        return array(
            'type'  => 'url',
            'value' => 'http://gravitywiz.com/documentation/gp-pay-per-word/'
        );
    }

}

class GWPayPerWord extends GP_Pay_Per_Word { }