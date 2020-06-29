<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Account_Funds_Admin
 */
class WC_Account_Funds_Admin {

	/** @var Settings Tab ID */
	private $settings_tab_id = 'account_funds';

	/**
	 * Constructor
	 */
	public function __construct() {
		// Users
		add_filter( 'manage_users_columns', array( $this, 'manage_users_columns' ) );
		add_action( 'manage_users_custom_column', array( $this, 'manage_users_custom_column' ), 10, 3 );
		add_action( 'show_user_profile', array( $this, 'user_meta_fields' ) );
		add_action( 'edit_user_profile', array( $this, 'user_meta_fields' ) );
		add_action( 'personal_options_update', array( $this, 'save_user_meta_fields' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save_user_meta_fields' ) );

		// Settings
		add_action( 'woocommerce_settings_tabs_array', array( $this, 'add_woocommerce_settings_tab' ), 50 );
		add_action( 'woocommerce_settings_tabs_' . $this->settings_tab_id, array( $this, 'woocommerce_settings_tab_action' ), 10 );
		add_action( 'woocommerce_update_options_' . $this->settings_tab_id, array( $this, 'woocommerce_settings_save' ), 10 );
	}

	/**
	 * Add column
	 * @param  array $columns
	 * @return array
	 */
	public function manage_users_columns( $columns ) {
		if ( current_user_can( 'manage_woocommerce' ) ) {
			$columns['account_funds'] = __( 'Account Funds', 'woocommerce-account-funds' );
		}
		return $columns;
	}

	/**
	 * Column value
	 * @param  string $value
	 * @param  string $column_name
	 * @param  int $user_id
	 * @return string
	 */
	public function manage_users_custom_column( $value, $column_name, $user_id ) {
		if ( $column_name === 'account_funds' ) {
        	$funds = get_user_meta( $user_id, 'account_funds', true );
        	$funds = $funds ? $funds : 0;
        	$value = wc_price( $funds );
   		}
    	return $value;
	}

	/**
	 * Show Meta Fields
	 * @param  object $user
	 */
	public function user_meta_fields( $user ) {
		if ( current_user_can( 'manage_woocommerce' ) ) {
		    $funds = get_user_meta( $user->ID, 'account_funds', true );
		    $funds = $funds ? $funds : 0;
		    ?>
			<h3><?php _e( 'Account Funds', 'woocommerce-account-funds' ); ?></h3>
			<table class="form-table">
				<tr>
	                <th><label for="account_funds"><?php _e( 'Account Funds Amount', 'woocommerce-account-funds' ); ?></label></th>
	                <td>
	                    <input type="text" name="account_funds" id="account_funds" value="<?php echo esc_attr( $funds ); ?>" class="small-text" /><br/>
	                    <span class="description"><?php _e( 'Funds this user can use to purchase items', 'woocommerce-account-funds' ); ?></span>
	                </td>
	            </tr>
			</table>
			<?php
		}
	}

	/**
	 * Save meta fields.
	 *
	 * @version 2.1.6
	 *
	 * @param int $user_id User ID.
	 */
	public function save_user_meta_fields( $user_id ) {
		if ( isset( $_POST['account_funds'] ) && current_user_can( 'manage_woocommerce' ) ) {
			$current_funds = floatval( get_user_meta( $user_id, 'account_funds', true ) );
			$new_funds     = floatval( wc_clean( $_POST['account_funds'] ) );
			if ( update_user_meta( $user_id, 'account_funds', $new_funds ) ) {
				if ( $current_funds < $new_funds ) {
					// Send email to customer.
					$wc_emails = WC_Emails::instance();
					$email = $wc_emails->emails['WC_Account_Funds_Email_Account_Funds_Increase'];
					$email->trigger( $user_id, $current_funds, $new_funds );
				}
			}
		}
	}

	/**
	 * Returns settings array.
	 * @return array settings
	 */
	public function get_settings() {
		$settings = array(
			array(
				'name' => __( 'Discount Settings', 'woocommerce-account-funds' ),
				'type' => 'title',
				'desc' => '',
				'id'   => 'account_funds_title'
			),
			array(
				'name'     => __( 'Give Discount', 'woocommerce-account-funds' ),
				'type'     => 'checkbox',
				'desc'     => __( 'Apply a discount when account funds are used to purchase items', 'woocommerce-account-funds' ),
				'id'       => 'account_funds_give_discount'
			),
			array(
				'name'     => __( 'Discount Type', 'woocommerce-account-funds' ),
				'type'     => 'select',
				'options'  => array(
					'fixed'      => __( 'Fixed Price', 'woocommerce-account-funds' ),
					'percentage' => __( 'Percentage', 'woocommerce-account-funds' )
				),
				'desc'     => __( 'Percentage discounts will be based on the amount of funds used.', 'woocommerce-account-funds' ),
				'id'       => 'account_funds_discount_type',
				'desc_tip' => true
			),
			array(
				'name'    => __( 'Discount Amount', 'woocommerce-account-funds' ),
				'type'    => 'text',
				'desc'    => __( 'Enter numbers only. Do not include the percentage sign.', 'woocommerce-account-funds' ),
				'default' => '',
				'id'      => 'account_funds_discount_amount',
				'desc_tip' => true
			),
			array( 'type' => 'sectionend', 'id' => 'account_funds_title' ),
			array(
				'name' => __( 'Funding', 'woocommerce-account-funds' ),
				'type' => 'title',
				'desc' => '',
				'id'   => 'account_funds_funding_title'
			),
			array(
				'name'            => __( 'Enable "My Account" Top-up', 'woocommerce-account-funds' ),
				'type'            => 'checkbox',
				'desc'            => __( 'Allow customers to top up funds via their account page.', 'woocommerce-account-funds' ),
				'id'              => 'account_funds_enable_topup'
			),
			array(
				'name'            => __( 'Minimum Top-up', 'woocommerce-account-funds' ),
				'type'            => 'text',
				'desc'            => '',
				'default'         => '',
				'placeholder'     => 0,
				'id'              => 'account_funds_min_topup',
				'desc_tip'        => true
			),
			array(
				'name'            => __( 'Maximum Top-up', 'woocommerce-account-funds' ),
				'type'            => 'text',
				'desc'            => '',
				'default'         => '',
				'placeholder'     => __( 'n/a', 'woocommerce-account-funds' ),
				'id'              => 'account_funds_max_topup',
				'desc_tip'        => true
			),
			array( 'type' => 'sectionend', 'id' => 'account_funds_funding_title' ),
			array(
				'name' => __( 'Paying with Account Funds', 'woocommerce-account-funds' ),
				'type' => 'title',
				'desc' => '',
				'id'   => 'account_funds_payment_title'
			),
			array(
				'name'     => __( 'Partial Funds Payment', 'woocommerce-account-funds' ),
				'type'     => 'checkbox',
				'desc'     => __( 'Allow customers to apply available funds and pay the difference via another gateway.', 'woocommerce-account-funds' ),
				'desc_tip' => __( 'If disabled, users must pay for the entire order using the account funds payment gateway.', 'woocommerce-account-funds' ),
				'id'       => 'account_funds_partial_payment'
			),
			array( 'type' => 'sectionend', 'id' => 'account_funds_payment_title' ),
		);

		return apply_filters( 'woocommerce_account_funds_get_settings', $settings );
	}

	/**
	 * Add settings tab to woocommerce
	 */
	public function add_woocommerce_settings_tab( $settings_tabs ) {
		$settings_tabs[ $this->settings_tab_id ] = __( 'Account Funds', 'woocommerce-account-funds' );
		return $settings_tabs;
	}

	/**
	 * Do this when viewing our custom settings tab(s). One function for all tabs.
	 */
	public function woocommerce_settings_tab_action() {
		woocommerce_admin_fields( $this->get_settings() );
	}

	/**
	 * Save settings in a single field in the database for each tab's fields (one field per tab).
	 */
	public function woocommerce_settings_save() {
		woocommerce_update_options( $this->get_settings() );
	}
}

new WC_Account_Funds_Admin();
