<?php



class WC_Report_Sales_By_Supplier extends WC_Admin_Report {

	/**
	 * Output the report.
	 */

	public function output_report() {

		$term_id = '';

		$end_date = date( 'Y-m-d' );

		$start_date = date( 'Y-m-d', strtotime( '-1 month' ) );

		$htmlTable = '<tr><td colspan="6">Please select supplier and date range.</td></tr>';

		if ( isset( $_POST['export'] ) ) {

			$term_id = $_POST['term_id'];

			$start_date = $_POST['start_date'];

			$end_date = $_POST['end_date'];

			global $wp_session;

			$wp_session['start_date'] = $start_date;

			$wp_session['end_date'] = $end_date;

			add_filter( 'posts_where', array( $this, 'filter_where' ) );

			 $args = array(

				 'posts_per_page' => -1,

				 'post_type' => 'shop_order',

				 'post_status' => array( 'wc-processing', 'wc-completed', 'wc-failed', 'wc-on-hold', 'wc-cancelled', 'wc-expired' ),

				 'meta_key' => 'dropship_supplier_' . $term_id,

				 'meta_value' => $term_id,

			 );

			 $the_query = new WP_Query( $args );

			 $htmlTable = '';

			 if ( $the_query->have_posts() ) {

				 while ( $the_query->have_posts() ) {

					 $the_query->the_post();

					 $order = wc_get_order( get_the_ID() );

					 $order_data = $order->get_data();

					 $order_id = $order_data['id'];

					 $order_parent_id = $order_data['parent_id'];

					 $order_status = $order_data['status'];

					 $order_currency = $order_data['currency'];

					 $order_version = $order_data['version'];

					 $order_payment_method = $order_data['payment_method'];

					 $order_payment_method_title = $order_data['payment_method_title'];

					 $order_total_final = $order_data['total'];

					 $order_date_created = $order_data['date_created']->date( 'M d, Y' );

					 $order_date_modified = $order_data['date_modified']->date( 'Y-m-d H:i:s' );

					 $order_timestamp_created = $order_data['date_created']->getTimestamp();

					 $order_timestamp_modified = $order_data['date_modified']->getTimestamp();

					 $order_discount_total = $order_data['discount_total'];

					 $order_discount_tax = $order_data['discount_tax'];

					 $order_shipping_total = wc_price( $order_data['shipping_total'] );

					 $order_shipping_tax = $order_data['shipping_tax'];

					 $order_total = $order_data['cart_tax'];

					 $order_total_tax = $order_data['total_tax'];

					 $order_customer_id = $order_data['customer_id'];

					 /**************** BILLING INFORMATION*/

					 $order_billing_first_name = $order_data['billing']['first_name'];

					 $order_billing_last_name = $order_data['billing']['last_name'];

					 $order_billing_company = $order_data['billing']['company'];

					 $order_billing_address_1 = $order_data['billing']['address_1'];

					 $order_billing_address_2 = $order_data['billing']['address_2'];

					 $order_billing_city = $order_data['billing']['city'];

					 $order_billing_state = $order_data['billing']['state'];

					 $order_billing_postcode = $order_data['billing']['postcode'];

					 $order_billing_country = $order_data['billing']['country'];

					 $order_billing_email = $order_data['billing']['email'];

					 $order_billing_phone = $order_data['billing']['phone'];

					 /**********************SHIPPING INFORMATION*/

					 $order_shipping_first_name = $order_data['shipping']['first_name'];

					 $order_shipping_last_name = $order_data['shipping']['last_name'];

					 $order_shipping_company = $order_data['shipping']['company'];

					 $order_shipping_address_1 = $order_data['shipping']['address_1'];

					 $order_shipping_address_2 = $order_data['shipping']['address_2'];

					 $order_shipping_city = $order_data['shipping']['city'];

					 $order_shipping_state = $order_data['shipping']['state'];

					 $order_shipping_postcode = $order_data['shipping']['postcode'];

					 $order_shipping_country = $order_data['shipping']['country'];

					 $states = WC()->countries->get_states( $order_shipping_country );

					 $order_shipping_state = ! empty( $states[ $order_shipping_state ] ) ? $states[ $order_shipping_state ] : '';

					 $order_shipping_country = ! empty( WC()->countries->countries[ $order_shipping_country ] ) ? : '';

					 $states = WC()->countries->get_states( $order_billing_country );

					 $order_billing_state = ! empty( $states[ $order_billing_state ] ) ? $states[ $order_billing_state ] : '';

					 $order_billing_country = WC()->countries->countries[ $order_billing_country ];

					 if ( ! empty( $order_shipping_address_2 ) ) {

						  $full_ship_address = $order_shipping_first_name . ' ' . $order_shipping_last_name . ', ' . $order_shipping_address_1 . ', ' . $order_shipping_address_2 . ', ' . $order_shipping_city . ', ' . $order_shipping_state . ', ' . $order_shipping_postcode . ', ' . $order_shipping_country;

					 } else {

						  $full_ship_address = $order_shipping_first_name . ' ' . $order_shipping_last_name . ', ' . $order_shipping_address_1 . ', ' . $order_shipping_city . ', ' . $order_shipping_state . ', ' . $order_shipping_postcode . ', ' . $order_shipping_country;

					 }

					 if ( ! empty( $order_billing_address_2 ) ) {

						 $fullBillAddress = $order_billing_first_name . ' ' . $order_billing_last_name . ', ' . $order_billing_address_1 . ', ' . $order_billing_address_2 . ', ' . $order_billing_city . ', ' . $order_billing_state . ', ' . $order_shipping_postcode . ', ' . $order_billing_country;

					 } else {

						 $fullBillAddress = $order_billing_first_name . ' ' . $order_billing_last_name . ', ' . $order_billing_address_1 . ', ' . $order_billing_city . ', ' . $order_billing_state . ', ' . $order_billing_postcode . ', ' . $order_billing_country;

					 }

					 $order_data = $order->get_data();

					 $order_status = $order_data['status'];

					 $htmlTable .= '<tr>';

					 $htmlTable .= '<td>#' . get_the_ID() . ' ' . $order_billing_first_name . ' ' . $order_billing_last_name . '</td>';

					 $htmlTable .= '<td>' . $order_date_created . '</td>';

					 $htmlTable .= '<td>' . $order_status . '</td>';

					 $htmlTable .= '<td>' . $fullBillAddress . '</td>';

					 $htmlTable .= '<td>' . $full_ship_address . '</td>';

					 $htmlTable .= '<td>' . $order_billing_email . '</td>';

					 $htmlTable .= '<td>' . $order_billing_phone . '</td>';

					 $htmlTable .= '<td>' . get_woocommerce_currency_symbol( $order_currency ) . $order_total_final . '</td>';

					 $htmlTable .= '</tr>';

				 }

				 wp_reset_postdata();

			 }

			 remove_filter( 'posts_where', array( $this, 'filter_where' ) );

		} else if ( isset( $_POST['export_csv'] ) ) {

			$term_id = $_POST['term_id'];

			$term = get_term_by( 'id', $term_id, 'dropship_supplier' );

			$start_date = $_POST['start_date'];

			$end_date = $_POST['end_date'];

			global $wp_session;

			$wp_session['start_date'] = $start_date;

			$wp_session['end_date'] = $end_date;

			add_filter( 'posts_where', array( $this, 'filter_where' ) );

			$args = array(

				'posts_per_page' => -1,

				'post_type' => 'shop_order',

				'post_status' => array( 'wc-processing', 'wc-completed', 'wc-failed', 'wc-on-hold', 'wc-cancelled', 'wc-expired' ),

				'meta_key' => 'dropship_supplier_' . $term_id,

				'meta_value' => $term_id,

			);

			$the_query = new WP_Query( $args );

			$htmlTable = '';

			$delimiter = ',';

			$filename = $term->slug . '_' . date( 'Y-m-d' ) . '.csv';

			ob_end_clean();

			$f = fopen( 'php://memory', 'w' );

			$fields = array( 'Order', 'Date', 'Status', 'Billing', 'Ship to', 'Email', 'Phone', 'Total' );

			fputcsv( $f, $fields, $delimiter );

			if ( $the_query->have_posts() ) {

				while ( $the_query->have_posts() ) {

					$the_query->the_post();

					$order = wc_get_order( get_the_ID() );

					$order_data = $order->get_data();

					$order_id = $order_data['id'];

					$order_parent_id = $order_data['parent_id'];

					$order_status = $order_data['status'];

					$order_currency = $order_data['currency'];

					$order_version = $order_data['version'];

					$order_payment_method = $order_data['payment_method'];

					$order_payment_method_title = $order_data['payment_method_title'];

					$order_total_final = $order_data['total'];

					$order_date_created = $order_data['date_created']->date( 'M d, Y' );

					$order_date_modified = $order_data['date_modified']->date( 'Y-m-d H:i:s' );

					$order_timestamp_created = $order_data['date_created']->getTimestamp();

					$order_timestamp_modified = $order_data['date_modified']->getTimestamp();

					$order_discount_total = $order_data['discount_total'];

					$order_discount_tax = $order_data['discount_tax'];

					$order_shipping_total = wc_price( $order_data['shipping_total'] );

					$order_shipping_tax = $order_data['shipping_tax'];

					$order_total = $order_data['cart_tax'];

					$order_total_tax = $order_data['total_tax'];

					$order_customer_id = $order_data['customer_id'];

					/**************** BILLING INFORMATION*/

					$order_billing_first_name = $order_data['billing']['first_name'];

					$order_billing_last_name = $order_data['billing']['last_name'];

					$order_billing_company = $order_data['billing']['company'];

					$order_billing_address_1 = $order_data['billing']['address_1'];

					$order_billing_address_2 = $order_data['billing']['address_2'];

					$order_billing_city = $order_data['billing']['city'];

					$order_billing_state = $order_data['billing']['state'];

					$order_billing_postcode = $order_data['billing']['postcode'];

					$order_billing_country = $order_data['billing']['country'];

					$order_billing_email = $order_data['billing']['email'];

					$order_billing_phone = $order_data['billing']['phone'];

					/**********************SHIPPING INFORMATION*/

					$order_shipping_first_name = $order_data['shipping']['first_name'];

					$order_shipping_last_name = $order_data['shipping']['last_name'];

					$order_shipping_company = $order_data['shipping']['company'];

					$order_shipping_address_1 = $order_data['shipping']['address_1'];

					$order_shipping_address_2 = $order_data['shipping']['address_2'];

					$order_shipping_city = $order_data['shipping']['city'];

					$order_shipping_state = $order_data['shipping']['state'];

					$order_shipping_postcode = $order_data['shipping']['postcode'];

					$order_shipping_country = $order_data['shipping']['country'];

					$states = WC()->countries->get_states( $order_shipping_country );

					$order_shipping_state = ! empty( $states[ $order_shipping_state ] ) ? $states[ $order_shipping_state ] : '';

					// $order_shipping_country = WC()->countries->countries[$order_shipping_country];
					$order_shipping_country = ! empty( WC()->countries->countries[ $order_shipping_country ] ) ? : '';

					$states = WC()->countries->get_states( $order_billing_country );

					$order_billing_state = ! empty( $states[ $order_billing_state ] ) ? $states[ $order_billing_state ] : '';

					$order_billing_country = WC()->countries->countries[ $order_billing_country ];

					if ( ! empty( $order_shipping_address_2 ) ) {

						$full_ship_address = $order_shipping_first_name . ' ' . $order_shipping_last_name . ', ' . $order_shipping_address_1 . ', ' . $order_shipping_address_2 . ', ' . $order_shipping_city . ', ' . $order_shipping_state . ', ' . $order_shipping_postcode . ', ' . $order_shipping_country;

					} else {

						$full_ship_address = $order_shipping_first_name . ' ' . $order_shipping_last_name . ', ' . $order_shipping_address_1 . ', ' . $order_shipping_city . ', ' . $order_shipping_state . ', ' . $order_shipping_postcode . ', ' . $order_shipping_country;

					}

					if ( ! empty( $order_billing_address_2 ) ) {

						  $fullBillAddress = $order_billing_first_name . ' ' . $order_billing_last_name . ', ' . $order_billing_address_1 . ', ' . $order_billing_address_2 . ', ' . $order_billing_city . ', ' . $order_billing_state . ', ' . $order_shipping_postcode . ', ' . $order_billing_country;

					} else {

						$fullBillAddress = $order_billing_first_name . ' ' . $order_billing_last_name . ', ' . $order_billing_address_1 . ', ' . $order_billing_city . ', ' . $order_billing_state . ', ' . $order_billing_postcode . ', ' . $order_billing_country;

					}

					$order_data = $order->get_data();

					$order_status = $order_data['status'];

					$currency = get_woocommerce_currency_symbol( $order_currency );

					// $currency = $currency, PHP_EOL;

					$currency = html_entity_decode( $currency, ENT_HTML5, 'utf-8' );

					$lineData = array( '#' . get_the_ID() . ' ' . $order_billing_first_name . ' ' . $order_billing_last_name, $order_date_created, $order_status, $fullBillAddress, $full_ship_address, $order_billing_email, $order_billing_phone, $currency . $order_total_final );

					fputcsv( $f, $lineData, $delimiter );

				}

				fseek( $f, 0 );

				header( 'Content-Type: text/csv' );

				header( 'Content-Disposition: attachment; filename="' . $filename . '";' );

				fpassthru( $f );

				exit;

				wp_reset_postdata();

			}

			remove_filter( 'posts_where', array( $this, 'filter_where' ) );

		} else {

			global $wp_session;

			$wp_session['start_date'] = $start_date;

			$wp_session['end_date'] = $end_date;

		}

		$taxonomies = get_terms(
			array(

				'taxonomy' => 'dropship_supplier',

				'hide_empty' => false,

			)
		);

		$html = '<div id="poststuff" class="woocommerce-reports-wide" bis_skin_checked="1">

				<div class="postbox" bis_skin_checked="1">

					<h3 class="screen-reader-text"></h3>

						<div class="stats_range">

						<form action="" method="POST">

							<ul>

								<li class="custom">';

		if ( ! empty( $taxonomies ) ) :

			$html .= '<select name="term_id" required>';

			$html .= '<option value="">Select Supplier</option>';

			foreach ( $taxonomies as $category ) {

				$selected = ( $term_id == esc_attr( $category->term_id ) ) ? 'selected' : '';

				$html .= '<option value="' . esc_attr( $category->term_id ) . '" ' . $selected . '>' . esc_attr( $category->name ) . '</option>';

			}

			$html .= '</select>';

								endif;

								$html .= '</li>

								<li class="custom">

									Date:

									<div bis_skin_checked="1">

										<input type="date" size="11" placeholder="yyyy-mm-dd" value="' . $start_date . '" name="start_date" class="" autocomplete="off">							<span>–</span>

										<input type="date" size="11" placeholder="yyyy-mm-dd" value="' . $end_date . '" name="end_date" class="" autocomplete="off">

										<button type="submit" class="button" name="export">Show</button>

									</div>

								</li>

								<li class="custom">

									<button type="submit" class="button" name="export_csv">Export Csv</button>

								</li>

							</ul>

						</form>

						</div>

					<div class="inside" bis_skin_checked="1">

				    <table class="widefat">

      <thead>

          <tr>

              <th><strong>Order</strong></th>

              <th><strong>Date</strong></th>

              <th><strong>Status</strong></th>

              <th><strong>Billing</strong></th>

              <th><strong>Ship to</strong></th>

              <th><strong>Email</strong></th>

              <th><strong>Phone</strong></th>

              <th><strong>Total</strong></th>

          </tr>

      </thead>

      <tbody>';

		$html .= $htmlTable;

		$html .= '</tbody>

    </table>

    </div>

	</div></div>';

		echo $html;

	}



	function filter_where( $where ) {

		global $wp_session;

		if ( isset( $wp_session['start_date'] ) || isset( $wp_session['end_date'] ) ) {

			$endDate = date( 'Y-m-d', strtotime( $wp_session['end_date'] . ' +1 day' ) );

			$startDate = date( 'Y-m-d', strtotime( $wp_session['start_date'] . ' -1 day' ) );

			$where .= " AND post_date > '" . $startDate . "' AND post_date < '" . $endDate . "'";

		} else {

			$end_date = date( 'Y-m-d' );

			$endDate = date( 'Y-m-d', strtotime( $end_date . ' +1 day' ) );

			$start_date = date( 'Y-m-d', strtotime( '-1 month' ) );

			$startDate = date( 'Y-m-d', strtotime( $start_date . ' -1 day' ) );

			$where .= " AND post_date > '" . $startDate . "' AND post_date < '" . $endDate . "'";

		}

		return $where;

	}



}




