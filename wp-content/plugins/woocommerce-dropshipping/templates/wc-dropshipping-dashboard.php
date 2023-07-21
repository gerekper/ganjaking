<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
   $dashboard_report = $this->dashboard;

   $wcd_count_prod_listings = ( null === $dashboard_report->count_prod_listings() ) ? 0 : $dashboard_report->count_prod_listings();
   $wcd_count_prod_out_stock = $dashboard_report->count_prod_out_stock();
   $wcd_orders = $dashboard_report->count_orders();
   $wcd_currency = $dashboard_report->get_currency();
   $wcd_ali_prod = $dashboard_report->get_ali_orders();
   $wcd_orders_inprogress = count( $dashboard_report->get_inprogress_orders() );
   $wcd_orders_completed = $dashboard_report->get_completed_orders();
   $wcd_orders_pending = $dashboard_report->get_pending_orders();
   $wcd_orders_affiliate = $dashboard_report->get_affiliate_prod();
   $wcd_week_old_sales = $dashboard_report->get_week_total_sales();
   $wcd_get_best_selling_prod = $dashboard_report->get_best_selling_prod();
   $wcd_get_low_stocks_prod = $dashboard_report->get_low_on_stocks_prod();
   $get_completed_dropship_orders = $dashboard_report->get_completed_dropship_orders();
   $get_products_draft_count = $dashboard_report->get_products_draft_count();
?>
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script> -->
<div id="woocommerce-dropshipping-dashboard">

  <div class="dash-row">
	 <h1 id="dashboard-header"><?php _e( 'WooCommerce Dropshipping Dashboard', 'woocommerce-dropshipping' ); ?></h1>
  </div>
  <div class="dash-row wcd-blurb-container">
	 <div class="metric-box metric-style1">
		<img src="<?php echo plugin_dir_url( __FILE__ ) . '../assets/icons/listings.svg'; ?>">
		<h2><?php echo $wcd_count_prod_listings; ?></h2>
		<p class="wcd-blurb-text"><?php _e( 'Published Listings', 'woocommerce-dropshipping' ); ?></p>
	 </div>
	 <div class="metric-box metric-style2">
		<img src="<?php echo plugin_dir_url( __FILE__ ) . '../assets/icons/low-stock.svg'; ?>">
		<h2><?php echo $wcd_count_prod_out_stock; ?></h2>
		<p class="wcd-blurb-text"><?php _e( 'Out of Stock', 'woocommerce-dropshipping' ); ?></p>
	 </div>
	 <div class="metric-box metric-style3">
		<img src="<?php echo plugin_dir_url( __FILE__ ) . '../assets/icons/orders.svg'; ?>">
		<h2><?php echo $wcd_orders[0]; ?></h2>
		<p class="wcd-blurb-text"><?php _e( 'Orders', 'woocommerce-dropshipping' ); ?></p>
	 </div>
	 <div class="metric-box metric-style4">
		<img src="<?php echo plugin_dir_url( __FILE__ ) . '../assets/icons/totaol.svg'; ?>">
		<h2><?php echo $wcd_currency; ?> <?php echo isset( $wcd_week_old_sales['profit'] ) ? $wcd_week_old_sales['profit'] : ''; ?></h2>
		<p class="wcd-blurb-text"><?php _e( 'Projected Profit', 'woocommerce-dropshipping' ); ?></p>
	 </div>

	 <?php if ( ( $get_products_draft_count ) >= 1 ) { ?>
	  <div class="metric-box metric-style1">
		 <img src="<?php echo plugin_dir_url( __FILE__ ) . '../assets/icons/ali-draft-prods.png'; ?>" style="    height: 65px;margin: 0 auto; padding: 10px;">
		 <h2 id="ali-draft-prod-count"><?php echo $get_products_draft_count; ?></h2>
		 <p class="wcd-blurb-text"><?php _e( 'Draft Products', 'woocommerce-dropshipping' ); ?></p>
		 <button class="button button1" id="ali-draft-publish-btn" >Publish Products</button>
	  </div>
	  <?php } ?>
  </div>
  <div class="dash-row">
	 <div class="dash-section-50 bar-chart">
		<h2 class="white"><?php _e( 'Recent Order Data', 'woocommerce-dropshipping' ); ?></h2>
		<div class="chart-wrapper">
		   <canvas id="bar-chart-grouped"></canvas>
		</div>
	 </div>
	 <div class="dash-section-50 dash-stats">
		<h2 class="white"><?php _e( 'Account Information', 'woocommerce-dropshipping' ); ?></h2>
		<p id="account-info-note"><?php _e( 'WooCommerce orders at a glance', 'woocoommerce-dropshipping' ); ?></p>
		<div class="blurb">
		   <div class="blurb-inner">
			  <img src="<?php echo plugin_dir_url( __FILE__ ) . '../assets/icons/aliexpress.svg'; ?>">
			  <h3><?php _e( 'Aliexpress Orders', 'woocommerce-dropshipping' ); ?></h3>
			  <div class="blurb-content"><span><?php echo $wcd_ali_prod[0]; ?></span></div>
		   </div>
		</div>
		<div class="blurb">
		   <div class="blurb-inner">
			  <img src="<?php echo plugin_dir_url( __FILE__ ) . '../assets/icons/progress-1.svg'; ?>">
			  <h3><?php _e( 'Orders in Progress', 'woocommerce-dropshipping' ); ?></h3>
			  <div class="blurb-content"><?php echo $wcd_orders_inprogress; ?></div>
		   </div>
		</div>
		<div class="blurb">
		   <div class="blurb-inner">
			  <img src="<?php echo plugin_dir_url( __FILE__ ) . '../assets/icons/progress.svg'; ?>">
			  <h3><?php _e( 'Orders Completed', 'woocommerce-dropshipping' ); ?></h3>
			  <div class="blurb-content"><?php echo $wcd_orders_completed; ?></div>
		   </div>
		</div>
		<div class="blurb">
		   <div class="blurb-inner">
			  <img src="<?php echo plugin_dir_url( __FILE__ ) . '../assets/icons/stock-alert.svg'; ?>">
			  <h3><?php _e( 'Out of Stock Listings', 'woocommerce-dropshipping' ); ?></h3>
			  <div class="blurb-content"><?php echo $wcd_count_prod_out_stock; ?></div>
		   </div>
		</div>
		<div class="blurb">
		   <div class="blurb-inner">
			  <img src="<?php echo plugin_dir_url( __FILE__ ) . '../assets/icons/pending.svg'; ?>">
			  <h3><?php _e( 'Pending Orders', 'woocommerce-dropshipping' ); ?></h3>
			  <div class="blurb-content"><?php echo $wcd_orders_pending; ?></div>
		   </div>
		</div>
		<div class="blurb">
		   <div class="blurb-inner">
			  <img src="<?php echo plugin_dir_url( __FILE__ ) . '../assets/icons/progress.svg'; ?>">
			  <h3><?php _e( 'Completed Dropshipping Orders', 'woocommerce-dropshipping' ); ?></h3>
			  <div class="blurb-content"><?php echo $get_completed_dropship_orders; ?></div>
		   </div>
		</div>
		<div class="blurb">
		   <div class="blurb-inner">
			  <img src="<?php echo plugin_dir_url( __FILE__ ) . '../assets/icons/affiliate.svg'; ?>">
			  <h3><?php _e( 'Active Affiliate Products', 'woocommerce-dropshipping' ); ?></h3>
			  <div class="blurb-content"><?php echo $wcd_orders_affiliate; ?></div>
		   </div>
		</div>
	 </div>
  </div>
  <div class="dash-row">
	 <div class="dash-slider">
		<h2 class="white font-28"><?php _e( 'Best Selling Products', 'woocommerce-dropshipping' ); ?></h2>
		<div class="gap"></div>
		<?php foreach ( $wcd_get_best_selling_prod as $key => $value ) { ?>
		<div class="product-slide">
		  <a href="<?php echo $value[3]; ?>">
			<?php echo $value[2]; ?>
			<h3><?php echo $value[1]; ?></h3>
			<?php echo $value[0] . ' sold'; ?>
		  </a>
		</div>
	  <?php } ?>
	 </div>
  </div>
  <div class="dash-row">
	 <div class="dash-slider">
		<h2 class="white font-28"><?php _e( 'Low on Stock', 'woocommerce-dropshipping' ); ?></h2>
		<div class="gap-2"></div>
		   <?php foreach ( $wcd_get_low_stocks_prod as $key => $value ) { ?>
		   <div class="product-slide">
			 <a href="<?php echo $value[3]; ?>">
				<?php echo $value[2]; ?>
			   <h3><?php echo $value[1]; ?></h3>
				<?php echo $value[0] . ' left'; ?>
			 </a>
		   </div>
		 <?php } ?>
	 </div>
  </div>
  <div class="dash-row">
	 <h2 class="white font-40"><?php _e( 'Plugin Setup', 'woocommerce-dropshipping' ); ?></h2>
  </div>
  <div id="plugin-setup" class="dash-row thirds">
	 <div class="metric-box">
		<img src="<?php echo plugin_dir_url( __FILE__ ) . '../assets/icons/aliexpress.svg'; ?>">
		<h2><?php _e( 'Aliexpress Import', 'woocommerce-dropshipping' ); ?></h2>
		<p class="plugin-setup-desc"><?php _e( 'Install the extension to get started', 'woocommerce-dropshipping' ); ?></p>
		<a target="_blank" href="https://chrome.google.com/webstore/detail/woocommerce-dropshipping/hfhghglengghapddjhheegmmpahpnkpo?hl=en"><?php _e( 'Install Now', 'woocommerce-dropshipping' ); ?></a>
	 </div>
	 <div class="metric-box">
		<img src="<?php echo plugin_dir_url( __FILE__ ) . '../assets/icons/settings.svg'; ?>">
		<h2><?php _e( 'Adjust Plugin Settings', 'woocommerce-dropshipping' ); ?></h2>
		<p class="plugin-setup-desc"><?php _e( 'Configure packing slips, emails and more.', 'woocommerce-dropshipping' ); ?></p>
		<a href="<?php echo get_admin_url(); ?>admin.php?page=wc-settings&tab=wc_dropship_settings"><?php _e( 'Go to Settings', 'woocommerce-dropshipping' ); ?></a>
	 </div>
	 <div class="metric-box">
		<img src="<?php echo plugin_dir_url( __FILE__ ) . '../assets/icons/affiliate.svg'; ?>">
		<h2><?php _e( 'Suppliers', 'woocommerce-dropshipping' ); ?></h2>
		<p class="plugin-setup-desc"><?php _e( 'Organise your store\'s products by their supplier.', 'woocommerce-dropshipping' ); ?></p>
		<a href="<?php echo get_admin_url(); ?>edit-tags.php?taxonomy=dropship_supplier&post_type=product"><?php _e( 'Manage Suppliers', 'woocommerce-dropshipping' ); ?></a>
	 </div>
  </div>
</div>
<!-- End of dashboard -->


<?php wp_enqueue_style( 'add_custom_dashboard_style' ); ?>
<?php wp_enqueue_script( 'add_dropshipping_chart_lib' ); ?>
<?php wp_enqueue_script( 'add_custom_dashboard_script' ); ?>
