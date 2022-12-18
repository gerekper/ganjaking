<?php

/**
 * YITH WooCommerce Ajax Search template
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Ajax Search
 * @version 1.1.1
 */

if ( ! defined( 'YITH_WCAS' ) ) {
	exit; }

global $porto_settings;

$container_class = 'yith-ajaxsearchform-container' . rand();
$show_cats       = isset( $porto_settings['search-cats'] ) && $porto_settings['search-cats'];
if ( $show_cats && wp_is_mobile() ) {
	$show_cats = ( ! isset( $porto_settings['search-cats-mobile'] ) || $porto_settings['search-cats-mobile'] );
}

?>

<form role="search" method="get" id="yith-ajaxsearchform" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="yith-ajaxsearchform-container <?php echo esc_attr( $container_class ); ?> searchform<?php echo ! $show_cats ? '' : ' searchform-cats'; ?>">
	<fieldset>
		<span class="text"><input name="s" id="yith-s" class="yith-s" type="text" value="<?php echo get_search_query(); ?>" placeholder="<?php esc_attr_e( 'Search&hellip;', 'woocommerce' ); ?>" /></span>
		<?php
		if ( $show_cats ) {
			$args             = array(
				'show_option_all' => __( 'All categories', 'woocommerce' ),
				'class'           => 'cat',
				'hierarchical'    => 1,
				'echo'            => 1,
				'value_field'     => 'slug',
				'selected'        => 1,
			);
			$args['taxonomy'] = 'product_cat';
			$args['name']     = 'product_cat';
			if ( isset( $porto_settings['search-sub-cats'] ) && ! $porto_settings['search-sub-cats'] ) {
				$args['depth'] = 1;
			}
			wp_dropdown_categories( $args );
		}
		?>
		<span class="button-wrap"><button class="btn" id="yith-searchsubmit" title="<?php esc_attr_e( 'Search', 'woocommerce' ); ?>" type="submit"><i class="fas fa-search"></i></button></span>
		<input type="hidden" name="post_type" value="product" />
		<?php if ( defined( 'ICL_LANGUAGE_CODE' ) ) : ?>
			<input type="hidden" name="lang" value="<?php echo( ICL_LANGUAGE_CODE ); ?>" />
		<?php endif ?>
	</fieldset>
</form>

<script type="text/javascript">
( function() {
	var porto_init_yith_search = function() {
		( function( $ ) {
			var search_loader_url = '<?php echo esc_url( PORTO_URI . '/images/ajax-loader@2x.gif' ); ?>';
			<?php
			$admin_ajax = admin_url( 'admin-ajax.php', 'relative' );
			if ( strpos( $admin_ajax, '?' ) === false ) {
				$admin_ajax .= '?';
			} else {
				$admin_ajax .= '&';
			}
			?>
			var ajax_url = '<?php echo esc_url( $admin_ajax ); ?>';

			var yith_search = $('.<?php echo esc_js( $container_class ); ?> .yith-s').<?php echo version_compare( YITH_WCAS_VERSION, '1.3.1', '>=' ) ? 'yithautocomplete' : 'autocomplete'; ?>({
				minChars: <?php echo get_option( 'yith_wcas_min_chars' ) * 1; ?>,
				appendTo: '.<?php echo esc_js( $container_class ); ?>',
				serviceUrl: function() {
					<?php if ( $show_cats ) : ?>
					var val = $('.<?php echo esc_js( $container_class ); ?> .cat').val();
					<?php else : ?>
					var val = '0';
					<?php endif; ?>
					if (val != '0') {
						return ajax_url + 'action=yith_ajax_search_products'<?php echo ! $show_cats ? '' : " + '&product_cat=' + val"; ?>;
					} else {
						return ajax_url + 'action=yith_ajax_search_products';
					}
				},
				onSearchStart: function(){
					$(this).css('background', 'url('+search_loader_url+') no-repeat 97% center');
					$(this).css('background-size', '16px 16px');
				},
				onSearchComplete: function(){
					$(this).css('background', 'transparent');
				},

				onSelect: function (suggestion) {
					if( suggestion.id != -1 ) {
						window.location.href = suggestion.url;
					}
				},
				formatResult: function (suggestion, currentValue) {
					var pattern = '(' + $.<?php echo version_compare( YITH_WCAS_VERSION, '1.3.1', '>=' ) ? 'YithAutocomplete' : 'Autocomplete'; ?>.utils.escapeRegExChars(currentValue) + ')';
					var html = '';

					if ( typeof suggestion.img !== 'undefined' ) {
						html += suggestion.img;
					}

					html += '<div class="yith_wcas_result_content"><div class="title">';
					html += suggestion.value.replace(new RegExp(pattern, 'gi'), '<strong>$1<\/strong>');
					html += '</div>';

					if ( typeof suggestion.div_badge_open !== 'undefined' ) {
						html += suggestion.div_badge_open;
					}

					if ( typeof suggestion.on_sale !== 'undefined' ) {
						html += suggestion.on_sale;
					}

					if ( typeof suggestion.featured !== 'undefined' ) {
						html += suggestion.featured;
					}

					if ( typeof suggestion.div_badge_close !== 'undefined' ) {
						html += suggestion.div_badge_close;
					}

					if ( typeof suggestion.price !== 'undefined' && suggestion.price != '' ) {
						html += ' ' + suggestion.price;
					}

					if ( typeof suggestion.excerpt !== 'undefined' ) {
						html += ' ' +  suggestion.excerpt.replace(new RegExp(pattern, 'gi'), '<strong>$1<\/strong>');
					}

					html += '</div>';

					return html;
				}
			});

			$('.<?php echo esc_js( $container_class ); ?> .cat').on('change', function() {
				$('.<?php echo esc_js( $container_class ); ?> .yith-s').focus();
			});
		} )( window.jQuery );
	};

	if ( window.theme && theme.isLoaded ) {
		porto_init_yith_search();
	} else {
		window.addEventListener( 'load', porto_init_yith_search );
	}
} )();
</script>
