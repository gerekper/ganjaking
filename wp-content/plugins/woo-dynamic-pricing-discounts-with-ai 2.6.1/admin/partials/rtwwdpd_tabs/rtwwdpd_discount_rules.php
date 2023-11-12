<div class="clear"></div>
<div class="rtwwdpd_rules">
	<aside id="rtwwdpd_subtab_margin">
		<ul class="subsubsub">
			<?php
			$rtwwdpd_verification_done = get_site_option( 'rtwbma_verification_done', array() );
			$rtwwdpd_product_rules_active 		= '';
			$rtwwdpd_category_rules_active 		= '';
			$rtwwdpd_cart_rules_active 			= '';
			$rtwwdpd_bogo_rules_active 			= '';
			$rtwwdpd_variation_rules_active 	= '';
			$rtwwdpd_tiered_rules_active 		= '';
			$rtwwdpd_payment_method_active		= '';
			$rtwwdpd_shipping_method_active		= '';
			$rtwwdpd_attribute_active			= '';
			$rtwwdpd_prod_tag_active			= '';
			$rtwwdpd_next_buy_bonus				= '';
			$rtwwdpd_nth_order_active 			= '';
			$rtwwdpd_bogo_least 				= '';
			if( !empty( $rtwwdpd_verification_done ) && $rtwwdpd_verification_done['status'] == true && !empty($rtwwdpd_verification_done['purchase_code']) )
			{
				if( isset( $_GET[ 'rtwwdpd_sub' ] ) )
				{
					if( $_GET[ 'rtwwdpd_sub' ] == "rtwwdpd_prod_rules" )
					{
						$rtwwdpd_product_rules_active = "current";
					}
					elseif( $_GET[ 'rtwwdpd_sub' ] == "rtwwdpd_cat_rules" )
					{
						$rtwwdpd_category_rules_active = "current";
					}
					elseif( $_GET[ 'rtwwdpd_sub' ] == "rtwwdpd_cart_rules" )
					{
						$rtwwdpd_cart_rules_active = "current";
					}
					elseif( $_GET[ 'rtwwdpd_sub' ] == "rtwwdpd_bogo_rules" )
					{
						$rtwwdpd_bogo_rules_active = "current";
					}
					elseif( $_GET[ 'rtwwdpd_sub' ] == "rtwwdpd_variation_rules" )
					{
						$rtwwdpd_variation_rules_active = "current";
					}
					elseif( $_GET[ 'rtwwdpd_sub' ] == "rtwwdpd_tiered_rules" )
					{
						$rtwwdpd_tiered_rules_active = "current";
					}
					elseif( $_GET[ 'rtwwdpd_sub' ] == "rtwwdpd_payment_method" )
					{
						$rtwwdpd_payment_method_active = "current";
					}
					elseif( $_GET[ 'rtwwdpd_sub' ] == "rtwwdpd_shipping_method" )
					{
						$rtwwdpd_shipping_method_active = "current";
					}
					elseif( $_GET[ 'rtwwdpd_sub' ] == "rtwwdpd_attribute" )
					{
						$rtwwdpd_attribute_active = "current";
					}
					elseif( $_GET[ 'rtwwdpd_sub' ] == "rtwwdpd_prod_tags" )
					{
						$rtwwdpd_prod_tag_active = "current";
					}
					elseif( $_GET[ 'rtwwdpd_sub' ] == "rtwwdpd_next_buy_bonus" )
					{
						$rtwwdpd_next_buy_bonus = "current";
					}
					elseif( $_GET[ 'rtwwdpd_sub' ] == "rtwwdpd_nth_order_active" )
					{
						$rtwwdpd_nth_order_active = "current";
					}
					elseif( $_GET[ 'rtwwdpd_sub' ] == "rtwwdpd_bogo_least_active" )
					{
						$rtwwdpd_bogo_least = "current";
					}
				}
				else{
					$rtwwdpd_product_rules_active = "current";
				}
			}

			echo 	"<li>";
			echo 		'<a href="'.esc_url( admin_url().'admin.php?page=rtwwdpd&rtwwdpd_tab=rtwwdpd_discount_rules&rtwwdpd_sub=rtwwdpd_prod_rules').'" class="'.esc_attr( $rtwwdpd_product_rules_active ).'" ><span class="rtw_rules">' . esc_html__( 'Product ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) . '</span>'. esc_html__( 'Rules', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) .'</a>';
			echo 	"</li>";

			echo 	"<li>";
			echo 		'<a href="'.esc_url( admin_url().'admin.php?page=rtwwdpd&rtwwdpd_tab=rtwwdpd_discount_rules&rtwwdpd_sub=rtwwdpd_cat_rules').'" class="'.esc_attr( $rtwwdpd_category_rules_active ).'" ><span class="rtw_rules">' . esc_html__( 'Category ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) . '</span>'. esc_html__( 'Rules', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) .'</a>';
			echo 	"</li>";

			echo 	"<li>";
			echo 		'<a href="'.esc_url( admin_url().'admin.php?page=rtwwdpd&rtwwdpd_tab=rtwwdpd_discount_rules&rtwwdpd_sub=rtwwdpd_cart_rules').'" class="'.esc_attr( $rtwwdpd_cart_rules_active ).'" ><span class="rtw_rules">' . esc_html__( 'Cart ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) . '</span>'. esc_html__( 'Rules', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) .'</a>';
			echo 	"</li>";

			echo 	"<li>";
			echo 		'<a href="'.esc_url( admin_url().'admin.php?page=rtwwdpd&rtwwdpd_tab=rtwwdpd_discount_rules&rtwwdpd_sub=rtwwdpd_bogo_rules').'" class="'.esc_attr( $rtwwdpd_bogo_rules_active ).'" ><span class="rtw_rules">' . esc_html__( 'BOGO ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) . '</span>'. esc_html__( 'Rules', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) .'</a>';
			echo 	"</li>";

			echo 	"<li>";
			echo 		'<a href="'.esc_url( admin_url().'admin.php?page=rtwwdpd&rtwwdpd_tab=rtwwdpd_discount_rules&rtwwdpd_sub=rtwwdpd_variation_rules').'" class="'.esc_attr( $rtwwdpd_variation_rules_active ).'" ><span class="rtw_rules">' . esc_html__( 'Variation\'s ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) . '</span>'. esc_html__( 'Rules', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) .'</a>';
			echo 	"</li>";

			echo 	"<li>";
			echo 		'<a href="'.esc_url( admin_url().'admin.php?page=rtwwdpd&rtwwdpd_tab=rtwwdpd_discount_rules&rtwwdpd_sub=rtwwdpd_tiered_rules').'" class="'.esc_attr( $rtwwdpd_tiered_rules_active ).'" ><span class="rtw_rules">' . esc_html__( 'Tiered ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) . '</span>'. esc_html__( 'Rules', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) .'</a>';
			echo 	"</li>";

			echo 	"<li>";
			echo 		'<a href="'.esc_url( admin_url().'admin.php?page=rtwwdpd&rtwwdpd_tab=rtwwdpd_discount_rules&rtwwdpd_sub=rtwwdpd_payment_method').'" class="'.esc_attr( $rtwwdpd_payment_method_active ).'" ><span class="rtw_rules">' . esc_html__( 'Payment ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) . '</span>'. esc_html__( 'Rules', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) .'</a>';
			echo 	"</li>";

			echo 	"<li>";
			echo 		'<a href="'.esc_url( admin_url().'admin.php?page=rtwwdpd&rtwwdpd_tab=rtwwdpd_discount_rules&rtwwdpd_sub=rtwwdpd_attribute').'" class="'.esc_attr( $rtwwdpd_attribute_active ).'" ><span class="rtw_rules">' . esc_html__( 'Attribute ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) . '</span>'. esc_html__( 'Rules', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) .'</a>';
			echo 	"</li>";

			echo 	"<li>";
			echo 		'<a href="'.esc_url( admin_url().'admin.php?page=rtwwdpd&rtwwdpd_tab=rtwwdpd_discount_rules&rtwwdpd_sub=rtwwdpd_prod_tags').'" class="'.esc_attr( $rtwwdpd_prod_tag_active ).'" ><span class="rtw_rules">' . esc_html__( 'Product Tag ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) . '</span>'. esc_html__( 'Rules', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) .'</a>';
			echo 	"</li>";

			echo 	"<li>";
			echo 		'<a href="'.esc_url( admin_url().'admin.php?page=rtwwdpd&rtwwdpd_tab=rtwwdpd_discount_rules&rtwwdpd_sub=rtwwdpd_shipping_method').'" class="'.esc_attr( $rtwwdpd_shipping_method_active ).'" ><span class="rtw_rules">' . esc_html__( 'Shipping ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) . '</span>'. esc_html__( 'Rules', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) .'</a>';
			echo 	"</li>";

			echo 	"<li>";
			echo 		'<a href="'.esc_url( admin_url().'admin.php?page=rtwwdpd&rtwwdpd_tab=rtwwdpd_discount_rules&rtwwdpd_sub=rtwwdpd_next_buy_bonus').'" class="'.esc_attr( $rtwwdpd_next_buy_bonus ).'" ><span class="rtw_rules">' . esc_html__( 'Next Buy ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) . '</span>'. esc_html__( 'Bonus', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) .'</a>';
			echo 	"</li>";

			echo 	"<li>";
			echo 		'<a href="'.esc_url( admin_url().'admin.php?page=rtwwdpd&rtwwdpd_tab=rtwwdpd_discount_rules&rtwwdpd_sub=rtwwdpd_nth_order_active').'" class="'.esc_attr( $rtwwdpd_nth_order_active ).'" ><span class="rtw_rules">' . esc_html__( 'Nth Order ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) . '</span>'. esc_html__( 'Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) .'</a>';
			echo 	"</li>";

			echo 	"<li>";
			echo 		'<a href="'.esc_url( admin_url().'admin.php?page=rtwwdpd&rtwwdpd_tab=rtwwdpd_discount_rules&rtwwdpd_sub=rtwwdpd_bogo_least_active').'" class="'.esc_attr( $rtwwdpd_bogo_least ).'" ><span class="rtw_rules">' . esc_html__( 'Least Amount ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) . '</span>'. esc_html__( 'Product Free', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) .'</a>';
			echo 	"</li>";
			?>
		</ul>
	</aside>

	<?php
	$rtwwdpd_verification_done = get_site_option( 'rtwbma_verification_done', array() );
	if( !empty( $rtwwdpd_verification_done ) && $rtwwdpd_verification_done['status'] == true && !empty($rtwwdpd_verification_done['purchase_code']) )
	{
		if( isset( $_GET[ 'rtwwdpd_sub' ] ) )
		{
			if( $_GET[ 'rtwwdpd_sub' ] == "rtwwdpd_prod_rules" )
			{
				include_once( RTWWDPD_DIR . 'admin/partials/rtwwdpd_subtabs/rtwwdpd_prod_rule.php' );
			}
			elseif( $_GET[ 'rtwwdpd_sub' ] == "rtwwdpd_cat_rules" )
			{
				include_once( RTWWDPD_DIR . 'admin/partials/rtwwdpd_subtabs/rtwwdpd_cate_rule.php' );
			}
			elseif( $_GET[ 'rtwwdpd_sub' ] == "rtwwdpd_cart_rules" )
			{
				include_once( RTWWDPD_DIR . 'admin/partials/rtwwdpd_subtabs/rtwwdpd_cart_rule.php' );
			}
			elseif( $_GET[ 'rtwwdpd_sub' ] == "rtwwdpd_bogo_rules" )
			{
				include_once( RTWWDPD_DIR . 'admin/partials/rtwwdpd_subtabs/rtwwdpd_bogo_rule.php' );
			}
			elseif( $_GET[ 'rtwwdpd_sub' ] == "rtwwdpd_variation_rules" )
			{
				include_once( RTWWDPD_DIR . 'admin/partials/rtwwdpd_subtabs/rtwwdpd_variation_rule.php' );
			}
			elseif( $_GET[ 'rtwwdpd_sub' ] == "rtwwdpd_tiered_rules" )
			{
				include_once( RTWWDPD_DIR . 'admin/partials/rtwwdpd_subtabs/rtwwdpd_tiered_rule.php' );
			}
			elseif( $_GET[ 'rtwwdpd_sub' ] == "rtwwdpd_payment_method" )
			{
				include_once( RTWWDPD_DIR . 'admin/partials/rtwwdpd_subtabs/rtwwdpd_payment_method.php' );
			}
			elseif( $_GET[ 'rtwwdpd_sub' ] == "rtwwdpd_shipping_method" )
			{
				include_once( RTWWDPD_DIR . 'admin/partials/rtwwdpd_subtabs/rtwwdpd_shipping_method.php' );
			}
			elseif( $_GET[ 'rtwwdpd_sub' ] == "rtwwdpd_attribute" )
			{
				include_once( RTWWDPD_DIR . 'admin/partials/rtwwdpd_subtabs/rtwwdpd_attribute.php' );
			}
			elseif( $_GET[ 'rtwwdpd_sub' ] == "rtwwdpd_prod_tags" )
			{
				include_once( RTWWDPD_DIR . 'admin/partials/rtwwdpd_subtabs/rtwwdpd_prod_tag.php' );
			}
			elseif( $_GET[ 'rtwwdpd_sub' ] == "rtwwdpd_next_buy_bonus" )
			{
				include_once( RTWWDPD_DIR . 'admin/partials/rtwwdpd_subtabs/rtwwdpd_next_buy_bonus.php' );
			}
			elseif( $_GET[ 'rtwwdpd_sub' ] == "rtwwdpd_nth_order_active" )
			{
				include_once( RTWWDPD_DIR . 'admin/partials/rtwwdpd_subtabs/rtwwdpd_nth_order.php' );
			}
			elseif( $_GET[ 'rtwwdpd_sub' ] == "rtwwdpd_bogo_least_active" )
			{
				include_once( RTWWDPD_DIR . 'admin/partials/rtwwdpd_subtabs/rtwwdpd_least_amt_pro.php' );
			}
		}
		else{
			include_once( RTWWDPD_DIR . 'admin/partials/rtwwdpd_subtabs/rtwwdpd_prod_rule.php' );
		}
	}
	?>
</div>