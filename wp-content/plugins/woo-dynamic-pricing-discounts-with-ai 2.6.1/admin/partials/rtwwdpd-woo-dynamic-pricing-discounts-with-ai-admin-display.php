<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       www.redefiningtheweb.com
 * @since      1.0.0
 *
 * @package    Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai
 * @subpackage Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<?php
	$rtwwdpd_verification_done = get_site_option( 'rtwbma_verification_done', array() );

	if( !empty( $rtwwdpd_verification_done ) && $rtwwdpd_verification_done['status'] == true && !empty($rtwwdpd_verification_done['purchase_code']) )
	{	
		$rtwwdpd_discount_rules_active 		= '';
		$rtwwdpd_specific_customer_active 	= '';
		$rtwwdpd_settings_active 			= '';
		$rtwwdpd_coming_sale_active 		= '';
		$rtwwdpd_plus_member_active 		= '';

		if( isset( $_GET[ 'rtwwdpd_tab' ] ) )
		{
			if( $_GET[ 'rtwwdpd_tab' ] == "rtwwdpd_discount_rules" )
			{
				$rtwwdpd_discount_rules_active = "nav-tab-active";
			}
			elseif( $_GET[ 'rtwwdpd_tab' ] == "rtwwdpd_specific_customer" )
			{
				$rtwwdpd_specific_customer_active = "nav-tab-active";
			}
			elseif( $_GET[ 'rtwwdpd_tab' ] == "rtwwdpd_settings" ) 
			{
				$rtwwdpd_settings_active = "nav-tab-active";
			}
			elseif( $_GET[ 'rtwwdpd_tab' ] == "rtwwdpd_coming_sale" ) 
			{
				$rtwwdpd_coming_sale_active = "nav-tab-active";
			}
			elseif( $_GET[ 'rtwwdpd_tab' ] == "rtwwdpd_plus_member" ) 
			{
				$rtwwdpd_plus_member_active = "nav-tab-active";
			}
		}
		else
		{
			$rtwwdpd_discount_rules_active = "nav-tab-active";
		}
		settings_errors();
	?>
	<div class="wrap rtwwdpd">

		<h2 class="rtw-main-heading"><span><img src="<?php echo esc_url( RTWWDPD_URL.'admin/images/Dynamic-Pricing-Discount-logo.png' ); ?>" alt=""><?php esc_html_e( 'WooCommerce Dynamic Pricing & Discounts with AI', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></span><a href="<?php echo esc_url( admin_url( 'admin.php?page=rtwwdpd&rtwwdpd_tab=rtwwdpd_discount_rules&rtwwdpd_action=delete_purchase_code' ) );?>" class="rtwwdpd-button rtw-button"><?php esc_html_e( 'Remove Purchase Code', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></a></h2>
		<nav class="nav-tab-wrapper">
			<a class="nav-tab <?php echo esc_attr( $rtwwdpd_discount_rules_active ); ?>" href="<?php echo esc_url( get_admin_url() . 'admin.php?page=rtwwdpd&rtwwdpd_tab=rtwwdpd_discount_rules' );?>">
				<span><img src="<?php echo esc_url( RTWWDPD_URL.'admin/images/Discount-Rules.png' ); ?>" alt=""></span>
				<?php esc_html_e( 'Discount Rules', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
			</a>

			<a class="nav-tab <?php echo esc_attr($rtwwdpd_specific_customer_active); ?>" href="<?php echo esc_url( get_admin_url() . 'admin.php?page=rtwwdpd&rtwwdpd_tab=rtwwdpd_specific_customer' );?>">
				<span><img src="<?php echo esc_url( RTWWDPD_URL.'admin/images/Customer-Rules.png' ); ?>" alt=""></span>
				<?php esc_html_e( 'Rules for Specific Customer', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
			</a>

			<a class="nav-tab <?php echo esc_attr($rtwwdpd_settings_active); ?>" href="<?php echo esc_url( get_admin_url() . 'admin.php?page=rtwwdpd&rtwwdpd_tab=rtwwdpd_settings' );?>">
				<span><img src="<?php echo esc_url(RTWWDPD_URL.'admin/images/Setting.png'); ?>" alt=""></span>
				<?php esc_html_e( 'Settings', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
			</a>

			<a class="nav-tab <?php echo esc_attr($rtwwdpd_coming_sale_active); ?>" href="<?php echo esc_url( get_admin_url() . 'admin.php?page=rtwwdpd&rtwwdpd_tab=rtwwdpd_coming_sale' );?>">
				<span><img src="<?php echo esc_url(RTWWDPD_URL.'admin/images/Coming-Sale.png'); ?>" alt=""></span>
				<?php esc_html_e( 'Coming Sale', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
			</a>

			<a class="nav-tab <?php echo esc_attr($rtwwdpd_plus_member_active); ?>" href="<?php echo esc_url( get_admin_url() . 'admin.php?page=rtwwdpd&rtwwdpd_tab=rtwwdpd_plus_member' );?>">
				<span><img src="<?php echo esc_url(RTWWDPD_URL.'admin/images/Plus-Members.png'); ?>" alt=""></span>
				<?php esc_html_e( 'Plus Members', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
			</a>

		</nav>
		<div class="main-wrapper">
		<?php
			if( isset( $_GET[ 'rtwwdpd_tab' ] ) )
			{
				if( $_GET[ 'rtwwdpd_tab' ] == "rtwwdpd_discount_rules" ){
					include_once( RTWWDPD_DIR . 'admin/partials/rtwwdpd_tabs/rtwwdpd_discount_rules.php' );
				}
				elseif( $_GET[ 'rtwwdpd_tab' ] == "rtwwdpd_specific_customer" ){
					include_once( RTWWDPD_DIR . 'admin/partials/rtwwdpd_tabs/rtwwdpd_specific_customer.php' );
				}
				elseif( $_GET[ 'rtwwdpd_tab' ] == "rtwwdpd_settings" ){
					include_once( RTWWDPD_DIR . 'admin/partials/rtwwdpd_tabs/rtwwdpd_settings.php' );
				}
				elseif( $_GET[ 'rtwwdpd_tab' ] == "rtwwdpd_coming_sale" ){
					include_once( RTWWDPD_DIR . 'admin/partials/rtwwdpd_tabs/rtwwdpd_coming_sale.php' );
				}
				elseif( $_GET[ 'rtwwdpd_tab' ] == "rtwwdpd_plus_member" ){
					include_once( RTWWDPD_DIR . 'admin/partials/rtwwdpd_tabs/rtwwdpd_plus_member.php' );
				}
			}
			else{
				include_once( RTWWDPD_DIR . 'admin/partials/rtwwdpd_tabs/rtwwdpd_discount_rules.php' );
			}
		?>
		</div>
	</div>
	<?php
	}
	else{
		include_once( RTWWDPD_DIR . 'admin/partials/rtwwdpd_without_verify.php' );
	}