<?php
/**
 * Add new field for contact customize panel.
 *
 * Page for adding new field to contact module.
 *
 * @package    WordPress
 * @subpackage Kassyopea
 * @since      1.1
 */

if ( ! defined( 'IFRAME_REQUEST' ) ) {
	define( 'IFRAME_REQUEST', true );
}

$wp_load = dirname( dirname( __FILE__ ) );

for ( $i = 0; $i < 10; $i ++ ) {
	if ( file_exists( $wp_load . '/wp-load.php' ) ) {

		require_once "$wp_load/wp-load.php";
		break;
	} else {
		$wp_load = dirname( $wp_load );
	}
}

@header( 'Content-Type: ' . get_option( 'html_type' ) . '; charset=' . get_option( 'blog_charset' ) ); //phpcs:ignore

?>
<html 
<?php
if ( yit_ie_version() < 9 && yit_ie_version() > 0 ) {
	echo 'class="ie8"';
}
?>
xmlns="http://www.w3.org/1999/xhtml" <?php do_action( 'admin_xml_ns' ); ?> <?php language_attributes(); ?>>
<head>
	<meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ); ?>; charset=<?php echo esc_attr( get_option( 'blog_charset' ) ); ?>" />
	<title><?php esc_html_e( 'Add shortcode', 'yit' ); ?></title>
	<?php if ( isset( $sitepress ) ) : ?>
		<script type="text/javascript">
			var ajaxurl = '<?php echo esc_attr( admin_url( 'admin-ajax.php' ) ); ?>';
		</script>
	<?php endif; ?>
	<?php
	wp_admin_css( 'wp-admin', true );

	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

	wp_enqueue_style( 'ywcca_admin_style', YWCCA_ASSETS_URL . 'css/ywcca_admin_light_style.css', array( 'jquery' ), YWCCA_VERSION );
	wp_enqueue_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', array(), WC_VERSION );
	wp_enqueue_script( 'select2', WC()->plugin_url() . '/assets/js/select2/select2.full' . $suffix . '.js', array( 'jquery' ), '4.0.3' ); //phpcs:ignore

	wp_enqueue_script( 'selectWoo', WC()->plugin_url() . '/assets/js/selectWoo/selectWoo.full' . $suffix . '.js', array( 'jquery' ), '1.0.4' ); //phpcs:ignore
	wp_enqueue_script( 'wc-enhanced-select', WC()->plugin_url() . '/assets/js/admin/wc-enhanced-select' . $suffix . '.js', array( 'jquery', 'selectWoo' ), WC_VERSION ); //phpcs:ignore
	wp_localize_script(
		'wc-enhanced-select',
		'wc_enhanced_select_params',
		array(
			'i18n_no_matches'           => _x( 'No matches found', 'enhanced select', 'woocommerce' ),
			'i18n_ajax_error'           => _x( 'Loading failed', 'enhanced select', 'woocommerce' ),
			'i18n_input_too_short_1'    => _x( 'Please enter 1 or more characters', 'enhanced select', 'woocommerce' ),
			'i18n_input_too_short_n'    => _x( 'Please enter %qty% or more characters', 'enhanced select', 'woocommerce' ),
			'i18n_input_too_long_1'     => _x( 'Please delete 1 character', 'enhanced select', 'woocommerce' ),
			'i18n_input_too_long_n'     => _x( 'Please delete %qty% characters', 'enhanced select', 'woocommerce' ),
			'i18n_selection_too_long_1' => _x( 'You can only select 1 item', 'enhanced select', 'woocommerce' ),
			'i18n_selection_too_long_n' => _x( 'You can only select %qty% items', 'enhanced select', 'woocommerce' ),
			'i18n_load_more'            => _x( 'Loading more results&hellip;', 'enhanced select', 'woocommerce' ),
			'i18n_searching'            => _x( 'Searching&hellip;', 'enhanced select', 'woocommerce' ),
			'ajax_url'                  => admin_url( 'admin-ajax.php' ),
			'search_products_nonce'     => wp_create_nonce( 'search-products' ),
			'search_customers_nonce'    => wp_create_nonce( 'search-customers' ),
			'search_categories_nonce'   => wp_create_nonce( 'search-categories' ),
		)
	);

	wp_enqueue_script( 'ywcca_admin_script', YWCCA_ASSETS_URL . 'js/ywcca_admin' . $suffix . '.js', array( 'jquery', 'select2' ), '1.0.0' ); //phpcs:ignore


	$ywcca_localize_script = array(
		'i18n_matches_1'            => _x( 'One result is available, press enter to select it.', 'enhanced select', 'woocommerce' ),
		'i18n_matches_n'            => _x( '%qty% results are available, use up and down arrow keys to navigate.', 'enhanced select', 'woocommerce' ),
		'i18n_no_matches'           => _x( 'No matches found', 'enhanced select', 'woocommerce' ),
		'i18n_ajax_error'           => _x( 'Loading failed', 'enhanced select', 'woocommerce' ),
		'i18n_input_too_short_1'    => _x( 'Please enter 1 or more characters', 'enhanced select', 'woocommerce' ),
		'i18n_input_too_short_n'    => _x( 'Please enter %qty% or more characters', 'enhanced select', 'woocommerce' ),
		'i18n_input_too_long_1'     => _x( 'Please delete 1 character', 'enhanced select', 'woocommerce' ),
		'i18n_input_too_long_n'     => _x( 'Please delete %qty% characters', 'enhanced select', 'woocommerce' ),
		'i18n_selection_too_long_1' => _x( 'You can only select 1 item', 'enhanced select', 'woocommerce' ),
		'i18n_selection_too_long_n' => _x( 'You can only select %qty% items', 'enhanced select', 'woocommerce' ),
		'i18n_load_more'            => _x( 'Loading more results&hellip;', 'enhanced select', 'woocommerce' ),
		'i18n_searching'            => _x( 'Searching&hellip;', 'enhanced select', 'woocommerce' ),
		'ajax_url'                  => admin_url( 'admin-ajax.php' ),
		'search_categories_nonce'   => wp_create_nonce( YWCCA_SLUG . '_search-categories' ),
		'plugin_nonce'              => '' . YWCCA_SLUG . '',

	);

	wp_localize_script( 'ywcca_admin_script', 'ywcca_admin_i18n', $ywcca_localize_script );

	remove_action( 'admin_print_styles', array( 'WC_Name_Your_Price_Admin', 'add_help_tab' ), 20 );

	do_action( 'admin_print_styles' );
	do_action( 'admin_print_scripts' );
	do_action( 'admin_head' );
	?>
	<style type="text/css">
		html, body {
			background: #fff;
		}

		.button {
			background: #00a0d2;
			border-color: #0073aa;
			-webkit-box-shadow: inset 0 1px 0 rgba(120, 200, 230, .5), 0 1px 0 rgba(0, 0, 0, .15);
			box-shadow: inset 0 1px 0 rgba(120, 200, 230, .5), 0 1px 0 rgba(0, 0, 0, .15);
			color: #fff;
			text-decoration: none;
			display: inline-block;
			font-size: 13px;
			line-height: 26px;
			height: 28px;
			margin: 0;
			padding: 0 10px 1px;
			cursor: pointer;
			border-width: 1px;
			border-style: solid;
			-webkit-appearance: none;
			-webkit-border-radius: 3px;
			border-radius: 3px;
			white-space: nowrap;
			-webkit-box-sizing: border-box;
			-moz-box-sizing: border-box;
			box-sizing: border-box;
			font-family: inherit;
			font-weight: inherit;
		}

		.button:focus {
			border-color: #0e3950;
			-webkit-box-shadow: inset 0 1px 0 rgba(120, 200, 230, .6), 0 0 0 1px #5b9dd9, 0 0 2px 1px rgba(30, 140, 190, .8);
			box-shadow: inset 0 1px 0 rgba(120, 200, 230, .6), 0 0 0 1px #5b9dd9, 0 0 2px 1px rgba(30, 140, 190, .8);
		}

		.button:hover {
			background: #0091cd;
			border-color: #0073aa;
			-webkit-box-shadow: inset 0 1px 0 rgba(120, 200, 230, .6);
			box-shadow: inset 0 1px 0 rgba(120, 200, 230, .6);
			color: #fff;
		}

		.select2.select2-container{
			width:100%!important;
		}

	</style>
</head>
<body>

<div id="ywcca_lightbox_content">
	<p class="title_shortcode">
		<label for="ywcca_title"><?php esc_html_e( 'Title', 'yith-woocommerce-category-accordion' ); ?></label>
		<input class="widefat" type="text" id="ywcca_title" placeholder="<?php echo esc_attr__( 'Insert a title', 'yith-woocommerce-category-accordion' ); ?>">
	</p>
	<p class="ywcca_select_field">
		<label for="ywcca_how_show"><?php esc_html_e( 'Show in Accordion', 'yith-woocommerce-category-accordion' ); ?></label>
		<select id="ywcca_how_show" class="widefat">
			<option value="" selected><?php esc_html_e( 'Select an option', 'yith-woocommerce-category-accordion' ); ?></option>
			<option value="wc"  ><?php esc_html_e( 'WooCommerce Category', 'yith-woocommerce-category-accordion' ); ?></option>
			<option value="wp"  ><?php esc_html_e( 'Worpress Category', 'yith-woocommerce-category-accordion' ); ?></option>
			<option value="tag" ><?php esc_html_e( 'Tags', 'yith-woocommerce-category-accordion' ); ?></option>
			<option value="menu" ><?php esc_html_e( 'Menu', 'yith-woocommerce-category-accordion' ); ?></option>
		</select>
	</p>
	<div class="ywcca_wc_field" style="display:none;">
		<p class="ywcca_wc_sub_cat">
			<label for="ywcca_show_wc_subcat"><?php esc_html_e( 'Show WooCommerce Subcategories', 'yith-woocommerce-category-accordion' ); ?></label>
			<input type="checkbox" id="ywcca_show_wc_subcat">
		</p>
		<p class="ywcca_wc_exclude">
			<label for="ywcca_exclude_wc_cat"><?php esc_html_e( 'Exclude WooCommerce Categories', 'yith-woocommerce-category-accordion' ); ?></label>
			<?php
			$args = array(
				'id'               => 'ywcca_exclude_wc_cat',
				'class'            => 'wc-product-search',
				'name'             => 'ywcca_exclude_wc_cat',
				'data-multiple'    => true,
				'data-action'      => 'yith_category_accordion_json_search_wc_categories',
				'data-placeholder' => __( 'Select categories', 'yith-woocommerce-category-accordion' ),
			);

			yit_add_select2_fields( $args );

			?>
		</p>

	</div>
	<div class="ywcca_wp_field" style="display:none;">
		<p class="ywcca_wp_sub_field">
			<label for="ywcca_show_wp_subcat"><?php esc_html_e( 'Show WordPress Subcategories', 'yith-woocommerce-category-accordion' ); ?></label>
			<input type="checkbox" id="ywcca_show_wp_subcat" />
		</p>
		<p class="ywcca_wp_post_field">
			<label for="ywcca_show_post"><?php esc_html_e( 'Show Last Post', 'yith-woocommerce-category-accordion' ); ?></label>
			<input type="checkbox" id="ywcca_show_post" />
		</p>
		<p class="ywcca_wp_post_limit">
			<label for="ywcca_post_limit"><?php esc_html_e( 'Number Post (-1 for all post )', 'yith-woocommerce-category-accordion' ); ?></label>
			<input type="text" id="ywcca_post_limit" value="-1">
		</p>
		<p class="ywcca_wp_exclude">
			<label for="ywcca_exclude_wp_cat"><?php esc_html_e( 'Exclude WordPress Categories', 'yith-woocommerce-category-accordion' ); ?></label>
			<?php

			$args = array(
				'id'               => 'ywcca_exclude_wp_cat',
				'class'            => 'wc-product-search',
				'name'             => 'ywcca_exclude_wp_cat',
				'data-multiple'    => true,
				'data-action'      => 'yith_json_search_wp_categories',
				'data-placeholder' => __( 'Select categories', 'yith-woocommerce-category-accordion' ),

			);

			yit_add_select2_fields( $args );

			?>
		</p>
	</div>
	<div class="ywcca_menu_field" style="display:none;">
		<?php
		$menu_option = yith_get_navmenu();
		?>
		<p class="ywcca_menu_multiselect">
			<label for="ywcca_include_menu"><?php esc_html_e( 'Add menu in accordion', 'yith-woocommerce-category-accordion' ); ?></label>
			<select id="ywcca_include_menu" name="ywcca_include_menu[]" multiple="multiple" class="widefat">
				<?php
				foreach ( $menu_option as $key => $val ) {
					?>

					<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_attr( $val ); ?></option>
					<?php
				}
				?>
			</select>
		</p>
		<p class="ywcca_menu_label">
			<label for="ywcca_name_menu"><?php esc_html_e( 'Menu Label', 'yith-woocommerce-category-accordion' ); ?></label>
			<input type="text" id="ywcca_name_menu" name="ywcca_name_menu" >

		</p>
	</div>
	<div class="ywcca_tag_field" style="display: none;">
		<p class="ywcca_choose_tag_wc">
			<label for="ywcca_tag_wc"><?php esc_html_e( 'WooCommerce Tag', 'yith-woocommerce-category-accordion' ); ?></label>
			<input type="checkbox"  id="ywcca_tag_wc" />
		</p>
		<p class="ywcca_name_tag_wc">
			<label for="ywcca_name_wc_tag"><?php esc_html_e( 'WooCommerce Tag Label', 'yith-woocommerce-category-accordion' ); ?></label>
			<input type="text" id="ywcca_name_wc_tag">
		</p>
		<p class="ywcca_choose_tag_wp">
			<label for="ywcca_tag_wp"><?php esc_html_e( 'WordPress Tag', 'yith-woocommerce-category-accordion' ); ?></label>
			<input type="checkbox" id="ywcca_tag_wp" />
		</p>
		<p class="ywcca_name_tag_wp">
			<label for="ywcca_name_wp_tag"><?php esc_html_e( 'WordPress Tag Label', 'yith-woocommerce-category-accordion' ); ?></label>
			<input type="text" id="ywcca_name_wp_tag">
		</p>
	</div>
	<p class="ywcca_highlight">
		<label for="ywcca_highlight_curr_cat"><?php esc_html_e( 'Highlight the current category', 'yith-woocommerce-category-accordion' ); ?></label>
		<input type="checkbox" id="ywcca_highlight_curr_cat" />
	</p>
	<div class="ywcc_show_count_field" style="display:none;">
		<p class="ywcca_show_count">
			<label for="ywcca_show_count"><?php esc_html_e( 'Show Count', 'yith-woocommerce-category-accordion' ); ?></label>
			<input type="checkbox" id="ywcca_show_count" />
		</p>
	</div>
	<p class="ywcca_select_style">
		<label for="ywcca_acc_style"><?php esc_html_e( 'Style', 'yith-woocommerce-category-accordion' ); ?></label>
		<select id="ywcca_acc_style" name="ywcca_acc_style">
			<option value="style_1" selected><?php esc_html_e( 'Style 1', 'yith-woocommerce-category-accordion' ); ?></option>
			<option value="style_2" ><?php esc_html_e( 'Style 2', 'yith-woocommerce-category-accordion' ); ?></option>
			<option value="style_3" ><?php esc_html_e( 'Style 3', 'yith-woocommerce-category-accordion' ); ?></option>
			<option value="style_4" ><?php esc_html_e( 'Style 4', 'yith-woocommerce-category-accordion' ); ?></option>
		</select>
	</p>
	<p class="ywcca_orderby" style="display: none;">
		<label for="ywcca_orderby_sel"><?php esc_html_e( 'Order By', 'yith-woocommerce-category-accordion' ); ?></label>
		<select id="ywcca_orderby_sel" name="ywcca_orderby_sel">
			<option value="name" selected><?php esc_html_e( 'Name', 'yith-woocommerce-category-accordion' ); ?></option>
			<option value="count" ><?php esc_html_e( 'Count', 'yith-woocommerce-category-accordion' ); ?></option>
			<option value="id" ><?php esc_html_e( 'ID', 'yith-woocommerce-category-accordion' ); ?></option>
		</select>
		<select class="ywcca_order" id="ywcca_order_sel" name="ywcca_order_sel">
			<option value="asc" selected ><?php esc_html_e( 'ASC', 'yith-woocommerce-category-accordion' ); ?></option>
			<option value="desc" ><?php esc_html_e( 'DESC', 'yith-woocommerce-category-accordion' ); ?></option>

		</select>
	</p>

</div>
<div class="widget-control-actions">
	<div class="alignright" style="margin-right: 10px;">
		<input type="submit" name="ywcca_shortcode_insert" id="ywcca_shortcode_insert" class="button" value="<?php echo esc_attr__( 'Insert', 'yith-woocommerce-category-accordion' ); ?>">
	</div>
	<br class="clear">
</div>
<script type="text/javascript">

	var toggle_field = function (name, action) {

		switch (action) {

			case 'show' :

				name.show();
				break;
			case 'hide':

				name.hide();
				break;

		}
	}
	jQuery(document).on('click', '.button', function () {

		var how_show            =   jQuery('#ywcca_how_show').val(),
			string_short_code   =   'how_show="'+how_show+'" ';

		if(how_show=='')
		{
			alert("Select an option");
			return;
		}


		switch( how_show ){

			case 'wc' :

				var show_sub_cat    = jQuery('#ywcca_show_wc_subcat').is(":checked") ? 'on' :    'off',
					exclude_cat     = jQuery('#ywcca_exclude_wc_cat').val(),
					show_count      = jQuery('#ywcca_show_count').is(":checked")    ?   'on'    :   'off';
                    console.log('show_count');
                    console.log(show_count);

				exclude_cat = exclude_cat === null ? '' : exclude_cat;
				string_short_code+= 'show_sub_cat="'+show_sub_cat+'" exclude_cat="'+exclude_cat+'" show_count="'+show_count+'" ';
				break;
			case 'wp' :
				var show_sub_cat    = jQuery('#ywcca_show_wp_subcat').is(":checked") ? 'on' :    'off',
					exclude_cat     = jQuery('#ywcca_exclude_wp_cat').val(),
					show_count      = jQuery('#ywcca_show_count').is(":checked")    ?   'on'    :   'off',
					show_last_post  = jQuery('#ywcca_show_post').is(":checked")     ?   'on'    :   'off',
					post_limit      = jQuery('#ywcca_post_limit').val();

				exclude_cat = exclude_cat === null ? '' : exclude_cat;
				string_short_code+= 'show_sub_cat="'+show_sub_cat+'" exclude_cat="'+exclude_cat+'" show_last_post="'+show_last_post+'" post_limit="'+post_limit+'" show_count="'+show_count+'" ';
				break;
			case 'menu':

				var menu_ids       =   jQuery('#ywcca_include_menu').val(),
					menu_name  = jQuery('#ywcca_name_menu').val();
					string_short_code +=   'menu_ids="'+menu_ids.join(",")+'" name_menu="'+menu_name+'" ';
				break;




		}
		/*General params*/
		var title                  =   jQuery('#ywcca_title').val(),
			highlight              =   jQuery('#ywcca_highlight_curr_cat').is(":checked")? 'on'  :   'off',
			style                  =   jQuery('#ywcca_acc_style').val(),
			orderby                =   jQuery('#ywcca_orderby_sel').val(),
			order                  =   jQuery('#ywcca_order_sel').val(),
			tag_wc                 =   jQuery('#ywcca_tag_wc').is(":checked") ? 'on' : 'off',
			menu_wc_name            =  jQuery('#ywcca_name_wc_tag').val(),
			menu_wp_name = jQuery('#ywcca_name_wp_tag').val(),
			tag_wp                 =   jQuery('#ywcca_tag_wp').is(":checked") ? 'on' :  'off';

		string_short_code +=  'tag_wc="'+tag_wc+'" tag_wp="'+tag_wp+'" highlight="'+highlight+'" orderby="'+orderby+'" order="'+order+'" acc_style="'+style+'" name_wc_tag="'+menu_wc_name+'" name_wp_tag="'+menu_wp_name+'" ';

		var    str = '[yith_wcca_category_accordion title="' + title + '" '+string_short_code+']',
			win = window.dialogArguments || opener || parent || top;

		win.send_to_editor(str);
		var ed;
		if (typeof tinyMCE != 'undefined' && ( ed = tinyMCE.activeEditor ) && !ed.isHidden()) {
			ed.setContent(ed.getContent());
		}

	});

	jQuery(document).on('change', '#ywcca_how_show', function(e){
		var t          =   jQuery(this),
			container  =   t.parents('#ywcca_lightbox_content'),
			wc         =   container.find('.ywcca_wc_field'),
			wp         =   container.find('.ywcca_wp_field'),
			menu       =   container.find('.ywcca_menu_field'),
			count      =   container.find('.ywcc_show_count_field'),
			tag        =   container.find('.ywcca_tag_field'),
			order      =   container.find('.ywcca_orderby'),
			value      =   t.val();

		switch( value ) {

			case 'wc'  :
				toggle_field(wc, 'show');
				toggle_field(count,'show');
				toggle_field(wp, 'hide');
				toggle_field(menu, 'hide');
				toggle_field(tag,'hide');
				toggle_field(order, 'show');
				break;

			case 'wp' :
				toggle_field(wc, 'hide');
				toggle_field(count,'show');
				toggle_field(wp, 'show');
				toggle_field(menu, 'hide');
				toggle_field(tag,'hide');
				toggle_field(order, 'show');
				break;

			case 'menu' :
				toggle_field(wc, 'hide');
				toggle_field(count,'hide');
				toggle_field(wp, 'hide');
				toggle_field(menu, 'show');
				toggle_field(tag,'hide');
				toggle_field(order, 'hide');
				break;

			case 'tag' :
				toggle_field(wc, 'hide');
				toggle_field(count,'hide');
				toggle_field(wp, 'hide');
				toggle_field(menu, 'hide')
				toggle_field(tag,'show');
				toggle_field(order, 'show');
				break;
			default:
				toggle_field(wc, 'hide');
				toggle_field(count,'hide');
				toggle_field(wp, 'hide');
				toggle_field(menu, 'hide');
				toggle_field(tag,'hide');
				toggle_field(order, 'hide');
				break;
		}
	})


</script>
</body>
</html>
