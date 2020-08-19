<?php

/**
* Plugin Name: GP PayPal One-time Fee
* Description: Add a one-time fee to the first payment of a PayPal Standard subscription.
* Plugin URI: http://gravitywiz.com/
* Version: 2.0.beta1.1
* Author: David Smith
* Author URI: http://gravitywiz.com/
* License: GPL2
* Perk: True
*/

/**
* Saftey net for individual perks that are active when core Gravity Perks plugin is inactive.
*/
$gw_perk_file = __FILE__;
if(!require_once(dirname($gw_perk_file) . '/safetynet.php'))
    return;

class GWPaypalOneTimeFee extends GWPerk {

	public $version = '2.0.beta1.1';
	public $min_gravity_forms_version = '1.8.17';
	public $min_paypal_standard_version = '2.1.5';

	/**
	 * Required for PP Standard < 2.0
	 */
	public static $config = null;

    function init() {

        $this->add_tooltip( $this->key('field'), '<h6>' . __('Enable One-time Fee', 'gravityperks') . '</h6>' . __('Enable this option to specify a specific field or the form total as a one-time fee on the first payment of this subscription.', 'gravityperks' ) );

	    // ## PP Standard < 2.0

	    $this->enqueue_script(array('handle' => 'gwp-admin', 'pages' => array('gf_paypal')) );
	    $this->enqueue_script(array('handle' => 'gwp-common', 'pages' => array('gf_paypal')) );

        add_action('gform_paypal_add_option_group', array(&$this, 'paypal_settings_ui'), 10, 2);
        add_action('gform_paypal_save_config', array(&$this, 'save_paypal_settings'));
		add_filter( 'gform_paypal_get_feeds', array( $this, 'dynamic_config' ), 10, 2 );

	    // # PP Standard > 2.0

	    add_filter( 'gform_paypal_feed_settings_fields', array( $this, 'add_settings_fields' ), 10, 2 );
	    add_filter( 'gform_paypal_get_payment_feed', array( $this, 'modify_feed' ), 10, 3 );

    }



	## PayPal > 2.0

	function add_settings_fields( $setting_fields, $form ) {

		$setup_fee_field = array(
			'name'     => 'setup_fee',
			'label'    => __( 'Setup Fee', 'gravityperks' ),
			'type'     => 'select',
			'choices'  => $this->get_fee_choices( $form ),
			'tooltip'  => '<h6>' . __( 'Name', 'gravityforms' ) . '</h6>',
			'onchange' => 'if( ( gpOrigValue == "" && this.value != "" ) || ( gpOrigValue != "" && this.value == "" ) ) {
			    jQuery( this ).parents( "form" ).submit();
			}',
			'onfocus'  => 'window.gpOrigValue = this.value;',
			'validation_callback' => array( $this, 'validate_fee_setting' )
		);

		foreach( $setting_fields as &$section ) {
			foreach( $section['fields'] as &$field ) {
				if( $field['name'] == 'trial' ) {
					$field['dependency'] = array(
						'field' => 'setup_fee',
						'values' => ''
					);
					$section['fields'][] = $setup_fee_field;
				}
			}
		}

		return $setting_fields;
	}

	function get_fee_choices( $form ) {

		$choices[] = array(
			'label' => __( 'No fee', 'gravityperks' ),
			'value' => ''
		);

		$product_fields = GFCommon::get_fields_by_type( $form, array( 'product' ) );
		$product_group = array(
			'label' => __( 'Products', 'gravityperks' ),
			'choices' => array()
		);

		foreach( $product_fields as $product_field ) {

			$product_group['choices'][] = array(
				'label' => GFFormsModel::get_label( $product_field ),
				'value' => $product_field['id']
			);

		}

		$other_group = array(
			'label' => __( 'Other', 'gravityperks' ),
			'choices' => array(
				array(
					'label' => __( 'Form Total', 'gravityperks' ),
					'value' => 'all'
				)
			)
		);

		$choices = array_merge( $choices, array( $product_group ), array( $other_group ) );

		return $choices;
	}

	function validate_fee_setting( $field, $field_setting ) {

		$recurring_amount = rgpost( '_gaddon_setting_recurringAmount' );

		if( $recurring_amount == $field_setting ) {
			GFPayPal::get_instance()->set_field_error( $field, __( 'The <b>Setup Fee</b> cannot have the same value as the <b>Recurring Amount</b>.' ) );
		}

	}



	## PayPal < 2.0

    function paypal_settings_ui($config, $form) {
        ?>

        <style type="text/css">
	        .message { padding: 10px; border-radius: 3px; width:60%; margin: 15px 0; }
	        #<?php echo $this->key('trial_disabled'); ?> { background-color: #FFFFE0; border: 1px solid #E6DB55; }
	        #<?php echo $this->key('error'); ?> { background-color: #FFEBE8; border: 1px solid #CC0000; }
        </style>

        <script type="text/javascript">

        var gwotf = {};

        gwotf.labels = {};
        gwotf.labels.noFee = '<?php _e('No fee', 'gravityperks'); ?>';
        gwotf.labels.productFields = '<?php _e('Product Fields', 'gravityperks'); ?>';
        gwotf.labels.other = '<?php _e('Other', 'gravityperks'); ?>';
        gwotf.labels.formTotal = '<?php _e('Form Total', 'gravityperks'); ?>';

        jQuery(document).ready(function($){

            gwotf.feedTypeSelect = jQuery('#gf_paypal_type');

            gwotf.recurringSelect = jQuery('#gf_paypal_recurring_amount');
            gwotf.feeSelect = jQuery('#<?php echo $this->key('field'); ?>');
            gwotf.errorDiv = jQuery('#<?php echo $this->key('error'); ?>');

            gwotf.trialCheckbox = jQuery('#gf_paypal_trial_period');
            gwotf.trialMessage = jQuery('#<?php echo $this->key('trial_disabled'); ?>');

            gwotf.feedTypeSelect.change(function(){ gwotf.toggleFeeSettings(); });
            gwotf.recurringSelect.change(function(){ gwotf.toggleError(); });
            gwotf.feeSelect.change(function(){
                gwotf.toggleError();
                gwotf.toggleTrial();
            });

            gwotf.toggleFeeSettings(true);
            gwotf.toggleError(true);
            gwotf.toggleTrial(true);

        });

        jQuery(document).bind('paypalFormSelected', function(event, form){

            gwotf.feeSelect.html(gwotf.getFeeOptions(form));

            gwotf.toggleError(true);
            gwotf.toggleTrial(true);
        });

        gwotf.toggleFeeSettings = function(isInit) {
            var settingsField = jQuery('#<?php echo $this->key('settings'); ?>');
            if(gwotf.feedTypeSelect.val() == 'subscription') {
                settingsField.show();
            } else {
                settingsField.hide();
            }
        }

        gwotf.toggleError = function(isInit) {
            if(gwotf.recurringSelect.val() == gwotf.feeSelect.val() && gwotf.feeSelect.val()) {
                gwotf.errorDiv.gwpSlide('down', !isInit);
            } else {
                gwotf.errorDiv.gwpSlide('up', !isInit);
            }
        }

        gwotf.toggleTrial = function(isInit) {

            // only want to show trial message when "activating", aka setting a product for the fee select when no product was previously selected
            var isActivate = gwotf.feeSelect.data('prevVal') == '';

            if(!gwotf.feeSelect.val()) {
                gwotf.trialCheckbox.prop('disabled', false);
                gwotf.trialMessage.gwpSlide('up', !isInit);
            } else {
                gwotf.trialCheckbox.prop('checked', false).prop('disabled', true).trigger('click');
                if(!isInit && isActivate)
                    gwotf.trialMessage.gwpSlide('down', !isInit);
            }

            gwotf.feeSelect.data('prevVal', gwotf.feeSelect.val());
        }

        gwotf.getProductFields = function(form) {
            var productFields = [];
            for(i in form['fields']) {
                var field = form['fields'][i];
                if(field.type == 'product')
                    productFields.push(field);
            }
            return productFields;
        }

        gwotf.getFeeOptions = function(form) {

            var productFields = gwotf.getProductFields(form);
            var str = '<option value="">' + gwotf.labels.noFee + '</option>';

            str += '<optgroup label="' + gwotf.labels.productFields + '">';
            for(i in productFields) {

                var fieldId = productFields[i].id;
                var fieldLabel = gperk.getFieldLabel(productFields[i]);

                str += '<option value="' + fieldId + '">' + fieldLabel + '</option>';

            }
            str += '</optgroup>';

            str += '<optgroup label="' + gwotf.labels.other + '"><option value="all">' + gwotf.labels.formTotal + '</option></optgroup>';

            return str;
        }

        </script>

        <div id="<?php echo $this->key('settings'); ?>" class="margin_vertical_10">

            <label class="left_header" for="<?php echo $this->key('field'); ?>"><?php printf(__('%sPerk:%s One-time Fee', 'gravityperks'), '<strong>', '</strong>'); ?> <?php gform_tooltip($this->key('enable')); ?></label>

            <ul style="overflow:hidden;">
                <li>
                    <select id="<?php echo $this->key('field'); ?>" name="<?php echo $this->key('field'); ?>">
                        <?php echo $this->get_fee_options( $form, rgars($config, 'meta/' . $this->key('field')) ); ?>
                    </select>
                    <div id="<?php echo $this->key('trial_disabled'); ?>" class="message" style="display:none;">
                        <?php _e('<strong>Please note:</strong> The "One-time Fee" option does not work in combination with a trial period. The "Trial Period" period option has been disabled above.', 'gravityperks'); ?>
                    </div>
                    <div id="<?php echo $this->key('error'); ?>" class="message">
                        <?php _e('<strong>Oops!</strong> The same field has been selected for the "One-time Fee" option and "Recurring Amount" option. Please map these options to unique fields.', 'gravityperks'); ?>
                    </div>
                </li>
            </ul>

        </div>

        <?php
    }

    function get_fee_options($form, $selected_field) {

        $str = '<option value="">' . __('No fee', 'gravityperks') . '</option>';
        $fields = GFCommon::get_fields_by_type($form, array('product'));

        if(!empty($fields)) {
            $str .= '<optgroup label="' . __('Product Fields', 'gravityperks') . '">';
            foreach($fields as $field){

                $field_id = $field['id'];
                $field_label = RGFormsModel::get_label($field);

                $selected = $field_id == $selected_field ? 'selected="selected"' : '';
                $str .= '<option value="' . $field_id . '" ' . $selected . '>' . $field_label . '</option>';

            }
            $str .= '</optgroup>';
        }

        $selected = $selected_field == 'all' ? 'selected="selected"' : "";
        $str .= '<optgroup label="' . __('Other', 'gravityperks') . '"><option value="all" ' . $selected . '>' . __('Form Total', 'gravityperks') . '</option></optgroup>';

        return $str;
    }

    function save_paypal_settings($config) {

        // if not a subscription feed, disable one time fee on save
        if($config['meta']['type'] != 'subscription') {
            $config['meta'][$this->key('field')] = false;
            return $config;
        }

        $config['meta'][$this->key('field')] = rgpost($this->key('field'));

        // if one time fee is set, remove trial period
        if($config['meta'][$this->key('field')]) {
            $config['meta']['trial_period_enabled'] = false;
            $config['meta']['trial_amount'] = false;
            $config['meta']['trial_period_number'] = false;
            $config['meta']['trial_period_type'] = false;
        }

        return $config;
    }

    function dynamic_config($configs, $form_id) {

        $form = RGFormsModel::get_form_meta($form_id);

        foreach($configs as &$config) {

            // only process the "active" feed
            if(!GFPayPal::has_paypal_condition($form, $config))
                continue;

            self::$config = $config;
            $meta = rgar($config, 'meta');
            $fee_field_id = rgar($meta, $this->key('field'));

            // check if one time fee is set
            if(!$fee_field_id)
                continue;

            $lead = self::create_lead($form);
            $order_total = GFCommon::get_order_total($form, $lead);

            // if fee is form total, first payment will be form total, recurring will be handled by PayPal add-on
            if($fee_field_id == 'all') {

                $first_payment = $order_total;
                //$first_payment += floatval($products['shipping']['price']);

            // if fee is a field...
            } else {

                $fee_field = RGFormsModel::get_field($form, $fee_field_id);
                $fee_amount = $this->get_product_total($fee_field, $form, $lead);

                // if no fee, don't activate trial
                if($fee_amount <= 0)
                    continue;

                // if recurring amount is form total, add filter to adjust recurring total, set first payment as form total
                if(rgar($meta, 'recurring_amount_field') == 'all') {

                    add_filter("gform_paypal_query_{$form['id']}", array(&$this, 'modify_subscription_query_old'), 10, 3);
                    $first_payment = $order_total;
                    //$first_payment += floatval($products['shipping']['price']);

                // if recurring amount is a field, first payment is recurring amount + fee amount
                } else {

                    $recurring_field = RGFormsModel::get_field($form, rgar($meta, 'recurring_amount_field'));
                    $recurring_amount = $this->get_product_total($recurring_field, $form, $lead);
                    $first_payment = $recurring_amount + $fee_amount;

                }

            }

            $meta['trial_period_enabled']	= 1;
            $meta['trial_amount']			= $first_payment;
            $meta['trial_period_number']	= $meta['billing_cycle_number'];
            $meta['trial_period_type']		= $meta['billing_cycle_type'];

            $config['meta'] = $meta;

        }

        return $configs;
    }

	public static function create_lead($form) {
		return !empty(GWPaypalOneTimeFee::$lead) ? GWPaypalOneTimeFee::$lead : RGFormsModel::create_lead($form);
	}

	/**
	 * Adjust the recurring amount by removing the one-time fee from the recurring total.
	 *
	 * @param mixed $query_string
	 * @param mixed $form
	 * @param mixed $lead
	 */
	function modify_subscription_query_old($query_string, $form, $lead) {

		parse_str($query_string, $pieces);

		if(isset($pieces['a3'])) {

			$meta = rgar(self::$config, 'meta');
			$fee_field = RGFormsModel::get_field($form, rgar($meta, $this->key('field')));
			$fee_amount = $this->get_product_total($fee_field, $form, $lead);
			$total = GFCommon::get_order_total($form, $lead);

			$pieces['a3'] = $total - $fee_amount;

		}

		$new_query_string = '&' . http_build_query($pieces);

		return $new_query_string;
	}



	## Helpers

	function modify_feed( $feed, $entry, $form ) {

		$fee_field_id = rgars( $feed, 'meta/setup_fee' );
		if( ! $fee_field_id ) {
			return $feed;
		}

		$order_total = GFCommon::get_order_total( $form, $entry );
		$products    = GFCommon::get_product_fields( $form, $entry );

		// if fee is form total, first payment will be form total, recurring will be handled by PayPal add-on
		if( $fee_field_id == 'all' ) {

			$first_payment = $order_total;
			$first_payment += floatval( rgars( $products, 'shipping/price' ) );

		// if fee is a field...
		} else {

			$fee_field = GFFormsModel::get_field( $form, $fee_field_id );
			if( ! $fee_field ) {
				return $feed;
			}

			$fee_amount = $this->get_product_total( $fee_field, $form, $entry );
			$recurring_amount_field_id = rgars( $feed, 'meta/recurring_amount_field' );

			// if no fee, don't activate trial
			if( $fee_amount <= 0 ) {
				return $feed;
			}

			// if recurring amount is form total, add filter to adjust recurring total, set first payment as form total
			if( $recurring_amount_field_id == 'form_total' ) {

				add_filter( "gform_paypal_query_{$form['id']}", array( $this, 'modify_subscription_query' ), 10, 4 );
				$first_payment = $order_total;
				$first_payment += floatval( rgars( $products, 'shipping/price' ) );

			// if recurring amount is a field, first payment is recurring amount + fee amount
			} else {

				$recurring_field = GFFormsModel::get_field( $form, $recurring_amount_field_id );
				$recurring_amount = $this->get_product_total( $recurring_field, $form, $entry );
				$first_payment = $recurring_amount + $fee_amount;

			}

		}

		$feed['meta']['trial_enabled']	    = 1;
		$feed['meta']['trial_product']      = 'enter_amount';
		$feed['meta']['trial_amount']		= $first_payment;
		$feed['meta']['trialPeriod_length']	= rgars( $feed, 'meta/billingCycle_length' );
		$feed['meta']['trialPeriod_unit']	= rgars( $feed, 'meta/billingCycle_unit' );

		return $feed;
	}

	/**
	 * Adjust the recurring amount by removing the one-time fee from the recurring total.
	 *
	 * @param mixed $query_string
	 * @param mixed $form
	 * @param mixed $lead
	 */
	function modify_subscription_query( $query_string, $form, $entry, $feed = false ) {

		parse_str( $query_string, $pieces );

		if( isset( $pieces['a3'] ) ) {

			$meta       = rgar( $feed, 'meta' );
			$fee_field  = GFFormsModel::get_field( $form, rgar( $meta, 'setup_fee' ) );
			$fee_amount = $this->get_product_total( $fee_field, $form, $entry );
			$total      = GFCommon::get_order_total($form, $entry );

			$pieces['a3'] = $total - $fee_amount;

		}

		$new_query_string = '&' . http_build_query($pieces);

		return $new_query_string;
	}

    function get_product_total( $field, $form, $lead ) {

        $products = self::get_product_fields($form, $lead);
        $amount = 0;

        foreach($products['products'] as $id => $product) {

            if($id != $field['id'])
                continue;

            $price = GFCommon::to_number($product['price']);

            if(is_array(rgar($product, 'options')) && !empty($product['options'])){
                foreach($product['options'] as $option) {
                    $price += GFCommon::to_number($option['price']);
                }
            }

            $quantity = GFCommon::to_number($product['quantity']);
            $amount += ($price * $quantity);

        }

        return $amount;
    }

    public static function get_product_fields( $form, $lead ) {
        return GFCommon::get_product_fields( $form, $lead, true );
    }

	/**
	 * Deprecated
	 */
	public function check_has_min( $plugin_file, $plugin_data ) {
		return;
	}

    function documentation() {
        return array(
            'type' => 'url',
            'value' => 'http://gravitywiz.com/documentation/gp-paypal-one-time-fee/'
        );
    }

}