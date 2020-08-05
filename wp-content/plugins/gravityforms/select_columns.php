<?php

// For backwards compatibility, load WordPress if it hasn't been loaded yet
// Will be used if this file is being called directly
if ( ! class_exists( 'RGForms' ) ) {
	for ( $i = 0; $i < $depth = 10; $i ++ ) {
		$wp_root_path = str_repeat( '../', $i );

		if ( file_exists( "{$wp_root_path}wp-load.php" ) ) {
			require_once( "{$wp_root_path}wp-load.php" );
			require_once( "{$wp_root_path}wp-admin/includes/admin.php" );
			break;
		}
	}

	auth_redirect();
}

/**
 * Class GFSelectColumns
 *
 * Handles the changing of what columns are shown on the Entry page
 *
 * @since Unknown
 */
class GFSelectColumns {

	/**
	 * Renders the column selection page.
	 *
	 * @since  Unknown
	 * @access public
	 *
	 * @uses GFFormsModel::get_form_meta()
	 * @uses GFFormsModel::get_grid_columns()
	 * @uses GFSelectColumns::get_selectable_entry_meta()
	 * @uses GFFormsModel::convert_field_objects()
	 * @uses GFFormsModel::get_input_type()
	 * @uses GF_Field::get_entry_inputs()
	 * @uses GFCommon::get_label()
	 *
	 * @return void
	 */
	public static function select_columns_page() {

		$form_id = absint( $_GET['id'] );
		if ( empty( $form_id ) ) {
			echo __( 'Oops! We could not locate your form. Please try again.', 'gravityforms' );
			exit;
		}
		?>
		<html>
		<head>
		<?php
		wp_print_styles( array( 'wp-admin', 'colors-fresh' ) );
		wp_print_scripts( array( 'jquery-ui-sortable' ) );

		//adds touchscreen support on mobile devices
		if ( wp_is_mobile() ) {
			wp_print_scripts( array( 'jquery-touch-punch' ) );
		}
		?>

			<style type="text/css">
				body {
					color: #444;
					background: #fff;
					font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
					font-size: 13px;
					line-height: 1.4em;
				}

				#sortable_available, #sortable_selected {
					list-style-type: none;
					margin: 0;
					padding: 2px;
					height: 350px;
					border: 1px solid #9092B2;
					border-radius: 5px;
					overflow: auto;
				}
				#sortable_available li, #sortable_selected li {
					margin: 0 2px 2px 2px;
					padding: 2px;
					width: 96%;
					cursor: pointer;
				}

				.field_hover {
					border: 1px dashed #2175A9 !important;
				}

				.placeholder {
					background-color: #F6F9FC;
					height: 20px;
				}

				.gcolumn_wrapper {
					height: 290px;
					padding: 0 36px;
					display: flex;
					justify-content: space-between;
				}

				.gcolumn_container_left, .gcolumn_container_right {
					width: 250px;
				}

				.gform_select_column_heading {
					padding-bottom: 7px;
					font-size: 1.125rem;
				}

				.column-arrow-mid {
					height: 100%;
					display: flex;
					align-items: center;
					justify-content: center;
				}

				div.panel-buttons {
					padding: 20px 32px;
					position: absolute;
					bottom: 0;
					left: 0;
					right: 0;
					border-top: 1px solid #E2E8F0;
					background: #F6F9FC;
				}

				div.panel-buttons .button,
				div.panel-buttons .button-primary {
					font-size: .875rem;
					font-weight: 600;
					line-height: 2.15384615;
					min-height: 30px;
					margin: 0;
					cursor: pointer;
					border-width: 1px;
					border-style: solid;
					-webkit-appearance: none;
					white-space: nowrap;
					box-sizing: border-box;
					color: #3E7DA6;
					border-color: #3E7DA6;
					background: #fff;
					border-radius: .3125rem;
					padding: .125rem 1.125rem;
					transition: all .3s ease;
				}

				div.panel-buttons .button-primary {
					background: #3E7DA6;
					border-color: #3E7DA6;
					color: #fff;
				}

				div.panel-buttons .button:hover,
				div.panel-buttons .button-primary:hover {
					box-shadow: 0 4px 6px rgba( 28, 31, 63, 0.0837013 );
					transform: translate( 0, -2px );
				}




			</style>

			<script type="text/javascript">
				jQuery(document).ready(function () {

					jQuery("#sortable_available, #sortable_selected").sortable({connectWith: '.sortable_connected', placeholder: 'placeholder'});

					jQuery(".sortable_connected li").hover(
						function () {
							jQuery(this).addClass("field_hover");
						},
						function () {
							jQuery(this).removeClass("field_hover");
						}
					);

				});
				var columns = new Array();

				function SelectColumns() {
					jQuery("#sortable_selected li").each(function () {
						columns.push(this.id);
					});
					self.parent.parent.ChangeColumns(columns);
				}
			</script>

		</head>
		<body>
		<?php
		$columns = RGFormsModel::get_grid_columns( $form_id );
		$field_ids = array_keys( $columns );
		$form = RGFormsModel::get_form_meta( $form_id );
		array_push( $form['fields'], array( 'id' => 'id', 'label' => __( 'Entry Id', 'gravityforms' ) ) );
		array_push( $form['fields'], array( 'id' => 'date_created', 'label' => __( 'Entry Date', 'gravityforms' ) ) );
		array_push( $form['fields'], array( 'id' => 'ip', 'label' => __( 'User IP', 'gravityforms' ) ) );
		array_push( $form['fields'], array( 'id' => 'source_url', 'label' => __( 'Source Url', 'gravityforms' ) ) );
		array_push( $form['fields'], array( 'id' => 'payment_status', 'label' => __( 'Payment Status', 'gravityforms' ) ) );
		array_push( $form['fields'], array( 'id' => 'transaction_id', 'label' => __( 'Transaction Id', 'gravityforms' ) ) );
		array_push( $form['fields'], array( 'id' => 'payment_amount', 'label' => __( 'Payment Amount', 'gravityforms' ) ) );
		array_push( $form['fields'], array( 'id' => 'payment_date', 'label' => __( 'Payment Date', 'gravityforms' ) ) );
		array_push( $form['fields'], array( 'id' => 'created_by', 'label' => __( 'User', 'gravityforms' ) ) );

		$form = self::get_selectable_entry_meta( $form );
		$form = GFFormsModel::convert_field_objects( $form );
		?>
		<div class="gcolumn_wrapper">
			<div class="gcolumn_container_left">
				<div class="gform_select_column_heading"><?php esc_html_e( 'Active Columns', 'gravityforms' ); ?></div>
				<ul id="sortable_selected" class="sortable_connected">
				<?php
				foreach ( $columns as $field_id => $field_info ) {
					?>
					<li id="<?php echo esc_attr( $field_id ) ?>"><?php echo esc_html( $field_info['label'] ) ?></li>
				<?php
				}
				?>
				</ul>
			</div>

			<div class="column-arrow-mid">
				<svg width="47" height="22" fill="none" xmlns="http://www.w3.org/2000/svg"><g filter="url(#filter0_d)"><path fill-rule="evenodd" clip-rule="evenodd" d="M36 17a8 8 0 100-16 8 8 0 000 16z" fill="#fff"/><path d="M36 17.5a8.5 8.5 0 100-17 8.5 8.5 0 000 17z" stroke="#9092B2"/></g><path fill-rule="evenodd" clip-rule="evenodd" d="M36.606 11.52a.5.5 0 010-.707l1.48-1.48H32.5a.5.5 0 110-1h5.586l-1.48-1.48a.5.5 0 11.707-.707L40 8.833l-2.687 2.687a.5.5 0 01-.707 0z" fill="#3E7DA6"/><g filter="url(#filter1_d)"><path fill-rule="evenodd" clip-rule="evenodd" d="M11 17a8 8 0 100-16 8 8 0 000 16z" fill="#fff"/><path d="M11 17.5a8.5 8.5 0 100-17 8.5 8.5 0 000 17z" stroke="#9092B2"/></g><path fill-rule="evenodd" clip-rule="evenodd" d="M10.394 6.146a.5.5 0 010 .707l-1.48 1.48H14.5a.5.5 0 110 1H8.914l1.48 1.48a.5.5 0 11-.707.707L7 8.833l2.687-2.687a.5.5 0 01.707 0z" fill="#3E7DA6"/><defs><filter id="filter0_d" x="25" y="0" width="22" height="22" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"><feFlood flood-opacity="0" result="BackgroundImageFix"/><feColorMatrix in="SourceAlpha" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/><feOffset dy="2"/><feGaussianBlur stdDeviation="1"/><feColorMatrix values="0 0 0 0 0.227451 0 0 0 0 0.227451 0 0 0 0 0.341176 0 0 0 0.0596411 0"/><feBlend in2="BackgroundImageFix" result="effect1_dropShadow"/><feBlend in="SourceGraphic" in2="effect1_dropShadow" result="shape"/></filter><filter id="filter1_d" x="0" y="0" width="22" height="22" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"><feFlood flood-opacity="0" result="BackgroundImageFix"/><feColorMatrix in="SourceAlpha" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/><feOffset dy="2"/><feGaussianBlur stdDeviation="1"/><feColorMatrix values="0 0 0 0 0.227451 0 0 0 0 0.227451 0 0 0 0 0.341176 0 0 0 0.0596411 0"/><feBlend in2="BackgroundImageFix" result="effect1_dropShadow"/><feBlend in="SourceGraphic" in2="effect1_dropShadow" result="shape"/></filter></defs></svg>
			</div>

			<div class="gcolumn_container_right" id="available_column">
				<div class="gform_select_column_heading"> <?php esc_html_e( 'Inactive Columns', 'gravityforms' ); ?></div>
				<ul id="sortable_available" class="sortable_connected">
				<?php
				foreach ( $form['fields'] as $field ) {
					/* @var GF_Field $field */
					if ( RGFormsModel::get_input_type( $field ) == 'checkbox' && ! in_array( $field->id, $field_ids ) ) {
						?>
						<li id="<?php echo esc_attr( $field->id ); ?>"><?php echo esc_html( GFCommon::get_label( $field ) ) ?></li>
					<?php
					}

					$inputs = $field->get_entry_inputs();

					$input_type = GFFormsModel::get_input_type( $field );

					$display = ! in_array( $input_type, array( 'list', 'repeater' ) );

					/**
					 * Allows fields to be added or removed from the select columns UI on the entry list.
					 *
					 * @since 2.4
					 *
					 * @param bool     $display Whether the field will be available for selection.
					 * @param GF_Field $field
					 * @param array    $form
					 */
					$display = gf_apply_filters( array( 'gform_display_field_select_columns_entry_list', $form_id, $field->id ), $display, $field, $form );

					if ( is_array( $inputs ) ) {
						foreach ( $inputs as $input ) {
							if ( rgar( $input, 'isHidden' ) ) {
								continue;
							}

							if ( ! in_array( $input['id'], $field_ids ) && ! ( $field->type == 'creditcard' && in_array( $input['id'], array( floatval( "{$field->id}.2" ), floatval( "{$field->id}.3" ), floatval( "{$field->id}.5" ) ) ) ) ) {
								?>
								<li id="<?php echo esc_attr( $input['id'] ); ?>"><?php echo esc_html( GFCommon::get_label( $field, $input['id'] ) ); ?></li>
							<?php
							}
						}
					} else if ( ! $field->displayOnly && ! in_array( $field->id, $field_ids ) && $display ) {
						?>
						<li id="<?php echo $field->id ?>"><?php echo esc_html( GFCommon::get_label( $field ) ); ?></li>
					<?php
					}
				}
				?>
				</ul>
			</div>
		</div>

		<div class="panel-buttons">
			<input type="button" value="  <?php esc_attr_e( 'Save', 'gravityforms' ); ?>  " class="button-primary" onclick="SelectColumns();" onkeypress="SelectColumns();" />&nbsp;
			<input type="button" value="<?php esc_attr_e( 'Cancel', 'gravityforms' ); ?>" class="button" onclick="self.parent.tb_remove();" onkeypress="self.parent.tb_remove();" />
		</div>

		</body>
		</html>

	<?php
	}

	/**
	 * Adds the entry meta to the Form object.
	 *
	 * @since  Unknown
	 * @access public
	 *
	 * @uses GFFormsModel::get_entry_meta()
	 *
	 * @param array $form The Form object.
	 *
	 * @return array $form The Form object.
	 */
	public static function get_selectable_entry_meta( $form ) {
		$entry_meta = GFFormsModel::get_entry_meta( $form['id'] );
		$keys       = array_keys( $entry_meta );
		foreach ( $keys as $key ) {
			array_push( $form['fields'], array( 'id' => $key, 'label' => $entry_meta[ $key ]['label'] ) );
		}

		return $form;
	}
}

GFSelectColumns::select_columns_page();
