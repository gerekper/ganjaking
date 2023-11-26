<?php 
$rtwwdpd_prod_rule = get_option( 'rtwwdpd_single_prod_rule' );
$rtwwdpd_category_rule = get_option( 'rtwwdpd_single_cat_rule' );
$rtwwdpd_bogo_rule = get_option( 'rtwwdpd_bogo_rule' );
$rtwwdpd_tier_rule = get_option( 'rtwwdpd_tiered_rule' );
$rtwwdpd_coming_sale = get_option( 'rtwwdpd_coming_sale' );

$rtwwdpd_today_date = current_time('Y-m-d');
$rtwwdpd_symbol = get_woocommerce_currency_symbol();
if( !isset( $rtwwdpd_coming_sale ) )
{
	$rtwwdpd_coming_sale = array();
}
if(!isset($rtwwdpd_prod_rule))
{
	$rtwwdpd_prod_rule = array();
}
if(!isset($rtwwdpd_category_rule))
{
	$rtwwdpd_category_rule = array();
}
if(!isset($rtwwdpd_bogo_rule))
{
	$rtwwdpd_bogo_rule = array();
}
if(!isset($rtwwdpd_tier_rule))
{	
	$rtwwdpd_tier_rule = array();
}
$rtwwdpd_max_disxount = 0;
$rtwwdpd_max_dis_onsale = 0;
if( is_array( $rtwwdpd_coming_sale ) && !empty( $rtwwdpd_coming_sale ) )
{
	foreach ($rtwwdpd_coming_sale as $key => $value) {
		
		if($value['rtwwdpd_sale_discount_type'] = 'rtwwdpd_discount_percentage' )
		{
			if( $value['rtwwdpd_sale_discount_value'] > $rtwwdpd_max_dis_onsale )
			{
				$rtwwdpd_max_dis_onsale = $value['rtwwdpd_sale_discount_value'];
			}
		}
	}
}

$product_rule_empty = array();
if(is_array($rtwwdpd_prod_rule) && !empty($rtwwdpd_prod_rule))
{
	foreach ($rtwwdpd_prod_rule as $key => $value) {
		if($value['rtwwdpd_single_from_date'] <= $rtwwdpd_today_date && $value['rtwwdpd_single_to_date'] >= $rtwwdpd_today_date && $value['rtwwdpd_rule_on'] != 'rtwwdpd_cart')
		{
			if( $value['rtwwdpd_discount_type'] == 'rtwwdpd_discount_percentage' )
			{
				if($value['rtwwdpd_discount_value'] > $rtwwdpd_max_disxount)
				{
					$rtwwdpd_max_disxount = $value['rtwwdpd_discount_value'];
				}
			}
			$product_rule_empty[] = $value;
		}
	}
}

$rtwwdpd_max_tier_dis = 0;

if(is_array($rtwwdpd_tier_rule) && !empty($rtwwdpd_tier_rule))
{	
	foreach ($rtwwdpd_tier_rule as $key => $value) {
		if($value['rtwwdpd_from_date'] <= $rtwwdpd_today_date && $value['rtwwdpd_to_date'] >= $rtwwdpd_today_date)
		{
			if( $value['rtwwdpd_discount_type'] == 'rtwwdpd_discount_percentage' )
			{
				foreach ($value['discount_val'] as $val) {
					
					if($val > $rtwwdpd_max_tier_dis)
					{
						$rtwwdpd_max_tier_dis = $val;
					}
				}
			}
		}
	}
}
$rtwwdpd_user = wp_get_current_user();
?>
<div class="">
	
	<?php
	$variable_poduct_array = array();
	if( is_array( $rtwwdpd_prod_rule ) && !empty( $rtwwdpd_prod_rule ) && !empty($product_rule_empty)){
	?>
	<div class="rtwwdpd-carousel-main-container">
		<div class="rtwwdpd-carousel-header-bg">
			<div class="rtwwdpd-carousel-header-image">
				<img src="<?php echo esc_url(RTWWDPD_URL); ?>assets/images/special-offer.png" alt="<?php echo esc_attr_e('Special-Offer','rtwwdpd-woo-dynamic-pricing-discounts-with-ai') ;?>">
			</div>
			<div class="rtwwdpd-carousel-header-content">
				<h2 class="rtwwdpd-carousel-header-title"><?php esc_html_e( 'Special Offer', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></h2>
				<h3><?php esc_html_e( 'Up to ' . $rtwwdpd_max_disxount . '% OFF', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></h3>
			</div>
		</div>
			<h2 class="rtwwdpd_simple_product"><?php esc_html_e( 'On Simple Products', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></h2>
		<div class="rtwwdpd-carousel-slider owl-theme owl-carousel">
			<?php
			foreach ($rtwwdpd_prod_rule as $key => $value) { 
				$days = array(1,2,3,4,5,6,7);
				$active_days = get_option('rtwwdpd_saved_product_days', $days);
				$current_day = date('N');
				
				if(!in_array($current_day, $active_days))
				{
					continue;
				}
				if(isset($value['product_id']))
				{
					$rtwwdpd_product = wc_get_product( $value['product_id'] );
					
					if( $rtwwdpd_product->is_type( 'variable' ))
					{
						$variable_poduct_array[] = $rtwwdpd_product;
						continue 1;
					}
					if($value['rtwwdpd_single_from_date'] > $rtwwdpd_today_date || $value['rtwwdpd_single_to_date'] < $rtwwdpd_today_date)
					{
						continue 1;
					}
					$rtwwdpd_user_role = $value['rtwwdpd_select_roles'] ;
					$role_matched = false;
					foreach ($rtwwdpd_user_role as $rol => $role) {
						if($role == 'all'){
							$role_matched = true;
						}
						if (in_array( $role, (array) $rtwwdpd_user->roles ) ) {
							$role_matched = true;
						}
					}
					if($role_matched == false)
					{
						continue 1;
					}
					$rtwwdpd_image = wp_get_attachment_image_src( get_post_thumbnail_id( $value['product_id'] ), 'single-post-thumbnail' ); 
					$rtwwdpd_prod_price = $rtwwdpd_product->get_price();
					$rtwwdpd_discounted = 0;
				
					?>
					<div class="rtwwdpd-carousel-slider-item">
						<div class="rtwwdpd-carousel-slider-image">
							<a href="<?php echo get_permalink($value['product_id']); ?>"><img src="<?php echo esc_url($rtwwdpd_image[0]); ?>" alt="<?php echo esc_attr_e('Special-Offer','rtwwdpd-woo-dynamic-pricing-discounts-with-ai') ;?>">
							</a>
						</div>
						<div class="rtwwdpd-carousel-slider-content">
							<span class="rtwwdpd-carousel-offer"><?php esc_html_e( 'Offer', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
							<?php 
							if($value['rtwwdpd_discount_type'] == 'rtwwdpd_discount_percentage'){
								echo '<h2>'. esc_html__( 'Flat '. $value['rtwwdpd_discount_value'] .'% OFF', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai') . '</h2>';

								$rtwwdpd_discounted = $rtwwdpd_prod_price - ( $rtwwdpd_prod_price * ( $value['rtwwdpd_discount_value'] / 100 ));

							}
							else{
								echo '<h2>'. esc_html__( 'Flat ' . $rtwwdpd_symbol . $value['rtwwdpd_discount_value'] .' OFF', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai') . '</h2>';

								$rtwwdpd_discounted = $rtwwdpd_prod_price - ( $rtwwdpd_prod_price * ( $value['rtwwdpd_discount_value'] / 100 ));
								if( $value['rtwwdpd_max_discount'] < $rtwwdpd_discounted )
								{
									$rtwwdpd_discounted = $value['rtwwdpd_max_discount'];
								}
							}
							?>
							<h3><?php echo esc_html( get_the_title( $value['product_id'] ) );
							?></h3>
							<p><?php echo date( get_option('date_format'), strtotime( $value['rtwwdpd_single_from_date'] ) ) .' ~ '. date( get_option('date_format'), strtotime( $value['rtwwdpd_single_to_date'] ) );
								?></p>
							<p class="rtwwdpd-carosuel-price"><ins>
								<?php echo esc_html( $rtwwdpd_symbol . $rtwwdpd_discounted ); ?>
							</ins> 
							<del><?php echo esc_html( $rtwwdpd_symbol . $rtwwdpd_prod_price ); ?></del></p>
							<p><a href="<?php echo get_permalink($value['product_id']); ?>" class="rtwwdpd-carousel-buy-btn"><?php esc_html_e( 'Buy Now', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></a></p>
						</div>
					</div>
			<?php } } ?>
			
		</div>
			<h2 class="rtwwdpd_simple_product"><?php esc_html_e( 'On Variable Products', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></h2>
		<div class="rtwwdpd-carousel-slider owl-theme owl-carousel">
			<?php
			$active_dayss = get_site_option('rtwwdpd_discount_on_selected_days', 'no');
			foreach ($rtwwdpd_prod_rule as $key => $value) { 
				
						
				if($active_dayss == 'yes')
				{
					$active_days = isset($value['rtwwwdpd_pro_day']) ? $value['rtwwwdpd_pro_day'] : array();
					$current_day = date('N');

					if(!in_array($current_day, $active_days))
					{
						continue;
					}
				}

				if(isset($value['product_id']))
				{
					$rtwwdpd_product = wc_get_product( $value['product_id'] );
					if( !$rtwwdpd_product->is_type( 'variable' ))
					{
						$variable_poduct_array[] = $rtwwdpd_product;
						continue 1;
					}
					if($value['rtwwdpd_single_from_date'] > $rtwwdpd_today_date || $value['rtwwdpd_single_to_date'] < $rtwwdpd_today_date)
					{
						continue 1;
					}
					$rtwwdpd_user_role = $value['rtwwdpd_select_roles'] ;
					$role_matched = false;
					foreach ($rtwwdpd_user_role as $rol => $role) {
						if($role == 'all'){
							$role_matched = true;
						}
						if (in_array( $role, (array) $rtwwdpd_user->roles ) ) {
							$role_matched = true;
						}
					}
					if($role_matched == false)
					{
						continue 1;
					}
					$rtwwdpd_image = wp_get_attachment_image_src( get_post_thumbnail_id( $value['product_id'] ), 'single-post-thumbnail' ); 
					$rtwwdpd_prod_price = $rtwwdpd_product->get_price();
					$rtwwdpd_discounted = 0;
				
					?>
					<div class="rtwwdpd-carousel-slider-item">
						<div class="rtwwdpd-carousel-slider-image">
							<a href="<?php echo get_permalink($value['product_id']); ?>"><img src="<?php echo esc_url($rtwwdpd_image[0]); ?>" alt="<?php echo esc_attr_e('Special-Offer','rtwwdpd-woo-dynamic-pricing-discounts-with-ai') ;?>">
							</a>
						</div>
						<div class="rtwwdpd-carousel-slider-content">
							<span class="rtwwdpd-carousel-offer"><?php esc_html_e( 'Offer', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
							<?php 
							if($value['rtwwdpd_discount_type'] == 'rtwwdpd_discount_percentage'){
								echo '<h2>'. esc_html__( 'Flat '. $value['rtwwdpd_discount_value'] .'% OFF', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai') . '</h2>';

								$rtwwdpd_discounted = $rtwwdpd_prod_price - ( $rtwwdpd_prod_price * ( $value['rtwwdpd_discount_value'] / 100 ));

							}
							else{
								echo '<h2>'. esc_html__( 'Flat ' . $rtwwdpd_symbol . $value['rtwwdpd_discount_value'] .' OFF', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai') . '</h2>';

								$rtwwdpd_discounted = $rtwwdpd_prod_price - ( $rtwwdpd_prod_price * ( $value['rtwwdpd_discount_value'] / 100 ));
								if( $value['rtwwdpd_max_discount'] < $rtwwdpd_discounted )
								{
									$rtwwdpd_discounted = $value['rtwwdpd_max_discount'];
								}
							}
							?>
							<h3><?php echo esc_html( get_the_title( $value['product_id'] ) );
							?></h3>
							<p><?php echo date( get_option('date_format'), strtotime( $value['rtwwdpd_single_from_date'] ) ) .' ~ '. date( get_option('date_format'), strtotime( $value['rtwwdpd_single_to_date'] ) );
								?></p>
							<p class="rtwwdpd-carosuel-price"><ins>
								<?php echo esc_html( $rtwwdpd_symbol . $rtwwdpd_discounted ); ?>
							</ins> 
							<del><?php echo esc_html( $rtwwdpd_symbol . $rtwwdpd_prod_price ); ?></del></p>
							<p><a href="<?php echo get_permalink($value['product_id']); ?>" class="rtwwdpd-carousel-buy-btn"><?php esc_html_e( 'Buy Now', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></a></p>
						</div>
					</div>
			<?php } } ?>
			
		</div>
	</div>
	<?php } 
	if( is_array( $rtwwdpd_bogo_rule ) && !empty( $rtwwdpd_bogo_rule ) ){
	?>
	<div class="rtwwdpd-carousel-main-container">
		<div class="rtwwdpd-carousel-header-bg">
			<div class="rtwwdpd-carousel-header-image">
				<img src="<?php echo esc_url(RTWWDPD_URL); ?>assets/images/buy-one-get-one-free-offer.png" alt="<?php echo esc_attr_e('Buy-One-Get-One','rtwwdpd-woo-dynamic-pricing-discounts-with-ai') ;?>">
			</div>
			<div class="rtwwdpd-carousel-header-content">
				<h2 class="rtwwdpd-carousel-header-title"><?php esc_html_e('Buy One Get One Free', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');?></h2>
			</div>
		</div>
		<div class="rtwwdpd-carousel-slider owl-theme owl-carousel">
			<?php 
			$active_dayss = get_site_option('rtwwdpd_discount_on_selected_days', 'no');
			foreach ($rtwwdpd_bogo_rule as $key => $value) { 
						
				if($active_dayss == 'yes')
				{
					$active_days = isset($value['rtwwwdpd_bogo_day']) ? $value['rtwwwdpd_bogo_day'] : array();
					$current_day = date('N');

					if(!in_array($current_day, $active_days))
					{
						continue;
					}
				}
				$days = array(1,2,3,4,5,6,7);
				$active_days = get_option('rtwwdpd_saved_bogo_days', $days);
				$current_day = date('N');
				
				if(!in_array($current_day, $active_days))
				{
					continue;
				}
				if(isset($value['product_id']))
				{
					$rtwwdpd_user_role = $value['rtwwdpd_select_roles'] ;
					$role_matched = false;
					foreach ($rtwwdpd_user_role as $rol => $role) {
						if($role == 'all'){
							$role_matched = true;
						}
						if (in_array( $role, (array) $rtwwdpd_user->roles ) ) {
							$role_matched = true;
						}
					}
					if($role_matched == false)
					{
						continue 1;
					}
					$rtwwdpd_image = wp_get_attachment_image_src( get_post_thumbnail_id( $value['product_id'][0] ), 'single-post-thumbnail' ); 
					$rtwwdpd_free_image = wp_get_attachment_image_src( get_post_thumbnail_id( $value['rtwbogo'][0] ), 'single-post-thumbnail' ); 
					$rtwwdpd_product = wc_get_product( $value['product_id'][0] );
					$rtwwdpd_discounted = 0;
					?>
			<a href="<?php echo get_permalink( $value['product_id'][0] ); ?>">
				<div class="rtwwdpd-carousel-slider-item">
					<div class="rtwwdpd-carousel-slider-image">
						<img src="<?php echo esc_url($rtwwdpd_image[0]); ?>" alt="<?php echo esc_attr_e('Buy-One-Get-One','rtwwdpd-woo-dynamic-pricing-discounts-with-ai') ;?>">
					</div>
					<div class="rtwwdpd-carousel-slider-content">
						<h3><?php esc_html_e('Buy ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); echo esc_html(get_the_title( $value['product_id'][0] )); ?></h3>
					</div>
					<div class="rtwwdpd-carousel-slider-image">
						<img src="<?php echo esc_url($rtwwdpd_free_image[0]); ?>" alt="<?php echo esc_attr_e('Buy-One-Get-One','rtwwdpd-woo-dynamic-pricing-discounts-with-ai') ;?>">
					</div>
					<div class="rtwwdpd-carousel-slider-content">
						<h3><?php esc_html_e('Get ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); echo esc_html(get_the_title( $value['rtwbogo'][0] )); esc_html_e(' Free', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');?></h3>
					</div>
				</div>
			</a>
			<?php } } ?>
		</div>
	</div>
	<?php }
	if( is_array( $rtwwdpd_tier_rule ) && !empty( $rtwwdpd_tier_rule ) ){
		
	?>
	<div class="rtwwdpd-carousel-main-container">
		<div class="rtwwdpd-carousel-header-bg">
			<div class="rtwwdpd-carousel-header-image">
				<img src="<?php echo esc_url(RTWWDPD_URL); ?>assets/images/trending-offer.png" alt="<?php echo esc_attr_e('Trending-Tier-Offer','rtwwdpd-woo-dynamic-pricing-discounts-with-ai') ;?>">
			</div>
			<div class="rtwwdpd-carousel-header-content">
				<h2 class="rtwwdpd-carousel-header-title"><?php esc_html_e('Trending Tier Offer', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></h2>
				<h3>
				<?php 
				if( $rtwwdpd_max_tier_dis != 0 ){
				esc_html_e( 'Up to ' . $rtwwdpd_max_tier_dis . '% OFF', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); 
				}?>
				</h3>
			</div>
		</div>
		<div class="rtwwdpd-carousel-slider owl-theme owl-carousel">
			<?php
			foreach ($rtwwdpd_tier_rule as $key => $value) { 
			if(is_array($value['products']) && !empty($value['products']))
			{
				foreach ( $value['products'] as $pross => $pros ) {
					
					$rtwwdpd_max_tier = 0;
					foreach ($value['discount_val'] as $val) {
						if($rtwwdpd_max_tier < $val)
						{
							$rtwwdpd_max_tier = $val;
						}
					}
					$rtwwdpd_user_role = $value['rtwwdpd_select_roles'] ;
					$role_matched = false;
					foreach ($rtwwdpd_user_role as $rol => $role) {
						if($role == 'all'){
							$role_matched = true;
						}
						if (in_array( $role, (array) $rtwwdpd_user->roles ) ) {
							$role_matched = true;
						}
					}
					if($role_matched == false)
					{
						continue 1;
					}
					$rtwwdpd_image = wp_get_attachment_image_src( get_post_thumbnail_id( $pros ), 'single-post-thumbnail' ); 
					?>
					<div class="rtwwdpd-carousel-slider-item">
						<div class="rtwwdpd-carousel-slider-image">
							<a href="<?php echo esc_url( get_permalink( $pros )); ?>"><img src="<?php echo esc_url( $rtwwdpd_image[0] ); ?>" alt="<?php echo esc_attr_e('Trending-Tier-Offer','rtwwdpd-woo-dynamic-pricing-discounts-with-ai') ;?>"></a>
						</div>
						<div class="rtwwdpd-carousel-slider-content">
							<h2>
							<?php if( $value['rtwwdpd_discount_type'] == 'rtwwdpd_discount_percentage' )
							{ 
								esc_html_e( 'Up to ' . $rtwwdpd_max_tier . '% OFF', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); 
							}else{ 
								esc_html_e( 'Up to ' . $rtwwdpd_max_tier . ' OFF', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' );
							} ?>
							</h2>
							<h3><?php echo esc_html( get_the_title( $pros ) );
									?></h3>
								<?php 
								foreach ($value['discount_val'] as $k => $va) {
									if( $value['rtwwdpd_discount_type'] == 'rtwwdpd_discount_percentage' )
									{ 
										echo '<p>' . esc_html__( 'Buy ' . $value['quant_min'][$k] . ' to ' . $value['quant_max'][$k] . ' Get ' . $va . '% OFF', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai') . '</p>';
									}
									else{
										echo '<p>' . esc_html__( 'Buy ' . $value['quant_min'][$k] . ' to ' . $value['quant_max'][$k] . ' Get ' . $va . ' OFF', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai') . '</p>';
									}
								}
								?>
							
							<p><a href="<?php echo esc_url( get_permalink( $pros )); ?>" class="rtwwdpd-carousel-buy-btn"><?php esc_html_e( 'Buy Now', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></a></p>
						</div>
					</div>
				<?php } 
				}
			}?>
		</div>
	</div>
	<?php } ?>
	<?php if( is_array( $rtwwdpd_coming_sale ) && !empty( $rtwwdpd_coming_sale ) ){ ?>
	<div class="rtwwdpd-carousel-main-container">
		<div class="rtwwdpd-carousel-header-bg">
			<div class="rtwwdpd-carousel-header-image">
				<img src="<?php echo esc_url(RTWWDPD_URL); ?>assets/images/comingsale.png" alt="<?php echo esc_attr_e('Coming Soon','rtwwdpd-woo-dynamic-pricing-discounts-with-ai') ;?>">
			</div>
			<div class="rtwwdpd-carousel-header-content">
				<h2 class="rtwwdpd-carousel-header-title"><?php esc_html_e( 'Coming Soon', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></h2>
				<h3>
				<?php
				if($value['rtwwdpd_sale_discount_type'] = 'rtwwdpd_discount_percentage' )
				{ 
					esc_html_e( 'Up to ' . $rtwwdpd_max_dis_onsale . '% OFF', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); 
				}else{
					esc_html_e( 'Up to ' . $rtwwdpd_max_dis_onsale . ' OFF', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); 
				}
				?>
				</h3>
				<h3><b><?php esc_html_e( ' ' . ( isset( $rtwwdpd_coming_sale[0]['rtwwdpd_sale_from_date'] ) ? date( get_option('date_format'), strtotime($rtwwdpd_coming_sale[0]['rtwwdpd_sale_from_date'])) : ' ' ). ' ~ ' .( isset( $rtwwdpd_coming_sale[0]['rtwwdpd_sale_to_date'] ) ? date( get_option('date_format'), strtotime($rtwwdpd_coming_sale[0]['rtwwdpd_sale_to_date'])) : '' ), 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b></h3>
			</div>
		</div>
		<div class="rtwwdpd-carousel-slider owl-theme owl-carousel">
			<?php
			foreach ($rtwwdpd_coming_sale as $key => $value) { 
				if(isset($value['product_id']))
				{
					$rtwwdpd_user_role = $value['rtwwdpd_select_roles'] ;
					$role_matched = false;
					foreach ($rtwwdpd_user_role as $rol => $role) {
						if($role == 'all'){
							$role_matched = true;
						}
						if (in_array( $role, (array) $rtwwdpd_user->roles ) ) {
							$role_matched = true;
						}
					}
					if($role_matched == false)
					{
						continue 1;
					}
					foreach ($value['product_id'] as $pro => $pro_id) 
					{

						$rtwwdpd_image = wp_get_attachment_image_src( get_post_thumbnail_id( $pro_id ), 'single-post-thumbnail' ); 
						$rtwwdpd_product = wc_get_product( $pro_id );
						$rtwwdpd_prod_price = $rtwwdpd_product->get_price();
						$rtwwdpd_discounted = 0;
					
						?>
						<div class="rtwwdpd-carousel-slider-item">
							<div class="rtwwdpd-carousel-slider-image">
								<a href="<?php echo get_permalink( $pro_id ); ?>"><img src="<?php echo esc_url($rtwwdpd_image[0]); ?>" alt="<?php echo esc_attr_e('Coming-sale','rtwwdpd-woo-dynamic-pricing-discounts-with-ai') ;?>">
								</a>
							</div>
							<div class="rtwwdpd-carousel-slider-content">
								<span class="rtwwdpd-carousel-offer"><?php esc_html_e( 'Offer', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
								<?php 
								if($value['rtwwdpd_sale_discount_type'] == 'rtwwdpd_discount_percentage'){
									echo '<h2>'. esc_html__( 'Flat '. $value['rtwwdpd_sale_discount_value'] .'% OFF', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai') . '</h2>';

									$rtwwdpd_discounted = $rtwwdpd_prod_price - ( $rtwwdpd_prod_price * ( $value['rtwwdpd_sale_discount_value'] / 100 ));

								}
								else{
									echo '<h2>'. esc_html__( 'Flat ' . $rtwwdpd_symbol . $value['rtwwdpd_sale_discount_value'] .' OFF', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai') . '</h2>';

									$rtwwdpd_discounted = $rtwwdpd_prod_price - ( $rtwwdpd_prod_price * ( $value['rtwwdpd_sale_discount_value'] / 100 ));
									if( $value['rtwwdpd_sale_max_discount'] < $rtwwdpd_discounted )
									{
										$rtwwdpd_discounted = $value['rtwwdpd_sale_max_discount'];
									}
								}
								?>
								<h3><?php echo esc_html( get_the_title( $pro_id ) );
								?></h3>
								<p><?php echo date( get_option('date_format'), strtotime( $value['rtwwdpd_sale_from_date'] ) ) .' ~ '. date( get_option('date_format'), strtotime( $value['rtwwdpd_sale_to_date'] ) );
								?></p>
								<p class="rtwwdpd-carosuel-price"><ins>
									<?php echo esc_html( $rtwwdpd_symbol . $rtwwdpd_discounted ); ?>
								</ins> 
								<del><?php echo esc_html( $rtwwdpd_symbol . $rtwwdpd_prod_price ); ?></del></p>
								<p><a href="<?php echo get_permalink( $pro_id ); ?>" class="rtwwdpd-carousel-buy-btn"><?php esc_html_e( 'Buy Now', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></a></p>
							</div>
						</div>
					<?php 
				}
			} } ?>
			
		</div>
	</div>
	<?php } 
	?>
</div>