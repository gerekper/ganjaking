<?php

if (!defined('ABSPATH')) {

    exit; // Exit if accessed directly } if ( ! class_exists( 'WC_DS_Settings' ) ) :
}

if ( ! class_exists( 'WC_DS_Settings' ) ) :
    
    function wc_ds_add_settings($settings) {

        /**         
        * Settings class 
        */ 
        
        class WC_DS_Settings extends WC_Settings_Page {

            /**
             * The request response 
             * 
             * @var array 
             */ 

            private $response = null;
            
            /**
             * The error message
             *
             * @var string
             */

            private $error_message = '';

            /**
             * Setup settings class 
             */ 

            const SETTINGS_NAMESPACE = 'dropshipping';

            public function __construct() {

                $this->id = 'wc_dropship_settings';

                $this->label = __('Dropshipping', 'wc_dropship_settings');

                add_filter('woocommerce_settings_tabs_array', array($this, 'add_settings_page'), 20);

                add_action('woocommerce_settings_' . $this->id, array($this, 'output'));
            }

            /**
             * Get settings array
             *
             * @param string $current_section Optional. Defaults to empty string.
             * @return array Array of settings
             */ 

            public function get_settings($current_section = '') {

                $base_name = explode('/', plugin_basename(__FILE__));

                wp_enqueue_style('wc_dropshipping_checkout_style', plugins_url() . '/' . $base_name[0] . '/assets/css/custom.css');

                // Tab to update options //global $current_section; 

                if (isset($_POST) && !empty($_POST)) {

                    $options = get_option('wc_dropship_manager');

                    foreach ($_POST as $key => $opt) {

                        if ($key != 'submit')
                            $options[$key] = $_POST[$key];
                    }

                    if (isset($_POST['supp_notification'])) {

                        $options['supp_notification'] = '1';
                    } else {

                        $options['supp_notification'] = '0';
                    }

                    if (isset($_POST['csv_inmail'])) {

                        $options['csv_inmail'] = '1';
                    } else {

                        $options['csv_inmail'] = '0';
                    }

                    if (isset($_POST['billing_phone'])) {

                        $options['billing_phone'] = '1';
                    } else {

                        $options['billing_phone'] = '0';
                    }

                    if (isset($_POST['email_supplier'])) {

                        $options['email_supplier'] = '1';
                    } else {

                        $options['email_supplier'] = '0';
                    }

                    if (isset($_POST['hide_suppliername'])) {

                        $options['hide_suppliername'] = '1';
                    } else {

                        $options['hide_suppliername'] = '0';
                    }

                    if (isset($_POST['hide_suppliername_on_product_page'])) {

                        $options['hide_suppliername_on_product_page'] = '1';
                    } else {

                        $options['hide_suppliername_on_product_page'] = '0';
                    }

                    if (isset($_POST['hideorderdetail_suppliername'])) {

                        $options['hideorderdetail_suppliername'] = '1';
                    } else {

                        $options['hideorderdetail_suppliername'] = '0';
                    }

                    if (isset($_POST['full_information'])) {

                        $options['full_information'] = '1';
                    } else {

                        $options['full_information'] = '0';
                    }

                    if (isset($_POST['show_logo'])) {

                        $options['show_logo'] = '1';
                    } else {

                        $options['show_logo'] = '0';
                    }

                    if (isset($_POST['order_date'])) {

                        $options['order_date'] = '1';
                    } else {

                        $options['order_date'] = '0';
                    }

                    if (isset($_POST['smtp_check'])) {

                        $options['smtp_check'] = '1';
                    } else {

                        $options['smtp_check'] = '0';
                    }

                    if (isset($_POST['std_mail'])) {

                        $options['std_mail'] = '1';
                    } else {

                        $options['std_mail'] = '0';
                    }

                    if (isset($_POST['checkout_order_number'])) {

                        $options['checkout_order_number'] = '1';
                    } else {

                        $options['checkout_order_number'] = '0';
                    }

                    if (isset($_POST['show_pay_type'])) {

                        $options['show_pay_type'] = '1';
                    } else {

                        $options['show_pay_type'] = '0';
                    }

                    if (isset($_POST['cnf_mail'])) {

                        $options['cnf_mail'] = '1';
                    } else {

                        $options['cnf_mail'] = '0';
                    }

                    if (isset($_POST['cc_mail'])) {

                        $options['cc_mail'] = '1';
                    } else {

                        $options['cc_mail'] = '0';
                    }

                    if (isset($_POST['from_name'])) {

                        $options['from_name'] = $_POST['from_name'];
                    } else {

                        $options['from_name'] = '';
                    }

                    if (isset($_POST['from_email'])) {

                        $options['from_email'] = $_POST['from_email'];
                    } else {

                        $options['from_email'] = '';
                    }

                    if (isset($_POST['hide_shipping_price'])) {

                        $options['hide_shipping_price'] = '1';
                    } else {

                        $options['hide_shipping_price'] = '0';
                    }

                    if (isset($_POST['hide_tax'])) {

                        $options['hide_tax'] = '1';
                    } else {

                        $options['hide_tax'] = '0';
                    }

                    if (isset($_POST['total_price'])) {

                        $options['total_price'] = '1';
                    } else {

                        $options['total_price'] = '0';
                    }

                    if (isset($_POST['product_price'])) {

                        $options['product_price'] = '1';
                    } else {

                        $options['product_price'] = '0';
                    }

                    if (isset($_POST['shipping'])) {

                        $options['shipping'] = '1';
                    } else {

                        $options['shipping'] = '0';
                    }

                    if (isset($_POST['payment_method'])) {

                        $options['payment_method'] = '1';
                    } else {

                        $options['payment_method'] = '0';
                    }

                    if (isset($_POST['cost_of_goods'])) {

                        $options['cost_of_goods'] = '1';
                    } else {

                        $options['cost_of_goods'] = '0';
                    }

                    if (isset($_POST['billing_address'])) {

                        $options['billing_address'] = '1';
                    } else {

                        $options['billing_address'] = '0';
                    }

                    if (isset($_POST['shipping_address'])) {

                        $options['shipping_address'] = '1';
                    } else {

                        $options['shipping_address'] = '0';
                    }

                    if (isset($_POST['product_image'])) {

                        $options['product_image'] = '1';
                    } else {

                        $options['product_image'] = '0';
                    }

                    if (isset($_POST['store_name'])) {

                        $options['store_name'] = '1';
                    } else {

                        $options['store_name'] = '0';
                    }

                    if (isset($_POST['store_address'])) {

                        $options['store_address'] = '1';
                    } else {

                        $options['store_address'] = '0';
                    }

                    if (isset($_POST['complete_email'])) {

                        $options['complete_email'] = '1';
                    } else {

                        $options['complete_email'] = '0';
                    }

                    if (isset($_POST['order_complete_link'])) {

                        $options['order_complete_link'] = '1';
                    } else {

                        $options['order_complete_link'] = '0';
                    }

                    if (isset($_POST['type_of_package'])) {

                        $options['type_of_package'] = '1';
                    } else {

                        $options['type_of_package'] = '0';
                    }

                    if (isset($_POST['customer_note'])) {

                        $options['customer_note'] = '1';
                    } else {

                        $options['customer_note'] = '0';
                    }
					
					if ( isset($_POST['customer_email' ] ) ) {
						$options['customer_email'] = '1';
					} else {
						$options['customer_email'] = '0';
					}

                    // Aliexpress Settings get POST 

                    if (isset($_POST['ali_cbe_enable_name'])) {

                        $options['ali_cbe_enable_name'] = '1';
                    } else {

                        $options['ali_cbe_enable_name'] = '0';
                    }

                    if (isset($_POST['ali_cbe_price_rate_name'])) {

                        $options['ali_cbe_price_rate_name'] = $_POST['ali_cbe_price_rate_name'];
                    } else {

                        $options['ali_cbe_price_rate'] = '';
                    }

                    if (isset($_POST['ali_cbe_price_rate_value_name'])) {

                        if ($options['ali_cbe_price_rate_value_name'] < 1 || !is_numeric($options['ali_cbe_price_rate_value_name'])) {

                            $options['ali_cbe_price_rate_value_name'] = 0;
                        } else {

                            $options['ali_cbe_price_rate_value_name'] = $_POST['ali_cbe_price_rate_value_name'];
                        }
                    } else {

                        $options['ali_cbe_price_rate_value_name'] = 0;
                    }

                    update_option('wc_dropship_manager', $options);
                }

                $options = get_option('wc_dropship_manager');

                //echo '<pre>'; print_r($options); echo '</pre>'; 

                if (isset($options['supp_notification'])) {

                    $supp_notification = $options['supp_notification'];
                } else {

                    $supp_notification = '';
                }

                if (isset($options['csv_inmail'])) {

                    $csvcheck = $options['csv_inmail'];
                } else {

                    $csvcheck = '';
                }

                if (isset($_POST['packing_slip_header'])) {

                    if ('' != $_POST['packing_slip_header']) {

                        $options['packing_slip_header'] = $_POST['packing_slip_header'];
                    } else {

                        $options['packing_slip_header'] = '';
                    }
                }

                if (isset($_POST['renewal_email'])) {

                    $options['renewal_email'] = '1';
                } else {

                    $options['renewal_email'] = '0';
                }

                if (isset($options['full_information'])) {

                    $full_information = $options['full_information'];
                } else {

                    $full_information = '';
                }

                if (isset($options['show_logo'])) {

                    $show_logo = $options['show_logo'];
                } else {

                    $show_logo = '';
                }

                if (isset($options['order_date'])) {

                    $order_date = $options['order_date'];
                } else {

                    $order_date = '';
                }

                if (isset($options['smtp_check'])) {

                    $smtp_check = $options['smtp_check'];
                } else {

                    $smtp_check = '';
                }

                if (isset($options['std_mail'])) {

                    $std_mail = $options['std_mail'];
                } else {

                    $std_mail = '';
                }

                if (isset($options['checkout_order_number'])) {

                    $checkout_order_number = $options['checkout_order_number'];
                } else {

                    $checkout_order_number = 0;
                }

                if (isset($options['show_pay_type'])) {

                    $show_pay_type = $options['show_pay_type'];
                } else {

                    $show_pay_type = '';
                }

                if (isset($options['cnf_mail'])) {

                    $cnf_mail = $options['cnf_mail'];
                } else {

                    $cnf_mail = '';
                }

                if (isset($options['cc_mail'])) {

                    $cc_mail = $options['cc_mail'];
                } else {

                    $cc_mail = '';
                }

                if (isset($options['from_name'])) {

                    $from_name = $options['from_name'];
                } else {

                    $from_name = '';
                }

                if (isset($options['from_email'])) {

                    $from_email = $options['from_email'];
                } else {

                    $from_email = '';
                }

                if (isset($options['hide_shipping_price'])) {

                    $hide_shipping_price = $options['hide_shipping_price'];
                } else {

                    $hide_shipping_price = '';
                }

                if (isset($options['hide_tax'])) {

                    $hide_tax = $options['hide_tax'];
                } else {

                    $hide_tax = '';
                }

                if (isset($options['total_price'])) {

                    $total_price = $options['total_price'];
                } else {

                    $total_price = '';
                }

                if (isset($options['product_price'])) {

                    $product_price = $options['product_price'];
                } else {

                    $product_price = '';
                }

                if (isset($options['shipping'])) {

                    $shipping = $options['shipping'];
                } else {

                    $shipping = '';
                }

                if (isset($options['cost_of_goods'])) {

                    $cost_of_goods = $options['cost_of_goods'];
                } else {

                    $cost_of_goods = '';
                }

                if (isset($options['billing_address'])) {

                    $billing_address = $options['billing_address'];
                } else {

                    $billing_address = '';
                }

                if (isset($options['billing_phone'])) {

                    $billing_phone = $options['billing_phone'];
                } else {

                    $billing_phone = '';
                }

                if (isset($options['email_supplier'])) {

                    $email_supplier = $options['email_supplier'];
                } else {

                    $email_supplier = '';
                }

                if (isset($options['hide_suppliername'])) {

                    $hide_suppliername = $options['hide_suppliername'];
                } else {

                    $hide_suppliername = '';
                }

                if (isset($options['hide_suppliername_on_product_page'])) {

                    $hide_suppliername_on_product_page = $options['hide_suppliername_on_product_page'];
                } else {

                    $hide_suppliername_on_product_page = '';
                }

                if (isset($options['hideorderdetail_suppliername'])) {

                    $hideorderdetail_suppliername = $options['hideorderdetail_suppliername'];
                } else {

                    $hideorderdetail_suppliername = '';
                }

                if (isset($options['shipping_address'])) {

                    $shipping_address = $options['shipping_address'];
                } else {

                    $shipping_address = '';
                }

                if (isset($options['product_image'])) {

                    $product_image = $options['product_image'];
                } else {

                    $product_image = '';
                }

                if (isset($options['store_name'])) {

                    $store_name = $options['store_name'];
                } else {

                    $store_name = '';
                }

                if (isset($options['store_address'])) {

                    $store_address = $options['store_address'];
                } else {

                    $store_address = '';
                }

                if (isset($options['complete_email'])) {

                    $complete_email = $options['complete_email'];
                } else {

                    $complete_email = '';
                }

                if (isset($options['order_complete_link'])) {

                    $order_complete_link = $options['order_complete_link'];
                } else {

                    $order_complete_link = '';
                }

                if (isset($options['type_of_package'])) {

                    $type_of_package = $options['type_of_package'];
                } else {

                    $type_of_package = '';
                }

                if (isset($options['customer_note'])) {

                    $customer_note = $options['customer_note'];
                } else {

                    $customer_note = '';
                }
				
				if (isset($options['customer_email'])) {
					$customer_email = $options['customer_email'];
				} else {
					$customer_email = '';
				}
				
				if ($customer_email == '1') {
					$customer_email = ' checked="checked" ';
				} else {
					$customer_email = ' ';
				}

                // Aliexpress Settings for setting variable creation 

                if (isset($options['ali_cbe_enable_name'])) {

                    $ali_cbe_enable_setting = $options['ali_cbe_enable_name'];
                } else {

                    $ali_cbe_enable_setting = '';
                }

                if (isset($options['ali_cbe_price_rate_name'])) {

                    if ($options['ali_cbe_price_rate_name'] == 'ali_cbe_price_rate_percent_offset') {

                        $ali_cbe_price_rate_selected_1 = 'selected';

                        $ali_cbe_price_rate_selected_2 = '';
                    } else {

                        $ali_cbe_price_rate_selected_1 = '';

                        $ali_cbe_price_rate_selected_2 = 'selected';
                    }
                }

                // if (isset($options['ali_cbe_price_rate_value_name'])) { // $ali_cbe_price_rate_value_setting = $options['ali_cbe_price_rate_value_name']; // } else { // $ali_cbe_price_rate_value_setting = ''; // } // For Checked Checkbox 

                if ($csvcheck == '1') {

                    $csvInMail = ' checked="checked" ';
                } else {

                    $csvInMail = ' ';
                }

                if ($supp_notification == '1') {

                    $supp_notification_attr = ' checked="checked" ';
                } else {

                    $supp_notification_attr = ' ';
                }

                if ($full_information == '1') {

                    $checkfull = ' checked="checked" ';

                    $disabledPdfOptions = '';
                } else {

                    $checkfull = ' ';

                    $disabledPdfOptions = 'disabled';
                }

                if ($show_logo == '1') {

                    $logoshow = ' checked="checked" ';

                    $show_logo_option = 'style="display:block"';
                } else {

                    $logoshow = ' ';

                    $show_logo_option = 'style="display:hide"';
                }

                if ($order_date == '1') {

                    $date_order = ' checked="checked" ';
                } else {

                    $date_order = ' ';
                }

                if ($smtp_check == '1') {

                    $check_smtp = ' checked="checked" ';
                } else {

                    $check_smtp = ' ';
                }

                if ($std_mail == '1' || $std_mail == '') {

                    $std_mail = ' checked="checked" ';
                } else {

                    $std_mail = ' ';
                }

                if ($checkout_order_number == '1') {

                    $checkout_order_number = ' checked="checked" ';
                } else {

                    $checkout_order_number = ' ';
                }

                if ($show_pay_type == '1' || $show_pay_type == '') {

                    $show_pay_type = ' checked="checked" ';
                } else {

                    $show_pay_type = ' ';
                }

                if ($cnf_mail == '1') {

                    $cnf_mail = ' checked="checked" ';
                } else {

                    $cnf_mail = ' ';
                }

                if ($cc_mail == '1' || $cc_mail == '') {

                    $cc_mail = ' checked="checked" ';
                } else {

                    $cc_mail = ' ';
                }

                if ($hide_shipping_price == '1') {

                    $hide_shipping_price = ' checked="checked" ';
                } else {

                    $hide_shipping_price = ' ';
                }

                if ($hide_tax == '1') {

                    $hide_tax = ' checked="checked" ';
                } else {

                    $hide_tax = ' ';
                }

                if ($total_price == '1') {

                    $total_price = ' checked="checked" ';
                } else {

                    $total_price = ' ';
                }

                if ($product_price == '1') {

                    $price_product = ' checked="checked" ';

                    $product_price_option = 'style="display:block"';
                } else {

                    $price_product = ' ';

                    $product_price_option = 'style="display:hide"';
                }

                if ($shipping == '1') {

                    $product_shipping = ' checked="checked" ';

                    $show_shipping_information_option = 'style="display:block"';
                } else {

                    $product_shipping = ' ';

                    $show_shipping_information_option = 'style="display:hide"';
                }

                if ($cost_of_goods == '1' || $cost_of_goods == '') {

                    $cost_of_goods = ' checked="checked" ';
                } else {

                    $cost_of_goods = ' ';
                }

                if ($billing_address == '1') {

                    $address_billing = ' checked="checked" ';

                    $billing_address_option = 'style="display:block"';
                } else {

                    $address_billing = ' ';

                    $billing_address_option = 'style="display:hide"';
                }

                if ($billing_phone == '1') {

                    $phone_billing = ' checked="checked" ';
                } else {

                    $phone_billing = ' ';
                }

                if ($email_supplier == '1') {

                    $supplier_email = ' checked="checked" ';
                } else {

                    $supplier_email = ' ';
                }

                if ($hide_suppliername == '1') {

                    $suppliername_hide = ' checked="checked" ';
                } else {

                    $suppliername_hide = ' ';
                }

                if ($hide_suppliername_on_product_page == '1') {

                    $hide_suppliername_on_product_page = ' checked="checked" ';
                } else {

                    $hide_suppliername_on_product_page = ' ';
                }

                if ($hideorderdetail_suppliername == '1') {

                    $suppliername_hideorderdetail = ' checked="checked" ';
                } else {

                    $suppliername_hideorderdetail = ' ';
                }

                if ($shipping_address == '1') {

                    $address_shipping = ' checked="checked" ';

                    $shipping_address_option = 'style="display:block"';
                } else {

                    $address_shipping = ' ';

                    $shipping_address_option = 'style="display:hide"';
                }

                if ($product_image == '1') {

                    $image_product = ' checked="checked" ';

                    $product_image_option = 'style="display:block"';
                } else {

                    $image_product = ' ';

                    $product_image_option = 'style="display:hide"';
                }

                if ($store_name == '1') {

                    $name_store = ' checked="checked" ';
                } else {

                    $name_store = ' ';
                }

                if ($store_address == '1') {

                    $address_store = ' checked="checked" ';
                } else {

                    $address_store = ' ';
                }

                if ($complete_email == '1') {

                    $email_complete = ' checked="checked" ';
                } else {

                    $email_complete = ' ';
                }

                if ($order_complete_link == '1') {

                    $link_complete_order = ' checked="checked" ';
                } else {

                    $link_complete_order = ' ';
                }

                if ($type_of_package == '1') {

                    $type_of_package = ' checked="checked" ';

                    $type_of_package_option = 'style="display:block"';
                } else {

                    $type_of_package = ' ';

                    $type_of_package_option = 'style="display:hide"';
                }

                if ($customer_note == '1') {

                    $customer_note = ' checked="checked" ';
                } else {

                    $customer_note = ' ';
                }

                // Aliexpress Settings for checkbox value 

                if ($ali_cbe_enable_setting == '1') {

                    $ali_cbe_enable_checkbox = ' checked="checked" ';
                } else {

                    $ali_cbe_enable_checkbox = ' ';
                }

                if (isset($options['ali_cbe_price_rate_value_name'])) {

                    if ($options['ali_cbe_price_rate_value_name'] < 1 || !is_numeric($options['ali_cbe_price_rate_value_name'])) {

                        $options['ali_cbe_price_rate_value_name'] = 0;
                    }
                }

                if (isset($options['renewal_email'])) {

                    $renewal_email = $options['renewal_email'];
                } else {

                    $renewal_email = '';
                }

                if ($renewal_email == '1') {

                    $renewal_email = ' checked="checked" ';
                } else {

                    $renewal_email = ' ';
                }

                $woocommerce_url = plugins_url() . '/woocommerce/';

                //echo '<form method="post" id="mainform" action="" enctype="multipart/form-data">'; 

                echo '<ul class="wc-dropship-setting-tabs"> 

                    <li data-id="general_settings" class="active">General Settings</li> 

                    <li data-id="supplier_email_notifications">Supplier Email Notifications</li> 

                    <li data-id="packing_slips">Packing Slips</li> 

                    <li data-id="customised_supplier_emails">Customised Supplier Emails</li> 

                    <li data-id="smtp_options">SMTP Options</li> 

				</ul>';

				echo '<div class="drop-setting-section active" id="general_settings">';

				echo '<h3>AliExpress Chrome Browser Extension (CBE) Settings</h3>';

				echo '<table> 

					<tr> 
                        <td> 
                        <span> 

                            <td><input name="ali_cbe_enable_name" id="ali_cbe_enable_name" type="checkbox" ' . $ali_cbe_enable_checkbox . ' /></td> 

                        </span> 

                        <td> 

					   <td><h4><label for="ali_cbe_enable_name">Enable Support for the AliExpress CBE:</label></h4></td> 
					</tr> 

				</table>';

				if (isset($ali_cbe_enable_setting)) {

					if ($ali_cbe_enable_setting == '1') {

					   echo '<table> 

                            <tr> 

                                <td><h4>Generate AliExpress API Key: </h4></td> 

                                <td> 
                                    <span> 
                                        <button type="button" id="generate_ali_key" class="button-primary">Generate AliExpress API Key</button>
                                    </span> 
                                <td> 
                            </tr> 
                        </table>';

    					echo '<table> 

                            <tr id="hide_key"> 
                                <td id="ali_api_key"></td> 
                            </tr> 

    					</table>';

    					echo '<table> 

                            <tr> 

                                <td><h4>Price Markup Method:</h4></td> 

                                <td><img class="help_tip" data-tip="This setting controls whether the prices listed for products on your WooCommerce store are marked up by a given percentage or by a fixed amount when compared to the AliExpress supplier&apos;s prices" src="' . $woocommerce_url . 'assets/images/help.png" height="16" width="16"></td> 

                                <td>

                                    <span> 
                                        <td> 
                                            <select name="ali_cbe_price_rate_name"> 

                                            <option value="ali_cbe_price_rate_percent_offset" ' . @$ali_cbe_price_rate_selected_1 . '>Percentage Offset</option> 

                                            <option value="ali_cbe_fixed_price_offset"' . @$ali_cbe_price_rate_selected_2 . '>Fixed Amount Offset</option> 

                                            </select>
                                        </td>
                                    </span> 
                                <td>
                            </tr> 
                        </table>';

						echo '<table> 

                            <tr> 
                                <td><h4>Markup Offset Value:</h4></td> 

                                <td><img class="help_tip" data-tip="This setting will either contain a percentage or fixed amount based on the chosen price markup method above" src="' . $woocommerce_url . 'assets/images/help.png" height="16" width="16"></td>
                                <td> 
                                    <span> 
                                        <td><input name="ali_cbe_price_rate_value_name" value="' . @$options['ali_cbe_price_rate_value_name'] . '" size="5" /></td> 
                                    </span> 
                                <td> 
                            </tr> 
                        </table>';
                    }
                }

				echo '</div>';

				echo '<div class="drop-setting-section" id="supplier_email_notifications">';

				echo '<h3>Supplier Email Notifications</h3> 

                <p>When an order&apos;s status switches to processing, emails are sent to each supplier to notify them to ship their products. You can set a custom message for the suppliers in the box below to be included in these emails</p> 

                <table> 
                    <tr> 
                        <td><label for="email_order_note">Email order note:</label></td> 
                        <td><img class="help_tip" data-tip="This note will appear on emails that suppliers will receive with your order notifications" src="' . $woocommerce_url . 'assets/images/help.png" height="16" width="16"></td> 

                        <td><textarea name="email_order_note" cols="45" >' . @$options['email_order_note'] . '</textarea></td> 
                    </tr> 
                </table>';

				echo '<p></p>

                    <table>

                        <tr>
                            <td><input name="renewal_email" id="renewal_email" type="checkbox" ' . $renewal_email . ' /></td>
                            <td><label for="renewal_email">Do not send renewal email to suppliers:</label></td>
                            <td><img class="help_tip" data-tip="If checked this option will not send subscription renewal email\'s to suppliers" src="' . $woocommerce_url . 'assets/images/help.png" height="16" width="16"></td> 
                        </tr>
                    </table>';

					echo '<h3>.CSV File Inventory Update Settings</h3> 

                        <p>These options relate to how your store processes data imported from CSV spreadsheet files, if you receive them from your supplier</p> 

                        <table> 

                            <tr> 

                                <td><label for="inventory_pad">Inventory Padding:</label></td> 

                                <td><img class="help_tip" data-tip="If the supplier&apos;s stock falls below this number on an imported spreadsheet, the item will be considered out of stock in your store. <br>Set this to zero if you want to directly use the inventory numbers your supplier gives you, or higher if you want to ensure that they don&apos;t sell out of their products before you make a sale." src="' . $woocommerce_url . 'assets/images/help.png" height="16" width="16"></td> 

                                <td><input name="inventory_pad" value="' . @$options['inventory_pad'] . '" size="1" /></td> 
                            </tr> 			
					<!--<tr> 

    					 <td valign="top"><label for="url_product_feed">Url to product feed:</label></td> 
    					 <td><img class="help_tip" data-tip="After updating the in-stock/out of stock status this url will be called to regenerate your product feed. <br />(Just leave blank if you don\'t have a product feed)" src="' . $woocommerce_url . 'assets/images/help.png" height="16" width="16"></td> 

    					 <td> 
    					 <input name="url_product_feed" value="' . @$options['url_product_feed'] . '" size="100" /> 
    					 </td> 
					</tr>--> 

					</table>';

					echo '</div>';

					echo '<div class="drop-setting-section" id="packing_slips">';

					echo '<h3>Packing Slips</h3> 

					   <table> 
                            <tr> 
                                <td><input name="full_information" id="full_information" class="fullinfo miscellaneous_packing_slip_options_master_checkbox" type="checkbox" ' . $checkfull . ' /></td> 

                                <td><label for="full_information"><b>Attach PDF to supplier Email?:</b></label></td> 
                                </tr> 
                            </table> 
    					<br/><br/> 

    					<div class="packing-slip-sections"> 

    					<h4>Header</h4> 

    					<table> 
                            <tr> 
                                <td><label for="packing_slip_header">Packing Slip Title:</label></td> 

                                <td><img class="help_tip" data-tip="This will be the custom title of the packing slip" src="' . $woocommerce_url . 'assets/images/help.png" height="16" width="16"></td> 

                                <td><input name="packing_slip_header" value="' . @$options['packing_slip_header'] . '" size="100" /></td> 
                            </tr> 
                        </table> 
                        <p></p> 

                        <table> 

                            <tr> 

                                <td><input name="show_logo" id="show_logo" class="miscellaneous_packing_slip_options_checkbox" data-id="show_logo" type="checkbox" ' . $logoshow . ' ' . $disabledPdfOptions . ' /></td> 

                                <td><label for="show_logo">Show logo in the header:</label></td> 
                            </tr> 
                        </table> 

                        <p></p> 

                        <div class="inner-toggle show_logo" ' . $show_logo_option . '> 

                            <p style="margin-left:50px;"><b>NOTE:</b> For best results, please keep logo dimensions within 200x60 px</p> 

                            <table style="margin-left:50px;"> 

                                <tr> 

                                    <td style="width:150px"><label for="packing_slip_url_to_logo" >Url to Logo:</label></td> 

                                    <td><img class="help_tip" data-tip="Please specify the URL where your company&apos;s logo can be found" src="' . $woocommerce_url . 'assets/images/help.png" height="16" width="16"></td> 

                                    <td><input name="packing_slip_url_to_logo" value="' . @$options['packing_slip_url_to_logo'] . '" size="75" /></td> 
                                </tr> 
                            </table> 
                            <p></p> 

                            <table style="margin-left:50px;"> 
                                <tr> 
                                    <td style="width:150px"><label for="packing_slip_url_to_logo_width" >Logo Width:</label></td> 

                                    <td><img class="help_tip" data-tip="Please specify the width of your company logo in pixels" src="' . $woocommerce_url . 'assets/images/help.png" height="16" width="16"></td> 

                                    <td><input name="packing_slip_url_to_logo_width" value="' . @$options['packing_slip_url_to_logo_width'] . '" size="1" />
                                    </td> 
                                </tr> 
                            </table>
                        </div>';

                        echo '<p></p> 

                            <table> 
                                <tr> 
                                    <td><input name="order_date" id="show_order_date" class="miscellaneous_packing_slip_options_checkbox" type="checkbox" ' . $date_order . ' ' . $disabledPdfOptions . ' /></td> 

                                    <td><label for="show_order_date">Show order date beside order number:</label></td> 
                                </tr> 
                            </table>';

						echo '<p></p> 

                            <table> 
                                <tr> 
                                    <td><input name="shipping" id="show_shipping_information" data-id="show_shipping_information" class="miscellaneous_packing_slip_options_checkbox" type="checkbox" ' . $product_shipping . ' ' . $disabledPdfOptions . ' /></td> 

                                    <td><label for="show_shipping_information">Show shipping information:</label></td> 
                                </tr> 
                            </table>';

						echo '<p></p> 

                            <div class="inner-toggle show_shipping_information" ' . $show_shipping_information_option . '> 

                                <p style="margin-left:50px;"><b>NOTE:</b> For best results, please make sure that any custom terms or phrases listed below are kept to a reasonable legnth. If your terms are too long, it may cause text wrapping and alignment issues with your packing slips.</p> 

                                <table style="margin-left:50px;"> 
                                    <tr> 
                                        <td style="width:150px"><label for="dropship_chosen_shipping_method" >Chosen Shipping Method Label:</label></td> 

                                        <td><img class="help_tip" data-tip="Please specify chosen Shipping Method Label" src="' . $woocommerce_url . 'assets/images/help.png" height="16" width="16"></td> 

                                        <td><input name="dropship_chosen_shipping_method" value="' . @$options['dropship_chosen_shipping_method'] . '" size="30" maxlength="50" /></td> 
                                    </tr> 
                                </table> 
                                <p></p> 

                                <table style="margin-left:50px;"> 
                                    <tr> 
                                        <td style="width:150px"><label for="dropship_payment_type" >Payment Type Label:</label></td> 

                                        <td><img class="help_tip" data-tip="Please specify chosen Payment Type Label" src="' . $woocommerce_url . 'assets/images/help.png" height="16" width="16"></td> 

                                        <td><input name="dropship_payment_type" value="' . @$options['dropship_payment_type'] . '" size="30" maxlength="50"/></td>
                                    </tr> 
                                </table>
                            </div>';

						echo '<p></p> 

                            <table> 
                                <tr> 
                                    <td><input name="customer_note" id="show_customer_note" class="miscellaneous_packing_slip_options_checkbox" type="checkbox" ' . $customer_note . ' ' . $disabledPdfOptions . ' /></td> 

                                    <td><label for="show_customer_note">Display the "Customer Note" into the Dropshipper packing slip:</label></td> 
                                </tr> 
                            </table>
                        </div>';
                        
                        echo '<br/><br/> 

                            <div class="packing-slip-sections"> 
                                <h4>Products</h4> 

                                <table> 
                                    <tr> 
                                        <td><input name="product_image" id="product_image" data-id="product_image" class="miscellaneous_packing_slip_options_checkbox" type="checkbox" ' . $image_product . ' ' . $disabledPdfOptions . ' /></td> 

                                        <td><label for="product_image">Show product thumbnail image:</label></td> 
                                    </tr> 
                                </table> 
                                <p></p> 

                                <div class="inner-toggle product_image" ' . $product_image_option . '> 

                                    <table style="margin-left:50px;"> 
                                        <tr> 
                                            <td style="width:150px"><label for="dropship_image" >Image Label:</label></td> 

                                            <td><img class="help_tip" data-tip="Please specify Image Label" src="' . $woocommerce_url . 'assets/images/help.png" height="16" width="16"></td> 

                                            <td><input name="dropship_image" value="' . @$options['dropship_image'] . '" size="30" maxlength="50" /></td> 
                                        </tr> 
                                    </table>
                                </div>';

                            echo '<p></p> 

                                <table> 
                                    <tr> 
                                    <td style="width:150px"><label for="dropship_sku" >SKU Label:</label></td> 

                                    <td><img class="help_tip" data-tip="Please specify SKU label" src="' . $woocommerce_url . 'assets/images/help.png" height="16" width="16"></td> 

                                    <td><input name="dropship_sku" value="' . @$options['dropship_sku'] . '" size="30" maxlength="50" /></td> 
                                    </tr> 
                                </table> 

                                <table> 
                                    <tr> 
                                        <td style="width:150px"><label for="dropship_product" >Product Label:</label></td> 

                                        <td><img class="help_tip" data-tip="Please specify Product label" src="' . $woocommerce_url . 'assets/images/help.png" height="16" width="16"></td> 

                                        <td><input name="dropship_product" value="' . @$options['dropship_product'] . '" size="30" maxlength="50" /></td> 
                                    </tr> 
                                </table> 

                                <table> 
                                    <tr> 
                                        <td style="width:150px"><label for="dropship_quantity">Quantity Label:</label></td> 

                                        <td><img class="help_tip" data-tip="Please specify Quantity label" src="' . $woocommerce_url . 'assets/images/help.png" height="16" width="16"></td> 

                                        <td><input name="dropship_quantity" value="' . @$options['dropship_quantity'] . '" size="30" maxlength="50"/></td> 
                                    </tr> 
                                </table>';

                            echo '<p></p> 

                                <table> 
                                    <tr> 
                                        <td><input name="type_of_package" id="type_of_package" data-id="type_of_package" class="miscellaneous_packing_slip_options_checkbox" type="checkbox" ' . $type_of_package . ' ' . $disabledPdfOptions . ' /></td> 

                                        <td><label for="type_of_package">Additional field in the "Add/Edit Product" to specify the "Type of Package" 

                                        <img class="help_tip" data-tip="This will also be added as an additional column in the packing slip" style="margin: 0 0 0 0px;" src="' . $woocommerce_url . 'assets/images/help.png" height="16" width="16">: </label></td> 
                                    </tr> 
                                </table>';

                            echo '<p></p> 

                                <div class="inner-toggle type_of_package" ' . $type_of_package_option . '> 
                                    <table style="margin-left:50px;"> 
                                        <tr> 
                                            <td style="width:150px"><label for="type_of_package_conversion">Type Of Package Label:</label></td> 

                                            <td><img class="help_tip" data-tip="Please specify Type Of Package label" src="' . $woocommerce_url . 'assets/images/help.png" height="16" width="16"></td> 

                                            <td><input name="type_of_package_conversion" value="' . @$options['type_of_package_conversion'] . '" size="30" maxlength="50"/></td> 
                                        </tr> 
                                    </table>
                                </div>';

                            echo '<p></p> 

                                <table> 
                                    <tr> 
                                        <td><input name="product_price" id="product_price" data-id="product_price" class="miscellaneous_packing_slip_options_checkbox" type="checkbox" ' . $price_product . ' ' . $disabledPdfOptions . ' /></td> 

                                        <td><label for="product_price">Show product prices:</label></td> 
                                    </tr> 
                                </table>';

                            echo '<p></p> 

                                <div class="inner-toggle product_price" ' . $product_price_option . '> 

                                    <table style="margin-left:50px;"> 
                                        <tr> 
                                            <td style="width:150px"><label for="dropship_price">Price Label:</label></td> 

                                            <td><img class="help_tip" data-tip="Please specify Price label" src="' . $woocommerce_url . 'assets/images/help.png" height="16" width="16"></td> 

                                            <td><input name="dropship_price" value="' . @$options['dropship_price'] . '" size="30" maxlength="50" /></td>
                                        </tr> 
                                    </table>
                                </div>';

                            echo '<p></p> 

                                <table> 
                                    <tr> 
                                        <td><input name="cost_of_goods" id="cost_of_goods" class="miscellaneous_packing_slip_options_checkbox" type="checkbox" ' . $cost_of_goods . ' ' . $disabledPdfOptions . ' /></td> 

                                        <td><label for="cost_of_goods">Show Cost instead of Sell Price?:</label></td>
                                    </tr> 
                                </table>
                            </div>';

                            echo '<br/><br/> 

                                <div class="packing-slip-sections"> 
                                    <h4>Company & Detail</h4> 

                                    <table> 
                                        <tr> 
                                        <td style="width:150px"><label for="packing_slip_company_name" >Company Name:</label></td> 

                                        <td><img class="help_tip" data-tip="Please enter the name of your company" src="' . $woocommerce_url . 'assets/images/help.png" height="16" width="16"></td> 

                                        <td><input name="packing_slip_company_name" value="' . @$options['packing_slip_company_name'] . '" size="30" /></td> 
                                        </tr> 
                                    </table>';

                                echo '<p></p> 

                                    <div class="inner-toggle"> 

                                        <table style="margin-left:50px;"> 
                                            <tr> 
                                            <td ><label for="dropship_company_address">Company Address Label:</label></td> 

                                            <td><img class="help_tip" data-tip="Please specify the Company Address Label" src="' . $woocommerce_url . 'assets/images/help.png" height="16" width="16"></td> 

                                            <td><input name="dropship_company_address" value="' . @$options['dropship_company_address'] . '" size="30" maxlength="50" /></td> 
                                            </tr> 
                                            </tr> 
                                        </table>
                                    </div>';

                                echo '<p></p> 

                                    <table> 

                                        <tr> 
                                            <td style="width:150px"><label for="packing_slip_address" >Address:</label></td> 

                                            <td><img class="help_tip" data-tip="Please enter your company&apos;s mailing address. This address will appear on your packing slips" src="' . $woocommerce_url . 'assets/images/help.png" height="16" width="16"></td> 

                                            <td><input name="packing_slip_address" value="' . @$options['packing_slip_address'] . '" size="50" /></td> 
                                        </tr> 
                                    </table> 
                                    <p></p> 
                                    <table> 
                                        <tr> 
                                        <td style="width:150px"><label for="packing_slip_customer_service_email" >Customer Service Email:</label></td> 

                                        <td><img class="help_tip" data-tip="Please enter the email address at which customers can reach your company if they have service issues" src="' . $woocommerce_url . 'assets/images/help.png" height="16" width="16"></td> 

                                        <td><input name="packing_slip_customer_service_email" value="' . @$options['packing_slip_customer_service_email'] . '" size="30" /></td> 
                                        </tr> 
                                    </table> 

                                    <p></p> 

                                    <table> 
                                        <tr> 
                                        <td style="width:150px"><label for="packing_slip_customer_service_phone">Customer Service Phone Number:</label></td> 

                                        <td><img class="help_tip" data-tip="Please enter the phone number at which customers can reach your company if they have service issues" src="' . $woocommerce_url . 'assets/images/help.png" height="16" width="16"></td> 

                                        <td><input name="packing_slip_customer_service_phone" value="' . @$options['packing_slip_customer_service_phone'] . '" size="10" /></td> 
                                        </tr> 
                                    </table>';

                                echo '<p></p> 

                                    <table> 
                                        <tr> 
                                            <td><input name="shipping_address" id="shipping_address" data-id="shipping_address" class="miscellaneous_packing_slip_options_checkbox" type="checkbox" ' . $address_shipping . ' ' . $disabledPdfOptions . ' /></td> 

                                            <td><label for="shipping_address">Show shipping address at the bottom:</label></td> 
                                        </tr> 
                                    </table>';

                                echo '<p></p> 

                                    <div class="inner-toggle shipping_address" ' . $shipping_address_option . '> 

                                        <table style="margin-left:50px;"> 
                                            <tr> 
                                                <td style="width:150px"><label for="dropship_shipping_address_email">Shipping Address Label:</label></td> 

                                                <td><img class="help_tip" data-tip="Please specify Shipping Address Label" src="' . $woocommerce_url . 'assets/images/help.png" height="16" width="16"></td> 

                                                <td><input name="dropship_shipping_address_email" value="' . @$options['dropship_shipping_address_email'] . '" size="30" maxlength="50" /></td> 
                                            </tr> 
                                        </table>
                                    </div>';

								echo '<p></p> 

                                    <table> 
                                        <tr> 
                                            <td><input name="billing_address" id="billing_address" data-id="billing_address" class="miscellaneous_packing_slip_options_checkbox" type="checkbox" ' . $address_billing . ' ' . $disabledPdfOptions . ' /></td> 

                                            <td><label for="billing_address">Show billing address at the bottom:</label></td> 
                                        </tr> 
                                    </table>';

								echo '<p></p> 

                                    <div class="inner-toggle billing_address" ' . $billing_address_option . '> 

                                        <table style="margin-left:50px;"> 

                                            <tr> 

                                            <td style="width:150px"><label for="dropship_billing_address_email">Billing Address Label:</label></td> 

                                            <td><img class="help_tip" data-tip="Please specify Billing Address Label" src="' . $woocommerce_url . 'assets/images/help.png" height="16" width="16"></td> 

                                            <td><input name="dropship_billing_address_email" value="' . @$options['dropship_billing_address_email'] . '" size="30" maxlength="50"/></td> 

                                            </tr> 
                                        </table>
                                    </div>
                                </div>';

                            if (empty(@$options['dropship_additional_comment'])) {

							    $additionalCommentDefault = 'Sign : ____________________________________________<br> 

                                Print : ____________________________________________<br> 

                                Date : ____________________________________________<br>';
								
                            } else {

								$additionalCommentDefault = @$options['dropship_additional_comment'];
							}

							echo '<br/><br/> 

                                <div class="packing-slip-sections"> 

                                    <h4>Footer</h4> 
                                    <p><b>Any additional comment to be displayed</b></p> 
                                    <p>Max length: 200 characters</p> 
                                    <p>NOTE: Please make sure this content as small as possible so that it fits properly at the bottom of Pdf slip.</p> 

                                    <table> 
                                        <tr> 
                                        <td style="width:150px"><label for="dropship_additional_comment" >Comments:</label></td> 

                                        <td><img class="help_tip" data-tip="Please specify Additional Comment" src="' . $woocommerce_url . 'assets/images/help.png" height="16" width="16"></td> 

                                        <td><textarea name="dropship_additional_comment" maxlength="200" rows="6" cols="45">' . $additionalCommentDefault . '</textarea></td> 
                                        </tr> 
                                    </table>';

                                    echo '<p></p> 

                                        <table> 
                                            <tr> 
                                                <td style="width:150px"><label for="packing_slip_thankyou">Thank You Message:</label></td> 

                                                <td><img class="help_tip" data-tip="This message will appear at the bottom of the packing slip" src="' . $woocommerce_url . 'assets/images/help.png" height="16" width="16"></td> 

                                                <td><textarea name="packing_slip_thankyou" cols="45" >' . @$options['packing_slip_thankyou'] . '</textarea></td>
                                            </tr> 

                                        </table>
                                </div>';

							echo '<br/><br/>';

							echo '<div class="packing-slip-sections"> 

                                <h4>Send Order Details to Suppliers</h4> 
                                <p></p> 
                                <table> 
                                    <tr> 
                                        <td><input name="supp_notification" id="supp_notification" class="miscellaneous_packing_slip_options_checkbox" type="checkbox" ' . $supp_notification_attr . ' ' . $disabledPdfOptions . ' /></td> 

                                        <td><label for="supp_notification">Do not send email notifications to supplier:</label></td> 
                                    </tr> 
                                </table> 

                                <p>This option controls whether or not you want to send a .CSV spreadsheet file as an attachment with the regular order notification emails that are sent to your suppliers</p> 

                                <table> 
                                    <tr> 
                                        <td><input name="csv_inmail" id="csv_inmail" class="miscellaneous_packing_slip_options_checkbox" type="checkbox" ' . $csvInMail . ' ' . $disabledPdfOptions . ' /></td> 

                                        <td><label for="csv_inmail">Send CSV with Supplier Notifications:</label></td> 
                                    </tr> 
                                </table>
                            </div>';

							echo '<p></p> 

                                <table> 
                                    <tr> 
                                    <td><input name="total_price" id="total_price" class="miscellaneous_packing_slip_options_checkbox" type="checkbox" ' . $total_price . ' ' . $disabledPdfOptions . ' /></td> 

                                    <td><label for="total_price">Show the total price in the packing slip:</label></td> 
                                    </tr> 
                                </table>';

                            echo '<p></p> 

                                <table> 
                                    <tr> 
                                        <td><input name="hide_shipping_price" id="hide_shipping_price" class="miscellaneous_packing_slip_options_checkbox" type="checkbox" ' . $hide_shipping_price . ' ' . $disabledPdfOptions . ' /></td> 

                                        <td><label for="hide_shipping_price">Hide the shipping cost in the packing slip:</label></td> 
                                    </tr> 
                                </table>';

                            echo '<p></p> 

                                <table> 
                                    <tr> 
                                        <td><input name="hide_tax" id="hide_tax" class="miscellaneous_packing_slip_options_checkbox" type="checkbox" ' . $hide_tax . ' ' . $disabledPdfOptions . ' /></td> 

                                        <td><label for="hide_tax">Hide Tax in supplier email:</label></td> 
                                    </tr> 

                                </table>';

                            echo '<p></p> 

                                <table> 
                                    <tr> 
                                        <td><input name="billing_phone" id="show_customer_phone" class="miscellaneous_packing_slip_options_checkbox" type="checkbox" ' . $phone_billing . ' ' . $disabledPdfOptions . ' /></td> 

                                        <td><label for="show_customer_phone">Include the customer&apos;s phone number in the packing slip:</label></td> 
                                    </tr> 
                                </table>';

                            echo '<p></p> 

                                <table>
                                    <tr> 
                                        <td><input name="hide_suppliername" id="hidesuppliername" class="miscellaneous_packing_slip_options_checkbox" type="checkbox" ' . $suppliername_hide . ' ' . $disabledPdfOptions . ' /></td> 

                                        <td><label for="hidesuppliername">Hide the supplier names on order confirmation emails:</label></td> 
                                    </tr>
                                </table>';

							echo '<p></p> 

                                <table>
                                    <tr>
                                        <td><input name="hide_suppliername_on_product_page" id="hide_suppliername_on_product_page" class="miscellaneous_packing_slip_options_checkbox" type="checkbox" ' . $hide_suppliername_on_product_page . ' ' . $disabledPdfOptions . ' /></td> 

                                        <td><label for="hide_suppliername_on_product_page">Show supplier names on product pages:</label></td> 
                                    </tr> 
                                </table>';

                            echo '<p></p> 

                                <table> 
                                    <tr> 
                                        <td><input name="hideorderdetail_suppliername" id="hideorderdetail_suppliername" class="miscellaneous_packing_slip_options_checkbox" type="checkbox" ' . $suppliername_hideorderdetail . ' ' . $disabledPdfOptions . ' /></td> 

                                        <td><label for="hideorderdetail_suppliername">Hide supplier names on the Order Details page:</label></td> 
                                    </tr> 
                                </table>';

                            echo '<p></p> 

                                <table> 
                                    <tr> 
                                        <td><input name="show_pay_type" id="show_pay_type" class="miscellaneous_packing_slip_options_checkbox" type="checkbox" ' . $show_pay_type . ' ' . $disabledPdfOptions . ' /></td> 

                                        <td><label for="show_pay_type">Show "Payment Type" in the notification email:</label></td>
                                    </tr>
                                </table>';
                            echo '<p></p>
                                <table>
                                    <tr>
                                        <td><input name="customer_email" id="customer_email" class="miscellaneous_packing_slip_options_checkbox" type="checkbox" ' . $customer_email . ' ' . $disabledPdfOptions . ' /></td>
                                        <td><label for="customer_email">Include "Customer email" into the Dropshipper packing slip:</label></td>
                                    </tr>
                                </table>';

							echo '<p></p> 

                                <table>
                                    <tr>
                                        <td><input name="store_name" id="store_name" class="miscellaneous_packing_slip_options_checkbox" type="checkbox" ' . $name_store . ' ' . $disabledPdfOptions . ' /></td> 

                                        <td><label for="store_name">Include store name in the order notification CSV filename:</label></td>
                                    </tr>
                                </table>';

                            echo '<p></p> 

                                <table> 
                                    <tr> 
                                        <td><input name="store_address" id="store_address" class="miscellaneous_packing_slip_options_checkbox" type="checkbox" ' . $address_store . ' ' . $disabledPdfOptions . ' /></td> 

                                        <td><label for="store_address">Include the store&apos;s URL in the order notification CSV filename:</label></td> 
                                    </tr> 
                                </table>';

                            echo '<p></p> 

                                <table>
                                    <tr>
                                        <td><input name="complete_email" id="complete_email" class="miscellaneous_packing_slip_options_checkbox" type="checkbox" ' . $email_complete . ' ' . $disabledPdfOptions . ' /></td> 

                                        <td><label for="complete_email">Send an additional email to the supplier when the order is completed:</label></td>
                                    </tr>
                                </table>';

                            echo '<p></p> 

                                <table> 
                                    <tr> 
                                        <td><input name="order_complete_link" id="order_complete_link" class="miscellaneous_packing_slip_options_checkbox" type="checkbox" ' . $link_complete_order . ' ' . $disabledPdfOptions . ' /></td> 

                                        <td><label for="order_complete_link">Allow suppliers to mark their orders as shipped by clicking a link on the email, without logging in to your store:</label></td>
                                    </tr>
                                </table>';

                            echo '<p></p> 

                                <table> 
                                    <tr> 
                                        <td><input name="email_supplier" id="email_supplier" class="miscellaneous_packing_slip_options_checkbox" type="checkbox" ' . $supplier_email . ' ' . $disabledPdfOptions . ' /></td> 

                                        <td><label for="email_supplier">When an admin creates a new supplier, send registration details to the supplier&apos;s email:</label></td>
                                    </tr>
                                </table>';

                            echo '<p></p> 

                                <table> 

                                    <tr> 

                                        <td><input name="cnf_mail" id="cnf_mail" class="miscellaneous_packing_slip_options_checkbox" type="checkbox" ' . $cnf_mail . ' ' . $disabledPdfOptions . ' /></td> 

                                        <td><label for="cnf_mail">Notify via email when suppliers open order notification emails 

                                        <img class="help_tip" data-tip="A notification will be sent to your store when a supplier opens order notification emails that you send out." style="margin: 0 0 0 0px;" src="' . $woocommerce_url . 'assets/images/help.png" height="16" width="16">: </label></td>
                                    </tr>
                                </table>';

                            echo '<p></p> 

                                <table> 
                                    <tr>
                                        <td><input name="std_mail" id="std_mail" class="miscellaneous_packing_slip_options_checkbox" type="checkbox" ' . $std_mail . ' ' . $disabledPdfOptions . ' /></td> 

                                        <td><label for="std_mail">Use the standard WooCommerce mail format for email notification:</label></td> 
                                    </tr>
                                </table>';

                            echo '<p></p> 

                                <table> 
                                    <tr> 
                                        <td><input name="checkout_order_number" id="checkout_order_number" class="miscellaneous_packing_slip_options_checkbox" type="checkbox" ' . $checkout_order_number . ' ' . $disabledPdfOptions . ' /></td> 

                                        <td><label for="checkout_order_number">Include order number field on checkout</label></td> 
                                    </tr> 

                                </table>';

                            echo '<p></p> 

                                <tr>
                                    <td><input name="cc_mail" id="cc_mail" class="miscellaneous_packing_slip_options_checkbox" type="checkbox" ' . $cc_mail . ' ' . $disabledPdfOptions . ' /></td> 

                                    <td><label for="cc_mail">Don&apos;t cc: the store admin when sending order notification emails to suppliers</label></td> 
                                </tr> 

                            </table>';

						echo '</div>';

						echo '<div class="drop-setting-section" id="customised_supplier_emails">';

						echo '<h3>Customised Supplier Emails</h3> 

                        <p></p> 

                        <table> 

                            <p></p> 

                            <tr> 

                                <td><label for="supplier_email_packing_slip_title_color" >Packing Slip Title Color:</label></td> 

                                <td><img class="help_tip" data-tip="EX: #000" src="' . $woocommerce_url . 'assets/images/help.png" height="16" width="16"></td> 

                                <td><input name="supplier_email_packing_slip_title_color" class="drop_color" type="text" value="' . @$options['supplier_email_packing_slip_title_color'] . '" size="30" /></td>
                            </tr> 

                            <tr> 
                                <td><label for="supplier_email_packing_slip_title_font_size" >Packing Slip Title Font Size:</label></td> 

                                <td><img class="help_tip" data-tip="EX: 24x" src="' . $woocommerce_url . 'assets/images/help.png" height="16" width="16"></td> 

                                <td><input name="supplier_email_packing_slip_title_font_size" type="text" value="' . @$options['supplier_email_packing_slip_title_font_size'] . '" size="30" /></td> 
                            </tr> 

					        <tr> 

                                <td><label for="supplier_email_background_color" >Email Background Color:</label></td> 

                                <td><img class="help_tip" data-tip="EX: #000" src="' . $woocommerce_url . 'assets/images/help.png" height="16" width="16"></td> 

                                <td><input name="supplier_email_background_color" class="drop_color" type="text" value="' . @$options['supplier_email_background_color'] . '" size="30" /></td>
                            </tr> 

                            <tr> 
                                <td><label for="supplier_email_order_note_font_size">Email Order Note Font Size :</label></td> 

                                <td><img class="help_tip" data-tip="EX: 14px" src="' . $woocommerce_url . 'assets/images/help.png" height="16" width="16"></td> 

                                <td><input name="supplier_email_order_note_font_size" type="text" value="' . @$options['supplier_email_order_note_font_size'] . '" size="30" /></td> 
                            </tr> 
                            <tr> 
                                <td><label for="supplier_email_order_note_font_color">Email Order Note Font Color :</label></td> 

                                <td><img class="help_tip" data-tip="EX: #000" src="' . $woocommerce_url . 'assets/images/help.png" height="16" width="16"></td> 

                                <td><input name="supplier_email_order_note_font_color" class="drop_color" type="text" value="' . @$options['supplier_email_order_note_font_color'] . '" size="30" /></td>
                            </tr> 

                            <tr> 

                                <td><label for="supplier_email_footer_message_font_size">Email Footer Message Font Size :</label></td> 

                                <td><img class="help_tip" data-tip="EX: 14px" src="' . $woocommerce_url . 'assets/images/help.png" height="16" width="16"></td> 

                                <td><input name="supplier_email_footer_message_font_size" type="text" value="' . @$options['supplier_email_footer_message_font_size'] . '" size="30" /></td> 
                            </tr> 

                            <tr> 
                                <td><label for="supplier_email_footer_message_font_color">Email Footer Message Font Color :</label></td> 

                                <td><img class="help_tip" data-tip="EX: #000" src="' . $woocommerce_url . 'assets/images/help.png" height="16" width="16"></td> 

                                <td><input name="supplier_email_footer_message_font_color" class="drop_color" type="text" value="' . @$options['supplier_email_footer_message_font_color'] . '" size="30" /></td>
                            </tr> 

                            <tr> 
                                <td><label for="supplier_email_body_font_size" >Body Font Size:</label></td> 

                                <td><img class="help_tip" data-tip="EX: 18px" src="' . $woocommerce_url . 'assets/images/help.png" height="16" width="16"></td> 

                                <td><input name="supplier_email_body_font_size" type="text" value="' . @$options['supplier_email_body_font_size'] . '" size="30" /></td>
                            </tr> 

                            <tr> 
                                <td><label for="supplier_email_body_font_color" >Body Font Color:</label></td> 

                                <td><img class="help_tip" data-tip="EX: #000" src="' . $woocommerce_url . 'assets/images/help.png" height="16" width="16"></td> 

                                <td><input name="supplier_email_body_font_color" class="drop_color" type="text" value="' . @$options['supplier_email_body_font_color'] . '" size="30" /></td>
                            </tr> 

                            <tr> 
                                <td><label for="supplier_email_bottom_sub_heading_font_size" >Bottom Sub Heading Font Size:</label></td> 

                                <td><img class="help_tip" data-tip="EX: 14px" src="' . $woocommerce_url . 'assets/images/help.png" height="16" width="16"></td> 

                                <td><input name="supplier_email_bottom_sub_heading_font_size" type="text" value="' . @$options['supplier_email_bottom_sub_heading_font_size'] . '" size="30" /></td>
                            </tr> 

                            <tr> 
                                <td><label for="supplier_email_bottom_sub_heading_font_color" >Bottom Sub Heading Font Color:</label></td> 

                                <td><img class="help_tip" data-tip="EX: #000" src="' . $woocommerce_url . 'assets/images/help.png" height="16" width="16"></td> 

                                <td><input name="supplier_email_bottom_sub_heading_font_color" class="drop_color" type="text" value="' . @$options['supplier_email_bottom_sub_heading_font_color'] . '" size="30" /></td>
                            </tr> 

                            <tr> 
                                <td><label for="supplier_email_bottom_sub_heading_content_font_size" >Bottom Sub Heading Content Font Size:</label></td> 

                                <td><img class="help_tip" data-tip="EX: 14px" src="' . $woocommerce_url . 'assets/images/help.png" height="16" width="16"></td> 

                                <td><input name="supplier_email_bottom_sub_heading_content_font_size" type="text" value="' . @$options['supplier_email_bottom_sub_heading_content_font_size'] . '" size="30" /></td> 
                            </tr> 

                            <tr> 
                                <td><label for="supplier_email_bottom_sub_heading_content_color" >Bottom Sub Heading Content Color:</label></td> 

                                <td><img class="help_tip" data-tip="EX: #000" src="' . $woocommerce_url . 'assets/images/help.png" height="16" width="16"></td> 

                                <td><input name="supplier_email_bottom_sub_heading_content_color" class="drop_color" type="text" value="' . @$options['supplier_email_bottom_sub_heading_content_color'] . '" size="30" /></td>
                            </tr>
                        </table>
                    </div>';

                    echo '<div class="drop-setting-section" id="smtp_options">';

                        echo '<h3>SMTP Options</h3> 

                            <p></p> 
                            <table> 
                                <tr>
                                    <td><input name="smtp_check" id="smtp_check" type="checkbox" ' . $check_smtp . ' /></td> 

                                    <td><label for="smtp_check">Check this option if you are using SMTP to send emails from your WooCommerce store:</label></td>
                                </tr> 
                            </table>';

                        echo '<h2>Email Sender Information (if left empty, emails sent from the store will use default WooCommerce settings)</h2> 

                            <table class="form-table">
                                <tbody> 
                                    <tr valign="top">
                                        <th scope="row" class="titledesc"> 

                                            <label for="from_name">Emails sent from the store should show this sender name: <img class="help_tip" data-tip="This option will override default functionality of woocommerce" src="' . $woocommerce_url . 'assets/images/help.png" height="16" width="16"></label>
                                        </th> 

                                        <td class="forminp forminp-text"> 

                                            <input name="from_name" id="from_name" type="text" size="30" value="' . $from_name . '" class="" placeholder=""> 
                                        </td> 
                                    </tr> 

                                    <tr valign="top"> 
                                        <th scope="row" class="titledesc"> 
                                            <label for="from_email">Emails sent from the store should show this sender email address:<img class="help_tip" data-tip="This option will override default WooCommerce functionality" src="' . $woocommerce_url . 'assets/images/help.png" height="16" width="16"></label>
                                        </th> 

                                        <td class="forminp forminp-email"> 
                                            <input name="from_email" id="from_email" type="email" size="30" value="' . $from_email . '" class="" placeholder="" multiple="multiple"> 

                                            <input type="hidden" name="show_admin_notice_option" value="0" />
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>';

					echo '<div class="slidesection_bkp">
					 <p></p>';

                return apply_filters('woocommerce_get_settings_' . $this->id, array(), $current_section);
            }

            /**
             *
             * Output the settings 
             */ 

            public function output() {

                global $current_section;

                $settings = $this->get_settings($current_section);

                WC_Admin_Settings::output_fields($settings);
            }

        }

        $settings[] = new WC_DS_Settings();

        return $settings;
    }

    add_filter('woocommerce_get_settings_pages', 'wc_ds_add_settings', 15);

    endif;