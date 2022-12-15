<?php
/**
 * The template for displaying warranty options.
 *
 * @package WooCommerce_Warranty\Templates
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;
?>
<!DOCTYPE html>
<html>
<head>
	<title>
		<?php
		// translators: #%1$d: Warranty ID, %2$s: Site name.
		printf( esc_html__( 'RMA Request #%1$d - %2$s', 'wc_warranty' ), esc_html( $warranty['ID'] ), esc_html( get_bloginfo( 'name' ) ) );
		?>
	</title>
	<link rel="stylesheet" media="print" href="<?php echo esc_url( plugins_url( 'assets/css/print.css', WooCommerce_Warranty::$plugin_file ) ); ?>" />
	<style>
		body {
			font-family: Trebuchet MS, Tahoma, Verdana, Arial, sans-serif;
			width: 800px;
		}
		#header {
			padding: 10px 30px;
		}

		#header img {
			max-height: 200px;
		}

		h2 {
			text-align: center;
			margin: 60px 0 0px 0;
		}

		table.details {
			padding-top: 50px;
			width: 400px;
			margin: 0 auto;
		}
		table th {
			text-align: left;
		}
		.print {
			float: right;
			background-color: #f2f2f2;
			border: 1px solid #bbb;
			border-radius: 11px;
			color: #000;
			display: block;
			font-size: 0.9em;
			height: 22px;
			line-height: 22px;
			margin-top: 7px;
			padding-left: 20px;
			padding-right: 20px;
			text-decoration: none;
			width: 30px;
		}
	</style>
</head>
<body onload="window.print()">
<a class="print" href="#" onclick="window.print()">Print</a>
<div id="header">
	<?php if ( $logo ) : ?>
	<img class="logo" src="<?php echo esc_attr( $logo ); ?>" />
	<?php else : ?>
	<h1><?php bloginfo( 'name' ); ?></h1>
	<?php endif; ?>

	<?php if ( 'yes' === $show_url ) : ?>
	<p><small><a href="<?php bloginfo( 'url' ); ?>"><?php bloginfo( 'url' ); ?></a></small></p>
	<?php endif; ?>
</div>
<div id="content">
	<h2>
	<?php
	// translators: Waranty ID.
	printf( esc_html__( 'RMA Request #%d', 'wc_warranty' ), esc_html( $warranty['ID'] ) );
	?>
	</h2>

	<table class="borderless details" cellpadding="5">
		<tr>
			<th><?php esc_html_e( 'Date', 'wc_warranty' ); ?>:</th>
			<td><?php echo esc_html( date_i18n( WooCommerce_Warranty::get_datetime_format(), strtotime( $warranty['post_modified'] ) ) ); ?></td>
		</tr>
		<tr>
			<th><?php esc_html_e( 'Order Number', 'wc_warranty' ); ?>:</th>
			<td><?php echo $order ? esc_html( $order->get_order_number() ) : '-'; ?></td>
		</tr>
		<tr>
			<th><?php esc_html_e( 'Customer', 'wc_warranty' ); ?>:</th>
			<td><?php echo esc_html( $first_name . ' ' . $last_name . ' &ndash; ' . $email ); ?></td>
		</tr>
		<tr>
			<th><?php esc_html_e( 'Product', 'wc_warranty' ); ?>:</th>
			<td><?php echo esc_html( $product_name ); ?></td>
		</tr>
		<tr>
			<th><?php esc_html_e( 'RMA #', 'wc_warranty' ); ?>:</th>
			<td><?php echo esc_html( $warranty['code'] ); ?></td>
		</tr>

		<?php
		foreach ( $inputs as $input ) {
			if ( 'paragraph' === $input->type ) {
				continue;
			}

			$field = $form['fields'][ $input->key ];
			$value = ( isset( $warranty[ 'field_' . $input->key ] ) ) ? $warranty[ 'field_' . $input->key ] : '-';
			$value = maybe_unserialize( $value );
			if ( is_array( $value ) ) {
				$value = implode( ', ', $value );
			}
			?>
			<tr>
				<th><?php echo esc_html( $field['name'] ); ?>:</th>
				<td><?php echo wp_kses_post( $value ); ?></td>
			</tr>
			<?php
		}
		?>
		<tr>
			<th><?php esc_html_e( 'Tracking', 'wc_warranty' ); ?>:</th>
			<td><?php echo $tracking_html; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped. ?></td>
		</tr>
	</table>
</div>

</body>
</html>
