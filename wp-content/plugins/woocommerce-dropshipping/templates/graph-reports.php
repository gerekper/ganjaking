<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

   $base_name = explode( '/', plugin_basename( __FILE__ ) );
	$dashboard_report = $this->dashboard;
   $wcd_count_prod_listings = ( null === $dashboard_report->count_prod_listings() ) ? 0 : $dashboard_report->count_prod_listings();
   $wcd_count_untracked_prod_listings = ( null === $dashboard_report->count_untracked_prod_listings() ) ? 0 : $dashboard_report->count_untracked_prod_listings();
   $wcd_count_prod_out_stock = $dashboard_report->count_prod_out_stock();
   $wcd_daily_orders_count = count( $dashboard_report->get_daily_total_orders() );
   $wcd_daily_orders_profit = $dashboard_report->get_daily_purchases_total();
   $wcd_orders = $dashboard_report->count_orders();
   $wcd_currency = $dashboard_report->get_currency();
   $wcd_ali_prod = $dashboard_report->get_ali_orders();
   $wcd_orders_inprogress = count( $dashboard_report->get_inprogress_orders() );
   $wcd_orders_completed = $dashboard_report->get_completed_orders();
   $wcd_orders_pending = ( null === $dashboard_report->get_pending_orders() ) ? 0 : $dashboard_report->get_pending_orders();
   $wcd_orders_affiliate = $dashboard_report->get_affiliate_prod();
   $wcd_week_old_sales = $dashboard_report->get_week_total_sales();
   $wcd_daily_sales = $dashboard_report->get_daily_total_sales();
   $wcd_get_best_selling_prod = $dashboard_report->get_best_selling_prod();
   $wcd_get_low_stocks_prod = $dashboard_report->get_low_on_stocks_prod();
   $get_completed_dropship_orders = $dashboard_report->get_completed_dropship_orders();

   $get_one_week_daily_total_orders = $dashboard_report->get_one_week_daily_total_orders();
   $get_one_week_daily_profit = $dashboard_report->get_one_week_daily_profit();

   $get_one_month_total_orders = $dashboard_report->get_one_month_total_orders();
   $get_per_month_profit = $dashboard_report->get_per_month_profit();

   $get_per_week_total_orders = $dashboard_report->get_per_week_total_orders();
   $get_per_week_profit = $dashboard_report->get_per_week_profit();

   $get_per_year_total_orders = $dashboard_report->get_per_year_total_orders();
   $get_per_year_profit = $dashboard_report->get_per_year_profit();
  // echo "<pre>";
  // print_r($get_per_month_profit);

?> 
<style>

:root {
	--p_info_card_item: 4;
	--p_info_card_gap: 15px;
	--p_info_card_width: calc((100% / var(--p_info_card_item)) - var(--p_info_card_gap));

	/* eco_info */
	--p_aco_info_right_size: 100px;
	--p_aco_info_left_size: calc(100% - var(--p_aco_info_right_size));

	/* grid */
	--p_col_gap: 15px;
	--p_col_count: 12;
	--p_col_size: ((100% / var(--p_col_count)) - (var(--p_col_gap) / 2));
}

/* grid */
.p_row {
	display: flex;
	flex-wrap: wrap;
}
.p_row > * {
	margin: calc(var(--p_col_gap) / 2);
	flex-grow: 1;
	flex-shrink: 0;
	flex-basis: initial;
}
.p_col_8 {
	width: calc(var(--p_col_size) * 8);
}

.p_col_4 {
	width: calc(var(--p_col_size) * 4);
}
/* grid end */
/* background */
.bg_skblu {
	background-color: #11cfe9;
}
.bg_oblu {
	background-color: #0f7ab0;
}
.bg_opnk {
	background-color: #e55b58;
}
.bg_gren {
	background-color: #11ba5f;
}
/* p_info_card */
.p_info_wrap {
	display: flex;
	flex-wrap: wrap;
}
.p_info_card {
	position: relative;
	flex: 1;
	flex-shrink: 0;
	padding: 20px;
	border-radius: 5px;
	color: #fff;
	/* background-color: red; */
	width: var(--p_info_card_width);
	margin: calc(var(--p_info_card_gap) / 2);
	overflow: hidden;
}
.p_info_card .p_icon {
	position: absolute;
	font-size: 124px;
	right: 15px;
	bottom: -50%;
	z-index: 0;
	color: #000;
	opacity: .1;
}
.p_info_card .p_num {
	position: relative;
	font-size: 40px;
	line-height: 64px;
	margin: 0px;
}
.p_info_card .p_title {
	position: relative;
	font-size: 20px;
	font-weight: 500;
	margin: 0px;

}
.p_info_card .p_text {
	position: relative;
	font-size: 14px;
	line-height: 22px;
	margin: 0px;
}



/* p_dropdown */
.p_dropdown {
	position: relative;
	display: inline-block;
}
.p_dropdown .p_drop {
 font-size: 15px;
 text-align: center;
 padding: 6px 12px;
 color: #454545
}
.p_dropdown:hover .p_drop_content {
	display: block;
  }
.p_drop_content {
	display: none;
	position: absolute;
	background-color: #f9f9f9;
	min-width: 160px;
	box-shadow: 0px 3px 6px 0px rgba(0,0,0,0.2);
	padding: 12px 16px;
	z-index: 1;
	right: 0px;
  }

  .p_drop_cont_left {
	  right: 0px;
  }
  
  .p_drop_content .p_drop_item {
	display: block;
	padding: 6px 12px;
	color: #000;
	font-size: 15px;
	text-decoration: none;
	transition: .3s all ease-in-out;
  }
  .p_drop_content .p_drop_item:hover {
	  color: #fff;
	  background-color: #004d58;
  }

  /* aco_info */
  .p_eco_wrap {
	max-width: 100%;
  }
  .p_aco_info {
	  border: 2px solid #f7f7f7;
	  background-color: #f1f1f161;
  }
  .p_aco_info .p_aco_head {
	  display: flex;
	  padding: 10px 15px;
	  background-color: #fff;
  }
  .p_aco_info .p_aco_head .p_title {
	  display: inline-block;
	  font-size: 18px;
	  margin: 0px;
  }
  .p_aco_info .p_aco_head .p_eco_dropdown {
	display: inline-block;
	margin: 0px;
	margin-left: auto;
  }

  .p_eco_body {
	padding: 20px;
  }
  .p_aco_list .p_aco_list_item {
	padding: 10px 0px;
	display: flex;
	align-items: center;
	min-height: 50px;
  }
  .p_aco_list .p_aco_list_item + .p_aco_list_item  {
	  border-top: 1px solid #948a8a;
  }
  .p_aco_list .p_aco_list_item .p_text {
	  font-size: 16px;
	  margin: 0px;
  }
  .p_left {
	  width: var(--p_aco_info_left_size);
  }
  .p_right {
	  width: var(--p_aco_info_right_size);
  }
  .p_aco_list .p_aco_list_item .p_bag {
	  background-color:#13ba55;
	  color: #fff;
	  padding: 4px 8px;
	  border-radius: 2px;
  }

  /* grap wrap */
  .p_graph_wrap {
	max-width: 100%;
	background: #f1f1f161;
	border: 2px solid #f1f1f1;
	padding-bottom: 10px;
	border-radius: 2px;
  }
  
  .p_graph_wrap .p_graph_head {
	display: flex;
	padding: 10px 15px;
	background-color: #fff;
}
.p_graph_wrap .p_graph_head .p_dropdown {
	margin-left: auto;
}
.p_graph_body .p_graph {
	min-height: 300px;
}
.p_btn_wrap {
	text-align: center;
	padding: 6px 12px;
}
.p_btn_wrap .p_btn {
	padding: 6px 16px;
	font-size: 15px;
	border: none;
	background-color: #1fb6cc;
	color: #fff;
	border-radius: 2px;
}
.p_btn_wrap .p_btn:hover,
.p_btn_wrap .p_btn.active {
	background-color: #2691b2;
}


@media  screen and (max-width: 767.98px) {
	:root {
		/* --p_info_card_item: 1; */
		--p_col_count: 1;
		--p_info_card_width: unset;
		--p_aco_info_right_size: 66px;
	}
	.p_row {
		flex-direction: column;
	}
	.p_col_4,
	.p_col_8 {
		width: 100%;
		margin: 0px;
	}
	.p_info_wrap {
		flex-direction: column;
	}
	.p_graph_wrap,
	.p_aco_info {
		margin: 10px;
	}
	.p_btn_wrap {
		padding: 6px;
	}
	.p_btn_wrap .p_btn {
		font-size: 14px;
	}
	.p_graph_body .p_graph {
		min-height: 150px;
	}
}   
</style>
<section class="p_wrapper">
		<div class="p_info_wrap">
			<div class="p_info_card bg_oblu">
				<div class="p_icon">
					<i class="far fa-calendar-check"></i>
				</div>
				<h3 class="p_num"><?php echo esc_html( $wcd_count_prod_listings ); ?></h3>

				<h5 class="p_title">Active Listing</h5>
				<p class="p_text">Active and monitered listings</p>
			</div>
			<div class="p_info_card bg_opnk">
				<div class="p_icon">
					<i class="far fa-calendar-check"></i>
				</div>
				<h3 class="p_num"><?php echo esc_html( $wcd_count_untracked_prod_listings ); ?></h3>
				<h5 class="p_title">Untracked Listings</h5>
				<p class="p_text">Current number of Untracked listings </p>
			</div>
			<div class="p_info_card bg_skblu">
				<div class="p_icon">
					<i class="fas fa-shopping-cart"></i>
				</div>
				<h3 class="p_num"><?php echo esc_html( $wcd_daily_orders_count ); ?></h3>
				<h5 class="p_title">Orders</h5>
				<p class="p_text">Orders in the Past 24 hours</p>
			</div>
			<div class="p_info_card bg_gren">
				<div class="p_icon">
					<i class="fas fa-dollar-sign"></i>
				</div>
				<h3 class="p_num"><?php echo esc_html( get_woocommerce_currency_symbol() . ( $wcd_daily_sales['profit'] !== null ? $wcd_daily_sales['profit'] : 0 ) ); ?></h3>

				<h5 class="p_title">Profit</h5>
				<p class="p_text">Estimate profit in the last 24 hours.</p>
			</div>
		</div>

		<!-- flex row -->
		<div class="p_row">
			<!-- graph -->
			<div class="p_col_8">
				<div class="p_graph_wrap">
					<div class="p_graph_head">
						<!-- dropdown -->
						<div class="p_dropdown">
							<span class="p_drop"><i class="fas fa-chevron-down"></i></span>
							<div class="p_drop_content">
								<a href="javascript:void(0)" class="p_drop_item">Drop item</a>
								<a href="javascript:void(0)" class="p_drop_item">Drop item</a>
								<a href="javascript:void(0)" class="p_drop_item">Drop item</a>
								<a href="javascript:void(0)" class="p_drop_item">Drop item</a>
							</div>
						</div>
		
					</div>
					<div class="p_graph_body">
						<div class="p_graph">
							<canvas id="myChart" width="400" height="400"></canvas>
						</div>
						<div class="p_graph_foot">
							<div class="p_btn_wrap">
								  <button class="p_btn active" onclick="timeFrame(this)" value="day">Days</button>
								<button class="p_btn" onclick="timeFrame(this)" value="week">Weeks</button>
								<button class="p_btn" onclick="timeFrame(this)" value="month">Months</button>
								<button class="p_btn" onclick="timeFrame(this)" value="year">Years</button>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="p_col_4">
				<!-- aco info -->
				<div class="p_eco_wrap">
					<div class="p_aco_info">
						<div class="p_aco_head">
							<h5 class="p_title">Account Information</h5>
							<!-- dropdown -->
							<div class="p_dropdown p_eco_dropdown">
								<span class="p_drop"><i class="fas fa-chevron-down"></i></span>
								<div class="p_drop_content">
									<a href="javascript:void(0)" class="p_drop_item">Drop item</a>
									<a href="javascript:void(0)" class="p_drop_item">Drop item</a>
									<a href="javascript:void(0)" class="p_drop_item">Drop item</a>
									<a href="javascript:void(0)" class="p_drop_item">Drop item</a>
								</div>
							</div>
						</div>
						<div class="p_eco_body">
							<div class="p_aco_list">
								<div class="p_aco_list_item">
									<p class="p_text p_left"><span class="p_txt">Auto Orders Mode</span></p>
									<p class="p_text p_right"><span class="p_bag"><?php echo esc_html( $wcd_orders[0] ); ?></span></p>

								</div>
								<div class="p_aco_list_item">
									<p class="p_text p_left"><span class="p_txt">Max Monitoring Products</span></p>
									<p class="p_text p_right"><span class="p_bag">0</span></p>
								</div>
								<div class="p_aco_list_item">
									<p class="p_text p_left"><span class="p_txt">Out Of Stock Listings:</span></p>
									<p class="p_text right"><span class="p_bag"><?php echo esc_html( $wcd_count_prod_out_stock ); ?></span></p>

								</div>
								<div class="p_aco_list_item">
									<p class="p_text p_left"><span class="p_txt">Pending Orders:</span></p>
									<p class="p_text p_right"><span class="p_bag"><?php echo esc_html( $wcd_orders_pending ); ?></span></p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<script src="<?php echo esc_url( plugins_url() ) . '/' . esc_attr( $base_name[0] ) . '/assets/js/jquery.min.js'; ?>"></script>

	<script>
		 $(document).ready(function(){
			$('.p_btn').click(function() {
				$(this).addClass('active').siblings().removeClass('active');
			});
		});
	</script>
	 <script type="text/javascript" src="<?php echo esc_url( plugins_url() ) . '/' . esc_attr( $base_name[0] ) . '/assets/js/chart.js'; ?>"></script>
	<script src="<?php echo esc_url( plugins_url() ) . '/' . esc_attr( $base_name[0] ) . '/assets/js/chartjs-adapter-date-fns.bundle.min.js'; ?>"></script>
		<script>
	// setup 
	
	  const day = [
			{ x: Date.parse('<?php echo esc_url( $get_one_week_daily_total_orders[0]->post_date ); ?>'), y: <?php echo esc_url( $get_one_week_daily_total_orders[0]->total ); ?> },
			{ x: Date.parse('<?php echo esc_url( $get_one_week_daily_total_orders[1]->post_date ); ?>'), y: <?php echo esc_url( $get_one_week_daily_total_orders[1]->total ); ?>},
			{ x: Date.parse('<?php echo esc_url( $get_one_week_daily_total_orders[2]->post_date ); ?>'), y: <?php echo esc_url( $get_one_week_daily_total_orders[2]->total ); ?> },
			{ x: Date.parse('<?php echo esc_url( $get_one_week_daily_total_orders[3]->post_date ); ?>'), y: <?php echo esc_url( $get_one_week_daily_total_orders[3]->total ); ?> },
			{ x: Date.parse('<?php echo esc_url( $get_one_week_daily_total_orders[4]->post_date ); ?>'), y: <?php echo esc_url( $get_one_week_daily_total_orders[4]->total ); ?> },
			{ x: Date.parse('<?php echo esc_url( $get_one_week_daily_total_orders[5]->post_date ); ?>'), y: <?php echo esc_url( $get_one_week_daily_total_orders[5]->total ); ?> },
			{ x: Date.parse('<?php echo esc_url( $get_one_week_daily_total_orders[6]->post_date ); ?>'), y: <?php echo esc_url( $get_one_week_daily_total_orders[6]->total ); ?> },
		];
		
		const day1 = [
			{ x: Date.parse('<?php echo esc_url( $get_one_week_daily_profit[0]->post_date ); ?>'), y: <?php echo esc_url( $get_one_week_daily_profit[0]->total ); ?> },
			{ x: Date.parse('<?php echo esc_url( $get_one_week_daily_profit[1]->post_date ); ?>'), y: <?php echo esc_url( $get_one_week_daily_profit[1]->total ); ?>},
			{ x: Date.parse('<?php echo esc_url( $get_one_week_daily_profit[2]->post_date ); ?>'), y: <?php echo esc_url( $get_one_week_daily_profit[2]->total ); ?> },
			{ x: Date.parse('<?php echo esc_url( $get_one_week_daily_profit[3]->post_date ); ?>'), y: <?php echo esc_url( $get_one_week_daily_profit[3]->total ); ?> },
			{ x: Date.parse('<?php echo esc_url( $get_one_week_daily_profit[4]->post_date ); ?>'), y: <?php echo esc_url( $get_one_week_daily_profit[4]->total ); ?> },
			{ x: Date.parse('<?php echo esc_url( $get_one_week_daily_profit[5]->post_date ); ?>'), y: <?php echo esc_url( $get_one_week_daily_profit[5]->total ); ?> },
			{ x: Date.parse('<?php echo esc_url( $get_one_week_daily_profit[6]->post_date ); ?>'), y: <?php echo esc_url( $get_one_week_daily_profit[6]->total ); ?> },
		];
		

		const week = [
			{ x: Date.parse('<?php echo esc_url( $get_per_week_total_orders[0]->post_date ); ?>'), y: <?php echo esc_url( $get_per_week_total_orders[0]->total ); ?> },
			{ x: Date.parse('<?php echo esc_url( $get_per_week_total_orders[1]->post_date ); ?>'), y: <?php echo esc_url( $get_per_week_total_orders[1]->total ); ?> },
			{ x: Date.parse('<?php echo esc_url( $get_per_week_total_orders[2]->post_date ); ?>'), y: <?php echo esc_url( $get_per_week_total_orders[2]->total ); ?> },
			{ x: Date.parse('<?php echo esc_url( $get_per_week_total_orders[3]->post_date ); ?>'), y: <?php echo esc_url( $get_per_week_total_orders[3]->total ); ?> },
			{ x: Date.parse('<?php echo esc_url( $get_per_week_total_orders[4]->post_date ); ?>'), y: <?php echo esc_url( $get_per_week_total_orders[4]->total ); ?> },
		];
		
		const week1 = [
			{ x: Date.parse('<?php echo esc_url( $get_per_week_profit[0]->post_date ); ?>'), y: <?php echo esc_url( $get_per_week_profit[0]->total ); ?> },
			{ x: Date.parse('<?php echo esc_url( $get_per_week_profit[1]->post_date ); ?>'), y: <?php echo esc_url( $get_per_week_profit[1]->total ); ?> },
			{ x: Date.parse('<?php echo esc_url( $get_per_week_profit[2]->post_date ); ?>'), y: <?php echo esc_url( $get_per_week_profit[2]->total ); ?> },
			{ x: Date.parse('<?php echo esc_url( $get_per_week_profit[3]->post_date ); ?>'), y: <?php echo esc_url( $get_per_week_profit[3]->total ); ?> },
			{ x: Date.parse('<?php echo esc_url( $get_per_week_profit[4]->post_date ); ?>'), y: <?php echo esc_url( $get_per_week_profit[4]->total ); ?> },
		];

		 const month = [
			{ x: Date.parse('<?php echo esc_url( $get_one_month_total_orders[0]->post_date ); ?>'), y: <?php echo esc_url( $get_one_month_total_orders[0]->total ); ?> },
			{ x: Date.parse('<?php echo esc_url( $get_one_month_total_orders[1]->post_date ); ?>'), y: <?php echo esc_url( $get_one_month_total_orders[1]->total ); ?> },
			{ x: Date.parse('<?php echo esc_url( $get_one_month_total_orders[2]->post_date ); ?>'), y: <?php echo esc_url( $get_one_month_total_orders[2]->total ); ?> },
			{ x: Date.parse('<?php echo esc_url( $get_one_month_total_orders[3]->post_date ); ?>'), y: <?php echo esc_url( $get_one_month_total_orders[3]->total ); ?> },
			{ x: Date.parse('<?php echo esc_url( $get_one_month_total_orders[4]->post_date ); ?>'), y: <?php echo esc_url( $get_one_month_total_orders[4]->total ); ?> },
		];
		
		const month1 = [
			{ x: Date.parse('<?php echo esc_url( $get_per_month_profit[0]->post_date ); ?>'), y: <?php echo esc_url( $get_per_month_profit[0]->total ); ?> },
			{ x: Date.parse('<?php echo esc_url( $get_per_month_profit[1]->post_date ); ?>'), y: <?php echo esc_url( $get_per_month_profit[1]->total ); ?> },
			{ x: Date.parse('<?php echo esc_url( $get_per_month_profit[2]->post_date ); ?>'), y: <?php echo esc_url( $get_per_month_profit[2]->total ); ?> },
			{ x: Date.parse('<?php echo esc_url( $get_per_month_profit[3]->post_date ); ?>'), y: <?php echo esc_url( $get_per_month_profit[3]->total ); ?> },
			{ x: Date.parse('<?php echo esc_url( $get_per_month_profit[4]->post_date ); ?>'), y: <?php echo esc_url( $get_per_month_profit[4]->total ); ?> },
		];

	   const year = [
			{ x: Date.parse('<?php echo esc_url( $get_per_year_total_orders[0]->post_date ); ?>'), y: <?php echo esc_url( $get_per_year_total_orders[0]->total ); ?> },
			{ x: Date.parse('<?php echo esc_url( $get_per_year_total_orders[1]->post_date ); ?>'), y: <?php echo esc_url( $get_per_year_total_orders[1]->total ); ?> },
			{ x: Date.parse('<?php echo esc_url( $get_per_year_total_orders[2]->post_date ); ?>'), y: <?php echo esc_url( $get_per_year_total_orders[2]->total ); ?> },
			{ x: Date.parse('<?php echo esc_url( $get_per_year_total_orders[3]->post_date ); ?>'), y: <?php echo esc_url( $get_per_year_total_orders[3]->total ); ?> },
			{ x: Date.parse('<?php echo esc_url( $get_per_year_total_orders[4]->post_date ); ?>'), y: <?php echo esc_url( $get_per_year_total_orders[4]->total ); ?> },
		];
		
		const year1 = [
			{ x: Date.parse('<?php echo esc_url( $get_per_year_profit[0]->post_date ); ?>'), y: <?php echo esc_url( $get_per_year_profit[0]->total ); ?> },
			{ x: Date.parse('<?php echo esc_url( $get_per_year_profit[1]->post_date ); ?>'), y: <?php echo esc_url( $get_per_year_profit[1]->total ); ?> },
			{ x: Date.parse('<?php echo esc_url( $get_per_year_profit[2]->post_date ); ?>'), y: <?php echo esc_url( $get_per_year_profit[2]->total ); ?> },
			{ x: Date.parse('<?php echo esc_url( $get_per_year_profit[3]->post_date ); ?>'), y: <?php echo esc_url( $get_per_year_profit[3]->total ); ?> },
			{ x: Date.parse('<?php echo esc_url( $get_per_year_profit[4]->post_date ); ?>'), y: <?php echo esc_url( $get_per_year_profit[4]->total ); ?> },
		];
		
	const data = {
	 // labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
	  datasets: [{
		label: 'Order',
		data: day,       
		borderColor: [
		  'rgba(54, 162, 235, 1)'
		  
		],
		borderWidth: 1
	  },{
		label: 'Profit',
		data: day1,
		backgroundColor: [
		  'rgba(255, 26, 104, 0.2)'
		],
		borderWidth: 1
	  }]
	};

	// config 
	const config = {
	  type: 'bar',
	  data,
	  options: {
		  legend: {
				display: true,
				labels: {
					fontColor: "#000080",
				}
			},
		scales: {
		  x:{
		   type: 'time',
		   time: {
		   unit: 'day'
		   }
		  },
		  y: {
			beginAtZero: true
		  }
		}
	  }
	};

	// render init block
	const myChart = new Chart(
	  document.getElementById('myChart'),
	  config
	);
	
	function timeFrame(period) {

			
 if(period.value == 'day') {
				  myChart.config.options.scales.x.time.unit = period.value;
				  myChart.config.data.datasets[0].data = day;
				  myChart.config.data.datasets[1].data = day1;       
				 
			 }
			 if(period.value == 'week') {
				 myChart.config.options.scales.x.time.unit = period.value;
				 myChart.config.data.datasets[0].data = week;
				 myChart.config.data.datasets[1].data = week1;
			 }
			if(period.value == 'month') {
				 myChart.config.options.scales.x.time.unit = period.value;
			   myChart.config.data.datasets[0].data = month;
				myChart.config.data.datasets[1].data = month1;
			}
			 if(period.value == 'year') {
				myChart.config.options.scales.x.time.unit = period.value;
				 myChart.config.data.datasets[0].data = year;
				  myChart.config.data.datasets[1].data = year1;
			 }
			 myChart.update();
			
		}
		
		
	</script>
