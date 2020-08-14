<?php
/**
 * YITH WooCommerce Ajax Search template
 *
 * @author  YITH
 * @package YITH WooCommerce Ajax Search Premium
 * @version 1.2.3
 */

if ( ! defined( 'YITH_WCAS' ) ) {
	exit;
} // Exit if accessed directly

wp_enqueue_script( 'yith_wcas_frontend' );
$research_post_type = ( get_option( 'yith_wcas_default_research' ) ) ? get_option( 'yith_wcas_default_research' ) : 'product';
?>
<div class="yith-ajaxsearchform-container yith-ajaxsearchform-wide <?php echo esc_attr( $class ); ?> ">
	<form role="search" method="get" id="yith-ajaxsearchform" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<div class="yith-ajaxsearch-filters">
			<div class="yith-ajaxsearchform-select yith-ajaxsearchform-select-list">
				<?php
				wp_nonce_field( 'yith-ajax-search' );
				if ( get_option( 'yith_wcas_show_search_list' ) === 'yes' ) :
					$selected_search = ( isset( $_REQUEST['post_type'] ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['post_type'] ) ) : $research_post_type; //phpcs:ignore ?>

					<select class="yit_wcas_post_type selectbox" id="yit_wcas_post_type" name="post_type">
						<option value="product" <?php selected( 'product', $selected_search ); ?>><?php esc_html_e( 'Products', 'yith-woocommerce-ajax-search' ); ?></option>
						<option value="any" <?php selected( 'any', $selected_search ); ?>><?php esc_html_e( 'All', 'yith-woocommerce-ajax-search' ); ?></option>
					</select>

				<?php else : ?>
					<input type="hidden" name="post_type" class="yit_wcas_post_type" id="yit_wcas_post_type" value="<?php echo esc_attr( $research_post_type ); ?>" />
				<?php endif; ?>
			</div>
			<div class="yith-ajaxsearchform-select yith-ajaxsearchform-select-category">
				<?php
				if ( get_option( 'yith_wcas_show_category_list' ) === 'yes' ) :

					$product_categories = yith_wcas_get_shop_categories( get_option( 'yith_wcas_show_category_list_all' ) === 'all' );

					$selected_category = ( isset( $_REQUEST['product_cat'] ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['product_cat'] ) ) : ''; //phpcs:ignore

					if ( ! empty( $product_categories ) ) :
						?>
						<select class="search_categories selectbox" id="search_categories" name="product_cat">
							<option value="" <?php selected( '', $selected_category ); ?>><?php esc_html_e( 'All', 'yith-woocommerce-ajax-search' ); ?></option>
							<?php foreach ( $product_categories as $category ) :
								if( empty( $category ) ) :
									continue;
								endif;
							?>
								<option value="<?php echo esc_attr( $category->slug ); ?>" <?php selected( $category->slug, $selected_category ); ?>><?php echo esc_html( $category->name ); ?></option>
							<?php endforeach; ?>
						</select>
					<?php endif ?>
				<?php endif ?>
			</div>
		</div>
		<div class="search-input-container">
			<input type="search" value="<?php echo esc_attr( get_search_query() ); ?>" name="s" id="yith-s" class="yith-s" placeholder="<?php echo esc_attr( get_option( 'yith_wcas_search_input_label' ) ); ?>" data-append-to=".search-input-container" data-loader-icon="<?php echo str_replace( '"', '', apply_filters( 'yith_wcas_ajax_search_icon', '' ) ); ?>" data-min-chars="<?php echo esc_attr( get_option( 'yith_wcas_min_chars' ) ); ?>" />
		</div>

		<div class="search-submit-container">
			<?php if ( apply_filters( 'yith_wcas_submit_as_input', true ) ) : ?>
				<input type="submit" id="yith-searchsubmit" value="<?php echo esc_attr( apply_filters( 'yith_wcas_submit_label', get_option( 'yith_wcas_search_submit_label' ) ) ); ?>" />
			<?php else : ?>
				<button type="submit" id="yith-searchsubmit"><?php echo apply_filters( 'yith_wcas_submit_label', get_option( 'yith_wcas_search_submit_label' ) ) ; ?></button>
			<?php endif; ?>
		</div>

		<?php if ( defined( 'ICL_LANGUAGE_CODE' ) ) : ?>
			<input type="hidden" name="lang" value="<?php echo esc_attr( ICL_LANGUAGE_CODE ); ?>" />
		<?php endif ?>

	</form>
</div>
<script>
	jQuery(document).ready(function(a){"use strict";var b=a(".yith-s"),c="undefined"!=typeof woocommerce_params&&"undefined"!=typeof woocommerce_params.ajax_loader_url?woocommerce_params.ajax_loader_url:yith_wcas_params.loading,d=""==b.data("loader-icon")?c:b.data("loader-icon"),e=a("#yith-searchsubmit"),f=b.data("min-chars");e.on("click",function(){var b=a(this).closest("form");return""!=b.find(".yith-s").val()}),b.each(function(){var b=a(this),c=b.closest("form"),e=!1,g=c.find(".search_categories"),h=c.find(".yit_wcas_post_type"),i=c.find('[name="lang"]').length>0?c.find('[name="lang"]').val():"",j="undefined"==typeof b.data("append-to")?b.closest(".yith-ajaxsearchform-container"):b.closest(b.data("append-to")),k=yith_wcas_params.ajax_url.toString().replace("%%endpoint%%","yith_ajax_search_products");b.yithautocomplete({minChars:f,maxHeight:"auto",appendTo:j,triggerSelectOnValidInput:!1,serviceUrl:k+"&post_type="+h.val()+"&lang="+i+"&action=yith_ajax_search_products",onSearchStart:function(){b.css({"background-image":"url("+d+")","background-repeat":"no-repeat","background-position":"center right"})},onSearchComplete:function(){b.css("background-image","none"),a(window).trigger("resize"),b.trigger("focus")},onSelect:function(a){a.id!=-1&&(window.location.href=a.url)},beforeRender:function(){if("true"==yith_wcas_params.show_all&&e){var d={s:b.val(),post_type:c.find(".yit_wcas_post_type").val()};c.find(".search_categories").length>0&&(d.product_cat=c.find(".search_categories").val());var f=c.attr("action"),g=f.indexOf("?")!==-1?"&":"?",h=f+g+a.param(d),i='<div class="link-result"><a href="'+h+'">'+yith_wcas_params.show_all_text+"</a></div>",k=j.find(".autocomplete-suggestions");k.append(i)}},transformResult:function(b){return b="string"==typeof b?a.parseJSON(b):b,e=b.results,b},formatResult:function(b,c){var d="("+a.YithAutocomplete.utils.escapeRegExChars(c)+")",e="";return"undefined"!=typeof b.img&&(e+=b.img),e+='<div class="yith_wcas_result_content"><div class="title">',e+=b.value.replace(new RegExp(d,"gi"),"<strong>$1</strong>"),e+="</div>","undefined"!=typeof b.product_categories&&(e+=" "+b.product_categories),"undefined"!=typeof b.div_badge_open&&(e+=b.div_badge_open),"undefined"!=typeof b.on_sale&&(e+=b.on_sale),"undefined"!=typeof b.outofstock&&(e+=b.outofstock),"undefined"!=typeof b.featured&&(e+=b.featured),"undefined"!=typeof b.div_badge_close&&(e+=b.div_badge_close),"undefined"!=typeof b.price&&""!=b.price&&(e+=" "+yith_wcas_params.price_label+" "+b.price),"undefined"!=typeof b.excerpt&&(e+=" "+b.excerpt.replace(new RegExp(d,"gi"),"<strong>$1</strong>")),e+="</div>"}}),g.length&&g.on("change",function(a){var c=b.yithautocomplete(),d=yith_wcas_params.ajax_url.toString().replace("%%endpoint%%","yith_ajax_search_products");""!=g.val()?c.setOptions({serviceUrl:d+"&product_cat="+g.val()+"&lang="+i}):c.setOptions({serviceUrl:d+"&lang="+i}),c.hide(),c.onValueChange()}),h.length&&("any"==h.val()?g.attr("disabled","disabled"):g.removeAttr("disabled"),h.on("change",function(a){var c=b.yithautocomplete(),d=yith_wcas_params.ajax_url.toString().replace("%%endpoint%%","yith_ajax_search_products");"any"==h.val()?g.attr("disabled","disabled"):g.removeAttr("disabled"),""!=h.val()?c.setOptions({serviceUrl:d+"&post_type="+h.val()+"&lang="+i}):c.setOptions({serviceUrl:d+"&lang="+i}),c.hide(),c.onValueChange()}))})});
</script>
